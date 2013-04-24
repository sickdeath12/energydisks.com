<?php

class ppGdModify {

	const FEED_MAX_SIZE = 700;
	protected static $sizeThreshold;
	protected $filenameModifier;
	protected $modifiedFilename;
	protected $maxWidth;
	protected $maxHeight;
	protected $origPath;
	protected $imgIsUsable;
	protected $modifiedPath;
	protected $origWidth;
	protected $origHeight;
	protected $width;
	protected $height;
	protected $ext;
	protected $doDownsize;
	protected $doWatermark;
	protected $downsizeSuccess;
	protected $watermarkSuccess;
	protected $originalImg;
	protected $transformedImg;
	protected $wmImg;
	protected $GDType;
	public $tagObj;


	public static function contentImg( $imgTagObj ) {
		if ( !pp::browser()->isMobile || ppOpt::test( 'mobile_enable', 'false' ) ) {
			$img = new ppGdModify( $imgTagObj, ppPostImgUtil::maxWidth() );
		} else {
			/* temporarily removed while we experiment with not constraining mobile
			   content images, and see if customers complain - 11/14/11 */
			// $mobileHeightConstraint = pp::browser()->hasRetinaDisplay ? 700 : 350;
			$img = new ppGdModify( $imgTagObj, ppPostImgUtil::maxWidth() );
			// if ( $img->tagObj->height() > 350 ) {
			// 	$img->tagObj->width( intval( NrUtil::constrainRectSide( 350, $img->tagObj->height(), $img->tagObj->width() ) ) );
			// 	$img->tagObj->height( 350 );
			// }
		}
		return $img->tagObj;
	}


	public static function sidebarImg( $imgTagObj ) {
		$img = new ppGdModify( $imgTagObj, ppSidebar::data()->content_width );
		return $img->tagObj;
	}


	public static function lightboxImg( $imgTagObj ) {
		if ( !pp::browser()->isMobile || ppOpt::test( 'mobile_enable', 'false' ) ) {
			$img = new ppGdModify( $imgTagObj, pp::num()->maxLightboxOverlayImgSize, pp::num()->maxLightboxOverlayImgSize );
		} else {
			$mobileConstraint = pp::browser()->hasRetinaDisplay ? 900 : 450;
			$img = new ppGdModify( $imgTagObj, $mobileConstraint, $mobileConstraint );
		}
		return $img->tagObj;
	}

	public static function feedImg( $imgTagObj ) {
		$img = new ppGdModify( $imgTagObj, self::FEED_MAX_SIZE, self::FEED_MAX_SIZE );
		return $img->tagObj;
	}


	public static function constrainImgSize( $imgTagObj, $maxWidth = 1000000, $maxHeight = 1000000 ) {
		$img = new ppGdModify( $imgTagObj, $maxWidth, $maxHeight );
		return $img->tagObj;
	}


	protected function __construct( $imgTagObj, $maxWidth = 1000000, $maxHeight = 1000000 ) {
		$this->tagObj = $imgTagObj;

		if ( !$this->tagObj || $this->tagObj->filename() === 'nodefaultimage.gif' || $this->tagObj->hasClass( 'ss-first-img' ) ) {
			return;
		}

		if ( !is_numeric( $maxWidth ) || $maxWidth < 1 || !is_numeric( $maxHeight ) || $maxHeight < 1 ) {
			return;
		}

		$this->origPath = ppUtil::pathFromUrl( $this->tagObj->src() );
		if ( NrUtil::isIn( '(pp_', $this->origPath ) ) {
			return;
		}

		$this->ext = NrUtil::fileExt( $this->tagObj->filename() );

		if ( $this->tagObj->width() && $this->tagObj->width() < $maxWidth ) {
			$this->maxWidth = $this->tagObj->width();
		} else {
			$this->maxWidth = $maxWidth;
		}

		if ( $this->tagObj->height() && $this->tagObj->height() < $maxHeight ) {
			$this->maxHeight = $this->tagObj->height();
		} else {
			$this->maxHeight = $maxHeight;
		}

		// setup info about the un-modified image
		$this->determineUsability();
		$this->analyzeDimensions();

		// test whether downsizing and/or watermarking necessary
		$this->prepDownsize();
		$this->prepWatermark();

		// if the file needs modifying
		if ( $this->filenameModifier ) {
			$this->modifyFilename();

			// if the modified file does not already exist, create it
			if ( !file_exists( $this->modifiedPath ) ) {
				$this->transformImg();
			} else {
				$this->tagObj->src( ppUtil::urlFromPath( $this->modifiedPath ) );
			}
		}

		// change width and height attr on img tag
		$this->tagObj->width( $this->width );
		$this->tagObj->height( $this->height );
	}


	protected function transformImg() {

		$addedDimensions = $this->origWidth + $this->origHeight;
		if ( $addedDimensions > self::sizeThreshold() ) {
			$skipTransform = true;
			if ( $largeThumb = ppPostImgUtil::relatedImg( $this->tagObj->src(), 'large' ) ) {
				if ( @file_exists( $largeThumbPath = ppUtil::pathFromUrl( $largeThumb->src() ) ) ) {
					if ( $this->maxWidth < $largeThumb->width() || $this->maxHeight < $largeThumb->height() ) {
						$this->origPath = $largeThumbPath;
						$skipTransform  = false;
					}
				}
			}
			if ( $skipTransform ) {
				return;
			}
		}

		if ( !headers_sent() && !is_admin() ) {
			echo "\n<!-- ppGdModify::transformImg() w:{$this->origWidth} x h:{$this->origHeight}, t={$addedDimensions} -->";
		}

		require_once( TEMPLATEPATH . '/classes/class.gd.php' );
		$this->originalImg = GD_Img::createFromImg( $this->origPath );
		$this->transformedImg = $this->originalImg;

		if ( $this->doDownsize ) {
			$this->downsizeImg();
		}

		if ( $this->doWatermark ) {
			$this->watermarkImg();
		}

		// if we haven't hit any snags downsizing or watermarking, write the file
		if ( $this->downsizeSuccess !== false && $this->watermarkSuccess !== false ) {
			$this->transformedImg->writeToFile( $this->modifiedPath );
		}

		// if writing the file succeeded, update the img tag object that gets returned by the public funcs
		if ( $this->transformedImg->write_success && @file_exists( $this->modifiedPath ) ) {
			$this->tagObj->src( ppUtil::urlFromPath( $this->modifiedPath ) );
		}
	}


	protected function watermarkImg() {
		$watermark = GD_Img::createFromImg( $this->wmImg->path );
		$position = $this->watermarkMergeCoordinates( $watermark );
		$copymergeFunc = ( $watermark->type == 'png' || $watermark->type == 'gif' ) ? 'GD_Img::imagecopymergeAlpha' : 'imagecopymerge';
		$args = array(
			$this->transformedImg->img,
			$watermark->img,
			$position['x'], $position['y'], 0, 0,
			$watermark->width,
			$watermark->height,
			ppOpt::id( 'watermark_alpha', 'int' )
		);
		$this->watermarkSuccess = call_user_func_array( $copymergeFunc, $args );
	}


	protected function downsizeImg() {
		$downsized = new GD_Img( $this->width, $this->height, $this->GDType );
		$downsized->create();
		if ( $this->GDType == 'png' ) {
			imagealphablending( $downsized->img, false );
			imagesavealpha( $downsized->img, true );
		}
		$this->downsizeSuccess = imagecopyresampled(
			$downsized->img,
			$this->originalImg->img,
			0, 0, 0, 0,
			$this->width,
			$this->height,
			$this->originalImg->width,
			$this->originalImg->height
		);
		if ( $this->downsizeSuccess ) {
			$this->transformedImg = $downsized;
			$this->tagObj->addClass( 'gd-downsized' );
		}
	}


	protected function modifyFilename() {
		$modifiedFilename = str_replace( '.' . $this->ext, "(pp{$this->filenameModifier})." . $this->ext, $this->tagObj->filename() );
		$this->modifiedPath = str_replace( $this->tagObj->filename(), $modifiedFilename, $this->origPath );
		if ( NrUtil::isIn( TEMPLATEPATH . '/images/', $this->modifiedPath ) ) {
			$this->modifiedPath = str_replace( TEMPLATEPATH . '/images', pp::fileInfo()->imagesFolderPath, $this->modifiedPath );
		}
	}


	protected function prepWatermark() {
		$this->wmImg = ppImg::id( 'watermark' );

		if ( !$this->imgIsUsable ) {
			return;

		} else if ( !$this->wmImg->exists ) {
			return;

		} else if ( !ppOpt::test( 'image_protection', 'watermark' ) ) {
			return;

		} else if ( NrUtil::isIn( 'masthead_image', $this->origPath ) ) {
			return;

		} else if ( preg_match( '/logo_[0-9]{9}/', $this->origPath ) ) {
			return;

		} else if ( NrUtil::isIn( 'widget_custom_image_', $this->origPath ) ) {
			return;

		} else if ( ppOpt::id( 'watermark_alpha', 'int' ) === 0 ) {
			return;

		} else if ( ( $this->origWidth + $this->origHeight ) < ppOpt::id( 'watermark_size_threshold', 'int' ) ) {
			return;

		} else if ( ppContentRenderer::renderingPosts() && ppHelper::isBeforeWatermarkStartDate( ppPost::fromGlobal() ) ) {
			return;
		}

		$this->doWatermark = true;
		$wmFilename = ppImg::id( 'watermark' )->filename;
		$wmId = ( $wmFilename == 'watermark.png' ) ? 'Default' : preg_replace( "/[^0-9]*/", '', $wmFilename );
		$this->filenameModifier .= "_m" . $wmId;
		$this->filenameModifier .= '_a' . ppOpt::id( 'watermark_alpha', 'int' );
		$this->filenameModifier .= '_p' . preg_replace( "/[a-z ]*/", '', ucwords( ppOpt::id( 'watermark_position' ) ) );
	}


	protected function prepDownsize() {
		if ( $this->tagObj->hasClass( 'do-not-downsize' ) ) {
			return;
		}

		if ( !$this->origWidth || !$this->origHeight ) {
			return;
		}
		$this->width  = $this->origWidth;
		$this->height = $this->origHeight;

		if ( $this->width > $this->maxWidth ) {
			$this->doDownsize  = true;
			$this->width  = $this->maxWidth;
			$this->height = intval( $this->maxWidth / ( $this->origWidth / $this->origHeight ) );
		}

		if ( $this->height > $this->maxHeight ) {
			$this->doDownsize = true;
			$this->height = $this->maxHeight;
			$this->width  = intval( $this->maxHeight / ( $this->origHeight / $this->origWidth ) );
		}

		if ( $this->doDownsize && ppOpt::test( 'gd_img_downsizing', 'enabled' ) && $this->imgIsUsable ) {
			$this->filenameModifier = "_w{$this->width}_h{$this->height}";

		} else if ( $this->doDownsize && ppOpt::test( 'gd_img_downsizing', 'disabled' ) ) {
			$this->doDownsize = false;
		}
	}


	protected function analyzeDimensions() {
		if ( $this->imgIsUsable && $imgData = NrUtil::imgData( $this->origPath ) ) {
			$this->origWidth  = $imgData->width;
			$this->origHeight = $imgData->height;
			$this->GDType     = $imgData->GDType;
			$this->tagObj->width( $this->origWidth );
			$this->tagObj->height( $this->origHeight );
		} else {
			$this->origWidth  = $this->tagObj->width()  ? $this->tagObj->width()  : false;
			$this->origHeight = $this->tagObj->height() ? $this->tagObj->height() : false;
			$path = $this->origPath ? $this->origPath : $this->tagObj->src();
			$this->GDType = ( NrUtil::fileExt( $path ) == 'jpg' ) ? 'jpeg' : NrUtil::fileExt( $path );
		}
	}


	protected function determineUsability() {
		if ( !$this->origPath || !@file_exists( $this->origPath ) ) {
			$this->imgIsUsable = false;
		} else {
			$this->imgIsUsable = in_array( NrUtil::imgData( $this->origPath, 'GDType' ), array( 'jpeg', 'gif', 'png' ) );
		}
	}


	protected function watermarkMergeCoordinates( $watermark ) {
		if ( ppOpt::test( 'gd_img_downsizing', 'enabled' ) ) {
			$width  = $this->width;
			$height = $this->height;
		} else {
			$width  = $this->origWidth;
			$height = $this->origHeight;
		}
		$left_x   = $top_y = 0;
		$center_x = ( $width - $watermark->width ) / 2;
		$right_x  = $width - $watermark->width;
		$middle_y = ( $height - $watermark->height ) / 2;
		$bottom_y = $height - $watermark->height;
		switch ( $pos = ppOpt::id( 'watermark_position' ) ) {
			case 'top left':
				$y = $top_y;
				$x = $left_x;
				break;
			case 'top center':
				$y = $top_y;
				$x = $center_x;
				break;
			case 'top right':
				$y = $top_y;
				$x = $right_x;
				break;
			case 'middle left':
				$y = $middle_y;
				$x = $left_x;
				break;
			case 'middle center':
				$y = $middle_y;
				$x = $center_x;
				break;
			case 'middle right':
				$y = $middle_y;
				$x = $right_x;
				break;
			case 'bottom left':
				$y = $bottom_y;
				$x = $left_x;
				break;
			case 'bottom center':
				$y = $bottom_y;
				$x = $center_x;
				break;
			case 'bottom right':
				$y = $bottom_y;
				$x = $right_x;
				break;
			default:
				new ppIssue( "Unknown value '$pos' for 'watermark_position' in ppGdModify::watermarkMergeCoordinates()" );
				$y = $middle_y;
				$x = $center_x;
		}
		return compact( 'x', 'y' );
	}


	public static function sizeThreshold() {
		if ( self::$sizeThreshold === null ) {
			self::$sizeThreshold = ppOpt::id( 'gd_img_downsizing_max_size', 'int' );
		}
		return self::$sizeThreshold;
	}


	public static function flushCache() {
		self::$sizeThreshold = null;
	}
}


