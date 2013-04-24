<?php

class ppWidgetAreaWidth {

	private $sidebarName;
	public $width;
	public $minWidth;


	public function __construct( $sidebarName ) {
		if ( !ppWidgetUtil::isRegisteredArea( $sidebarName ) ) {
			new ppIssue( "\$sidebarName '$sidebarName' not found" );
		} else {
			$this->sidebarName = $sidebarName;
			$this->calculateWidths();
		}
	}


	private function calculateWidths() {
		$sidebarsWidgets = get_option( 'sidebars_widgets' );
		$sidebarData = $sidebarsWidgets[$this->sidebarName];
		$areaWidth = 0;
		$hasUndefinedWidthWidget = false;

		// store widest width of defined-width widget
		// make note if a non-defined widget is present
		foreach ( $sidebarData as $widgetHandle ) {

			list( $widgetType, $widgetId ) = ppWidgetUtil::parseWidgetHandle( $widgetHandle );

			switch ( $widgetType ) {

				// social media icon
				case 'pp-social-media-icon':
					$iconInfo = get_option( 'widget_pp-social-media-icon' );
					$iconSize = ppSocialMediaIcon_Widget::sizeInfo( $iconInfo[$widgetId] );
					$widgetWidth = $iconSize['html_size'];
					$areaWidth = $this->widerOf( $widgetWidth, $areaWidth );
					break;

				// custom icon
				case 'pp-custom-icon':
					$customIconInfo = get_option( 'widget_pp-custom-icon' );
					$imgNumber = $customIconInfo[$widgetId]['number'];
					$widgetWidth = ppImg::id( 'widget_custom_image_' . $imgNumber )->width;
					$areaWidth = $this->widerOf( $widgetWidth, $areaWidth );
					break;

				// facebook likebox
				case 'pp-facebook-likebox':
					$likeboxInfo = get_option( 'widget_pp-facebook-likebox' );
					$widgetWidth = $likeboxInfo[$widgetId]['width'];
					$areaWidth = $this->widerOf( $widgetWidth, $areaWidth );
					break;

				// twitter.com widget
				case 'pp-twitter-com':
					$twitterWidgetInfo = get_option( 'widget_pp-twitter-com' );
					$twitterWidgetWidth = $twitterWidgetInfo[$widgetId]['width'];
					if ( $twitterWidgetWidth == "'auto'" ) {
						$hasUndefinedWidthWidget = true;
					} else {
						if ( $twitterWidgetWidth < 150 ) {
							$twitterWidgetWidth = 150; // undocumented min-width of widget
						}
						$areaWidth = $this->widerOf( $twitterWidgetWidth, $areaWidth );
					}
					break;

				// sliding twitter widget
				case 'pp-sliding-twitter':
					if ( !isset( $widget_info ) ) {
						$slidingWidgetInfo = get_option( 'widget_pp-sliding-twitter' );
					}
					$imgVal = isset( $slidingWidgetInfo[$widgetId] ) ? $slidingWidgetInfo[$widgetId]['image'] : 'A';
					// using an image
					if ( $imgVal != 'no' ) {
						// built in images
						if ( $imgVal == 'A' ) {
							$widgetWidth = 300; // twitter image1 width
						} elseif ( $imgVal == 'B' ) {
							$widgetWidth = 194; // twitter image2 width
						// custom images
						} else {
							$widgetWidth = ppImg::id( 'widget_custom_image_' . $imgVal )->width;
						}
					// no background image
					} else {
						$widgetWidth = intVal( $slidingWidgetInfo[$widgetId]['tweet_width'] ) + 25;
					}
					$areaWidth = $this->widerOf( $widgetWidth, $areaWidth );
					break;

				// widget with no known width (flexible)
				default:
					$hasUndefinedWidthWidget = true;
					break;
			}
		}

		// non-zero column width means at least one widget has defined width
		$widthInfo = array();
		if ( $areaWidth ) {
			// there is also an undefined-width widget, call it a flex, but note the mininum width
			if ( $hasUndefinedWidthWidget ) {
				$this->width = 'flex';
				$this->minWidth = $areaWidth;
			// only defined-width widgets, return this as actual width
			} else {
				$this->width = $areaWidth;
			}
		// column_width still 0, no defined-width widgets found
		} else {
			$this->width = 'flex';
		}
	}


	/* comparing widget width with working column width  */
	private function widerOf( $widgetWidth, $areaWidth ) {
		if ( $widgetWidth > $areaWidth ) {
			return intval( $widgetWidth );
		} else {
			return intval( $areaWidth );
		}
	}
}
