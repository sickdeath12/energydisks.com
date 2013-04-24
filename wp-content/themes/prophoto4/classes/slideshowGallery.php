<?php


class ppSlideshowGallery {


	const MAX_VERTICAL_SLIDESHOW_HEIGHT = 800;
	const FULLSCREEN_MAX_WIDTH = 1800;
	const FULLSCREEN_MAX_HEIGHT = 1200;
	protected $widestImgWidth;
	protected $widestImgHeight;
	protected $widestImgUrl;
	protected $tallestImgHeight;
	protected $horizontalImgRatio;
	protected $hasHorizontalImg;
	protected $gallery;
	protected static $musicPlayerRequired = false;
	protected static $slideshows = array();


	public static function btnsSrcs() {
		$btnColor         = ppOpt::id( 'slideshow_btns_color' );
		$btnColorSan      = str_replace( '#', '', $btnColor );
		$startFilename    = 'gallery_btn_start_' . $btnColorSan . '_v2.png';
		$spriteFilename   = 'gallery_btns_sprite_' . $btnColorSan . '_v2.png';
		$startFilepath    = pp::fileInfo()->imagesFolderPath . '/' . $startFilename;
		$spriteFilepath   = pp::fileInfo()->imagesFolderPath . '/' . $spriteFilename;
		$startAaFilepath  = str_replace( '.png', '_aa.png', $startFilepath );
		$spriteAaFilepath = str_replace( '.png', '_aa.png', $spriteFilepath );

		if ( !file_exists( $startFilepath ) ) {
			require_once( TEMPLATEPATH . '/classes/class.gd.php' );
			require_once( TEMPLATEPATH . '/classes/class.gallery-sprite.php' );

			// large-sized buttons, for browsers that anti-alias when downsizing
			$start = new Gallery_Start_Btn( $btnColor, 200 );
			$start->writeToFile( $startFilepath )->destroy();
			$sprite = new Gallery_Sprite( $btnColor, 200 );
			$sprite->writeToFile( $spriteFilepath )->destroy();

			// anti-aliased exactly-sized images for < IE8
			$AaStart = new Gallery_Start_Btn( $btnColor, 35 * 4 );
			$AaStart->halveSize()->halveSize()->writeToFile( $startAaFilepath )->destroy();
			$AaSprite = new Gallery_Sprite( $btnColor, 20 * 4 );
			$AaSprite->halveSize()->halveSize()->writeToFile( $spriteAaFilepath )->destroy();
		}

		return array(
			'sprite'    => ppUtil::urlFromPath( $spriteFilepath ),
			'sprite_aa' => ppUtil::urlFromPath( $spriteAaFilepath ),
			'start'     => ppUtil::urlFromPath( $startFilepath ),
			'start_aa'  => ppUtil::urlFromPath( $startAaFilepath ),
		);
	}


	public static function fullscreenMarkup() {
		$gallery = ppGallery::load( intval( $_GET['pp_slideshow_id'] ) );
		if ( $gallery ) {
			$slideshow = self::instance( $gallery );
			echo $slideshow->markup();
			ppUtil::renderView( 'copyright_footer' );
			echo "\n\n</div>\n";
			ppHtml::lateConditionalJavascript();
			echo "</body>\n</html>";
			exit();
		}
	}


	public static function instance( ppGallery $gallery ) {
		if ( !isset( self::$slideshows[ $gallery->id() ] ) ) {
			self::$slideshows[ $gallery->id() ] = new ppSlideshowGallery( $gallery );
		}
		return self::$slideshows[ $gallery->id() ];
	}


	public static function needsSwfObject() {
		return (bool) self::$musicPlayerRequired;
	}


	public static function popupUrl( $galleryID ) {
		return pp::site()->url . '/?slideshow_popup=1&pp_slideshow_id=' . $galleryID;
	}


	public function id() {
		return $this->gallery->id();
	}


	public function gallery() {
		return $this->gallery;
	}


	public function widestImgWidth() {
		return $this->widestImgWidth;
	}


	public function horizontalImgRatio() {
		return $this->horizontalImgRatio;
	}


	public function popupRelAttr() {
		$dims = $this->dimensions( 1000 );
		return $dims['slideshowWidth'] . 'x' . $dims['slideshowHeight'];
	}


	public function dimensions( $maxWidth ) {
		if ( $maxWidth == 'fullscreen' ) {
			return array();
		}

		if ( $this->hasHorizontalImg ) {
			$noControlsWidth  = min( $maxWidth, $this->widestImgWidth() );
			$noControlsHeight = $noControlsWidth / $this->horizontalImgRatio();

		} else {
			if ( $this->tallestImgHeight > self::MAX_VERTICAL_SLIDESHOW_HEIGHT ) {
				$noControlsHeight = self::MAX_VERTICAL_SLIDESHOW_HEIGHT;
				$noControlsWidth  = $this->widestImgWidth * ( self::MAX_VERTICAL_SLIDESHOW_HEIGHT / $this->tallestImgHeight );
			} else {
				$noControlsHeight = $this->tallestImgHeight;
				$noControlsWidth  = $this->widestImgWidth;
			}
			if ( $maxWidth < $noControlsWidth ) {
				$noControlsHeight = ( $maxWidth * $noControlsHeight ) / $noControlsWidth;
				$noControlsWidth  = $maxWidth;
			}
		}

		if ( ppOpt::test( 'slideshow_controls_overlaid', 'true' ) || $this->gallery->slideshowOption( 'disableThumbstrip' ) ) {
			$slideshowWidth  = $viewingAreaWidth  = $noControlsWidth;
			$slideshowHeight = $viewingAreaHeight = $noControlsHeight;

		} else if ( ppOpt::test( 'slideshow_controls_position', 'top || bottom' ) ) {
			$slideshowWidth      = $viewingAreaWidth = $noControlsWidth;
			$viewingAreaHeight   = $noControlsHeight;
			$slideshowHeight     = $noControlsHeight + $this->controlsShortDim();
			$iPadSlideshowHeight = $noControlsHeight + $this->iPadControlsShortDim();

		} else {
			if ( $noControlsWidth + $this->controlsShortDim() > $maxWidth ) {
				$slideshowWidth        = $noControlsWidth;
				$viewingAreaWidth      = $slideshowWidth - $this->controlsShortDim();
				$viewingAreaHeight     = $viewingAreaWidth / $this->horizontalImgRatio();
				$slideshowHeight       = $viewingAreaHeight;
				$iPadViewingAreaWidth  = $noControlsWidth - $this->iPadControlsShortDim();
				$iPadViewingAreaHeight = $iPadViewingAreaWidth / $this->horizontalImgRatio();
				$iPadSlideshowHeight   = $iPadViewingAreaHeight;
			} else {
				$slideshowWidth     = $noControlsWidth + $this->controlsShortDim();
				$iPadSlideshowWidth = $noControlsWidth + $this->iPadControlsShortDim();
				$slideshowHeight    = $noControlsHeight;
				$viewingAreaHeight  = $noControlsHeight;
				$viewingAreaWidth   = $noControlsWidth;
			}
		}

		return array_map( 'round', array(
			'slideshowWidth'        => $slideshowWidth,
			'slideshowHeight'       => $slideshowHeight,
			'viewingAreaWidth'      => $viewingAreaWidth,
			'viewingAreaHeight'     => $viewingAreaHeight,
			'iPadSlideshowWidth'    => isset( $iPadSlideshowWidth )    ? $iPadSlideshowWidth    : $slideshowWidth,
			'iPadSlideshowHeight'   => isset( $iPadSlideshowHeight )   ? $iPadSlideshowHeight   : $slideshowHeight,
			'iPadViewingAreaWidth'  => isset( $iPadViewingAreaWidth )  ? $iPadViewingAreaWidth  : $viewingAreaWidth,
			'iPadViewingAreaHeight' => isset( $iPadViewingAreaHeight ) ? $iPadViewingAreaHeight : $viewingAreaHeight,
			'mobileSlideshowWidth'  => $noControlsWidth,
			'mobileSlideshowHeight' => $noControlsHeight,
		) );
	}


	public function controlsShortDim() {
		return intval(
			ppOpt::id( 'slideshow_thumb_size' ) +
			2 * ( ppOpt::id( 'slideshow_thumb_padding' ) + ppOpt::id( 'slideshow_thumb_border_width' ) )
		);
	}


	public function iPadControlsShortDim() {
		$thumbSize    = max( ppOpt::id( 'slideshow_thumb_size', 'int' ), 100 );
		$paddingWidth = max( ppOpt::id( 'slideshow_thumb_padding', 'int' ), 15 );
		return $thumbSize + ( 2 * ( $paddingWidth + ppOpt::id( 'slideshow_thumb_border_width', 'int' ) ) );
	}


	public function jsData( $maxWidth ) {
		$dimensions = $this->dimensions( $maxWidth );

		$data = array_merge(
			array_slice( $dimensions, 0, 8 ),
			array(
				'title' => $this->gallery->title(),
				'subtitle' => $this->gallery->subtitle(),
			),
			$this->gallery->slideshowOptions()
		);

		if ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) {
			$mobileMargins = 2 * ppOpt::id( 'mobile_content_margin', 'int' );
			$data['mobile_portrait_dims']  = array_slice( $dimensions, -2 );
			$mobileMaxWidth = pp::browser()->isIPhone ? ppMobileHtml::IPHONE_MAX_CSS_WIDTH : ppMobileHtml::STANDARD_MOBILE_DEVICE_MAX_WIDTH;
			$data['mobile_landscape_dims'] = array_slice( $this->dimensions( $mobileMaxWidth - $mobileMargins ), -2 );
			$imgDimensions = $this->dimensions( pp::browser()->mobileScreenWidth - $mobileMargins );
		} else {
			$imgDimensions = $dimensions;
			$data['mobile_portrait_dims']  = $dimensions;
			$data['mobile_landscape_dims'] = $dimensions;
		}

		$thumbSize = ppOpt::id( 'slideshow_thumb_size' );
		$thumbSizeStr = "{$thumbSize}x{$thumbSize}xCROP";

		if ( pp::browser()->isIPad && $thumbSize < 100 ) {
			$thumbSize = 100;
			$thumbSizeStr = "100x100xCROP";
		}

		foreach ( $this->gallery->imgs() as $index => $img ) {
			$constrain = $this->constraints( $imgDimensions, ( $maxWidth == 'fullscreen' ) );
			$imgTag = ppGdModify::constrainImgSize( $img->tagObj(), $constrain['width'], $constrain['height'] );
			$data['imgs'][$index] = array(
				'fullsizeSrc' => $imgTag->src(),
				'thumbSrc' => $img->thumb( $thumbSizeStr )->src(),
			);
		}

		return json_encode( $data );
	}


	public function markup() {
		if ( is_feed() ) {
			return $this->gallery->feedMarkup();
		}

		$autostart = $this->gallery->slideshowOption( 'autoStart' ) ? ' autostart' : '';
		$maxWidth  = isset( $_GET['slideshow_popup'] ) ? 1000 : ppHelper::contentWidth( ppSidebar::onThisPage() );
		if ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) {
			$maxWidth = ppHelper::devicePixelAdjustedContentWidth();
		}
		$constrain = $this->constraints( $this->dimensions( $maxWidth ), ( isset( $_GET['fullscreen'] ) ) );
		$firstImg  = ppGdModify::constrainImgSize( $this->gallery->img(0)->tagObj(), $constrain['width'], $constrain['height'] );

		if ( count( $this->gallery->imgs() ) == 1 ) {
			return NrHtml::img( $firstImg->src(), 'class=slideshow-fail-only-one-img aligncenter' );
		}

		$musicData = '';
		if ( $musicFile = $this->gallery->slideshowOption( 'musicFile' ) ) {
			$pathFixedURL = ppPathfixer::fix( $musicFile );
			if ( @file_exists( $musicFilePath = ppUtil::pathFromUrl( $pathFixedURL ) ) ) {
				self::$musicPlayerRequired = true;
				$musicData  = '" data-music-file="' . $pathFixedURL . '"';
				$musicData .= ' data-music-autostart="' . ppOpt::id( 'slideshow_mp3_autostart' ) . '"';
				$musicData .= ' data-music-loop="' . ppOpt::id( 'slideshow_mp3_loop' );
			} else {
				new ppIssue( 'Unable to find MP3 "' . $musicFile . '" for slideshow, deleted from gallery.' );
				$this->gallery->slideshowOption( 'musicFile', '' );
				$this->gallery->save();
			}
		}

		return NrHtml::div( NrHtml::img(
			$firstImg->src(), 'class=exclude ss-first-img' ),
			array(
				'id'    => 'pp-slideshow-' . $this->gallery->id(),
				'class' => 'pp-slideshow pp-gallery pp-slideshow-not-loaded' . $autostart,
				'data'  => 'options-file|' . @ppStaticFile::url( 'slideshow.js' ) . $musicData,
			)
		);
	}


	protected function __construct( ppGallery $gallery ) {
		$this->gallery = $gallery;
		$this->analyzeImgs();
	}


	protected function constraints( $dimensions, $fullscreen ) {
		$constrain = array();
		if ( $fullscreen ) {
			$constrain['width']  = self::FULLSCREEN_MAX_WIDTH;
			$constrain['height'] = self::FULLSCREEN_MAX_HEIGHT;
		} else {
			$constrain['width']  = $dimensions['viewingAreaWidth'];
			$constrain['height'] = $dimensions['viewingAreaHeight'];
		}
		return $constrain;
	}


	protected function analyzeImgs() {
		$horizontalImgRatios = array();

		foreach ( $this->gallery->imgs() as $img ) {

			if ( $img->width() > $this->widestImgWidth ) {
				$this->widestImgWidth  = $img->width();
				$this->widestImgHeight = $img->height();
				$this->widestImgUrl    = $img->url();
			}
			$this->tallestImgHeight = max( $this->tallestImgHeight, $img->height() );

			if ( $img->width() > $img->height() ) {
				$this->hasHorizontalImg = true;
				$ratio = strval( $img->width() / $img->height() );
				isset( $horizontalImgRatios[$ratio] ) ? $horizontalImgRatios[$ratio]++ : $horizontalImgRatios[$ratio] = 1;
			}
		}

		if ( $horizontalImgRatios ) {
			uasort( $horizontalImgRatios, create_function( '$a, $b', 'return ( $a > $b ) ? -1 : 1;' ) );
			$ratio = reset( array_keys( $horizontalImgRatios ) );
			$this->horizontalImgRatio = NrUtil::isIn( '.', $ratio ) ? floatval( $ratio ) : intval( $ratio );
		}
	}
}
