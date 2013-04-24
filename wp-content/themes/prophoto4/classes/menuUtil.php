<?php


class ppMenuUtil {

	public static function menuItem( $ID, $children = null ) {
		$itemData = self::loadItemData( $ID );
		if ( null == $itemData ) {
			return new ppMenuItem_Empty();
		}

		$children = is_array( $children ) ? $children : null;

		if ( self::isHidden( $itemData ) ) {
			return new ppMenuItem_Empty();
		}

		switch ( $itemData->type ) {
			case 'new':
				$menuClass = 'ppMenuItem_New';
				break;
			case 'container':
				$menuClass = 'ppMenuItem_Container';
				break;
			case 'internal':
				if ( !isset( $itemData->internalType ) ) {
					$itemData->internalType = 'page';
				}
				switch ( $itemData->internalType ) {
					case 'page':
						$menuClass = 'ppMenuItem_Page';
						break;
					case 'pages':
						$menuClass = 'ppMenuItem_Pages';
						break;
					case 'category':
						$menuClass = 'ppMenuItem_Category';
						break;
					case 'categories':
						$menuClass = 'ppMenuItem_Categories';
						break;
					case 'archives':
						$menuClass = 'ppMenuItem_Archives';
						break;
					case 'gallery':
						$menuClass = 'ppMenuItem_Gallery';
						break;
					case 'rss':
						$menuClass = 'ppMenuItem_Rss';
						break;
					case 'recent_posts':
						$menuClass = 'ppMenuItem_RecentPosts';
						break;
					case 'home':
						$menuClass = 'ppMenuItem_Home';
						break;
					default:
						new ppIssue( "Unknown menu item internalType '{$itemData->internalType}'" );
						$menuClass = 'ppMenuItem_Empty';
						break;
				}
				break;
			case 'special':
				if ( !isset( $itemData->specialType ) ) {
					$itemData->specialType = 'email';
				}
				switch ( $itemData->specialType ) {
					case 'email':
						$menuClass = 'ppMenuItem_Email';
						break;
					case 'twitter':
						$menuClass = 'ppMenuItem_Twitter';
						break;
					case 'inline_search':
					case 'dropdown_search':
						$menuClass = 'ppMenuItem_Search';
						break;
					case 'subscribe_by_email':
						$menuClass = 'ppMenuItem_SubscribeByEmail';
						break;
					case 'show_contact_form':
					case 'show_bio':
						$menuClass = 'ppMenuItem_ShowHidden';
						break;
					case 'show_custom_html':
						$menuClass = 'ppMenuItem_ShowCustomHtml';
						break;
					case 'call_telephone':
						$menuClass = 'ppMenuItem_CallTelephone';
						break;
					default:
						new ppIssue( "Unknown menu item specialType '{$itemData->specialType}'" );
						$menuClass = 'ppMenuItem_Empty';
						break;
				}
				break;
			case 'manual':
				$menuClass = 'ppMenuItem';
				break;
			default:
				new ppIssue( "Unknown menu item type '{$itemData->type}'" );
				$menuClass = 'ppMenuItem_Empty';
				break;
		}

		return ppMenuItem::_instance( $menuClass, $ID, $itemData, $children );
	}


	public static function update( $ID, $data ) {
		$itemData = self::loadItemData( $ID );
		if ( null == $itemData ) {
			return false;
		}

		$excludedPageIDs = array();
		foreach ( $data as $key => $val ) {
			if ( NrUtil::startsWith( $key, 'exclude_pageID_' ) && is_numeric( $val ) ) {
				unset( $data[$key] );
				$excludedPageIDs[] = $val;
			}
		}
		$data['excludedPageIDs'] = implode( ',', $excludedPageIDs );

		foreach ( self::checkboxes() as $checkboxVal ) {
			if ( !isset( $data[$checkboxVal] ) ) {
				unset( $itemData->{$checkboxVal} );
			}
		}

		if ( !isset( $data['iconConstrained'] ) ) {
			$itemData->iconNotConstrained = true;
		} else {
			unset( $itemData->iconNotConstrained );
		}

		foreach ( $data as $key => $val ) {
			$itemData->{$key} = $val;
		}

		if ( isset( $itemData->url ) ) {
			$itemData->url = strip_tags( $itemData->url );
		}

		if ( isset( $itemData->text ) ) {
			$itemData->text = str_replace( '"', '&quot;', stripslashes( $itemData->text ) );
		}

		ppOpt::update( $ID, json_encode( $itemData ) );
		return true;
	}


	public static function checkboxes() {
		return array(
			'relNofollow', 'hideOnSingle', 'hideOnPage', 'hideOnFront_page',
			'hideOnHome', 'hideOnCategory', 'hideOnArchive',
		);
	}


	public static function menuHasItems( $key ) {
		$items = self::_menuItems( $key );
		return @is_array( $items ) && !empty( $items );
	}


	public static function menuItems( $key ) {
		return (array) self::_menuItems( $key );
	}


	protected static function _menuItems( $key ) {
		return json_decode( ppOpt::id( $key . '_structure' ), true );
	}


	protected static function loadItemData( $ID ) {
		$rawItemData = ppOpt::menuData( $ID );

		if ( empty( $rawItemData ) ) {
			new ppIssue( "No data found for menu item '$ID'" );
			return null;
		}

		$itemData = json_decode( $rawItemData );

		if ( $itemData == null ) {
			new ppIssue( "Unable to decode data for menu item '$ID', raw was '$rawItemData'" );
			return null;
		}

		return $itemData;
	}


	protected static function isHidden( $itemData ) {
		if ( is_admin() ) {
			return false;
		} else {
			$hideOnPageType = 'hideOn' . ucFirst( ppUtil::pageType() );
			return ( isset( $itemData->{$hideOnPageType} ) && $itemData->{$hideOnPageType} );
		}
	}
}


class ppMenuItem_Internal extends ppMenuItem {


	protected $internalType;


	protected function __construct( $ID, $itemData, $children ) {
		$this->internalType = $itemData->internalType;
		parent::__construct( $ID, $itemData, $children );
	}
}



class ppMenuItem_Special extends ppMenuItem {


	protected $specialType;


	protected function __construct( $ID, $itemData, $children ) {
		$this->specialType = $itemData->specialType;
		parent::__construct( $ID, $itemData, $children );
	}
}



class ppMenuItem_New extends ppMenuItem {


	public function markup() {
		return parent::markup();
	}
}



class ppMenuItem_Empty extends ppMenuItem {


	public function __construct() {}


	public function markup() {
		return '';
	}
}


class ppMenuItem_ShowHidden extends ppMenuItem_Special {


	protected $className;


	public function url() {
		return '#' . $this->className;
	}


	protected function __construct( $ID, $itemData, $children ) {
		$this->className = 'show-hidden-' . str_replace( 'show_', '', $itemData->specialType );
		$this->classes[] = $this->className;
		parent::__construct( $ID, $itemData, $children );
	}
}
