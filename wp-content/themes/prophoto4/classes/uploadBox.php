<?php


abstract class ppUploadBox {


	protected $ID;
	protected $name;
	protected $comment;
	protected $file;
	protected $classes = array();


	public static function imgInstance( $ID, $name, $comment = '' ) {
		return new ppUploadBox_Img( $ID, $name, $comment );
	}


	public static function renderImg( $ID, $name, $comment = '' ) {
		$imgUploadBox = new ppUploadBox_Img( $ID, $name, $comment );
		$imgUploadBox->render();
	}


	public static function renderMastheadImg( $ID, $name, $comment = '' ) {
		$imgUploadBox = new ppUploadBox_Img_Masthead( $ID, $name, $comment );
		$imgUploadBox->render();
	}


	public static function renderSwf( $ID, $name, $comment = '' ) {
		$swfUploadBox = new ppUploadBox_Swf( $ID, $name, $comment );
		$swfUploadBox->render();
	}


	public static function renderBg( $ID, $name, $comment = '' ) {
		$bgUploadBox = new ppUploadBox_Img_Bg( $ID, $name, $comment );
		$bgUploadBox->render();
	}


	public static function renderLinkedImg( $ID, $name, $comment = '', $linkComment = '' ) {
		$linkedImgUploadBox = new ppUploadBox_Img_Linked( $ID, $name, $comment, $linkComment );
		$linkedImgUploadBox->render();
	}


	public static function renderImgWithOption( $imgArgs, $optionArgs ) {
		$imgWithOptionUploadBox = new ppUploadBox_Img_WithOption( $imgArgs, $optionArgs );
		$imgWithOptionUploadBox->render();
	}


	public function __construct( $ID, $name, $comment = '' ) {
		$this->ID      = $ID;
		$this->name    = $name;
		$this->comment = $comment;

		if ( $this->hasUploadedFile() ) {
			$this->classes[] = 'has-file';

		} else {
			$this->classes[] = 'no-file';
			$this->classes[] = 'empty';
		}

		$this->setup();
	}


	public function id() {
		return $this->ID;
	}


	public function name() {
		return $this->name;
	}


	public function comment() {
		return $this->comment;
	}


	public function uploadBtnLabel() {
		return $this->uploadBtnLabel;
	}


	public function replaceBtnLabel() {
		return $this->replaceBtnLabel;
	}


	public function deleteBtnLabel() {
		return $this->deleteBtnLabel;
	}


	public function uploadBtnHref() {
		return ppIFrame::url( 'file_upload_form&upload_type=img&file_id=' . $this->id(), '410', '110' );
	}


	public function deleteBtnHref() {
		return ppIFrame::url( 'file_reset_form&upload_type=img&file_id=' . $this->id(), '410', '110' );
	}


	public function commentMarkup() {
		return $this->comment() ? NrHtml::p( $this->comment(), 'class=comment' ) : null;
	}


	public function file() {
		if ( $this->file == null ) {
			$this->file = ppImg::id( $this->id() );
		}
		return $this->file;
	}


	public function filesize() {
		return $this->file()->fileSize;
	}


	public function classes() {
		$classes = array_merge( (array) $this->classes, (array) ppGetInterfaceClasses( $this->id() ) );
		return join( ' ', apply_filters( 'pp_upload_box_classes', $classes, $this ) );
	}


	public function debug() {
		if ( pp::site()->isDev || pp::browser()->isTech ) {
			return '<tt>' . $this->id() . '</tt>';
		}
	}


	public function fileDisplay() {
		return NrHtml::a( $this->file()->url, $this->file()->filename, 'class=uploaded-file' );
	}


	public function render() {
		$this->renderView( 'upload_box' );
	}


	protected function hasUploadedFile() {
		return $this->file()->exists;
	}


	protected function renderView( $file ) {
		ppUtil::renderView( $file, array( 'upload' => $this ) );
	}


	public function aboveOptions()   {}
	public function leftTopOptions() {}
	public function leftBtmOptions() {}
	public function belowOptions()   {}
	public function sizingBox()      {}
	public function fileStatsBox()   {}
	protected function setup()       {}
}

