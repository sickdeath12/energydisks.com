<?php


class ppHtml {


	public static function statcounterAnalyticsCode() {
		return self::analyticsCode( 'statcounter' );
	}


	public static function googleAnalyticsCode() {
		return self::analyticsCode( 'google' );
	}


	private static function analyticsCode( $analyticsType ) {
		if ( !ppOpt::test( "{$analyticsType}_analytics_code" ) ) {
			return;

		} else if ( !current_user_can( 'level_1' ) ) {
			return self::updateLegacyAnalyticsCode( ppOpt::id( "{$analyticsType}_analytics_code" ), $analyticsType );

		} else {
			return "<!-- " . strtoupper( $analyticsType ) . " ANALYTICS code not inserted when you are logged in as administrator -->\n";
		}
	}


	private static function updateLegacyAnalyticsCode( $code, $type ) {
		if ( $type == 'google' && NrUtil::isIn( 'document.write(unescape', $code ) ) {
			preg_match( "/<script(?:.)*_getTracker\(\"([A-Z0-9-]*)(?:.)*<\/script>/s", $code, $match );
			if ( isset( $match[1] ) ) {
				$newCode = "<script type=\"text/javascript\">\n\n  var _gaq = _gaq || [];\n  _gaq.push(['_setAccount', '" . $match[1] . "']);\n  _gaq.push(['_trackPageview']);\n\n  (function() {\n    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n  })();\n\n</script>";
				ppOpt::update( 'google_analytics_code', str_replace( $match[0], $newCode, $code ) );
				$code = ppOpt::id( 'google_analytics_code' );
			}
		}
		return $code;
	}


	public static function adBannerMarkup() {
		if ( ppOpt::test( 'show_ad_banners', 'false') ) {
			return '';
		}

		$bannersMarkup = '';
		for ( $i = 1; $i <= pp::num()->maxAdBanners; $i++ ) {
			$banner = ppImg::id( "banner{$i}" );
			if ( $banner->exists ) {
				$bannersMarkup .= NrHtml::a( $banner->linkurl, NrHtml::img( $banner->url ), 'target=_blank' );
			}
		}

		if ( !$bannersMarkup ) {
			return '';
		}

		return NrHtml::p( $bannersMarkup, 'id=ad-banners&class=content-bg sc' );
	}


	public static function renderPostFooterMeta() {
		if ( is_page() || is_404() || is_search() ) {
			return;
		}

		$categoryList = ppOpt::test( 'categories_in_post_footer', 'yes' ) ? self::categoryList() : null;
		$tagList      = ppOpt::test( 'tags_in_post_footer',       'yes' ) ? self::tagList()      : null;

		if ( $categoryList || $tagList ) {
			echo  NrHtml::div( $categoryList . $tagList, 'class=article-meta article-meta-bottom' );
		}

		if ( is_single() ) {
			ppUtil::renderView( 'article_prev_next_links' );
		}
	}


	public static function postClasses( ppPost $post ) {
		$addedClasses = array( 'sc' );

		if ( $post->password() ) {
			$addedclasses[] = 'has-password';
			$addedClasses[] = $post->passwordRequired() ? 'protected' : 'permitted';
		}

		if ( ppHelper::isBeforeWatermarkStartDate( $post ) ) {
			$addedClasses[] = 'no-watermark';
		}

		return implode( ' ', get_post_class( $addedClasses ) );
	}


	public static function tagList() {
		if ( !has_tag() ) {
			return;
		}

		$tagList = get_the_tag_list(
			'<span class="tag-links article-meta-item">' . ppOpt::id( 'tag_list_prepend' ) . ' ',
			ppOpt::id( 'tag_list_divider' ),
			'</span>'
		 );

		if ( is_home() ) {
			return ppOpt::test( 'tags_on_home', 'yes' ) ? $tagList : null;

		} elseif ( is_single() ) {
			return ppOpt::test( 'tags_on_single', 'yes' ) ? $tagList : null;

		} elseif ( is_tag() ) {
			return ppOpt::test( 'tags_on_tags', 'yes' ) ? $tagList : null;

		} elseif ( is_archive() ) {
			return ppOpt::test( 'tags_on_archive', 'yes' ) ? $tagList : null;

		} else {
			return;
		}
	}


	public static function categoryList() {
		return
			'<span class="article-category-list article-meta-item">' .
				ppOpt::id( 'category_list_prepend' ) . get_the_category_list( ppOpt::id( 'category_list_divider' ) ) .
			'</span>';
	}


	public static function maintenanceMode() {
		if ( NrUtil::GET( 'open', 'sesame' ) || isset( $_COOKIE['pptech'] ) ) {
			return;
		}

		if ( !current_user_can( 'switch_themes' ) ) {
			ppUtil::renderView( "maintenance_mode" );
			if ( !ppUtil::unitTesting() ) {
				exit();
			}
		} else {
			$func = ppUtil::func( 'echo "<div id=\"maintenance-mode-remind\">Under Construction mode is ON</div>\n";' );
			add_action( 'pp_begin_body', $func );
			add_action( 'pp_mobile_begin_body', $func );
		}
	}


	public static function wpHead() {
		if ( pp::browser()->isIPad ) {
			add_action( 'wp_head', ppUtil::func( 'echo NrHtml::scriptSrc( pp::site()->themeUrl . "/dynamic/js/swipe.js" );' ) );
		}
		if ( !class_exists( 'KimiliFlashEmbed' ) && !class_exists( 'Platinum_SEO_Pack' ) ) {
			$unformatted = ppUtil::ob( 'do_action', 'wp_head' );
			$formatted   = "\n\n\t<!-- wp_head() elements -->\n\t";
			$formatted  .= str_replace( array( "\n", "\n\t\t", "type='text/javascript' " ), array( "\n\t", "\n\t", '' ), $unformatted );
			$formatted  .= "\n\t<!-- END wp_head() elements -->\n";
			return $formatted;
		} else {
			wp_head();
			return null;
		}
	}


	public static function insertIntoHead() {
		$userInserted = trim( str_replace( "\n", "\n\t", stripslashes( ppOpt::id( 'insert_into_head' ) ) ) );
		if ( empty( $userInserted ) ) {
			return '';
		}
		$insert  = "\n\n\t<!-- ProPhoto user-inserted head element -->\n\t";
		$insert .= $userInserted;
		$insert .= "\n\t<!-- END ProPhoto user-inserted head element -->";
		return $insert;
	}


	public static function obfuscatedEmailLink( $aTag ) {
		preg_match( '/href=' . MATCH_QUOTED . '[^>]*>(?:\s)*?(.*)(?:\s)*?<\/a/', $aTag, $match );
		if ( isset( $match[1] ) && isset( $match[2] ) ) {
			$href = $match[1];
			$text = trim( $match[2] );
			$aTag = str_replace( $href, self::toJsOrd( $href ), $aTag );
			if ( NrUtil::startsWith( $text, '<img' ) ) {
				$aTag = str_replace( $text, '<span class="jsobf img">' . $text . '</span>', $aTag );
			} else {
				$aTag = preg_replace( "/>(?:\s)*?" . preg_quote( $text ) . "(?:\s)*?</", '>' . NrHtml::span( self::toJsOrd( $text ), 'class=jsobf js-info' ) . '<span class="force-width">' . strrev( trim( $text ) ) . '</span><', $aTag );
			}
		}
		return str_replace( '<a href', '<a rel="nofollow" href', $aTag );
	}


	private static function toJsOrd( $input ) {
		return implode( ',', array_map( 'ord', str_split( $input ) ) );
	}


	public static function jsWriteObfuscated( $input ) {
		return NrHtml::span( self::toJsOrd( $input ), 'class=jsobf js-info' );
	}


	public static function bodyClasses() {
		$addedClasses = array();

		if ( pp::browser()->isMobile ) {
			$addedClasses[] = 'mobile-browser';
			if ( ppOpt::test( 'mobile_enable', 'true' ) ) {
				$addedClasses[] = 'mobile';
				$addedClasses[] = 'mobile-site-enabled';
				$addedClasses[] = 'mobile-display-width-' . strval( 320 - ( 2 * ppOpt::id( 'mobile_content_margin' ) ) );
				if ( pp::browser()->hasRetinaDisplay ) {
					$addedClasses[] = 'retina-display';
				}
			} else {
				$addedClasses[] = 'mobile-site-disabled';
			}
		} else {
			$addedClasses[] = 'not-mobile';
		}

		if ( is_404() || ppUtil::isEmptySearch() ) {
			$addedClasses[] = 'page';
		}

		if ( pp::browser()->isIPad ) {
			$addedClasses[] = 'ipad';

		} else if ( pp::browser()->isIPhone ) {
			$addedClasses[] = 'iphone';
		}

		if ( ppSidebar::onThisPage() ) {
			$addedClasses[] = 'has-sidebar';
		}

		if ( ppOpt::test( 'excerpts_on_' . ppUtil::pageType(), 'true' ) ) {
			$addedClasses[] = 'excerpted-posts';
		}

		if ( isset( $_GET['pp_slideshow_id'] ) && isset( $_GET['fullscreen'] ) ) {
			$addedClasses[] = 'popup-slideshow';
			$addedClasses[] = 'fullscreen-slideshow';
		}

		if ( ppUtil::isStaticFrontPage() ) {
			$addedClasses[] = 'is-front-page';
		}

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/MSIE (6|7)\.0/', $_SERVER['HTTP_USER_AGENT'] ) ) {
			$addedClasses[] = 'cant-antialias-downsized-imgs';
		}

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/MSIE (6|7|8)\.0/', $_SERVER['HTTP_USER_AGENT'] ) ) {
			$addedClasses[] = 'cant-fade-imgs-with-black';
		}

		if ( pp::browser()->isMobileSafari ) {
			$addedClasses[] = 'mobile-safari';
		}

		if ( pp::browser()->isTech ) {
			$addedClasses[] = 'pp-tech';
		}

		if ( pp::site()->isDev ) {
			$addedClasses[] = 'pp-dev';
		}

		if ( isset( $_GET['slideshow_popup'] ) ) {
			$addedClasses[] = 'popup-slideshow';
			$addedClasses[] = 'content-width-' . 1000;
		} else {
			$addedClasses[] = 'content-width-' . ppHelper::contentWidth( ppSidebar::onThisPage() );
		}

		if ( is_singular() ) {
			$addedClasses[] = 'article-' . ppPost::fromGlobal()->slug();
		}

		if ( 'PC' == NrUtil::browserInfo( 'os_short' ) ) {
			$addedClasses[] = 'pc';
		}

		$addedClasses[] = 'full-width-' . ppHelper::contentWidth( ppHelper::WITHOUT_SIDEBAR );
		$addedClasses[] = 'headerlayout-' . ppOpt::id( 'headerlayout' );

		$bodyClasses = get_body_class( $addedClasses );

		if ( ppGallery::isGalleryQuasiPage() ) {
			$bodyClasses[] = 'gallery-quasi-page';
			$bodyClasses[] = 'gallery-quasi-page-' . $_GET['pp_gallery_id'];
			foreach ( $bodyClasses as $index => $class ) {
				if ( $class === 'home' || preg_match( '/^(postid-|page-id-|is-front-page|article-)/', $class ) ) {
					unset( $bodyClasses[$index] );
				}
			}
		}

		return implode( ' ', $bodyClasses );
	}


	public static function favicon() {
		$favicon = ppImg::id( 'favicon' );
		if ( $favicon->exists ) {
			return NrHtml::link( 'shortcut icon', $favicon->url );
		}
	}


	public static function appleTouchIcon() {
		$appleTouchIcon = ppImg::id( 'apple_touch_icon' );
		if ( $appleTouchIcon->exists ) {
			return NrHtml::link( 'apple-touch-icon', $appleTouchIcon->url );
		}
	}


	public static function ipadMeta() {
		if ( pp::browser()->isIPad ) {
			$iPadScreenWidth = 768;
			$blogTotalWidth = ppOpt::id( 'blog_width' ) + ppHelper::blogBorderWidth();

			// this avoids really cramped appearance on ipad for certain minimalistic style designs
			if ( ppHelper::blogBorderWidth() == 0 && ppOpt::id( 'content_margin' ) == '0' ) {
				$blogTotalWidth += 60;
			}

			$metaWidth = ( $blogTotalWidth < $iPadScreenWidth ) ? 'device-width' : $blogTotalWidth;

			if ( isset( $_GET['pp_slideshow_id'] ) && isset( $_GET['fullscreen'] ) ) {
				$iPadMeta = NrHtml::meta( 'name', 'viewport', 'content', 'width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;' );
			} else {
				$iPadMeta = NrHtml::meta( 'name', 'viewport', 'content', "width=$metaWidth" );
			}

			$iPadMeta .= NrHtml::meta( 'name', 'apple-mobile-web-app-capable', 'content', 'yes' );
			return $iPadMeta;
		}
	}


	public static function emailFriendHref( ppPost $article, $context ) {
		$searchReplace = array(
			'%post_name%' => $article->title(),
			'%post_url%'  => $article->permalink(),
			'%site_name%' => pp::site()->name,
			'%site_url%'  => pp::site()->url,
		);
		$subject = str_replace( array_keys( $searchReplace ), array_values( $searchReplace ), ppOpt::id( $context . '_emailafriend_link_subject' ) );
		$body    = str_replace( array_keys( $searchReplace ), array_values( $searchReplace ), ppOpt::id( $context . '_emailafriend_link_body' ) );
		return 'mailto:?subject=' . rawurlencode( $subject ) . '&amp;body=' . rawurlencode( $body );
	}


	public static function lateConditionalJavascript() {
		ppHtml::loadSwfObject();
		ppFacebook::renderJavascript();
	}


	public static function loadSwfObject() {
		if ( ppSlideshowGallery::needsSwfObject() ) {
			$load = true;
		} else if ( ppOpt::test( 'logo_swf_switch', 'on' ) && ppImg::id( 'logo_swf' )->exists ) {
			$load = true;
		} else if ( ppOpt::test( 'masthead_display', 'custom' ) && ppImg::id( 'masthead_custom_flash' )->exists ) {
			$load = true;
		} else {
			$load = false;
		}
		if ( $load ) {
			echo NrHtml::scriptSrc( pp::site()->wpurl . '/wp-includes/js/swfobject.js' );
		}
	}
}
