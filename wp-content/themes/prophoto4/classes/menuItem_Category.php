<?php


class ppMenuItem_Category extends ppMenuItem_Internal {


	protected $categoryName;


	public function url() {
		return pp::site()->url . '/?category_name=' . $this->categoryName;
	}


	protected function __construct( $ID, $itemData, $children ) {
		parent::__construct( $ID, $itemData, $children );
		$this->categoryName = $itemData->categoryName;
	}
}

