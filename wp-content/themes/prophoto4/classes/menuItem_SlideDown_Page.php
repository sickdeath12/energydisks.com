<?php

class ppMenuItem_SlideDown_Page extends ppMenuItem_SlideDown {


	protected $classes = array( 'ajax-fetch-slidedown' );
	protected $pageID;


	public function url() {
		return pp::site()->url . '/?page_id=' . $this->pageID;
	}


	protected function __construct( $ID, $itemData, $children ) {
		parent::__construct( $ID, $itemData, $children );
		if ( isset( $itemData->pageID ) ) {
			$this->pageID = $itemData->pageID;
		}
	}
}

