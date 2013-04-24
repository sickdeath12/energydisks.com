<?php


class ppMenuItem_Page extends ppMenuItem_Internal {


	protected $pageID;
	protected $pageLoadMethod = 'standard';


	public function url() {
		return get_permalink( $this->pageID );
	}


	protected function __construct( $ID, $itemData, $children ) {
		parent::__construct( $ID, $itemData, $children );
		if ( isset( $itemData->pageID ) ) {
			$this->pageID = $itemData->pageID;
		}
		if ( isset( $itemData->pageLoadMethod ) ) {
			$this->pageLoadMethod = $itemData->pageLoadMethod;
			if ( $itemData->pageLoadMethod == 'ajax_slide_down' ) {
				$this->classes[] = 'ajax-fetch-slidedown';
			}
		}
	}
}

