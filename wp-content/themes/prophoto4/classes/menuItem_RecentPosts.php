<?php


class ppMenuItem_RecentPosts extends ppMenuItem_Internal {


	protected $numRecentPosts;
	protected $hasOwnChildren = true;


	public function children() {
		return wp_get_archives( "type=postbypost&limit={$this->numRecentPosts}&echo=0" );
	}


	public function mobileChildren() {
		global $wpdb;
		$query =
			"SELECT id, post_title, post_date
			 FROM $wpdb->posts
			 WHERE post_type = 'post' AND post_status = 'publish'
			 ORDER BY post_date
			 DESC
			 LIMIT {$this->numRecentPosts}";

		$key = md5($query);
		$cache = wp_cache_get( 'wp_get_archives' , 'general');
		if ( !isset( $cache[ $key ] ) ) {
			$arcresults = $wpdb->get_results($query);
			$cache[ $key ] = $arcresults;
			wp_cache_set( 'wp_get_archives', $cache, 'general' );
		} else {
			$arcresults = $cache[ $key ];
		}

		$children = array();
		if ( $arcresults ) {
			foreach ( (array) $arcresults as $arcresult ) {
				if ( $arcresult->post_date != '0000-00-00 00:00:00' ) {
					$text = strip_tags( apply_filters( 'the_title', $arcresult->post_title ) );
					$url  = get_permalink( $arcresult->id );
					$children[$text] = $url;
				}
			}
		}
		return $children;
	}


	protected function __construct( $ID, $itemData, $children ) {
		$this->numRecentPosts = ( isset( $itemData->numRecentPosts ) && is_numeric( $itemData->numRecentPosts ) ) ? $itemData->numRecentPosts : 8;
		parent::__construct( $ID, $itemData, $children );
	}
}

