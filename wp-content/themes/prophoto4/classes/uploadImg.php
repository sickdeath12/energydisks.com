<?php

class ppUploadImg extends ppUpload {


	protected $imgId;
	protected $wrongFileTypeMsg;
	protected $uploadDestinationDir = 'images';
	protected $acceptableTypes = array(
		'jpg'   => 'image/jpeg',
		'jpeg'  => 'image/jpeg',
		'gif'   => 'image/gif',
		'png'   => 'image/png',
		// M$ proprietary formats:
		'pjpeg' => 'image/pjpeg',
		'xpng'  => 'image/x-png',
	);


	public function __construct( $imgId, $files ) {

		$this->wrongFileTypeMsg = ppString::id( 'only_img_types_allowed' );

		$this->imgId = $this->validateImgId( $imgId );
		$this->file  = $this->validateFiles( $files );

		if ( !$this->imgId || !$this->file ) {
			return;
		}

		$this->moveUploadedFile();

		if ( $this->uploadSuccess ) {
			if ( @in_array( $mime = NrUtil::imgData( $this->moveResult['file'] )->mime, array( 'image/psd' ) ) ) {
				$this->uploadSuccess = false;
				$this->uploadErrorMsg = "Image type <code>$mime</code> not permitted.";
			} else {
				ppImg::update( $this->imgId, basename( $this->moveResult['file'] ) );
				if ( $this->imgId == 'facebook_static_front_page' || $this->imgId == 'fb_home' ) {
					add_action( 'shutdown', ppUtil::func( "ppFacebook::refreshNonArticleOGCache_onImgUpdate( '$this->imgId' );" ), 11 );
				}
			}
		}
	}


	public function id() {
		return $this->imgId;
	}


	public function fileRenamer() {
		return create_function( '$dir,$name,$ext', 'return "' . $this->imgId . '_' . time() . '"  . $ext;' );
	}


	private function validateImgId( $imgId ) {
		if ( !is_string( $imgId ) || empty( $imgId ) ) {
			$this->uploadErrorMsg = "Invalid \$imgId " . NrUtil::getVarDump( $imgId );
			new ppIssue( $this->uploadErrorMsg );
			return false;

		} else {
			return $imgId;
		}
	}


	protected function validateFiles( $files ) {
		if ( !parent::validateFiles( $files ) ) {
			return false;

		} else if ( !in_array( $files[self::FILE_ID]['type'], $this->acceptableTypes ) ) {
			$this->uploadErrorMsg = $this->wrongFileTypeMsg();
			return false;

		} else {
			return $files[self::FILE_ID];
		}
	}


	protected function wrongFileTypeMsg() {
		return ppString::id( 'only_img_types_allowed' );
	}
}

