<?php

class NrPostImg {

	protected $id;
	protected $exists;
	protected $filename;
	protected $url;
	protected $urlDir;
	protected $path;
	protected $pathDir;
	protected $width;
	protected $height;
	protected $htmlAttr;
	protected $exif;
	protected $thumbs;
	protected $metaData;
	protected $wpObj;


	public function __construct( $id ) {
		$this->id = intval( $id );
		$this->wpObj = get_post( $this->id );
		if ( $this->wpObj && $this->wpObj->post_type == 'attachment' ) {
			$this->exists = true;
			$this->url = $this->filterURL( $this->wpObj->guid );
			$this->filename = basename( $this->url );
			$this->urlDir = str_replace( $this->filename, '', $this->url );
			$this->path = $this->pathFromUrl( $this->url );
			$this->pathDir = str_replace( $this->filename, '', $this->path );
			$this->setupMeta();
		} else {
			$this->error( "Img with \$id '$id' not found in db" );
			$this->exists = false;
		}
	}


	public function id() {
		return $this->id;
	}


	public function exists() {
		return $this->exists;
	}


	public function filename() {
		return $this->filename;
	}


	public function width() {
		return $this->width;
	}


	public function height() {
		return $this->height;
	}


	public function exif() {
		return $this->exif;
	}


	public function src() {
		return $this->url;
	}


	public function url() {
		return $this->url;
	}


	public function wpObj() {
		return $this->wpObj;
	}


	public function title() {
		if ( ( NrUtil::startsWith( $this->filename, $this->wpObj->post_title ) ) ) {
			$title = '';
		// to handle case where orig image has space, like "foobar copy.jpg", which upon uploading
		// gets filename of "foobar-copy.jpg" but post_title retains space: "foobar copy"
		} else if ( NrUtil::startsWith( $this->filename, str_replace( ' ', '-', $this->wpObj->post_title ) ) ) {
			$title = '';
		} else {
			$title = $this->wpObj->post_title;
		}
		return apply_filters( 'pp_post_img_title', $title, $this );
	}


	public function titleAttr() {
		return esc_attr( $this->title() );
	}


	public function alt() {
		return trim( strip_tags( get_post_meta( $this->id(), '_wp_attachment_image_alt', true ) ) );
	}


	public function caption() {
		return trim( strip_tags( $this->wpObj->post_excerpt ) );
	}


	public function tagObj() {
		return $this->newImgTag( $this->url, array( 'width' => $this->width, 'height' => $this->height ) );
	}


	public function thumb( $size = 'thumbnail' ) {
		if ( isset( $this->thumbs[$size] ) && file_exists( $this->pathFromUrl( $this->thumbs[$size]->src() ) ) ) {
			return $this->thumbs[$size];

		} else if ( $this->maybeCreateThumb( $size ) ) {
			return $this->thumbs[$size];

		} else {
			return $this->tagObj()->addClass( 'thumb-not-found' );
		}
	}


	protected function maybeCreateThumb( $size ) {
		global $_wp_additional_image_sizes;

		// passing in one-off, custom thumb size params
		if ( $custom = $this->customSize( $size ) ) {
			$_wp_additional_image_sizes[$size] = array( 'width' => $custom->width, 'height' => $custom->height, 'crop' => $custom->crop );

		// missing standard size should almost always mean that parent img size was
		// smaller than the thumb size, so it was not needed, thus not generated
		} else if ( $this->isStandardSize( $size ) ) {
			return false;

		} else if ( !array_key_exists( $size, (array) $_wp_additional_image_sizes ) ) {
			$this->error( 'Unknown related img size requested' );
			return false;
		}

		// this indicates we've checked before and made note that this custom size is not needed
		 if ( @in_array( $size, $this->metaData['unneeded_custom_sizes'] ) ) {
			return false;
		}

		if ( !$imgData = NrUtil::imgData( $this->path ) ) {
			return false;
		}

		$sizeData = (object) $_wp_additional_image_sizes[$size];
		$constrainedDims = wp_constrain_dimensions( $imgData->width, $imgData->height, $sizeData->width, $sizeData->height );

		// parent img is smaller than custom size, don't create, make note that we don't check all this crap again
		if ( $constrainedDims == array( $imgData->width, $imgData->height ) ) {

			if ( isset( $this->metaData['unneeded_custom_sizes'] ) ) {
				$this->metaData['unneeded_custom_sizes'][] = $size;
			} else {
				$this->metaData['unneeded_custom_sizes'] = array( $size );
			}

			$this->storeModifiedMetaData();
			return false;
		}

		$makeThumbsPath = $this->path;
		$imgData = NrUtil::imgData( $this->path );
		if ( $imgData && ( $imgData->height + $imgData->width > NrPostImg::maxDownsizeThreshold() ) ) {
			if ( isset( $this->thumbs['large'] ) && $largePath = ppUtil::pathFromUrl( $this->thumbs['large']->src() ) ) {
				$makeThumbsPath = $largePath;
			} else {
				return false;
			}
		}

		$createdThumbData = image_make_intermediate_size( $makeThumbsPath, $sizeData->width, $sizeData->height, $sizeData->crop );

		if ( $createdThumbData ) {
			$this->metaData['sizes'][$size] = $createdThumbData;
			$this->storeModifiedMetadata();
			$this->setupMeta();
			return true;

		} else {
			return false;
		}
	}


	protected function storeModifiedMetaData() {
		wp_update_attachment_metadata( $this->id, $this->metaData );
	}


	protected function setupMeta() {
		$this->metaData = wp_get_attachment_metadata( $this->id );

		if ( empty( $this->metaData ) && @file_exists( $this->path ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$meta = self::wp_generate_attachment_metadata( $this->id, $this->path );
			if ( !empty( $meta ) && is_array( $meta ) ) {
				add_post_meta( $this->id, '_wp_attachment_metadata', $meta );
				$this->metaData = $meta;
			}
		}

		$this->width    = isset( $this->metaData['width'] )  ? intval( $this->metaData['width'] ) : 0;
		$this->height   = isset( $this->metaData['height'] ) ? intval( $this->metaData['height'] ) : 0;
		$this->htmlAttr = isset( $this->metaData['hwstring_small'] ) ? $this->metaData['hwstring_small'] : '';
		if ( isset( $this->metaData['image_meta'] ) ) {
			$this->exif = (object) $this->metaData['image_meta'];
		}

		$thumbsMeta = array();
		if ( isset( $this->metaData['sizes'] ) && is_array( $this->metaData['sizes'] ) ) {
			foreach ( $this->metaData['sizes'] as $size => $thumbData ) {
				$thumbsMeta[$size] = $this->newImgTag(
					$this->urlDir . $thumbData['file'],
					array(
						'width' => intval( $this->metaData['sizes'][$size]['width'] ),
						'height' => intval( $this->metaData['sizes'][$size]['height'] ),
					)
				);
			}
		}
		$this->thumbs = $thumbsMeta;
	}


	protected function newImgTag( $src, $args = null ) {
		return new NrImgTag( $src, $args );
	}


	protected function filterURL( $url ) {
		return $url;
	}


	protected function pathFromUrl( $url ) {
		return str_replace( untrailingslashit( get_option( 'siteurl' ) ), ABSPATH, $url );
	}


	protected function isStandardSize( $size ) {
		return in_array( $size, array( 'thumbnail', 'medium', 'large' ) );
	}


	protected function customSize( $size ) {
		if ( preg_match( '/^[0-9]+x[0-9]+(xCROP)?$/', $size ) ) {
			@list( $width, $height, $crop ) = explode( 'x', $size );
			return (object) array(
				'width' => $width,
				'height' => $height,
				'crop' => ( $crop == 'CROP' ),
			);
		} else {
			return false;
		}
	}


	protected function error( $msg ) {
		trigger_error( $msg, E_USER_WARNING );
	}


	protected static function maxDownsizeThreshold() {
		if ( class_exists( 'ppGdModify' ) ) {
			return ppGdModify::sizeThreshold();
		} else {
			return 6000;
		}
	}


	public static function wp_generate_attachment_metadata( $id, $path ) {
		$img = NrUtil::imgData( $path );
		if ( $img && ( $img->width + $img->height < NrPostImg::maxDownsizeThreshold() ) ) {
			return wp_generate_attachment_metadata( $id, $path );
		} else {
			return array();
		}
	}
}

