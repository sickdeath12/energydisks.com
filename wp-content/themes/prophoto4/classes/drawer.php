<?php

class ppDrawer {


	protected static $drawerNum;


	public static function renderAll() {
		if ( isset( $_GET['gallery_popup'] ) ) {
			return;
		}

		for ( $i = 1; $i <= pp::num()->maxSidebarDrawers; $i++ ) {
			self::$drawerNum = $i;
			if ( ppWidgetUtil::areaHasWidgets( "drawer-$i" ) ) {
				$drawer = ppUtil::renderView( 'drawer', array( 'drawerNum' => $i ), ppUtil::RETURN_VIEW );
				echo preg_replace_callback( '/<img[^>]*>/i', 'ppDrawer::maybeDownsizeDrawerImgs', $drawer );
			}
		}
	}



	protected static function maybeDownsizeDrawerImgs( $match ) {
		static $widths = array();
		if ( !isset( $widths[self::$drawerNum] ) ) {
			$widths[self::$drawerNum] = self::contentWidth( self::$drawerNum );
		}
		return ppGdModify::constrainImgSize( ppImgTag::createFromHtml( $match[0] ), $widths[self::$drawerNum] )->markup();
	}


	public static function tabTextMarkup( $text ) {
		if ( !is_string( $text ) ) {
			new ppIssue( 'Non-string $text input' );
			$text = 'INFO';
		}

		$text = trim( $text );
		if ( $text === '' ) {
			$text = 'INFO';
		}

		$strLen = function_exists( 'mb_strlen' ) ? 'mb_strlen' : 'strlen';
		$subStr = function_exists( 'mb_substr' ) ? 'mb_substr' : 'substr';

		$textLength = $strLen( $text );
		$markup = "\n";
		for ( $i = 0; $i < $textLength; $i++ ) {
			$character = $subStr( $text, $i, 1 );
			$character =  ( ' ' == $character ) ? '&nbsp;' : $character;
			$markup .= '<span>' . $character . "</span>\n";
		}

		return $markup;
	}


	public static function contentWidth( $drawerNum ) {
		$drawer_default_width     = ppOpt::id( 'drawer_content_width_' . $drawerNum );
		$drawer_widget_width_info = new ppWidgetAreaWidth( 'drawer-' . $drawerNum );

		// min width, check against default drawer width
		if ( intval( $drawer_widget_width_info->minWidth > $drawer_default_width  ) ) {
			return $drawer_widget_width_info->minWidth;

		// use exact width of widest widget (no flexible width widgets)
		} elseif ( $drawer_widget_width_info->width != 'flex' ) {
			return  $drawer_widget_width_info->width;

		// flexible-width widgets only, or min-width less than default, use default width
		} else {
			return $drawer_default_width;
		}
	}


	public static function inUse() {
		for ( $i = 1; $i <= pp::num()->maxSidebarDrawers; $i++ ) {
			if ( ppWidgetUtil::areaHasWidgets( 'drawer-' . $i ) ) {
				return true;
			}
		}
		return false;
	}
}

