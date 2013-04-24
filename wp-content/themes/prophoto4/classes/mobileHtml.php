<?php

class ppMobileHtml {


	const STANDARD_MOBILE_DEVICE_MAX_WIDTH = 533;
	const IPHONE_MAX_CSS_WIDTH = 480;

	public static function renderPrevNextArticleLinks() {
		if ( !is_single() ) {
			return;
		}
		$wrapperID = 'adjacent-posts-links';
		$prev = (object) array();
		$next = (object) array();
		if ( $prevPost = get_adjacent_post( false, '', true ) ) {
			$prev->href = get_permalink( $prevPost );
			$prev->text = ppOpt::translate( 'mobile_prev_post_link' );
			$prev->rel  = NrUtil::isIn( 'pp-slideshow-', $prevPost->post_content ) ? ' rel="external"' : '';
		}
		if ( $nextPost = get_adjacent_post( false, '', false ) ) {
			$next->href = get_permalink( $nextPost );
			$next->text = ppOpt::translate( 'mobile_next_post_link' );
			$next->rel  = NrUtil::isIn( 'pp-slideshow-', $nextPost->post_content ) ? ' rel="external"' : '';
		}
		ppUtil::renderView( 'mobile_article_prev_next_links', compact( 'wrapperID', 'prev', 'next' ) );
	}


	public static function renderOlderNewerPostsLinks() {
		if ( ppGallery::isGalleryQuasiPage() ) {
			return;
		}
		$wrapperID = 'adjacent-paged-posts-links';
		$prev = (object) array();
		$next = (object) array();
		if ( $olderPostsLink = get_next_posts_link( ppOpt::id( 'older_posts_link_text' ) ) ) {
			$prev->href = self::href( $olderPostsLink );
			$prev->text = 'older posts';
		}
		if ( $newerPostsLink = get_previous_posts_link( ppOpt::id( 'newer_posts_link_text' ) ) ) {
			$next->href = self::href( $newerPostsLink );
			$next->text = 'newer posts';
		}
		ppUtil::renderView( 'mobile_article_prev_next_links', compact( 'wrapperID', 'prev', 'next' ) );
	}


	public static function footerColorClass() {
		$map = array(
			'black'  => 'ui-bar-a',
			'blue'   => 'ui-bar-b',
			'white'  => 'ui-bar-c',
			'gray'   => 'ui-bar-d',
			'yellow' => 'ui-bar-e',
		);
		return $map[ ppOpt::id( 'mobile_footer_color_scheme' ) ];
	}


	protected static function href( $tag ) {
		preg_match( '/href=' . MATCH_QUOTED . '/', $tag, $match );
		return isset( $match[1] ) ? $match[1] : '';
	}


	public static function renderBlogHeader() {
		if ( !is_singular() || ppOpt::test( 'mobile_show_logo_on_single', 'true' ) ) {
			self::renderLogo();
		}

		if ( !is_singular() || is_front_page() ) {
			self::renderMasthead();
		}
	}


	protected static function renderLogo() {
		if ( ppOpt::test( 'mobile_logo_use_desktop', 'true' ) )  {
			if ( ppOpt::test( 'headerlayout', 'masthead_nav || nav_masthead' ) || !ppImg::id( 'logo' )->exists ) {
				return;
			} else {
				$logoImg = ppImg::id( 'logo' );
			}
		} else {
			if ( !ppImg::id( 'mobile_logo' )->exists ) {
				return;
			} else {
				$logoImg = ppImg::id( 'mobile_logo' );
			}
		}

		$logoTag = new ppImgTag( $logoImg->url, 'id=logo-img&alt=' . pp::site()->name . ' logo' );
		$logoTag = ppGdModify::constrainImgSize( $logoTag, pp::browser()->mobileScreenWidth );

		$height = intval( $logoImg->height * ( ppMobileHtml::STANDARD_MOBILE_DEVICE_MAX_WIDTH / $logoImg->width ) );
		$logoTag->height( $height );
		$logoTag->width( ppMobileHtml::STANDARD_MOBILE_DEVICE_MAX_WIDTH );

		$logoLink = NrHtml::a( $logoImg->linkurl, $logoTag->markup(), array(
			'title' => pp::site()->name,
			'rel'   => 'home',
			'id'    => 'logo-img-a',
		) );

		echo NrHtml::tag( 'header', $logoLink );
	}


	protected static function renderMasthead() {
		$prefix_ = ppOpt::test( 'mobile_masthead_use_desktop_settings', 'true' ) ? '' : 'mobile_';

		if ( ppOpt::test( "{$prefix_}masthead_display", 'off' ) ) {
			return;
		}

		$classes = 'masthead-image';
		if ( ppBlogHeader::mastheadSlideshowOnThisPage( $prefix_ ) ) {
			$classes .= ' pp-slideshow pp-slideshow-not-loaded autostart';
		}

		$img = ppImg::id( "{$prefix_}masthead_image" . ppBlogHeader::mastheadImgNum( $prefix_ ) );

		$imgTag = new ppImgTag( $img->url );
		$imgTag->width( $img->width );
		$imgTag->height( $img->height );

		ppUtil::renderView( 'header_masthead', array( 'img' => $imgTag, 'classes' => $classes, 'href' => null ) );
	}


	public static function mobileDie( $msg ) {
		ppUtil::renderView( 'mobile_die', compact( 'msg' ) );
		die;
	}
}


