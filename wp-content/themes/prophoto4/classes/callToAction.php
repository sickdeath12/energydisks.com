<?php


class ppCallToAction {


	public static function markup( $where ) {
		if ( self::showBtnsHere( $where ) ) {
			$items = array();
			for ( $i = 1; $i <= pp::num()->maxCallToActionItems; $i++ ) {
				if ( self::itemValid( $i ) ) {
					$items[] = self::itemMarkup( $i );
				}
			}
			if ( $items ) {
				$items = self::addSeparators( $items );
				return NrHtml::section( join( '', $items ), 'class=call-to-action-wrap' );
			}
		}
		return '';
	}


	public static function render( $where ) {
		echo self::markup( $where );
	}


	public static function heightAlignCss() {
		$fontSize = ppOpt::cascade( 'call_to_action_link_font_size', 'gen_font_size' );
		$heights = array();

		if ( ppOpt::test( 'call_to_action_separator', 'image' ) && ppImg::id( 'call_to_action_separator_img' )->exists ) {
			$heights['sep'] = ppImg::id( 'call_to_action_separator_img' )->height;
		} else if ( ppOpt::test( 'call_to_action_separator', 'text' ) ) {
			$heights['sep'] = $fontSize;
		}

		for ( $i = 1; $i <= pp::num()->maxCallToActionItems; $i++ ) {
			if ( self::itemValid( $i ) ) {
				if ( ppOpt::test( 'call_to_action_' . $i, 'facebook_like_btn' ) ) {
					if ( ppOpt::test( 'call_to_action_fb_like_layout_' . $i, 'button_count' ) ) {
						$heights[$i] = 20;
					} else {
						$heights[$i] = ppOpt::test( 'call_to_action_fb_like_with_send_' . $i, 'false' ) ? 62 : 92;
					}
				} else if ( ppOpt::test( 'call_to_action_display_' . $i, 'image' ) ) {
					$heights[$i] = ppImg::id( 'call_to_action_' . $i )->height;
				} else {
					$heights[$i] = $fontSize;
				}
			}
		}

		$unique = array_unique( $heights );
		if ( count( $unique ) == 1 ) {
			return '';
		}
		arsort( $heights );
		$tallest = reset( $heights );

		$css = '';
		if ( isset( $heights['sep'] ) && $heights['sep'] != $tallest ) {
			$customMargin = ( ( $tallest - $heights['sep'] ) / 2 );
			$css .= ".call-to-action-wrap .sep { margin-top:{$customMargin}px; }\n";
			unset( $heights['sep'] );
		}

		foreach ( $heights as $num => $height ) {
			if ( $height == $tallest ) {
				continue;
			}
			$customMargin = ( ( $tallest - $height ) / 2 );
			$css .= "body .call-to-action-wrap .item-$num { margin-top:{$customMargin}px; }\n";
		}

		return $css;
	}


	protected static function itemValid( $num ) {
		if ( !ppOpt::test( 'call_to_action_' . $num, 'off' ) ) {
			if ( ppOpt::test( 'call_to_action_' . $num, 'facebook_like_btn' ) ) {
				return true;
			} else if ( ppOpt::test( 'call_to_action_display_' . $num, 'text' ) && ppOpt::test( 'call_to_action_text_' . $num ) ) {
				return true;
			} else if ( ppOpt::test( 'call_to_action_display_' . $num, 'image' ) ) {
				return ppImg::id( 'call_to_action_' . $num )->exists;
			}
		} else {
			return false;
		}
	}


	protected static function itemMarkup( $num ) {
		if ( ppOpt::test( 'call_to_action_' . $num, 'facebook_like_btn' ) ) {
			$likeBtn = ppFacebook::callToActionLikeBtnMarkup(
				ppOpt::id( 'call_to_action_fb_like_layout_' . $num ),
				ppOpt::id( 'call_to_action_fb_like_with_send_' . $num )
			);
			return NrHtml::div( $likeBtn, self::attr( $num ) );
		} else if ( ppOpt::test( 'call_to_action_' . $num, 'custom_url' ) && !ppOpt::test( 'call_to_action_url_' . $num ) ) {
			return NrHtml::span( self::anchor( $num ), self::attr( $num ) );
		} else {
			return NrHtml::a( self::href( $num ), self::anchor( $num ), self::attr( $num ) );
		}
	}


	protected static function anchor( $num ) {
		if ( ppOpt::test( 'call_to_action_display_' . $num, 'text' ) ) {
			return ppOpt::id( 'call_to_action_text_' . $num );

		} else if ( ppOpt::test( 'call_to_action_display_' . $num, 'image' ) ) {
			$img = ppImg::id( 'call_to_action_' . $num );
			if ( $img->exists ) {
				return NrHtml::img( $img->url );
			}
		}
	}


	protected static function href( $num ) {
		if ( ppOpt::test( 'call_to_action_' . $num, 'show_contact_form' ) ) {
			return '#';
		} else {
			$article = ppPost::fromGlobal();
			switch ( ppOpt::id( 'call_to_action_' . $num ) ) {
				case 'back_to_top':
					return '#top';
				case 'follow_on_twitter':
					return 'http://twitter.com/' . trim( ppOpt::orVal( 'call_to_action_twittername_' . $num, ppOpt::id( 'twitter_name' ) ) ) . '/';
				case 'custom_url':
					return ppOpt::id( 'call_to_action_url_' . $num );
				case 'tweet_this_url':
					if ( $article ) {
						return 'http://twitter.com/share?url=' . rawurlencode( $article->permalink() );
					}
					break;
				case 'share_on_facebook';
					if ( $article ) {
						return 'http://facebook.com/share.php?u=' . rawurlencode( $article->permalink() );
					}
					break;
				case 'pinterest_follow_me':
					return 'http://pinterest.com/' . trim( ppOpt::id( 'call_to_action_pinterest_name_' . $num ) ) . '/';
					break;
				case 'pinterest_pin_site_image':
					return "javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());";
					break;
				case 'subscribe_rss':
					return ppRss::url();
				case 'subscribe_by_email':
					return 'http://feedburner.google.com/fb/a/mailverify?email=&uri=' . ppRss::feedburnerId() . '&loc=' . ppOpt::id( 'subscribebyemail_lang' );
				case 'email_me':
					if ( ppOpt::test( 'call_to_action_email_' . $num ) ) {
						return 'mailto:' . ppOpt::id( 'call_to_action_email_' . $num );
					} else {
						return 'mailto:' . get_option( 'admin_email' );
					}
				case 'email_this_url':
					if ( $article ) {
						return ppHtml::emailFriendHref( $article, 'call_to_action_' . $num );
					}
					break;

			}
		}
		return '#';
	}


	protected static function attr( $num ) {
		$target = '_self';
		$classes = array(
			'item',
			'item-' . $num,
			'type-' . ppOpt::id( 'call_to_action_' . $num ),
		);
		if ( ppOpt::test( 'call_to_action_' . $num, 'facebook_like_btn' ) ) {
			$classes[] = 'fb-like-btn-layout-' . ppOpt::id( 'call_to_action_fb_like_layout_' . $num );
			$classes[] = 'fb-like-btn-send-'   . ppOpt::id( 'call_to_action_fb_like_with_send_' . $num );
		} else {
			$classes[] = 'display-' . ppOpt::id( 'call_to_action_display_' . $num );
		}
		switch ( ppOpt::id( 'call_to_action_' . $num ) ) {
			case 'show_contact_form':
				$classes[] = 'show-hidden-contact_form';
				break;
			case 'share_on_facebook':
				$target = '_blank';
				break;
			case 'tweet_this_url':
				$target = '_blank';
				break;
			case 'follow_on_twitter':
				$target = '_blank';
				break;
			case 'subscribe_by_email':
				$target = '_blank';
				break;
			case 'pinterest_follow_me':
			case 'custom_url':
				$target = ppOpt::id( 'call_to_action_target_' . $num );
				break;
		}
		return 'class=' . join( ' ', $classes ) . '&target=' . $target;
	}


	protected static function addSeparators( $items ) {
		if ( ppOpt::test( 'call_to_action_separator' ) ) {

			if ( ppOpt::test( 'call_to_action_separator', 'text' ) && trim( ppOpt::id( 'call_to_action_separator_text' ) ) != '' ) {
				$sep = trim( ppOpt::id( 'call_to_action_separator_text' ) );

			} else if ( ppImg::id( 'call_to_action_separator_img' )->exists ) {
				$sep = '&nbsp;';

			} else {
				return $items;
			}

			$withSeps = array();
			foreach ( $items as $item ) {
				$withSeps[] = $item;
				$withSeps[] = "<span class=\"sep\">$sep</span>";
			}

			array_pop( $withSeps );
			return $withSeps;

		} else {
			return $items;
		}
	}


	protected static function showBtnsHere( $where ) {
		if ( !ppOpt::test( 'call_to_action_enable', 'true' ) ) {
			return false;

		} else if ( !ppOpt::test( 'call_to_action_location', $where ) ) {
			return false;

		} else if ( is_home() && !ppOpt::test( 'call_to_action_on_home', 'true' ) ) {
			return false;

		} else if ( is_single() && !ppOpt::test( 'call_to_action_on_single', 'true' ) ) {
			return false;

		} else if ( is_page() && !ppOpt::test( 'call_to_action_on_page', 'true' ) ) {
			return false;

		} else if ( is_404() ) {
			return false;

		} else if ( ppUtil::isEmptySearch() ) {
			return false;

		} else {
			return !is_archive();
		}
	}
}

