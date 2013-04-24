<?php

class ppBio {


	private static $minimized = false;
	private static $markupRequired;


	public static function render() {
		if ( self::$markupRequired ) {
			ppUtil::renderView( 'bio' );
		}
	}


	public static function picMarkup() {
		if ( ppOpt::test( 'biopic_display', 'off' ) || !ppImg::id( 'biopic1' )->exists ) {
			return;
		}

		$biopicImgTag = ppImg::id( 'biopic1' )->imgTag()
			->id( 'biopic' )
			->addClass( 'bio-col' )
			->alt( pp::site()->name . ' bio picture' );

		if ( ppBio::randomizePics() ) {
			return '<script>ppRandomizeBiopic(jQuery);</script><noscript>' . $biopicImgTag->markup() . '</noscript>';
		} else {
			return $biopicImgTag->markup();
		}
	}


	public static function randomizePics() {
		if ( !ppOpt::test( 'biopic_display', 'random' ) ) {
			return false;
		}

		for ( $i = 2; $i <= pp::num()->maxBioImages; $i++ ) {
			if ( ppImg::id( 'biopic' . $i )->exists ) {
				return true;
			}
		}
		return false;
	}


	public static function minimized() {
		return self::$minimized;
	}


	public static function mightBeMinimized() {
		return ( ppOpt::test( 'bio_include', 'yes' ) && ( ppOpt::test( 'use_hidden_bio', 'yes' ) || ppOpt::test( 'bio_pages_minimize', 'minimized' ) ) );
	}


	public static function _onClassLoad() {
		if ( ppOpt::test( 'bio_include', 'no' ) ) {
			self::$markupRequired = false;
			self::$minimized = false;

		} else if ( ppOpt::test( 'use_hidden_bio', 'yes' ) ) {
			self::$markupRequired = true;
			self::$minimized = true;

		} else if ( ppOpt::test( 'bio_' . ppUtil::pageType(), 'on' ) ) {
			self::$markupRequired = true;
			self::$minimized = false;

		} else if ( ppOpt::test( 'bio_pages_minimize', 'minimized' ) ) {
			self::$markupRequired = true;
			if ( !ppOpt::test( 'bio_' . ppUtil::pageType(), 'on' ) ) {
				self::$minimized = true;
			}

		} else {
			self::$markupRequired = false;
			self::$minimized = false;
		}
	}
}
