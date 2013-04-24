<?php

class ppIFrame {


	protected $content;
	protected $args;
	protected static $uploadType;
	protected static $uploadImgID;
	protected static $fontZipID;


	public static function jsToParent( $js ) {
		if ( !is_string( $js ) ) {
			new ppIssue( 'Invalid input' );
			return '';
		}
		return NrHtml::script( NrHtml::cdata( "var win = window.dialogArguments || opener || parent || top; win.$js;" ) );
	}


	public static function refreshParent() {
		return ppIFrame::jsToParent( 'location.href = win.location.href' );
	}


	public static function url( $args, $width = '', $height = '', $postID = '0' ) {
		$href = pp::site()->wpurl . '/wp-admin/?pp_iframe=' . $args . '&post_id=' . $postID . '&TB_iframe=true';
		if ( $width ) {
			$href .= "&amp;width=$width";
		}
		if ( $height ) {
			$href .= "&amp;height=$height";
		}
		return $href;
	}


	public function __construct( $args ) {
		if ( !NrUtil::isAssoc( $args ) || !isset( $args['pp_iframe'] ) ) {
			new ppIssue( 'Invalid $args' );
			return;
		}

		$this->args = $args;
		$this->content = $this->args['pp_iframe'];
		$this->bootstrap();
	}

	private function sendHeader() {
		if ( !headers_sent() ) {
			header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
		}
	}


	public function render() {

		ppAdmin::loadScript( 'jquery' );
		ppAdmin::loadFile( 'admin.css' );
		ppAdmin::loadFile( 'iframe.css' );
		ppAdmin::loadFile( 'iframe.js' );

		$this->sendHeader();

		// the only way wp gives us to set id for iframe body tag
		$GLOBALS['body_id'] = strval( $this->content );

		switch ( $this->content ) {

			case 'grid_admin':
				ppGridAdmin::render();
				break;

			case 'edit_menu_item':
				ppMenuAdmin::renderEditMenuItemScreen( $this->args['menu_item_id'] );
				break;

			case 'design_zip_upload':
			case 'file_upload_form':
				$this->printImgUploadForm();
				break;

			case 'file_reset_form':
				self::wp_iframe( create_function( '$_this', 'ppIFrame::printFileResetForm( $_this );' ), $this );
				break;

			case 'file_deleted':

				if ( isset( $_POST['file_type'] ) && $_POST['file_type'] == 'audio_file' ) {
					@unlink( ppAudioFile::id( $_POST['file_id'] )->path );
					ppImg::update( $_POST['file_id'], '' );
					ppImg::update( $_POST['file_id'] . '_filename', '' );
					$js = self::jsToParent( 'ppRefreshModifiedAudioFile({id:"' . $_POST['file_id'] . '"},"delete"); win.tb_remove();' );
					$msg = 'MP3 audio file #' . preg_replace( '/^audio/', '', $_POST['file_id'] ) . ' successfully deleted.';
					self::wpIframeMsg( $js . $msg );

				} else if ( isset( $_POST['file_id'] ) ) {
					$imgID = $_POST['file_id'];
					ppImg::update( $imgID, '' );
					$js = self::jsToParent( 'ppRefreshModifiedFile(' . json_encode( ppImg::id( $imgID ) ) . ',"delete"); win.tb_remove();' );
					$msg = "Image <code>$imgID</code> deleted.";
					self::wpIframeMsg( $js . $msg );

				} else if ( isset( $_POST['font_zip_id'] ) ) {
					ppOpt::delete( $_POST['font_zip_id'] );
					$js = self::jsToParent( 'ppRefreshModifiedFont({id:"' . $_POST['font_zip_id'] . '"},"delete"); win.tb_remove();' );
					$msg = "Font <code>{$_POST['font_zip_id']}</code> deleted.";
					self::wpIframeMsg( $js . $msg );
				}
				break;

			case 'font_upload_success':
				$font = $this->args['font'];
				$data = array(
					'id' => $font->id(),
					'num' => str_replace( 'custom_font_', '', $font->id() ),
					'name' => $font->name(),
					'css' => ppFontUtil::fontFaceCss( $font->id() ),
				);
				$js = self::jsToParent( 'ppRefreshModifiedFont(' . json_encode( $data ) . '); win.tb_remove();' );
				self::wpIframeMsg( $js . $font->successMsg() );
				break;

			case 'img_upload_success':
				if ( !isset( $this->args['file_id'] ) || !is_string( $this->args['file_id'] ) ) {
					new ppIssue( 'Invalid or no "file_id" passed for upload success iframe' );
					$this->args['file_id'] = 'unknown';
				}
				self::wp_iframe( 'ppIFrame::printUploadSuccess', $this->args['file_id'] );
				break;

			case 'audio_upload_success':
				$audioFile = ppAudioFile::id( $this->args['file_id'] );
				$js = self::jsToParent( 'ppRefreshModifiedAudioFile(' . json_encode( $audioFile ) . '); win.tb_remove();' );
				$msg = 'MP3 audio file #' . $audioFile->number . ' successfully updated.';
				self::wpIframeMsg( $js . $msg );
				break;

			case 'design_import_success':
				$refreshJs = self::refreshParentUrl( array(
					'add_notice' => 'design_import_success',
					'design_name' => urlencode( $this->args['imported_design']->name() )
				) );
				$importMsg = ppString::id( 'design_import_success', $this->args['imported_design']->name() );
				self::wpIframeMsg( $refreshJs . $importMsg );
				break;


			case 'msg':
				if ( !isset( $this->args['msg'] ) || !is_string( $this->args['msg'] ) ) {
					new ppIssue( 'Invalid or no "msg" passed for iframe' );
					$this->args['msg'] = 'unknown error';
				}
				self::wp_iframe( create_function( '$msg', 'echo "<p>$msg</p>";' ), $this->args['msg'] );
				break;

			case 'new_design_form':
				self::wp_iframe( ppUtil::func( 'ppUtil::renderView( "iframe_new_design_form" );' ) );
				break;

			case 'new_design_from_starter_form':
				$starterDesigns = ppStarterDesigns::data();
				$starterId      = $_GET['starter'];
				$starterName    = $starterDesigns->{$starterId}->name;
				self::varToView( 'iframe_new_design_from_starter_form', array( 'starter_id' => $starterId, 'starter_name' => $starterName ) );
				break;

			case 'edit_design_form':
				self::designToView( $_GET['design_id'], 'iframe_edit_design_form' );
				break;

			case 'copy_design_form':
				self::designToView( $_GET['design_id'], 'iframe_copy_design_form' );
				break;

			case 'export_design':
				$exportResultMsg = ppDesignUtil::export( $_GET['design_id'] );
				self::wpIframeMsg( $exportResultMsg );
				break;

			case 'export_everything':
				$exportEverythingResultMsg = ppDesignUtil::exportEverything();
				self::wpIframeMsg( $exportEverythingResultMsg );
				break;

			case 'designs_page_data_POSTed':
				$this->afterProcessPOST( $_POST );
				break;

			case 'import_p3_designs':
				if ( !empty( $_POST ) ) {
					unset( $_POST['import_p3_designs'] );
					foreach ( array_keys( $_POST ) as $designID ) {
						ppImportP3::localDesignByID( $designID );
					}
					$refreshJs = self::refreshParentUrl( array(
						'add_notice' => 'p3_design_import_success',
					) );
					$importMsg = ppString::id( 'p3_design_import_success' );
					self::wpIframeMsg( $refreshJs . $importMsg );
				} else {
					ppUtil::renderView( 'import_p3_designs_form', array( 'designs' => ppImportP3::unimportedDesigns() ) );
				}
				break;

			case 'export_design_for_store':
				ppAdmin::loadFile( 'export-for-design-store.css' );
				ppAdmin::loadFile( 'export-for-design-store.js' );
				$designerExport = new ppDesignerExport( $_GET['design_id'], $_POST, $_FILES );
				if ( !$designerExport->isCurrentEnoughBuild() ) {
					$designerExport->renderResult();
				} else {
					if ( empty( $_POST ) ) {
						$this->prepSimpleUploadForm();
						self::designToView( $_GET['design_id'], 'export_for_design_store' );
					} else {
						$designerExport->process();
						$designerExport->renderResult();
					}
				}
				break;

			case 'get_facebook_id':
				ppFacebook::renderIDFinderScreen();
				break;

			default:
				new ppIssue( "Unknown \$this->content '$this->content'",  'tech' );
		}

		unset( $GLOBALS['body_id'] );
	}


	private static function varToView( $view, $varArray ) {
		if ( !is_string( $view ) || !NrUtil::isAssoc( $varArray ) ) {
			new ppIssue( 'Bad input types' );
			return;
		}
		$func = create_function( '$varArray', 'ppUtil::renderView( "' . $view . '", $varArray );' );
		self::wp_iframe( $func, $varArray );
	}


	private function afterProcessPOST( $post ) {
		if ( !isset( $post['pp_POST_identifier'] ) ) {
			new ppIssue( 'Invalid form data, missing "pp_POST_identifier" index' );
			self::wpIframeMsg( '<p>Sorry, there was a problem with your request. Please try again.</p>' );
			return;
		}

		switch ( $post['pp_POST_identifier'] ) {

			case 'designs_page_edit_meta':
				$design = ppStorage::requestDesign( $post['design_id'] );
				$refreshJs = self::refreshParentUrl( array( 'add_notice' => 'edit_meta_success', 'design_id' => $design->id() ) );
				self::wpIframeMsg( $refreshJs . '<p>' . ppString::id( 'design_meta_updated', $design->name() ) . '</p>' );
				break;

			case 'designs_page_create_new':
			case 'designs_page_copy':
				$refreshJs = self::refreshParentUrl( array(
					'add_notice' => 'new_design_created',
					'design_name' => urlencode( $post['new_design_name'] ) )
				);
				self::wpIframeMsg( $refreshJs . '<p>' . ppString::id( 'new_design_created', $post['new_design_name'] ) . '</p>' );
				break;
		}
	}


	private static function refreshParentUrl( $params ) {
		$url = admin_url( 'admin.php?page=pp-designs&' . http_build_query( $params ) );
		return self::jsToParent( "location.href = '$url'" );
	}


	private static function wpIframeMsg( $msg ) {
		self::wp_iframe( create_function( '$msg', 'echo $msg;' ), $msg );
	}


	private static function designToView( $designId, $view ) {
		$design = ppStorage::requestDesign( $designId );
		if ( $design ) {
			self::wp_iframe( create_function( '$design', 'ppUtil::renderView( "' . $view . '", compact( "design" ) );' ), $design );
		} else {
			new ppIssue( "Unable to load design '{$designId}' for view '$view'" );
		}
	}


	public static function printUploadSuccess( $imgID ) {
		echo self::jsToParent( 'ppRefreshModifiedFile(' . json_encode( ppImg::id( $imgID ) ) . ');' );
		echo self::jsToParent( 'tb_remove()' );
		echo "<p>File <code>$imgID</code> successfully updated.</p>";
		remove_action( 'init', 'widget_akismet_register', 10 );
	}


	private function printImgUploadForm() {
		$this->prepSimpleUploadForm();

		// this allows us to get extra input fields inside the upload form
		// created within the iframe content_func call below
		if ( isset( $this->args['file_id'] ) ) {
			self::$uploadImgID = $this->args['file_id'];
		}
		if ( isset( $this->args['font_zip_id'] ) ) {
			self::$fontZipID = $this->args['font_zip_id'];
		}
		self::$uploadType = $this->args['upload_type'];
		add_action( 'pre-upload-ui', 'ppIFrame::uploadFormAdditionalFields' );

		// actually prints the page, using media_upload_type_form( "pp_img" )
		// as the $content_func() to generate the main content within wp_iframe()
		self::wp_iframe( ppUtil::func( 'media_upload_type_form( "pp_img" );' ) );
	}


	private function prepSimpleUploadForm() {

		// these scripts required to prevent js errors
		ppAdmin::loadScript( 'swfupload-handlers' );
		ppAdmin::loadScript( 'utils' );

		// force use of browser uploader
		add_filter( 'flash_uploader', '__return_false' );

		remove_action( 'post-upload-ui', 'media_upload_max_image_resize' );
		remove_action( 'post-upload-ui', 'media_upload_text_after', 5 );

		// remove media tabs
		add_filter( 'media_upload_tabs', '__return_false' );
	}


	public static function wp_iframe( $func, $var = null ) {
		if ( !did_action( 'init' ) ) {
			/* this is a hacky bandaid and at some point i need to fix it */
			remove_action( 'init', 'widget_akismet_register', 10 );
			do_action( 'init' );
		}
		wp_iframe( $func, $var );
	}


	public static function uploadFormAdditionalFields() {
		echo NrHtml::hiddenInput( 'pp_POST_identifier', 'upload_file' );
		echo NrHtml::hiddenInput( 'file_id', self::$uploadImgID );
		echo NrHtml::hiddenInput( 'font_zip_id', self::$fontZipID );
		echo NrHtml::hiddenInput( 'upload_type', self::$uploadType );
		echo NrHtml::hiddenInput( 'formurl', $_SERVER['REQUEST_URI'] );

		// when the thickbox iframe js works, it removes the "TB_iframe" from uri
		$iframed = NrUtil::isIn( 'TB_iframe=true', $_SERVER['REQUEST_URI'] ) ? 'false' : 'true' ;
		echo NrHtml::hiddenInput( 'iframed', $iframed );
		self::$uploadType  = null;
		self::$uploadImgID = null;
	}


	public static function printFileResetForm( $_this ) { ?>
		<h3>Delete this file?</h3>
		<form action="<?php echo trailingslashit( pp::site()->wpurl ); ?>?pp_iframe=file_deleted" method="post">
			<?php

			if ( isset( $_this->args['file_id'] ) ) {
				echo NrHtml::hiddenInput( 'file_id', $_this->args['file_id'] );

			} else if ( isset( $_this->args['font_zip_id'] ) ) {
				echo NrHtml::hiddenInput( 'font_zip_id', $_this->args['font_zip_id'] );
			}

			if ( isset( $_this->args['upload_type'] ) && $_this->args['upload_type'] == 'audio_file' ) {
				echo NrHtml::hiddenInput( 'file_type', 'audio_file' );
			}

			?>
			<?php echo NrHtml::hiddenInput( 'action', 'reset' ); ?>
			<input type="submit" value="Yes, delete">
		</form>
		<?php
	}


	private function bootstrap() {
		// i was drinking a beer when i coded this
		register_admin_color_schemes();
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		ppAdmin::jsToHead( addslashes( ppUtil::siteData( $js = true ) ) );
	}
}

