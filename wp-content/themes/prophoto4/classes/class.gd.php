<?php


/* generic GD Image class */
class GD_Img {

	public $height;
	public $width;
	public $img;


	public static function createFromImg( $imgPath ) {
		if ( !NrUtil::isWebSafeImg( $imgPath ) ) {
			new ppIssue( 'Incorrect file type passed to GD_Img::createFromImg()' );
			return false;
		}

		if ( !file_exists( $imgPath ) ) {
			new ppIssue( "\$imgPath '$imgPath' not found for GD_Img::createFromImg()" );
			return false;
		}
		$img = new GD_Img( null, null, NrUtil::imgData( $imgPath, 'GDType' ) );
		$img->create( $imgPath );
		return $img;
	}


	public function __construct( $width = null, $height = null, $type = 'png' ) {
		if ( $width ) {
			$this->width = $width;
			if ( !$height ) {
				$height = $width;
			}
			$this->height = $height;
		}
		$this->type = $type;
	}


	public function create( $path = null ) {
		if ( $path && !headers_sent() && !is_admin() ) {
			echo "\n<!-- GD_Img::create() " . ppUtil::urlFromPath( $path ) . " -->\n\n";
		}
		if ( $path === null ) {
			if ( !headers_sent() && !is_admin() ) {
				echo "\n<!-- GD_Img::create() w:{$this->width} x h:{$this->height} -->\n\n";
			}
			$this->img = imagecreatetruecolor( $this->width, $this->height );
		} else if ( !is_string( $path ) || !@file_exists( $path ) ) {
			return $this;
		} else if ( !in_array( $this->type, array( 'png', 'jpeg', 'gif' ) ) ) {
			return $this;
		} else if ( $this->type == 'png' ) {
			$this->img = imagecreatefrompng( $path );
		} else if ( $this->type == 'jpeg' ) {
			$this->img = imagecreatefromjpeg( $path );
		} else if ( $this->type == 'gif' ) {
			$this->img = imagecreatefromgif( $path );
		}
		if ( $this->img && $path ) {
			$this->width  = imagesx( $this->img );
			$this->height = imagesy( $this->img );
		}
		return $this;
	}


	public function destroy() {
		imagedestroy( $this->img );
		return $this;
	}


	public function colorAllocate( $hex ) {
		if ( !is_string( $hex ) || !preg_match( '/#(?:[0-9a-fA-F]{3}){1,2}$/i', $hex ) ) {
			new ppIssue( "Invalid hex color '$hex' passed to GD_Img::colorAllocate()" );
			$hex = '#ffffff';
		}
		$rgb = $this->hexToRgb( $hex );
		return imagecolorallocate( $this->img, $rgb['r'], $rgb['g'], $rgb['b'] );
	}


	public function hexToRgb( $hex ) {
		$hex = str_replace( "#", "", $hex );
		if ( strlen( $hex ) == 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$color['r'] = hexdec( substr( $hex, 0, 2 ) );
		$color['g'] = hexdec( substr( $hex, 2, 2 ) );
		$color['b'] = hexdec( substr( $hex, 4, 2 ) );
		return $color;
	}


	public function outputImage() {
		$this->header();
		$this->stream();
		$this->destroy();
		return $this;
	}


	private function stream( $filepath = null ) {
		$this->stream_success = @call_user_func( 'image' . $this->type, $this->img, $filepath, ( $this->type == 'png' ) ? 9 : 100 );
		return $this;
	}


	private function header() {
		header( "Content-type: image/$this->type" );
		return $this;
	}


	public function writeToFile( $path ) {
		if ( !is_string( $path ) ) {
			new ppIssue( '$path must be string in GD_Img::writeToFile()' );
			return $this;
		}

		$ext = strtolower( NrUtil::fileExt( $path ) );
		if ( $ext == 'jpg' ) {
			$ext = 'jpeg';
		}

		if ( $ext != strtolower( $this->type ) ) {
			new ppIssue( "File ext '$ext' doesn't match img type '$this->type' on path: '$path' in GD_Img::writeToFile()" );
			return $this;
		}

		$this->stream( $path );
		@chmod( $path, 0755 );
		$this->write_success = $this->stream_success;
		return $this;
	}


	public function fillBg( $bgHex = null ) {
		if ( $bgHex ) {
			$this->bg_color = $this->colorAllocate( $bgHex );
		}
		imagefilledrectangle( $this->img, 0, 0, $this->width, $this->height, $this->bg_color );
		return $this;
	}


	protected function makeTransparent( $color ) {
		imagecolortransparent( $this->img, $color );
		return $this;
	}


	protected function brushedLine( $x1, $y1, $x2, $y2 ) {
		// you must set a brush image before using this func
		imageline( $this->img, $x1, $y1, $x2, $y2, IMG_COLOR_BRUSHED );
		return $this;
	}

	public function halveSize() {

		$halved = new GD_Img( $this->width / 2, $this->height / 2, $this->type );
		$halved->create();

		// preserve transparency
		if ( $this->type == 'png' ) {
			$halved->bg_hex = $this->bg_hex;
			$halved->bg_color = $halved->colorAllocate( $this->bg_hex );
			$halved->makeTransparent( $halved->bg_color )->fillBg();
		}

		imagecopyresampled( $halved->img, $this->img, 0, 0, 0, 0, $halved->width, $halved->height, $this->width, $this->height );

		$this->destroy();
		return $halved;
	}


	/* variation of imagecopymerge() that supports alpha transparency in src img
	   from PHP.net comment at: http://www.php.net/manual/en/function.imagecopymerge.php#92787 */
	static function imagecopymergeAlpha( $dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct ){
		$opacity = 100 - $pct;

		// getting the watermark width
		$w = imagesx( $src_im );

		// getting the watermark height
		$h = imagesy( $src_im );

		// creating a cut resource
		$cut = imagecreatetruecolor( $src_w, $src_h );

		// copying that section of the background to the cut
		imagecopy( $cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h );

		// inverting the opacity
		$opacity = 100 - $opacity;

		// placing the watermark now
		imagecopy( $cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h );
		$copyMergeResult = imagecopymerge( $dst_im, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $opacity );
		imagedestroy( $cut );
		return $copyMergeResult;
	}
}


/* for compat with non-bundled GD library missing imagerotate() func */
if ( !function_exists( 'imagerotate' ) ) {
	function imagerotate( $srcImg, $angle, $bgcolor, $ignore_transparent = 0 ) {
		return _imagerotate( $srcImg, $angle );
	}
}

function _imagerotate( $img1, $rec ) {
	$wid = imagesx( $img1 );
	$hei = imagesy( $img1 );
	switch( $rec ) {
		case 270:
			$img2 = @imagecreatetruecolor( $hei, $wid );
			break;
		case 180:
			$img2 = @imagecreatetruecolor( $wid, $hei );
			break;
		default:
			$img2 = @imagecreatetruecolor( $hei, $wid );
	}
	if ( $img2 ) {
		for( $i = 0; $i < $wid; $i++ ) {
			for($j = 0;$j < $hei; $j++) {
				$ref = imagecolorat( $img1, $i, $j );
				switch($rec) {
					case 270:
						if ( !@imagesetpixel( $img2, ( $hei - 1 ) - $j, $i, $ref ) ) {
							return false;
						}
						break;
					case 180:
						if ( !@imagesetpixel( $img2, $i, ( $hei - 1 ) - $j, $ref ) ) {
							return false;
						}
						break;
					default:
						if ( !@imagesetpixel( $img2, $j, ( $wid - 1 ) - $i, $ref ) ) {
							return false;
						}
				}
			}
		}
		return $img2;
	}
	return false;
}

