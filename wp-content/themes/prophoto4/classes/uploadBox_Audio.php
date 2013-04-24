<?php

class ppUploadBox_Audio extends ppUploadBox_Img {


	protected $audioFileNum;
	protected $optID;
	protected $uploadBtnLabel  = 'Upload MP3';
	protected $replaceBtnLabel = 'Replace MP3';
	protected $deleteBtnLabel  = 'Delete MP3';



	public function leftBtmOptions() {
		$added = new ppOptionBox( $this->optID . '_filename', 'text|30', "audio file #{$this->audioFileNum} song name" );
		return NrHtml::div( $added->option_markup, 'class=below-options only-if-file sc' );
	}


	public function fileStatsBox() {
		$this->renderView( 'upload_box_file_data' );
	}


	public function file() {
		if ( $this->file == null ) {
			$this->file = ppAudioFile::id( $this->id() );
		}
		return $this->file;
	}


	public function uploadBtnHref() {
		return $this->scrubHrefs( parent::uploadBtnHref() );
	}


	public function deleteBtnHref() {
		return $this->scrubHrefs( parent::deleteBtnHref() );
	}


	public function fileDisplay() {
		return NrHtml::a( $this->file()->url, $this->file()->songName, 'class=uploaded-file' );
	}


	public function __construct( $num ) {
		parent::__construct( 'audio' . $num, 'MP3 audio file #' . $num, '' );
		$this->optID = "audio{$num}";
		$this->audioFileNum = $num;
		$this->classes[] = 'upload-audio';
	}


	protected function scrubHrefs( $in ) {
		return str_replace( 'upload_type=img', 'upload_type=audio_file', $in );
	}


	protected function setup() {}
}

