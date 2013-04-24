<?php

class ppContentRenderer {


	protected static $renderingPosts;


	public static function render() {

		do_action( 'pp_before_render_content' );

		if ( is_404() ) {
			return self::render404();
		}

		$wpPostObjects = self::wpPostObjects();

		self::renderPageIntro( $wpPostObjects );

		self::startRenderingPosts();

		if ( self::renderingGridExcerpts() ) {
			ppGrid::excerpts( $wpPostObjects )->render();

		} else {
			foreach ( $wpPostObjects as $index => $wpPostObject ) {
				self::setupWpGlobals( $wpPostObject, $index );
				$post = new ppPost( $wpPostObject );
				ppPost::setGlobalPost( $post );
				$post->render();
			}
		}

		self::endRenderingPosts();

		if ( is_search() && empty( $wpPostObjects ) ) {
			self::renderEmptySearch();
		}

		if ( !is_singular() && ( !pp::browser()->isMobile || ppOpt::test( 'mobile_enable', 'false' ) ) ) {
			self::renderPagingNavigation();
		}

		do_action( 'pp_after_render_content' );
	}


	public static function renderingGridExcerpts() {
		if ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) {
			return false;
		} else {
			return ( ppOpt::test( 'excerpt_style', 'grid' ) && ppOpt::test( 'excerpts_on_' . ppUtil::pageType(), 'true' ) );
		}
	}


	public static function renderingPosts() {
		return self::$renderingPosts;
	}


	protected static function renderPagingNavigation() {
		global $wp_query, $paged;

		if ( self::renderingGridExcerpts() ) {
			$gridMaxPages = ceil( $wp_query->found_posts / ( ppOpt::id( 'excerpt_grid_rows', 'int' ) * ppOpt::id( 'excerpt_grid_cols', 'int' ) ) );
		} else {
			$gridMaxPages = null;
		}

		if ( ppOpt::test( 'paginate_post_navigation', 'true' ) ) {

			// we use get_pagenum_link() to let WP handle figuring out the permalink
			// structure, then we work backwards to build up correct format and base
			$sample = str_replace( '56789', '%#%', get_pagenum_link( 56789 ) );
			$format = substr( $sample, strrpos( $sample, 'page' ) );
			$base   = str_replace( $format, '%_%', $sample );

			$total = $gridMaxPages ? $gridMaxPages : $wp_query->max_num_pages;

			$paginatedLinks = paginate_links( array(
				'base'      => $base,
				'format'    => $format,
				'total'     => $total,
				'current'   => ( isset( $paged ) && $paged != 0 ) ? $paged : 1,
				'prev_next' => ppOpt::id( 'pagination_show_prev_next', 'bool' ),
				'prev_text' => ppOpt::id( 'pagination_prev_text' ),
				'next_text' => ppOpt::id( 'pagination_next_text' ),
				'mid_size'  => max( 1, intval( ( ppOpt::id( 'max_paginated_links', 'int' ) - 3 ) / 2 ) ),
				'type'      => 'array'
			) );
			if ( $paginatedLinks && is_array( $paginatedLinks ) ) {
				echo NrHtml::ul( '<li>' . implode( $paginatedLinks, "</li>\n<li>" ) . '</li>', 'class=paginated-links sc content-bg' );
			}

		} else {
			$olderPostsLink = get_next_posts_link( ppOpt::id( 'older_posts_link_text' ), $gridMaxPages ? $gridMaxPages : 0 );
			$newerPostsLink = get_previous_posts_link( ppOpt::id( 'newer_posts_link_text' ) );
			if ( $olderPostsLink || $newerPostsLink ) {
				ppUtil::renderView( 'older_newer_posts_links', compact( 'olderPostsLink', 'newerPostsLink' ) );
			}
		}
	}


	protected static function renderPageIntro( $wpPostObjects ) {
		$meta = null;

		if ( is_author() ) {
			$post   = new ppPost( array_shift( $wpPostObjects ) );
			$prefix = ppOpt::id( 'translate_author_archives' );
			$title  = NrUtil::validUrl( $post->authorProfileUrl() ) ? NrHtml::a( $post->authorProfileUrl(), $post->authorName() ) : $post->authorProfileUrl();
			$meta   = $post->authorDesc();

		} else if ( is_tag() ) {
			$prefix = ppOpt::translate( 'tag_archives' );
			$title  = single_tag_title( '', NO_ECHO );
			$meta   = category_description();

		} else if ( is_category() ) {
			$prefix = ppOpt::translate( 'category_archives' );
			$title  = single_cat_title( '', NO_ECHO );
			$meta   = category_description();

		} else if ( is_search() ) {
			$prefix = ppOpt::translate( 'search_results' );
			$title  = apply_filters( 'the_search_query', get_search_query() );

		} else if ( is_day() ) {
			$prefix = 'Daily Archives:';
			$title  = get_the_time( get_option( 'date_format' ) );

		} else if ( is_month() ) {
			$prefix = ppOpt::translate( 'archives_monthly' );
			$title  = get_the_time( 'F Y' );

		} else if ( is_year() ) {
			$prefix = ppOpt::translate( 'archives_yearly' );
			$title  = get_the_time( 'Y' );

		} else {
			return;
		}

		ppUtil::renderView( 'page_title', compact( 'prefix', 'title', 'meta' ) );
	}


	protected static function renderEmptySearch() {
		$postObj = self::emptyPostObject();
		$postObj->post_title    = ppOpt::translate( 'search_notfound_header' );
		$postObj->post_name     = 'no-search-results';
		$postObj->post_content  = ppOpt::translate( 'search_notfound_text' );
		$postObj->post_content .= ppUtil::renderView( 'search_in_post', null, ppUtil::RETURN_VIEW );
		self::setupWpGlobals( $postObj );
		ppUtil::renderView( pp::browser()->isMobile ? 'mobile_article' : 'article', array( 'article' => new ppPost( $postObj ) ) );
	}



	protected static function render404() {
		$custom404 = new WP_Query( array( 'post_type' => 'page', 'name' => 'custom-404' ) );
		wp_reset_query();

		if ( isset( $custom404->post ) && $custom404->post != '' ) {
			$postObj = $custom404->post;

		} else {
			$postObj = self::emptyPostObject();
			$postObj->post_title   = ppOpt::translate( '404_header' );
			$postObj->post_content = ppOpt::translate( '404_text' );
		}

		$postObj->post_content .= ppUtil::renderView( 'search_in_post', null, ppUtil::RETURN_VIEW );
		self::setupWpGlobals( $postObj );
		ppUtil::renderView( 'article', array( 'article' => new ppPost( $postObj ) ) );
	}



	protected static function wpPostObjects() {
		global $wp_query;
		return (array) $wp_query->posts;
	}


	protected static function setupWpGlobals( $wpPostObject, $index = 0 ) {
		unset( $GLOBALS['post'] );
		$GLOBALS['post'] = $wpPostObject;
		setup_postdata( $wpPostObject );
		$GLOBALS['wp_query']->post = $wpPostObject;
		$GLOBALS['wp_query']->current_post = $index;
	}


	protected static function startRenderingPosts() {
		global $wp_query;
		self::$renderingPosts  = true;
		$wp_query->in_the_loop = true;
		do_action_ref_array( 'loop_start', array( $wp_query ) );
	}


	protected static function endRenderingPosts() {
		global $wp_query;
		self::$renderingPosts  = false;
		$wp_query->in_the_loop = false;
		do_action_ref_array( 'loop_end', array( $wp_query ) );
	}


	public static function emptyPostObject() {
		$time = current_time( 'mysql' );
		return (object) array(
			'ID' => 0,
			'post_author' => 1,
			'post_date' => $time,
			'post_date_gmt' => $time,
			'post_content' => '',
			'post_title' => '',
			'post_excerpt' => '',
			'post_status' => 'publish',
			'comment_status' => '',
			'ping_status' => '',
			'post_password' => '',
			'post_name' => '',
			'to_ping' => '',
			'pinged' => '',
			'post_modified' => $time,
			'post_modified_gmt' => $time,
			'post_content_filtered' => '',
			'post_parent' => 0,
			'guid' => '',
			'menu_order' => '',
			'post_type' => 'post',
			'post_mime_type' => '',
			'comment_count' => 0,
			'ancestors' => array(),
			'filter' => 'raw',
		);
	}
}

