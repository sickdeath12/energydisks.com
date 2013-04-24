<?php

class ppConstants {

	public $site;
	public $wp;
	public $browser;
	public $numbers;


	public function __construct() {
		$this->site = (object) array(
			'svn'      => require( TEMPLATEPATH . '/svn.php' ),
			'url'      => defined( 'WP_SITEURL' ) ? WP_SITEURL : get_bloginfo( 'url', 'display' ),
			'wpurl'    => defined( 'WP_HOME' ) ? WP_HOME : get_bloginfo( 'wpurl' ),
			'themeUrl' => get_bloginfo( 'template_directory' ),
			'name'     => get_bloginfo( 'name', 'display' ),
			'tagline'  => get_bloginfo( 'description' ),
			'isDev'    => IS_DEV,
			'hasStaticFrontPage' => ( get_option( 'show_on_front' ) == 'page' && get_option( 'page_on_front' ) ),
			'extResourceUrl'     => EXT_RESOURCE_URL,
		);
		$this->wp = (object) array(
			'imgThumbWidth'  => get_option( 'thumbnail_size_w' ),
			'imgThumbHeight' => get_option( 'thumbnail_size_h' ),
			'imgMedWidth'    => get_option( 'medium_size_w' ),
			'imgMedHeight'   => get_option( 'medium_size_h' ),
			'imgLargeWidth'  => get_option( 'large_size_w' ),
			'imgLargeHeight' => get_option( 'large_size_h' ),
			'dbContactLog'   => 'prophoto_theme_contact_log',
		);
		$userAgent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$isIPad = ( NrUtil::isIn( 'iPad', $userAgent ) || isset( $_GET['spoof_ipad'] ) );
		$isIPhone = ( !$isIPad && NrUtil::isIn( 'iPhone', $userAgent ) );
		$mobileUABits = array(
			'iPhone', 'iPod', 'Android', 'CUPCAKE', 'incognito', 'dream',
			'webmate', 'froyo', 'BlackBerry9500', 'BlackBerry9520', 'BlackBerry9530',
			'BlackBerry9550', 'BlackBerry 9800', 'IEMobile/7.0', 'Googlebot-Mobile',
			's800', 'bada', 'webOS',
		);
		$isMobile = isset( $_GET['force_mobile'] );
		foreach ( $mobileUABits as $UABit ) {
			if ( !$isMobile && !$isIPad && NrUtil::isIn( $UABit, $userAgent ) ) {
				$isMobile = true;
			}
		}
		$this->browser = (object) array(
			'isTech'   => ( !isset( $_GET['notTech'] ) && ( NrUtil::isIn( 'prophototech', $userAgent ) || IS_DEV ) ),
			'isIPad'   => $isIPad,
			'isIPhone' => $isIPhone,
			'isMobile' => $isMobile,
			'isMobileSafari'    => ( $isIPad || $isIPhone ),
			'hasRetinaDisplay'  => NrUtil::COOKIE( 'retina_display', 'true' ),
			'mobileScreenWidth' => NrUtil::COOKIE( 'retina_display', 'true' ) ? 960 : ppMobileHtml::STANDARD_MOBILE_DEVICE_MAX_WIDTH,
		);
		// TODO: some of these can probably be moved into specific classes
		$this->numbers = (object) array(
			'maxAdBanners'               => 30,
			'maxMastheadImages'          => apply_filters( 'pp_maxmastheadimages', 60 ),
			'blogDropshadowNarrowWidth'  => 7,
			'blogDropshadowWideWidth'    => 60,
			'maxBioImages'               => 30,
			'maxBioWidgetColumns'        => 4,
			'maxFooterWidgetColumns'     => 4,
			'maxCustomMenuLinks'         => 10,
			'maxCustomWidgetImages'      => apply_filters( 'pp_maxcustomwidgetimages', 25 ),
			'maxSidebarDrawers'          => 4,
			'maxContactFormCustomFields' => 4,
			'maxAudioUploads'            => 20,
			'maxWidgetMenus'             => 3,
			'maxCustomFonts'             => 8,
			'maxCallToActionItems'       => 8,
			'maxExtraBgImgs'             => 12,
			'maxLightboxOverlayImgSize'  => apply_filters( 'pp_maxlightboxoverlayimgsize', 900 ),

		);
		$tutorialsUrl = PROPHOTO_SITE_URL . 'support/';
		$this->tutorials = (object) array(
			'wpVersionFail'          => $tutorialsUrl . 'wp-version-not-supported/',
			'backupBlog'             => $tutorialsUrl . 'backup-blog/',
			'ftpUploadAudio'         => $tutorialsUrl . 'ftp-uploading-mp3s/',
			'favicon'                => $tutorialsUrl . 'favicon/',
			'editThemeFiles'         => $tutorialsUrl . 'editing-theme-files/',
			'customizeBioArea'       => $tutorialsUrl . 'customizing-bio-area/',
			'understandingWidgets'   => $tutorialsUrl . 'understanding-widgets/',
			'featuredGalleries'      => $tutorialsUrl . 'featured-galleries/',
			'understandingDesigns'   => $tutorialsUrl . 'understanding-designs/',
			'changePermissions'      => $tutorialsUrl . 'changing-permissions/',
			'ftp'                    => $tutorialsUrl . 'all-about-ftp/',
			'importDesign'           => $tutorialsUrl . 'import-p4-designs/',
			'enableSubscribeByEmail' => $tutorialsUrl . 'enable-email-subscriptions/',
			'fixContactForm'         => $tutorialsUrl . 'fix-contact-form/',
			'nestedThemeFolder'      => $tutorialsUrl . 'nested-theme-folder/',
			'misnamedThemeFolder'    => $tutorialsUrl . 'misnamed-theme-folder/',
			'uploadsFolder'          => $tutorialsUrl . 'uploads-folder/',
			'missingP4Folder'        => $tutorialsUrl . 'missing-p4-folder/',
			'staticFilesNotWritten'  => $tutorialsUrl . 'static-files-not-written/',
			'changeBlogAddress'      => $tutorialsUrl . 'change-blog-address/',
			'contactForm'            => $tutorialsUrl . 'contact-form/',
			'contactFormNotSending'  => $tutorialsUrl . 'contact-form-not-sending/',
			'contactFormNotLoading'  => $tutorialsUrl . 'contact-form-not-loading/',
			'backupNag'              => $tutorialsUrl . 'db-backup-plugin-nag/',
			'feedburnerFeed'         => $tutorialsUrl . 'feedburner-url/',
			'upgradeWp'              => $tutorialsUrl . 'upgrading-wordpress/',
			'registerGlobals'        => $tutorialsUrl . 'register-globals/',
			'safeMode'               => $tutorialsUrl . 'safe-mode-folders-fix/',
			'commentSpam'            => $tutorialsUrl . 'comment-spam/',
			'manualPluginInstall'    => $tutorialsUrl . 'manual-plugin-install/',
			'customFonts'            => $tutorialsUrl . 'uploading-custom-fonts/',
			'grids'                  => $tutorialsUrl . 'grids/',
			'ftpAudioFiles'          => $tutorialsUrl . 'ftp-audio-files/',
			'facebookFindNumericID'  => $tutorialsUrl . 'find-facebook-personal-numeric-id/',
			'extFacebookFanbox'      => PROPHOTO_SITE_URL . 'external/facebook-likebox/',
			'extFacebookBizPage'     => PROPHOTO_SITE_URL . 'external/facebook-page/',
			'extTwitterWidget'       => PROPHOTO_SITE_URL . 'external/twitter-widget/',
			'contactUs'              => PROPHOTO_SITE_URL . 'support/contact/',
		);
	}
}


