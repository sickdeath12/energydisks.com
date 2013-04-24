<?php


class ppMenuItem_CallTelephone extends ppMenuItem_Special {


	protected $telephoneNumber;


	public function url() {
		return 'tel:' . $this->telephoneNumber;
	}


	protected function __construct( $ID, $itemData, $children ) {
		$this->telephoneNumber = $itemData->telephoneNumber;
		parent::__construct( $ID, $itemData, $children );
	}

}