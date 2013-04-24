<?php

class ppLazyLoad {


	protected static $lazyLoadCount;
	protected static $blankSrc;
	protected static $incompatibleDevice;


	public static function filter( $text ) {
		if ( self::$incompatibleDevice || isset( $_GET['ajaxFetching'] ) ) {
			return $text;
		}

		preg_match_all( "/<img[^>]+(src=(?:\"|')([^\"']+)(?:\"|'))[^>]+>/i", $text, $matches  );

		if ( empty( $matches ) || !isset( $matches[2] ) ) {
			return $text;
		}

		$tags = $matches[0];
		$srcs = $matches[1];
		$urls = $matches[2];

		foreach ( $urls as $index => $url ) {
			if ( self::lazyLoadImg( $url ) && !NrUtil::isIn( 'psp-active', $tags[$index] ) ) {
				self::$lazyLoadCount++;
				if ( self::$lazyLoadCount > 3 ) {
					$text = str_replace( ' ' . $srcs[$index], ' src="' . self::$blankSrc . '" lazyload-src="' . $url . '"', $text );
				}
			}
		}

		return $text;
	}


	protected static function lazyLoadImg( $url ) {
		if ( $url === self::$blankSrc ) {
			return false;

		} else if ( NrUtil::isIn( '/smilies/', $url ) ) {
			return false;

		} else if ( $path = ppUtil::pathFromUrl( $url ) ) {
			if ( $imgData = NrUtil::imgData( $path ) ) {
				return ( $imgData->height + $imgData->width > 400 );
			} else {
				return true;
			}

		} else {
			return true;
		}

	}


	public static function _onClassLoad() {
		self::$lazyLoadCount = 0;
		self::$blankSrc = pp::site()->themeUrl . '/images/blank.gif';
		self::$incompatibleDevice = ( pp::browser()->isMobile || pp::browser()->isIPad );
	}

}

