<?php


class ppRemoteFiles {


	protected static $missingFiles;
	protected static $remoteFiles = array();


	public static function attemptDownload() {
		if ( in_array( $GLOBALS['pagenow'], array( 'update.php', 'update-core.php', 'plugins.php' ) ) ) {
			return false;

		} else if ( !ppRemoteFiles::missingFiles() ) {
			return false;

		} else if ( !ppUtil::s3SiteReachable() ) {
			return false;

		} else if ( !ppUtil::imagesFolderWriteable() ) {
			return false;

		} else if ( isset( $_GET['retry_download_remote_files'] ) ) {
			return true;

		} else {
			return ( (bool) get_transient( 'delay_download_remote_files_attempt' ) === false );
		}
	}


	public static function missingFiles() {
		if ( self::$missingFiles == null ) {
			$remoteFiles = self::allFileData();
			$toDownload = array();
			foreach ( $remoteFiles as $filename => $fileData ) {
				$pathStart = ppFontUtil::isFontFile( $filename ) ? pp::fileInfo()->fontsFolderPath : pp::fileInfo()->imagesFolderPath;
				$filepath  = $pathStart . '/' . $filename;
				if ( !@file_exists( $filepath ) || md5_file( $filepath ) != $fileData['hash']  ) {
					if ( @file_exists( $filepath ) ) {
						@unlink( $filepath );
					}
					$toDownload[$filename] = $fileData['hash'];
				}
			}
			self::$missingFiles = $toDownload;
		}
		return self::$missingFiles;
	}


	public static function allFileData() {
		if ( array() === self::$remoteFiles ) {
			$starterFiles = glob( TEMPLATEPATH . '/config/conf.starter_*' );
			foreach ( $starterFiles as $starterFile ) {
				$data = ppUtil::loadConfig( str_replace( array( 'conf.', '.php' ), '', basename( $starterFile ) ) );
				if ( isset( $data['remote_files'] ) ) {
					self::$remoteFiles = array_merge( self::$remoteFiles, $data['remote_files' ] );
				}
			}
			self::$remoteFiles = array_merge( self::$remoteFiles, self::remoteThemeFiles() );
		}
		return self::$remoteFiles;
	}


	public static function download( $filename, $filehash ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$file = download_url( EXT_RESOURCE_URL . '/img/' . $filename );

		if ( is_wp_error( $file ) ) {
			$msg = $file->get_error_message();
		} else {
			$path  = ppFontUtil::isFontFile( $filename ) ? pp::fileInfo()->fontsFolderPath : pp::fileInfo()->imagesFolderPath;
			$moved = copy( $file, $localFile = $path . '/' . $filename );
			if ( !$moved ) {
				$msg = 'Unable to move downloaded file';
			} else {
				if ( md5_file( $localFile ) == $filehash ) {
					$msg = 'Successfully downloaded';
					@chmod( $localFile, 0755 );
				} else {
					$msg = 'File download corrupted or incomplete';
					@unlink( $localFile );
				}
			}
		}

		@unlink( $file );
		return $msg;
	}


	public static function downloadMissingFiles() {
		if ( self::attemptDownload() ) {
			add_action( 'admin_footer', 'ppRemoteFiles::addJavascript' );
			if ( ppOpt::id( 'activation_time' ) && ( time() - ppOpt::id( 'activation_time', 'int' ) ) > 60*60 * 4 ) {
				set_transient( 'delay_download_remote_files_attempt', 'true', 60*60 * 12 );
			}
		}
	}


	public static function addJavascript() {
		$imgs = json_encode( self::$missingFiles );
		echo "<script>\nvar remoteFiles = $imgs;\n";
		echo file_get_contents( TEMPLATEPATH . '/dynamic/js/remoteFiles.js' );
		echo '</script>';
	}


	protected static function remoteThemeFiles() {
		return array(
			'ajaxLoadingSpinner.gif' => array(
				'hash' => 'a51c5608d01acf32df728f299767f82b',
				'width' => '32',
				'height' => '32',
				'size' => '3',
			),

			'borderCAC9C9.gif' => array(
				'hash' => '1b2aabf4a8a1fddb1f1fa521edd611c9',
				'width' => '1',
				'height' => '1',
				'size' => '1',
			),

			'comment.png' => array(
				'hash' => '28c41789b6c7a597c9d1ee6d8389753b',
				'width' => '13',
				'height' => '11',
				'size' => '1',
			),

			'comments-closed.gif' => array(
				'hash' => '466b48684203a09d9ea13702e932e0c8',
				'width' => '9',
				'height' => '9',
				'size' => '1',
			),

			'comments-open.gif' => array(
				'hash' => '11218e92cd556a75b598d4bdb3a4aef8',
				'width' => '9',
				'height' => '8',
				'size' => '1',
			),

			'dropshadow_corners.png' => array(
				'hash' => '57b743ed8ac3f6ecfc3b842f72103d8e',
				'width' => '14',
				'height' => '14',
				'size' => '1',
			),

			'dropshadow_sides.png' => array(
				'hash' => '62f1e338266a6c8de72a46f8328ca07c',
				'width' => '14',
				'height' => '7',
				'size' => '1',
			),

			'dropshadow_topbottom.png' => array(
				'hash' => 'a22b548f8147bd2fff3577d827867c74',
				'width' => '7',
				'height' => '14',
				'size' => '1',
			),

			'dropshadow_wide_corners.png' => array(
				'hash' => '61e6f5a6acb7b93a7b41d1197a5df671',
				'width' => '120',
				'height' => '120',
				'size' => '4',
			),

			'dropshadow_wide_sides.png' => array(
				'hash' => '66ba616b6a2f1fba90fcdcada33a4979',
				'width' => '120',
				'height' => '10',
				'size' => '1',
			),

			'dropshadow_wide_topbottom.png' => array(
				'hash' => 'a127ebddd1058b4c7d8c85ee074556a3',
				'width' => '10',
				'height' => '120',
				'size' => '1',
			),

			'email.gif' => array(
				'hash' => '71cef411695c139e089b1d05e0aa6750',
				'width' => '16',
				'height' => '11',
				'size' => '1',
			),

			'lightbox-btn-close.gif' => array(
				'hash' => '2c38ae5be85141bf8867c9523f4bc357',
				'width' => '66',
				'height' => '22',
				'size' => '1',
			),

			'lightbox-btn-next.gif' => array(
				'hash' => '23414965ebe526012e473c1d4d65d4e7',
				'width' => '63',
				'height' => '32',
				'size' => '1',
			),

			'lightbox-btn-prev.gif' => array(
				'hash' => '5a9118de4ef6226473190d6e82f6d80d',
				'width' => '63',
				'height' => '32',
				'size' => '1',
			),

			'lightbox-ico-loading.gif' => array(
				'hash' => 'b5fe8df97005341f898e2cf84e68de01',
				'width' => '32',
				'height' => '32',
				'size' => '4',
			),

			'link.gif' => array(
				'hash' => 'cd0e92997769a20ffb6ac3dcc97feeef',
				'width' => '14',
				'height' => '11',
				'size' => '1',
			),

			'minima-comments-show-hide.png' => array(
				'hash' => '2a3f78c4763ea82766eeea3c9d755bde',
				'width' => '21',
				'height' => '61',
				'size' => '1',
			),

			'post-interaction-button-bg.jpg' => array(
				'hash' => 'a6b458559234d6edf6cecd20283c0442',
				'width' => '10',
				'height' => '30',
				'size' => '1',
			),

			'tab1-left.jpg' => array(
				'hash' => 'b4e1d85a49b684f2c333c4bc5e227c9c',
				'width' => '94',
				'height' => '36',
				'size' => '1',
			),

			'tab1-right.jpg' => array(
				'hash' => '1134c33d808834f27950a566c3392849',
				'width' => '94',
				'height' => '36',
				'size' => '1',
			),

			'tab2-left.jpg' => array(
				'hash' => '19499d42fe7b388573f446852a78381a',
				'width' => '97',
				'height' => '36',
				'size' => '1',
			),

			'tab2-right.jpg' => array(
				'hash' => '90f3d65a723c6a6ca606de460406792e',
				'width' => '97',
				'height' => '36',
				'size' => '1',
			),

			'watermark.png' => array(
				'hash' => 'f1ecccd908e00e41a6e9f51fe2184025',
				'width' => '300',
				'height' => '93',
				'size' => '9',
			),
		);
	}


	public static function flushCache() {
		self::$missingFiles = null;
		self::$remoteFiles = array();
	}

}

