<?php

class ppPostImgUtil {


	private static $rootNoExt;


	public static function relatedImg( $url, $requestedSize ) {
		$parentImg = ppParentImg::fromUrl( $url );
		if ( !$parentImg ) {
			return false;
		}

		if ( $requestedSize == 'fullsize' ) {
			return $parentImg->tagObj();
		}

		return $parentImg->thumb( $requestedSize );
	}


	public static function maxWidth( $accountForSidebar = null ) {
		if ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) {
			$contentWidth = pp::browser()->mobileScreenWidth - ( ppOpt::id( 'mobile_content_margin' ) * 2 );
		} else {
			if ( $accountForSidebar == null ) {
				$accountForSidebar = ppSidebar::onThisPage();
			}
			$contentWidth = ppHelper::contentWidth( $accountForSidebar );
		}
		return $contentWidth - 2 * ppOpt::id( 'post_pic_border_width', 'int' );
	}
}

