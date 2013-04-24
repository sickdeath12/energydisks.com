<?php


class ppFolders {

	const CONTAINING_DIR_NAME = '/p4';
	public $wpUploadPath;
	public $wpUploadRelPath;
	public $wpUploadUrl;
	public $folderPath;
	public $p3FolderPath;
	public $folderUrl;
	public $imagesSubDir = '/images';
	public $imagesFolderPath;
	public $imagesFolderUrl;
	public $gallerySubDir = '/gallery';
	public $galleryFolderPath;
	public $galleryFolderUrl;
	public $placeholdersSubDir = '/placeholders';
	public $placeholdersFolderPath;
	public $placeholdersFolderUrl;
	public $staticSubDir = '/static';
	public $staticFolderPath;
	public $staticFolderUrl;
	public $designsSubDir = '/designs';
	public $designsFolderPath;
	public $designsFolderUrl;
	public $fontsSubDir = '/fonts';
	public $fontsFolderPath;
	public $fontsFolderUrl;
	public $musicSubDir = '/music';
	public $musicFolderPath;
	public $musicFolderUrl;
	public $backupSubDir = '/backup';
	public $backupFolderPath;
	public $backupFolderUrl;
	public $issuesSubDir = '/issues';
	public $issuesFolderPath;
	public $issuesFolderUrl;
	public $folderError = false;
	public $okToWrite = false;
	protected $abspath;
	protected $wpContentDir;
	protected $wpUrl;
	protected static $attemptNum = 1;


	/* constructor function, build psuedo-global registry vars, make folders if necessary */
	public function __construct( $abspath = null, $wpContentDir = null, $wpUrl = null ) {
		$this->abspath      = is_null( $abspath )      ? ABSPATH           : $abspath;
		$this->wpContentDir = is_null( $wpContentDir ) ? WP_CONTENT_DIR    : $wpContentDir;
		$this->wpUrl        = is_null( $wpUrl )        ? pp::site()->wpurl : $wpUrl;

		// full filesystem path to WordPress uploads folder
		$this->setWpUploadPath();

		// absolute url to WordPress uploads folder
		$this->wpUploadUrl = self::urlFromPath( $this->wpUploadPath );

		// full filesystem path to P3 folder system
		$this->p3FolderPath = trailingslashit( $this->wpUploadPath ) . 'p3/';

		// full filesystem path to ProPhoto containing folder
		$this->folderPath = $this->wpUploadPath . self::CONTAINING_DIR_NAME;

		// absolute URL to ProPhoto containing folder
		$this->setFolderUrl();

		// create (if necessary) folder stucture
		$this->createFolderStructure();

		// specific properties for each subdir
		$this->imagesFolderPath       = $this->folderPath . $this->imagesSubDir;
		$this->imagesFolderUrl        = $this->folderUrl  . $this->imagesSubDir;
		$this->galleryFolderPath      = $this->folderPath . $this->gallerySubDir;
		$this->galleryFolderUrl       = $this->folderUrl  . $this->gallerySubDir;
		$this->placeholdersFolderPath = $this->folderPath . $this->placeholdersSubDir;
		$this->placeholdersFolderUrl  = $this->folderUrl  . $this->placeholdersSubDir;
		$this->staticFolderPath       = $this->folderPath . $this->staticSubDir;
		$this->staticFolderUrl        = $this->folderUrl  . $this->staticSubDir;
		$this->designsFolderPath      = $this->folderPath . $this->designsSubDir;
		$this->designsFolderUrl       = $this->folderUrl  . $this->designsSubDir;
		$this->fontsFolderPath        = $this->folderPath . $this->fontsSubDir;
		$this->fontsFolderUrl         = $this->folderUrl  . $this->fontsSubDir;
		$this->musicFolderPath        = $this->folderPath . $this->musicSubDir;
		$this->musicFolderUrl         = $this->folderUrl  . $this->musicSubDir;
		$this->backupFolderPath       = $this->folderPath . $this->backupSubDir;
		$this->backupFolderUrl        = $this->folderUrl  . $this->backupSubDir;
		$this->issuesFolderPath       = $this->folderPath . $this->issuesSubDir;
		$this->issuesFolderUrl        = $this->folderUrl  . $this->issuesSubDir;
	}


	protected function setWpUploadPath() {

		// start by reading the upload path from the database
		$uploadPath = ( trim( get_option( 'upload_path' ) ) === '' ) ? 'wp-content/uploads' : get_option( 'upload_path' );

		// if there is no stored upload path in database, create one
		if ( trim( $uploadPath ) === '' ) {
			$uploadPath = $this->wpContentDir . '/uploads';
		}

		// use the server path to the WordPress installation folder plus the
		// upload path to define an absolute server path to prophoto upload folder
		$uploadPath = @path_join( $this->abspath, $uploadPath );

		// allow user-set constant UPLOADS to trump other test
		if ( defined( 'UPLOADS' ) ) {
			$uploadPath = $this->abspath . UPLOADS;
		}

		$this->wpUploadPath    = untrailingslashit( $this->winSanitize( $uploadPath ) );
		$this->wpUploadRelPath = str_replace( $this->abspath, '', $this->wpUploadPath );
	}


	protected function setFolderUrl() {

		$folderUrl = get_option( 'upload_url_path' );

		if ( !$folderUrl || !NrUtil::isIn( 'http://', $folderUrl ) ) {
			$folderUrl = self::urlFromPath( $this->wpUploadPath );
		}

		// allow user-set constant UPLOADS to trump other tests
		if ( defined( 'UPLOADS' ) ) {
			$folderUrl = trailingslashit( pp::site()->wpurl ) . UPLOADS;
		}

		// append subdirectory
		$folderUrl = untrailingslashit( $folderUrl ) .  self::CONTAINING_DIR_NAME;

		// sanitize path for windows installs
		$this->folderUrl = str_replace( '\\', '/', $folderUrl );
	}


	protected function createFolderStructure() {

		// couldn't find or create main prophoto containint folder
		if ( !wp_mkdir_p( $this->folderPath ) ) {
			$this->folderError = ppString::id( 'create_dir_error', $this->folderPath );

		// main ProPhoto containing folder exists/was created, create sub-dirs
		} else {
			$subDirs = array(
				$this->imagesSubDir,
				$this->gallerySubDir,
				$this->staticSubDir,
				$this->designsSubDir,
				$this->fontsSubDir,
				$this->musicSubDir,
				$this->backupSubDir,
				$this->issuesSubDir,
				$this->placeholdersSubDir,
			);
			foreach ( $subDirs as $subDir ) {
				$createDir = $this->folderPath . $subDir;
				if ( !wp_mkdir_p( $createDir ) || !@file_exists( $createDir ) ) {
					$this->folderError = ppString::id( 'create_dir_error', $createDir  );
					break;
				}
			}
		}

		// try to fix errors
		if ( $this->folderError ) {
			if ( self::$attemptNum < 3 ) {
				$this->repairPermissions();
			} else {
				self::reportErrors();
			}
		} else {
			$this->okToWrite = true;
		}
	}


	protected function reportErrors() {
		// because no "uploads" folder
		if ( !file_exists( $this->wpUploadPath ) ) {
			ppAdmin::warn( 'cant_create_folder', pp::tut()->uploadsFolder );

		// uploads exists, but no "p4" folder
		} else if ( !file_exists( $this->folderPath ) ) {
			ppAdmin::warn( 'cant_create_folder', pp::tut()->missingP4Folder );

		// safe mode problem
		} else if ( ini_get( 'safe_mode' ) ) {
			ppAdmin::warn( 'safe_mode_subfolder_problem' );

		// um, if this happens, i want to know about it
		} else {
			ppAdmin::warn( 'tell_jared_problem', 'ppFolders::reportErrors' );
		}
	}


	protected function repairPermissions() {
		@chmod( $this->wpContentDir, 0755 );
		@chmod( $this->wpUploadPath, 0777 );
		@chmod( $this->ppFolderPath, 0777 );
		self::$attemptNum++;
		if ( self::$attemptNum <= 3 ) {
			$this->createFolderStructure();
		}
	}


	protected function winSanitize( $path ) {
		if ( NrUtil::isIn( 'IIS/7.5', $_SERVER['SERVER_SOFTWARE'] ) && NrUtil::startsWith( $this->abspath, '\\\\' ) ) {
			return $path;
		} else {
			return str_replace( '\\', '/', $path );
		}
	}


	protected function urlFromPath( $path ) {
		$url = str_replace( untrailingslashit( $this->winSanitize( $this->abspath ) ), $this->wpUrl, $path );
		if ( $url === $path ) {
			$url = apply_filters( 'pp_folders_url_from_path_fail', false, $path, $this->wpUrl );
			if ( false === $url ) {
				$this->folderError = 'Unable to determine wpUploadUrl';
			}
		}
		return $url;
	}
}

