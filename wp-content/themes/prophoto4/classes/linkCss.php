<?php

class ppLinkCss {


	protected $key;
	protected $color;
	protected $style;
	protected $size;
	protected $colorVisited;
	protected $colorHover;
	protected $family;
	protected $weight;
	protected $decoration;
	protected $decorationHover;
	protected $textTransform;
	protected $letterSpacing;
	protected $doNonLink;


	public function __construct( $key ) {
		$this->key = $key;
		$this->color           = ppOpt::color( "{$key}_font_color" );
		$this->colorVisited    = ppOpt::color( "{$key}_visited_font_color" );
		$this->colorHover      = ppOpt::color( "{$key}_hover_font_color" );
		$this->nonLinkColor    = ppOpt::color( str_replace( '_link', '', $key ) . '_font_color' );
		$this->style           = ppOpt::id( "{$key}_font_style" );
		$this->size            = ppOpt::id( "{$key}_font_size" );
		$this->family          = ppOpt::id( "{$key}_font_family" );
		$this->weight          = ppOpt::id( "{$key}_font_weight" );
		$this->decoration      = ppOpt::id( "{$key}_decoration" );
		$this->decorationHover = ppOpt::id( "{$key}_hover_decoration" );
		$this->textTransform   = ppOpt::id( "{$key}_text_transform" );
		$this->letterSpacing   = ppOpt::id( "{$key}_letterspacing" );
		$this->marginBottom    = ppOpt::id( "{$key}_margin_bottom" );
		$this->lineHeight      = ppOpt::id( "{$key}_line_height" );
	}


	public function rules( $selector ) {

		$a        = new ppCss( $this->key );
		$aLink    = new ppCss( $this->key );
		$aVisited = new ppCss( $this->key );
		$aHover   = new ppCss( $this->key );

		if ( $this->color ) {
			$a->addDec( 'color', $this->color );
			$aLink->addDec( 'color', $this->color );
			$aVisited->addDec( 'color', $this->color );
		}

		if ( $this->style ) {
			$a->addDec( 'font-style', $this->style );
		}

		if ( $this->size ) {
			$a->addDec( 'font-size', $this->size . 'px' );
		}

		if ( $this->colorVisited ) {
			$aVisited->addDec( 'color', $this->colorVisited );
		}

		if ( $this->colorHover ) {
			$aHover->addDec( 'color', $this->colorHover );
		}

		if ( $this->family ) {
			$a->addDec( 'font-family', $this->family );
		}

		if ( $this->weight ) {
			$a->addDec( 'font-weight', $this->weight );
		}

		if ( $this->decoration ) {
			$a->addDec( 'text-decoration', $this->decoration );
			$aLink->addDec( 'text-decoration', $this->decoration );
			$aVisited->addDec( 'text-decoration', $this->decoration );
		}

		if ( $this->decorationHover ) {
			$aHover->addDec( 'text-decoration', $this->decorationHover );
		}

		if ( $this->textTransform ) {
			$a->addDec( 'text-transform', $this->textTransform );
		}

		if ( $this->letterSpacing ) {
			$a->addDec( 'letter-spacing', $this->letterSpacing );
		}


		return
			$this->nonLinkRule( $selector ) .
			$a->rule( $this->buildSelector( $selector, 'a' ) ) .
			$aLink->rule( $this->buildSelector( $selector, 'a:link' ) ) .
			$aVisited->rule( $this->buildSelector( $selector, 'a:visited' ) ) .
			$aHover->rule( $this->buildSelector( $selector, 'a:hover' ) );
	}


	public function withNonLink() {
		$this->doNonLink = true;
		return $this;
	}


	protected function buildSelector( $selector, $subSelector ) {
		if ( !NrUtil::isIn( ',', $selector ) ) {
			return "$selector $subSelector";
		} else {
			$processedSelectors = array();
			foreach ( explode( ',', $selector ) as $selectorPart ) {
				$processedSelectors[] = trim( $selectorPart ) . ' ' . $subSelector;
			}
			return implode( ', ', $processedSelectors );
		}
	}


	protected function nonLinkRule( $selector ) {
		if ( !$this->doNonLink && !$this->marginBottom ) {
			return;
		}

		$nonLink = new ppCss( $this->key );

		if ( $this->nonLinkColor ) {
			$nonLink->addDec( 'color', $this->nonLinkColor );
		}

		if ( $this->family ) {
			$nonLink->addDec( 'font-family', $this->family );
		}

		if ( $this->size ) {
			$nonLink->addDec( 'font-size', $this->size . 'px' );
		}

		if ( $this->textTransform ) {
			$nonLink->addDec( 'text-transform', $this->textTransform );
		}

		if ( $this->weight ) {
			$nonLink->addDec( 'font-weight', $this->weight );
		}

		if ( $this->style ) {
			$nonLink->addDec( 'font-style', $this->style );
		}

		if ( $this->letterSpacing ) {
			$nonLink->addDec( 'letter-spacing', $this->letterSpacing . 'px' );
		}

		if ( $this->marginBottom ) {
			$nonLink->addDec( 'margin-bottom', $this->marginBottom . 'px' );
		}

		if ( $this->lineHeight ) {
			$nonLink->addDec( 'line-height', $this->lineHeight . 'em' );
		}

		return $nonLink->rule( $selector );
	}
}
