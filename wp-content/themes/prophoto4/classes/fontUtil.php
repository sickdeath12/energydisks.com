<?php


class ppFontUtil {


	protected static $familyParams;
	protected static $websafeFamilyParams;


	public static function familyParams() {
		if ( self::$familyParams === null ) {

			$flatParamArray = array( 'select', '', 'select font...' );

			for ( $i = 1; $i <= pp::num()->maxCustomFonts; $i++ ) {

				if ( $fontData = ppOpt::id( 'custom_font_' . $i, 'array' ) ) {

					if ( self::hasAvailableFontFile( $fontData['name'], $fontData['slug'] ) ) {

						if ( !in_array( $fontData['name'], $flatParamArray ) ) {

							$fallback = ppOpt::id( 'custom_font_' . $i . '_fallback' );

							if ( $fallback ) {
								$flatParamArray[] = self::cssVal( $fontData['name'] ) . ', ' . $fallback;

							} else {
								$flatParamArray[] = self::cssVal( $fontData['name'] ) . ', ' . self::websafeFonts( 'Arial' );
							}

							$flatParamArray[] = $fontData['name'];
						}

					} else {
						ppOpt::delete( 'custom_font_' . $i );
						ppOpt::delete( 'custom_font_' . $i . '_fallback' );
					}
				}
			}

			self::$familyParams = join( '|', $flatParamArray ) . str_replace( 'select||select font...', '', self::websafeFamilyParams() );
		}

		return self::$familyParams;
	}


	public static function websafeFamilyParams() {
		if ( self::$websafeFamilyParams === null ) {
			$flatParamArray = array( 'select', '', 'select font...' );
			foreach ( self::websafeFonts() as $fontName => $fontCssVal ) {
				$flatParamArray[] = $fontCssVal;
				$flatParamArray[] = $fontName;
			}
			self::$websafeFamilyParams = join( '|', $flatParamArray );
		}
		return self::$websafeFamilyParams;
	}


	public static function websafeFonts( $index = null ) {
		$websafeFonts = array(
			'Arial'          => 'Arial, Helvetica, sans-serif',
			'Times'          => 'Times, Georgia, serif',
			'Verdana'        => 'Verdana, Tahoma, sans-serif',
			'Century Gothic' => '"Century Gothic", Helvetica, Arial, sans-serif',
			'Helvetica'      => 'Helvetica, Arial, sans-serif',
			'Georgia'        => 'Georgia, Times, serif',
			'Lucida Grande'  => '"Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana, sans-serif',
			'Palatino'       => 'Palatino, Georgia, serif',
			'Garamond'       => 'Garamond, Palatino, Georgia, serif',
			'Tahoma'         => 'Tahoma, Verdana, Helvetica, sans-serif',
			'Courier'        => 'Courier, monospace',
			'Trebuchet MS'   => '"Trebuchet MS", Tahoma, Helvetica, sans-serif',
			'Comic Sans MS'  => '"Comic Sans MS", Arial, sans-serif',
			'Bookman'        => 'Bookman, Palatino, Georgia, serif',
		);
		if ( $index ) {
			return $websafeFonts[$index];
		} else {
			return $websafeFonts;
		}
	}


	public static function fontFaceCss( $customFontID ) {
		if ( $fontData = ppOpt::id( $customFontID, 'array' ) ) {
			$testFilename = $fontData['slug'] . '-webfont.eot';
			if ( @file_exists( pp::fileInfo()->fontsFolderPath . '/' . $testFilename ) ) {
				$fontData['urlStart'] = pp::fileInfo()->fontsFolderUrl . '/';
			} else {
				$fontData['urlStart'] = 'http://prophoto.s3.amazonaws.com/img/';
			}
			return ppUtil::renderView( 'font_face_css', array( 'font' => (object) $fontData ), ppUtil::RETURN_VIEW );
		}
	}


	public static function hasAvailableFontFile( $name, $slug ) {
		if ( $name && $slug ) {
			$allExist = true;
			foreach ( ppFontUtil::fontFileExts() as $ext ) {
				if ( !@file_exists( pp::fileInfo()->fontsFolderPath . '/' . $slug . '-webfont.' . $ext ) ) {
					$allExist = false;
				}
			}
			if ( $allExist ) {
				return true;
			} else {
				return in_array( $slug . '-webfont.' . $ext, array_keys( ppRemoteFiles::allFileData() ) );
			}
			return true;
		}
		return false;
	}


	public static function fontFileExts() {
		return array( 'eot', 'svg', 'ttf', 'woff' );
	}


	public static function isFontFile( $file ) {
		return in_array( NrUtil::fileExt( $file ), self::fontFileExts() );
	}


	protected static function cssVal( $fontName ) {
		return NrUtil::isIn( ' ', trim( $fontName ) ) ? '"' . trim( $fontName ) . '"' : trim( $fontName );
	}


	public function flushCache() {
		self::$familyParams = null;
		self::$websafeFamilyParams = null;
	}

}


