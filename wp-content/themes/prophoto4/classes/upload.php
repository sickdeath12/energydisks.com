<?php

abstract class ppUpload {

	const FILE_ID = 'async-upload';
	protected $file;
	protected $moveResult;
	protected $uploadErrorMsg;
	protected $uploadSuccess = false;
	protected $acceptableTypes;
	protected static $destinationDir;


	public function success() {
		return $this->uploadSuccess;
	}


	public function errorMsg() {
		return $this->uploadErrorMsg;
	}


	protected function moveUploadedFile() {

		self::setupDestination( $this->uploadDestinationDir );

		$overrides = array(

			// our custom function to rename the uploaded imgs
			'unique_filename_callback' => $this->fileRenamer(),

			// acceptable image mime-types
			'mimes' => $this->acceptableTypes,

			// don't check for $_POST['action'] to validate form
			'test_form' => false,
		);

		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$this->moveResult = wp_handle_upload( $this->file, $overrides );

		if ( !isset( $this->moveResult['error'] ) ) {
			@chmod( $this->moveResult['file'], 0755 );
			$this->uploadSuccess = true;

		} else {
			$this->uploadErrorMsg = $this->moveResult['error'];
		}
	}


	protected function fileRenamer() {
		return false;
	}


	public static function setupDestination( $input ) {
		if ( !is_array( $input ) && !has_filter( 'upload_dir', 'ppUpload::setupDestination' ) ) {
			self::$destinationDir = $input;
			add_filter( 'upload_dir', 'ppUpload::setupDestination' );

		} else {
			$dirData = $input;
			if ( self::$destinationDir == 'images' ) {
				$dirData['path']   = pp::fileInfo()->imagesFolderPath;
				$dirData['url']    = pp::fileInfo()->imagesFolderUrl;
				$dirData['subdir'] = pp::fileInfo()->imagesSubDir;
				$dirData['error']  = pp::fileInfo()->folderError;
			} else if ( self::$destinationDir == 'fonts' ) {
				$dirData['path']   = pp::fileInfo()->fontsFolderPath;
				$dirData['url']    = pp::fileInfo()->fontsFolderUrl;
				$dirData['subdir'] = pp::fileInfo()->fontsSubDir;
				$dirData['error']  = pp::fileInfo()->folderError;
			} else if ( self::$destinationDir == 'music' ) {
				$dirData['path']   = pp::fileInfo()->musicFolderPath;
				$dirData['url']    = pp::fileInfo()->musicFolderUrl;
				$dirData['subdir'] = pp::fileInfo()->musicSubDir;
				$dirData['error']  = pp::fileInfo()->folderError;
			}
			return $dirData;
		}
	}


	protected function validateFiles( $files ) {
		if ( !NrUtil::isAssoc( $files ) || !isset( $files[self::FILE_ID] ) ) {
			$this->uploadErrorMsg = '$files input must be associative array with index "' . self::FILE_ID . '".';
			new ppIssue( $this->uploadErrorMsg );
			return false;

		} else if ( intval( $files[self::FILE_ID]['error'] ) !== UPLOAD_ERR_OK ) {
			$this->uploadErrorMsg = $this->errorMsgString( $files[self::FILE_ID]['error'] );
			new ppIssue( $this->uploadErrorMsg );
			return false;

		} else {
			return true;
		}
	}


	protected function errorMsgString( $errCode ) {
		switch ( $errCode ) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return ppString::id( 'upload_error_ini_size' );
			case UPLOAD_ERR_PARTIAL:
				return 'File was only partially uploaded, please try again.';
				break;
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded, please try again.';
			case UPLOAD_ERR_NO_TMP_DIR:
				return ppString::id( 'missing_php_temp_dir' );
			case UPLOAD_ERR_CANT_WRITE:
				return ppString::id( 'php_disk_write_error' );
			case UPLOAD_ERR_EXTENSION:
				return 'Upload failure caused by unknown PHP extension.';
			default:
				new ppIssue( 'Unknown upload error code' );
				return 'An unknown upload error occurred.';
		}
	}
}

