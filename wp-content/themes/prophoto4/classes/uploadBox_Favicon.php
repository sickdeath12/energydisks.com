<?php


class ppUploadBox_Favicon extends ppUploadBox {


	protected $uploadBtnLabel  = 'Upload Favicon';
	protected $replaceBtnLabel = 'Replace Favicon';
	protected $deleteBtnLabel  = 'Delete Favicon';


	public function __construct() {
		parent::__construct( 'favicon', 'Favicon', ppString::id( 'blurb_favicon' ) );
	}


	public function fileStatsBox() {
		$this->renderView( 'upload_box_file_data' );
	}


	public function fileDisplay() {
		return NrHtml::img( $this->file()->url ) . NrHtml::a( $this->file()->url, $this->file()->filename, 'class=uploaded-file' );
	}


	protected function setup() {
		$this->classes[] = 'file';
		$this->classes[] = 'upload-favicon';
	}

}

