<?php

class ppParentImg {


	private $inputUrl;
	private $inputPath;
	private $inputFilename;
	private $parentUrl;
	private $parentPath;
	private $relativeUrl;
	private $found;
	private $postImgObj = false;


	public static function fromUrl( $inputUrl ) {
		$parent = new ppParentImg( $inputUrl );
		return $parent->postImgObj();
	}


	protected function maybeMoveThemeImg() {
		if ( NrUtil::isIn( TEMPLATEPATH, $this->inputPath ) ) {
			$movedPath = str_replace( TEMPLATEPATH, pp::fileInfo()->folderPath, $this->inputPath );
			if ( file_exists( $movedPath ) || ppUtil::moveFile( $this->inputPath, $movedPath ) ) {
				$this->inputUrl = ppUtil::urlFromPath( $movedPath );
			}
		}
	}


	private function __construct( $inputUrl ) {
		if ( !$this->inputUrlValid( $inputUrl ) ) {
			return;
		}

		$this->maybeMoveThemeImg();

		$this->parentUrl   = preg_replace( '/-[0-9]*x[0-9]*\./', '.', $this->inputUrl );
		$this->parentUrl   = preg_replace( '/\(pp_w[0-9]+_h[0-9]+[^\)]+\)/', '', $this->parentUrl );
		$this->relativeUrl = str_replace( pp::fileInfo()->wpUploadUrl . '/', '', $this->parentUrl );
		global $wpdb;

		// first, we try to locate the image based on our own helper meta data, which uses the relative path
		// to the image to try lookup the image in the DB, which should work even if blog addresses change
		$parentPost = $wpdb->get_row( $wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_pp_attachment_helper' AND meta_value = '%s'",
			 $this->relativeUrl
		) );
		if ( isset( $parentPost->post_id ) ) {
			$this->postImgObj = new ppPostImg( $parentPost->post_id );

		// if we can't find a helper meta entry, we should be able to lookup the image in the db based on the full
		// url. if we can find it, add the helper meta so that we can do this with the relative path in the future
		} else {
			$parentPost = $wpdb->get_row( $wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE guid = '%s'",
				$this->parentUrl
			) );
			if ( isset( $parentPost->ID ) ) {
				add_post_meta( $parentPost->ID, '_pp_attachment_helper', $this->relativeUrl );
				$this->postImgObj = new ppPostImg( $parentPost->ID );
			}
		}

		// if we can't find the post, but we have a good parent img, insert a new post
		if ( !$this->postImgObj ) {
			$this->parentPath = ppUtil::pathFromUrl( $this->parentUrl );

			if ( @file_exists( $this->parentPath ) && $imgData = NrUtil::imgData( $this->parentPath ) ) {

				$imgStub = rtrim( '.' . NrUtil::fileExt( $this->inputUrl ), basename( $this->inputUrl ) );

				$addedId = wp_insert_post( array(
					'post_title' => $imgStub,
					'post_content' => '',
					'post_type' => 'attachment',
					'post_name' => $imgStub . time(),
					'guid' => $this->parentUrl,
					'post_mime_type' => $imgData->mime,
					'post_status' => 'inherit',
				) );

				if ( !function_exists( 'wp_generate_attachment_metadata' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
				}

				$meta = NrPostImg::wp_generate_attachment_metadata( $addedId, $this->parentPath );

				if ( !empty( $meta ) && is_array( $meta ) ) {
					add_post_meta( $addedId, '_wp_attachment_metadata', $meta );
					add_post_meta( $addedId, '_pp_attachment_helper', $this->relativeUrl );
					$this->postImgObj = new ppPostImg( $addedId );
				}
			}
		}
	}


	private function postImgObj() {
		return $this->postImgObj;
	}


	private function inputUrlValid( $inputUrl ) {
		if ( !is_string( $inputUrl ) && !is_null( $inputUrl ) ) {
			new ppIssue( 'Non-string $inputUrl' );
			return false;
		}

		if ( basename( $inputUrl ) == 'nodefaultimage.gif' ) {
			return false;
		}

		$this->inputPath = ppUtil::pathFromUrl( $inputUrl );

		if ( !@file_exists( $this->inputPath ) ) {
			return false;
		}

		$this->inputUrl = $inputUrl;
		return true;
	}

}
