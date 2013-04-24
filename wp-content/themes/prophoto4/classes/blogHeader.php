<?php

class ppBlogHeader {


	const FOR_MOBILE = true;
	protected static $layout;
	protected static $hTag;
	protected static $mastheadImgNum;
	protected static $elements = array();
	protected static $usingSubnav;
	protected static $subnavPlacement;


	public static function markup() {
		do_action( 'pp_pre_header' );

		$markup = '';
		self::$hTag = is_singular() ? 'h2' : 'h1';

		if ( ppHelper::logoInMasthead() ) {
			$markup .= self::maybeAddSubNav( 1 );
			$markup .= NrHtml::div( self::logo() . self::masthead(), 'id=masthead&class=sc' );
			$markup .= self::maybeAddSubNav( 2 );
			$markup .= self::nav();
			$markup .= self::maybeAddSubNav( 3 );

		} else {

			$orderedElements = self::orderedElements();

			if ( !NrUtil::isIn( 'logo', self::$layout ) ) {
				$markup .= ppUtil::renderView( 'header_alt_h', array( 'hTag' => self::$hTag ), ppUtil::RETURN_VIEW );
			}

			$markup .= self::maybeAddSubNav( $subNavIndex = 1 );
			foreach ( $orderedElements as $element ) {
				if ( 'masthead' == $element && !ppHelper::skipMasthead() ) {
					$markup .= NrHtml::div( self::masthead(), 'id=masthead&class=sc' );
				} else {
					$markup .= self::$element();
				}
				$subNavIndex++;
				$markup .= self::maybeAddSubNav( $subNavIndex );
			}
			$markup .= self::maybeAddSubNav( $subNavIndex + 1 );
		}

		return apply_filters( 'pp_blog_header_markup', NrHtml::tag( 'header', $markup , 'class=sc' ) );
	}


	public static function mastheadDims( $mobileWidth = null, $considerRequestingBrowser = true ) {
		if ( is_int( $mobileWidth ) ) {
			$blogWidth = $mobileWidth;
			$mb_ = ppOpt::test( 'mobile_masthead_use_desktop_settings', 'false' ) ? 'mobile_' : '';
		} else {
			$blogWidth = ppOpt::id( 'blog_width', 'int' );
			$mb_  = '';
		}

		$img = self::mastheadImgTag( $mb_ );

		if ( !ppHelper::logoInMasthead() || ( pp::browser()->isMobile && $considerRequestingBrowser ) ) {
			$width  = $blogWidth;
			$height = intval( NrUtil::constrainRectSide( $blogWidth, $img->width(), $img->height() ) );

		} else if ( ppOpt::test( 'headerlayout', 'logomasthead_nav || mastheadlogo_nav' ) ) {
			$logo   = ppImg::id( 'logo' );
			$width  = $blogWidth - $logo->width;
			$height = $logo->height;

		} else if ( ppOpt::test( 'headerlayout', 'mastlogohead_nav' ) ) {
			$width  = $blogWidth;
			$height = ppImg::id( 'logo' )->height;

		} else {
			new ppIssue( 'Unexpected condition in ppBlogHeader::mastheadDims()' );
		}

		return (object) compact( 'width', 'height' );
	}


	public static function menuDim( $key, $dim ) {
		$key = NrUtil::isIn( 'secondary_', $key ) ? 'secondary_nav_menu_' : 'primary_nav_menu_';
		$padding = intval( ppOpt::orVal( $key . 'link_tb_padding', round( ppOpt::id( $key . 'link_font_size' ) * 0.715, 0 ) ) );
		$fontsize = ppOpt::id( $key . 'link_font_size', 'int' );
		if ( $dim == 'height' ) {
			return intval( $fontsize + ( $padding * 2 ) );
		} else if ( $dim == 'fontsize' ) {
			return $fontsize;
		} else if ( $dim == 'padding' ) {
			return $padding;
		} else {
			new ppIssue( 'Invalid $dim param passed to ppBlogHeader::menuDim()' );
		}
	}


	protected static function maybeAddSubNav( $index ) {
		if ( ppOpt::test( 'secondary_nav_menu_onoff', 'off' ) ) {
			return '';
		}

		if ( null === self::$usingSubnav ) {
			self::$usingSubnav = ppMenuUtil::menuHasItems( 'secondary_nav_menu' );
		}

		if ( null == self::$subnavPlacement ) {
			self::$subnavPlacement = ppOpt::id( 'secondary_nav_menu_placement' );
		}

		if ( self::$usingSubnav && self::$subnavPlacement == strval( $index ) ) {
			$menuItems = $menuItems = ppMenuUtil::menuItems( 'secondary_nav_menu' );
			$menuItems = self::reorderForSplit( $menuItems, 'secondary' );
			$markup = '';
			$firstItemID = reset( array_keys( $menuItems ) );
			$lastItemID  = end( array_keys( $menuItems ) );
			foreach ( $menuItems as $itemID => $children ) {
				$item = ppMenuUtil::menuItem( $itemID, $children );
				if ( $itemID == $firstItemID ) {
					$item->addClass( 'first-menu-item' );
				}
				if ( $itemID == $lastItemID ) {
					$item->addClass( 'last-menu-item' );
				}
				$markup .= $item->markup();
			}
			$markup  = NrHtml::div( NrHtml::ul( $markup, 'class=secondary-nav-menu suckerfish sc' ), 'id=secondary-nav&class=sc' );
			$markup .= NrHtml::div( '', 'id=secondary-nav-ajax-receptacle&class=nav-ajax-receptacle sc content-bg' );
			return $markup;
		} else {
			return '';
		}
	}


	private static function logo() {
		return ppUtil::renderView( 'header_logo', array( 'logo' => ppImg::id( 'logo' ), 'h1or2' => self::$hTag ), ppUtil::RETURN_VIEW );
	}


	private static function reorderForSplit( $menuItems, $which ) {
		if ( !ppOpt::test( $which . '_nav_menu_align', 'split' ) || !ppOpt::test( $which . '_nav_menu_split_after_id' ) ) {
			return $menuItems;
		}

		if ( $which == 'primary' && ppOpt::test( 'headerlayout', 'pptclassic' ) ) {
			return $menuItems;
		}

		$splitIndex = null;
		foreach ( array_keys( $menuItems ) as $index => $ID ) {
			if ( $ID == ppOpt::id( $which . '_nav_menu_split_after_id' ) ) {
				$splitIndex = $index;
			}
		}
		if ( $splitIndex !== null ) {
			$leftSide  = array_slice( $menuItems, 0, $splitIndex + 1 );
			$rightSide = array_reverse( array_slice( $menuItems, $splitIndex + 1, count( $menuItems ) - ( $splitIndex + 1 ) ) );
			$menuItems = array_merge( $leftSide, $rightSide );
		}

		return $menuItems;
	}


	public static function nav() {
		$markup = '';
		if ( ppOpt::test( 'primary_nav_menu_onoff', 'off' ) ) {
			return $markup;
		}

		$menuItems = ppMenuUtil::menuItems( 'primary_nav_menu' );

		if ( !$menuItems ) {
			return $markup;
		}

		$menuItems = self::reorderForSplit( $menuItems, 'primary' );

		$lastItemID  = end( array_keys( $menuItems ) );
		$firstItemID = reset( array_keys( $menuItems ) );
		foreach ( (array) $menuItems as $itemID => $children ) {
			$item = ppMenuUtil::menuItem( $itemID, $children );
			if ( $itemID == $firstItemID ) {
				$item->addClass( 'first-menu-item' );
			}
			if ( $itemID == $lastItemID ) {
				$item->addClass( 'last-menu-item' );
			}
			$markup .= $item->markup();
		}
		$markup  = NrHtml::tag( 'nav', NrHtml::ul( $markup, 'class=primary-nav-menu suckerfish sc' ), 'id=primary-nav&class=sc' );
		$markup .= NrHtml::div( '', 'id=primary-nav-ajax-receptacle&class=nav-ajax-receptacle sc content-bg' );
		return $markup;
	}


	private static function masthead() {
		if ( ppHelper::skipMasthead() ) {
			return '';
		}

		$max = self::mastheadDims();

		$imgTag = self::mastheadImgTag();
		if ( !ppOpt::test( 'headerlayout', 'logomasthead_nav || mastheadlogo_nav' ) ) {
			$imgTag = ppGdModify::constrainImgSize( $imgTag, $max->width, $max->height );
		}

		$classes = 'masthead-image';

		if ( self::mastheadSlideshowOnThisPage() ) {
			$classes .= ' pp-slideshow pp-slideshow-not-loaded autostart';
		}

		if ( self::customFlashOnThisPage() ) {
			$classes .= ' custom-flash';
		}

		if ( self::$mastheadImgNum && ppOpt::test( 'masthead_image' . self::$mastheadImgNum . '_linkurl' ) ) {
			$href = ppUtil::userUrl( 'masthead_image' . self::$mastheadImgNum . '_linkurl' );
		} else {
			$href = null;
		}

		return ppUtil::renderView( 'header_masthead', array( 'img' => $imgTag, 'classes' => $classes, 'href' => $href ), ppUtil::RETURN_VIEW );
	}


	protected function customFlashOnThisPage() {
		if ( !ppOpt::test( 'masthead_display', 'custom' ) ) {
			return false;
		}
		if ( ppOpt::test( 'masthead_modify', 'false' ) ) {
			return true;
		}
		if ( ppOpt::test( 'masthead_on_' . ppUtil::pageType( ppUtil::NO_ARCHIVE_TYPE ), 'modified' ) ) {
			return false;
		}
		return true;
	}


	public function mastheadSlideshowOnThisPage( $prefix_ = '' ) {
		if ( !ppOpt::test( "{$prefix_}masthead_display", 'slideshow' ) ) {
			return false;

		} else if ( ppOpt::test( "{$prefix_}masthead_modify", 'false' ) ) {
			return self::hasAtLeastTwoMastheadImgs( $prefix_ );

		} else if ( ppHelper::logoInMasthead() && ppOpt::test( "{$prefix_}masthead_on_" . ppUtil::pageType( ppUtil::NO_ARCHIVE_TYPE ), 'modified' ) ) {
			return false;

		} else if ( ppOpt::test( "{$prefix_}modified_masthead_display", 'image' ) && ppOpt::test( "{$prefix_}masthead_on_" . ppUtil::pageType( ppUtil::NO_ARCHIVE_TYPE ), 'modified' ) ) {
			return false;

		} else {
			return self::hasAtLeastTwoMastheadImgs( $prefix_ );
		}
	}


	protected function hasAtLeastTwoMastheadImgs( $prefix_ ) {
		$foundImgs = 0;
		for ( $i = 1; $i <= pp::num()->maxMastheadImages; $i++ ) {
			if ( ppImg::id( $prefix_ . 'masthead_image' . $i )->exists ) {
				$foundImgs++;
			}
			if ( $foundImgs > 1 ) {
				return true;
			}
		}
		return false;
	}


	protected function mastheadImgTag( $mb_ = '' ) {
		if ( $overrideImgUrl = self::mastheadOverrideImg( $mb_ ) ) {
			$imgTag = new ppImgTag( $overrideImgUrl );
			$imgData = NrUtil::imgData( ppUtil::pathFromUrl( $overrideImgUrl ) );
			if ( $imgData ) {
				$imgTag->width( $imgData->width );
				$imgTag->height( $imgData->height );
			}
		} else {
			self::$mastheadImgNum = self::mastheadImgNum( $mb_ );
			$img = ppImg::id( $mb_ . 'masthead_image' . self::$mastheadImgNum );
			$imgTag = new ppImgTag( $img->url );
			$imgTag->width( $img->width );
			$imgTag->height( $img->height );
		}
		$imgTag->id( 'masthead-img' );
		$imgTag->alt( 'Masthead header' );
		return $imgTag;
	}


	public static function mastheadImgNum( $mb_ ) {
		$mastheadDisplay = ppOpt::id( $mb_ . 'masthead_display' );

		if ( $mastheadDisplay == 'static' ) {
			$imgNum = '1';

		} else if ( $mastheadDisplay == 'random' ) {
			$imgNum = self::randomMastheadImgNum();

		} else if ( $mastheadDisplay == 'slideshow' && ppOpt::test( 'masthead_slideshow_image_order', 'random' ) ) {
			$imgNum = self::randomMastheadImgNum();

		} else if ( ppOpt::test( $mb_ . 'masthead_modify', 'false' ) ) {
			$imgNum = '1';

		} else if ( ppOpt::test( $mb_ . 'modified_masthead_image' ) &&
				    ppOpt::test( $mb_ . 'masthead_on_' . ppUtil::pageType( ppUtil::NO_ARCHIVE_TYPE ), 'modified' ) ) {
			$imgNum = ppOpt::id( $mb_ . 'modified_masthead_image' );

		} else {
			$imgNum = '1';
		}

		if ( ppImg::id( $mb_ . "masthead_image{$imgNum}" )->exists ) {
			return $imgNum;
		} else {
			return '1';
		}
	}


	private static function randomMastheadImgNum() {
		$nums = array();
		for ( $i = 1; $i <= pp::num()->maxMastheadImages; $i++ ) {
			if ( ppImg::id( "masthead_image{$i}" )->exists ) {
				$nums[] = strval( $i );
			}
		}
		if ( empty( $nums ) ) {
			new ppIssue( 'No masthead imgs found in ppBlogHeader::randomMastheadImgNum()' );
			return '1';
		}

		shuffle( $nums );
		return array_shift( $nums );
	}


	private static function mastheadOverrideImg() {
		if ( !is_singular() || !$customMeta = get_post_meta( ppPost::fromGlobal()->id(), 'custom_masthead_image', AS_STRING ) ) {
			return false;
		}

		if ( is_numeric( $customMeta ) && ppImg::id( "masthead_image$customMeta" )->exists ) {
			return ppImg::id( "masthead_image$customMeta" )->url;

		} else if ( NrUtil::isWebSafeImg( $customMeta ) ) {
			$customImgName = basename( $customMeta );
			$customImgPath = pp::fileInfo()->imagesFolderPath . '/' . $customImgName;
			if ( file_exists( $customImgPath ) ) {
				return ppUtil::urlFromPath( $customImgPath );
			}
		}

		return false;
	}


	private static function orderedElements() {
		self::$layout = ppOpt::id( 'headerlayout' );

		if ( self::$layout == 'pptclassic' ) {
			return array( 'logo', 'masthead' );

		} else {
			return explode( '_', str_replace( array( 'left', 'right', 'center' ), '', self::$layout ) );
		}
	}


	/* TODO: move this out of this class eventually */
	public static function mastheadOptions( $context ) {
		if ( $context == 'desktop' ) {
			$prefix_ = '';
			$Masthead = 'Masthead';
		} else {
			$prefix_ = 'mobile_';
			$Masthead = 'Mobile masthead';
		}

		// DISPLAY options
		ppStartMultiple( "$Masthead display" );
		if ( $context == 'desktop' && ppHelper::logoInMasthead() && !ppOpt::test( "{$prefix_}masthead_display", 'off' ) ) {
			$offOption = array();
		} else {
			$offOption = array( 'off' => 'do not display masthead' );
		}
		ppO( "{$prefix_}masthead_display", ppUtil::radioParams( array_merge( $offOption, array(
			'static'    => 'single static image',
			'random'    => 'random static image',
			'slideshow' => 'slideshow of images',
			'custom'    => 'custom uploaded flash .swf file',
		) ) ) );

		ppO( "{$prefix_}masthead_modify", ppUtil::radioParams( array(
			'false' => 'same masthead on all page types',
			'true'  => 'remove or change on some page types',
		) ), 'optionally override the masthead display for select page types' );

		if ( pp::site()->hasStaticFrontPage ) {
			$home = 'posts page';
			$static = array( "{$prefix_}masthead_on_front_page" => 'static front page' );
		} else {
			$home = 'home page';
			$static = array();
		}
		ppO( "{$prefix_}modify_masthead_on", ppUtil::checkboxParams( 'modified', array_merge( $static, array(
			$prefix_ . 'masthead_on_home'    => $home,
			$prefix_ . 'masthead_on_single'  => 'individual post pages',
			$prefix_ . 'masthead_on_page'    => 'static WordPress "Pages"',
			$prefix_ . 'masthead_on_archive' => 'archive, category, author, and search',
		) ) ), 'on selected (checked) page types, masthead will be removed or changed' );

		ppO( "{$prefix_}modified_masthead_display", ppUtil::radioParams( array(
			'none'  => 'remove masthead',
			'image' => 'show a single static masthead image',
		) ), 'how to modify masthead on selected pages' );

		$imgOptions = array();
		for ( $i = 1; $i <= pp::num()->maxMastheadImages; $i++ ) {
			if ( !ppImg::id( "{$prefix_}masthead_image" . $i )->exists ) continue;
			$imgOptions[$i] = 'Masthead image #' . $i;
		}
		ppO( "{$prefix_}modified_masthead_image", ppUtil::selectParams( $imgOptions ), 'choose one of your uploaded masthead images to display on selected page-types' );

		ppStopMultiple();

		// SLIDESHOW options
		ppStartMultiple( "$Masthead slideshow options" );
		ppO( "{$prefix_}masthead_slideshow_hold_time", 'slider|1|30| seconds|0.5', 'hold time (in seconds) each slideshow image is shown' );
		ppO( "{$prefix_}masthead_slideshow_transition_time", 'slider|0|6| seconds|0.2', 'time of transition effect between slideshow images' );
		ppO( "{$prefix_}masthead_slideshow_image_order", 'radio|random|play images in random order|sequential|play images in sequential order' );
		ppO( "{$prefix_}masthead_slideshow_loop_images", 'radio|true|loop images|false|stop on last image' );
		ppO( "{$prefix_}masthead_slideshow_transition_type", ppUtil::radioParams( array(
			'crossfade'   => 'cross-fade',
			'fade'        => 'fade to bg color then to image',
			'slide'       => 'hold then slide horizontally',
			'topslide'    => 'hold then slide vertically',
			'steadyslide' => 'steady horizontal slide',
		) ), 'image transition effect' );
		ppO( "{$prefix_}masthead_slideshow_bg_color", 'color|optional', 'background color of masthead slideshow' );
		ppStopMultiple();
	}


	/* TODO: move this out of this class eventually */
	public static function mastheadOptionJs() {
		echo <<<HTML
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function($){
				if ( /area=header/.test( window.location.href ) ) {
					var prefix = '';
				} else {
					var prefix = 'mobile_';
				}
				function ppProcessMastheadDisplayChoice( choice ) {
					$('#subgroup-masthead').removeClass('static random slideshow custom off').addClass(choice);

					// image titles and comments
					$('span.masthead-conditional').hide();
					$('span.mc-'+choice).show();

					// add image and custom swf upload show/hide
					if ( choice == 'static' ) {
						$('#add-masthead-upload').addClass('hidden').hide();
						$('#subgroup-masthead .upload-box').not(':first').hide();
						$('#subgroup-masthead .upload-box:first').show();
						$('#upload-box-'+prefix+'masthead_custom_flash').addClass('hidden').hide();
					} else if ( choice == 'random' ) {
						$('#subgroup-masthead .upload-box').not('.empty').show();
						$('#subgroup-masthead .upload-box.empty:first').show();
						$('#upload-box-'+prefix+'masthead_custom_flash').addClass('hidden').hide();
					} else if ( choice == 'slideshow' ) {
						$('#subgroup-masthead .upload-box').not('.empty').show();
						$('#subgroup-masthead .upload-box.empty:first').show();
						$('#upload-box-'+prefix+'masthead_custom_flash').addClass('hidden').hide();
					} else if ( choice == 'custom' ) {
						$('#subgroup-masthead .upload-box').not(':first').hide();
						$('#upload-box-'+prefix+'masthead_custom_flash').removeClass('hidden').show();
					} else if ( choice == 'off' ) {
						$('#subgroup-masthead .upload-box').hide();
					}
				}
				ppProcessMastheadDisplayChoice( $('#'+prefix+'masthead_display-individual-option input[type=radio]:checked').val() );
				$('#'+prefix+'masthead_display-individual-option input[type=radio]').click(function(){
					ppProcessMastheadDisplayChoice( $(this).val() );
				});

				// classes for masthead modification, used for show/hide ux of complex masthead modified options
				var masthead_modify = $('#'+prefix+'masthead_modify-individual-option input[type=radio]:checked').val();
				$('#'+prefix+'masthead_display-option-section').addClass('masthead-modify-'+masthead_modify);
				$('#'+prefix+'masthead_modify-individual-option input[type=radio]').click(function(){
					$('#'+prefix+'masthead_display-option-section')
						.removeClass('masthead-modify-true masthead-modify-false')
						.addClass( 'masthead-modify-'+$(this).val() );
				});
				var modify_display = $('#'+prefix+'modified_masthead_display-individual-option input[type=radio]:checked').val();
				$('#'+prefix+'masthead_display-option-section table').addClass('modify-display-'+modify_display);
				$('#'+prefix+'modified_masthead_display-individual-option input[type=radio]').click(function(){
					$('#'+prefix+'masthead_display-option-section table')
						.removeClass('modify-display-none modify-display-image')
						.addClass( 'modify-display-'+$(this).val() );
				});
			});
		</script>
HTML;
	}


	public static function flushCache() {
		self::$usingSubnav = null;
		self::$subnavPlacement = null;
	}
}

