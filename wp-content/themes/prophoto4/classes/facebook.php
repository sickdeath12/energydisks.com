<?php

class ppFacebook {


	protected static $javascriptSDKRequired   = false;
	protected static $commentListenerRequired = false;


	public static function meta( ppQuery $thisPage, $article, $seoMetaDesc = '' ) {
		if ( !$thisPage->isBlogPostsPage() && !$thisPage->isArticle() ) {
			return '';
		}

		$meta  = self::ogMeta( 'og:site_name', pp::site()->name );
		$meta .= self::ogMeta( 'og:type', ( $thisPage->isBlogPostsPage() || $thisPage->isStaticFrontPage() ) ? 'website' : 'article' );

		if ( $thisPage->isBlogPostsPage() ) {
			$meta .= self::ogMeta( 'og:title', ppOpt::orVal( 'facebook_blog_posts_page_title', pp::site()->tagline ) );
			$meta .= self::ogMeta( 'og:url', pp::site()->url );
			if ( $desc = ppOpt::id( 'facebook_blog_posts_page_desc' ) ) {
				$meta .= self::ogMeta( 'og:description', $desc );
			} else if ( $seoMetaDesc ) {
				$meta .= self::ogMeta( 'og:description', $seoMetaDesc );
			}

		} else if ( $thisPage->isStaticFrontPage() ) {
			$meta .= self::ogMeta( 'og:title', ppOpt::orVal( 'facebook_static_front_page_title', $article->title() ) );
			$meta .= self::ogMeta( 'og:url', $article->permalink() );
			if ( $desc = ppOpt::id( 'facebook_static_front_page_desc' ) ) {
				$meta .= self::ogMeta( 'og:description', $desc );
			} else if ( $seoMetaDesc ) {
				$meta .= self::ogMeta( 'og:description', $seoMetaDesc );
			}

		} else if ( $thisPage->isArticle() ) {
			$meta .= self::ogMeta( 'og:title', $article->title() );
			$meta .= self::ogMeta( 'og:url', $article->permalink() );
			if ( $seoMetaDesc ) {
				$meta .= self::ogMeta( 'og:description', $seoMetaDesc );
			}
		}

		if ( $thisPage->isStaticFrontPage() && ppImg::id( 'facebook_static_front_page' )->exists ) {
			$imgSrc = ppImg::id( 'facebook_static_front_page' )->url;

		} else if ( $thisPage->isArticle() && $excerptImgSrc = $article->excerptImgSrc() ) {
			$imgSrc = $excerptImgSrc;

		} else if ( ppImg::id( 'fb_home' )->exists ) {
			$imgSrc = ppImg::id( 'fb_home' )->url;

		} else if ( ppImg::id( 'logo' )->exists ) {
			$imgSrc = ppImg::id( 'logo' )->url;

		} else if ( ppImg::id( 'masthead_image1' )->exists ) {
			$imgSrc = ppImg::id( 'masthead_image1' )->url;

		} else {
			$imgSrc = null;
		}

		if ( $imgSrc ) {
			$meta .= self::ogMeta( 'og:image', ppGdModify::constrainImgSize( new ppImgTag( $imgSrc ), 1200 )->src() );
		}

		if ( $fbAdmins = ppOpt::id( 'facebook_admins' ) ) {
			if ( is_numeric( $fbAdmins ) ) {
				$meta .= self::ogMeta( 'fb:admins', $fbAdmins );
			} else if ( NrUtil::isIn( ',', $fbAdmins ) && is_numeric( join( '', explode( ',', $fbAdmins ) ) ) ) {
				$meta .= self::ogMeta( 'fb:admins', $fbAdmins );
			}
		}

		return apply_filters( 'pp_facebook_meta', $meta, $article );
	}


	public static function commentsMarkup( ppPost $article, ppQuery $thisPage ) {
		if ( !$thisPage->isArticle() && ppOpt::test( 'fb_comments_num_shown_nonsingle', '0' ) ) {
			return '';
		}

		self::$javascriptSDKRequired   = true;
		self::$commentListenerRequired = true;
		$params = array(
			'href'        => $article->permalink(),
			'width'       => ppHelper::contentWidth( ppSidebar::onThisPage() ),
			'colorscheme' => ppOpt::id( 'fb_comments_colorscheme' ),
			'num-posts'   => $thisPage->isArticle() ? ppOpt::id( 'fb_comments_num_shown_single' ) : ppOpt::id( 'fb_comments_num_shown_nonsingle' )
		);
		$markup = self::fbMarkup( 'fb-comments', $params );

		if ( ppOpt::test( 'fb_comments_also_show_unique_wp', 'false' ) ) {
			$commentsForSEO = '<div class="fb-comments-text-seo comments-area-hidden">';
			$comments = $article->comments();
			foreach ( $comments as $comment ) {
				$commentsForSEO .= NrHtml::div(
					$comment->authorMarkup() . $comment->timeMarkup() . $comment->text(),
					'class=' . $comment->classes()
				);
			}
			$commentsForSEO .= '</div>';
			$markup .= $commentsForSEO;
		}
		return $markup;
	}


	protected static function fbMarkup( $class, $params ) {
		$markup = '<div class="' . $class . '"';
		foreach ( $params as $attr => $val ) {
			$markup .= ' data-' . $attr . '="' . $val . '"';
		}
		$markup .= '></div>';
		return $markup;
	}


	public static function addArticleFooterLikeBtn() {
		if ( ppOpt::test( 'like_btn_enable', 'false' ) ) {
			$return = false;

		} else if ( is_404() || is_search() || is_feed() || ppGallery::isGalleryQuasiPage() ) {
			$return = false;

		} else if ( is_home() && !ppOpt::test( 'like_btn_on_home' ) ) {
			$return = false;

		} else if ( is_page() && !ppOpt::test( 'like_btn_on_page' ) ) {
			$return = false;

		} else if ( is_single() && !ppOpt::test( 'like_btn_on_single' ) ) {
			$return = false;

		} else {
			$return = (bool) ppPost::fromGlobal();
		}

		return apply_filters( 'pp_do_article_footer_like_btn', $return );
	}


	public static function likeBoxMarkup( $params ) {
		self::$javascriptSDKRequired = true;
		if ( isset( $params['width'] ) ) {
			$params['width'] = min( $params['width'], ppWidgetUtil::areaWidth() );
		}
		$params = apply_filters( "pp_like_box_params", $params );
		return self::fbMarkup( 'fb-like-box', $params );
	}


	public static function articleFooterLikeBtnMarkup() {
		return self::likeBtnMarkup( 'article_footer', ppOpt::id( 'like_btn_layout' ), ppOpt::id( 'like_btn_with_send_btn' ) );
	}


	public static function callToActionLikeBtnMarkup( $layout, $withSend ) {
		return self::likeBtnMarkup( 'cta', $layout, $withSend );
	}


	protected static function likeBtnMarkup( $context, $layout, $withSend ) {
		if ( !$post = ppPost::fromGlobal() ) {
			return '';
		}
		self::$javascriptSDKRequired = true;

		switch ( $layout ) {
			case 'standard':
			case 'standard_with_faces':
				$width = '450';
				break;
			case 'button_count':
				$width = '90';
				break;
			case 'box_count':
				$width = '55';
				break;
		}

		$params = apply_filters( "pp_{$context}_like_btn_params", array(
			'href'        => ppPost::fromGlobal()->permalink(),
			'send'        => $withSend,
			'layout'      => $layout,
			'width'       => $width,
			'show-faces'  => ( $layout == 'standard_with_faces' ) ? 'true' : 'false',
			'action'      => ppOpt::id( 'like_btn_verb' ),
			'colorscheme' => ppOpt::id( 'like_btn_color_scheme' ),
		) );

		$markup = self::fbMarkup( 'fb-like', $params );

		if ( $context == 'article_footer' ) {
			$markup = '<div class="pp-fb-like-btn-wrap">' . $markup . '</div>';
		}

		return apply_filters( "pp_{$context}_like_btn_markup", $markup );
	}


	public static function refreshNonArticleOGCache_onImgUpdate( $imgId ) {
		if ( !pp::site()->hasStaticFrontPage ) {
			if ( $imgId == 'fb_home' ) {
				self::lintURL( pp::site()->url );
			}
		} else {
			if ( $imgId == 'fb_home' ) {
				self::lintURL( get_permalink( get_option( 'page_for_posts' ) ) );
			} else {
				self::lintURL( pp::site()->url );
			}
		}
	}


	public static function refreshNonArticleOGCache_onOptUpdate( $optID ) {
		static $addedActionForBlogPostsPage   = false;
		static $addedActionForStaticFrontPage = false;
		if ( NrUtil::isIn( 'static_front_page', $optID ) && pp::site()->hasStaticFrontPage && !$addedActionForStaticFrontPage ) {
			add_action( 'shutdown', ppUtil::func( 'ppFacebook::lintURL( "' . pp::site()->url . '" );' ), 11 );
			$addedActionForStaticFrontPage = true;
		}
		if ( NrUtil::isIn( 'blog_posts_page', $optID ) && !$addedActionForBlogPostsPage ) {
			$url = pp::site()->hasStaticFrontPage ? get_permalink( get_option( 'page_for_posts' ) ) : pp::site()->url;
			add_action( 'shutdown', ppUtil::func( 'ppFacebook::lintURL( "' . $url . '" );' ), 11 );
			$addedActionForBlogPostsPage = true;
		}
	}


	public static function refreshArticleOGCache( $postID ) {
		if ( NrUtil::GET( 'action', 'edit' ) && ( NrUtil::GET( 'message', '1' ) || NrUtil::GET( 'message', '6' ) ) && !pp::site()->isDev ) {
			echo NrHtml::script( '
			if ( typeof jQuery != "undefined" ) {
				jQuery.post( "https://graph.facebook.com/", { "id": "' . get_permalink( $_GET['post'] ) . '", "scrape": true } );
			}' );
		}
	}


	public static function lintURL( $url ) {
		wp_remote_post( 'https://graph.facebook.com/', array( 'body' => array( 'id' => $url, 'scrape' => true ) ) );
	}


	public static function renderIDFinderScreen() {
		if ( isset( $_POST['fb_profile_url'] ) ) {
			$found = self::findFacebookID( $_POST['fb_profile_url'], new WP_Http() );
			if ( $found->ID ) {
				ppIframe::wp_iframe( ppUtil::func( "ppUtil::renderView( 'found_facebook_id', array( 'ID' => $found->ID, 'name' => '$found->name' ) );" ) );
			} else {
				ppIFrame::wp_iframe( ppUtil::func( "ppUtil::renderView( 'get_facebook_id', array( 'errorMsg' => '$found->errorMsg' ) );" ) );
			}
		} else {
			ppIFrame::wp_iframe( ppUtil::func( "ppUtil::renderView( 'get_facebook_id', array( 'errorMsg' => '' ) );" ) );
		}
	}


	public static function findFacebookID( $URL, WP_Http $http ) {
		$found = (object) array( 'ID' => null, 'name' => null, 'errorMsg' => null );
		$pageError = 'Please enter a Facebook personal profile URL, not a Facebook "Page" URL.';
		$supplyURL = 'Please supply the URL of your <b>personal Facebook profile</b>';
		$URL = trim( untrailingslashit( $URL ) );

		if ( empty( $URL ) ) {
			$found->errorMsg = "$supplyURL.";

		} else if ( preg_match( '/facebook.com\/profile\.php\?id=([0-9]+)$/', $URL, $urlMatch ) ) {
			$found->ID = $urlMatch[1];

		} else if ( NrUtil::validUrl( $URL ) && !NrUtil::isIn( 'facebook.com', $URL ) ) {
			$found->errorMsg = 'Not a valid Facebook personal profile URL.';

		} else if ( NrUtil::isIn( 'facebook.com/pages/', $URL ) ) {
			$found->errorMsg = $pageError;

		} else if ( preg_match( '/http(s)?:\/\/(www\.)?facebook.com$/', $URL ) ) {
			$found->errorMsg = "$supplyURL, not the Facebook home page.";

		} else if ( !NrUtil::validUrl( $URL ) && !NrUtil::isIn( '/', $URL ) ) {
			$vanitySlug = $URL;

		} else {
			$vanitySlug = end( explode( '/', $URL ) );
		}

		if ( isset( $vanitySlug ) ) {
			$json = wp_remote_retrieve_body( $http->get( 'http://graph.facebook.com/' . $vanitySlug ) );

			if ( $json && is_object( $obj = json_decode( $json ) ) ) {

				if ( isset( $obj->category ) && isset( $obj->likes ) ) {
					$found->errorMsg = $pageError;

				} else if ( isset( $obj->id ) && isset( $obj->name ) ) {
					$found->ID   = $obj->id;
					$found->name = $obj->name;

				} else if ( isset( $obj->error ) && $obj->error->code = 803 ) {
					$found->errorMsg = 'No Facebook user could be found for username "' . $vanitySlug . '"';
				}

			} else {
				$found->errorMsg = ppString::id( 'facebook_id_not_found' );
			}
		}

		return $found;
	}


	protected static function ogMeta( $property, $content ) {
		return NrHtml::meta( 'property', $property, 'content', $content );
	}


	public static function renderJavascript() {
		if ( self::javascriptRequired() ) {
			echo apply_filters( 'pp_facebook_javascript', NrHtml::script( file_get_contents( TEMPLATEPATH . '/dynamic/js/facebook.js' ) ) );
		}
	}


	protected function javascriptRequired() {
		if ( self::$javascriptSDKRequired ) {
			return true;
		} else if ( !pp::browser()->isMobile || ppOpt::test( 'mobile_enable', 'false' ) ) {
			return ppWidgetUtil::instanceOfTypeExists( 'pp-facebook-likebox' );
		} else if ( ppOpt::test( 'fb_comments_enable', 'true' ) || ppOpt::test( 'like_btn_enable', 'true' ) ) {
			return true;
		}
	}
}


