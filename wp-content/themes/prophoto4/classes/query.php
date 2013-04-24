<?php


class ppQuery {


	protected static $instance;


	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new ppQuery();
		}
		return self::$instance;
	}


	public function isBlogPostsPage() {
		return is_home();
	}


	public function isStaticFrontPage() {
		return ( pp::site()->hasStaticFrontPage && is_front_page() );
	}


	public function isArticle() {
		return is_singular();
	}


	public function isPost() {
		return is_single();
	}


	public function isPage() {
		return is_page();
	}


	public function isGalleryQuasiPage() {
		return ppGallery::isGalleryQuasiPage();
	}


	public function is404() {
		return is_404();
	}


	public function isFeed() {
		return is_feed();
	}


	public function isSearch() {
		return is_search();
	}


	public function searchedFor() {
		return get_query_var( 's' );
	}


	public function isEmptySearch() {
		return ppUtil::isEmptySearch();
	}


	public function isArchive() {
		return is_archive();
	}


	public function isAuthorArchive() {
		return is_author();
	}


	public function isTagArchive() {
		return is_tag();
	}


	public function isCategoryArchive() {
		return is_category();
	}


	public function isDateArchive() {
		return is_date();
	}


	public function isYearArchive() {
		return is_year();
	}


	public function isMonthArchive() {
		return is_month();
	}


	public function isDayArchive() {
		return is_day();
	}


	public function isAdmin() {
		return is_admin();
	}


	public function isPaged() {
		 return is_paged();
	}


	public function pagedNumber() {
		return get_query_var( 'paged' );
	}


	public function typeStringGeneral() {
		return $this->typeString( false );
	}


	public function typeStringSpecific() {
		return $this->typeString( true );
	}


	private function typeString( $returnArchiveType ) {
		if ( $this->isStaticFrontPage() )
			return 'front_page';

		if ( $this->isBlogPostsPage() )
			return 'home';

		if ( $this->isPost() )
			return 'single';

		if ( $this->isPage() )
			return 'page';

		if ( $returnArchiveType ) {

			if ( $this->isCategoryArchive() )
				return 'category';

			if ( $this->isTagArchive() )
				return 'tag';

			if ( $this->isSearch() )
				return 'search';

			if ( $this->isAuthorArchive() )
				return 'author';
		}

		/* technically search is not an archive, but we consider it in the archive-type bucket */
		if ( $this->isArchive() || $this->isSearch() )
			return 'archive';

		if ( $this->isAdmin() )
			return 'admin';

		if ( $this->is404() )
			return '404';

		if ( $this->isFeed() )
			return 'feed';

		return 'unknown_page_type';
	}


	public function data() {
		$dataArray = array(
			'true'  => array(),
			'false' => array(),
			'type_string_general'  => $this->typeStringGeneral(),
			'type_string_specific' => $this->typeStringSpecific(),
			'paged_number' => $this->pagedNumber(),
			'searched_for' => $this->searchedFor(),
		);
		$methods = get_class_methods( $this );
		foreach ( $methods as $method ) {
			if ( NrUtil::startsWith( $method, 'is' ) ) {
				if ( (bool) $this->$method() ) {
					$dataArray['true'][] = $method;
				} else {
					$dataArray['false'][] = $method;
				}
			}
		}
		return $dataArray;
	}
}

