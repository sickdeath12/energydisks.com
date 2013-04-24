<?php


class ppUploadBox_Swf extends ppUploadBox {


	protected $uploadBtnLabel  = 'Upload .swf File';
	protected $replaceBtnLabel = 'Replace .swf File';
	protected $deleteBtnLabel  = 'Delete .swf File';


	public function fileStatsBox() {
		$this->renderView( 'upload_box_file_data' );
	}


	protected function setup() {
		$this->classes[] = 'file';
		$this->classes[] = 'upload-swf';
	}

}

