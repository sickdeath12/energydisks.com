<?php
/* ----------------------- */
/* NAVIGATION MENU OPTIONS */
/* ----------------------- */


$subGroups = array(
	'primary_nav_menu' =>   'Primary navigation menu',
	'secondary_nav_menu' => 'Secondary navigation menu',
);
for ( $i = 1; $i <= pp::num()->maxWidgetMenus; $i++ ) { 
	$subGroups['widget_menu_'.$i] = 'Vertical nav. menu #' . $i;
}


// tabs and header
ppSubgroupTabs( $subGroups );
ppOptionHeader( 'Navigation Menu Options', 'menu' );


$menus = array(
	array(
		'key' => 'primary_nav_menu',
		'title_prefix' => 'Menu ',
		'which' => 'primary',
	),
	array(
		'key' => 'secondary_nav_menu',
		'title_prefix' => 'Secondary menu ',
		'which' => 'secondary',
	),
);


foreach ( $menus as $menu ) {
		
	ppOptionSubgroup( $menu['key'] );
	
	
	ppO( $menu['key'] . '_admin', 'function|ppMenuAdmin::markup|' . $menu['key'], '', $menu['title_prefix'] . ' items' );
	

	// menu alignment
	ppO( $menu['key'] . '_align', 'function|ppMenuAdmin::alignmentOption|' . $menu['key'], '', 'Menu Alignment' );
	
	if ( $menu['key'] == 'secondary_nav_menu' ) {
		ppO( 'secondary_nav_menu_placement', 'function|ppMenuAdmin::secondaryNavPlacement', 'where in the header area the secondary menu should appear', 'Secondary menu placement' );
	}

	// main menu background image/color
	ppUploadBox::renderBg( $menu['key'] . '_bg', $menu['title_prefix'] . 'background color &amp; image' );

	// dropdown bg colors
	ppStartMultiple( $menu['title_prefix'] . 'dropdown background appearance' );
	ppO( $menu['key'] . '_dropdown_bg_color', 'color|optional', 'background color of menu dropdowns' );
	ppO( $menu['key'] . '_dropdown_bg_hover_color', 'color|optional', 'background color of individual dropdown menu links when hovered over' );
	ppO( $menu['key'] . '_dropdown_opacity', 'slider', 'opacity of menu dropdowns<br /><em>(modern browsers only)</em>' );
	ppStopMultiple();


	// menu links
	ppFontGroup( array(
		'key' => $menu['key'] . '_link',
		'title' => $menu['title_prefix'] . 'link appearance',
		'inherit' => 'all',
		'add' => array( 'letterspacing' ),
	) );

	// dropdown links
	ppStartMultiple( $menu['title_prefix'] . 'dropdown links' );
	ppO( $menu['key'] . '_dropdown_link_textsize', 'text|3', 'size (in pixels) of dropdown menu link text' );
	ppO( $menu['key'] . '_dropdown_link_font_color', 'color|optional', 'dropdown menu link text color' );
	ppO( $menu['key'] . '_dropdown_link_hover_font_color', 'color|optional', 'dropdown menu link text color when hovered over' );
	ppStopMultiple();



	// link padding/spacing
	ppStartMultiple( $menu['title_prefix'] . 'link custom spacing' );
	ppO( $menu['key'] . '_link_spacing_between', 'text|3', 'override default horizontal spacing (in pixels) between each top-level menu link' );
	ppO( $menu['key'] . '_edge_padding','text|3', 'override horizontal spacing (in pixels) between the entire menu and edges of site' );
	ppO( $menu['key'] . '_link_tb_padding','text|3', 'override padding (in pixels) above and below top-level menu link items' );
	
	ppStopMultiple();

	// nav top border
	ppStartMultiple( 'Custom lines above/below ' . strtolower( $menu['title_prefix'] ) );
	ppO( $menu['key'] . '_border_top_onoff', 'radio|on|custom line above menu|off|no custom line above menu', 'custom line <em>above</em> the menu' );
	ppO( $menu['key'] . '_border_bottom_onoff', 'radio|on|custom line below menu|off|no custom line below menu', 'custom line <em>below</em> the menu' );
	ppO( $menu['key'] . '_border_blank', 'blank' );
	ppBorderGroup( array( 'key' => $menu['key'] . '_top_border', 'comment' => 'appearance of line <em>above</em> menu' ) );
	ppBorderGroup( array( 'key' => $menu['key'] . '_btm_border', 'comment' => 'appearance of line <em>below</em> menu' ) );
	ppStopMultiple();
	
	ppO( $menu['key'] . '_onoff', "radio|on|show {$menu['which']} navigation menu|off|do not show {$menu['which']} navigation menu", 'select "do not show" if you want to save your menu links and structure, but want to temporarily disable the display of this menu', ucfirst( $menu['which'] ) . ' navigation menu display' );
	

	ppEndOptionSubgroup();
	
}


for ( $i = 1; $i <= pp::num()->maxWidgetMenus; $i++ ) {
	 
	$key = 'widget_menu_' . $i;
	
	ppOptionSubgroup( $key );
	
	ppO( $key . '_admin', 'function|ppMenuAdmin::markup|' . $key, '', "Vertical menu #$i items" );
	
	
	ppO( "widget_menu_{$i}_location_note", 'note', ppString::id( 'blurb_widget_menu_location', $i ), "Vertical menu #$i location" );
	
	$levels = array(
		'li' => 'first',
		'sub_li' => 'second',
		'sub_sub_li' => 'third',
	);
	
	ppStartMultiple( 'Menu item vertical spacing' );
	foreach ( $levels as $key => $nth ) {
		ppO( "widget_menu_{$i}_{$key}_margin_btm", 'slider|0|35|px', "spacing below each {$nth}-level menu item" );
	}
	ppStopMultiple();
	
	ppStartMultiple( 'Menu item list decoration (bullet/numbering style)' );
	foreach ( $levels as $key => $nth ) {
		ppO( "widget_menu_{$i}_{$key}_list_style", 'select|none|no decoration|disc|bullets (•,•,•,•)|decimal|numbers (1,2,3)|lower-alpha|lower-case letters (a,b,c,d)|upper-alpha|upper-case letters (A,B,C,D)|lower-roman|lowercase roman numerals (i,ii,iii,iv)|upper-roman|uppercase roman numerals (I,II,III,IV)|image|custom image', "<b>$nth</b> level lists" );
	}
	foreach ( $levels as $key => $nth ) {
		ppO( "widget_menu_{$i}_{$key}_list_image", 'image', "<b>{$nth}</b> level custom list image" );
	}
	ppStopMultiple();
	
	foreach ( $levels as $key => $nth ) {
		ppFontGroup( array(
			'key' => "widget_menu_{$i}_{$key}_link",
			'title' => ucfirst( $nth ) . ' level menu item font/link appearance',
			'inherit' => 'all',
			'add' => array( 'nonlink_color' ),
		) );
	}
	
	
	
	ppEndOptionSubgroup();
	
}

