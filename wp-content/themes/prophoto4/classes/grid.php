<?php

abstract class ppGrid {


	protected $ID;
	protected $articleID;
	protected $type;
	protected $style;
	protected $rows;
	protected $cols = 3;
	protected $rowNum;
	protected $colNum;
	protected $itemWidth;
	protected $lastInRow;
	protected $formMsg;
	protected $transientHandle;
	protected $gridItems = array();


	abstract protected function loadGridItems();


	public static function flushMarkupCache( $type ) {
		switch ( $type ) {
			case 'all':
				$key = '';
				break;
			case 'articles':
				$key = 'a';
				break;
			case 'galleries';
				$key = 'g';
				break;
			default:
				new ppIssue( "Unknown type  '$type' passed" );
				$key = ''; // ensure all transients deleted as safegaurd
		}

		global $wpdb;
		$wpdb->query( $q= "DELETE FROM $wpdb->options WHERE option_name LIKE '%transient_ppgr" . $key . "%'" );
		$wpdb->query( $q= "DELETE FROM $wpdb->options WHERE option_name LIKE '%transient_timeout_ppgr" . $key . "%'" );
		wp_cache_flush();
	}


	public static function flushMarkupCacheOnArticleSave() {
		add_action( 'save_post', ppUtil::func( "
			if ( !defined( 'DOING_AUTOSAVE' ) || !DOING_AUTOSAVE ) {
				ppGrid::flushMarkupCache( 'all' );
			}
		" ) );
		add_action( 'transition_post_status', create_function( '$newStatus,$oldStatus,$post', '
			if ( $newStatus == "publish" && $oldStatus != "publish" ) {
				ppGrid::flushMarkupCache( "all" );
			}
		' ), 10, 3 );
		add_action( 'delete_category', ppUtil::func( 'ppGrid::flushMarkupCache( "all" );' ) );
		add_action( 'edited_category', ppUtil::func( 'ppGrid::flushMarkupCache( "all" );' ) );

	}


	public static function modifyQuery() {
		if ( !has_action( 'pre_get_posts', 'ppGrid::modifyQuery' ) ) {
			add_action( 'pre_get_posts', 'ppGrid::modifyQuery' );
		} else if ( ppContentRenderer::renderingGridExcerpts() ) {
			add_filter( 'post_limits', 'ppGrid::setPostsPerPage' );
		}
	}


	public static function setPostsPerPage( $LIMIT ) {
		$page = max( intval( $GLOBALS['wp_query']->query_vars['paged'] ), 1 );
		$perPage = ppOpt::id( 'excerpt_grid_rows', 'int' ) * ppOpt::id( 'excerpt_grid_cols', 'int' );
		return "LIMIT " . ( ( $page - 1 ) * $perPage ) . ", " . $perPage;
	}


	public static function instance( $ID ) {
		$rawData = ppOpt::id( 'grid_' . $ID );

		if ( $rawData && $data = json_decode( $rawData ) ) {
			switch ( $data->type ) {
				case 'recent_posts':
					return new ppGrid_RecentPosts( $ID, $data );
				case 'categories':
					return new ppGrid_Categories( $ID, $data );
				case 'selected_articles':
					return new ppGrid_SelectPosts( $ID, $data );
				case 'galleries':
					return new ppGrid_Galleries( $ID, $data );
				case 'empty':
					return new ppGrid_Empty( $ID );
				default:
					new ppIssue( "Unknown grid type $data->type passed to ppGrid::instance()" );
					return new ppGrid_Empty( $ID );
			}

		} else {
			new ppIssue( "Unable to load data for grid with \$ID '$ID'" );
			return new ppGrid_Empty( $ID );
		}
	}


	public static function emptyInstance( $ID ) {
		return new ppGrid_Empty( $ID );
	}


	public static function excerpts( $postObjects ) {
		return new ppGrid_Excerpts( $postObjects );
	}


	public function id() {
		return $this->ID;
	}


	public function cols() {
		return ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) ? 1 : $this->cols;
	}


	public function rows() {
		return $this->rows;
	}


	public function type() {
		return $this->type;
	}


	public function style() {
		return $this->style;
	}


	public function categoryExcluded( $categoryID ) {
		if ( $this->type != 'categories' ) {
			return false;
		} else {
			return in_array( $categoryID, $this->excludedCategoryIDs );
		}
	}


	public function galleryDisplayType() {
		if ( $this->type() != 'galleries' ) {
			return 'select_galleries';
		} else {
			return $this->displayType();
		}
	}


	public function placeholderMarkup() {
		return NrHtml::img( 'http://prophoto.s3.amazonaws.com/img/pp-grid-placeholder-' . $this->type() . '.jpg', array(
			'id' => 'pp-grid-' . $this->id(),
			'class' => 'pp-grid-placeholder pp-grid-type-' . $this->type(),
			'style' => 'display:none;'
		) );
	}


	public function update( $updatedVals ) {
		foreach ( $updatedVals as $key => $val ) {
			$property = str_replace( 'grid_', '', $key );
			if ( in_array( $property, array( 'type', 'rows', 'cols', 'style' )  ) ) {
				$this->{$property} = $val;
			}
		}

		$updateArray = array(
			'type'  => $this->type,
			'rows'  => $this->rows,
			'cols'  => $this->cols,
			'style' => $this->style,
		);

		if ( isset( $updatedVals['article_id'] ) ) {
			$updateArray['articleID'] = $updatedVals['article_id'];
		}

		if ( $this->type() == 'categories' ) {
			$excludedCategoryIDs = array();
			foreach ( $updatedVals as $key => $val ) {
				if ( NrUtil::isIn( 'exclude_category_id_', $key ) ) {
					$excludedCategoryIDs[] = $val;
				}
			}
			if ( $excludedCategoryIDs ) {
				$updateArray['excluded_categories'] = implode( '|', $excludedCategoryIDs );
			}
		}

		if ( $this->type() == 'selected_articles' ) {
			$updateArray['postIDs'] = $updatedVals['selected_articles_ids'];
		}

		if ( $this->type() == 'galleries' ) {
			$updateArray['galleryIDs']  = $updatedVals['selected_galleries_ids'];
			$updateArray['displayType'] = $updatedVals['gallery_display'];
		}

		ppOpt::update( 'grid_' . $this->ID, json_encode( $updateArray ) );
		return ppGrid::instance( $this->ID );
	}


	public function imgDims() {
		return $this->itemWidth() . 'x' . $this->itemHeight() . 'xCROP';
	}


	public function itemWidth() {
		if ( $this->itemWidth === null ) {
			$this->itemWidth = intval( ( self::contextWidth() - $this->guttersWidth() ) / $this->cols() );
		}
		return $this->itemWidth;
	}


	public function itemHeight() {
		return intval( $this->itemWidth() / 1.5 );
	}


	public function itemSize() {
		return $this->itemWidth() + $this->itemHeight();
	}


	public function formMsg( $set = null ) {
		if ( $set !== null ) {
			$this->formMsg = $set;
		} else {
			return $this->formMsg;
		}
	}


	protected function contextWidth() {
		if ( ppWidgetUtil::renderingArea() ) {
			return ppWidgetUtil::areaWidth();
		} else {
			if ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) {
				return ppMobileHtml::STANDARD_MOBILE_DEVICE_MAX_WIDTH;
			} else {
				return ppHelper::contentWidth( ppSidebar::onThisPage() );
			}
		}
	}


	protected function guttersWidth() {
		$individualGutterWidth = ppOpt::id( 'grid_' . $this->style() . '_gutter', 'int' );
		return ( $this->cols() - 1 ) * $individualGutterWidth;
	}


	protected function classes() {
		$classes = array(
			'grid',
			'grid-type-'  . $this->type,
			'grid-style-' . $this->style,
			'sc',
		);
		return implode( ' ', $classes );
	}


	public function itemAttributes( ppGridItem $gridItem ) {
		$classes = array( 'grid-item' );
		$classes[] = 'grid-row-' . $this->rowNum;
		$classes[] = 'grid-col-' . $this->colNum;
		if ( $this->lastInRow ) {
			$classes[] = 'last-in-row';
		}
		if ( basename( $gridItem->img() ) == 'nodefaultimage.gif' ) {
			$classes[] = 'no-img';
		}
		return 'class="' . implode( ' ', $classes ) . '" style="width:' . $this->itemWidth() . 'px;"';
	}


	protected function transientHandle() {
		if ( null == $this->transientHandle ) {
			$key = ( $this->type == 'galleries' ) ? 'g' : 'a';
			$add = ( $this->type == 'excerpts' ) ? ppUtil::pageType() . $GLOBALS['wp_query']->query_vars_hash : '';
			$this->transientHandle = 'ppgr' . $key . '_' . md5( $this->style() . $this->contextWidth() . $this->itemWidth() . $this->cols() . $this->id() . $add );
		}
		return $this->transientHandle;
	}


	public function markup() {
		$gridMarkup = '';
		if ( isset( $_GET['dump_grids'] ) ) {
			$gridMarkup .= NrDump::it( $this, false );
		}

		$cachedMarkup = get_transient( $this->transientHandle() );
		if ( $cachedMarkup && NrUtil::isIn( 'end grid markup -->', $cachedMarkup ) && !isset( $_GET['no_cache'] ) ) {
			return "\n\n<!-- grid markup served from cache -->\n" . $gridMarkup . $cachedMarkup;
		}

		$gridMarkup .=  '<div id="grid-' . $this->ID . '" class="' . $this->classes() . '">' . "\n";
		$gridMarkup .=  '<div class="row sc">';

		$this->rowNum = $this->colNum = $currentNum = 1;
		$gridItemsRendered = 0;

		foreach ( $this->gridItems as $gridItem ) {

			$this->lastInRow = ( is_int( $currentNum / $this->cols() ) );
			$gridMarkup .= ppUtil::renderView( 'grid_item_' . $this->style, array( 'grid' => $this, 'gridItem' => $gridItem ), ppUtil::RETURN_VIEW );
			$this->colNum = ( $this->colNum == $this->cols() ) ? 1 : $this->colNum + 1;

			if ( $this->lastInRow ) {
				if ( $this->rowNum < $this->rows ) {
					$gridMarkup .=  "</div>\n<div class=\"row sc\">";
				}
				$this->rowNum = $this->rowNum + 1;
			}

			$currentNum++;
		}
		$gridMarkup .=  "\n</div>\n</div>\n<!-- end grid markup -->\n\n";
		if ( !isset( $_GET['dump_grids'] ) ) {
			set_transient( $this->transientHandle(), $gridMarkup, 60*60 * 24 * 5 );
		}
		return $gridMarkup;
	}


	public function render() {
		echo $this->markup();
	}


	protected function __construct( $ID, $data ) {
		$this->ID        = $ID;
		$this->type      = $data->type;
		$this->rows      = isset( $data->rows ) ? intval( $data->rows ) : null;
		$this->cols      = isset( $data->cols ) ? intval( $data->cols ) : null;
		$this->articleID = isset( $data->articleID ) ? intval( $data->articleID ) : null;
		$this->style     = $data->style;
		$this->loadGridItems();
	}


	protected function gridItemFromWpPostObj( $wpPost ) {
		// we set up and tear down the global post object
		// because content for these posts may get cached
		// and used later, and some "the_content" filters
		// rely on global post being accurate (eg like btn)
		$foundGlobalPost = is_admin() ? false : ppPost::fromGlobal();

		$post = new ppPost( $wpPost );
		ppPost::setGlobalPost( $post );
		$gridItem = new ppGridItem( $post );

		if ( $foundGlobalPost ) {
			ppPost::setGlobalPost( $foundGlobalPost );
		} else {
			$GLOBALS['post'] = null; // prevent plugin conflicts
		}

		return $gridItem;
	}

}


class ppGrid_Empty extends ppGrid {


	protected $type = 'empty';

	public function __construct( $ID ) {
		$this->ID = $ID;
	}

	public function render(){}

	public function loadGridItems(){}
}
