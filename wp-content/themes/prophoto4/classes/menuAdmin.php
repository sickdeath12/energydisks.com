<?php

class ppMenuAdmin {


	public static function markup( $handle ) {
		$menuItems = ppMenuUtil::menuItems( $handle );
		$highestID = self::highestID( $menuItems, 0, $handle ) + 1;

		return ppUtil::renderView( 'menu_admin', compact( 'handle', 'menuItems', 'highestID' ), ppUtil::RETURN_VIEW );
	}


	public static function highestID( $array, $currentHighest, $handle ) {
		foreach ( $array as $ID => $maybeChildren ) {
			$num = intval( str_replace( $handle . '_item_', '', $ID ) );
			$currentHighest = max( $currentHighest, $num );
			if ( is_array( $maybeChildren ) ) {
				$currentHighest = self::highestID( $maybeChildren, $currentHighest, $handle );
			}
		}
		return $currentHighest;
	}


	public static function alignmentOption( $handle ) {
		$basicOption = new ppOptionBoxIndividual( $handle . '_align', 'radio|left|left align|center|center align|right|right align|split|split some items left, some right', '', '' );
		$markup = $basicOption->input_markup;


		$menuItems = ppMenuUtil::menuItems( ( $handle == 'secondary_nav_menu' ) ? 'secondary_nav_menu' : 'primary_nav_menu' );
		$markup .= NrHtml::p( '
			<span class="normal">Drag highlighted items to reposition split:</span>
			<span class="only-one">You must have at least two menu items to split alignment.</span>
			<span class="only-two">Adding more items will allow you to reposition the split.</span>
		', 'class=split-explain' );

		if ( !empty( $menuItems ) ) {
			$markup .= '<div class="menu-align-wrap sc">';
			foreach ( $menuItems as $id => $maybeChildren ) {
				$item = ppMenuUtil::menuItem( $id );
				$markup .= NrHtml::span( $item->text(), 'class=menu-align-box&id=aligner_' . $id );
			}
			$markup .= '</div>';
		}

		$splitID = new ppOptionBoxIndividual( $handle . '_split_after_id', 'text', '', '' );
		$markup .= NrHtml::div( $splitID->input_markup, 'id=' . $handle . '_split_after_hidden_input&class=split_after' );
		return $markup;
	}


	public static function secondaryNavPlacement() {
		$headerParts = ppOpt::test( 'headerlayout', 'pptclassic' ) ? array( 'logonav', 'masthead' ) : explode( '_', ppOpt::id( 'headerlayout' ) );
		$headerParts = array_map( 'ppMenuAdmin::headerPartTerms', $headerParts );
		array_push( $headerParts, '' );
		array_unshift( $headerParts, '' );

		$radioOpts = array();
		foreach ( $headerParts as $index => $previousPart ) {
			if ( isset( $headerParts[$index+1] ) ) {
				$radioOpts[] = $index + 1;
				$nextPart = $headerParts[$index+1];
				if ( !$previousPart ) {
					$radioOpts[] = "top of header area, above $nextPart";
				} else if ( !$nextPart ) {
					$radioOpts[] = "bottom of header area, below $previousPart";
				} else {
					$radioOpts[] = "between $previousPart and $nextPart";
				}
			}
		}

		$option = new ppOptionBoxIndividual( 'secondary_nav_menu_placement', 'radio|' . implode( '|', $radioOpts ), '', '' );
		return $option->input_markup;
	}


	protected static function headerPartTerms( $part ) {
		switch ( $part ) {
			case 'logomasthead':
			case 'mastlogohead':
			case 'mastheadlogo':
				return 'logo/masthead';
			case 'logoleft':
			case 'logocenter':
			case 'logoright':
				return 'logo';
			case 'nav':
				return 'main nav menu';
			case 'logonav':
				return 'logo/main nav menu';
		}
		return $part;
	}


	public static function renderEditMenuItemScreen( $ID ) {
		if ( isset( $_POST['menu_item_id'] ) ) {
			$updateSuccess = ppMenuUtil::update( $ID, $_POST );
			if ( $updateSuccess ) {
				self::adviseEditScreen( 'Link updated.' );
			}
		}

		ppAdmin::loadStyle( 'media' );
		ppAdmin::loadScript( 'jquery-ui-tabs' );
		ppAdmin::loadStyle( 'thickbox' );
		ppAdmin::loadScript( 'thickbox' );

		ppAdmin::loadFile( 'customize.css' );
		ppAdmin::loadFile( 'menu-admin-edit-item.css' );
		ppAdmin::loadFile( 'menu-admin-edit-item.js' );
		ppAdmin::loadFile( 'options.js' );
		ppAdmin::loadFile( 'colorbox.js', array(), ppAdmin::LOAD_IN_FOOTER );

		ppAdmin::jQueryUiCss();

		$item = ppMenuUtil::menuItem( $ID );

		ppIFrame::wp_iframe( create_function( '$item', 'ppUtil::renderView( "menu_admin_item_form", compact( "item" ) );' ), $item );
	}


	public static function posts() {
		return self::articles( 'post' );
	}


	public static function pages() {
		return self::articles( 'page' );
	}


	public static function categories() {
		$categoryData = get_categories();
		$categories = array( 'select...' => '' );
		foreach ( (array) $categoryData as $category ) {
			$categories[$category->name . '&nbsp;'] = $category->category_nicename;
		}
		return $categories;
	}


	public static function adviseEditScreen( $text ) {
		$markup = NrHtml::p( NrHtml::span( $text ), 'class=gmail-notice-show' );
		add_action( 'pp_menu_item_edit_notices', ppUtil::func( "echo '$markup';" ) );
	}


	protected static function articles( $type ) {
		global $wpdb;
		$results = $wpdb->get_results( "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = '$type' AND post_status = 'publish'" );
		$formattedArticles = array( 'select...' => '' );
		foreach ( $results as $result ) {
			$index = $result->post_title ? $result->post_title : "<em>Untitled $type</em> (id:{$result->ID})";
			$formattedArticles[$index] = $result->ID;
		}
		return $formattedArticles;
	}

}
