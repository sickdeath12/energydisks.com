<?php


class ppMenuItem_Archives extends ppMenuItem_Internal {


	protected $archivesNestThreshold = 12;
	protected $classes = array( 'menu-item-archives' );
	protected $hasOwnChildren = true;


	public function children() {
		global $wpdb, $wp_locale;

		// Fetch list of archive, from DB or from cache if applicable
		$query =
			"SELECT DISTINCT YEAR(post_date) AS `year`,
			MONTH(post_date) AS `month`,
			count(ID) as posts
			FROM $wpdb->posts
			WHERE
				post_type = 'post' AND
				post_status = 'publish'
			GROUP BY YEAR(post_date), MONTH(post_date)
			ORDER BY post_date DESC";

		$key = md5( $query );
		$cache = wp_cache_get( 'archive_dropdown_menu' , 'pp_theme' );

		// result not cached, perform the query
		if ( !isset( $cache[ $key ] ) ) {
			$archiveResults = $wpdb->get_results( $query );
			$cache[ $key ] = $archiveResults;
			wp_cache_add( 'archive_dropdown_menu', $cache, 'pp_theme' );

		// results are cached retrieve them
		} else {
			$archiveResults = $cache[ $key ];
		}

		if ( !$archiveResults ) {
			return;
		}

		// First pass: loop through the months and get all the unique years
		$years = array();
		foreach( $archiveResults as $archiveResult ) {
			if ( !in_array( $archiveResult->year, $years ) )
				$years[] = $archiveResult->year;
		}

		// initialize some variables
		$currentyear = 0;
		$nest        = false; // we're nesting
		$nested      = false; // we're not in the middle of a nested list
		$count       = 0;     // counter

		$output = '';
		foreach ( (array) $archiveResults as $archiveResult ) {
			$count++;
			$year = $archiveResult->year;
			$month = $archiveResult->month;
			// Need to nest sub level?
			if ( $nest && ( $currentyear != $year ) ) {
				$currentyear = $year;
				if ( $nested )  {
					$output .= "\t</ul>\n</li>\n";
				}
				$output .= "<li class='pp_archives_parent has-children'><a href='".trim( get_year_link( $year ) ) . "'>$year</a><ul class='pp_archives_nested'>\n";
				$nested = true;
			}
			$url = get_month_link( $year, $month );
			$text = sprintf(__('%1$s %2$d'), $wp_locale->get_month( $month ), $year );
			$output .= '<li>' . trim( get_archives_link( $url, $text, 'text' ) ) . "</li>\n";
			if ( $count >= $this->archivesNestThreshold ) {
				$nest = true;
			}
		}

		if ( $nested ) {
			$output .= "\n</ul></li>\n";
		}

		return $output;
	}


	protected function mobileChildren() {
		global $wpdb, $wp_locale;

		$query =
			"SELECT
				YEAR( post_date )  AS `year`,
			 	MONTH( post_date ) AS `month`
			 FROM $wpdb->posts
			 WHERE
				post_type   = 'post' AND
				post_status = 'publish'
			 GROUP BY YEAR( post_date ), MONTH( post_date )
			 ORDER BY post_date DESC";

		$key = md5( $query );
		$cache = wp_cache_get( 'wp_get_archives' , 'general' );
		if ( !isset( $cache[ $key ] ) ) {
			$archives = $wpdb->get_results( $query );
			$cache[ $key ] = $archives;
			wp_cache_set( 'wp_get_archives', $cache, 'general' );
		} else {
			$archives = $cache[ $key ];
		}

		$children = array();
		if ( $archives ) {
			foreach ( (array) $archives as $archive ) {
				$url = get_month_link( $archive->year, $archive->month );
				$text = sprintf(__('%1$s %2$d'), $wp_locale->get_month( $archive->month ), $archive->year );
				$children[$text] = $url;
			}
		}
		return $children;
	}


	protected function __construct( $ID, $itemData, $children ) {
		if ( isset( $itemData->archivesNestThreshold ) ) {
			$this->archivesNestThreshold = $itemData->archivesNestThreshold;
		}
		if ( $this->isInWidgetMenu( $ID ) ) {
			$this->archivesNestThreshold = 9999;
		}
		parent::__construct( $ID, $itemData, $children );
	}
}

