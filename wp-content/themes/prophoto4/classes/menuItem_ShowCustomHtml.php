<?php

class ppMenuItem_ShowCustomHtml extends ppMenuItem_Special {


	protected $customHTML;
	protected $classes = array( 'ajax-fetch-slidedown' );


	public function url() {
		return pp::site()->url . '/?pp_menu_ajax_fetch_custom_html=' . $this->id();
	}


	public function markup() {
		if ( empty( $this->customHTML ) ) {
			new ppIssue( "Attempt to print menu markup for type 'slideDownCustomHTML' with no custom text/html" );
			return '';
		} else {
			return parent::markup();
		}
	}


	protected function __construct( $ID, $itemData, $children ) {
		if ( isset( $itemData->customHTML ) ) {
			$this->customHTML = stripslashes( $itemData->customHTML );
		}
		parent::__construct( $ID, $itemData, $children );
	}
}

