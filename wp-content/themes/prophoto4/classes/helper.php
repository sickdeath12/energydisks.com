<?php

/* maybe these methods can eventually be moved into other classes, but I can't figure
   out how to organize them right now, so they are "helper" functions. Maybe they should
   go into ppUtil */

class ppHelper {


	const WITHOUT_SIDEBAR = false;
	const DEVICE_PIXELS = true;


	public static function contentWidth( $checkSidebar = true ) {
		if ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) {
			$blogWidth      = 320;
			$contentMargins = ppOpt::id( 'mobile_content_margin' ) * 2;
			$checkSidebar   = false;
		} else {
			$blogWidth      = ppOpt::id( 'blog_width' );
			$contentMargins = ppOpt::id( 'content_margin' ) * 2;
		}

		if ( isset( $_GET['full_width'] ) ) {
			$checkSidebar = false;
		}

		if ( $checkSidebar && ppWidgetUtil::areaHasWidgets( 'fixed-sidebar' ) ) {
			return $contentWidth = $blogWidth - ppSidebar::data()->total_width - $contentMargins;

		} else {
			return $contentWidth = $blogWidth - $contentMargins;
		}
	}


	public static function devicePixelAdjustedContentWidth() {
		return pp::browser()->mobileScreenWidth - ( ppOpt::id( 'mobile_content_margin' ) * 2 );
	}


	public static function skipMasthead() {
		if ( ppHelper::logoInMasthead() ) {
			return false;

		} else if ( ppOpt::test( 'masthead_display', 'off' ) ) {
			return true;

		} else if ( ppOpt::test( 'masthead_display', 'static || random' ) ) {
			return false;

		} else if ( ppOpt::test( 'masthead_modify', 'false' ) ) {
			return false;

		} else if ( ppOpt::test( 'modified_masthead_display', 'image' ) ) {
			return false;

		} else if ( ppOpt::test( 'masthead_on_' . ppUtil::pageType( ppUtil::NO_ARCHIVE_TYPE ), 'modified' ) ) {
			return true;

		} else {
			return false;
		}
	}



	public static function logoInMasthead() {
		if ( ppOpt::test( 'headerlayout', 'logomasthead_nav || mastlogohead_nav || mastheadlogo_nav' ) ) {
			return true;
		} else {
			return false;
		}
	}


	public static function isBeforeWatermarkStartDate( ppPost $post ) {
		if ( $watermarkStartdate = ppOpt::id( 'watermark_startdate' ) ) {
			if ( preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $watermarkStartdate ) ) {
				$watermarkStartdate = intval( str_replace( '-', '', $watermarkStartdate ) );
				$publishedDate = intval( preg_replace( '/ .*/', '', str_replace( '-', '', $post->publishedDate() ) ) );
				if ( $publishedDate < $watermarkStartdate ) {
					return true;
				}
			}
		}
		return false;
	}


	public static function blogBorderWidth() {
		switch ( ppOpt::id( 'blog_border' ) ) {
			case 'border':
				$width = 2 * ppOpt::id( 'blog_border_width' );
				return ( $width > 0 ) ? $width : 0;
				break;
			case 'dropshadow':
				if ( ppOpt::test( 'blog_border_shadow_width', 'narrow' ) ) {
					return pp::num()->blogDropshadowNarrowWidth * 2;
				} else {
					return pp::num()->blogDropshadowWideWidth * 2;
				}
				break;
			case 'none':
				return 0;
				break;
			default:
				new ppIssue( 'Unknown value for "blog_border"' );
				return 0;
		}
	}


	public static function updateActiveStatus() {
		$request = wp_remote_post( PROPHOTO_SITE_URL, array( 'body' => array(
			'uid'         => ppUid::get(),
			'url'         => pp::site()->url,
			'txn_id'      => ppOpt::id( 'txn_id' ),
			'payer_email' => ppOpt::id( 'payer_email' ),
			'svn'         => pp::site()->svn,
			'pp_ver'      => 'P4',
			'wp_ver'      => ppUtil::wpVersion(),
			'php_ver'     => floatval( phpversion() ),
			'server_raw'  => $_SERVER['SERVER_SOFTWARE'],
			'server'      => ppUtil::server(),
			'nameservers' => ppUtil::nameservers(),
			'web_host'    => ppUtil::webHost(),
			'mysql_ver'   => preg_replace( '/[^0-9\.]/', '', mysql_get_server_info() ),
			'one_click'   => ppUtil::isAutoUpgradeCapable() ? 'capable' : 'incapable',
			'plugins'     => json_encode( (array) get_option( 'active_plugins' ) ),
			'design'      => json_encode( ppActiveDesign::toArray() ),
			'non_design'  => json_encode( ppOpt::getNonDesignOptions() ),
			'requestHandler' => 'CheckIn::process'
		) ) );
		return wp_remote_retrieve_body( $request );
	}
}

