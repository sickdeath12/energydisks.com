<?php


class ppMenuItem_Email extends ppMenuItem_Special {


	protected $email;


	public function url() {
		return 'mailto:' . $this->email;
	}


	public function aTag() {
		return trim( ppHtml::obfuscatedEmailLink( parent::aTag() ) );
	}


	protected function __construct( $ID, $itemData, $children ) {
		$this->email = isset( $itemData->email ) ? $itemData->email : '';
		parent::__construct( $ID, $itemData, $children );
	}
}

