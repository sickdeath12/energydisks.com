<?php

class ppMenuItem_Search extends ppMenuItem_Special {


	const DEFAULT_BTN_TXT = 'Search';
	protected $searchBtnText;
	protected $hasOwnChildren = true;


	public function aTag() {
		if ( $this->specialType == 'inline_search' ) {
			return pp::browser()->isMobile ? $this->mobileForm() : $this->form();
		} else {
			return parent::aTag();
		}
	}


	public function children() {
		if ( $this->specialType == 'inline_search' ) {
			return null;
		} else {
			return $this->form();
		}
	}


	public function text() {
		if ( $this->specialType == 'inline_search' ) {
			return $this->searchBtnText;
		} else {
			return parent::text();
		}
	}


	protected function form() {
		return NrHtml::group( array(
			NrHtml::openForm( pp::site()->url, null, 'get' ),
				NrHtml::textInput( 's', '', 12 ),
				NrHtml::submit( $this->searchBtnText ),
			NrHtml::closeForm(),
		) );
	}


	protected function mobileForm() {
		return NrHtml::group( array(
			NrHtml::openForm( pp::site()->url, 'class=mobile-search', 'get' ),
				NrHtml::searchInput( 's', $this->searchBtnText ),
			NrHtml::closeForm(),
		) );
	}


	protected function __construct( $ID, $itemData, $children ) {

		$this->searchBtnText = isset( $itemData->searchBtnText ) ? $itemData->searchBtnText : self::DEFAULT_BTN_TXT;

		if ( $itemData->specialType == 'inline_search' ) {
			$itemData->anchor = 'text';
		}

		parent::__construct( $ID, $itemData, $children );

		if ( !$this->text ) {
			$this->text = $this->searchBtnText;
		}

		if ( $this->specialType == 'inline_search' ) {
			$this->classes[] = 'mi-search-inline';
		} else {
			$this->classes[] = 'mi-search-dropdown';
		}

	}
}