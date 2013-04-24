<?php

class ppAudioFile extends ppImg {


	public $number;
	public $songName;


	public static function id( $audioFileID ) {
		$audioFileID = self::validateId( $audioFileID );

		if ( array_key_exists( $audioFileID, self::$cache ) ) {
			return self::$cache[$audioFileID];

		} else {
			return new ppAudioFile( $audioFileID );
		}
	}


	protected function __construct( $audioFileID ) {

		$this->id = $audioFileID;
		$this->number = preg_replace( '/^audio/', '', $this->id );

		if ( isset( self::$customImgs[$this->id] ) ) {
			$this->filename = self::$customImgs[$this->id];
		} else {
			$this->filename = '';
		}

		$this->exists = (bool) $this->filename;
		$this->songName = trim( ppOpt::id( $audioFileID . '_filename' ) ) ? ppOpt::id( $audioFileID . '_filename' ) : $this->filename;
		$this->getPath();
		$this->getExt();
		$this->getUrl();
		$this->fileSize = number_format( ( @filesize( $this->path ) ) / 1024 );
		self::cache( $this->id, $this );
	}



	protected function getPath() {
		$imagesDirLoc = self::$uploadImgDir . $this->filename;
		$musicDirLoc  = pp::fileInfo()->musicFolderPath . '/' . $this->filename;
		if ( @file_exists( $musicDirLoc ) ) {
			$this->path = $musicDirLoc;
		} else if ( @file_exists( $imagesDirLoc ) ) {
			$this->path = $imagesDirLoc;
		} else {
			new ppIssue( "Audio w/ filename $this->filename not found in music dir or legacy (p3) images dir in ppAudioFile::getPath()" );
			$this->filename = '';
			$this->exists = false;
			return;
		}
		$this->path = str_replace( '\\', '/', $this->path );
	}
}


