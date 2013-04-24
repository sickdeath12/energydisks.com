<?php

class ppGalleryAdmin {


	public static function galleriesTab() {
		$galleryID = @is_numeric( $_REQUEST['pp_gallery_id'] ) ? intval( $_REQUEST['pp_gallery_id'] ) : null;

		if ( $galleryID && isset( $_REQUEST['delete_pp_gallery'] ) && $_REQUEST['delete_pp_gallery'] == 'true' ) {
			if ( $gallery = ppGallery::load( $galleryID ) ) {
				$gallery->delete();
				self::iFrameNotice( 'Gallery deleted.' );
				self::galleriesOverviewScreen();
				return;
			}
		}

		if ( $galleryID && @!empty( $_REQUEST['insert_pp_gallery_as'] ) ) {
			if ( $_REQUEST['insert_pp_gallery_as'] == 'slideshow' ) {
				media_send_to_editor( self::galleryPlaceholderMarkup( $galleryID, 'slideshow' ) );
			} else if ( $_REQUEST['insert_pp_gallery_as'] == 'lightbox' ) {
				media_send_to_editor( self::galleryPlaceholderMarkup( $galleryID, 'lightbox' ) );
			} else if ( $_REQUEST['insert_pp_gallery_as'] == 'fullsize_imgs' ) {
				media_send_to_editor( self::galleryInsertAllMarkup( $galleryID ) );
			}
			exit();

		} else if ( $galleryID || isset( $_REQUEST['create_new_from'] ) ) {
			self::editGalleryScreen( $galleryID );

		} else {
			self::galleriesOverviewScreen();
		}
	}


	protected static function galleryInsertAllMarkup( $galleryID ) {
		$gallery = ppGallery::load( $galleryID );
		return ppMediaAdmin::insertAllImgsMarkup( $gallery->imgs() );
	}


	protected static function editGalleryScreen( $galleryID ) {
		if ( isset( $_REQUEST['create_new_from'] ) && !isset( $_REQUEST['save_changes'] ) ) {
			$title   = $_REQUEST['post_title_text'] ? $_REQUEST['post_title_text'] : "Unnamed Gallery $galleryID";
			$gallery = ppGallery::create( array( 'title' => $title, 'imgs' => explode( ',', $_REQUEST['create_new_from'] ) ) );

			if ( !$gallery ) {
				new ppIssue( 'Create new gallery error. $_POST[create_new_from]:' . $_REQUEST['create_new_from'] );
				self::iFrameNotice( 'Error creating new gallery. Please try again.' );
				self::galleriesOverviewScreen();
				return;
			}

			$gallery->save();

			if ( isset( $_REQUEST['post_id'] ) ) {
				$gallery->associateWithArticle( $_REQUEST['post_id'] );
			}

		} else {

			$gallery = ppGallery::load( $galleryID );

			if ( !$gallery ) {
				self::iFrameNotice( 'Error Loading requested gallery. Please try again.' );
				self::galleriesOverviewScreen();
				return;
			}

			if ( isset( $_REQUEST['save_changes'] ) ) {
				self::saveChanges( $gallery );
			}
		}

		self::renderIFrameScreen( 'ppUtil::renderView( "gallery_edit", compact( "gallery" ) );', compact( 'gallery' ) );
	}


	protected static function saveChanges( ppGallery $gallery ) {
		$gallery->update( 'title',    $_REQUEST['pp_gallery_title'] );
		$gallery->update( 'subtitle', $_POST['pp_gallery_subtitle'] );
		if ( !empty( $_REQUEST['pp_gallery_reorder'] ) ) {
			$gallery->reorder( $_REQUEST['pp_gallery_reorder'] );
		}

		// slideshow-specific options
		if ( !empty( $_REQUEST['slideshow_shopping_cart_url'] ) ) {
			$gallery->slideshowOption( 'shoppingCartUrl', $_REQUEST['slideshow_shopping_cart_url'] );
		}
		if ( trim( $_REQUEST['slideshow_hold_time'] ) !== $gallery->slideshowOption( 'holdTime' ) ) {
			$gallery->slideshowOption( 'holdTime', empty( $_REQUEST['slideshow_hold_time'] ) ? '' : floatval( $_REQUEST['slideshow_hold_time'] ) );
		}
		$gallery->slideshowOption( 'autoStart', isset( $_REQUEST['slideshow_auto_start'] ) );
		if ( !empty( $_REQUEST['slideshow_music_file'] ) ) {
			$gallery->slideshowOption( 'musicFile', $_REQUEST['slideshow_music_file'] );
		}
		$gallery->slideshowOption( 'disableThumbstrip', isset( $_REQUEST['slideshow_disable_thumbstrip'] ) );
		if ( !empty( $_REQUEST['slideshow_disable_thumbstrip'] ) ) {
			$gallery->slideshowOption( 'disableThumbstrip', $_REQUEST['slideshow_disable_thumbstrip'] );
		}

		// lightbox-specific options
		if ( trim( $_REQUEST['lightbox_requested_thumb_size'] ) !== $gallery->lightboxOption( 'thumb_size' ) ) {
			$gallery->lightboxOption( 'thumb_size', empty( $_REQUEST['lightbox_requested_thumb_size'] ) ? '' : intval( $_REQUEST['lightbox_requested_thumb_size'] ) );
		}
		$gallery->lightboxOption( 'show_main_image', ppUtil::formatVal( $_REQUEST['show_main_image'], 'bool' ) );

		$gallery->save();
	}


	protected static function galleriesOverviewScreen() {
		self::renderIFrameScreen( 'ppUtil::renderView( "galleries_tab" );', compact( 'markup' ) );
	}


	public static function modifyUploadScreen() {

		// if uploading images to add to an existing gallery
		if ( isset( $_REQUEST['pp_gallery_id'] ) ) {

			// change main upload form text
			ppUtil::changeWPText( 'Add media files from your computer', '' );
			ppUtil::changeWPText( 'Choose files to upload', 'Choose images to add:' );

			// render gallery visual summary
			add_filter( 'media_upload_form_url', 'ppGalleryAdmin::renderGalleryVisualSummary' );

			// images added hidden notice
			$addedMsg = self::gmailNotice( "Uploaded images have been added to your gallery." );
			add_action( 'post-upload-ui', ppUtil::func( "echo '$addedMsg';" ) );


		// if uploading imgs to create a new gallery
		} else if ( isset( $_REQUEST['new_pp_gallery'] ) ) {

			// images uploaded/new gallery created hidden notice
			$newMsg = 'Uploaded images have been added to a new gallery. Click the edit/insert button below to proceed.';
			add_action( 'post-upload-ui', ppUtil::func( "echo ppGalleryAdmin::gmailNotice( '$newMsg' );" ) );

			// change upload form text
			ppUtil::changeWPText( 'Add media files from your computer', 'Upload images for your new ProPhoto gallery' );

			// create a new gallery to receive the uploaded imgs
			$title = isset( $_GET['title'] ) ? $_GET['title'] : 'Unnamed gallery ' . time();
 			$gallery = ppGallery::create( array( 'title' => $title, 'imgs' => array() ) );
			// this is for the asynch upload callbacks to use
			$_REQUEST['pp_gallery_id'] = $gallery->id();

			// associate with post
			if ( isset( $_REQUEST['post_id'] ) ) {
				$gallery->associateWithArticle( $_REQUEST['post_id'] );
			}

		} else {
			return;
		}


		// get our gallery ID into SwfUploader and BrowserUploader
		add_filter(
			'swfupload_post_params',
			$func = create_function( '$params', '$params["pp_gallery_id"]=$_REQUEST[\'pp_gallery_id\'];return $params;' )
		);
		add_filter( 'upload_post_params', $func );
		add_filter(
			'media_upload_form_url',
			create_function( '$url', 'return $url."&add_to_pp_gallery=1&pp_gallery_id=".$_REQUEST[\'pp_gallery_id\'];' )
		);

		// set action to add new attachment IDs to our gallery when adding
		add_action( 'add_attachment', 'ppGalleryAdmin::associateNewUploadedImgs' );

		// wrap a div around the whole form for specific styling
		add_filter( 'media_upload_form_url', create_function( '$r', 'echo "<div id=\"add-to-gallery-wrap\">"; return $r;' ) );
		add_action( 'admin_footer', ppUtil::func( 'echo "</div>";' ) );
	}



	public static function associateNewUploadedImgs( $imgID ) {
		$gallery = ppGallery::load( intval( $_REQUEST['pp_gallery_id'] ) );
		if ( $gallery ) {
			$gallery->addImg( $imgID );
			$gallery->save();
		}
	}


	public static function renderGalleryVisualSummary( $url ) {
		$gallery = ppGallery::load( intval( $_REQUEST['pp_gallery_id'] ) );
		if ( $gallery ) {
			ppUtil::renderView( 'gallery_visual_summary', compact( 'gallery' ) );
		}
		return $url;
	}


	public static function galleryPlaceholderImgUrl( $gallery, $type ) {
		$placeholderBasePath = TEMPLATEPATH . "/images/{$type}_placeholder_base.jpg";
		$placeholderBaseUrl  = ppUtil::urlFromPath( $placeholderBasePath );

		if ( !is_object( $gallery ) ) {
			return $placeholderBaseUrl;
		}

		require_once( TEMPLATEPATH . '/classes/class.gd.php' );
		$custom      = GD_Img::createFromImg( ppUtil::pathFromUrl( $gallery->img(0)->thumb( '300x200' )->src() ) );
		$placeholder = GD_Img::createFromImg( $placeholderBasePath );
		$button      = GD_Img::createFromImg( TEMPLATEPATH . "/images/{$type}_placeholder_btn.png" );

		if ( !$custom || !$placeholder || !$button || $custom->height > 200 ) {
			return $placeholderBaseUrl;
		}

		$lbOffset = ( $type == 'lightbox' ) ? 8 : 0;

		$firstCopySuccess = imagecopy(
			$placeholder->img, // destination, being copied TO
			$custom->img, // source, copying ONTO destination
			( $placeholder->width - $custom->width ) / 2, // x coordinate being copied TO
			( ( 200 - $custom->height ) / 2 ) + $lbOffset, // y coordinate being copied TO
			0, 0, // x, y on source, where to start copying from
			$custom->width,
			$custom->height
		);

		if ( !$firstCopySuccess ) {
			return $placeholderBaseUrl;
		}

		$secondCopySuccess = imagecopy(
			$placeholder->img, // destination, being copied TO
			$button->img, // source, copying ONTO destination
			0, 0, // x, y, coord on DEST, copying TO
			0, 0, // x, y on source, where to start copying from
			$button->width,
			$button->height
		);

		if ( !$secondCopySuccess ) {
			return $placeholderBaseUrl;
		}

		$placeholderPath = pp::fileInfo()->placeholdersFolderPath . "/{$type}-placeholder-" . $gallery->id() . '.jpg';
		$placeholder->writeToFile( $placeholderPath );
		$customPlaceholderUrl = ppUtil::urlFromPath( $placeholderPath );

		if ( $placeholder->write_success && $customPlaceholderUrl ) {
			return $customPlaceholderUrl;
		} else {
			return $placeholderBaseUrl;
		}
	}


	public static function galleryPlaceholderMarkup( $galleryID, $type ) {
		$gallery = ppGallery::load( $galleryID );
		return NrHtml::img(
			self::galleryPlaceholderImgUrl( $gallery, $type ),
			array(
				'id'    => "pp-$type-$galleryID",
				'class' => "pp-$type-placeholder pp-gallery-placeholder $type-$galleryID mceItem",
				'style' => 'display:none;',
			)
		);
	}


	public static function allGalleryIDs() {
		$galleryFilePaths = glob( pp::fileInfo()->galleryFolderPath . '/*.js' );
		$galleryIDs  = array();
		foreach ( (array) $galleryFilePaths as $filepath ) {
			$galleryIDs[] = intval( str_replace( '.js', '', basename( $filepath ) ) );
		}
		return (array) $galleryIDs;
	}


	protected static function renderIFrameScreen( $code, $arg = false ) {
		ppAdmin::loadStyle( 'media' );
		ppAdmin::loadScript( 'jquery-ui-sortable' );
		ppAdmin::loadScript( 'jquery-ui-tabs' );
		ppAdmin::jQueryUiCss();

		$functionCode = "media_upload_header(); do_action( 'pp_post_media_tabs' ); $code";

		if ( $arg ) {
			// whackiness is to get the name AND value of variable into anonymous func
			ppIFrame::wp_iframe( create_function( '$' . reset( array_keys( $arg ) ), $functionCode ), reset( $arg ) );
		} else {
			ppIFrame::wp_iframe( ppUtil::func( $functionCode ) );
		}
	}


	protected static function iFrameNotice( $str ) {
		add_action( 'pp_post_media_tabs', ppUtil::func( "echo ppGalleryAdmin::gmailNotice( '$str', true );" ) );
	}


	public static function gmailNotice( $txt, $show = false, $extraAttr = null ) {
		$show = $show ? '-show' : '';
		return NrHtml::p( NrHtml::span( $txt ), 'class=gmail-notice' . $show . $extraAttr );
	}


	public static function availableMusicFiles() {
		$fileArray = array();
		for ( $i = 1; $i <= pp::num()->maxAudioUploads; $i++ ) {
			$audio = ppAudioFile::id( "audio{$i}" );
			if ( $audio->exists ) {
				$fileArray[$audio->songName] = $audio->url;
			}
		}
		$files = array_map( 'basename', glob( pp::fileInfo()->musicFolderPath . '/*.{mp3,MP3}', GLOB_BRACE ) );
		foreach ( $files as $file ) {
			if ( !in_array( pp::fileInfo()->musicFolderUrl . '/' . $file, $fileArray ) ) {
				$fileArray[$file] = pp::fileInfo()->musicFolderUrl . '/' . $file;
			}
		}
		return $fileArray;
	}


	public static function trashAssociatedGalleries( $articleID ) {
		$associatedGalleries = ppGallery::galleriesAssociatedWithArticle( $articleID );
		foreach ( $associatedGalleries as $associatedGallery ) {
			$associatedGallery->trash();
		}
	}


	public static function untrashAssociatedGalleries( $articleID ) {
		$associatedGalleryIDs = (array) get_post_meta( $articleID, ppGallery::ASSOCIATED_GALLERY_META_HANDLE, false );
		foreach ( $associatedGalleryIDs as $associatedGalleryID ) {
			ppGallery::untrash( $associatedGalleryID );
		}
	}
}

