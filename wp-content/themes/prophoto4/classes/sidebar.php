<?php

class ppSidebar {


	protected static $data;


	public static function render() {
		if ( ppWidgetUtil::areaHasWidgets( 'fixed-sidebar' ) ) {
			$widgetsMarkup = ppWidgetUtil::areaContent( 'fixed-sidebar' );
			echo preg_replace_callback( '/<img[^>]*>/i', 'ppSidebar::maybeDownsizeSidebarImgs', $widgetsMarkup );
		}
	}


	public static function data() {

		if ( self::$data == null ) {

			$sb = array();
			$sb['side'] = $sb['outer_side'] = ppOpt::id( 'sidebar' );
			$sb['content_side'] = $sb['inner_side'] = ( $sb['side'] == 'right' ) ? 'left' : 'right';
			$sb['content_width'] = ppOpt::id( 'sidebar_width', 'int' );
			$sb['outer_padding_width'] = ppOpt::id( 'sidebar_padding', 'int' );

			// border info
			if ( ppOpt::test( 'sidebar_border_switch', 'on' ) ) {
				$sb['using_border'] = true;
				$sb['border_color'] = ppOpt::id( 'sidebar_border_color' );
				$sb['border_width'] = ppOpt::id( 'sidebar_border_width', 'int' );
				$sb['border_style'] = ppOpt::id( 'sidebar_border_style' );
			} else {
				$sb['using_border'] = false;
				$sb['border_color'] = '#ffffff';
				$sb['border_width'] = 0;
				$sb['border_style'] = 'none';
			}

			// inner (content-side) padding
			if ( ppOpt::id( 'sidebar_inner_padding_override' ) != '' ) {
				$sb['inner_padding_width'] = ppOpt::id( 'sidebar_inner_padding_override', 'int' );
				$sb['move_post_footer_items'] = false;

			} else if ( $sb['using_border'] ) {
				$sb['inner_padding_width'] = $sb['outer_padding_width'];
				$sb['move_post_footer_items'] = false;

			} else {
				$sb['inner_padding_width'] = 0;
				$sb['move_post_footer_items'] = true;
			}

			// total width
			$sb['total_width'] = $sb['content_width'] + $sb['outer_padding_width'] + $sb['inner_padding_width'];
			if ( $sb['using_border'] ) {
				$sb['total_width'] += $sb['border_width'];
			}

			self::$data = (object) $sb;
		}

		return self::$data;
	}


	public static function onThisPage() {
		if ( isset( $_GET['ajaxFetching'] ) ) {
			return false;
		}

		if ( !ppWidgetUtil::areaHasWidgets( 'fixed-sidebar' ) ) {
			return false;
		}

		if ( ppUtil::isStaticFrontPage() ) {
			return ppOpt::id( 'sidebar_on_front_page', 'bool' );
		}

		if ( is_home() && ppOpt::id( 'sidebar_on_home', 'bool' ) ) {
			return true;
		}

		if ( ( is_archive() || is_search() ) && ppOpt::id( 'sidebar_on_archive', 'bool' ) ) {
			return true;
		}

		if ( is_single() || is_page() ) {

			$post = ppPost::fromGlobal();

			if ( $post && $perPostDoSidebar = get_post_meta( $post->id(), 'do_sidebar', AS_STRING ) ) {
				return ppUtil::formatVal( strtolower( $perPostDoSidebar ), 'bool' );
			}

			if ( is_single() && ppOpt::id( 'sidebar_on_single', 'bool' ) ) {
				return true;

			} else if ( is_page() && ppOpt::id( 'sidebar_on_page', 'bool' ) ) {
				return true;
			}
		}

		return false;
	}


	private static function maybeDownsizeSidebarImgs( $match ) {
		return ppGdModify::sidebarImg( ppImgTag::createFromHtml( $match[0] ) )->markup();
	}


	public function flushCache() {
		self::$data = null;
	}
}

