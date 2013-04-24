<?php


class ppUploadBox_Img_Linked extends ppUploadBox_Img {


	protected $linkComment;


	public function leftBtmOptions() {
		$linkUrl = new ppOptionBox( $this->id() . '_linkurl', 'text|25', $this->linkComment, '' );
		return NrHtml::div( $linkUrl->option_markup, 'class=below-options only-if-file sc' );
	}


	public function __construct( $ID, $name, $comment, $linkComment ) {
		$this->classes[] = 'upload-box-linked-img';
		$this->linkComment = $linkComment ? $linkComment : 'optional URL link for this image';
		parent::__construct( $ID, $name, $comment, $linkComment );
	}
}


