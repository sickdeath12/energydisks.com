<?php


class ppMenuItem_Pages extends ppMenuItem_Internal {


	protected $hasOwnChildren = true;
	protected $excludedPageIDs;


	public function children() {

		$params = array(
			'orderby'  => 'name',
			'echo'     => false,
			'title_li' => false,
		);

		if ( pp::browser()->isIPad || pp::browser()->isIPhone ) {
			$params['depth'] = -1;
		}

		if ( $this->excludedPageIDs ) {
			$params['exclude'] = $this->excludedPageIDs;
		}

		$pages = wp_list_pages( $params );

		// prevent the menu item from being rendered if no pages
		if ( $pages == '' && !is_admin() ) {
			$this->text = '';
		}

		return $pages;
	}


	public function mobileChildren() {
		$pages = get_pages( $this->excludedPageIDs ? array( 'exclude' => $this->excludedPageIDs ) : null );
		$children = array();
		foreach ( $pages as $page ) {
			$children[$page->post_title] = get_permalink( $page->ID );
		}
		return $children;
	}


	protected function __construct( $ID, $itemData, $children ) {
		if ( isset( $itemData->excludedPageIDs ) && is_array( explode( ',', $itemData->excludedPageIDs ) ) ) {
			$this->excludedPageIDs = $itemData->excludedPageIDs;
		}
		parent::__construct( $ID, $itemData, $children );
	}
}

