<?php

class ppDesignsPage {


	const ACTIVE = true;
	const INACTIVE = false;
	public $importDesignUrl;
	public $exportEverythingUrl;
	public $importP3DesignsUrl;
	public $showReset = false;
	public $activeDesign;
	public $inactiveDesigns = array();
	public $manuallyImportedDesignsMsg = '';


	public static function render() {
		$page = new ppDesignsPage();
		ppUtil::renderView( 'designs_page', compact( 'page' ) );
	}


	protected function __construct() {

		if ( $activeDesignObj = ppStorage::activeDesign() ) {
			$this->activeDesign = $this->designInfoObject( $activeDesignObj, self::ACTIVE );
		}

		if ( $manuallyImported = ppDesignUtil::importManuallyUploadedDesigns() ) {
			foreach ( $manuallyImported as $importedDesign ) {
				if ( is_object( $importedDesign ) ) {
					$importedMsg = ppString::id( 'design_import_success', $importedDesign->name() );
					$this->manuallyImportedDesignsMsg .= NrHtml::div( $importedMsg, 'class=pp-admin-msg advise updated' );
				}
			}
			ppStorage::_onClassLoad();
		}

		$storedDesignIds = ppStorage::designIds();
		foreach ( $storedDesignIds as $designID ) {
			if ( $this->activeDesign && $designID == $this->activeDesign->id ) {
				continue;
			}
			$design = ppStorage::requestDesign( $designID );
			if ( $design ) {
				$this->inactiveDesigns[] = $this->designInfoObject( $design, self::INACTIVE );
			} else {
				new ppIssue( "Unable to load design with id = '$designID'" );
			}
		}

		if ( ppImportP3::unimportedDesigns() ) {
			$this->importP3DesignsUrl = ppIFrame::url( 'import_p3_designs', '450', '175' );
		}

		if ( pp::site()->isDev || pp::browser()->isTech || isset( $_GET['show_export'] ) ) {
			$this->exportEverythingUrl = ppIFrame::url( 'export_everything', '450', '175' );
		}

		if ( pp::site()->isDev || pp::browser()->isTech || isset( $_GET['show_reset'] ) ) {
			$this->showReset = true;
		}
	}


	public static function loadFiles() {
		ppAdmin::showVideoFirstTime( 'manage-designs-page' );
		ppAdmin::loadFile( 'designs.js' );
		ppAdmin::loadFile( 'designs.css' );
		ppAdmin::loadStyle( 'thickbox' );
		ppAdmin::loadScript( 'thickbox' );
	}


	/* handle adding admin notices to notify user of actions that have taken place */
	public static function handleNotices() {

		if ( isset( $_POST['pp_POST_identifier'] ) ) {

			switch ( $_POST['pp_POST_identifier'] ) {

				case 'designs_page_reset_all':
					ppAdmin::warn( 'all_designs_reset' );
					break;

				case 'designs_page_misc':

					switch ( $_POST['action'] ) {

						case 'delete_design':
							ppAdmin::advise( 'design_deleted' );
							break;

						case 'activate_design':
							$activated = ppStorage::requestDesign( $_POST['value'] );
							if ( false !== $activated ) {
								if ( ppWidgetUtil::$widgetsProgramaticallyDeactivated ) {
									ppAdmin::advise( 'design_activated_widgets_deactivated', $activated->name() );
								} else {
									ppAdmin::advise( 'design_activated', $activated->name() );
								}
							}
							break;
					}
			}


		} else if ( isset( $_GET['add_notice'] ) ) {

			switch ( $_GET['add_notice'] ) {

				case 'edit_meta_success':

					if ( !isset( $_GET['design_id'] ) ) {
						new ppIssue( 'Insufficient info, lacking "design_id"' );
						return;
					}

					$design = ppStorage::requestDesign( $_GET['design_id'] );

					if ( false === $design ) {
						return;
					}

					ppAdmin::advise( 'design_meta_updated', $design->name() );
					break;

				case 'new_design_created':
					ppAdmin::advise( 'new_design_created', urldecode( $_GET['design_name'] ) );
					break;

				case 'design_import_success':
					ppAdmin::advise( 'design_import_success', urldecode( $_GET['design_name'] ) );
					return;

				case 'p3_design_import_success':
					ppAdmin::advise( 'p3_design_import_success' );
					break;
			}
		}
	}


	private function designInfoObject( ppDesign $design, $active ) {
		$exportForStoreUrl = false;
		if ( $active ) {
			$options = $design->options();
			if ( ppOpt::test( 'designed_for_prophoto_store', 'true' ) ) {
				$exportForStoreUrl = ppIFrame::url( "export_design_for_store&design_id="    . $design->id() );
			}
		}
		return (object) array(
			'id'         => $design->id(),
			'export_url' => ppIFrame::url( "export_design&design_id="    . $design->id(), '440', '200' ),
			'edit_url'   => ppIFrame::url( "edit_design_form&design_id=" . $design->id(), '440', '315' ),
			'copy_url'   => ppIFrame::url( "copy_design_form&design_id=" . $design->id(), '440', '470' ),
			'name'       => $design->name(),
			'desc'       => $design->desc(),
			'is_active'  => $active,
			'export_for_store_url' => $exportForStoreUrl,
		);
	}
}

