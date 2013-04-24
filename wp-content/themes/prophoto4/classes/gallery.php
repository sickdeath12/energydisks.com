<?php

class ppGallery {


	const RETURN_ASSOC = true;
	const ASSOCIATED_GALLERY_META_HANDLE = 'associated_pp_galleries';
	protected static $galleries = array();
	protected static $isQuasiPage = false;
	protected static $maxNumImgs;
	protected $id;
	protected $title;
	protected $filepath;
	protected $subtitle = '';
	protected $slideshowOptions = array();
	protected $lightboxOptions = array();
	protected $imgs;


	public static function create( $data, $id = null ) {
		if ( $id && !is_int( $id ) ) {
			new ppIssue( 'ppGallery::create() requires integer or null for $id param' );
			return false;
		}
		if ( $id === null ) {
			$id = time();
		}
		if ( !is_array( $data ) || !isset( $data['title'] ) || !isset( $data['imgs'] ) || !is_array( $data['imgs'] ) ) {
			new ppIssue( 'Invalid $data passed to ppGallery::create()' );
			return false;
		}
		$gallery = new ppGallery( $id, $data );
		self::$galleries[$id] = $gallery;
		$gallery->save();
		return $gallery;
	}


	public static function load( $id ) {
		if ( !is_int( $id ) ) {
			new ppIssue( 'ppGallery::load() requires integer for $id param' );
			return false;
		}
		if ( !isset( self::$galleries[$id] ) ) {
			if ( !self::readStorage( $id ) ) {
				return false;
			}
		}
		return self::$galleries[$id];
	}


	public static function setupGalleryQuasiPage() {
		if ( isset( $_GET['gallery_page'] ) && $gallery = ppGallery::load( intval( $_GET['pp_gallery_id'] ) ) ) {
			global $wp_query, $post;
			self::$isQuasiPage = true;
			$quasiPageObj = ppContentRenderer::emptyPostObject();
			$quasiPageObj->post_title = $gallery->title();
			$quasiPageObj->post_content = ppGalleryAdmin::galleryPlaceholderMarkup( $gallery->id(), $_GET['gallery_page'] );
			$post = $quasiPageObj;
			$wp_query->post = $quasiPageObj;
			$wp_query->queried_object = $quasiPageObj;
			$wp_query->posts = array( $quasiPageObj );
			$wp_query->is_home = false;
			$wp_query->is_page = true;
			$wp_query->is_singular = true;
			add_action( 'wp_head', ppUtil::func( "echo '<link rel=\"canonical\" href=\"" . self::galleryQuasiPageURL() . "\" />\n';" ), 1 );
		}
	}

	public static function isGalleryQuasiPage() {
		return self::$isQuasiPage;
	}


	public static function galleryQuasiPageURL() {
		return pp::site()->url . '/?gallery_page=' . $_GET['gallery_page'] . '&pp_gallery_id=' . $_GET['pp_gallery_id'];
	}


	public static function galleriesAssociatedWithArticle( $articleID ) {
		$associatedGalleries  = array();
		$associatedGalleryIDs = (array) get_post_meta( $articleID, self::ASSOCIATED_GALLERY_META_HANDLE, false );
		rsort( $associatedGalleryIDs, SORT_NUMERIC );
		foreach ( $associatedGalleryIDs as $associatedGalleryID ) {
			if ( intval( $associatedGalleryID ) && $associatedGallery = ppGallery::load( intval( $associatedGalleryID ) ) ) {
				$associatedGalleries[] = $associatedGallery;
			}
		}
		return $associatedGalleries;
	}


	public function reorder( $order ) {
		if ( !is_string( $order ) ) {
			new ppIssue( '$order param must be parseable string for ppGallery::reorder' );
			return;
		}
		$orderArray = array();
		parse_str( $order, $orderArray );
		$newOrder = ( isset( $orderArray['imgs'] ) ) ? $orderArray['imgs'] : null;
		if ( is_array( $newOrder ) ) {
			$reordered = array();
			foreach ( $newOrder as $index => $imgId ) {
				foreach ( $this->imgs as $img ) {
					if ( $img->id() == $imgId ) {
						$reordered[$index] = $img;
					}
				}
			}
			$this->imgs = $reordered;
		}
	}


	public function associateWithArticle( $articleID ) {
		add_post_meta( $articleID, self::ASSOCIATED_GALLERY_META_HANDLE, $this->id() );
	}


	public function delete() {
		$this->deleteStaticFile();
		global $wpdb;
		$associateds = $wpdb->get_results( "SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '{$this->id}'" );
		foreach ( (array) $associateds as $associated ) {
			delete_post_meta( $associated->post_id, self::ASSOCIATED_GALLERY_META_HANDLE );
		}
		ppGrid::flushMarkupCache( 'galleries' );
	}


	public function trash() {
		$this->deleteStaticFile();
		$this->filepath = pp::fileInfo()->galleryFolderPath . "/trashed_{$this->id}.js";
		$this->save();
		unset( self::$galleries[$this->id()] );
	}


	public static function untrash( $galleryID ) {
		if ( @file_exists( $trashedFilePath = pp::fileInfo()->galleryFolderPath . "/trashed_$galleryID.js" ) ) {
			NrUtil::writeFile( pp::fileInfo()->galleryFolderPath . "/$galleryID.js", file_get_contents( $trashedFilePath ) );
			@unlink( $trashedFilePath );
		} else {
			new ppIssue( 'Unable to locate trashed file for gallery: ' . $galleryID );
		}
	}


	public function save() {
		$data = array();

		$data['title'] = $this->title();
		if ( $this->subtitle ) {
			$data['subtitle'] = $this->subtitle();
		}

		$data['imgs'] = array();
		foreach ( $this->imgs as $img ) {
			$data['imgs'][] = $img->id();
		}

		if ( $this->slideshowOptions() ) {
			$data['slideshow_options'] = $this->slideshowOptions();
		}
		if ( $this->lightboxOptions() ) {
			$data['lightbox_options'] = $this->lightboxOptions();
		}

		NrUtil::writeFile( $this->filepath, json_encode( $data ) );
		ppGrid::flushMarkupCache( 'galleries' );
	}


	public function widestImgWidth() {
		return $this->widestImgWidth;
	}


	public function update( $index, $newVal ) {
		if ( !is_string( $index ) || !isset( $this->{$index} ) ) {
			new ppIssue( "Unknown \$index '$index' passed to ppGallery::update()" );
		} else if ( $index == 'imgs' ) {
			new ppIssue( 'Cannot modify $this->imgs with ppGallery::update()' );
		} else {
			if ( in_array( $index, array( 'title', 'subtitle' ) ) && !is_string( $newVal ) ) {
				return;
			} else {
				$this->{$index} = $newVal;
			}
		}
	}


	public function addImg( $imgID ) {
		if ( !is_numeric( $imgID ) ) {
			new ppIssue( 'Non-numeric passed to $gallery->addImg()' );
			return;
		}
		$this->imgs[] = new ppPostImg( $imgID );
	}


	public function title() {
		return stripslashes( $this->title );
	}


	public function imgs() {
		return $this->imgs;
	}


	public function img( $index ) {
		return isset( $this->imgs[$index] ) ? $this->imgs[$index] : new ppPostImg( 0 );
	}


	public function id() {
		return $this->id;
	}


	public function subtitle() {
		return stripslashes( $this->subtitle );
	}


	public function slideshowOptions() {
		return $this->slideshowOptions;
	}


	public function lightboxOptions() {
		return $this->lightboxOptions;
	}


	public function slideshowOption( $id, $set = null ) {
		return $this->displayOption( 'slideshow', $id, $set );
	}


	public function lightboxOption( $id, $set = null ) {
		return $this->displayOption( 'lightbox', $id, $set );
	}


	public function feedMarkup() {
		$markup = '';
		foreach ( $this->imgs() as $img ) {
			$markup .= ppGdModify::feedImg( $img->tagObj() )->markup();
		}
		return $markup;
	}


	public function url( $displayType ) {
		if ( pp::browser()->isMobile && NrUtil::isIn( 'popup', $displayType ) ) {
			return $this->url( 'slideshow_in_page' );
		}
		switch ( $displayType ) {
			case 'popup_slideshow':
				return ppSlideshowGallery::popupUrl( $this->id() );
			case 'fullscreen_popup_slideshow':
				return ppSlideshowGallery::popupUrl( $this->id() ) . '&fullscreen=1';
			case 'slideshow_in_page':
			case 'slideshow_in_post': // legacy fallthrough
				return pp::site()->url . '/?gallery_page=slideshow&pp_gallery_id=' . $this->id();
			case 'slideshow_in_slidedown':
				return pp::site()->url . '/?gallery_page=slideshow&pp_gallery_id=' . $this->id() . '&full_width=1';
			case 'lightbox_in_page':
			case 'lightbox_in_post': // legacy fallthrough
				return pp::site()->url . '/?gallery_page=lightbox&pp_gallery_id='  . $this->id();
			case 'lightbox_in_slidedown':
				return pp::site()->url . '/?gallery_page=lightbox&pp_gallery_id='  . $this->id() . '&full_width=1';
		}
	}


	protected function displayOption( $type, $id, $set = null ) {
		if ( !is_string( $id ) ) {
			new ppIssue( 'Invalid non-string $id passed to ppGallery::' . $type . 'Option()' );
			return;
		}
		$varName = $type . 'Options';
		if ( $set !== null ) {
			$this->{$varName}[$id] = $set;
		} else {
			return isset( $this->{$varName}[$id] ) ? $this->{$varName}[$id] : null;
		}
	}


	protected function __construct( $id, $data ) {
		$this->id = $id;
		$this->filepath = pp::fileInfo()->galleryFolderPath . "/{$this->id}.js";
		$this->imgs = array();

		if ( count( $data['imgs'] ) > $this->maxNumImgs() ) {
			new ppIssue( 'Maximum number of images for gallery limit exceeded in gallery: ' . $this->id );
			$data['imgs'] = array_slice( $data['imgs'], 0, $this->maxNumImgs() );
		}

		foreach ( $data['imgs'] as $imgId ) {
			$img = new ppPostImg( $imgId );
			if ( $img->exists() && NrUtil::isWebSafeImg( $img->filename() ) ) {
				$this->imgs[] = $img;
			}
		}
		$this->title = $data['title'];
		if ( isset( $data['subtitle'] ) ) {
			$this->subtitle = $data['subtitle'];
		}
		if ( isset( $data['slideshow_options'] ) && $data['slideshow_options'] ) {
			$this->slideshowOptions = $data['slideshow_options'];
		}
		if ( isset( $data['lightbox_options'] ) && $data['lightbox_options'] ) {
			$this->lightboxOptions = $data['lightbox_options'];
		}
	}


	protected function maxNumImgs() {
		if ( self::$maxNumImgs === null ) {
			$defaultMaxNumImgs = 250;
			self::$maxNumImgs  = apply_filters( 'pp_max_num_gallery_imgs', $defaultMaxNumImgs );
		}
		return self::$maxNumImgs;
	}


	protected function deleteStaticFile() {
		if ( !@unlink( $this->filepath ) ) {
			new ppIssue( "Failure to delete() gallery file at '{$this->filepath}'" );
		}
	}


	protected static function readStorage( $id ) {
		$filepath = pp::fileInfo()->galleryFolderPath . "/$id.js";
		if ( @file_exists( $filepath ) ) {
			$data = json_decode( file_get_contents( $filepath ), self::RETURN_ASSOC );
			self::$galleries[$id] = new ppGallery( $id, $data );
			return true;
		} else if ( @file_exists( pp::fileInfo()->galleryFolderPath . "/trashed_$id.js" ) ) {
			return false;
		} else {
			// creating issue from within menu admin area can cause debug_backtrace() to hang
			// likely because of too much recursion
			if ( !class_exists( 'ppMenuAdmin', $useAutoloader = false ) ) {
				new ppIssue( "Unknown \$id '$id' passed to ppGallery::load()" );
			}
			return false;
		}
	}


	public static function flushCache() {
		self::$galleries   = array();
		self::$isQuasiPage = false;
		self::$maxNumImgs  = null;
	}
}
