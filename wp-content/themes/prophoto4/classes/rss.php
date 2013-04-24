<?php

class ppRss {


	public static function url() {
		$feedburnerUrl = self::feedburnerUrl();

		if ( empty( $feedburnerUrl ) ) {
			return get_bloginfo( 'rss2_url', 'display' );
		} else {
			return $feedburnerUrl;
		}
	}


	public static function link() {
		$link = NrHtml::link( 'alternate', ppRss::url(), 'type=application/rss+xml&title=' . esc_attr( pp::site()->name ) . ' Posts RSS feed' );
		return str_replace( 'rss xml', 'rss+xml', $link );
	}


	public static function feedburnerId() {
		$feedburnerUrl = self::feedburnerUrl();

		if ( '' === $feedburnerUrl ) {
			return '';
		}

		return rtrim( end( explode( '.feedburner.com/', $feedburnerUrl ) ), '/' );
	}


	public static function feedburnerRedirect() {

		if ( !is_feed() || !ppOpt::test( 'feedburner' ) || preg_match( '/feedburner/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
			return;
		}

		// set & validate the feed url
		$feedburnerUrl = ppOpt::id( 'feedburner' );
		if ( !NrUtil::validUrl( $feedburnerUrl ) || $feedburnerUrl == get_bloginfo( 'rss2_url', 'display' ) ) {
			return;
		}

		// Don't redirect comment feed
		if ( ( isset( $_GET['feed'] ) && $_GET['feed'] == 'comments-rss2' ) || is_single() ) {
			return;

		} else {

			// check if we're in a category or tag
			$request = $GLOBALS['wp'];
			$cat = ( isset( $request->query_vars['category_name'] ) || isset( $request->query_vars['cat'] ) );
			$tag = isset( $request->query_vars['tag'] );

			// If this is a category/tag feed do nothing
			if ( $cat || $tag ) {
				return;
			} else {
				return ppUtil::redirect( $feedburnerUrl );
			}
		}
	}


	private static function feedburnerUrl() {
		$feedburnerUrl = trim( ppOpt::id( 'feedburner' ) );

		if ( empty( $feedburnerUrl ) ) {
			return '';

		} else if ( !NrUtil::validUrl( $feedburnerUrl ) ) {
			new ppIssue( 'Stored feedburner url was not a valid url, was reset to blank', '' );
			ppOpt::update( 'feedburner', '' );
			return '';

		} else if ( !NrUtil::isIn( 'feeds.feedburner.com', $feedburnerUrl ) ) {
			new ppIssue( 'Invalid stored feedburner url, did not contain "feeds.feedburner.com", was reset to blank' );
			ppOpt::update( 'feedburner', '' );
			return '';

		} else if ( NrUtil::isIn( ' ', $feedburnerUrl ) ) {
			new ppIssue( 'Extra space in feedburner url, trimming and updating' );
			$feedburnerUrl = str_replace( ' ', '', $feedburnerUrl );
			ppOpt::update( 'feedburner', $feedburnerUrl );
			return $feedburnerUrl;

		} else {
			return $feedburnerUrl;
		}
	}
}

