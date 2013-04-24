<?php

class ppExif {


	private $imgPath;
	private $fileName;
	private $date;
	private $height;
	private $width;
	private $isColor;
	private $fStop;
	private $cameraMake;
	private $cameraModel;
	private $shutterSpeed;
	private $iSO;
	private $meteringMode;
	private $focalLength;
	private $fullExif;


	static public function data( $path ) {
		if ( !is_string( $path ) ) {
			new ppIssue( 'non-string input passed' );
			return false;
		} else if ( !NrUtil::isWebSafeImg( $path ) ) {
			new ppIssue( "not a valid image file at: $path" );
			return false;
		} else if ( !@file_exists( $path ) ) {
			new ppIssue( "no file at: $path" );
			return false;
		} else {
			$exif = new ppExif( $path );
			return $exif;
		}
	}


	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		}
		new ppIssue( "invalid Exif property '$name'" );
		return '';
	}


	protected function __construct( $path ) {
		$this->imgPath = $path;
		$this->fullExif = @exif_read_data( $path );
		if ( isset( $this->fullExif['Make'] ) ) {
			$this->fileName 	= $this->fullExif['FileName'];
			$this->date 		= $this->fullExif['FileDateTime'];
			$this->height 		= $this->fullExif['COMPUTED']['Height'];
			$this->width 		= $this->fullExif['COMPUTED']['Width'];
			$this->isColor 		= $this->fullExif['COMPUTED']['IsColor'];
			$this->fStop 		= $this->fullExif['COMPUTED']['ApertureFNumber'];
			$this->cameraMake 	= $this->fullExif['Make'];
			$this->cameraModel  = $this->fullExif['Model'];
			$this->shutterspeed = $this->fullExif['ExposureTime'];
			$this->iSO 			= $this->fullExif['ISOSpeedRatings'];
			$this->meteringMode = $this->fullExif['MeteringMode'];
			$this->focalLength  = $this->fullExif['FocalLength'];
		}
	}

}
