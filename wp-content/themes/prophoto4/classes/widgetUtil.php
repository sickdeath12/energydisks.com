<?php

class ppWidgetUtil {


	const RESTORE_OLD_WIDGETS = true;
	public static $widgetsProgramaticallyDeactivated;
	protected static $renderingArea = false;
	protected static $oldWidgets;


	public static function deleteInactiveWidgets() {
		$widgetAreas     = get_option( 'sidebars_widgets' );
		$inactiveWidgets = isset( $widgetAreas['wp_inactive_widgets'] ) ? $widgetAreas['wp_inactive_widgets'] : array();
		foreach ( (array) $inactiveWidgets as $inactiveWidget ) {
			list( $widgetType, $widgetId ) = self::parseWidgetHandle( $inactiveWidget );
			self::deleteWidget( $widgetType, $widgetId );
		}
	}


	public static function instanceData( $handle ) {
		if ( !is_string( $handle ) ) {
			new ppIssue( 'ppWidgetUtil::instanceData() requires string input param' );
			return false;
		}
		list( $type, $id ) = self::parseWidgetHandle( $handle );
		$widgetType = get_option( 'widget_' . $type );
		$instance = $widgetType[intval( $id )];
		return compact( 'id', 'type', 'instance' );
	}


	public static function updateWidget( $type, $id, $newInstanceData ) {
		if ( !$widgetTypeData = get_option( 'widget_' . $type ) ) {
			new ppIssue( "Unable to load widget type data for type: $type" );
			return false;
		}

		if ( !isset( $widgetTypeData[$id] ) ) {
			new ppIssue( "Unable to find data for widget of type '$type' with id: $id" );
			return false;
		}
		$widgetTypeData[$id] = $newInstanceData;
		$return = update_option( 'widget_' . $type, $widgetTypeData );
		wp_cache_flush();
		return $return;
	}


	public static function deleteWidget( $typeOrHandle, $id = null ) {
		if ( $id !== null ) {
			$type = $typeOrHandle;
		} else {
			$handle = $typeOrHandle;
			list( $type, $id ) = self::parseWidgetHandle( $handle );
		}

		if ( !is_string( $type ) || !is_numeric( $id ) ) {
			new ppIssue( 'Invalid param types passed' );
			return false;
		}

		$removedWidget = $removedFromSidebar = false;

		// first, remove the widget from it's type-specific option storage
		$typeWidgets = get_option( 'widget_' . $type );

		if ( $typeWidgets === false ) {
			return false;
		}

		if ( array_key_exists( $id, $typeWidgets ) ) {
			unset( $typeWidgets[$id] );
			update_option( 'widget_' . $type, $typeWidgets );
			$removedWidget = true;
		} else if ( is_numeric( $id ) && array_key_exists( intval( $id ), $typeWidgets ) ) {
			unset( $typeWidgets[ intval( $id ) ] );
			update_option( 'widget_' . $type, $typeWidgets );
			$removedWidget = true;
		} else {
			new ppIssue( "Widget {$type}-{$id} not found in type-storage" );
		}

		// next we remove it from it's sidebar
		$widgetAreas = get_option( 'sidebars_widgets' );
		foreach ( $widgetAreas as $widgetAreaName => $widgetArea ) {
			if ( is_array( $widgetArea ) ) {
				foreach ( $widgetArea as $index => $widgetHandle ) {
					if ( $widgetHandle == ( $type . '-' . $id ) ) {
						unset( $widgetAreas[$widgetAreaName][$index] );
						$removedFromSidebar = true;
					}
				}
			}
		}
		if ( $removedFromSidebar ) {
			update_option( 'sidebars_widgets', $widgetAreas );
		} else {
			new ppIssue( "Widget {$type}-{$id} not found in area-storage" );
		}
		if ( $removedWidget && $removedFromSidebar ) {
			return true;
		} else {
			return false;
		}
	}


	public static function addWidget( $sidebarName, $widgetType, $widgetInstanceData ) {
		if ( !is_string( $sidebarName ) || !is_string( $widgetType ) || !is_array( $widgetInstanceData ) ) {
			new ppIssue( 'Bad input params in ppWidgetUtil::addWidget()' );
			return false;
		}

		if ( !self::isRegisteredArea( $sidebarName ) ) {
			new ppIssue( "\$sidebarName '$sidebarName' is not a registered sidebar in ppWidgetUtil::addWidget()" );
			return false;
		}

		// get current widget instances info array for this widget type
		$widgets = get_option( 'widget_' . $widgetType );
		if ( !is_array( $widgets ) || empty( $widgets ) ) {
			// set placeholders so we never get an ID of 0 or 1 which can cause problems
			$widgets = array( 'placeholder', 'placeholder2' );
		}

		// add our custom widget instance
		$widgets[] = $widgetInstanceData;
		if ( in_array( 'placeholder', $widgets ) ) {
			unset( $widgets[0] );
			unset( $widgets[1] );
		}

		// get id of added widget
		$newWidgetId = array_pop( array_keys( $widgets ) );

		// update db with new widget added
		update_option( 'widget_' . $widgetType, $widgets );

		//  get current sidebar widgets info array
		$savedWidgets = get_option( 'sidebars_widgets' );
		if ( !is_array( $savedWidgets ) ) {
			$savedWidgets = array();
		}

		// update the info with our newly created widget, store in db
		$savedWidgets[$sidebarName][] = $widgetType . '-' . $newWidgetId;
		wp_set_sidebars_widgets( $savedWidgets );

		return $newWidgetId;
	}


	public static function instanceOfTypeExists( $type ) {
		if ( !is_string( $type ) ) {
			new ppIssue( '$type must be string in ppWidgetUtil::instanceOfTypeExists() given: ' . NrUtil::getVarDump( $type ) );
			return false;
		}
		return is_active_widget( false, false, $type );
	}


	public static function twitterHtmlInstanceExists() {
		return ( ppWidgetUtil::instanceOfTypeExists( 'pp-twitter-html-badge' ) || ppWidgetUtil::instanceOfTypeExists( 'pp-sliding-twitter' ) );
	}


	public static function areaHasWidgets( $sidebar ) {
		if ( !is_string( $sidebar ) ) {
			new ppIssue( '$sidebar must be string in ppWidgetUtil::hasWidget()' );
			return false;
		}
		$widgetsIn = get_option( 'sidebars_widgets' );
		return ( isset( $widgetsIn[$sidebar] ) && is_array( $widgetsIn[$sidebar] ) && !empty( $widgetsIn[$sidebar] ) );
	}


	public static function footerHasWidgets() {
		for ( $i = 1; $i <= pp::num()->maxFooterWidgetColumns; $i++ ) {
			if ( ppWidgetUtil::areaHasWidgets( 'footer-col-' . $i ) ) {
				return true;
			}
		}
		return ( ppWidgetUtil::areaHasWidgets( 'footer-spanning-col-top' ) || ppWidgetUtil::areaHasWidgets( 'footer-spanning-col-btm' ) );
	}


	public static function areaContent( $area ) {
		self::$renderingArea = $area;
		$content = ppUtil::ob( 'dynamic_sidebar', $area );
		self::$renderingArea = false;
		return $content;
	}


	public static function renderingArea() {
		return self::$renderingArea;
	}


	public static function areaWidth() {
		if ( NrUtil::isIn( 'bio-', self::$renderingArea ) ) {
			$bioColData = ppBioColumns::data();
			if ( self::$renderingArea == 'bio-spanning-col' ) {
				return $bioColData->spanningColWidth;
			} else {
				return $bioColData->colWidths[ str_replace( 'bio-col-', '', self::$renderingArea ) ]->width;
			}

		} else if ( self::$renderingArea == 'contact-form' ) {
			// 36% is the value we use in css laying out the section. janky, i know
			return intval( ppHelper::contentWidth( ppHelper::WITHOUT_SIDEBAR ) * 0.36 );

		} else if ( self::$renderingArea == 'fixed-sidebar' ) {
			return ppSidebar::data()->content_width;

		} else if ( NrUtil::isIn( 'drawer-', self::$renderingArea ) ) {
			return ppDrawer::contentWidth( str_replace( 'drawer-', '', self::$renderingArea ) );

		} else if ( NrUtil::isIn( 'footer-col-', self::$renderingArea ) ) {
			return ppFooter::columnInfo()->columnWidth;

		} else if ( NrUtil::isIn( 'footer-spanning-col-', self::$renderingArea ) ) {
			return ppFooter::columnInfo()->spanningWidth;
		}
	}


	public static function registerWidgets() {
		if ( !has_action( 'widgets_init', 'ppWidgetUtil::registerWidgets' ) ) {
			add_action( 'widgets_init', 'ppWidgetUtil::registerWidgets' );
		} else {
			$widgetFiles = glob( TEMPLATEPATH . '/widgets/*.php' );
			$widgetClasses = array();
			foreach ( $widgetFiles as $widgetFile ) {
				register_widget( 'pp' . ucfirst( str_replace( '.php', '', basename( $widgetFile ) ) ) );
	 		}
		}
	}


	public static function registerAreas() {
		register_sidebar( array(
			'id'           => 'contact-form',
			'before_title' => "<h2>",
			'after_title'  => "</h2>\n",
			'name'         => 'Contact Form Content Area',
		) );

		register_sidebar( array(
			'id'           => 'bio-spanning-col',
			'before_title' => "<h3 class='widgettitle'>",
			'after_title'  => "</h3>\n",
			'name'         => 'Bio Area Spanning Column',
		 ) );

		for ( $i = 1; $i <= pp::num()->maxBioWidgetColumns; $i++ ) {
			register_sidebar( array(
				'id'           => 'bio-col-' . $i,
				'before_title' => "<h3 class='widgettitle'>",
				'after_title'  => "</h3>\n",
				'name'         => 'Bio Area Column #' . $i,
			 ) );
		}

		register_sidebar( array(
			'id'           => 'fixed-sidebar',
			'before_title' => "<h3 class='widgettitle'>",
			'after_title'  => "</h3>\n",
			'name'         => 'Fixed Sidebar',
		) );

		for ( $i = 1; $i <= pp::num()->maxSidebarDrawers; $i++ ) {
			register_sidebar( array(
				'id'           => 'drawer-' . $i,
				'before_title' => "<h3 class='widgettitle'>",
				'after_title'  => "</h3>\n",
				'name'         => 'Sliding Drawer #' . $i,
				'description'  => 'Change text for this drawer\'s tab in "ProPhoto" > "Customize" > "Sidebar" > "Sliding Drawer Sidebars".',
			 ) );
		}

		register_sidebar( array(
			'id'           => 'footer-spanning-col-top',
			'before_title' => "<h3 class='widgettitle'>",
			'after_title'  => "</h3>\n",
			'name'         => 'Footer Top Spanning Column',
		) );

		for ( $i = 1; $i <= pp::num()->maxFooterWidgetColumns; $i++ ) {
			register_sidebar( array(
				'id'           => 'footer-col-' . $i,
				'before_title' => "<h3 class='widgettitle'>",
				'after_title'  => "</h3>\n",
				'name'         => 'Footer Column #' . $i,
			 ) );
		}

		register_sidebar( array(
			'id'           => 'footer-spanning-col-btm',
			'before_title' => "<h3 class='widgettitle'>",
			'after_title'  => "</h3>\n",
			'name'         => 'Footer Bottom Spanning Column',
		) );
	}


	public static function isRegisteredArea( $sidebar ) {
		if ( $sidebar == 'wp_inactive_widgets' ) {
			return true;
		}
		global $wp_registered_sidebars;
		return isset( $wp_registered_sidebars[$sidebar] );
	}


	public static function parseWidgetHandle( $handle ) {
		$lastDashPos = strrpos( $handle, '-' );
		$handle[$lastDashPos] = '^';
		$parts = explode( '^', $handle );
		return array( $parts[0], $parts[1] );
	}


	public static function exportData() {
		$widgetData = get_option( 'sidebars_widgets' );
		unset( $widgetData['wp_inactive_widgets'] );

		$exportData = array();
		foreach ( $widgetData as $area => $widgets ) {
			if ( is_array( $widgets ) && !empty( $widgets ) ) {
				$exportData[$area] = array();
				foreach ( $widgets as $widgetHandle ) {
					list( $widgetType, $widgetID ) = self::parseWidgetHandle( $widgetHandle );
					$data = self::instanceData( $widgetHandle );
					if ( $widgetType == 'pp-grid' ) {
						$data['instance']['gridOptionData'] = ppOpt::id( 'grid_widget_' . $widgetID );
					}
					$exportData[$area][] = array( $widgetType => $data['instance'] );
				}
			}
		}
		return $exportData;
	}


	public static function placeActivationWidgets( $activationWidgetsData, $restoreOldWidgets = false ) {
		wp_cache_flush();
		$currentWidgets = get_option( 'sidebars_widgets' );

		if ( $restoreOldWidgets ) {
			set_transient( 'pp_sidebars_widgets_preview_safeguard', $currentWidgets, 60*60*3 );
			self::$oldWidgets = array( 'sidebars_widgets' => $currentWidgets, 'added_widget_handles' => array() );
			add_action( 'pp_end_body', 'ppWidgetUtil::restoreWidgets' );
		}

		// move active widgets that are in the way into inactive area
		foreach ( $activationWidgetsData as $area => $widgetsInArea ) {
			if ( !empty( $currentWidgets[$area] ) ) {
				foreach ( $currentWidgets[$area] as $existingWidget ) {
					self::$widgetsProgramaticallyDeactivated = true;
					$currentWidgets['wp_inactive_widgets'][] = $existingWidget;
				}
				$currentWidgets[$area] = array();
			}
		}
		update_option( 'sidebars_widgets', $currentWidgets );
		wp_cache_flush();

		// insert new widgets from design
		foreach ( $activationWidgetsData as $area => $widgetsInArea ) {
			if ( $widgetsInArea == 'empty' ) {
				continue;
			}
			foreach ( $widgetsInArea as $widget ) {
				$widgetType     = reset( array_keys( $widget ) );
				$widgetInstance = reset( $widget );
				if ( isset( $widgetInstance['gridOptionData'] ) ) {
					$gridOptionData = $widgetInstance['gridOptionData'];
					unset( $widgetInstance['gridOptionData'] );
				}
				$addedID = ppWidgetUtil::addWidget( $area, $widgetType, $widgetInstance );
				if ( $restoreOldWidgets ) {
					self::$oldWidgets['added_widget_handles'][] = $widgetType . '-' . $addedID;
				}
				if ( isset( $gridOptionData ) ) {
					ppOpt::update( 'grid_widget_' . str_replace( 'pp-grid-', '', $addedID ), $gridOptionData );
					unset( $gridOptionData );
				}
			}
		}
	}


	public static function restoreWidgets() {
		if ( is_array( self::$oldWidgets ) ) {
			foreach ( self::$oldWidgets['added_widget_handles'] as $addedWidgetHandle ) {
				self::deleteWidget( $addedWidgetHandle );
			}
			update_option( 'sidebars_widgets', self::$oldWidgets['sidebars_widgets'] );
			delete_transient( 'pp_sidebars_widgets_preview_safeguard' );
		}
	}


	public static function updateDesignWidgetData( $data ) {
		$newFormat = array();
		foreach ( $data['active_widgets'] as $widgetArea => $widgets ) {
			$newFormat[$widgetArea] = array();
			foreach ( $widgets as $widgetHandle ) {
				list( $widgetType, $widgetID ) = ppWidgetUtil::parseWidgetHandle( $widgetHandle );
				$newFormat[$widgetArea][] = array( $widgetType => $data['widget_data'][$widgetHandle]['instance'] );
			}
		}
		return $newFormat;
	}


	public static function regenerateStaticFiles() {
		ppStorage::saveCustomizations( ppStorage::WIDGETS_MODIFIED_TRIGGER );
	}


	public static function widgetsPageJsCss() {
		$customImgDataArray = '';
		for ( $i = 1; $i <= pp::num()->maxCustomWidgetImages; $i++ ) {
			$widgetImg = ppImg::id( 'widget_custom_image_' . $i );
			if ( !$widgetImg->exists ){
				 continue;
			}
			$customImgDataArray .= "pp_custom_images['$i'] = '$widgetImg->url';";
			$customImgDataArray .= "pp_custom_images['{$i}-width'] = '$widgetImg->width';";
		}

		$twitterSlidingImgPreviewCSS = '
			.widget-liquid-right .widgets-holder-wrap:nth-child(7) .pp-twitter-slider img {
				max-width: ' . ppSidebar::data()->content_width . 'px; height:auto;
			}
			.widget-liquid-right .widgets-holder-wrap:nth-child(13) .pp-twitter-slider img,
			.widget-liquid-right .widgets-holder-wrap:nth-child(14) .pp-twitter-slider img,
			.widget-liquid-right .widgets-holder-wrap:nth-child(15) .pp-twitter-slider img,
			.widget-liquid-right .widgets-holder-wrap:nth-child(16) .pp-twitter-slider img {
				max-width: ' . ppFooter::columnInfo()->columnWidth . 'px; height:auto;
			}';

		echo NrHtml::script( "pp_custom_images = new Object(); $customImgDataArray " );
		echo NrHtml::script( ppTwitterSlider_Widget::js() );
		echo NrHtml::style( ppTwitterSlider_Widget::css() . $twitterSlidingImgPreviewCSS );
	}
}

