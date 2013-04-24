<?php

class ppPathfixer {


	private static $urls;
	private static $replaceURLs;


	public static function fix( $text ) {
		if ( null == self::$replaceURLs ) {
			self::$replaceURLs = self::$urls;
			foreach ( self::$urls as $index => $url ) {
				if ( NrUtil::startsWith( $url, 'http://www.' ) ) {
					self::$replaceURLs[] = str_replace( 'http://www.', 'http://', $url );
				} else {
					self::$replaceURLs[] = str_replace( 'http://', 'http://www.', $url );
				}
			}
			self::$replaceURLs = array_filter( self::$replaceURLs, create_function( '$url', 'return ( $url != pp::site()->wpurl );' ) );
			self::$replaceURLs = array_map( create_function( '$url', 'return $url . "/" . pp::fileInfo()->wpUploadRelPath;' ), self::$replaceURLs );
		}
		return str_replace( self::$replaceURLs, pp::fileInfo()->wpUploadUrl, $text );
	}


	public static function registerUrls( $registeredUrls ) {
		$newUrlRegistered = false;

		foreach ( (array) $registeredUrls as $registeredUrl ) {
			$registeredUrl = untrailingslashit( ppUtil::prefixUrl( $registeredUrl ) );
			if ( NrUtil::validUrl( $registeredUrl ) && !in_array( $registeredUrl, self::$urls ) ) {
				self::$urls[] = $registeredUrl;
				$newUrlRegistered = true;
			}
		}

		if ( $newUrlRegistered ) {
			self::storeUrls();
		}
	}


	private static function storeUrls() {
		ppOpt::update( 'pathfixer_old_urls', implode( '*', self::$urls ) );
	}


	public static function _onClassLoad() {
		self::$replaceURLs = null;

		// workaround this class on initial import procedure
		if ( class_exists( 'ppImportP3Menu', false ) ) {
			self::$urls = array();
			return;
		}

		// read urls from storage
		if ( $storedUrls = ppOpt::id( 'pathfixer_old_urls' ) ) {
			if ( NrUtil::isIn( '*', $storedUrls ) ) {
				self::$urls = explode( '*', $storedUrls );
			} else {
				self::$urls = array( $storedUrls );
			}
		} else {
			self::$urls = array();
		}

		// make sure current wpurl has been registered
		if ( !in_array( pp::site()->wpurl, self::$urls ) ) {
			self::registerUrls( pp::site()->wpurl );
		}
	}
}


