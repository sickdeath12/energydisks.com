<?php

class ppCss {


	private $key;
	private $decs = '';
	private $returnNull = false;


	public static function bgColorDec( $id ) {
		return self::_colorDec( $id, 'background-' );
	}

	public static function colorDec( $id ) {
		return self::_colorDec( $id );
	}

	protected static function _colorDec( $id, $prefix = '' ) {
		if ( $color = ppOpt::color( $id ) ) {
			$css = new ppCss( $id );
			$css->addDec( $prefix . 'color', $color );
			return $css->decs();
		} else {
			return '';
		}
	}


	public static function border( $key, $side = null ) {
		if ( NrUtil::endsWith( $key, '_border' ) ) {
			new ppIssue( "Bad key '$key' to ppCss::border" );
			$key = preg_replace( "/_border$/", '', $key );
		}

		if ( $side !== null ) {
			$side = "-$side";
		}

		$borderVal =
			ppOpt::orVal( "{$key}_border_style", 'solid' ) . ' ' .
			ppOpt::orVal( "{$key}_border_width", '0' ) . 'px ' .
			ppOpt::orVal( "{$key}_border_color", '#ffffff' );

		$borderCss = new ppCss( $key );
		$borderCss->addDec( 'border' . $side, $borderVal );
		return $borderCss;
	}


	public static function background( $key ) {
		$bgCss = new ppCss( $key );

		if ( $color = ppOpt::color( "{$key}_color" ) ) {
			$bgCss->addDec( 'background-color', $color );
		}

		$imgVal = ppImg::id( $key )->exists ? 'url(' . ppImg::id( $key )->url . ')' : 'none';
		$bgCss->addDec( 'background-image', $imgVal );

		$repeatVal = ppOpt::orVal( "{$key}_img_repeat", 'repeat' );
		$bgCss->addDec( 'background-repeat', $repeatVal );

		$positionVal = ppOpt::orVal( "{$key}_img_position", 'top left' );
		$bgCss->addDec( 'background-position', $positionVal );

		$attachmentVal = ppOpt::orVal( "{$key}_img_attachment", 'scroll' );
		$bgCss->addDec( 'background-attachment', $attachmentVal );

		return $bgCss;
	}


	public static function font( $key ) {
		$fontCss = new ppCss( $key );

		$colorKey = "{$key}_font_color";
		if ( NrUtil::isIn( '_link', $key ) ) {
			$nonLinkColorKey = str_replace( '_link', '', $key ) . '_font_color';
			if ( ppOpt::exists( $nonLinkColorKey ) ) {
				$colorKey = $nonLinkColorKey;
			}
		}

		if ( $color = ppOpt::color( $colorKey ) ) {
			$fontCss->addDec( 'color', $color );
		}

		if ( $size = ppOpt::id( "{$key}_font_size", 'int' ) ) {
			$fontCss->addDec( 'font-size', $size . 'px' );
		}

		if ( $style = ppOpt::id( "{$key}_font_style" ) ) {
			$fontCss->addDec( 'font-style', $style );
		}

		if ( $family = ppOpt::id( "{$key}_font_family" ) ) {
			$fontCss->addDec( 'font-family', $family );
		}

		if ( $transform = ppOpt::id( "{$key}_text_transform" ) ) {
			$fontCss->addDec( 'text-transform', $transform );
		}

		if ( $lineHeight = ppOpt::id( "{$key}_line_height" ) ) {
			$fontCss->addDec( 'line-height', $lineHeight . 'em' );
		}

		if ( $marginBottom = ppOpt::id( "{$key}_margin_bottom" ) ) {
			$fontCss->addDec( 'margin-bottom', $marginBottom . 'px' );
		}

		if ( $weight = ppOpt::id( "{$key}_font_weight" ) ) {
			$fontCss->addDec( 'font-weight', $weight );
		}

		if ( $letterSpacing = ppOpt::id( "{$key}_letterspacing" ) ) {
			$fontCss->addDec( 'letter-spacing', $letterSpacing );
		}

		return $fontCss;
	}


	public static function link( $key ) {
		return new ppLinkCss( $key );
	}


	public static function adminPreviewArea( $key, $selector = '', $extraCss = '' ) {
		$css = '';

		if ( !$selector ) {
			$selector = '#' . $key . '-font-preview';
		}

		if ( NrUtil::endsWith( $key, '_link' ) ) {
			$css .= self::link( $key )->rules( $selector );

		} else {
			$css .= self::font( $key )->rule( $selector );
		}

		if ( $extraCss ) {
			list( $subSelector, $rules ) = explode( '|', $extraCss );
			$css .= "$selector $subSelector { $rules }";
		}
		return $css;
	}


	public function decs() {
		return $this->returnNull ? null : $this->decs;
	}


	public function rule( $selectors ) {
		if ( !is_string( $selectors ) ) {
			new ppIssue( 'Non-string $selectors passed to ppCss::rule()' );
			return null;
		}
		return ( $this->returnNull || !$this->decs ) ? null : trim( $selectors ) . ' {' . $this->decs . '}' ;
	}


	public function onlyIf( $condition ) {
		if ( !$condition ) {
			$this->returnNull = true;
		}
		return $this;
	}


	public function addDec( $property, $value ) {
		$this->decs .= "$property:$value;";
	}

	public function __construct( $key ) {
		$this->key = $key;
	}
}
