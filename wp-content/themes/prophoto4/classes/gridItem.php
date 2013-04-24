<?php


class ppGridItem {


	protected $title;
	protected $subtitle;
	protected $text;
	protected $shortText;
	protected $date;
	protected $img;
	protected $url;
	protected $postImg;
	protected $aAttr = '';


	public function __construct( $input = null, $extraInput = null ) {

		if ( $input instanceof ppPost ) {
			$this->processPost( $input );

		} else if ( $input instanceof ppGallery ) {
			$this->processGallery( $input, $extraInput );

		} else if ( $input instanceof ppCategory ) {
			$this->processCategory( $input );
		}
	}


	public function title() {
		return $this->title;
	}


	public function subtitle() {
		return $this->subtitle;
	}


	public function text( $itemSize = null ) {
		if ( $itemSize && $itemSize < 375 ) {
			return $this->shortText;
		} else {
			return $this->text;
		}
	}


	public function date() {
		return $this->date;
	}


	public function img() {
		return $this->img;
	}


	public function url() {
		return $this->url;
	}


	public function aAttr() {
		return $this->aAttr;
	}


	public function imgTag( $thumbDims ) {
		@list( $width, $height ) = explode( 'x', $thumbDims );
		$gridImgTag = $this->postImg()->thumb( $thumbDims );
		if ( $gridImgTag->width() > $width ) {
			$gridImgTag->width( $width );
			$gridImgTag->style( "max-height:{$height}px;" );
		}
		return $gridImgTag;
	}


	public function postImg() {
		$this->postImg = ppParentImg::fromUrl( $this->img() );
		if ( !$this->postImg ) {
			$fallBack = ppImg::id( 'grid_article_img_fallback' );
			if ( $fallBack->exists && !$fallBack->isRemote ) {
				$this->postImg = ppParentImg::fromUrl( $fallBack->url );
			} else {
				$this->postImg = ppParentImg::fromUrl( pp::site()->themeUrl . '/images/' . $fallBack->defaultFilename );
			}
		}
		if ( $this->postImg ) {
			return $this->postImg;
		} else {
			new ppIssue( 'Unable to load postImg for grid item' );
			return new ppImgTag( pp::site()->themeUrl . '/images/blank.gif' );
		}
	}


	protected function processPost( ppPost $post ) {
		$excerpt              = strip_tags( $post->filteredExcerpt() );
		$this->text           = $this->trim( $excerpt, 120 ) . $post->readMoreLink( 'span' );
		$this->shortText      = $this->trim( $excerpt, 55 );
		$this->title          = $post->title();
		$this->date           = $post->publishedDate();
		$this->img            = $post->excerptImgSrc();
		$this->url            = $post->permalink();
		if ( !$this->img ) {
			$this->img = ppImg::id( 'grid_article_img_fallback' )->url;
		}
		$this->aAttr .= ' title="permalink to ' . esc_attr( $this->title() ) . '"';
	}


	protected function processGallery( ppGallery $gallery, $displayStyle ) {
		$this->title     = $gallery->title();
		$this->subtitle  = $gallery->subtitle();
		$this->img       = $gallery->img(0)->src();
		$this->url       = $gallery->url( $displayStyle );
		$this->text      = '<span class="num-imgs">' . count( $gallery->imgs() ) . '</span> images';
		$this->shortText = $this->text;
		$this->aAttr    .= ' title="show gallery ' . esc_attr( $this->title() ) . '"';
		if ( $displayStyle == 'popup_slideshow' ) {
			$this->aAttr .= ' class="popup-slideshow" rel="' . ppSlideshowGallery::instance( $gallery )->popupRelAttr() . '"';
		} else if ( $displayStyle == 'fullscreen_popup_slideshow' ) {
			$this->aAttr .= ' class="popup-slideshow fullscreen" rel="' . ppSlideshowGallery::instance( $gallery )->popupRelAttr() . '"';
		}
	}


	protected function processCategory( $category ) {
		$this->title     = $category->name();
		$this->text      = $this->trim( $category->description(), 120 );
		$this->shortText = $this->trim( $category->description(), 55 );
		$this->url       = $category->url();
		$this->img       = $category->img();
		$this->subtitle  = '<span class="num-posts">' . $category->count() . '</span> posts';
		$this->aAttr    .= ' title="category archives - ' . esc_attr( $this->title() ) . '"';
		return;
	}


	protected function trim( $text, $length ) {
		if ( strlen( $text ) > $length ) {
			return substr( $text, 0, $length ) . '...';
		} else {
			return $text;
		}
	}

}

