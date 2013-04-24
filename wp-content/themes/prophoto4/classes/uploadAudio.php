<?php


class ppUploadAudio extends ppUploadImg {


	protected $acceptableTypes = array( 'mp3' => 'audio/mp3', 'mp3.*' => 'audio/mpeg3', 'mp3+' => 'audio/mpeg' );
	protected $uploadDestinationDir = 'music';


	public function __construct( $fileID, $files ) {
		parent::__construct( $fileID, $files );
		if ( $this->uploadSuccess ) {
			ppOpt::update( $fileID . '_filename', $this->uploadedFilename() );
		}
	}


	public function uploadedFilename() {
		return stripslashes( preg_replace( '/.mp3$/i', '', $this->file['name'] ) );
	}


	protected function wrongFileTypeMsg() {
		return ppString::id( 'only_mp3_file_allowed' );
	}


	protected function validateFiles( $files ) {
		if ( !parent::validateFiles( $files ) ) {
			return false;

		} else if ( NrUtil::fileExt( $files[self::FILE_ID]['name'] ) != 'mp3' ) {
			$this->uploadErrorMsg = "Filetype must be <code>.mp3</code>, '." . NrUtil::fileExt( $files[self::FILE_ID]['name'] ) . "' uploaded.";
			return false;

		} else {
			return $files[self::FILE_ID];
		}
	}
}

