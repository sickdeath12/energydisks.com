<?php


class ppUploadFontZip extends ppUploadZip {


	protected $fontID;
	protected $fontName;
	protected $fontSlug;
	protected $uploadDestinationDir = 'fonts';
	protected $processSuccess;
	protected $successMsg;
	protected $uploadedZip;


	public function __construct( $fontID, $files ) {
		$this->fontID = $fontID;
		parent::__construct( $files );
	}


	public function id() {
		return $this->fontID;
	}


	public function name() {
		return $this->fontName;
	}


	public function slug() {
		return $this->fontSlug;
	}


	public function process() {
		$this->uploadedZip = new ppZip( $this->uploadedZipPath() );
		$this->uploadedZip->extract( pp::fileInfo()->fontsFolderPath );

		if ( $this->isWebFontKitZip() ) {
			$this->processSuccess = true;
			if ( $this->registerUploadedFont() ) {
				$this->moveFiles();
				$this->successMsg = 'Font <code>' . $this->fontName . '</code> uploaded successfully.';
			} else {

			}
			$this->cleanupGoodZip();

		} else {
			new ppIssue( 'Non-webfont zip file uploaded as font' );
			$this->uploadErrorMsg = ppString::id( 'non_webfont_kit_uploaded' );
			$this->cleanupBadZip();
		}
	}


	public function processSuccess() {
		return (bool) $this->processSuccess;
	}


	public function successMsg() {
		return $this->successMsg;
	}


	protected function registerUploadedFont() {
		if ( $styleRules = $this->loadStyleRules() ) {

			preg_match( "/font-family:(?: +)?'([^']+)';/", $styleRules, $matches );
			if ( isset( $matches[1] ) ) {
				$this->fontName = preg_replace( '/[^A-Za-z0-9_-]/', '', $matches[1] );
			}

			preg_match( "/src:(?: +)?url\('([^\.]+).eot'\);/", $styleRules, $matches );
			if ( isset( $matches[1] ) ) {
				$this->fontSlug = str_replace( '-webfont', '', $matches[1] );
			}

			if ( $this->fontName && $this->fontSlug ) {
				ppOpt::update( $this->fontID, json_encode( array( 'slug' => $this->fontSlug, 'name' => $this->fontName ) ) );
				return true;
			}
		}

		new ppIssue( 'Unable to find font data for uploaded font' );
		return false;
	}


	protected function moveFiles() {
		foreach ( $this->uploadedZip->extractedFiles() as $extracted ) {
			if ( preg_match( '/^' . $this->fontSlug . '-webfont./', basename( $extracted['filename'] ) ) ) {
				if ( basename( $extracted['filename'] ) != $extracted['stored_filename'] ) {
					ppUtil::moveFile( $extracted['filename'], pp::fileInfo()->fontsFolderPath . '/' . basename( $extracted['filename'] ) );
				}
			}
		}
	}


	protected function loadStyleRules() {
		foreach ( $this->uploadedZip->extractedFiles() as $extracted ) {
			if ( basename( $extracted['filename'] ) == 'stylesheet.css' ) {
				return file_get_contents( $extracted['filename'] );
			}
		}
		return false;
	}


	protected function isWebFontKitZip() {
		$exts  = array_map( create_function( '$file', 'return NrUtil::fileExt( $file["stored_filename"] );' ), $this->uploadedZip->extractedFiles() );
		foreach ( ppFontUtil::fontFileExts() as $ext ) {
			if ( !in_array( $ext, $exts ) ) {
				return false;
			}
		}
		return in_array( 'css', $exts );
	}


	protected function cleanupBadZip() {
		$extractedFiles = $this->uploadedZip->extractedFiles();

		foreach ( (array) $extractedFiles as $extracted ) {
			@unlink( $extracted['filename'] );
		}

		$this->removeDirs( $extractedFiles );
		@unlink( $this->uploadedZipPath() );
	}


	protected function cleanupGoodZip() {
		$extractedFiles = $this->uploadedZip->extractedFiles();

		foreach ( (array) $extractedFiles as $extracted ) {

			if ( !in_array( NrUtil::fileExt( $extracted['filename'] ), ppFontUtil::fontFileExts() ) ) {
				@unlink( $extracted['filename'] );

			} else if ( $this->fontSlug ) {

				if ( NrUtil::isIn( '-webfont-webfont', $extracted['filename'] ) ) {
					$fixedFilePath = str_replace( array( '-webfont-webfont-webfont', '-webfont-webfont' ), '-webfont', $extracted['filename'] );
					ppUtil::moveFile( $extracted['filename'], $fixedFilePath );
				}

				if ( !NrUtil::startsWith( $extracted['filename'], pp::fileInfo()->fontsFolderPath . '/' . $this->fontSlug . '-webfont.' ) ) {
					@unlink( $extracted['filename'] );
				}

			} else {
				@unlink( $extracted['filename'] );
			}
		}

		$this->removeDirs( $extractedFiles );

		@unlink( $this->uploadedZipPath() );
	}


	protected function removeDirs( $extractedFiles ) {

		foreach ( (array) $extractedFiles as $extracted ) {
		if ( @is_dir( $extracted['filename'] ) ) {
				@chmod( $extracted['filename'], 0777 );
				@rmdir( $extracted['filename'] );
			}
		}

		foreach ( (array) $extractedFiles as $extracted ) {
			if ( @is_dir( $extracted['filename'] ) ) {
				@chmod( $extracted['filename'], 0777 );
				@rmdir( $extracted['filename'] );
			}
		}

		foreach ( (array) $extractedFiles as $extracted ) {
			if ( @is_dir( $extracted['filename'] ) ) {
				@chmod( $extracted['filename'], 0777 );
				@rmdir( $extracted['filename'] );
			}
		}

		@chmod( pp::fileInfo()->fontsFolderPath . '/specimen_files/', 0777 );
		@rmdir( pp::fileInfo()->fontsFolderPath . '/specimen_files/' );
	}
}

