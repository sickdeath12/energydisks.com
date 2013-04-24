<?php

class ppGrid_Categories extends ppGrid {


	protected $rows = 50;
	protected $excludedCategoryIDs = array();
	protected $categories;


	public function __construct( $ID, $data ) {
		if ( isset( $data->excluded_categories )  ) {
			$this->excludedCategoryIDs = array_map( 'intval', explode( '|', $data->excluded_categories) );
		}
		parent::__construct( $ID, $data );
	}


	protected function loadGridItems() {
		$this->categories = ppCategory::getAll();
		foreach ( (array) $this->categories as $category ) {
			if ( !in_array( $category->id(), $this->excludedCategoryIDs ) ) {
				$this->gridItems[] = new ppGridItem( $category );
			}
		}
	}


	protected function updateType( $updateArray, $updatedVals ) {
		return $updateArray;
	}

}

