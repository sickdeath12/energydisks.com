<?php

class ppAdmin {


	const LOAD_IN_FOOTER = true;
	protected static $optionHelpData;
	protected static $enqueued = array( 'wp_enqueue_script' => array(), 'wp_enqueue_style' => array() );


	public static function addMenuItems() {
		if ( self::hidePpMenuItems() ) {
			return;
		}
		if ( !has_action( 'admin_menu', 'ppAdmin::addMenuItems' ) ) {
			add_action( 'admin_menu', 'ppAdmin::addMenuItems' );

		} else {
			add_menu_page(
			    "ProPhoto",
			    "ProPhoto 4",
			    "edit_themes",
			    "pp-customize",
			    'ppCustomize::render',
			    '',
			    58
			);

			$customizePage = add_submenu_page(
			    "pp-customize",
			    "ProPhoto &raquo; Customize",
			    "Customize",
			    'edit_themes',
			    'pp-customize',
			    'ppCustomize::render'
			);
			add_action( "load-$customizePage", 'ppAdmin::loadCustomizePageFiles' );

			$designsPage = add_submenu_page(
			    "pp-customize",
			    "ProPhoto &raquo; Manage Designs",
			    "Manage Designs",
			    'edit_themes',
			    'pp-designs',
			    'ppDesignsPage::render'
			);
			add_action( "load-$designsPage", 'ppDesignsPage::loadFiles' );
		}
	}


	public static function optionTutorialURL( $id ) {
		$id = preg_replace( '/[0-9]+/', '*num*', $id );
		if ( NrUtil::startsWith( $id, 'grid_category_' ) ) {
			$id = 'grid_category_images';
		}
		if ( isset( self::$optionHelpData['tutorial'][$id] ) ) {
			return admin_url( 'admin-ajax.php' ) . '?action=pp&modal_tutorial=' . self::$optionHelpData['tutorial'][$id];
		} else {
			if ( pp::site()->isDev && !NrUtil::isIn( 'grid_category_', $id ) ) {
				new ppIssue( "No tutorial slug set for option: '$id'" );
			}
			return '';
		}
	}


	public static function optionVideoURL( $id ) {
		$id = preg_replace( '/[0-9]+/', '*num*', $id );
		if ( isset( self::$optionHelpData['video'][$id] ) ) {
			return admin_url( 'admin-ajax.php' ) . '?action=pp&modal_video=' . self::$optionHelpData['video'][$id];
		} else {
			return false;
		}
	}


	public static function optionBlurb( $id ) {
		$modifiedID = preg_replace( '/[0-9]+/', '*num*', $id );
		if ( isset( self::$optionHelpData['blurb'][$id] ) ) {
			return self::$optionHelpData['blurb'][$id];
		} else if ( isset( self::$optionHelpData['blurb'][$modifiedID] ) ) {
			return self::$optionHelpData['blurb'][$modifiedID];
		} else if ( NrUtil::startsWith( $id, 'grid_category_' ) ) {
			return self::$optionHelpData['blurb']['grid_category_img'];
		} else {
			return '';
		}
	}


	public static function videoIconLink( $slug, $title, $size = 'large' ) {
		return NrHtml::a(
			ppAdmin::optionVideoURL( $slug ),
			"Watch the $title video",
			array(
				'id'    => "modal-video-$slug",
				'class' => "modal-video modal-video-$size",
				'title' => esc_attr( "watch the $title video" ),
			)
		);
	}


	public static function showVideoFirstTime( $slug ) {
		if ( !current_user_can( 'level_1' ) ) {
			return;
		}
		$user = wp_get_current_user();
		if ( !ppOpt::test( $handle = "first_time_video_{$slug}_shown_to_user_{$user->ID}" ) && !pp::browser()->isTech ) {
			add_action( 'admin_footer', ppUtil::func( "
				echo NrHtml::script( 'jQuery(document).ready(function($){
					$(\'#modal-video-" . $slug . "\').click();
				});' );
			" ) );
			ppOpt::update( $handle, 'true' );
		}
	}


	public static function warn( $msgId, $replace1 = null, $replace2 = null, $replace3 = null, $replace4 = null ) {
		self::adminNotice( 'error', $msgId, $replace1, $replace2, $replace3, $replace4 );
	}


	public static function advise( $msgId, $replace1 = null, $replace2 = null, $replace3 = null, $replace4 = null ) {
		self::adminNotice( 'updated', $msgId, $replace1, $replace2, $replace3, $replace4 );
	}


	public static function notify( $msgId, $replace1 = null, $replace2 = null, $replace3 = null, $replace4 = null ) {
		self::adminNotice( 'notify updated', $msgId, $replace1, $replace2, $replace3, $replace4 );
	}


	public static function loadPageFiles() {
		$filename = str_replace( '.php', '', $GLOBALS['pagenow'] );
		if ( @file_exists( TEMPLATEPATH . "/adminpages/js/$filename.js" ) ) {
			self::loadFile( "$filename.js" );
		}
		if ( @file_exists( TEMPLATEPATH . "/adminpages/css/$filename.css" ) ) {
			self::loadFile( "$filename.css" );
		}
	}


	public static function loadFile( $filename, $dependencies = array(), $inFooter = false ) {

		// don't load any ProPhoto files when we're auto updating,
		// as certain servers won't delete a file it considers "in use"
		if ( isset( $_GET['auto_upgrade_prophoto'] ) ) {
			return;
		}

		$ext = NrUtil::fileExt( $filename );
		if ( !@file_exists( TEMPLATEPATH . "/adminpages/$ext/$filename" ) ) {
			new ppIssue( "\$filename '$filename' not found in ppAdmin::loadFile()" );
			return;
		}
		$handle = 'pp_nq_' . strtolower( str_replace( '-', '_', preg_replace( "/\..*/", '', $filename ) ) );
		$enqueueFunc = ( $ext == 'js' ) ? 'wp_enqueue_script' : 'wp_enqueue_style';

		self::enqueueFile( $enqueueFunc, $handle, pp::site()->themeUrl . "/adminpages/$ext/$filename", $dependencies, pp::site()->svn, $inFooter );
	}


	public static function loadScript( $handle ) {
		self::enqueueFile( 'wp_enqueue_script', $handle );
	}


	public static function loadStyle( $handle, $url = false ) {
		self::enqueueFile( 'wp_enqueue_style', $handle, $url );
	}


	protected static function enqueueFile( $function, $handle, $url = false, $dependencies = array(), $ver = false, $inFooter = false ) {
		$params = compact( 'handle', 'url', 'dependencies', 'ver', 'inFooter' );
		if ( did_action( 'init' ) ) {
			$function( $params['handle'], $params['url'], $params['dependencies'], $params['ver'], $params['inFooter'] );
		} else {
			if ( !in_array( $params, self::$enqueued[$function] ) ) {
				self::$enqueued[$function][] = $params;
			}
		}
	}


	public static function writeProtectiveIndexes() {
		if ( get_transient( 'pp_checked_protective_indexes_recently' ) ) {
			return;
		}
		$files   = (array) pp::fileInfo();
		$folders = array_filter( $files, create_function( '$item', 'return ( $item[0] == "/" && substr_count( $item, "/" ) > 1 && $item != ABSPATH );' ) );
		foreach ( $folders as $folder ) {
			$indexPath = untrailingslashit( $folder ) . '/index.php';
			if ( !@file_exists( $indexPath ) ) {
				@NrUtil::writeFile( $indexPath, "<?php\n// Silence is golden.\n?>" );
			}
		}
		$possibleRemnantZips = glob( pp::fileInfo()->wpUploadPath . '/prophoto*zip' );
		foreach ( $possibleRemnantZips as $zip ) {
			@chmod( $zip, 0777 );
			@unlink( $zip );
		}
		set_transient( 'pp_checked_protective_indexes_recently', 'yes', 60*60 * 24 * 14 );
	}


	public static function jsToHead( $js ) {
		$js = str_replace( array( '/JRDY', 'JRDY', '$(' ), array( '});', 'jQuery(document).ready(function(){', 'jQuery(' ), $js );
		add_action( 'admin_head', ppUtil::func( "echo \"<script>$js</script>\";" ) );
	}


	public static function cssToHead( $css ) {
		add_action( 'admin_head', ppUtil::func( "echo \"<style>$css</style>\";" ) );
	}


	public static function showSvnFooter( $updateText = null ) {
		if ( $updateText === null ) {
			return add_filter( 'update_footer', 'ppAdmin::showSvnFooter', 100000 );
		}
		return '<span style="margin-right:15px">ProPhoto 4.1 build #<span id="current-svn">' . pp::site()->svn . '</span></span>' . $updateText;
	}


	public static function ftpAdvise() {
		$msgId = ( isset( $_GET['action'] ) && $_GET['action'] == 'do-core-upgrade' ) ? 'ftp_info_advise_core' : 'ftp_info_advise_plugin';
		$msg = addslashes( ppString::id( $msgId ) );
		$js = "JRDY $('input#hostname').parents('table.form-table').before('$msg'); /JRDY";
		self::jsToHead( $js );
	}


	public static function backupPluginNag() {
		if (
			class_exists( 'wpdbBackup' ) ||
			NrUtil::isIn( 'backup-nag-off', ppOpt::id( 'override_css' ) ) ||
			( pp::site()->isDev && !ppUtil::unitTesting() ) ||
			( isset( $_GET['activated'] ) && $_GET['activated'] == 'true' ) ||
			!ppOpt::test( 'activation_time' ) ||
			( time() - ppOpt::id( 'activation_time' ) < ( 60 * 60 * 24 * 40 ) )
		) {
			return;
		}
		add_action( 'admin_notices', ppUtil::func( 'ppString::adminError( "db_backup_plugin_missing" );' ) );
	}


	public static function tinymceTweaks() {
		add_filter( 'mce_external_plugins','ppAdmin::mcePlugins' );
		add_filter( 'mce_buttons_2','ppAdmin::mceInsertBreakTagButton' );
		add_filter( 'mce_css', 'ppAdmin::mceCss' );
	}


	public static function mcePlugins( $plugins ) {
		$plugins['ppInsertBreakTag'] = pp::site()->themeUrl . '/adminpages/js/tinymce.js';
		$plugins['pp_edit_gallery'] = pp::site()->themeUrl . '/adminpages/js/edit_gallery.js?cb=' . pp::site()->svn;
		return $plugins;
	}


	public static function mceInsertBreakTagButton( $buttons ) {
		array_push( $buttons, '|', 'ppInsertBreakTag' );
		return $buttons;
	}


	public static function mceCss( $css ) {
		return $css . ',' . pp::site()->themeUrl . '/adminpages/css/tinymce.css?ver=' . pp::site()->svn;
	}


	public static function jQueryUiCss() {
		if ( !pp::site()->isDev ) {
			ppAdmin::loadStyle( 'jquery-ui-theme-redmond', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/themes/smoothness/jquery-ui.css' );
		} else {
			ppAdmin::loadStyle( 'jquery-ui-theme-redmond', pp::site()->themeUrl . '/tests/helper/jquery-ui.css' );
		}
	}


	public static function loadRemoteJs() {
		echo NrHtml::script( 'jQuery(window).load(function(){
			jQuery.getScript("' . pp::site()->extResourceUrl . '/js/pp_remote.js?ver=' . date( 'ymdH' ) . '" );
		});' );
	}


	protected static function adminNotice( $class, $msgId, $replace1, $replace2, $replace3, $replace4 ) {
		$msg = ppString::id( $msgId, $replace1, $replace2, $replace3, $replace4 );
		self::addNoticeAction( $msg, $msgId . ' ' . $class );
	}


	public static function addNoticeAction( $msg, $class = null ) {
		$encodedMsg = addslashes( htmlentities( $msg ) );
		add_action( 'admin_notices', ppUtil::func( "ppAdmin::echoAdminNoticeMarkup( '$encodedMsg', '$class' );" ) );
	}


	public static function echoAdminNoticeMarkup( $msg, $class = null ) {
		if ( $class ) {
			$class .= ' ';
		}
		$msg = html_entity_decode( stripslashes( $msg ) );
		echo "<div class='{$class}pp-error pp-admin-msg'>$msg</div>";
	}


	protected static function hidePpMenuItems() {
		return ( ppOpt::test( 'dev_hide_options', 'true' ) && !NrUtil::isIn( 'pp_dev', $_SERVER['HTTP_USER_AGENT'] ) );
	}


	public static function loadCustomizePageFiles() {

		// wp core scripts
		ppAdmin::loadScript( 'jquery-ui-draggable' );
		ppAdmin::loadScript( 'jquery-ui-droppable' );
		ppAdmin::loadScript( 'jquery-ui-sortable' );
		ppAdmin::loadScript( 'jquery-ui-position' );
		ppAdmin::loadScript( 'jquery-ui-dialog' );
		ppAdmin::loadScript( 'thickbox' );

		// prophoto scripts
		if ( intval( ppUtil::wpVersion() ) >= 330 ) {
			ppAdmin::loadScript( 'jquery-ui-slider' );
		} else {
			ppAdmin::loadFile( 'jquery.ui.slider.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse' ) );
		}
		ppAdmin::loadFile( 'options.js' );
		ppAdmin::loadFile( 'fontpreview.js' );
		ppAdmin::loadFile( 'upload.js' );
		ppAdmin::loadFile( 'farbtastic.js' );

		// css
		ppAdmin::jQueryUiCss();
		ppAdmin::loadFile( 'customize.css' );
		ppAdmin::loadFile( 'farbtastic.css' );
		ppAdmin::loadStyle( 'pp_fontpreview', ppStaticFile::url( 'fontpreview.css' ) );
		ppAdmin::loadStyle( 'thickbox' );
	}


	public static function enqueuedFiles() {
		foreach ( self::$enqueued as $enqueueFunc => $enqueued ) {
			foreach ( $enqueued as $params ) {
				$enqueueFunc( $params['handle'], $params['url'], $params['dependencies'], $params['ver'], $params['inFooter'] );
			}
		}
	}


	public static function handleAjaxRequests() {
		if ( isset( $_POST['create_new_menu_item'] ) ) {
			if ( !$newID = $_POST['id'] ) {
				new ppIssue( 'No menu ID found to create new menu item' );
				die( 'failure' );
			}
			ppOpt::update( $newID, json_encode( array( 'text' => 'New link item', 'type' => 'new' ) ) );
			do_action( 'shutdown' );
			die( 'new menu item created' );
		}

		if ( isset( $_POST['delete_menu_item'] ) ) {
			$menuItemID = isset( $_POST['menu_item_id'] ) ? $_POST['menu_item_id'] : false;
			if ( !$menuItemID ) {
				new ppIssue( 'No menu ID found to delete menu item' );
				die( 'failure' );
			} else if ( !ppOpt::id( $menuItemID ) ) {
				new ppIssue( "No stored data for menu item with id '$menuItemID'" );
				die( 'failure' );
			}
			ppOpt::delete( $menuItemID );
			do_action( 'shutdown' );
			die( 'menu item deleted' );
		}

		if ( isset( $_POST['update_menu_structure'] ) ) {
			if ( !$menuID = $_POST['menu_id'] ) {
				new ppIssue( 'No menu id specified' );
				die( 'failure' );

			} else if ( !ppOpt::exists( $menuID ) ) {
				new ppIssue( "No menu structure with ID '$menuID' found to update" );
				die( 'failure' );

			} else if ( !$_POST['new_structure'] || null === json_decode( stripslashes( $_POST['new_structure'] ) ) ) {
				new ppIssue( 'Missing or corrupted menu structure ' . $_POST['new_structure'] . json_decode( $_POST['new_structure'] ) );
				die( 'failure' );

			} else {
				ppOpt::update( $menuID, stripslashes( $_POST['new_structure'] ) );
				do_action( 'shutdown' );
				die( 'menu structure recorded' );
			}
		}

		if ( isset( $_GET['load_menu_gallery_previews'] ) ) {
			$galleryIDs = ppGalleryAdmin::allGalleryIDs();
			rsort( $galleryIDs, SORT_NUMERIC );
			foreach ( $galleryIDs as $galleryID ) {
				$gallery = ppGallery::load( $galleryID );
				if ( $gallery ) {
					if ( !$gallery->imgs() ) {
						$gallery->delete();
					} else {
						ppUtil::renderView( 'gallery_link_preview', compact( 'gallery' ) );
					}
				}
			}
			exit();
		}

		if ( isset( $_GET['load_grid_selectable_articles'] ) ) {
			ppGridAdmin::renderArticleSelectables( max( 1, intval( $_GET['paged'] ) ) );
			exit();
		}

		if ( isset( $_GET['load_grid_selectable_galleries'] ) ) {
			ppGridAdmin::renderGallerySelectables();
			exit();
		}

		if ( isset( $_GET['delete_article_grid'] ) ) {
			ppOpt::delete( 'grid_article_' . $_GET['delete_article_grid'] );
			do_action( 'shutdown' );
			die( 'article grid deleted.' );
		}

		if ( isset( $_GET['modal_video'] ) ) {
			echo ppUtil::videoMarkup( $_GET['modal_video'] );
			exit();
		}

		if ( isset( $_GET['modal_tutorial'] ) ) {
			echo '<iframe src="http://www.prophotoblogs.com/support/' . $_GET['modal_tutorial'] . '/?modal_tutorial=1" width="100%" height="100%"></iframe>';
			exit();
		}

		if ( isset( $_GET['download_remote_file'] ) ) {
			echo ppRemoteFiles::download( $_GET['download_remote_file'], $_GET['remote_file_hash'] );
			exit();
		}

		if ( isset( $_GET['set_auto_upgrade_timeout_transient'] ) ) {
			$transientSet = set_transient( 'pp_delay_next_auto_upgrade_attempt', 'true', 60*60 * intval( $_GET['set_auto_upgrade_timeout_transient'] ) );
			echo $transientSet ? 'auto upgrade transient set.' : 'error setting auto upgrade transient.';
			exit();
		}

		if ( isset( $_GET['pptut'] ) && isset( pp::tut()->{$_GET['pptut']} ) ) {
			ppUtil::redirect( pp::tut()->{$_GET['pptut']} );
		}

		if ( isset( $_GET['current_svn'] ) ) {
			echo pp::site()->svn;
			exit();
		}

		if ( isset( $_GET['generate_download_link'] ) ) {
			set_transient( 'pp_download_link_generated_nonce', $fakeNonce = strval( rand( 10000, 99999 ) ), 60*4 );
			echo PROPHOTO_SITE_URL . 'download/?' . http_build_query( array(
				'payer_email'     => ppOpt::id( 'payer_email' ),
				'txn_id'          => ppOpt::id( 'txn_id' ),
				'update_product'  => 'prophoto4',
				'occasion'        => 'admin_latest_build_request',
				'nonce'           => $fakeNonce,
				'nonce_check_url' => admin_url( 'admin-ajax.php' ) . '?action=pp_nopriv&check_download_link_nonce=1',
			) );
			exit();
		}
	}


	public static function handleLoggedOutAjaxRequests() {
		if ( isset( $_GET['check_download_link_nonce'] ) && isset( $_GET['check_nonce_auth'] ) ) {
			if ( get_transient( 'pp_download_link_generated_nonce' ) && md5( $_GET['check_nonce_auth'] ) == 'de6d1b28fe608120687f9cf5961353ed' ) {
				echo get_transient( 'pp_download_link_generated_nonce' );
				exit();
			}
		}
		if ( isset( $_POST['fb_comment_added'] ) && isset( $_POST['articleID'] ) && isset( $_POST['permalink'] ) ) {
			$facebookApiReader       = new ppFacebookAPIReader( new WpHttpRequest( new WP_Http() ) );
			$facebookCommentsHandler = new ppFacebookCommentsHandler( new ppDb() );
			echo $facebookCommentsHandler->processNewComment( $_POST['articleID'], $_POST['permalink'], $facebookApiReader );
			exit();
		}
	}


	public static function regenFilesOnUrlChange() {
		wp_cache_flush();
		pp::register( "constants", new ppConstants() );
		pp::register( "folders", new ppFolders() );
		/* set constants again, pausing to ensure themeUrl is set correctly
		   preventing old, cached value from being written to static files
		   we do it twice, because we need to be sure we can use urlFromPath
		   to get the new themeUrl value */
		$constants = new ppConstants();
		$constants->site->themeUrl = ppUtil::urlFromPath( TEMPLATEPATH );
		pp::register( "constants", $constants );
		ppStorage::saveCustomizations( ppStorage::FORCE_FILE_REGEN );
	}


	public static function _onClassLoad() {
		self::$optionHelpData = ppUtil::loadConfig( 'option_help_data' );
		add_action( 'init', 'ppAdmin::enqueuedFiles' );
	}
}

