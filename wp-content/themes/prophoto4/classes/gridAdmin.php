<?php

class ppGridAdmin {


	protected static $adminID = 0;


	public static function render() {

		if ( NrUtil::POST( 'pp_POST_identifier', 'grid_admin' ) && ppNonce::check( 'grid_admin' ) && isset( $_POST['grid_id'] ) ) {
			$grid = self::handlePOST( $_POST );

		} else {
			if ( is_numeric( $_GET['grid_id'] ) && ppOpt::exists( 'grid_article_' . $_GET['grid_id'] ) ) {
				$grid = ppGrid::instance( 'article_' . $_GET['grid_id'] );
			} else {
				$grid = ppGrid::emptyInstance( 'article_' . time() );
			}
		}

		// instantiating grid can set up a global post object which, in
		// the admin area, can cause problems for poorly coded plugins
		$GLOBALS['post'] = null;

		ppUtil::changeWpText( 'Uploads', 'Grid Admin' );

		ppAdmin::loadScript( 'jquery-ui-draggable' );
		ppAdmin::loadScript( 'jquery-ui-droppable' );
		ppAdmin::loadScript( 'jquery-ui-sortable' );

		ppAdmin::loadFile( 'grid-admin.js' );
		ppAdmin::loadFile( 'grid-admin.css' );

		ppIFrame::wp_iframe( create_function( '$grid', '
			echo "<form id=\"grid-admin\" method=\"post\">";
				ppUtil::renderView( "grid_admin", compact( "grid" ) );
			echo "</form>";'
		), $grid );
	}


	public static function adminID() {
		self::$adminID++;
		return self::$adminID;
	}


	public static function renderArticleSelectables( $paged ) {
		$wpFilters = $GLOBALS['wp_filter'];
		unset( $GLOBALS['wp_filter'] );

		$wpArticles = new WP_Query( array(
			'post_type' => array( 'post', 'page' ),
			'posts_per_page' => 100,
			'paged' => $paged,
		 ) );
		wp_reset_query();
		$postNum = 0;
		foreach ( $wpArticles->posts as $wpArticle ) {
			$article = new ppPost( $wpArticle );
			if ( $article->type() == 'post' ) {
				$postNum++;
			}
			$isRecent = ( $article->type() == 'post' && $postNum <= 10 && $paged == 1 );
			ppUtil::renderView( 'grid_admin_available_article', compact( 'article', 'isRecent' ) );
		}

		$GLOBALS['wp_filter'] = $wpFilters;
	}


	public static function renderGallerySelectables() {
		$galleryIDs = ppGalleryAdmin::allGalleryIDs();
		foreach ( $galleryIDs as $galleryID ) {
			$gallery = ppGallery::load( $galleryID );
			if ( $gallery ) {
				if ( !$gallery->imgs() ) {
					$gallery->delete();
				} else {
					ppUtil::renderView( 'grid_admin_available_gallery', compact( 'gallery' ) );
				}
			}
		}
	}


	protected static function handlePOST( $POST ) {
		if ( NrUtil::POST( 'save_and_insert_grid', 'true' ) ) {

			$grid = ppGrid::emptyInstance( $POST['grid_id'] );
			$grid = $grid->update( $POST );

			// save because media_send_to_editor() calls exit()
			// without running shutdown hook where we save changes
			ppStorage::saveCustomizations();
			media_send_to_editor( $grid->placeholderMarkup() );

		} else {
			$grid = ppGrid::instance( $POST['grid_id'] );
			$grid = $grid->update( $POST );
			$grid->formMsg( 'Grid successfully updated.' );
			return $grid;
		}
	}


}