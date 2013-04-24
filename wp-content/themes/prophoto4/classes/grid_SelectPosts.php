<?php


class ppGrid_SelectPosts extends ppGrid {


	protected $postIDs = array();


	public function __construct( $ID, $data ) {
		if ( !empty( $data->postIDs ) ) {
			if ( is_array( $postIDs = explode( ',', $data->postIDs ) ) ) {
				$this->postIDs = array_map( 'intval', $postIDs );
			} else {
				new ppIssue( 'Invalid posts data passed to ppGrid_SelectPosts::__contruct()' );
			}
		}
		parent::__construct( $ID, $data );
	}


	public function postIDs() {
		return $this->postIDs;
	}


	protected function loadGridItems() {
		foreach ( $this->postIDs as $postID ) {
			$this->gridItems[] = new ppGridItem( new ppPost( $postID ) );
		}
		wp_reset_query();
	}

}

