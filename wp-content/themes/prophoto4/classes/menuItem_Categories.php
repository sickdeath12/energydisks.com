<?php


class ppMenuItem_Categories extends ppMenuItem_Internal {


	protected $hasOwnChildren = true;


	public function children() {
		return wp_list_categories( array( 'echo' => 0, 'title_li' => false ) );
	}

	public function mobileChildren() {
		$categories = ppCategory::getAll();
		$children = array();
		foreach ( (array) $categories as $category ) {
			$children[$category->name()] = $category->url();
		}
		return $children;
	}
}

