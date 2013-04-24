<?php

class ppLightboxGallery {


	protected $gallery;
	protected $imgs = array();
	protected $markup = '';


	public function __construct( ppGallery $gallery ) {
		$this->gallery = $gallery;
		$this->imgs = $this->gallery->imgs();
	}


	public function markup() {

		if ( is_feed() ) {
			return $this->gallery->feedMarkup();
		}

		if ( $this->showingMainImg() && isset( $this->imgs[0] ) ) {
			$contentSizedImg  = ppGdModify::contentImg( $this->imgs[0]->tagObj() );
			$lightboxSizedImg = ppGdModify::lightboxImg( new ppImgTag( $this->gallery()->img(0)->src() ) );
			$this->markup .= NrHtml::a( $lightboxSizedImg->src(), $contentSizedImg->addClass( 'pp-lb-img' )->markup(), 'title=' . $this->imgs[0]->titleAttr() );
			unset( $this->imgs[0] );
		}

		$thumbSize    = $this->thumbSize();
		$thumbMargin  = ppOpt::id( 'lightbox_thumb_margin', 'int' );
		$thumbBorders = 2 * ppOpt::id( 'post_pic_border_width', 'int' );
		$thumbUnit    = $thumbSize + $thumbBorders + $thumbMargin;
		$contentWidth = ppHelper::contentWidth( ppSidebar::onThisPage() );
		$fitsPerRow   = ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) ? 10000 : intval( ( $contentWidth + $thumbMargin ) / $thumbUnit );
		$numThumbs    = count( $this->imgs );

		// more images than fit on a row: add one extra img per row, resize all images to exactly fit
		if ( $numThumbs > $fitsPerRow ) {
			$fitsPerRow = $fitsPerRow + 1;
			$thumbUnit = intval( ( $contentWidth + $thumbMargin ) / $fitsPerRow );
			$thumbSize = $thumbUnit - $thumbBorders - $thumbMargin;
			$firstRowWidth = ( $fitsPerRow * $thumbUnit ) - $thumbMargin;

		// num images is same as fits per row, recalculate size for perfect fit
		} else if ( $numThumbs == $fitsPerRow ) {
			$thumbUnit = intval( ( $contentWidth + $thumbMargin ) / $fitsPerRow );
			$thumbSize = $thumbUnit - $thumbBorders - $thumbMargin;
			$firstRowWidth = ( $fitsPerRow * $thumbUnit ) - $thumbMargin;

		// num images less than what would fit per row, leave size as requested, calc row width
		} else {
			$firstRowWidth = ( $numThumbs * $thumbUnit ) - $thumbMargin;
		}

		// build thumbnail markup
		$this->markup .= "<div class=\"pp-lightbox-thumbs\" style=\"width:{$firstRowWidth}px;\">";

		$i = 1; $numThumbsRemaining = $numThumbs;

		foreach ( $this->imgs as $img ) {
			$numThumbsRemaining--;

			$thumb = $img->thumb( "{$thumbSize}x{$thumbSize}xCROP" );
			$thumb->width( $thumbSize )->height( $thumbSize )->style( 'height:' . $thumbSize . 'px;' )->addClass('pp-lb-img');
			$fullsizeImg = ppGdModify::lightboxImg( $img->tagObj() );
			$linkedThumb = NrHtml::a( $fullsizeImg->src(), $thumb->markup(), 'title=' . $img->titleAttr() );

			if ( is_int( $i / $fitsPerRow  ) || $numThumbsRemaining == 0 ) {
				$this->markup .= NrHtml::span( $linkedThumb, 'class=last' );
			} else {
				$this->markup .= $linkedThumb;
			}

			// start a new row when necessary
			if ( is_int( $i / $fitsPerRow  ) && $numThumbs > $i ) {
				if ( $numThumbsRemaining < $fitsPerRow ) {
					$rowWidth = ( $numThumbsRemaining * $thumbUnit ) - $thumbMargin;
				} else {
					$rowWidth = ( $fitsPerRow * $thumbUnit ) - $thumbMargin;
				}
				$this->markup .= "</div><div class=\"pp-lightbox-thumbs\" style=\"width:{$rowWidth}px;\">";
			}
			$i++;
		}

		$this->markup .= '</div>';
		return NrHtml::div( $this->markup, 'id=pp-lightbox-' . $this->gallery->id() . '&class=pp-lightbox pp-lightbox-not-loaded' );
	}


	public function gallery() {
		return $this->gallery;
	}


	protected function thumbSize() {
		if ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) {
			return pp::browser()->hasRetinaDisplay ? 216 : 108;
		} else {
			if ( is_numeric( $this->gallery->lightboxOption( 'thumb_size' ) ) ) {
				return intval( $this->gallery->lightboxOption( 'thumb_size' ) );
			} else {
				return ppOpt::id( 'lightbox_thumb_default_size', 'int' );
			}
		}
	}


	protected function showingMainImg() {
		return ( $this->gallery->lightboxOption( 'show_main_image' ) !== false && ( !pp::browser()->isMobile || ppOpt::test( 'mobile_enable', 'false' ) ) );
	}
}
