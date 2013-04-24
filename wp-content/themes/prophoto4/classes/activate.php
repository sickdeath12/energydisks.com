<?php

class ppActivate {


	const NO_AUTO_IMPORT_P3 = false;


	public static function init( $autoImportP3 = true ) {

		if ( false === get_option( ppStorage::DESIGNS_DB_OPTION_NAME ) && false === get_option( ppStorage::GLOBAL_OPTS_DB_OPTION_NAME ) ) {

			// setup default design
			if ( $autoImportP3 && ppImportP3::isP3User() && $importedP3ActiveDesign = ppImportP3::activeDesign() ) {
				$startingDesign = $importedP3ActiveDesign;
				$placeActivationWidgetsAfterDesignSave = false;
			} else {
				$startingDesign = new ppDesign( 'initial_activation_sunny_california', ppUtil::loadConfig( 'starter_sunny_california' ) );
				$startingDesign->name( 'Initial activation design - from Sunny California' );
				$startingDesign->desc( ppString::id( 'activation_design_desc' ) );
				$placeActivationWidgetsAfterDesignSave = true;
			}

			add_option( ppStorage::GLOBAL_OPTS_DB_OPTION_NAME );
			ppStorage::initializeDesignsWith( $startingDesign );

			// we place these after we save the design becuase grid widgets update the options
			if ( $placeActivationWidgetsAfterDesignSave ) {
				ppWidgetUtil::placeActivationWidgets( $startingDesign->activationWidgets() );
			}

			// import P3 widgets & non-design settings
			if ( $autoImportP3 && ppImportP3::isP3User() ) {
				ppImportP3::widgets();
				ppImportP3::nonDesignOptions();
				ppImportP3::contactFormLog();
			}

			self::addDefaultWidgets();
			self::removeNonProPhotoWidgetAreas();
			self::disableAdminBar();
		}

		if ( !ppUid::exists() ) {
			ppUid::set();
		}

		// set up a few scheduled events
		ppCron::schedule( 'backupRemind', '+2 weeks' );
		ppCron::schedule( 'wpHackWarn' );

		ppOpt::update( 'updated_time', time() );
		ppStaticFile::generateAll();

		// simplify standard upload path for common error prevention
		if ( NrUtil::isIn( 'wp-content/uploads', get_option( 'upload_path' ) ) ) {
			update_option( 'upload_path', 'wp-content/uploads' );
		}

		// change some defaults to be more photog-friendly
		update_option( 'blog_public', 1 );
		if ( get_option( 'posts_per_page' ) == 10 ) {
			update_option( 'posts_per_page', 5 );
		}
		if ( get_option( 'default_post_edit_rows' ) == 10 ) {
			update_option( 'default_post_edit_rows', 20 );
		}
		if ( get_option( 'blogdescription' ) == 'Just another WordPress site' ) {
			update_option( 'blogdescription', 'Blog' );
		}

		// first-ever activation welcome procedure
		if ( ppOpt::test( 'not_registered', 'true' ) ) {
			ppWelcome::showRegistrationForm();
		}

		ppOpt::update( 'activation_time', time() );
	}


	/* we run this because as of wp 3.3 retrieve_widgets() was jumbling ProPhoto and non-ProPhoto widgets in a mess */
	protected static function removeNonProPhotoWidgetAreas() {
		global $wp_registered_sidebars, $sidebar_widgets;
		if ( !is_array( $wp_registered_sidebars ) || reset( array_keys( $wp_registered_sidebars ) ) != 'contact-form' ) {
			new ppIssue( 'Unable to normalize widget areas before activate' );
			return false;
		}

		wp_cache_flush();
		$sidebars_widgets  = get_option( 'sidebars_widgets' );
		$_sidebars_widgets = array();

		$_sidebars_widgets['wp_inactive_widgets'] = isset( $sidebars_widgets['wp_inactive_widgets'] ) ? $sidebars_widgets['wp_inactive_widgets'] : array();
		foreach ( $wp_registered_sidebars as $id => $data ) {
			$_sidebars_widgets[$id] = ( isset( $sidebars_widgets[$id] ) && is_array( $sidebars_widgets[$id] ) ) ? $sidebars_widgets[$id] : array();
		}
		$_sidebars_widgets['array_version'] = isset( $sidebars_widgets['array_version'] ) ? $sidebars_widgets['array_version'] : 3;

		$sidebars_widgets = $_sidebars_widgets;
		update_option( 'sidebars_widgets', $_sidebars_widgets );
		wp_cache_flush();
	}


	private static function addDefaultWidgets() {
		$defaultWidgets = ppUtil::loadConfig( 'default_widgets' );
		if ( empty( $defaultWidgets ) ) {
			new ppIssue( 'Unable to load default widgets' );
			return;
		}

		if ( !ppWidgetUtil::areaHasWidgets( 'contact-form' ) ) {
			ppWidgetUtil::addWidget( 'contact-form', 'pp-text', $defaultWidgets['contact-text'] );
		}

		// bio default widget
		$bioEmpty = true;
		if ( ppWidgetUtil::areaHasWidgets( 'bio-spanning-col' ) ) {
			$bioEmpty = false;
		} else {
			for ( $i = 1; $i <= pp::num()->maxBioWidgetColumns; $i++ ) {
				if ( ppWidgetUtil::areaHasWidgets( 'bio-col-' . $i ) ) {
					$bioEmpty = false;
				}
			}
		}
		if ( $bioEmpty ) {
			ppWidgetUtil::addWidget( 'bio-spanning-col', 'pp-text', $defaultWidgets['bio-text'] );
		}

		// footer defaults
		if ( !ppWidgetUtil::footerHasWidgets() ) {
			ppWidgetUtil::addWidget( 'footer-col-1', 'search',       $defaultWidgets['search'] );
			ppWidgetUtil::addWidget( 'footer-col-1', 'links',        $defaultWidgets['links'] );
			ppWidgetUtil::addWidget( 'footer-col-2', 'archives',     $defaultWidgets['archives'] );
			ppWidgetUtil::addWidget( 'footer-col-3', 'categories',   $defaultWidgets['categories'] );
			ppWidgetUtil::addWidget( 'footer-col-3', 'meta',         $defaultWidgets['meta'] );
			ppWidgetUtil::addWidget( 'footer-col-4', 'recent-posts', $defaultWidgets['recent-posts'] );
			ppWidgetUtil::addWidget( 'footer-col-4', 'pages',        $defaultWidgets['pages'] );
		}
	}


	public static function disableAdminBar() {
		if ( !ppOpt::test( 'admin_bar_disabled', 'true' ) ) {
			global $wpdb;
			$wpdb->update( $wpdb->usermeta, array( 'meta_value' => 'false' ), array( 'meta_key' => 'show_admin_bar_front' ) );
			$wpdb->update( $wpdb->usermeta, array( 'meta_value' => 'false' ), array( 'meta_key' => 'show_admin_bar_admin' ) );
			$user = wp_get_current_user();
			update_user_option( $user->ID, 'show_admin_bar_front', 'false', true );
			update_user_option( $user->ID, 'show_admin_bar_admin', 'false', true );
			ppOpt::update( 'admin_bar_disabled', 'true' );
		}
	}
}
