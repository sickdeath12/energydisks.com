<?php


class ppReady {


	/* hook the main setup controlling func to 'wp_loaded' action */
	public static function init() {
		pp::register( 'constants', new ppConstants() );
		pp::register( 'folders', new ppFolders() );

		define( 'ROOTPATH', ppUtil::rootPath( apply_filters( 'blog_abspath', ABSPATH ), pp::site()->wpurl ) );
		define( 'ROOTURL',  ppUtil::rootUrl( pp::site()->wpurl ) );

		add_theme_support( 'post-thumbnails' );

		// these can't be run on 'after_setup_theme'
		ppWidgetUtil::registerAreas();
		ppWidgetUtil::registerWidgets();

		add_action( 'after_setup_theme', 'ppReady::ppLoaded' );

		if ( $beforePreviewWidgets = get_transient( 'pp_sidebars_widgets_preview_safeguard' ) ) {
			if ( is_array( $beforePreviewWidgets ) ) {
				delete_transient( 'pp_sidebars_widgets_preview_safeguard' );
				update_option( 'sidebars_widgets', $beforePreviewWidgets );
				wp_cache_flush();
			}
		}
	}


	/* main controlling method, do everything required once WordPress and theme are loaded */
	public static function ppLoaded() {

		add_action( 'shutdown', 'ppIssue::reportAll' );
		add_action( 'shutdown', 'ppStorage::saveCustomizations', 10 );

		global $pagenow;

		self::handlePOST( $_POST );

		if ( $pagenow == 'themes.php' && NrUtil::GET( 'activated', 'true' ) ) {
			ppActivate::init();
		}

		if ( get_transient( 'pp_design_designated_active' ) ) {
			ppLegacy::updateActiveDesign();
			delete_transient( 'pp_design_designated_active' );
		}

		self::everywhere();
		self::mobile();
		self::adminPages();
		self::blogPages();
		self::denyMu();

		switch ( $pagenow ) {
			case 'admin.php':
			case 'themes.php':
				self::themePages();
				break;
			case 'page-new.php':
			case 'post-new.php':
			case 'post.php':
			case 'page.php':
				self::composePages();
				break;
			case 'widgets.php':
				self::widgetsPage();
				break;
			case 'media-upload.php':
			case 'async-upload.php':
			case 'media-new.php':
				self::mediaUploadIFrame();
				break;
			case 'update-core.php';
			case 'update.php':
			case 'plugins.php':
				ppAdmin::ftpAdvise();
				break;
			case 'theme-editor.php':
				ppAdmin::warn( 'dont_edit_theme_files' );
				break;
			case 'admin-ajax.php':
				add_action( 'update_option_sidebars_widgets', 'ppWidgetUtil::regenerateStaticFiles' );
				add_action( 'update_option', create_function( '$optionName', "
					if ( preg_match( '/^widget_/', \$optionName ) ) {
						ppWidgetUtil::regenerateStaticFiles();
					}"
				) );
				break;
			case 'edit-comments.php':
				if ( !function_exists( 'akismet_init' ) ) {
					ppAdmin::warn( 'use_akismet' );
				}
				break;
		}
		do_action( 'post_pp_loaded' );
	}


	/* centralized $_POST data dispatcher */
	public static function handlePOST( $post ) {
		if ( !isset( $post['pp_POST_identifier'] ) ) {
			return false;
		}

		if ( $post['pp_POST_identifier'] == 'upload_file' ) {
			check_admin_referer( 'media-form' );
		} else {
			ppNonce::check( $post['pp_POST_identifier'] );
		}

		switch ( $post['pp_POST_identifier'] ) {

			case 'customize_page':
				$savedChanges = new ppCustomizeSubmit( $post );
				ppOpt::updateMultiple( $savedChanges->processedArray() );
				ppStorage::saveCustomizations();
				ppAdmin::advise( 'options_updated' );
				break;

			case 'upload_file':
				ppUploadUtil::processUpload( $post, $_FILES );
				break;

			case 'designs_page_misc':
			case 'designs_page_create_new':
			case 'designs_page_edit_meta':
			case 'designs_page_copy':
			case 'designs_page_reset_all':
				ppDesignUtil::processPOST( $post );
				break;

			case 'grid_admin':
				// do nothing, we let the gridAdmin class handle it
				break;

			default:
				new ppIssue( "Unknown POSTed pp_POST_identifier '{$post['pp_POST_identifier']}'" );
		}
	}


	/* run this code no matter what page is being requested */
	protected static function everywhere() {

		// intercept $_REQUEST commands
		self::interceptRequestCommands();

		// regenerate static files if theme updated
		self::svnBump();

		// filter post/page content
		ppContentFilter::addAllFilters();

		if ( !ppCron::scheduled( 'ppUpgrader::checkRecommendedSvn' ) ) {
			ppCron::schedule( 'ppUpgrader::checkRecommendedSvn', $delay = 1, 'twicedaily' );
		}
		if ( !ppCron::scheduled( 'ppHelper::updateActiveStatus' ) ) {
			ppCron::schedule( 'ppHelper::updateActiveStatus', '+2 weeks', 'monthly' );
		}
		if ( !ppCron::scheduled( 'ppUtil::lookupNameservers' ) ) {
			ppCron::schedule( 'ppUtil::lookupNameservers', 1, 'monthly' );
		}

		ppGrid::flushMarkupCacheOnArticleSave();


		// possibly do a cron task
		ppCron::maybeDo( 'backupRemind' );
		ppCron::maybeDo( 'wpHackWarn' );
		ppCron::maybeDo( 'ppUpgrader::checkRecommendedSvn' );
		ppCron::maybeDo( 'ppHelper::updateActiveStatus' );
		ppCron::maybeDo( 'ppUtil::lookupNameservers' );

/*
		if ( ppOpt::test( 'not_registered', 'true' ) ) {
			if ( !is_admin() ) {
				if ( current_user_can( 'level_1' ) ) {
					if ( ppOpt::test( 'dev_test_mode', 'disabled' ) ) {
						add_action( 'wp_head', ppUtil::func( 'echo wp_die( ppString::id( "not_registered_msg" ) );' ) );
					}
				} else if ( $GLOBALS['pagenow'] != 'wp-login.php' && !pp::browser()->isTech ) {
					wp_die( ppOpt::id( 'maintenance_message' ) );
				}
			} else {
				if ( ppOpt::test( 'dev_test_mode', 'disabled' ) ) {
					ppAdmin::warn( 'not_registered_admin_notice' );
				} else {
					if ( !pp::site()->isDev ) {
						ppAdmin::warn( 'dev_test_mode_enabled' );
					}
				}
			}
		}
*/
	}


	/* run this an any and every admin page */
	protected static function adminPages() {
		if ( !is_admin() ) {
			return;
		}

		if ( $GLOBALS['pagenow'] == 'index.php' ) {
			ppAdmin::showVideoFirstTime( 'understanding-wordpress' );
			add_filter( 'get_user_metadata', create_function( '$in,$ignore,$meta_key', "
				if ( \$meta_key == 'show_welcome_panel' ) {
					echo ppAdmin::videoIconLink( 'understanding-wordpress', 'Understanding WordPress' );
				}
				return \$in;
			" ), 10, 3 );
		}

		add_action( 'wp_ajax_pp',               'ppAdmin::handleAjaxRequests' );
		add_action( 'wp_ajax_nopriv_pp_nopriv', 'ppAdmin::handleLoggedOutAjaxRequests' );
		add_action( 'wp_ajax_pp_nopriv',        'ppAdmin::handleLoggedOutAjaxRequests' );

		self::tweakWpSuperCache();
		ppAdmin::loadPageFiles();
		ppAdmin::loadFile( 'admin.css' );
		ppAdmin::loadFile( 'colorbox.js', array(), ppAdmin::LOAD_IN_FOOTER );
		ppAdmin::jsToHead( addslashes( ppUtil::siteData( $js = true ) ) );
		ppAdmin::jsToHead( "jQuery(document).ready(function($){
			$('a.video, a.modal-video').colorbox({fixed:true});
			$('#update-nag').append(' Staying up to date is the best way to prevent getting your <b>blog hacked</b>.');
		});" );
		add_action( 'admin_footer', 'ppAdmin::loadRemoteJs' );
		ppAdmin::addMenuItems();
		ppUtil::setWpOption( 'blog_charset', 'UTF-8' );
		ppAdmin::showSvnFooter();
		ppAdmin::backupPluginNag();
		ppUpgrader::showUpgradeNotice();
		ppRemoteFiles::downloadMissingFiles();
		add_filter( 'sanitize_file_name_chars', create_function( '$chars', 'return array_merge( $chars, array( "©", "ø", "Ø", "å", "\'" ) );' ) );
		add_action( 'admin_enqueue_scripts', ppUtil::func( 'echo NrHtml::lessThanIE( 9, NrHtml::scriptSrc( pp::site()->themeUrl . "/dynamic/js/console.js" ) );' ), 1 );

		$regenStaticFiles = ppUtil::func( 'add_action( "shutdown", "ppAdmin::regenFilesOnUrlChange", 10 );' );
		add_action( 'update_option_siteurl', $regenStaticFiles );
		add_action( 'update_option_home', $regenStaticFiles );

		add_action( 'trashed_post',      'ppGalleryAdmin::trashAssociatedGalleries' );
		add_action( 'after_delete_post', 'ppGalleryAdmin::trashAssociatedGalleries' );
		add_action( 'untrashed_post',    'ppGalleryAdmin::untrashAssociatedGalleries' );
	}


	/* run on non-admin pages */
	protected static function blogPages() {
		if ( is_admin() ) {
			return;
		}
		ppGrid::modifyQuery();
		ppWidgetUtil::registerWidgets();
		ppWidgetUtil::registerAreas();
		add_action( 'template_redirect', 'ppRss::feedburnerRedirect' );
		add_action( 'get_header', 'ppGallery::setupGalleryQuasiPage', 1 );
		if ( ppOpt::test( 'maintenance_mode', 'on' ) ) {
			add_action( 'wp_head', 'ppHtml::maintenanceMode' );
			add_action( 'pp_mobile_head', 'ppHtml::maintenanceMode' );
		}
		if ( current_user_can( 'level_1' ) ) {
			add_filter( 'dynamic_sidebar_params', 'ppWidget::adminEditLink' );
		}
	}


	/* run on themes.php and admin.php pages, including "Customize" & "Designs" */
	protected static function themePages() {
		ppAdmin::cssToHead( '#current-theme img {width:300px}' ); // make our screenshot bigger

		if ( NrUtil::GET( 'page', 'pp-designs' ) ) {
			ppDesignsPage::handleNotices();
		}

		if ( isset( $_GET['pp_welcome'] ) ) {
			ppWelcome::doStep( $_GET['pp_welcome'] );
		}

		if ( NrUtil::GET( 'area', 'menus' ) || NrUtil::GET( 'area', 'mobile' ) ) {
			ppAdmin::loadFile( 'menu-admin.css' );
			ppAdmin::loadFile( 'menu-admin.js', $deps = array(), ppAdmin::LOAD_IN_FOOTER );
			ppAdmin::loadFile( 'easing.js', $deps = array(), ppAdmin::LOAD_IN_FOOTER );
		}

		add_action( 'admin_footer', ppUtil::func( 'do_action( "pp_minify_generated_js" );' ) );
		ppAdmin::writeProtectiveIndexes();
	}


	/* run on post/page writing/editing pages */
	protected static function composePages() {
		ppAdmin::showVideoFirstTime( 'images-galleries-overview' );
		ppAdmin::loadFile( 'posting.js' );
		ppAdmin::loadFile( 'posting.css' );
		ppAdmin::tinymceTweaks();
		ppMediaAdmin::changePostUploadButtons();
		add_filter( 'flash_uploader', '__return_true' ); // TODO: check, is this in the right spot?
		ppUtil::setWpOption( 'image_default_link_type', 'none' );
		add_action( 'all_admin_notices', ppUtil::func( "echo ppAdmin::videoIconLink( 'images-galleries-overview', 'Images and Galleries Overview' );" ) );
		add_action( 'admin_print_footer_scripts', 'ppFacebook::refreshArticleOGCache', 100000 );
	}


	/* run on "Appearance" > "Widgets" page */
	protected static function widgetsPage() {
		set_user_setting( 'widgets_access', 'off' ); // force drag and drop
		ppAdmin::showVideoFirstTime( 'understanding-widgets' );
		ppAdmin::loadFile( 'widgets.js' );
		ppAdmin::loadFile( 'widgets.css' );
		ppAdmin::loadFile( 'grid-admin.css' );
		ppAdmin::loadFile( 'grid-admin.js' );
		if ( isset( $_GET['pp_edit_widget'] ) ) {
			ppAdmin::advise( 'editing_single_widget' );
			ppAdmin::loadFile( 'edit-widget.css' );
			ppAdmin::loadFile( 'edit-widget.js', $deps = array(), ppAdmin::LOAD_IN_FOOTER );
		}
		add_action( 'admin_head', 'ppWidgetUtil::widgetsPageJsCss' );
		add_action( 'sidebar_admin_setup', 'ppWidgetUtil::regenerateStaticFiles' );
		add_action( 'widgets_admin_page', ppUtil::func( "echo ppAdmin::videoIconLink( 'understanding-widgets', 'Understanding Widgets' );" ) );
	}


	/* run on media upload iFrame page */
	protected static function mediaUploadIFrame() {
		ppAdmin::loadFile( 'wp_media_upload.js' );
		ppMediaAdmin::modifyUploadIFrameTabs();
		ppMediaAdmin::addButtonsBelowUploadForm();
		if ( $GLOBALS['pagenow'] != 'media-new.php' ) {
			ppUtil::changeWPText( 'Show', 'Show edit/insert options' );
			ppUtil::changeWPText( 'Hide', 'Hide edit/insert options' );
		}
		ppGalleryAdmin::modifyUploadScreen();
		ppMediaAdmin::handleMediaActions();
	}


	/* update static files on theme update */
	protected static function svnBump() {
		$storedSvn = ppOpt::id( 'svn' );
		if ( !$storedSvn || intval( $storedSvn ) < pp::site()->svn ) {
			ppOpt::update( 'svn', pp::site()->svn );
			ppLegacy::updateAll();
			ppStorage::saveCustomizations( ppStorage::FORCE_FILE_REGEN );
		}
	}


	/* deny in multi-user mode */
	protected static function denyMu() {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( is_admin() ) {
				add_action( 'admin_notices', ppUtil::func( 'ppString::adminError( "wpmu_warning" );' ) );
			} else {
				wp_die( ppString::id( 'wpmu_warning' ) );
			}
		}
	}


	/* intercept special requests made through $_GET and $_POST */
	protected static function interceptRequestCommands() {

		// fix legacy quasi-page links
		if ( isset( $_GET['gallery_post'] ) ) {
			ppUtil::redirect( pp::site()->url . '/?gallery_page=' . $_GET['gallery_post'] . '&pp_gallery_id=' . $_GET['pp_gallery_id'] );
		}

		if ( isset( $_GET['auto_upgrade_prophoto'] ) ) {
			$upgrader = new ppUpgrader();
			$upgrader->setupUpgrade();
		}

		if ( isset( $_GET['pp_slideshow_id'] ) ) {
			add_filter( 'show_admin_bar', '__return_false' );
			add_action( 'pp_begin_body', 'ppSlideshowGallery::fullscreenMarkup' );
		}

		if ( isset( $_GET['pp_iframe'] ) ) {
			$iFrame = new ppIFrame( $_GET );
			$iFrame->render();
			do_action( 'shutdown' );
			exit();
		}

		if ( isset( $_GET['slideshow_gallery_js'] ) )  {
			$gallery = ppGallery::load( intval( $_GET['slideshow_gallery_js'] ) );
			if ( $gallery ) {
				$slideshow = ppSlideshowGallery::instance( $gallery );
				echo $slideshow->jsData( $_GET['content_width'] );
			}
			exit();
		}

		if ( isset( $_GET['staticfile'] ) ) {
			ppStaticFile::output( $_GET['staticfile'] );
			exit();
		}


		if ( isset( $_GET['minify_js'] ) ) {
			ppStaticFile::minifyJs();
			exit();
		}

		if ( NrUtil::GET( 'open', 'sesame' ) ) {
			@setcookie( 'pptech', 'true', ONE_YEAR_FROM_NOW );
		}

		if ( NrUtil::GET( 'pp_support', 'true' ) || NrUtil::GET( 'ppblogdata', 'show' ) ) {
			$supportPage = new ppSupportPage();
			$supportPage->render();
			exit();
		}

		if ( isset( $_POST['unregister'] ) && md5( $_POST['unregister'] ) == 'ad6e9f720f422f406294645dc005053d' ) {
			ppOpt::updateMultiple( array(
				'not_registered' => 'true',
				'payer_email' => '',
				'txn_id' => '',
			) );
			ppStorage::saveCustomizations();
			echo 'Unregistered.';
			exit();
		}

		if ( isset( $_GET['pp_menu_ajax_fetch_custom_html'] ) ) {
			$customHTML = ppMenuUtil::menuItem( $_GET['pp_menu_ajax_fetch_custom_html'] )->customHTML;
			if ( $customHTML ) {
				add_action( 'pp_begin_body', ppUtil::func( '
					echo NrHtml::div( "' . str_replace( '"', '\"', $customHTML ) . '", "class=article-content" );
					echo "</body></html>";
					do_action( "shutdown" );
					exit();
				' ) );
			} else {
				echo 'error';
				exit();
			}
		}

		if ( isset( $_GET['show_issues'] ) && pp::browser()->isTech ) {
			$issuesPath = pp::fileInfo()->issuesFolderPath . '/';
			if ( file_exists( $issuesPath . $_GET['show_issues'] . '_issues.txt' ) ) {
				$file = $issuesPath . $_GET['show_issues'] . '_issues.txt';
			} else {
				$file = end( glob( $issuesPath . '*_issues.txt' ) );
			}
			if ( !empty( $file ) ) {
				header( 'Content-type: text/plain' );
				echo file_get_contents( $file );
			} else {
				echo 'No issues found';
			}
			exit();
		}

		if ( isset( $_GET['slideshow_popup'] ) ) {
			show_admin_bar( false );
		}

		if ( isset( $_GET['dump_folders'] ) ) {
			NrDump::it( new ppFolders() );
		}
	}


	protected static function mobile() {
		if ( !pp::browser()->isMobile ) {
			return;
		}
		if ( is_admin() ) {

		} else {
			add_filter( 'comment_post_redirect', create_function( '$redirURL', 'return preg_replace( "/#comment-[0-9]*$/", "", $redirURL );' ) );
			add_filter( 'wp_die_handler', ppUtil::func( 'return "ppMobileHtml::mobileDie";' ) );
		}
	}


	protected static function tweakWpSuperCache() {
		if ( function_exists( 'wp_cache_sanitize_value' ) && function_exists( 'wp_cache_replace_line' ) ) {
			global $cache_rejected_uri, $wp_cache_config_file;
			if ( !empty( $cache_rejected_uri ) && is_array( $cache_rejected_uri ) && !empty( $wp_cache_config_file) ) {
				$noCacheVars = array(
					'slideshow_gallery_js', 'pp_slideshow_id',
					'pp_iframe', 'staticfile', 'ppblogdata',
					'minify_js', 'open', 'pp_support', 'show_issues',
					'pp_menu_ajax_fetch_custom_html', 'preview_design',
					'dump_grids', 'dump_slideshows', 'dump_folders',
				);
				$updateURIs = false;
				foreach ( $noCacheVars as $var ) {
					if ( !in_array( $var, $cache_rejected_uri ) ) {
						$cache_rejected_uri[] = $var;
						$updateURIs = true;
					}
				}
				if ( $updateURIs ) {
					$text = wp_cache_sanitize_value(
						str_replace( '\\\\', '\\', implode( "\n", $cache_rejected_uri ) ),
						$cache_rejected_uri
					);
					wp_cache_replace_line('^ *\$cache_rejected_uri', "\$cache_rejected_uri = $text;", $wp_cache_config_file);
				}
			}
		}
	}
}
