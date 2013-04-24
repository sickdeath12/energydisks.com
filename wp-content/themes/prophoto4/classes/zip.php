<?php

/* facade class for working with PclZips */

class ppZip {


	private static $workingDir;
	private $zipName;
	private $zipPath;
	private $errors = array();
	private $firstFile;
	private $files;
	private $createResult;
	private $extractResult;
	private $addResults = array();
	private $pclZip;


	public function __construct() {

		// zipping something
		if ( func_num_args() === 2 ) {
			list( $zipName, $fileArray ) = func_get_args();

			if ( !is_string( $zipName ) || strtolower( NrUtil::fileExt( $zipName ) ) !== 'zip' ) {
				$this->logError( 'Invalid $zipName input data' );
				return;
			}

			if ( !is_array( $fileArray ) ) {
				$this->logError( '$fileArray must be array' );
				return;
			}

			$this->firstFile = array_shift( $fileArray );
			$this->files = $fileArray;

			$this->zipName = $zipName;
			$this->zipPath = self::$workingDir . $this->zipName;

			$this->pclZip = new PclZip( $this->zipPath );
			unset( $this->extractResult );

		// un-zipping something
		} else if ( func_num_args() === 1 ) {
			$uploadedZipPath = current( func_get_args() );

			if ( !is_string( $uploadedZipPath ) || NrUtil::fileExt( $uploadedZipPath ) !== 'zip' ) {
				$this->logError( 'Invalid $uploadedZip input data' );
				return;
			}

			if ( !@file_exists( $uploadedZipPath ) ) {
				$this->logError( "No file found at specified path: '$uploadedZipPath'" );
				return;
			}

			$this->zipPath = $uploadedZipPath;
			$this->zipName = basename( $this->zipPath );
			$this->pclZip = new PclZip( $this->zipPath );

			unset( $this->firstFile );
			unset( $this->files );
			unset( $this->createResult );
			unset( $this->addResults );

		// should never happen
		} else {
			$this->logError( "Incorrect num args passed to constructor" );
		}
	}


	public function extract( $extractDir = null ) {
		$extractTo = is_null( $extractDir ) ? self::$workingDir : $extractDir;
		$this->extractResult = $this->pclZip->extract( $extractTo );
		if ( is_array( $this->extractResult ) ) {
			foreach ( $this->extractResult as $extracted ) {
				if ( NrUtil::fileExt( $extracted['filename'] ) == 'zip' ) {
					$subZip = new ppZip( $extracted['filename'] );
					$subZipExtractedFiles = $subZip->pclZip->extract( $extractTo );
					if ( is_array( $subZipExtractedFiles ) ) {
						foreach ( $subZipExtractedFiles as $subZipExtractedFile ) {
							$this->extractResult[] = $subZipExtractedFile;
						}
					}
				}
			}
		}
	}


	public function create() {
		if ( @file_exists( $this->zipPath ) ) {
			@unlink( $this->zipPath );
		}

		if ( @file_exists( $this->firstFile ) ) {
			$this->createResult = current( $this->pclZip->create( $this->firstFile, PCLZIP_OPT_REMOVE_PATH, dirname( $this->firstFile ) ) );
		} else {
			$this->logError( "First file '{$this->firstFile}' for creation of zip not found" );
			return;
		}

		foreach ( $this->files as $file ) {
			if ( @file_exists( $file ) ) {
				$this->addResults[] = current( $this->pclZip->add( $file, PCLZIP_OPT_REMOVE_PATH, dirname( $file ) ) );
			} else {
				new ppIssue( "File '$file' was not added to zip because it was not found" );
			}
		}
	}

	public function zipUrl() {
		return ppUtil::urlFromPath( $this->zipPath );
	}


	public function zipPath() {
		return $this->zipPath;
	}


	public function zipName() {
		return $this->zipName;
	}


	public function dataFilePath() {
		if ( !$this->extractResult ) {
			$this->extract();
		}
		foreach ( $this->extractResult as $extracted ) {
			$filePath = $extracted['filename'];
			if ( NrUtil::isIn( '/design_data_', $filePath ) && 'txt' === NrUtil::fileExt( $filePath ) ) {
				return $filePath;
			} if ( NrUtil::isIn( '/export_', $filePath ) && 'txt' === NrUtil::fileExt( $filePath ) ) {
				return $filePath;
			}
		}
		new ppIssue( 'No data file found inside zip' );
		return false;
	}


	public function extractedfiles() {
		if ( !$this->extractResult ) {
			$this->extract();
		}
		return $this->extractResult ? $this->extractResult : array();
	}


	public function errors() {
		return $this->errors;
	}


	public function errorMsg() {
		$errorCount = count( $this->errors );
		if ( 0 === $errorCount ) {
			return 'No errors logged.';
		} else if ( 1 === $errorCount ) {
			return 'The following error occurred: ' . current( $this->errors ) . '.';
		} else {
			return 'The following ' . $errorCount . ' errors occurred: ' . implode( '. ', $this->errors );
		}
	}


	public static function workingDir( $set = null ) {
		if ( $set == null ) {
			return self::$workingDir;
		} else {
			self::$workingDir = $set;
		}
	}


	private function logError( $msg ) {
		new ppIssue( $msg );
		$this->errors[] = $msg;
	}


	public static function _onClassLoad() {
		self::$workingDir = trailingslashit( pp::fileInfo()->designsFolderPath );
		if ( !class_exists( 'PclZip' ) ) {
			define( 'PCLZIP_TEMPORARY_DIR', self::$workingDir );
			require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
		}
	}
}
