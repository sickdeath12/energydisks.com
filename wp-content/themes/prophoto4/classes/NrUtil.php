<?php

class NrUtil {


	public static function arrayify( $in ) {
		if ( is_object( $in ) || is_array( $in ) ) {
			$out = (array) $in;
			foreach ( $out as $index => $subItem ) {
				$out[$index] = self::arrayify( $subItem );
			}
		} else {
			$out = $in;
		}
		return $out;
	}


	public static function constrainRectSide( $constraint, $constrainedDim, $oppSide, $round = true ) {
		if ( !is_numeric( $constraint ) || !is_numeric( $constrainedDim ) || !is_numeric( $constrainedDim ) ) {
			return $oppSide;
		}
		if ( $constrainedDim <= $constraint ) {
			return $oppSide;
		} else {
			return $constraint / ( $constrainedDim / $oppSide );
		}
	}


	public static function imgData( $path, $requestedData = false ) {
		if ( !is_string( $path ) || !$raw = @getimagesize( $path ) ) {
			return false;
		}

		$data = (object) array(
			'width' => $raw[0],
			'height' => $raw[1],
			'htmlAttr' => $raw[3],
			'mime' => $raw['mime'],
			'GDType' => end( split( '/', $raw['mime'] ) ),
			'channels' => isset( $raw['channels'] ) ? $raw['channels'] : '',
			'type' => $raw[2],
			'bits' => isset( $raw['bits'] ) ? $raw['bits'] : '',
		);

		if ( $requestedData && isset( $data->$requestedData ) ) {
			return $data->$requestedData;
		} else {
			return $data;
		}
	}


	public static function GET( $indexName, $value = null ) {
		return self::superGlobal( $_GET, $indexName, $value );
	}


	public static function POST( $indexName, $value = null ) {
		return self::superGlobal( $_POST, $indexName, $value );
	}


	public static function COOKIE( $indexName, $value = null ) {
		return self::superGlobal( $_COOKIE, $indexName, $value );
	}


	private static function superGlobal( $superGlobal, $indexName, $value = null ) {
		if ( !is_string( $indexName ) ) {
			trigger_error( 'NrUtil::superGlobal() requires string for $indexName', E_USER_WARNING );
			return false;
		}
		if ( !isset( $superGlobal[$indexName] ) ) {
			return false;
		}
		if ( $value === null ) {
			return true;
		} else {
			return ( $superGlobal[$indexName] == $value );
		}
	}


	public static function minifyCss( $css ) {
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
		return $css;
	}


	public static function isWebSafeImg( $file ) {
		return preg_match( '/(\.png|\.gif|\.jpg|\.jpeg)/i', substr( trim( $file ), -5 ) );
	}


	public static function fileExt( $file ) {
		if ( !is_string( $file ) ) {
			trigger_error( "NRUtil::fileExt() expects string", E_USER_WARNING );
			return '';
		}
		return pathinfo( $file, PATHINFO_EXTENSION );
	}


	public static function startsWith( $string, $start ) {
		if ( !is_string( $string ) && !is_int( $string ) && !is_float( $string ) ) {
			return false;
		}
		$start = strval( $start );
		$string = strval( $string );
		if ( $start == '' || $string == '' ) {
			return false;
		}
		$startLength = strlen( $start );
		return ( substr( $string, 0, $startLength ) === $start );
	}


	public static function endsWith( $string, $end ) {
		$end = strval( $end );
		$string = strval( $string );
		$stringLength = strlen( $string );
		$endLength = strlen( $end );
		return ( substr( $string, $stringLength - $endLength, $endLength ) === $end );
	}


	public static function recursiveRmDir( $dir ) {
		if ( is_dir( $dir ) ) {
			$objects = scandir( $dir );
			foreach ( $objects as $object ) {
				if ( $object != "." && $object != ".." ) {
					if ( filetype( $dir . "/" . $object ) == "dir" ) {
						NrUtil::recursiveRmDir( $dir . "/" . $object );
					} else {
						unlink( $dir . "/" . $object );
					}
				}
			}
			reset( $objects );
			return rmdir( $dir );
		}
	}


	public static function writeFile( $filepath, $content, $mode = 'w+' ) {
		if ( $mode == 'prepend' ) {
			$mode = 'w+';
			if ( file_exists( $filepath ) ) {
				if ( defined( 'IS_DEV' ) && IS_DEV ) {
					$filecontents = file_get_contents( $filepath );
				} else {
					$filecontents = @file_get_contents( $filepath );
				}
				$content = $content . $filecontents;
			}
		}
		if ( defined( 'IS_DEV' ) && IS_DEV ) {
			$resource = fopen( $filepath, $mode );
			if ( !$resource ) {
				NrDump::it( $filepath, '$filepath' );
			}
			$writeResult = fwrite( $resource, $content );
			chmod( $filepath, 0755 );
			fclose( $resource );
			return (bool) $writeResult;
		} else {
			$resource = @fopen( $filepath, $mode );
			$writeResult = @fwrite( $resource, $content );
			@chmod( $filepath, 0755 );
			@fclose( $resource );
			return (bool) $writeResult;
		}
	}


	public static function texturlsToLinks( $text, $target = '_self' ) {
		return preg_replace(
			'/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;\'">\:\s\<\>\)\]\!])/',
			'<a target="' . $target . '" href="$1">$1</a>',
			$text );
	}


	public static function isAssoc( $array ) {
		if ( !is_array( $array ) ) {
			return false;
		}
		return array_keys( $array ) !== range( 0, count( $array ) - 1 );
	}


	public static function firstArrayKey( $array ) {
		$array = (array) $array;
		$keys = array_keys( $array );
		return $keys[0];
	}


	public static function firstArrayVal( $array ) {
		return $array[self::firstArrayKey($array)];
	}


	public static function validUrl( $url ) {
		@extract( parse_url( strval( $url ) ) );
		if ( !isset( $scheme ) || !isset( $host ) ) {
			return false;
		}
		if ( $host == 'localhost' || preg_match( '/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/', $host ) ) {
			return true;
		}
		$hostParts = explode( '.', $host );
		$tldLen = strlen( $hostParts[count( $hostParts ) - 1] );
		if ( count( $hostParts ) < 2 || in_array( '', $hostParts ) || $tldLen < 2 || $tldLen > 6 ) {
			return false;
		}
		return true;
	}


	public static function invalidUrl( $url ) {
		return !self::validUrl( $url );
	}


	public static function validEmail( $email ) {
		if ( !is_string( $email ) ) {
			return false;
		}
		return preg_match( "/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})/i", $email );
	}


	public static function invalidEmail( $email ) {
		return !self::validEmail( $email );
	}


	public static function isIn( $needle, $haystack ) {
		if ( !is_string( $needle ) || !is_string( $haystack ) || empty( $needle ) ) {
			return false;
		}
		return ( strpos( $haystack, $needle ) !== false );
	}


	public static function nthStrPos( $needle, $haystack, $nth ) {
		for ( $c = 0; $c < $nth; $c++ ) {
			$start = ( $c === 0 ) ? 0 : $position + 1;
			$position = strpos( $haystack, $needle, $start );
		}
		return $position;
	}


	public static function extractDomain( $url ) {
		if ( !is_string( $url ) ) {
			return false;
		}

		$pattern = "/^(?:\w+:\/\/)?[^:?#\/\s]*?([^.\s]+\.(?:[a-z]{2,}|co\.uk|org\.uk|ac\.uk|net\.au|org\.au|com\.au|co\.za|co\.nz|com\.br|com\.ph|fot\.br))(?:[:?#\/]|$)/xi";
		preg_match( $pattern, $url, $matches );
		if ( !isset( $matches[1] ) ) {
			return false;
		}
		return strtolower( $matches[1] );
	}


	public static function mysqlInsert( $data, $table, $db = '', $append = null ) {
		$fields = implode( ', ', array_keys( $data ) );
		$values = "'" . implode( "', '", array_values( $data ) ) . "'";
		if ( $append ) $append = ' ' . $append;
		$sql = "INSERT INTO $table ( $fields ) VALUES ( $values )$append;";
		if ( $db ) {
			connect_to_db( $db );
			mysql_query( $sql ) or die( mysql_error() );
			return true;
		}
		return $sql;
	}


	public static function tinyTime() {
		return base_convert( time(), 10, 36 );
	}


	public static function filePermissions( $filepath ) {
		return ( @file_exists( $filepath ) ) ? substr( sprintf( '%o', @fileperms( $filepath ) ), -3 ) : 'notfound';
	}


	public static function getVarDump( $var ) {
		ob_start();
		var_dump( $var );
		$dump = ob_get_contents();
		ob_end_clean();
		return $dump;
	}


	public static function inArrayI( $needle, $haystack ) {
		return in_array( strtolower( $needle ), array_map( 'strtolower', $haystack ) );
	}


	public static function dump( $var, $varname = '' ) {
		/* DEPRECATED */
		echo '<p style="color:red;background:#fff;"><b>NrUtil::dump()</b> DEPRECATED use <b>NrDump::it()</b></p>';
		$bt = debug_backtrace();
		$dump = ( is_array( $var ) || is_object( $var ) ) ? print_r( $var, true ) : self::getVarDump( $var );
		echo "<pre style='background-color:lightgray;position:relative;padding:5px;margin:5px;font-size:12px;line-height:1.2em'>";
		echo "<p style='font-size:10.5px;position:absolute;top:1px;right:1px;margin:0'>Dumped in <b>";
		echo basename( $bt[0]['file'] ) . "</b> line <b>{$bt[0]['line']}</b></p>";
		echo "<strong style='text-decoration:underline'>$varname</strong>: ";
		echo urldecode( htmlspecialchars( $dump ) );
		echo '</pre><br />';
	}


	function inspect( $var ) {
		$var = urldecode( htmlspecialchars( print_r( $var, true ) ) );
		echo '<div class="debug-box" style="background-color:white;color:black;position:fixed;top:8px;right:8px;padding:4px;border:1px solid #444;opacity:0.8;z-index:5000000;max-height:95%;max-width:95%;overflow-x:hidden;overflow-y:auto"><h6 style="background-color:black;color:white;padding:0 5px;opacity:0.9;margin:0;font-size:10px;">NrUtil::inspect() report</h6><pre>';
		echo $var;
		echo '</pre></div>';
	}


	public static function arrayToTable( $array, $skip = array(), $callback = '' ) {
		$id = self::tinyTime() . rand( 100, 999 );
		echo <<<HTML
		<style type="text/css" media="screen">
			table#i{$id} {
				border-collapse:collapse;
				font-size:12.5px;
				font-family:Courier, monospace;
				margin:20px auto;
			}
			#i{$id} td {
				padding:2px 5px;
				border:1px solid #aaa;
			}
			#i{$id} tr:hover {
				background:#ddd;
			}
		</style>
HTML;
		echo "\n\n" . '<table cellspacing="0" id="i' . $id . '">' . "\n";
		foreach ( (array) $array as $row ) {
			$classes = '';
			foreach ( (array) $row as $key => $val ) {
				if ( in_array( $key, $skip ) || !is_string( $val) ) continue;
				$classes[] = 'tr-' . str_replace( array( ' ', '.', "'" ), '', strip_tags( $val ) );
			}
			$classes = implode( ' ', (array) $classes );
			echo "\t<tr class='$classes'>\n";
			foreach ( (array) $row as $key => $val ) {
				if ( in_array( $key, $skip ) || !is_string( $val) ) continue;
				$val = trim( $val );
				$classval = strip_tags( $val );
				if ( ( is_array( $callback ) && method_exists( $callback[0], $callback[1] ) ) || ( $callback && function_exists( $callback ) ) ) {
					list( $key, $val, $classval ) = call_user_func( $callback, $key, $val );
				}
				echo "\t\t<td class='td-$classval'>$val</td>\n";
				unset( $key, $val );
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n\n";
	}


	public static function isMobile() {
		$mobileUABits = array(
    		'iPhone', 'iPod', 'Android', 'CUPCAKE', 'incognito', 'dream',
    		'webmate', 'froyo', 'BlackBerry9500', 'BlackBerry9520', 'BlackBerry9530',
    		'BlackBerry9550', 'BlackBerry 9800', 'IEMobile/7.0', 'Googlebot-Mobile',
			's800', 'bada', 'webOS',
		);
		$isMobile = isset( $_GET['force_mobile'] );
		foreach ( $mobileUABits as $UABit ) {
    		if ( !$isMobile && !self::isIpad() && self::isIn( $UABit, $_SERVER['HTTP_USER_AGENT'] ) ) {
        		$isMobile = true;
    		}
		}
		return $isMobile;
	}





	public static function isIPad() {
		return ( self::isIn( 'iPad', $_SERVER['HTTP_USER_AGENT'] ) || isset( $_GET['spoof_ipad'] ) );
	}


	public static function isIPhone() {
		return ( self::isIn( 'iPhone', $_SERVER['HTTP_USER_AGENT'] ) || isset( $_GET['spoof_iphone'] ) );
	}


	public static function isIPod() {
		return ( self::isIn( 'iPod', $_SERVER['HTTP_USER_AGENT'] ) || isset( $_GET['spoof_ipod'] ) );
	}


	public static function isIOS() {
		return ( self::isIPad() || self::isIPhone() || self::isIPod() );
	}


	public static function browserInfo( $requested_var = null, $ua = null ) {
		if ( !$ua ) {
			if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$ua = $_SERVER['HTTP_USER_AGENT'];
			} else {
				return;
			}
		}

		$is_mac  = false;
		$is_pc   = false;
		$is_ipad = false;

		// INTERNET EXPLORER
		if ( NrUtil::isIn( 'MSIE', $ua ) ) {
			if ( NrUtil::isIn( 'MSIE 9.0', $ua ) ) {
				$ver = '9';
			} else if ( NrUtil::isIn( 'MSIE 8.0', $ua ) ) {
				$ver = '8';
			} else if ( NrUtil::isIn( 'MSIE 7.0', $ua ) ) {
				$ver = '7';
			} else if ( NrUtil::isIn( 'MSIE 6.0', $ua ) ) {
				$ver = '6';
			}
			$is_pc = true;
			$shortname = 'IE';
			$longname  = 'Internet Explorer';
			$made_by   = 'Microsoft';
			$engine    = 'Trident';


		// FIREFOX
		} else if ( NrUtil::isIn( 'Firefox', $ua ) ) {
			$shortname = $longname = 'Firefox';
			$made_by = 'Mozilla';
			$engine = 'Gecko';
			$ua_parts = explode( '/', $ua );
			$ver = preg_replace( '/ .+/', '', array_pop( $ua_parts ) );
			if ( NrUtil::isIn( 'Macintosh', $ua ) ) {
				preg_match( '/X [0-9\.]+/', $ua, $os_ver );
				$os_ver = preg_replace( '/[^0-9\.]/', '', $os_ver[0] );
				$is_mac = true;
			} else {
				$is_pc = true;
			}

		// CHROME
		} else if ( NrUtil::isIn( 'Chrome', $ua ) ) {
			$shortname = $longname = 'Chrome';
			$made_by = 'Google';
			$engine = 'Webkit';
			preg_match( '/Chrome\/[0-9\.]+/', $ua, $ver );
			$ver = preg_replace( '/[^0-9\.]/', '', $ver[0] );
			$ver = explode( '.', $ver );
			$ver = $ver[0] . '.' . $ver[1];
			if ( NrUtil::isIn( 'Macintosh', $ua ) ) {
				$is_mac = true;
				preg_match( '/[0-9]+_[0-9]+_[0-9]+/', $ua, $os_ver );
				$os_ver = str_replace( '_', '.', $os_ver[0] );
			} else {
				$is_pc = true;
			}

		// IPAD
		} else if ( NrUtil::isIn( 'iPad', $ua ) ) {
			$shortname = 'iPad';
			$longname = 'iPad mobile Safari';
			$made_by = 'Apple';
			$engine = 'Webkit';
			preg_match( '/iPhone OS [0-9_]+/', $ua, $ipad_ver );
			$os_ver = str_replace( array( 'iPhone OS ', '_' ), array( '', '.' ), $ipad_ver[0] );
			preg_match( '/Version\/[0-9\.]+/', $ua, $ver );
			$ver = preg_replace( '/[^0-9\.]/', '', $ver[0] );
			$ver = explode( '.', $ver );
			$ver = $ver[0] . '.' . $ver[1];
			$is_ipad = true;

		// IPHONE
		} else if ( NrUtil::isIn( 'iPhone', $ua ) ) {
			$shortname = 'iPhone';
			$longname = 'iPhone mobile Safari';
			$made_by = 'Apple';
			$engine = 'Webkit';
			preg_match( '/iPhone OS [0-9_]+/', $ua, $ipad_ver );
			$os_ver = str_replace( array( 'iPhone OS ', '_' ), array( '', '.' ), $ipad_ver[0] );
			preg_match( '/Version\/[0-9\.]+/', $ua, $ver );
			$ver = preg_replace( '/[^0-9\.]/', '', $ver[0] );
			$ver = explode( '.', $ver );
			$ver = $ver[0] . '.' . $ver[1];
			$is_iphone = true;

		// SAFARI
		} else if ( NrUtil::isIn( 'Safari', $ua ) ) {
			$shortname = $longname = 'Safari';
			$made_by = 'Apple';
			$engine = 'Webkit';
			preg_match( '/Version\/[0-9\.]+/', $ua, $ver );
			$ver = preg_replace( '/[^0-9\.]/', '', $ver[0] );
			$ver = explode( '.', $ver );
			$ver = $ver[0] . '.' . $ver[1];
			if ( NrUtil::isIn( 'Macintosh', $ua ) ) {
				preg_match( '/[0-9]+_[0-9]+_[0-9]+/', $ua, $os_ver );
				$os_ver = str_replace( '_', '.', $os_ver[0] );
				$is_mac = true;
			} else {
				$is_pc = true;
			}

		// UNKNOWN
		} else {
			return;
		}

		if ( $is_mac ) {
			$os_short = 'Mac';
			$os_long = 'Macintosh OSX ' . $os_ver;
		} else if ( $is_pc ) {
			$os_short = 'PC';
			$os_long = 'Windows';
		} else if ( $is_ipad ) {
			$os_short = '';
			$os_long = 'iPad ' . $os_ver;
		} else if ( $is_iphone ) {
			$os_short = '';
			$os_long = 'iPhone ' . $os_ver;
		}

		$short_string = "$shortname $ver";
		if ( isset( $os_short ) ) $short_string .= " - $os_short";
		if ( isset( $ver ) ) $version = " version $ver";
		$long_string  = "$made_by $longname{$version} ($engine) - Operating system: $os_long";

		$r = compact( 'shortname', 'longname', 'made_by', 'engine', 'ver', 'os_short', 'os_long', 'short_string', 'long_string', 'ua' );

		if ( $requested_var ) {
			return $r[$requested_var];
		}
		return $r;
	}


	public static function isImg( $file ) {
		if ( strpos( $_SERVER['HTTP_USER_AGENT'], '(prophototech)' ) !== FALSE ) {
			trigger_error( 'Deprecated: use NrUtil::isWebSafeImg() instead.', E_USER_WARNING );
		}
		return self::isWebSafeImg( $file );
	}
}


