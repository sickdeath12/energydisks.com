<?php


class ppDesignerExport {


	protected $abort;
	protected $widgetInfo;
	protected $zipURL;
	protected $uploadedFile;
	protected $thumbFilename;
	protected $exportResultMsg;
	protected $resultMsg;
	protected $activationWidgets = array();
	protected $design;


	public function __construct( $designID, $widgetInfo, $files ) {
		$this->design     = ppStorage::requestDesign( $designID );
		$this->widgetInfo = $widgetInfo;
		$this->processUploadedThumb( $files );
		$this->processWidgets();
	}


	public function isCurrentEnoughBuild() {
		$request  = new WpHttpRequest( new WP_Http() );
		$response = $request->get( pp::site()->extResourceUrl . '/designer_export_required_svn.html' );
		if ( $response->code() != 200 || !is_numeric( $response->body() ) ) {
			$this->abort     = true;
			$this->resultMsg = 'Unable to lookup current minimum required build # for valid designer export.  Please try again.';
			return false;
		} else if ( pp::site()->svn < $response->body() ) {
			$this->abort     = true;
			$this->resultMsg = 'You need to update this installation of ProPhoto before exporting.
								The minimum required build number is currently: #' . $response->body() . '.
								You are running build #' . pp::site()->svn . '. You can download a copy of the
								latest build <a href="' . ppUtil::customizeURL( 'settings', 'misc' ) . '">here</a>.';
			return false;
		} else {
			return true;
		}
	}


	protected function processUploadedThumb( $files ) {
		if ( ppUtil::unitTesting() ) {
			return;
		}

		if ( !is_array( $files ) || !isset( $files['async-upload'] ) || !is_array( $files['async-upload'] ) ) {
			$this->abort = true;
			$this->resultMsg = 'Export failed. No thumbnail image uploaded or missing upload data.';
			return;
		}

		$this->uploadedFile = $files['async-upload'];
		$this->thumbFilename = 'design_thumb_' . $this->design->id() . '.' . NrUtil::fileExt( $this->uploadedFile['name'] );
		$this->thumbFilepath = pp::fileInfo()->imagesFolderPath . '/' . $this->thumbFilename;
		$moveSuccess = move_uploaded_file( $this->uploadedFile['tmp_name'], $this->thumbFilepath );

		if ( !$moveSuccess ) {
			$this->abort = true;
			$this->resultMsg = 'Export failed. Could not move uploaded thumb image.';
			return;
		}

		$thumb = NrUtil::imgData( $this->thumbFilepath );
		if ( $thumb->width != 360 || $thumb->height != 360 ) {
			$this->abort = true;
			$this->resultMsg = 'Export failed. Uploaded design thumb was not the correct dimensions of 360px by 360px.';
			return;
		}

		ppImg::update( 'design_thumb', $this->thumbFilename );
	}


	protected function processWidgets() {
		foreach ( (array) $this->widgetInfo as $key => $val ) {
			if ( $val == 'no_choice' ) {
				$this->abort = true;
				$this->resultMsg = 'Export failed. One or more required widget option selections were not made.';
				return;
			}
			if ( NrUtil::startsWith( $key, 'empty_widget_area_' ) ) {
				$this->activationWidgets[ str_replace( 'empty_widget_area_', '', $key) ] = 'empty';
			}
		}
		$sidebarsWidgets = get_option( 'sidebars_widgets' );
		foreach ( (array) $sidebarsWidgets as $widgetArea => $widgetsInArea ) {
			if ( is_array( $widgetsInArea ) && $widgetArea != 'wp_inactive_widgets' ) {
				foreach ( $widgetsInArea as $widgetHandle ) {
					if ( isset( $this->widgetInfo['include_widget_'.$widgetHandle] ) && $this->widgetInfo['include_widget_'.$widgetHandle] == 'true' ) {
						if ( !isset( $this->activationWidgets[$widgetArea] ) ) {
							$this->activationWidgets[$widgetArea] = array();
							$widgetIndex = '1';
						} else {
							$widgetIndex = strval( array_pop( array_keys( $this->activationWidgets[$widgetArea] ) ) + 1 );
						}
						$widgetData = ppWidgetUtil::instanceData( $widgetHandle );
						if ( $widgetData['type'] == 'pp-grid' ) {
							$widgetData['instance']['gridOptionData'] = ppOpt::id( 'grid_widget_' . $widgetData['id'] );
						}
						$this->activationWidgets[$widgetArea][$widgetIndex] = array(
							 $widgetData['type'] => $widgetData['instance'],
						);
					}
				}
			}
		}
	}


	public function process() {
		if ( $this->abort ) {
			return;
		}
		$this->exportResultMsg = ppDesignUtil::export( $this->design->id(), $this->activationWidgets );
		if ( NrUtil::isIn( 'was successfully created', $this->exportResultMsg ) ) {
			preg_match( "/href='([^']+)'/", $this->exportResultMsg, $match );
			if ( $match[1] ) {
				$this->zipURL = $match[1];
				$this->resultMsg = 'Design exported successfully. Download the zip file <a href="' . $this->zipURL .'">here</a>.';
			}
		}
	}


	public function zipURL() {
		return $this->zipURL;
	}


	public function renderResult() {
		echo NrHtml::p( $this->resultMsg );
	}

}

