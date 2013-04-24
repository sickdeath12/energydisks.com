<?php

class ppContentFilter {


	static $priority = 1000;
	const MATCH_IMGS_WITH_OPT_WRAPPING_A_HREF = "/(<a[^>]+href=(?:\"|')([^'\"]+)(?:\"|')[^>]*>)?(?:[ \t\n]+)?(<img[^>]*>)(?:[ \t\n]+)?(<\/a>)?/i";
	const MATCH_IMGS_WITH_OPT_WRAPPING_A_P = "/(?:<p[^>]*>(?:[ \t\n]+)?)?(?:<a[^>]+>(?:[ \t\n]+)?)?(<img[^>]*>)(?:[ \t\n]+)?(?:<\/a>)?(?:[ \t\n]+)?(?:<\/p>)?/i";


	public static function addAllFilters() {
		if ( is_admin() ) {
			return;
		}

		self::feedFilters();

		// filters that APPEND
		self::addFilter( 'ppContentFilter::addPostSignature', ppOpt::id( 'post_signature_filter_priority', 'int' ) );
		self::addFilter( 'ppContentFilter::addLikeButton',    ppOpt::id( 'like_btn_filter_priority', 'int' ) );

		// this one doesn't technically hook to 'the_content' but is a filter of content
		add_filter( 'the_password_form', 'ppContentFilter::passwordProtectedPhrase' );

		remove_filter( 'capital_P_dangit', 11 );
	}


	public static function addPostSignature( $content ) {
		if (
			   is_feed()
			|| is_404()
			|| is_search()
			|| !ppOpt::test( 'post_signature' )
			|| ( is_home()   && !ppOpt::test( 'post_signature_on_home', 'true' ) )
			|| ( is_page()   && !ppOpt::test( 'post_signature_on_page', 'true' ) )
			|| ( is_single() && !ppOpt::test( 'post_signature_on_single', 'true' ) )
			|| ( !$post = ppPost::fromGlobal() )
		) {
			return $content;
		}

		$signature = str_replace(
			array( '%post_title%', '%permalink%', '%post_id%', '%post_author_name%', '%post_author_id%' ),
			array( $post->title(), $post->permalink(), $post->id(), $post->authorName(), $post->authorID() ),
			ppOpt::id( 'post_signature' )
		);
		return $content . '<div class="pp-post-sig">' . $signature . '</div>';
	}


	public static function addLikeButton( $content ) {
		if ( ppFacebook::addArticleFooterLikeBtn() ) {
			return $content . ppFacebook::articleFooterLikeBtnMarkup();
		} else {
			return $content;
		}
	}


	public static function modifyImgs( $content ) {
		if ( is_feed() ) {
			return self::feedImgs( $content );
		} else {
			return self::nonFeedImgs( $content );
		}
	}


	private static function nonFeedImgs( $content ) {
		return preg_replace_callback( self::MATCH_IMGS_WITH_OPT_WRAPPING_A_HREF, 'ppContentFilter::modifyNonFeedImgs', $content );
	}


	private static function modifyNonFeedImgs( $matches ) {
		@list( $full, $linkOpen, $linkHref, $imgTag, $linkClose ) = $matches;

		// skip json-encoded img html inserted by plugins
		if ( NrUtil::isIn( 'src=\"', $full ) ) {
			return $full;
		}

		$img = ppImgTag::createFromHtml( $imgTag );

		if ( $img->hasClass( 'pp-gallery-placeholder' ) || $img->hasClass( 'psp-active' ) ) { // psp-active is plugin workaround
			return $full;
		}

		$img = ppGdModify::contentImg( $img );

		// non-protected images
		if ( ppOpt::test( 'image_protection', 'none' ) || $img->hasClass( 'exclude' ) || isset( $GLOBALS['filtering_sidebar_content'] ) ) {
			return $linkOpen . $img->markup() . $linkClose;
		}

		// remove links in certain situations
		$hrefStub = substr( $linkHref, 0, strlen( $linkHref ) - 4 );
		if ( !ppOpt::test( 'image_protection', 'right_click' ) && $linkOpen && $linkClose && !$img->hasClass( 'pp-lb-img' ) && !NrUtil::isIn( 'rel="lightbox"', $linkOpen ) && (
				( $linkHref == $img->src() ) ||
				( NrUtil::isIn( 'attachment_id=', $linkHref ) ) ||
				( NrUtil::isWebSafeImg( $linkHref ) && NrUtil::isIn( $hrefStub, $img->src() ) )
			) ) {
			$linkOpen = $linkClose = '';
		}


		// blank img overlay
		$wrapOpen = $wrapClose = '';
		if ( !pp::browser()->isMobile && ppOpt::test( 'image_protection', 'replace || watermark' ) ) {

			if ( $img->width() && $img->height() && ( $img->width() + $img->height() ) > 399 && !$img->hasClass( 'shrink-to-thumbnail' ) ) {
				// transfer alignment info
				if      ( $img->hasClass( 'aligncenter' ) ) $alignClass = 'aligncenter';
				else if ( $img->hasClass( 'alignleft' ) )   $alignClass = 'alignleft';
				else if ( $img->hasClass( 'alignright' ) )  $alignClass = 'alignright';
				else if ( $img->hasClass( 'alignnone' ) )   $alignClass = 'alignnone';
				else if ( $img->hasClass( 'pp-excerpt-img' ) ) $alignClass = 'excerpt-img pp-img-protect-excerpt-img-' . ppOpt::id( 'excerpt_image_size' );
				else $alignClass = 'aligncenter no-orig-alignclass';

				// add new markup
				$blank_img = pp::site()->themeUrl . '/images/blank.gif';
				$wrapOpen  = "
				<div class='pp-img-protect pp-img-protect-{$alignClass}' style='width:" . $img->width() . "px;'>
					<img class='pp-overlay' style='width:" . $img->width() . "px;height:" . $img->height() . "px;' src='$blank_img' />";
				$wrapClose = '</div>';
			}
		}

		return $linkOpen . $wrapOpen . $img->markup() . $wrapClose . $linkClose;
	}


	private static function feedImgs( $content ) {
		if ( ppOpt::test( 'modify_feed_images', 'false' ) ) {
			return $content;
		}

		// run image and surrounding HTML through our modifying callback
		$modifiedContent = preg_replace_callback( self::MATCH_IMGS_WITH_OPT_WRAPPING_A_P, 'ppContentFilter::modifyFeedImgs', $content );

		// image modification alert
		if ( $modifiedContent !== $content && ppOpt::test( 'modify_feed_images_alert' ) ) {
			$post = new ppPost( $GLOBALS['post'] );
			$msg = preg_replace( '/(\^([^^]+)\^)/i', '<a href="' . $post->permalink() . '">\\2</a>', ppOpt::id( 'modify_feed_images_alert' ), 1 );
			$modifiedContent = '<p><em><b>' . $msg . '</b></em></p>' . $modifiedContent;
		}

		return $modifiedContent;
	}


	private static function modifyFeedImgs( $matches ) {
		// strip images completely
		if ( ppOpt::test( 'modify_feed_images', 'remove' ) ) {
			return '';
		}

		$img = ppImgTag::createFromHtml( $matches[1] );

		// hunt for requested size thumb
		$thumb = ppPostImgUtil::relatedImg( $img->src(), ppOpt::id( 'feed_thumbnail_type' ) );

		// thumb found: swap the img source and remove size info from image html
		if ( $thumb ) {
			return $thumb->markup();

		// no thumbnail available, remove image
		} else {
			return '';
		}
	}


	public static function galleryMarkup( $text ) {
		if ( NrUtil::isIn( 'p3-placeholder', $text ) ) {

			if ( !NrUtil::isIn( 'p3-gallery-imported', $text ) ) {
				$text = preg_replace_callback(
					"/<img[^>]*p3-placeholder[^>]*>/i",
					'ppImportP3::gallery',
					$text
				);

			} else {
				$text = preg_replace( "/<img[^>]*p3-placeholder[^>]*>/i", '', $text );
			}
		}

		if ( NrUtil::isIn( 'placeholders/slideshow-placeholder', $text ) || NrUtil::isIn( 'images/slideshow_placeholder_base.jpg', $text ) ) {
			$text = preg_replace_callback(
				"/(?:<p>)?<img[^>]*slideshow-([0-9]+)[^>]*\/>(?:<\/p>)?/i",
				'ppContentFilter::slideshowMarkup',
				$text
			);
		}

		if ( NrUtil::isIn( 'placeholders/lightbox-placeholder', $text ) || NrUtil::isIn( 'images/lightbox_placeholder_base.jpg', $text ) ) {
			$text = preg_replace_callback(
				"/<img[^>]*lightbox-([0-9]+)[^>]*\/>/i",
				'ppContentFilter::lightboxMarkup',
				$text
			);
		}

		return $text;
	}


	public static function gridMarkup( $text ) {
		if ( NrUtil::isIn( 'pp-grid-placeholder', $text ) ) {
			$text = preg_replace_callback(
				"/<img[^>]*id=(?:\"|')pp-grid-([^'\"]+)(?:\"|')[^>]*\/>/i",
				'ppContentFilter::_gridMarkup',
				$text
			);
		}
		return $text;
	}


	protected static function _gridMarkup( $match ) {
		if ( isset( $match[1] ) ) {
			$gridID = $match[1];
			$grid = ppGrid::instance( $gridID );
			return $grid->markup();
		} else {
			return '';
		}
	}


	protected static function lightboxMarkup( $match ) {
		$galleryID = intval( $match[1] );
		$gallery = ppGallery::load( $galleryID );
		if ( !$gallery ) {
			return '';
		}
		$lightbox = new ppLightboxGallery( $gallery );
		return $lightbox->markup();
	}


	protected static function slideshowMarkup( $match ) {
		$galleryID = intval( $match[1] );
		$gallery = ppGallery::load( $galleryID );

		if ( !$gallery ) {
			return '';
		}

		$slideshow = ppSlideshowGallery::instance( $gallery );
		if ( isset( $_GET['dump_slideshows'] ) ) {
			NrDump::it( $slideshow );
		}
		return $slideshow->markup();
	}


	public static function passwordProtectedPhrase( $form ) {
		return str_replace( ppString::id( 'wp_password_protect_phrase' ), ppOpt::translate( 'password_protected' ), $form );
	}


	public static function absolutizeImgURLs( $url ) {
		return str_replace( array( 'src="../wp-content', 'src="/wp-content', 'src="wp-content' ), 'src="' . pp::site()->wpurl . '/wp-content', $url );
	}


	protected static function feedFilters() {
		add_filter( 'the_content_feed', 'ppPathfixer::fix' );
		add_filter( 'the_content_feed', 'ppContentFilter::galleryMarkup' );
		add_filter( 'the_content_feed', 'ppContentFilter::gridMarkup' );
		add_filter( 'the_content_feed', 'ppContentFilter::modifyImgs' );
	}


	protected static function addFilter( $func, $priority = null ) {
		if ( $priority == null || intval( $priority ) == 0 ) {
			$priority = self::$priority;
			self::$priority++;
		}
		add_filter( 'the_content', $func, $priority );
	}

}



