<?php

class ppImportP3 {


	const DONT_SAVE_NEW_DESIGN = false;
	protected static $importRecordsDbName = 'pp_import_records';
	protected static $sidebars;


	public static function uploadedDesign( $in ) {
		if ( isset( $in['design_id'] ) && isset( $in[$in['design_id']] ) ) {
			$designID = $in['design_id'];
			$designName = isset( $in['design_meta'][$designID]['name'] ) ? $in['design_meta'][$designID]['name'] : str_replace( '_', ' ', $designID );
			$designDesc = isset( $in['design_meta'][$designID]['description'] ) ? $in['design_meta'][$designID]['description'] : '';
			if ( @is_array( $in[$designID]['options'] ) && @is_array( $in[$designID]['images'] ) ) {
				$nonDesign = ( isset( $in['non_design'] ) && is_array( $in['non_design'] ) ) ? $in['non_design'] : array();
				return self::design( compact( 'designID', 'designName', 'designDesc' ), $in[$designID], $nonDesign, self::DONT_SAVE_NEW_DESIGN );
			}
		}
		new ppIssue( 'Error reading uploaded P3 design file' );
		return false;
	}


	protected static function design( $meta, $design, $nonDesign = array(), $saveNewDesign = true ) {
		ppUtil::logVar( $design, '2$design' );

		$design = ppImportP3Menu::importMenu( $design, $nonDesign );

		ppUtil::logVar( $design, '3$design after menu import' );

		$designOpts = self::transformOptions( $design['options'], $nonDesign );
		$designImgs = self::transformImgs( $design['images'] );

		ppUtil::logVar( $designOpts, '4$designOpts after transformOptions' );
		ppUtil::logVar( $designImgs, '5$designImgs after transformImgs' );

		foreach ( $designImgs as $img ) {
			self::moveImportImg( $img );
		}

		$importedDesign = new ppDesign( $meta['designID'], array(
			'meta' => array(
				'name' => $meta['designName'],
				'desc' => $meta['designDesc'] . ' (imported from ProPhoto3)',
			),
			'options' => $designOpts,
			'imgs' => $designImgs,
		) );

		ppUtil::logVar( $importedDesign, '6$importedDesign' );

		if ( $saveNewDesign ) {
			ppStorage::saveNewDesign( $importedDesign );
		}

		$importRecords = self::importRecords();
		$importRecords['p4']['imported_p3_designs'][] = $meta['designID'];
		self::updateImportRecords( $importRecords );

		return $importedDesign;
	}


	public static function localDesignByID( $designID, $saveNewDesign = true ) {
		$p3 = self::p3Storage();
		if ( !@is_array( $p3[$designID] ) ) {
			new ppIssue( 'Unable to load data for P3 Design: ' . $designID );
		}
		ppUtil::logVar( $p3, '1 $p3' );

		$designName = $p3['design_meta'][$designID]['name'];
		$designDesc = isset( $p3['design_meta'][$designID]['description'] ) ? $p3['design_meta'][$designID]['description'] : '';

		return self::design( compact( 'designID', 'designName', 'designDesc' ), $p3[$designID], $p3['non_design'], $saveNewDesign );
	}


	public function activeDesign() {
		$p3 = self::p3Storage();
		if ( $p3 && isset( $p3['active_design'] ) && NrUtil::isAssoc( $p3[$p3['active_design']] ) ) {

			// handle sidebar widgets with care when auto-importing
			$activeDesignOptions = $p3[$p3['active_design']]['options'];
			if ( isset( $activeDesignOptions['sidebar'] ) && $activeDesignOptions['sidebar'] == 'false' ) {
				self::$sidebars = get_option( 'sidebars_widgets' );
				if ( isset( self::$sidebars['p3-sidebar'] ) ) {
					foreach ( (array) self::$sidebars['p3-sidebar'] as $widgetHandle ) {
						self::moveWidgetToInactive( $widgetHandle, 'p3-sidebar' );
					}
				}
				update_option( 'sidebars_widgets', self::$sidebars );
			}

			// return design without saving because the db-initialization routine does it
			return self::localDesignByID( $p3['active_design'], self::DONT_SAVE_NEW_DESIGN );
		}
		return false;
	}


	public static function nonDesignOptions() {
		$p3 = self::p3Storage();
		if ( isset( $p3['non_design'] ) && is_array( $p3['non_design'] ) ) {
			foreach ( $p3['non_design'] as $key => $val ) {
				if ( NrUtil::startsWith( $key, 'seo_title_' ) ) {
					$p3['non_design'][$key] = str_replace(
						array( '%search%',       '%archive_title%', '%category_title%' ),
						array( '%search_query%', '%archive_date%',  '%category_name%' ),
						$val
					);
				}
			}
			$nonDesignOpts = self::transformOptions( $p3['non_design'] );
			ppUtil::logVar( $nonDesignOpts, "p3NonDesign" );
			ppOpt::updateMultiple( $nonDesignOpts );
		}

		if ( isset( $p3['non_design']['pathfixer'] ) && $p3['non_design']['pathfixer'] == 'on' && isset( $p3['non_design']['pathfixer_old'] ) ) {
			$fixUrls = (array) explode( ',', $p3['non_design']['pathfixer_old'] );
			ppPathfixer::registerUrls( $fixUrls );
		}
	}


	protected static function transformImgs( $in ) {
		$out = array();
		$transformedImgIDs = self::transformedImgIDs();
		foreach ( $in as $imgID => $imgFilename ) {
			if ( in_array( strtolower( NrUtil::fileExt( $imgFilename ) ), array( 'jpg', 'jpeg', 'gif', 'png', 'ico', 'mp3', 'swf' ) ) ) {
				if ( isset( $transformedImgIDs[$imgID] ) ) {
					$out[$transformedImgIDs[$imgID]] = $imgFilename;
				} else {
					$out[$imgID] = $imgFilename;
				}
			}
		}
		return $out;
	}


	protected static function transformOptions( $in, $nonDesign = array() ) {
		foreach ( (array) $nonDesign as $nonDesignKey => $nonDesignVal ) {
			if ( in_array( $nonDesignKey, self::formerlyNonDesignOptions() ) ) {
				$in[$nonDesignKey] = $nonDesignVal;
			}
		}

		$out = array();
		$transformed = self::transformedOptions();

		foreach ( $in as $optName => $optVal ) {

			if ( !in_array( $optName, self::eliminatedOptions() ) ) {

				if ( isset( $transformed[$optName][$optVal] ) ) {
					$optVal = $transformed[$optName][$optVal];
				}

				if ( isset( $transformed[$optName]['p4name'] ) ) {
					$optName = $transformed[$optName]['p4name'];
				}

				foreach ( self::transformedFontKeys() as $oldFontKey => $newFontKey ) {
					if ( NrUtil::startsWith( $optName, $oldFontKey ) ) {
						$optName = str_replace( $oldFontKey, $newFontKey, $optName );
					}
				}

				if ( !NrUtil::isIn( 'value=', $optName ) ) {
					$out[$optName] = preg_match( '/_menu_item_[0-9]+$/', $optName ) ? $optVal : stripslashes( $optVal );
				}
			}
		}

		if ( isset( $in['override_css'] ) ) {
			$out['override_css'] = self::transformHtmlIdentifiers( $in['override_css'] );
		}

		return $out;
	}


	public static function moveImportImg( $filename ) {
		$filepath = pp::fileInfo()->wpUploadPath . '/p3/images/' . $filename;
		if ( !file_exists( $filepath ) ) {
			$filepath = preg_replace( '/\/themes\/[^\/]*\//', '/themes/prophoto3/', TEMPLATEPATH . '/images/' . $filename );
		}
		if ( !file_exists( $filepath ) ) {
			return false;
		}
		return ppUtil::moveFile( $filepath, pp::fileInfo()->imagesFolderPath . '/' . $filename );
	}


	public static function unimportedDesigns() {
		$unimported = array();
		$p3 = self::p3Storage();
		if ( $p3 ) {
			$importedDesigns = self::importedDesigns();
			foreach ( (array) $p3['designs'] as $designID ) {
				if ( !in_array( $designID, $importedDesigns ) && @isset( $p3['design_meta'][$designID]['name'] ) ) {
					$unimported[$designID] = $p3['design_meta'][$designID]['name'];
				}
			}
		}
		return $unimported;
	}


	public static function deleteImportRecords() {
		delete_option( self::$importRecordsDbName );
	}


	public static function contactFormLog() {
		if ( $p3Log = get_option( 'p3theme_contact_log') ) {
			$newLog = get_option( pp::wp()->dbContactLog );
			if ( is_array( $newLog ) ) {
				$log = array_merge( $newLog, (array) $p3Log );
			} else {
				$log = $p3Log;
			}
			if ( false === $newLog ) {
				add_option( pp::wp()->dbContactLog, '', '', 'no'  );
			}
			update_option( pp::wp()->dbContactLog, $log );
		}
	}


	protected static function importedDesigns() {
		$importRecords = self::importRecords();
		return $importRecords['p4']['imported_p3_designs'];
	}


	protected static function importRecords() {
		$allImportRecords = ppUtil::storedArray( self::$importRecordsDbName );
		if ( false === $allImportRecords || !isset( $allImportRecords['p4'] ) ) {
			$p4records = array( 'p4' => array( 'imported_p3_designs' => array() ) );
			if ( is_array( $allImportRecords ) ) {
				$allImportRecords = array_merge( $allImportRecords, $p4records );
			} else {
				$allImportRecords = $p4records;
			}
		}
		return $allImportRecords;
	}


	protected static function updateImportRecords( $update ) {
		ppUtil::updateStoredArray( self::$importRecordsDbName, $update );
	}


	public static function widgets() {
		ppWidgetUtil::registerAreas();
		self::$sidebars = get_option( 'sidebars_widgets' );

		self::changeSidebarName( 'p3-contact-form', 'contact-form' );
		self::changeSidebarName( 'p3-sidebar', 'fixed-sidebar' );

		foreach ( self::$sidebars as $sidebar => $widgetHandles ) {
			if ( $sidebar != 'wp_inactive_widgets' && $sidebar != 'array_version' && is_array( $widgetHandles ) ) {
				foreach ( $widgetHandles as $widgetHandle ) {
					self::updateWidget( $widgetHandle, $sidebar );
				}
			}
		}
		update_option( 'sidebars_widgets', self::$sidebars );

		/* safegaurd to prevent any overwriting of imported P3 widgets */
		remove_theme_mod( 'sidebars_widgets' );
	}


	public static function transformExportedWidgetData( $data ) {
		ppUtil::logVar( $data, 'untransformed_p3_widget_data' );

		if ( isset( $data['active_widgets']['p3-sidebar'] ) ) {
			$data['active_widgets']['fixed-sidebar'] = $data['active_widgets']['p3-sidebar'];
			unset( $data['active_widgets']['p3-sidebar'] );
		}
		if ( isset( $data['active_widgets']['p3-contact-form'] ) ) {
			$data['active_widgets']['contact-form'] = $data['active_widgets']['p3-contact-form'];
			unset( $data['active_widgets']['p3-contact-form'] );
		}

		$transform = self::transformedWidgetNames();

		foreach ( $data['active_widgets'] as $areaName => $widgetsInArea ) {
			foreach ( $widgetsInArea as $index => $widgetHandle ) {
				list( $type, $id ) = ppWidgetUtil::parseWidgetHandle( $widgetHandle );
				if ( isset( $transform[$type] ) ) {
					$newHandle = $transform[$type] . '-' . $id;
					$data['widget_data'][$newHandle] = array(
						'id'       => $id,
						'type'     => $transform[$type],
						'instance' => $data['widget_data'][$widgetHandle]['widget_data'],
					);
					unset( $data['widget_data'][$widgetHandle] );
					$data['active_widgets'][$areaName][$index] = $newHandle;
				} else {
					$data['widget_data'][$widgetHandle] = array(
						'id'       => $id,
						'type'     => $type,
						'instance' => $data['widget_data'][$widgetHandle]['widget_data'],
					);
				}
			}
		}

		$newFormat = ppWidgetUtil::updateDesignWidgetData( $data );

		ppUtil::logVar( $newFormat, 'transformed_p3_widget_data' );

		return $newFormat;
	}


	protected static function updateWidget( $handle, $sidebar ) {

		$transform = self::transformedWidgetNames();

		list( $type, $ID ) = ppWidgetUtil::parseWidgetHandle( $handle );

		if ( in_array( $type, array_keys( $transform ) ) ) {
			self::moveWidgetToInactive( $handle, $sidebar );

			$widget = ppWidgetUtil::instanceData( $handle );
			$importedHandle = $transform[$type] . '-' . ppWidgetUtil::addWidget( $sidebar, $transform[$type], $widget['instance'] );

			list( $importedType, $importedID ) = ppWidgetUtil::parseWidgetHandle( $importedHandle );
			if ( $importedType == 'pp-facebook-likebox' && isset( $widget['instance']['box_code'] ) ) {
				ppLegacy::updateLikeBoxWidgetInstance( $widget['instance']['box_code'], $importedType, $importedID );
			}

			self::$sidebars[$sidebar][] = $importedHandle;
		}

		// transfer custom widget imgs
		if ( $type == 'p3-custom-icon' || ( $type == 'p3-twitter-slider' && is_numeric( $widget['instance']['image'] ) ) ) {
			$index = ( $type == 'p3-custom-icon' ) ? 'number' : 'image';
			$p3 = get_option( 'p3theme_options' );
			if ( $p3 ) {
				$imgId = 'widget_custom_image_' . $widget['instance'][$index];
				$imgFilename = $p3[$p3['active_design']]['images'][$imgId];
				self::moveImportImg( $imgFilename );
				ppImg::update( $imgId, $imgFilename );
			}
		}
	}


	protected static function moveWidgetToInactive( $handle, $sidebar ) {
		$updatedSidebar = array();
		foreach ( self::$sidebars[$sidebar] as $widgetHandle ) {
			if ( $widgetHandle != $handle ) {
				$updatedSidebar[] = $widgetHandle;
			}
		}

		self::$sidebars[$sidebar] = $updatedSidebar;
		self::$sidebars['wp_inactive_widgets'][] = $handle;
	}


	protected static function changeSidebarName( $old, $new ) {
		if ( isset( self::$sidebars[$old] ) ) {
			if ( !isset( self::$sidebars[$new] ) ) {
				self::$sidebars[$new] = self::$sidebars[$old];
			} else {
				self::$sidebars[$new] = array_merge( self::$sidebars[$new], self::$sidebars[$old] );
			}
			unset( self::$sidebars[$old] );
		}
	}


	public static function gallery( $match, $passedPost = null ) {
		if ( class_exists( 'ppImportP3Menu', false ) ) {
			// keep going
		} else if ( is_admin() || ( !is_home() && !is_singular() && !is_archive() && !is_category() && !is_tag() ) ) {
			// this is safeguard against plugins triggering imports for post revisions
			return '';
		}

		$p3PlaceholderImg = $match[0];

		$post = $passedPost ? $passedPost : ppPost::fromGlobal();
		if ( !$post ) {
			return '';
		}

		self::logGalleryImport( $post->id(), $match, 'raw $match input' );
		self::logGalleryImport( $post->id(), $p3PlaceholderImg, '$p3PlaceholderImg' );
		self::logGalleryImport( $post->id(), $post, '$post object' );

		if ( NrUtil::isIn( 'p3-flash-gallery-holder', $p3PlaceholderImg ) ) {
			$type = 'slideshow';
		} else if ( NrUtil::isIn( 'p3-lightbox-gallery-holder', $p3PlaceholderImg ) ) {
			$type = 'lightbox';
		} else {
			return '';
		}

		self::logGalleryImport( $post->id(), $type, '$type' );

		$attachedImgIDs = self::attachedImgIDs( $post );
		self::logGalleryImport( $post->id(), $attachedImgIDs, '$attachedImgIDs' );

		$slideshowData = array_map( 'urldecode', (array) unserialize( get_post_meta( $post->id(), 'p3_flash_gal_info', true ) ) );
		$title       = !@empty( $slideshowData['title'] )    ? $slideshowData['title']    : $post->title();
		$subtitle    = !@empty( $slideshowData['subtitle'] ) ? $slideshowData['subtitle'] : '';
		self::logGalleryImport( $post->id(), compact( 'slideshowData', 'title', 'subtitle' ), 'data' );

		$galleryID = intval( '10' . str_pad( $post->id(), 6, '0', STR_PAD_LEFT ) . rand( 10, 99 ) );

		if ( 2147483647 == $galleryID ) {
			new ppIssue( 'Gallery ID integer overflow' );
			$galleryID = rand( 100000, 999999 );
		}

		$gallery = ppGallery::create( array(
			'title'    => stripslashes( $title ),
			'subtitle' => stripslashes( $subtitle ),
			'imgs'     => $attachedImgIDs
		), $galleryID );
		self::logGalleryImport( $post->id(), $galleryID, '$galleryID' );
		self::logGalleryImport( $post->id(), $gallery, '$gallery' );

		if ( $type == 'slideshow' ) {
			if ( isset( $slideshowData['proofurl'] ) && !empty( $slideshowData['proofurl'] ) ) {
				$gallery->slideshowOption( 'shoppingCartUrl', $slideshowData['proofurl'] );
			}
			if ( isset( $slideshowData['speed'] ) && !empty( $slideshowData['speed'] ) ) {
				$gallery->slideshowOption( 'holdTime', floatval( $slideshowData['speed'] ) );
			}
			if ( isset( $slideshowData['auto_start'] ) && ppUtil::formatVal( $slideshowData['auto_start'], 'bool' ) ) {
				$gallery->slideshowOption( 'autoStart', true );
			}
			if ( isset( $slideshowData['mp3'] ) && !empty( $slideshowData['mp3'] ) ) {
				$gallery->slideshowOption( 'musicFile', $slideshowData['mp3'] );
			}

		} else {
			$lightboxData = array_map( 'urldecode', (array) unserialize( get_post_meta( $post->id(), 'p3_lightbox_gal_info', true ) ) );
			if ( isset( $lightboxData['thumb_size'] ) && is_numeric( $lightboxData['thumb_size'] ) ) {
				$gallery->lightboxOption( 'thumb_size', intval( $lightboxData['thumb_size'] ) );
			}
			if ( isset( $lightboxData['show_main_image'] ) && !empty( $lightboxData['show_main_image'] ) ) {
				$gallery->lightboxOption( 'show_main_image', ppUtil::formatVal( $lightboxData['show_main_image'], 'bool' ) );
			}
		}

		$gallery->save();
		if ( $passedPost ) {
			return array( 'id' => $gallery->id(), 'title' => $gallery->title() );
		}

		self::logGalleryImport( $post->id(), $gallery, 'updated $gallery object, after imported options folded in' );

		$modifiedImgTag  = str_replace( 'p3-placeholder', 'p3-placeholder p3-gallery-imported', $p3PlaceholderImg );
		$modifiedImgTag .= ppGalleryAdmin::galleryPlaceholderMarkup( $gallery->id(), $type );
		self::logGalleryImport( $post->id(), $modifiedImgTag, '$modifiedImgTag' );

		preg_match( "/<img[^>]*p3-placeholder[^>]*>/i", $post->rawContent(), $rawPlaceholderMatch );
		$modifiedContent = str_replace( reset( $rawPlaceholderMatch ), $modifiedImgTag, $post->rawContent() );

		self::logGalleryImport( $post->id(), $post->rawContent(), '$post->rawContent()' );
		self::logGalleryImport( $post->id(), $rawPlaceholderMatch, '$rawPlaceholderMatch' );
		self::logGalleryImport( $post->id(), $modifiedContent, '$modifiedContent' );

		// safeguards
		if ( !NrUtil::isIn( "id=\"pp-{$type}-" . $gallery->id() . '"', $modifiedContent ) || !NrUtil::isIn( "-gal-placeholder.gif", $modifiedContent ) ) {
			$gallery->delete();
			return '';
		}

		$wp_update_post_return = wp_update_post( array( 'ID' => $post->id(), 'post_content' => $modifiedContent ) );
		$gallery->associateWithArticle( $post->id() );

		self::logGalleryImport( $post->id(), compact( 'wp_update_post_return', 'wp_add_post_meta_return' ), 'result of wp update' );
		self::logGalleryImport( $post->id(), $_SERVER, '$_SERVER' );
		self::logGalleryImport( $post->id(), ppUtil::pageType(), 'ppUtil::pageType()' );

		return $modifiedImgTag;
	}


	protected static function logGalleryImport( $ID, $val, $name ) {
		if ( ppUtil::unitTesting() ) {
			return;
		}
		static $log = array();
		if ( !isset( $log[$ID] ) ) {
			$log[$ID] = '';
		}
		if ( is_array( $val ) && isset( $val['HTTP_COOKIE'] ) ) {
			unset( $val['HTTP_COOKIE'] );
		}
		$dump = is_object( $val ) ? print_r( $val, true ) : NrUtil::getVarDump( $val );
		$log[$ID] .= "$name\n------------------\n$dump\n\n\n";
		if ( $name == 'ppUtil::pageType()' ) {
			$filepath = pp::fileInfo()->issuesFolderPath . '/_galleryImportLog_' . $ID .  '.txt';
			@NrUtil::writeFile( $filepath, $log[$ID] );
		}
	}


	protected static function attachedImgIDs( ppPost $post ) {
		$attachedImgs = get_children( array(
			'post_parent' => $post->id(),
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'order' => 'ASC',
			'orderby' => 'menu_order ID'
		) );
		$attachedImgIDs = array();
		foreach ( (array) $attachedImgs as $imgID => $imgPostObj ) {
			if ( !preg_match( '/nogallery/i', $imgPostObj->post_content ) ) {
				$attachedImgIDs[] = $imgID;
			}
		}
		return $attachedImgIDs;
	}


	public static function isP3User() {
		return ( self::p3storage() );
	}


	public static function p3Storage() {
		return is_array( $p3 = get_option( 'p3theme_storage' ) ) ? $p3 : false;
	}


	protected static function transformHtmlIdentifiers( $in ) {
		$transformedIdentifiers = array(
			'.post-header'     => '.article-header',
			'.entry-title'     => '.article-title',
			'.post-title-wrap' => '.article-title-wrap',
			'.post-wrap-inner' => '.article-wrap-inner',
			'.post-footer'     => '.article-footer',
			'.entry-content'   => '.article-content',
			'.post-date'       => '.article-date',
			'.post-wrap'       => '.article-wrap',
			'#post-'           => '#article-',
			'.entry-meta'      => '.article-meta',
			'.postmeta'        => '.article-meta-item',
			'#topnav '         => 'ul.primary-nav-menu ',
			'#topnav{'         => 'ul.primary-nav-menu{',
			'#topnav-wrap'     => '#primary-nav',
			'.widget_p3-'      => '.widget_pp-',
			'../images/'       => ppUtil::urlFromPath( pp::fileInfo()->p3FolderPath . 'images/' ),
		);
		return str_replace( array_keys( $transformedIdentifiers ), array_values( $transformedIdentifiers ), $in );
	}


	protected static function eliminatedOptions() {
		static $eliminatedOptions = array();
		if ( empty( $eliminatedOptions ) ) {
			$eliminatedOptions = array_merge( ppImportP3Menu::eliminatedOptions(), array(
				'fb_set_post_image',
				'activation_time',
				'twitter_num_id',
				'flash_gal_main_corners',
				'flash_gal_fallback',
				'flash_gal_start_full_screen',
				'flash_gal_thumb_corners',
				'flash_gal_thumb_effect',
				'flash_gal_thumb_effect_speed',
				'flash_gal_thumb_effect_over',
				'flash_gal_thumb_effect_out',
				'contactform_yesno',
				'des_export_widgets',
				'cache_buster',
				'pathfixer',
				'pathfixer_old',
				'insert_all_align',
				'payer_email',
				'menuorder',
				'contact_text',
				'txn_id',
				'svn',
			) );
		}
		return $eliminatedOptions;
	}


	protected static function formerlyNonDesignOptions() {
		return array(
			'image_protection',
			'excerpts_on_home',
			'excerpts_on_archive',
			'excerpts_on_category',
			'excerpts_on_tag',
			'excerpts_on_author',
			'excerpts_on_search',
			'show_excerpt_image',
			'dig_for_excerpt_image',
			'excerpt_image_size',
			'comments_show_on_archive',
			'comments_on_archive_start_hidden',
			'reverse_comments',
			'custom_copyright',
		);

	}


	protected static function transformedWidgetNames() {
		return array(
			'p3-custom-icon'        => 'pp-custom-icon',
			'p3-facebook-likebox'   => 'pp-facebook-likebox',
			'p3-social-media-icons' => 'pp-social-media-icon',
			'p3-subscribe-by-email' => 'pp-feedburner-subscribe-by-email',
			'p3-text'               => 'pp-text',
			'p3-twitter-html'       => 'pp-twitter-html-badge',
			'p3-twitter-slider'     => 'pp-sliding-twitter',
			'p3-twitter-com-widget' => 'pp-twitter-com',
		);
	}


	protected static function transformedFontKeys() {
		return array(
			'comments_comment_timestamp' => 'comment_timestamp',
			'comments_comment_link' => 'comment_author_link',
			'comments_post_interaction_link' => 'comments_header_post_interaction_link',
			'flash_gal_title' => 'slideshow_title',
			'flash_gal_subtitle' => 'slideshow_subtitle',
			'nav_link' => 'primary_nav_menu_link',
		);
	}


	protected static function transformedImgIDs() {
		return array(
			'comments_comment_outer_bg'     => 'comments_body_area_bg',
			'comments_emailafriend_icon'    => 'comments_header_emailafriend_link_icon',
			'comments_emailafriend_image'   => 'comments_header_emailafriend_link_image',
			'comments_linktothispost_icon'  => 'comments_header_linktothispost_link_icon',
			'comments_linktothispost_image' => 'comments_header_linktothispost_link_image',
			'comments_addacomment_icon'     => 'comments_header_addacomment_link_icon',
			'comments_addacomment_image'    => 'comments_header_addacomment_link_image',
			'flash_gal_logo'                => 'slideshow_splash_screen_logo',
			'iphone_webclip_icon'           => 'apple_touch_icon',
			'nav_bg'                        => 'primary_nav_menu_bg',
		);
	}


	protected static function transformedOptions() {
		$t = array();
		$t['flash_speed']['p4name'] = 'masthead_slideshow_hold_time';
		$t['flash_order']['p4name'] = 'masthead_slideshow_image_order';
		$t['flash_fadetime']['p4name'] = 'masthead_slideshow_transition_time';
		$t['flash_loop']['p4name'] = 'masthead_slideshow_loop_images';
		$t['flash_loop']['yes'] = true;
		$t['flash_loop']['no'] = false;
		$t['flashheader_bg_color']['p4name'] = 'masthead_slideshow_bg_color';
		$t['flashheader_bg_color_bind']['p4name'] = 'masthead_slideshow_bg_color_bind';
		$t['flashheader_transition_effect']['p4name'] = 'masthead_slideshow_transition_type';
		$t['flashheader_transition_effect']['1'] = 'crossfade';
		$t['flashheader_transition_effect']['2'] = 'fade';
		$t['flashheader_transition_effect']['3'] = 'slide';
		$t['flashheader_transition_effect']['4'] = 'topslide';
		$t['flashheader_transition_effect']['5'] = 'steadyslide';
		$t['masthead_on_index']['p4name'] = 'masthead_on_home';
		$t['masthead_on_pages']['p4name'] = 'masthead_on_page';
		$t['excerpts_on_index']['p4name'] = 'excerpts_on_home';
		$t['bio_index']['p4name'] = 'bio_home';
		$t['bio_pages']['p4name'] = 'bio_page';
		$t['seo_title_front']['p4name'] = 'seo_title_front_page';
		$t['seo_title_post']['p4name'] = 'seo_title_single';
		$t['sidebar_on_index']['p4name'] = 'sidebar_on_home';
		$t['flash_gal_slideshow_transition_effect']['p4name']    = 'slideshow_transition_type';
		$t['flash_gal_slideshow_transition_effect']['crossFade'] = 'crossfade';
		$t['flash_gal_slideshow_transition_effect']['jump']      = 'fade';
		$t['flash_gal_disable_slideshow_timer']['p4name']        = 'slideshow_show_timer';
		$t['flash_gal_disable_slideshow_timer']['true']          = 'false';
		$t['flash_gal_disable_slideshow_timer']['false']         = 'true';
		$t['flash_gal_slideshow_transition_speed']['p4name']     = 'slideshow_transition_time';
		$t['flash_gal_loop_slideshow']['p4name']          = 'slideshow_loop_show';
		$t['flash_gal_overlay_opacity']['p4name']         = 'slideshow_splash_screen_opacity';
		$t['flash_gal_overlay_color']['p4name']           = 'slideshow_splash_screen_color';
		$t['flash_gal_overlay_color_bind']['p4name']      = 'slideshow_splash_screen_color_bind';
		$t['flash_gal_overlay_height']['p4name']          = 'slideshow_splash_screen_height';
		$t['flash_gal_filmstrip_bg_color']['p4name']      = 'slideshow_controls_bg_color';
		$t['flash_gal_filmstrip_opacity']['p4name']       = 'slideshow_controls_bg_opacity';
		$t['flash_gal_icon_opacity']['p4name']            = 'slideshow_btns_opacity';
		$t['flash_gal_auto_start']['p4name']              = 'slideshow_start_playing';
		$t['flash_gal_slideshow_duration']['p4name']      = 'slideshow_hold_time';
		$t['flash_gal_filmstrip_overlay']['p4name']       = 'slideshow_controls_overlaid';
		$t['flash_gal_filmstrip_autohide']['p4name']      = 'slideshow_controls_autohide';
		$t['flash_gal_filmstrip_autohide_timeout']['p4name'] = 'slideshow_controls_autohide_time';
		$t['flash_gal_filmstrip_position']['p4name']      = 'slideshow_controls_position';
		$t['flash_gal_bg_color']['p4name']                = 'slideshow_bg_color';
		$t['flash_gal_button_color']['p4name']            = 'slideshow_btns_color';
		$t['flash_gal_disable_full_screen']['p4name']     = 'slideshow_disable_full_screen';
		$t['flash_gal_thumb_padding']['p4name']           = 'slideshow_thumb_padding';
		$t['flash_gal_thumb_size']['p4name']              = 'slideshow_thumb_size';
		$t['flash_gal_thumb_border_width']['p4name']      = 'slideshow_thumb_border_width';
		$t['flash_gal_thumb_border_color']['p4name']      = 'slideshow_thumb_border_color';
		$t['flash_gal_thumb_effect_out']['p4name']        = 'slideshow_thumb_opacity';
		$t['flash_gal_thumb_effect_over']['p4name']       = 'slideshow_active_thumb_opacity';
		$t['flash_gal_mp3_autostart']['p4name']           = 'slideshow_mp3_autostart';
		$t['flash_gal_mp3_loop']['p4name']                = 'slideshow_mp3_loop';
		$t['pathfixer_old']['p4name'] = 'pathfixer_old_urls';
		$t['metakeywords']['p4name'] = 'seo_meta_keywords';
		$t['metadesc']['p4name'] = 'seo_meta_desc';
		$t['feed_only_thumbs']['p4name'] = 'feed_thumbnail_type';
		$t['postdate']['p4name'] = 'postdate_display';
		$t['catprelude']['p4name'] = 'category_list_prepend';
		$t['catdivider']['p4name'] = 'category_list_divider';
		$t['tags_on_index']['p4name'] = 'tags_on_home';
		$t['displaytime']['p4name'] = 'show_post_published_time';
		$t['tagged']['p4name'] = 'tag_list_prepend';
		$t['tag_sep']['p4name'] = 'tag_list_divider';
		$t['masthead_border_bottom']['p4name'] = 'masthead_btm_border';
		$t['masthead_border_top']['p4name'] = 'masthead_top_border';
		$t['nav_next_align']['p4name'] = 'newer_posts_link_align';
		$t['nav_previous_align']['p4name'] = 'older_posts_link_align';
		$t['nav_next']['p4name'] = 'newer_posts_link_text';
		$t['nav_previous']['p4name'] = 'older_posts_link_text';
		$t['moretext']['p4name'] = 'read_more_link_text';
		$t['excerpt_image_size']['full'] = 'fullsize';
		$t['ga_checksum']['p4name'] = 'link_removal_verified_hash';
		$t['ga_verifysum']['p4name'] = 'link_removal_txn_id';
		$t['statcounter_code']['p4name'] = 'statcounter_analytics_code';
		$t['comments_open_closed']['p4name'] = 'comments_on_home_start_hidden';
		$t['comments_open_closed']['open'] = 'false';
		$t['comments_open_closed']['closed'] = 'true';
		$t['archive_comments_show_hide']['p4name'] = 'comments_on_archive_start_hidden';
		$t['archive_comments_show_hide']['show'] = 'false';
		$t['archive_comments_show_hide']['hide'] = 'true';
		$t['comments_show_postauthor']['p4name'] = 'comments_header_show_article_author';
		$t['comment_author_link_blank']['p4name'] = 'comment_author_link_target';
		$t['comment_author_link_blank']['blank'] = '_blank';
		$t['comment_author_link_blank'][''] = '_self';
		$t['gravatars']['p4name'] = 'comments_show_avatars';
		$t['gravatars']['on'] = 'true';
		$t['gravatars']['off'] = 'false';
		$t['gravatar_size']['p4name'] = 'comment_avatar_size';
		$t['gravatar_align']['p4name'] = 'comment_avatar_align';
		$t['gravatar_padding']['p4name'] = 'comment_avatar_padding';
		$t['comments_moderation_text']['p4name'] = 'comment_awaiting_moderation_text';
		$t['translate_comments_email']['p4name']   = 'translate_comment_form_email_label';
		$t['translate_comments_name']['p4name']    = 'translate_comment_form_author_label';
		$t['translate_comments_website']['p4name'] = 'translate_comment_form_url_label';
		$t['translate_comments_error_message']['p4name'] = 'translate_comment_form_error_message';
		$t['translate_comments_comment']['p4name'] = 'translate_comment_form_comment_text_label';
		$t['translate_comments_button']['p4name'] = 'translate_comment_form_submit_button_label';
		$t['comment_meta_display']['p4name'] = 'comment_meta_position';
		$t['comments_comment_alt_bg']['p4name'] = 'comment_alt_bg_color';
		$t['comments_comment_alt_bg_bind']['p4name'] = 'comment_alt_bg_color_bind';
		$t['comments_comment_byauthor_bg']['p4name'] = 'comment_byauthor_bg_color';
		$t['comments_comment_outer_bg_img_repeat']['p4name'] = 'comments_body_area_bg_img_repeat';
		$t['comments_comment_outer_bg_img_position']['p4name'] = 'comments_body_area_bg_img_position';
		$t['comments_comment_outer_bg_img_attachment']['p4name'] = 'comments_body_area_bg_img_attachment';
		$t['comments_comment_outer_bg_color']['p4name'] = 'comments_body_area_bg_color';
		$t['comments_comment_outer_bg_color_bind']['p4name'] = 'comments_body_area_bg_color_bind';
		$t['comments_comment_font_size']['p4name'] = 'comment_text_and_link_font_size';
		$t['comments_comment_font_color']['p4name'] = 'comment_text_and_link_font_color';
		$t['comments_comment_font_color_bind']['p4name'] = 'comment_text_and_link_font_color_bind';
		$t['comments_comment_font_weight']['p4name'] = 'comment_text_and_link_font_weight';
		$t['comments_comment_font_style']['p4name'] = 'comment_text_and_link_font_style';
		$t['comments_comment_font_family']['p4name'] = 'comment_text_and_link_font_family';
		$t['comments_comment_text_transform']['p4name'] = 'comment_text_and_link_text_transform';
		$t['comments_comment_line_height']['p4name'] = 'comment_text_and_link_line_height';
		$t['comment_vertical_padding']['p4name'] = 'comment_tb_padding';
		$t['comment_horizontal_padding']['p4name'] = 'comment_lr_padding';
		$t['comments_body_lr_margin']['p4name'] = 'comments_body_area_lr_margin';
		$t['comments_body_tb_margin']['p4name'] = 'comments_body_area_tb_margin';
		$t['comment_vertical_separation']['p4name'] = 'comment_tb_margin';
		$t['comments_comment_border_onoff']['p4name'] = 'comment_bottom_border_onoff';
		$t['comments_comment_border_width']['p4name'] = 'comment_bottom_border_width';
		$t['comments_comment_border_style']['p4name'] = 'comment_bottom_border_style';
		$t['comments_comment_border_color']['p4name'] = 'comment_bottom_border_color';
		$t['comments_comment_alt_bg_color']['p4name'] = 'comment_alt_bg_color';
		$t['comments_comment_alt_bg_color_bind']['p4name'] = 'comment_alt_bg_color_bind';
		$t['comments_comment_alt_text_color']['p4name'] = 'comment_alt_font_color';
		$t['comments_comment_alt_text_color_bind']['p4name'] = 'comment_alt_font_color_bind';
		$t['comments_comment_alt_link_color']['p4name'] =      'comment_alt_link_font_color';
		$t['comments_comment_alt_link_color_bind']['p4name'] = 'comment_alt_link_font_color_bind';
		$t['comments_timestamp_alt_color']['p4name'] = 'comment_alt_timestamp_font_color';
		$t['comments_timestamp_alt_color_bind']['p4name'] = 'comment_alt_timestamp_font_color_bind';
		$t['comments_comment_byauthor_bg_color']['p4name'] = 'comment_byauthor_bg_color';
		$t['comments_comment_byauthor_bg_color_bind']['p4name'] = 'comment_byauthor_bg_color_bind';
		$t['comments_comment_byauthor_text_color']['p4name'] = 'comment_byauthor_font_color';
		$t['comments_comment_byauthor_text_color_bind']['p4name'] = 'comment_byauthor_font_color_bind';
		$t['comments_comment_byauthor_link_color']['p4name'] = 'comment_byauthor_link_font_color';
		$t['comments_comment_byauthor_link_color_bind']['p4name'] = 'comment_byauthor_link_font_color_bind';
		$t['comments_timestamp_byauthor_color']['p4name'] = 'comment_byauthor_timestamp_font_color';
		$t['comments_timestamp_byauthor_color_bind']['p4name'] = 'comment_byauthor_timestamp_font_color_bind';
		$t['comments_moderation_style']['p4name'] = 'comment_awaiting_moderation_font_style';
		$t['comments_moderation_color']['p4name'] = 'comment_awaiting_moderation_font_color';
		$t['comments_moderation_color_bind']['p4name'] = 'comment_awaiting_moderation_font_color_bind';
		$t['comments_moderation_alt_color']['p4name'] =      'comment_alt_awaiting_moderation_font_color';
		$t['comments_moderation_alt_color_bind']['p4name'] = 'comment_alt_awaiting_moderation_font_color_bind';
		$t['comments_post_interact_spacing']['p4name'] = 'comments_header_post_interact_link_spacing';
		$t['comments_header_addacomment_link_text']['p4name'] = 'comments_header_addacomment_link_text';
		$t['comments_linktothispost']['p4name'] = 'comments_header_linktothispost_link_include';
		$t['comments_header_linktothispost_link_text']['p4name'] = 'comments_header_linktothispost_link_text';
		$t['comments_emailafriend']['p4name'] = 'comments_header_emailafriend_link_include';
		$t['comments_emailafriend_text']['p4name'] = 'comments_header_emailafriend_link_text';
		$t['comments_emailafriend_body']['p4name'] = 'comments_header_emailafriend_link_body';
		$t['comments_emailafriend_subject']['p4name'] = 'comments_header_emailafriend_link_subject';
		$t['archive_comments']['p4name'] = 'comments_show_on_archive';
		$t['archive_comments']['on'] = 'true';
		$t['archive_comments']['off'] = 'false';
		$t['comments_body_scroll_height']['p4name'] = 'comments_scrollbox_height';
		$t['comments_body_scroll_index']['p4name'] = 'comments_in_scrollbox_on_home';
		$t['comments_body_scroll_index']['fixed'] = 'true';
		$t['comments_body_scroll_index']['all'] = 'false';
		$t['comments_body_scroll_singular']['p4name'] = 'comments_in_scrollbox_on_singular';
		$t['comments_body_scroll_singular']['fixed'] = 'true';
		$t['comments_body_scroll_singular']['all'] = 'false';
		$t['comments_lr_margin_switch']['p4name'] = 'comments_area_lr_margin_control';
		$t['comments_lr_margin']['p4name'] = 'comments_area_lr_margin';
		$t['comments_comment_bg_color']['p4name'] = 'comment_bg_color';
		$t['comments_comment_bg_color_bind']['p4name'] = 'comment_bg_color_bind';
		$t['comments_ajax_add']['p4name'] = 'comments_ajax_adding_enabled';
		$t['sidebar']['p4name'] = 'sidebar';
		$t['sidebar']['false'] = 'left';
		$t['blog_border_topbottom']['p4name'] = 'blog_border_visible_sides';
		$t['blog_border_topbottom']['yes'] = 'all_four_sides';
		$t['blog_border_topbottom']['no'] = 'left_and_right_only';
		$t['sponsors']['p4name'] = 'show_ad_banners';
		$t['sponsors']['on'] = 'true';
		$t['sponsors']['off'] = 'false';
		$t['sponsors_side_margins']['p4name'] = 'ad_banners_area_lr_margin';
		$t['sponsors_img_margin_right']['p4name'] = 'ad_banners_margin_right';
		$t['sponsors_img_margin_below']['p4name'] = 'ad_banners_margin_btm';
		$t['sponsors_border_color']['p4name'] = 'ad_banners_border_color';
		$t['nav_border_top']['p4name'] = 'primary_nav_menu_border_top_onoff';
		$t['nav_border_bottom']['p4name'] = 'primary_nav_menu_border_bottom_onoff';
		$t['nav_border_top_color']['p4name'] = 'primary_nav_menu_top_border_color';
		$t['nav_border_top_width']['p4name'] = 'primary_nav_menu_top_border_width';
		$t['nav_border_top_style']['p4name'] = 'primary_nav_menu_top_border_style';
		$t['nav_border_bottom_color']['p4name'] = 'primary_nav_menu_btm_border_color';
		$t['nav_border_bottom_width']['p4name'] = 'primary_nav_menu_btm_border_width';
		$t['nav_border_bottom_style']['p4name'] = 'primary_nav_menu_btm_border_style';
		$t['nav_bg_color']['p4name']      = 'primary_nav_menu_bg_color';
		$t['nav_bg_color_bind']['p4name'] = 'primary_nav_menu_bg_color_bind';
		$t['nav_dropdown_bg_color']['p4name']      = 'primary_nav_menu_dropdown_bg_color';
		$t['nav_dropdown_bg_color_bind']['p4name'] = 'primary_nav_menu_dropdown_bg_color_bind';
		$t['nav_dropdown_bg_hover_color']['p4name']      = 'primary_nav_menu_dropdown_bg_hover_color';
		$t['nav_dropdown_bg_hover_color_bind']['p4name'] = 'primary_nav_menu_dropdown_bg_hover_color_bind';
		$t['nav_dropdown_link_font_color']['p4name'] = 'primary_nav_menu_dropdown_link_font_color';
		$t['nav_dropdown_link_font_color_bind']['p4name'] = 'primary_nav_menu_dropdown_link_font_color_bind';
		$t['nav_dropdown_link_hover_font_color']['p4name'] = 'primary_nav_menu_dropdown_link_hover_font_color';
		$t['nav_dropdown_link_hover_font_color_bind']['p4name'] = 'primary_nav_menu_dropdown_link_hover_font_color_bind';
		$t['nav_dropdown_link_textsize']['p4name'] = 'primary_nav_menu_dropdown_link_textsize';
		$t['nav_align']['p4name'] = 'primary_nav_menu_align';
		$t['nav_edge_padding']['p4name'] = 'primary_nav_menu_edge_padding';
		$t['watermark_position']['center left']   = 'middle left';
		$t['watermark_position']['center center'] = 'middle center';
		$t['watermark_position']['center right']  = 'middle right';
		return $t;
	}
}
