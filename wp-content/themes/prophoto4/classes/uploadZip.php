<?php

abstract class ppUploadZip extends ppUpload {


	protected $acceptableTypes = array( 'zip' => 'application/zip' );


	public function __construct( $files ) {

		$this->file = $this->validateFiles( $files );

		if ( $this->file ) {
			$this->moveUploadedFile();
		}
	}


	public function uploadedZipPath() {
		if ( isset( $this->moveResult['file'] ) ) {
			return $this->moveResult['file'];
		} else {
			return false;
		}
	}


	protected function validateFiles( $files ) {
		if ( !parent::validateFiles( $files ) ) {
			return false;

		} else if ( NrUtil::fileExt( $files[self::FILE_ID]['name'] ) != 'zip' ) {
			$this->uploadErrorMsg = "Filetype must be '.zip', '." . NrUtil::fileExt( $files[self::FILE_ID]['name'] ) . "' uploaded.";
			return false;

		} else {
			return $files[self::FILE_ID];
		}
	}
}

