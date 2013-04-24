<?php

class ppMediaAdmin {


	public static function insertAllImgs( $imgIDs ) {
		if ( is_string( $imgIDs ) ) {
			$imgIDs = explode( ',', $imgIDs );
		}
		$imgs = array();
		foreach ( $imgIDs as $imgID ) {
			$imgs[] = new ppPostImg( $imgID );
		}
		if ( !function_exists( 'media_send_to_editor') ) {
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
		}
		return media_send_to_editor( self::insertAllImgsMarkup( $imgs ) );
	}


	public static function insertAllImgsMarkup( $imgs ) {
		$markup = '';
		foreach ( $imgs as $img ) {
			$imgTag = $img->tagObj();
			$imgTag->addClass( 'pp-insert-all' );
			$imgTag->addClass( 'size-full' );
			$imgTag->addClass( 'aligncenter' );
			$imgTag->title( $img->title() );
			$imgTag->alt( $img->alt() );
			if ( $img->caption() ) {
				$imgTag->addClass( 'has-caption' );
			}
			$markup .= $imgTag->markup();
			if ( $img->caption() ) {
				$markup .= NrHtml::p( $img->caption(), 'class=pp-caption pp-insert-all-caption' );
			}
		}
		return $markup;
	}


	public static function addButtonsBelowUploadForm() {
		ppAdmin::loadScript( 'jquery-ui-sortable' );

		if ( !has_action( 'admin_print_footer_scripts', 'ppMediaAdmin::addButtonsBelowUploadForm' ) ) {
			add_action( 'admin_print_footer_scripts', 'ppMediaAdmin::addButtonsBelowUploadForm' );
			return;
		}

		if ( did_action( 'post-upload-ui' ) || NrUtil::GET( 'tab', 'gallery' ) || ( isset( $_POST['save'] ) && isset( $_POST['attachments'] ) ) ) {

			echo NrHtml::submit( 'Insert all images', 'id=insert-all-imgs&class=pp-media-btn button&name=pp_insert_all' );
			echo NrHtml::submit( 'New ProPhoto gallery from images...', 'id=create-new-pp-gallery&class=pp-media-btn button&name=pp_new_gallery' );
			echo NrHtml::hiddenInput( 'post_title_text', '' );

			if ( isset( $_REQUEST['pp_gallery_id'] ) ) {
				echo NrHtml::submit( 'Edit/Insert ProPhoto Gallery...', 'id=go-to-edit-pp-gallery&class=pp-media-btn button&name=edit_pp_gallery' );
				echo NrHtml::hiddenInput( 'pp_gallery_id', $_REQUEST['pp_gallery_id'] );
			}
		}
	}


	public static function modifyUploadIFrameTabs( $tabsToFilter = null ) {
		if ( $tabsToFilter == null ) {
			add_filter( 'media_upload_tabs', 'ppMediaAdmin::modifyUploadIFrameTabs', 15 );
			add_filter( 'media_upload_pp_galleries', 'ppGalleryAdmin::galleriesTab' );
		} else {
			if ( apply_filters( 'pp_hide_upload_from_url_tab', true ) ) {
				unset( $tabsToFilter['type_url'] );
			}
			if ( isset( $tabsToFilter['gallery'] ) ) {
				$tabsToFilter['gallery'] = preg_replace( '/[^\(]*\(/', 'Uploaded (', $tabsToFilter['gallery'] );
			}
			return array_merge( array( 'pp_galleries' => 'ProPhoto Galleries' ), $tabsToFilter );
		}
	}


	public static function uploadIFrameUrl( $tab = 'type', $addArgs = null ) {
		$ID  = ppUtil::editArticleID();
		$url = "media-upload.php?tab=$tab&type=image&post_id=$ID";
		if ( $addArgs ) {
			$url .= "&$addArgs";
		}
		return esc_url( $url . '&TB_iframe=1' );
	}


	public static function changePostUploadButtons() {
		if ( !has_action( 'media_buttons', 'ppMediaAdmin::changePostUploadButtons' ) ) {
			add_action( 'media_buttons', 'ppMediaAdmin::changePostUploadButtons', 5 );
		} else {
			if ( ppUtil::wpVersion() < 330 ) {
				remove_action( 'media_buttons', 'media_buttons' );
				ppUtil::renderView( 'media_upload_buttons_legacy' );
			} else {
				add_action( 'media_buttons', ppUtil::func( "ppUtil::renderView( 'media_upload_buttons' );" ) );
			}
		}
	}


	public static function handleMediaActions() {
		if ( !isset( $_POST['attachments'] ) || !is_array( $_POST['attachments'] ) ) {
			return;
		}

		$saveImgChanges = ppUtil::func( "
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			media_upload_form_handler();
		" );

		if ( isset( $_POST['pp_insert_all'] ) ) {
			$saveImgChanges();
			ppMediaAdmin::insertAllImgs( array_keys( $_POST['attachments'] ) );
			exit();
		}

		if ( isset( $_POST['pp_new_gallery'] ) ) {
			$saveImgChanges();
			$newGalleryURL  = str_replace( '#038;', '', ppMediaAdmin::uploadIFrameUrl( 'pp_galleries' ) );
			$newGalleryURL .= '&create_new_from=' . implode( ',', array_keys( $_POST['attachments'] ) );
			$newGalleryURL .= '&post_title_text=' . $_POST['post_title_text'];
			@header( "Location: $newGalleryURL" );
			exit();
		}

		if ( isset( $_POST['edit_pp_gallery'] ) ) {
			$saveImgChanges();
			$editGalleryURL  = str_replace( '#038;', '', ppMediaAdmin::uploadIFrameUrl( 'pp_galleries' ) );
			$editGalleryURL .= '&pp_gallery_id=' . $_POST['pp_gallery_id'];
			@header( "Location: $editGalleryURL" );
			exit();
		}
	}
}
