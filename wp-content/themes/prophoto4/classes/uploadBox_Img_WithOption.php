<?php


class ppUploadBox_Img_WithOption extends ppUploadBox_Img {


	protected $optID;
	protected $optParams;
	protected $optComment;


	public function leftBtmOptions() {
		$added = new ppOptionBox( $this->optID, $this->optParams, $this->optComment );
		return NrHtml::div( $added->option_markup, 'class=below-options only-if-file sc' );
	}


	public function __construct( $imgArgs, $optArgs ) {
		list( $imgID, $imgName, $imgComment )   = $imgArgs;
		parent::__construct( $imgID, $imgName, $imgComment );

		list( $optID, $optParams, $optComment ) = $optArgs;
		$this->optID      = $optID;
		$this->optParams  = $optParams;
		$this->optComment = $optComment;
	}

}

