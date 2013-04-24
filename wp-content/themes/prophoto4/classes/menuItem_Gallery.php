<?php


class ppMenuItem_Gallery extends ppMenuItem_Internal {


	protected $galleryDisplay;
	protected $galleryID;
	protected $isFullscreen;
	protected $classes;
	protected $galleryType;
	protected $useFullWidth;
	protected $gallery;


	public function url() {
		return $this->gallery ? $this->gallery->url( $this->galleryDisplay ) : '';


		if ( NrUtil::isIn( 'popup_', $this->galleryDisplay ) ) {
			$url = ppSlideshowGallery::popupUrl( $this->galleryID );
			if ( $this->isFullscreen ) {
				$url .= '&fullscreen=1';
			}
			return $url;
		} else {
			return pp::site()->url . '/?gallery_page=' . $this->galleryType . '&pp_gallery_id=' . $this->galleryID . $this->useFullWidth;
		}
	}


	protected function __construct( $ID, $itemData, $children ) {
		$this->galleryID      = $itemData->galleryID;
		$this->gallery        = ppGallery::load( intval( $this->galleryID ) );
		$this->galleryDisplay = $itemData->galleryDisplay;


		$this->galleryType = NrUtil::isIn( 'slideshow', $this->galleryDisplay ) ? 'slideshow' : 'lightbox';

		if ( NrUtil::isIn( '_in_slidedown', $this->galleryDisplay ) ) {
			$this->useFullWidth = '&full_width=1';
			$this->classes[] = 'ajax-fetch-slidedown';
		}

		$this->isFullscreen = ( $this->galleryDisplay == 'fullscreen_popup_slideshow' );

		if ( $this->isFullscreen ) {
			$this->classes[] = 'fullscreen';
			$this->classes[] = 'popup-slideshow';

		} else if ( NrUtil::isIn( 'popup', $this->galleryDisplay ) ) {

			$this->classes[] = 'popup-slideshow';

			if ( $this->gallery ) {
				$this->rel = ppSlideshowGallery::instance( $this->gallery )->popupRelAttr();
			}
		}

		parent::__construct( $ID, $itemData, $children );
	}
}

