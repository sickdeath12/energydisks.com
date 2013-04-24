<?php

class ppImg {


	const FALLBACK_IMG_FILENAME = 'nodefaultimage.gif';
	public static $initialized    = false;
	public static $hasUpdates     = false;
	protected static $customImgs  = null;
	protected static $defaultImgs = null;
	protected static $themeImgDir;
	protected static $uploadImgDir;
	protected static $cache;
	protected $remoteData;
	public $id;
	public $filename;
	public $exists;
	public $defaultFilename;
	public $url;
	public $path;
	public $hasDefault;
	public $ext;
	public $linkurl;
	public $isRemote;



	public static function id( $imgId ) {
		$imgId = self::validateId( $imgId );

		if ( array_key_exists( $imgId, self::$cache ) ) {
			return self::$cache[$imgId];

		} else {
			return new ppImg( $imgId );
		}
	}


	public static function update( $imgId, $newFilename ) {
		$validatedImgId = self::validateId( $imgId );
		if ( $validatedImgId === 'fallback' && $imgId !== 'fallback' ) {
			return false;
		}
		if ( !self::isValidFilename( $newFilename ) ) {
			new ppIssue( "Invalid \$newFilename $newFilename passed to ppImg::update()" );
			return false;
		}
		self::clearCache( $validatedImgId );
		self::$customImgs[$validatedImgId]  = $newFilename;
		self::$hasUpdates = true;
	}


	public static function updateMultiple( $array ) {
		if ( !is_array( $array ) ) {
			return new ppIssue( 'ppImg::updateMultiple() requires array input' );
		}
		foreach ( $array as $key => $val ) {
			self::update( $key, $val );
		}
	}


	public static function getCustomImgs() {
		self::init();
		return self::$customImgs;
	}


	public static function hasUpdates() {
		self::init();
		return self::$hasUpdates;
	}


	public function imgTag() {
		return new ppImgTag( $this->url, array(
			'width' => $this->width,
			'height' => $this->height,
		) );
	}


	protected static function validateId( $imgId ) {
		self::init();
		if ( !is_string( $imgId ) ) {
			new ppIssue( 'ppImg requires string for $imgId param' );
			return 'fallback';
		} else {
			return $imgId;
		}
	}


	protected function __construct( $imgId ) {

		$this->id = $imgId;

		if ( isset( self::$customImgs[$imgId] ) ) {
			$this->filename = self::$customImgs[$imgId];

		} else if ( isset( self::$defaultImgs[$imgId] ) ) {
			$this->filename = self::$defaultImgs[$imgId];

		} else {
			$this->filename = self::FALLBACK_IMG_FILENAME;
		}

		$this->defaultFilename = ( isset( self::$defaultImgs[$imgId] ) ) ? self::$defaultImgs[$imgId] : '';
		$this->hasDefault = ( !empty( $this->defaultFilename ) );
		$this->exists = ( $this->filename != self::FALLBACK_IMG_FILENAME && $this->filename );

		if ( !$this->exists ) {
			$this->filename = self::FALLBACK_IMG_FILENAME;
		}

		$this->getPath();
		$this->getExt();
		$this->getUrl();
		$this->getInfo();


		if ( $linkurl = ppOpt::id( $this->id . '_linkurl' ) ) {
			$this->linkurl = $linkurl;
		}

		self::cache( $imgId, $this );
	}


	protected function cache( $imgId, $imgInfo ) {
		if ( !array_key_exists( $imgId, self::$cache ) ) {
			self::$cache[$imgId] = $imgInfo;
		}
	}


	protected function clearCache( $imgId ) {
		unset( self::$cache[$imgId] );
	}


	protected function getPath() {
		$themeImgLoc    = self::$themeImgDir . $this->filename;
		$uplaodedImgLoc = self::$uploadImgDir . $this->filename;
		if ( @file_exists( $themeImgLoc ) ) {
			$this->path = $themeImgLoc;
		} else if ( @file_exists( $uplaodedImgLoc ) ) {
			$this->path = $uplaodedImgLoc;
		} else if ( in_array( $this->filename, array_keys( $remoteData = ppRemoteFiles::allFileData() ) ) ) {
			$this->remoteData = (object) $remoteData[$this->filename];
			$this->isRemote = true;
		} else {
			new ppIssue( "Image w/ filename $this->filename not found in theme dir or upload dir in ppImg::getPath()" );
			$this->path = self::$themeImgDir . self::FALLBACK_IMG_FILENAME;
		}
		$this->path = str_replace( '\\', '/', $this->path );
	}


	protected function getUrl() {
		if ( !$this->isRemote ) {
			$this->url = ppUtil::urlFromPath( $this->path );
			if ( !$this->url && pp::fileInfo()->wpUploadUrl == '' ) {
				new ppIssue( 'Forced to guess image url' );
				$this->url = pp::site()->wpurl . '/' . trailingslashit( get_option( 'upload_path' ) ) . 'p4/images/' . $this->filename;
			}
		} else {
			$this->url = EXT_RESOURCE_URL . '/img/' . $this->filename;
		}
	}


	protected function getExt() {
		$this->ext = NrUtil::fileExt( $this->path );
	}


	protected function getInfo() {
		if ( !$this->isRemote ) {
			$imgInfo = @getimagesize( $this->path );
			$this->width    = $imgInfo[0];
			$this->height   = $imgInfo[1];
			$this->htmlAttr = $imgInfo[3];
			$this->fileSize = number_format( ( @filesize( $this->path ) ) / 1024 );
		} else {
			$this->width    = $this->remoteData->width;
			$this->height   = $this->remoteData->height;
			$this->htmlAttr = 'width="' . $this->width .'" height="' . $this->height . '"';
			$this->fileSize = $this->remoteData->size;
		}
	}


	public static function isValidFilename( $filename ) {
		if ( !is_string( $filename ) ) {
			return false;
		}
		if ( $filename == '' ) {
			return true;
		}
		if ( NrUtil::isWebSafeImg( $filename ) ) {
			return true;
		}
		if ( preg_match( '/\.(ico|swf|mp3)$/i', $filename ) ) {
			return true;
		}
		return false;
	}


	protected function init() {
		if ( !self::$initialized ) {
			self::$themeImgDir  = TEMPLATEPATH . '/images/';
			self::$uploadImgDir = pp::fileInfo()->imagesFolderPath . '/';
			self::$defaultImgs  = ppUtil::loadConfig( 'images' );
			self::$customImgs   = ppActiveDesign::imgs();
			self::$cache        = array();
			self::$hasUpdates   = false;
			self::$initialized  = true;
		}
	}

}

