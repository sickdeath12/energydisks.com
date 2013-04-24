<?php

class ppImportP3Menu {


	protected static $num = 0;
	protected static $options;
	protected static $images;
	protected static $globals;
	protected static $searchAlignedRight;
	protected static $searchAlignedRightID;
	protected static $subscribeByEmailAlignedRight;
	protected static $subscribeByEmailAlignedRightID;
	protected static $menuStructure = array();
	protected static $createdMenuItems = array();


	public static function importMenu( $design, $globals ) {
		self::$options = $design['options'];
		self::$images  = $design['images'];
		self::$globals = $globals;

		$p3order = self::order();
		foreach ( $p3order as $itemType ) {
			if ( NrUtil::startsWith( $itemType, 'customlink' ) ) {
				self::importCustomLink( "nav_$itemType" );
			} else {
				$method = 'import' . ucFirst( $itemType );
				if ( method_exists( 'ppImportP3Menu', $method ) ) {
					self::$method();
				} else {
					new ppIssue( "Unknown menu type for import: '$itemType'" );
				}
			}
		}

		// deal with right alignment
		$firstRight = null;
		if ( self::$subscribeByEmailAlignedRight ) {
			unset( self::$menuStructure[self::$subscribeByEmailAlignedRightID] );
			self::$menuStructure[self::$subscribeByEmailAlignedRightID] = self::$subscribeByEmailAlignedRightID;
			$firstRight = self::$subscribeByEmailAlignedRightID;
		}
		if ( self::$searchAlignedRight ) {
			unset( self::$menuStructure[self::$searchAlignedRightID] );
			self::$menuStructure[self::$searchAlignedRightID] = self::$searchAlignedRightID;
			if ( !$firstRight ) {
				$firstRight = self::$searchAlignedRightID;
			}
		}
		if ( $firstRight ) {
			$lastID = null;
			foreach ( self::$menuStructure as $ID => $ignore ) {
				if ( $ID == $firstRight ) {
					self::$options['primary_nav_menu_split_after_id'] = $lastID;
					if ( !isset( self::$options['nav_align'] ) || self::$options['nav_align'] == 'left' ) {
						self::$options['primary_nav_menu_align'] = 'split';
					}
				}
				$lastID = $ID;
			}
		}

		self::$options['primary_nav_menu_structure'] = json_encode( self::$menuStructure );
		return array(
			'options' => array_merge( self::$options, self::$createdMenuItems ),
			'images'  => self::$images
		);
	}


	protected static function importHome() {
		if ( self::optionIs( 'nav_home_link', 'on' ) ) {
			self::newLink( array(
				'type' => 'internal',
				'internalType' => 'home',
				'text' => self::option( 'nav_home_link_text', 'Home' ),
			) );
		}
	}


	protected static function importPortfolio() {
		if ( !self::optionIs( 'navportfolioonoff', 'off' ) ) {
			self::newLink( array(
				'type' => 'manual',
				'url' => self::option( 'navportfoliourl' ),
				'text' => self::option( 'navportfoliotitle', 'Portfolio' ),
				'target' => self::option( 'navportfoliotarget' ),
			) );
		}
	}


	protected static function importGalleries() {
		if ( !self::optionIs( 'nav_galleries_dropdown', 'off' ) ) {

			$gallery_posts = new WP_Query( array(
				'meta_key'       => 'p3_featured_gallery',
				'meta_value'     => 'true',
				'post_type'      => array( 'post', 'page' ),
				'posts_per_page' => 100
			) );
			wp_reset_query();

			$featuredP3galleries = array();
			foreach ( (array) $gallery_posts->posts as $wpPostObject ) {
				$post = new ppPost( $wpPostObject );
				if ( NrUtil::isIn( 'p3-flash-gallery-holder', $post->rawContent() ) ) {
					$featuredP3galleries[] = (object) ppImportP3::gallery( array( 'p3-flash-gallery-holder' ), $post );
				}
			}

			if ( $featuredP3galleries ) {
				self::$num++;
				$galleryContainerMenuID = 'primary_nav_menu_item_' . self::$num;
				self::$createdMenuItems[$galleryContainerMenuID] = json_encode( array(
					'type' => 'container',
					'text' => self::option( 'nav_galleries_dropdown_title', 'Galleries' ),
				) );
				$galleryMenuIDs = array();
				foreach ( $featuredP3galleries as $featuredP3gallery ) {
					self::$num++;
					$galleryMenuID = 'primary_nav_menu_item_' . self::$num;
					$galleryMenuIDs[$galleryMenuID] = $galleryMenuID;
					self::$createdMenuItems[$galleryMenuID] = json_encode( array(
						'type' => 'internal',
						'internalType' => 'gallery',
						'text' => $featuredP3gallery->title,
						'galleryID' => $featuredP3gallery->id,
						'galleryDisplay' => 'popup_slideshow',
					) );
				}
				self::$menuStructure[$galleryContainerMenuID] = $galleryMenuIDs;
			}
		}
	}


	protected static function importHiddenbio() {
		if ( !self::optionIs( 'bio_include', 'no' ) && ( self::optionIs( 'bio_pages_minimize', 'minimized' ) || self::optionIs( 'use_hidden_bio', 'yes' ) ) ) {
			self::newLink( array(
				'type' => 'special',
				'specialType' => 'show_bio',
				'text' => self::optionIs( 'use_hidden_bio', 'yes' ) ? self::option( 'hidden_bio_link_text', 'About Me' ) : self::option( 'bio_pages_minimize_text', 'About Me' ),
			) );
		}
	}


	protected static function importPages() {
		if ( self::optionIs( 'navpagesonoff', 'on' ) ) {
			self::newLink( array(
				'type' => 'internal',
				'internalType' => 'pages',
				'text' => self::option( 'navpagestitle', 'Pages' ),
				'excludedPageIDs' => trim( trim( self::option( 'nav_excluded_pages' ), ',' ) ),
			) );
		}
	}


	protected static function importRecentposts() {
		if ( self::optionIs( 'navrecentpostsonoff', 'on' ) ) {
			self::newLink( array(
				'type' => 'internal',
				'internalType' => 'recent_posts',
				'numRecentPosts' => self::option( 'navrecentpostslimit', '15' ),
				'text' => self::option( 'navrecentpoststitle', 'Recent Posts' ),
			) );
		}
	}


	protected static function importArchives() {
		if ( !self::optionIs( 'navarchivesonoff', 'off' ) ) {
			self::newLink( array(
				'type' => 'internal',
				'internalType' => 'archives',
				'text' => self::option( 'navarchivestitle', 'Archives' ),
				'archivesNestThreshold' => self::option( 'nav_archives_threshold', '12' ),
			) );
		}
	}


	protected static function importCategories() {
		if ( !self::optionIs( 'navcategoriesonoff', 'off' ) ) {
			self::newLink( array(
				'type' => 'internal',
				'internalType' => 'categories',
				'text' => self::option( 'navcategoriestitle', 'Categories' ),
			) );
		}
	}


	protected static function importBlogroll() {
		if ( !self::optionIs( 'navblogrollonoff', 'off' ) ) {

			$categories = get_terms( 'link_category' );
			foreach ( $categories as $category ) {

				self::$num++;
				$categoryContainerMenuID = 'primary_nav_menu_item_' . self::$num;
				self::$createdMenuItems[$categoryContainerMenuID] = json_encode( array(
					'type' => 'container',
					'text' => $category->name,
				) );

				$links = get_bookmarks( array( 'category' => $category->term_id ) );
				$linkIDs = array();
				foreach ( $links as $link ) {
					self::$num++;
					$linkMenuID = 'primary_nav_menu_item_' . self::$num;
					$linkIDs[$linkMenuID] = $linkMenuID;
					self::$createdMenuItems[$linkMenuID] = json_encode( array(
						'type' => 'manual',
						'text' => $link->link_name,
						'url' => $link->link_url,
					) );
				}
				self::$menuStructure[$categoryContainerMenuID] = $linkIDs;
			}
		}
	}


	protected static function importCustomLink( $customLinkID ) {
		if ( trim( self::option( $customLinkID . '_url' ) ) != '' ) {

			$linkData = array(
				'type'   => 'manual',
				'url'    => trim( self::option( $customLinkID . '_url' ) ),
				'text'   => trim( self::option( $customLinkID . '_title' ) ),
				'target' => self::option( $customLinkID . '_target' ),
				'iconConstrained' => true,
			);

			if ( isset( self::$images[$customLinkID.'_icon'] ) && !empty( self::$images[$customLinkID.'_icon'] ) ) {
				if ( trim( self::option( $customLinkID . '_title' ) ) == '' ) {
					$linkData['anchor'] = 'img';
					$linkData['text'] = 'Custom Icon Link';
				} else {
					$linkData['anchor'] = 'text_and_icon';
				}
			} else {
				$linkData['anchor'] = 'text';
			}

			$linkID = self::newLink( $linkData );

			if ( $linkData['anchor'] == 'text_and_icon' ) {
				self::$images[$linkID.'_icon'] = self::$images[$customLinkID.'_icon'];
				unset( self::$images[$customLinkID.'_icon'] );
			} else if ( $linkData['anchor'] == 'img' ) {
				self::$images[$linkID] = self::$images[$customLinkID.'_icon'];
				unset( self::$images[$customLinkID.'_icon'] );
			}
		}
	}


	protected static function importTwitter() {
		if ( self::optionIs( 'twitter_onoff', 'on' ) && self::option( 'twitter_name' ) ) {
			self::newLink( array(
				'type' => 'special',
				'specialType' => 'twitter',
				'text' => self::option( 'twitter_title', 'Twitter' ),
				'twitterID' => self::option( 'twitter_name' ),
				'numTweets' => self::option( 'twitter_count', '5' ),
			) );
		}
	}


	protected static function importEmaillink() {
		if ( self::optionIs( 'nav_emaillink_onoff', 'on' ) ) {
			self::newLink( array(
				'type' => 'special',
				'specialType' => 'email',
				'text' => self::option( 'nav_emaillink_text', 'Email' ),
				'email' => self::option( 'nav_emaillink_address', get_option( 'admin_email' ) ),
			) );
		}
	}


	protected static function importContact() {
		if ( !isset( self::$globals['contactform_yesno'] ) || self::$globals['contactform_yesno'] != 'no' ) {
			$contactText = isset( self::$globals['contactform_link_text'] ) ?self::$globals['contactform_link_text'] : 'Contact';
			self::newLink( array(
				'type' => 'special',
				'specialType' => 'show_contact_form',
				'text' => $contactText,
			) );
		}
	}


	protected static function importSubscribebyemail() {
		if ( self::optionIs( 'subscribebyemail_nav', 'on' ) ) {
			$linkID = self::newLink( array(
				'type' => 'special',
				'specialType' => 'subscribe_by_email',
				'text' => self::option( 'subscribebyemail_nav_submit', 'Subscribe by email' ),
				'subscribeByEmailBtnText' => self::option( 'subscribebyemail_nav_submit', 'Subscribe by email' ),
				'subscribeByEmailPrefill' => self::option( 'subscribebyemail_nav_textinput_value', 'enter email' ),
			) );
			if ( !isset( self::$options['subscribebyemail_nav_leftright'] ) || self::optionIs( 'subscribebyemail_nav_leftright', 'right' ) ) {
				self::$subscribeByEmailAlignedRight = true;
				self::$subscribeByEmailAlignedRightID = $linkID;
			}
		}
	}


	protected static function importSearch() {
		if ( !self::optionIs( 'nav_search_onoff', 'off' ) ) {

			$linkData = array(
				'type' => 'special',
				'searchBtnText' => self::option( 'nav_search_btn_text', 'search' ),
			);

			if ( self::optionIs( 'nav_search_dropdown', 'off' ) ) {
				$linkData['text'] = self::option( 'nav_search_btn_text', 'Search' );
				$linkData['specialType'] = 'inline_search';

			} else {
				$linkData['text'] = self::option( 'nav_search_dropdown_linktext', 'Search' );
				$linkData['specialType'] = 'dropdown_search';
			}

			$linkID = self::newLink( $linkData );

			if ( self::optionIs( 'nav_search_align', 'right' ) ) {
				self::$searchAlignedRight = true;
				self::$searchAlignedRightID = $linkID;
			}
		}
	}


	protected static function importRss() {
		if ( !self::optionIs( 'nav_rss', 'off' ) ) {
			$linkData = array(
				'type' => 'internal',
				'internalType' => 'rss',
				'text' => self::option( 'nav_rsslink_text', 'Subscribe' ),
			);

			$doIcon = ( self::optionIs( 'nav_rss_use_icon', 'yes' ) || !isset( self::$options['nav_rss_use_icon'] ) );
			$doText = ( self::optionIs( 'nav_rss_use_linktext', 'yes' ) || !isset( self::$options['nav_rss_use_linktext'] ) );

			if ( $doIcon && $doText ) {
				$linkData['anchor'] = 'text_and_icon';
				$linkData['iconAlign'] = 'left';

			} else if ( $doText ) {
				$linkData['anchor'] = 'text';

			} else if ( $doIcon ) {
				$linkData['anchor'] = 'img';
				$linkData['text'] = 'RSS Icon';

			} else {
				return;
			}

			$linkID = self::newLink( $linkData );

			if ( $linkData['anchor'] == 'text_and_icon' ) {
				self::$images[$linkID.'_icon'] = 'rss-icon.png';
			} else if ( $linkData['anchor'] == 'img' ) {
				self::$images[$linkID] = 'rss-icon.png';
			}
		}
	}


	protected static function order() {
		$defaultOrder = array();
		$defaultOrder[] = 'home';
		$defaultOrder[] = 'portfolio';
		$defaultOrder[] = 'galleries';
		$defaultOrder[] = 'hiddenbio';
		$defaultOrder[] = 'pages';
		$defaultOrder[] = 'recentposts';
		$defaultOrder[] = 'archives';
		$defaultOrder[] = 'categories';
		$defaultOrder[] = 'blogroll';
		for ( $i = 1; $i <= 10; $i++ ) {
			$defaultOrder[] = 'customlink' . $i;
		}
		$defaultOrder[] = 'twitter';
		$defaultOrder[] = 'emaillink';
		$defaultOrder[] = 'contact';
		$defaultOrder[] = 'subscribebyemail';
		$defaultOrder[] = 'search';
		$defaultOrder[] = 'rss';

		if ( isset( self::$options['menuorder'] ) && !empty( self::$options['menuorder'] ) ) {
			$customOrder = split( ';', self::$options['menuorder'] );
			foreach ( (array) $customOrder as $key => $val ) {
				if ( !$val ) {
					unset( $customOrder[$key] );
				}
			}
		} else {
			$customOrder = null;
		}

		if ( $customOrder ) {
			foreach ( $defaultOrder as $menuItem ) {
				if ( !in_array( $menuItem, $customOrder ) ) {
					$customOrder[] = $menuItem;
				}
			}
			return $customOrder;
		} else {
			return $defaultOrder;
		}
	}


	protected static function optionIs( $optionID, $testVal ) {
		if ( !isset( self::$options[$optionID] ) ) {
			return false;
		} else {
			return ( self::$options[$optionID] == $testVal );
		}
	}


	protected static function option( $optionID, $ifNotSet = '' ) {
		if ( !isset( self::$options[$optionID] ) ) {
			return $ifNotSet;
		} else {
			return self::$options[$optionID];
		}
	}


	protected static function newLink( $data ) {
		self::$num++;
		$ID = 'primary_nav_menu_item_' . self::$num;
		self::$createdMenuItems[$ID] = json_encode( $data );
		self::$menuStructure[$ID] = $ID;
		return $ID;
	}


	public static function reset() {
		self::$num = 0;
		self::$options = null;
		self::$menuStructure = array();
		self::$createdMenuItems = array();
		self::$searchAlignedRight = null;
		self::$searchAlignedRightID = null;
		self::$subscribeByEmailAlignedRight = null;
		self::$subscribeByEmailAlignedRightID = null;
	}


	public static function eliminatedOptions() {
		$eliminated =  array(
			'nav_home_link_text',
			'nav_home_link',
			'nav_galleries_dropdown',
			'nav_galleries_dropdown_title',
			'navportfolioonoff',
			'navportfoliourl',
			'navportfoliotitle',
			'navportfoliotarget',
			'hidden_bio_link_text',
			'bio_pages_minimize_text',
			'navrecentpostsonoff',
			'navrecentpostslimit',
			'navrecentpoststitle',
			'nav_excluded_pages',
			'navpagestitle',
			'navpagesonoff',
			'nav_archives_threshold',
			'navarchivestitle',
			'navarchivesonoff',
			'navcategoriestitle',
			'navcategoriesonoff',
			'navblogrollonoff',
			'twitter_onoff',
			'twitter_title',
			'twitter_count',
			'nav_emaillink_onoff',
			'nav_emaillink_text',
			'nav_emaillink_address',
			'contactform_link_text',
			'subscribebyemail_nav',
			'subscribebyemail_nav_submit',
			'subscribebyemail_nav_textinput_value',
			'nav_rss',
			'nav_rss_use_icon',
			'nav_rss_use_linktext',
			'nav_rsslink_text',
			'nav_search_onoff',
			'nav_search_dropdown_linktext',
			'nav_search_dropdown',
			'nav_search_btn_text',
			'nav_search_align',
		);
		for ( $i = 1; $i <= 10; $i++ ) {
			$eliminated[] = "nav_customlink{$i}_url";
			$eliminated[] = "nav_customlink{$i}_title";
			$eliminated[] = "nav_customlink{$i}_target";
		}
		return $eliminated;
	}

}