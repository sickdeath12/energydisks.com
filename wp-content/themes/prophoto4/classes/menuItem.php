<?php

class ppMenuItem {


	public $children;
	public $childUlStyle;
	public $liAttr;
	protected $ID;
	protected $menuKey;
	protected $text;
	protected $url = '';
	protected $target = '_self';
	protected $type = 'manual';
	protected $anchor = 'text';
	protected $anchorMarkup;
	protected $textClass;
	protected $customClasses;
	protected $classes = array();
	protected $rel;
	protected $relNofollow = false;
	protected $titleAttr;
	protected $hasOwnChildren = false;
	protected $className;
	protected $data;
	protected static $splittingRight = false;
	protected static $context;


	public static function _instance( $menuClass, $ID, $itemData, $children ) {
		return new $menuClass( $ID, $itemData, $children );
	}


	public static function newUntitled( $id ) {
		return new ppMenuItem( $id, (object) array( 'text' => 'New link item' ), null );
	}


	public function id() {
		return $this->ID;
	}


	public function markup() {
		if ( empty( $this->text ) ) {
			return '';
		}
		return ppUtil::renderView( 'menu_item', array( 'item' => $this ), ppUtil::RETURN_VIEW );
	}


	public function render() {
		echo $this->markup();
	}


	public function renderMobile() {
		return ppUtil::renderView( 'mobile_menu_item', array( 'item' => $this ) );
	}


	public function renderAdminForm() {
		ppUtil::renderView( 'menu_admin_item_form', array( 'item' => $this ) );
	}


	public function url() {
		if ( $this->url == '' ) {
			return '#';
		} else if ( $this->url[0] == '#' ) {
			return $this->url;
		} else {
			return ppUtil::prefixUrl( $this->url );
		}
	}


	public function text() {
		return $this->text;
	}


	public function aTag() {
		if ( $this->isInWidgetMenu() && ( !$this->url() || $this->url() == '#' ) ) {
			return $this->anchorMarkup;
		}

		$attr = array(
			'class' => $this->classes(),
			'target' => $this->target,
		);
		if ( $this->rel ) {
			$attr['rel'] = $this->rel;
		} else if ( $this->relNofollow ) {
			$attr['rel'] = 'nofollow';
		}
		if ( $this->titleAttr ) {
			$attr['title'] = esc_attr( $this->titleAttr );
		}

		if ( pp::browser()->isMobile ) {
			$attr['data'] = 'role|button';
		}
		return NrHtml::a( $this->url(), $this->anchorMarkup, $attr );
	}


	public function anchor() {
		if ( isset( $this->data->anchor ) && $this->data->anchor != 'text' ) {
			$this->classes[] = 'sc';

			$imgID = ( $this->data->anchor == 'img' ) ? $this->ID : $this->ID . '_icon';
			$img = ppImg::id( $imgID );

			if ( $img->exists ) {
				$imgTag = $img->imgTag();
				$imgTag->alt( $this->text() );


				$fontsize   = ppBlogHeader::menuDim( $this->menuKey, 'fontsize' );
				$menuHeight = ppBlogHeader::menuDim( $this->menuKey, 'height' );

				if ( $this->data->anchor == 'img' ) {

					if ( $this->isInWidgetMenu() ) {
						return $imgTag->markup();
					}

					if ( $img->height >= $menuHeight ) {
						$imgTag = ppGdModify::constrainImgSize( $imgTag, 100000, $menuHeight );
						$this->childUlStyle = ' style="margin-top:0;"';
					}

					if ( $img->height < $menuHeight ) {
						$imgPadding = intval( ( $menuHeight - $img->height ) / 2 );
						$this->liAttr = " style=\"padding-top:{$imgPadding}px;padding-bottom:{$imgPadding}px;\"";
						$this->childUlStyle = ' style="margin-top:' . $imgPadding . 'px;"';
					}
					return $imgTag->markup();

				} else {
					$this->iconAlign = ( isset( $this->data->iconAlign ) && $this->data->iconAlign == 'right' ) ? 'right' : 'left';
					$this->classes[] = 'icon-align-' . $this->iconAlign;

					$textPaddingCss = '';
					if ( !$this->isInWidgetMenu() ) {
						$constraint = $this->iconConstrained() ? ppOpt::id( $this->menuKey . '_link_font_size' ) : $menuHeight;
						if ( $img->height > $constraint ) {
							$imgTag = ppGdModify::constrainImgSize( $imgTag, 100000, $constraint );
						}

						$liHeight = max( $imgTag->height(), ppOpt::id( $this->menuKey . '_link_font_size' ) );

						if ( $menuHeight > $liHeight ) {
							$liPadding = intval( ( $menuHeight - $liHeight ) / 2 );
							$this->childUlStyle = " style=\"margin-top:{$liPadding}px;\"";
							$this->liAttr .= " style=\"padding-top:{$liPadding}px;padding-bottom:{$liPadding}px;\"";
						}
						if ( $imgTag->height() > $fontsize ) {
							$textPadding = intval( ( $imgTag->height() - $fontsize ) / 2 );
							$textPaddingCss = "&style=padding-top:{$textPadding}px;padding-bottom:{$textPadding}px;";
						} else if ( $imgTag->height() < $fontsize ) {
							$imgPaddingCss = intval( ( $fontsize - $imgTag->height() ) / 2 );
							$imgTag->style( "padding-top:{$imgPaddingCss}px;padding-bottom:{$imgPaddingCss}px;" );
						}
					}

					$text = NrHtml::span( $this->text(), 'class=icon-text' . $textPaddingCss );
					return ( $this->iconAlign == 'left' ) ? $imgTag->markup() . $text : $text . $imgTag->markup();
				}

			} else {
				new ppIssue( 'Attempt to load non-existent menu anchor img' );
				return $this->text();
			}

		} else {
			return $this->text();
		}
	}


	public function classes() {
		return implode( ' ', $this->classes );
	}


	public function editFormClasses() {
		$classes = array( 'edit-menu-item-wrap', 'type-' . $this->type );
		if ( $this->type == 'special' ) {
			$classes[] = 'special-type-' . $this->specialType;
		}
		if ( $this->hasOwnChildren ) {
			$classes[] = 'has-own-children';
		}
		if ( $this->isInWidgetMenu() ) {
			$classes[] = 'context-widget';
		}
		if ( $this->isInMobileMenu() ) {
			$classes[] = 'context-mobile';
		}
		return implode( ' ', $classes );
	}


	public function isInWidgetMenu( $key = null ) {
		if ( null == $key ) {
			$key = $this->menuKey;
		}
		return NrUtil::startsWith( $key, 'widget_menu' );
	}


	public function isInMobileMenu() {
		return NrUtil::startsWith( $this->menuKey, 'mobile_' );
	}


	public function __get( $property ) {
		if ( isset( $this->{$property} ) ) {
			return $this->{$property};
		} else {
			return '';
		}
	}


	public function className() {
		return $this->className;
	}


	public function addClass( $class ) {
		$this->classes[] = $class;
	}


	protected function __construct( $ID, $itemData, $children ) {
		$this->ID = $ID;
		$this->data = $itemData;
		$this->menuKey = preg_replace( '/_item_[0-9]*$/', '', $this->ID );
		if ( self::$context && $this->menuKey != self::$context ) {
			self::$splittingRight = false;
		}
		self::$context = $this->menuKey;

		foreach ( array( 'text', 'url', 'target', 'type', 'anchor', 'customClasses', 'titleAttr' ) as $param ) {
			if ( isset( $this->data->{$param} ) ) {
				$this->{$param} = stripslashes( $this->data->{$param} );
			}
		}

		$this->textClass = 'text-' . $this->sanitizeAttr( $this->text );
		$this->classes[] = $this->textClass;
		$this->classes[] = 'mi-type-' . $this->type;
		$this->className = str_replace( 'ppMenuItem_', '', get_class( $this ) );
		if ( $this->className != 'ppMenuItem' ) {
			$this->classes[] = 'mi-' . strtolower( $this->className );
		}

		if ( $this->hasOwnChildren && is_array( $children ) ) {
			new ppIssue( "Cannot nest menu items inside type '{$this->className}'" );
			$children = null;
		}
		if ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) && method_exists( $this, 'mobileChildren' ) ) {
			$this->children = $this->mobileChildren( $children );
		} else {
			$this->children = $this->children( $children );
		}
		if ( $this->children ) {
			$this->classes[] = 'has-children';
		}


		if ( $this->anchor == 'img' && ppImg::id( $this->id() )->exists ) {
			$this->classes[] = 'mi-anchor-img';
		} else if ( $this->anchor == 'text_and_icon' && ppImg::id( $this->id() . '_icon' )->exists ) {
			$this->classes[] = 'mi-anchor-text_and_icon';
		} else {
			$this->classes[] = 'mi-anchor-text';
		}

		if ( $this->customClasses ) {
			$this->classes = array_merge( $this->classes, explode( ' ', $this->customClasses ) );
		}

		if ( self::$splittingRight ) {
			$this->classes[] = 'split-right';
		}
		if (
			 ( !ppOpt::test( 'headerlayout', 'pptclassic' ) || $this->menuKey == 'secondary_nav_menu' ) &&
			 ppOpt::test( $this->menuKey . '_align', 'split' ) &&
			 ppOpt::test( $this->menuKey . '_split_after_id', $this->id() )
			) {
			self::$splittingRight = true;
		}


		foreach ( ppMenuUtil::checkboxes() as $checkbox ) {
			if ( isset( $this->data->{$checkbox} ) && $this->data->{$checkbox} ) {
				$this->{$checkbox} = true;
			}
		}

		$this->anchorMarkup = $this->anchor();
	}


	public function iconConstrained() {
		return isset( $this->data->iconConstrained );
	}


	protected function children( $children ) {
		return $children;
	}


	protected function sanitizeAttr( $text ) {
		return strtolower( preg_replace( '/[^A-Za-z1-9_-]/', '', str_replace( ' ', '-', $text ) ) );
	}
}






