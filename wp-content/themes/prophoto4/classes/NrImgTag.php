<?php

class NrImgTag {


	protected $tagInput;
	protected $src;
	protected $classes = array();
	protected $id;
	protected $alt = '';
	protected $title;
	protected $width;
	protected $height;
	protected $style;
	const MATCH_QUOTED = "(?:\"|')([^'\"]+)(?:\"|')";


	public static function createFromHtml( $tag, $childClass = null ) {
		if ( !is_string( $tag ) || !preg_match( '/^<img/i', $tag ) ) {
			self::error( "Invalid \$tag '$tag' passed to NrImgTag::createFromHtml()" );
		} else {
			$imgTag = $childClass ? new $childClass() : new NrImgTag();
			$imgTag->tagInput = $tag;
			$imgTag->parseTag();
			return $imgTag;
		}
	}


	public function __construct( $src = null, $args = null ) {
		if ( $src ) {
			if ( !is_string( $src ) ) {
				self::error( 'NrImgTag::__construct() requires string for $src param' );
			} else {
				$this->src = $src;
			}

			if ( $args ) {
				$argsArray = array();
				if ( is_string( $args ) ) {
					parse_str( $args, $argsArray );
				} else if ( is_array( $args ) ) {
					$argsArray = $args;
				} else {
					self::error( '$args must be string, array, or null in NrImgTag::__construct()' );
				}
				foreach ( array( 'id', 'width', 'height', 'alt', 'title', 'style' ) as $attr ) {
					if ( isset( $argsArray[$attr] ) ) {
						$this->{$attr} = $argsArray[$attr];
					}
				}
				if ( isset( $argsArray['class'] ) ) {
					if ( is_string( $argsArray['class'] ) ) {
						if ( NrUtil::isIn( ' ', $argsArray['class'] ) ) {
							$this->classes = explode( ' ', $argsArray['class'] );
						} else {
							$this->classes = array( $argsArray['class'] );
						}
					} else if ( is_array( $argsArray['class'] ) ) {
						$this->classes = $argsArray['class'];
					}
				}
			}
		}
	}


	public function id( $set = null ) {
		return $this->getOrSet( 'id', $set );
	}

	public function alt( $set = null ) {
		return $this->getOrSet( 'alt', $set );
	}

	public function title( $set = null ) {
		return $this->getOrSet( 'title', $set );
	}

	public function width( $set = null ) {
		return $this->getOrSet( 'width', $set );
	}

	public function height( $set = null ) {
		return $this->getOrSet( 'height', $set );
	}

	public function src( $set = null ) {
		return $this->getOrSet( 'src', $set );
	}

	public function style( $set = null ) {
		return $this->getOrSet( 'style', $set );
	}


	public function filename( $set = null ) {
		if ( is_string( $set ) ) {
			$this->src = str_replace( basename( $this->src ), $set, $this->src );
		}
		return basename( $this->src );
	}

	public function hasClass( $class ) {
		return in_array( $class, $this->classes );
	}

	public function addClass( $class ) {
		$this->classes[] = $class;
		return $this;
	}

	public function removeClass( $removeClass ) {
		foreach ( $this->classes as $key => $class ) {
			if ( $class == $removeClass ) {
				unset( $this->classes[$key] );
			}
		}
		return $this;
	}

	public function markup() {
		$markup = '<img src="' . $this->src . '" ';
		$markup .= $this->attrMarkup( 'id' );
		$markup .= $this->attrMarkup( 'class' );
		$markup .= $this->attrMarkup( 'width' );
		$markup .= $this->attrMarkup( 'height' );
		$markup .= $this->attrMarkup( 'alt' );
		$markup .= $this->attrMarkup( 'title' );
		$markup .= $this->attrMarkup( 'style' );
		$markup .= '/>';
		return $markup;
	}


	protected function attrMarkup( $attrName ) {
		if ( $attrName == 'class' && $this->classes ) {
			return 'class="' . implode( ' ', $this->classes ) . '" ';
		} else if ( isset( $this->{$attrName} ) && ( $this->{$attrName} || $attrName == 'alt' ) ) {
			return $attrName . '="' . $this->{$attrName} . '" ';
		} else {
			return '';
		}
	}

	protected function getOrSet( $attr, $set = null ) {
		if ( is_string( $set ) || is_numeric( $set ) ) {
			$this->{$attr} = $set;
			return $this;
		}
		return $this->{$attr};
	}



	protected function parseTag() {
		$this->src = $this->filterSrc( $this->attr( 'src' ) );
		if ( !$this->src ) {
			self::error( "No src attribute found in \$tag '$this->tagInput' passed to ppImgTag()" );
		} else {
			$this->classes = (array) $this->attr( 'class' );
			$this->id      = $this->attr( 'id' );
			$this->alt     = $this->attr( 'alt' );
			$this->title   = $this->attr( 'title' );
			$this->width   = $this->attr( 'width' );
			$this->height  = $this->attr( 'height' );
			$this->style   = $this->attr( 'style' );
		}
	}


	protected function filterSrc( $src ) {
		return $src;
	}


	protected function attr( $attrName ) {
		$match = array();
		$found = preg_match( "/{$attrName}=" . self::MATCH_QUOTED . '/i', $this->tagInput, $match );
		if ( $found && $match[1] ) {
			$attr = $match[1];
			if ( $attrName == 'class' ) {
				return explode( ' ', $attr );
			} else {
				return $attr;
			}
		}
	}


	protected static function error( $msg ) {
		if ( class_exists( 'ppIssue') ) {
			new ppIssue( $msg, 'tech' );
		} else {
			trigger_error( $msg, E_USER_WARNING );
		}
	}

}

