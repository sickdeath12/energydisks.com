<?php


$menus = array(
	array(
		'key'      => 'primary_nav_menu_',
		'ul_class' => 'primary-nav-menu',
		'wrap_id'  => 'primary-nav'
	),
	array(
		'key'      => 'secondary_nav_menu_',
		'ul_class' => 'secondary-nav-menu',
		'wrap_id'  => 'secondary-nav'
	),
);



foreach ( $menus as $menu ) {

	
	/* -- OVERALL ALIGNMENT -- */
	if ( ppOpt::test( $menu['key'] . 'align', 'left' ) ) {
		$menu_float = 'float:none;';
	} else if ( ppOpt::test( $menu['key'] . 'align', 'center' ) ) {
		$menu_float = 'float:left;';
	} else if ( ppOpt::test( $menu['key'] . 'align', 'right' ) ) {
		$menu_float = 'float:right;';
	} else {
		$menu_float = '';
	}



	/* -- DEFAULT SPACING --  */
	$default_nav_spacing = round( ( ppOpt::id( $menu['key'] . 'link_font_size' ) / 1.4 ), 0 );



	/* -- CUSTOM LINES ABOVE/BELOW -- */
	if ( !ppOpt::test( 'headerlayout', 'pptclassic' ) || $menu['key'] == 'secondary_nav_menu_' ) {
		$css .= ppCss::border( $menu['key'] . 'top', 'top'    )->onlyIf( ppOpt::test( $menu['key'] . 'border_top_onoff', 'on' )    )->rule( '#' . $menu['wrap_id'] );
		$css .= ppCss::border( $menu['key'] . 'btm', 'bottom' )->onlyIf( ppOpt::test( $menu['key'] . 'border_bottom_onoff', 'on' ) )->rule( '#' . $menu['wrap_id'] );
	}



	/* -- LEFT/RIGHT PADDING -- */
	$menu_lr_padding = '';
	if ( ppOpt::test( 'blog_border', 'dropshadow' ) || ppOpt::test( 'blog_border', 'none' ) ) {
		$menu_lr_padding = "padding: 0 18px;";
	} 
	if ( ppOpt::id( $menu['key'] . 'edge_padding' ) != '' && !ppOpt::test( $menu['key'] . 'align', 'center' ) && ( !ppOpt::test( 'headerlayout', 'pptclassic' ) || $menu['key'] == 'secondary_nav_menu_' ) ) {
		// help people trying to perfectly space menu items from left edge to right edge
		if ( ppOpt::id( $menu['key'] . 'edge_padding' ) === '0' ) {
			$css .= '#' . $menu['wrap_id'] . ' li.last-menu-item  { margin-right:0 !important }';
			$css .= '#' . $menu['wrap_id'] . ' li.first-menu-item { margin-left:0 !important }';
		}
		$menu_lr_padding = 'padding:0 ' . ppOpt::id( $menu['key'] . 'edge_padding' ) . 'px;'; 
	} else if ( ppOpt::test( 'blog_border', 'border' ) ) { 
		$menu_lr_padding = "padding:0 " . $default_nav_spacing . "px;";
	}



	/* -- PADDING BETWEEN LINKS -- */
	$menu_items_spacing_between = ppOpt::orVal( $menu['key'] . 'link_spacing_between', round( ppOpt::id( $menu['key'] . 'link_font_size' ) * 1.8, 0 ) );
	$nav_link_margin_side = ( ppOpt::test( $menu['key'] . 'align', 'right' ) || ppOpt::test( 'headerlayout', 'pptclassic' ) ) ? 'left' : 'right';



	/* -- PADDING ABOVE/BELOW LINKS -- */
	$nav_link_top_bottom_padding = ppBlogHeader::menuDim( $menu['key'], 'padding' );


	/* -- DROPDOWN BG COLOR -- */
	$dropdown_bg_color = ppOpt::cascade( $menu['key'] . 'dropdown_bg_color', $menu['key'] . 'bg_color' );
	if ( !$dropdown_bg_color ) {
		$dropdown_bg_color = 'transparent';
	}
	$dropdown_bg_color = 'background:' . $dropdown_bg_color . ' !important;';



	/* -- DROPDOWN LINEHEIGHT -- */
	$dropdown_lineheight = round( ( ppOpt::id( $menu['key'] . 'dropdown_link_textsize' ) * 1.5 ), 0 );



	/* -- TOP PADDING TO VERTICALLY CENTER EMBEDDED FORMS -- */
	$nav_height = ppOpt::id( $menu['key'] . 'link_font_size' ) + ( $nav_link_top_bottom_padding * 2 );
	$nav_form_top_padding = ( $nav_height - 22 ) / 2; // 22 is height of input
	$nav_form_top_padding = round( $nav_form_top_padding, 0 );



	/* -- LINK CSS -- */
	$css .= ppCss::link( $menu['key'] . 'link' )->rules( 'ul.' . $menu['ul_class'] );
	$css .= ppCss::link( $menu['key'] . 'dropdown_link' )->rules( 'ul.' . $menu['ul_class'] . ' ul li' );


	/* -- BACKGROUND --  */
	$selector = ( ppOpt::test( 'headerlayout', 'pptclassic' ) && $menu['key'] == 'primary_nav_menu_' ) ? 'ul.' . $menu['ul_class'] : '#' . $menu['wrap_id'];
	$css .= ppCss::background( $menu['key'] . 'bg' )->rule( $selector );





	$rawCss = <<<CSS
	ul.UL_CLASS-menu-class {
		line-height:[~MENUKEY_link_font_size]px; 
		font-size:[~MENUKEY_menu_link_font_size]px;
		$menu_float
		$menu_lr_padding
	}
	ul.UL_CLASS-menu-class li {
		margin-{$nav_link_margin_side}:{$menu_items_spacing_between}px;
		padding-top:{$nav_link_top_bottom_padding}px;
		padding-bottom:{$nav_link_top_bottom_padding}px; 
	}
	ul.UL_CLASS-menu-class li ul {
		margin-top:{$nav_link_top_bottom_padding}px; 
		$dropdown_bg_color
	}
	ul.UL_CLASS-menu-class ul li {
		margin-{$nav_link_margin_side}:0px;
		$dropdown_bg_color
		line-height:{$dropdown_lineheight}px;
	}
	ul.UL_CLASS-menu-class li.mi-search-inline,
	ul.UL_CLASS-menu-class li.mi-subscribebyemail {
		padding-top:{$nav_form_top_padding}px;
		line-height:17px; /* no ryhme or reason, just looks good */
	}
	ul.UL_CLASS-menu-class a {
		cursor:pointer;
		font-size:[~MENUKEY_link_font_size]px;
	}
	ul.UL_CLASS-menu-class li li a {
		font-size:[~MENUKEY_dropdown_link_textsize]px;
	}
	ul.UL_CLASS-menu-class li ul a:hover {
		ppCss::bgColorDec( 'MENUKEY_dropdown_bg_hover_color' ); 
	}
	ul.UL_CLASS-menu-class li.mi-twitter li {
		font-size:[~MENUKEY_dropdown_link_textsize]px;
	}
	ul.UL_CLASS-menu-class li.mi-twitter li:hover {
		ppCss::bgColorDec( 'MENUKEYdropdown_bg_hover_color' );
	}
	ul.UL_CLASS-menu-class li.split-right {
		float:right;
		margin-left:{$menu_items_spacing_between}px;
		margin-right:0;
	}
	ul.UL_CLASS-menu-class li.mi-twitter li span {
		color:[~MENUKEY_link_font_color]; 
	}

CSS;

	$css .= str_replace( array( '.UL_CLASS-menu-class', 'MENUKEY_' ), array( '.' . $menu['ul_class'], $menu['key'] ), $rawCss );
	
	$css .= <<<CSS
	ul.suckerfish input.nr-text {
		margin-top:0;
		margin-bottom:0;
	}
	ul.suckerfish li li {
		padding-top:0;
		margin-right:0;
	}
	ul.suckerfish li li,
	ul.suckerfish li.mi-search-inline,
	ul.suckerfish li.mi-subscribebyemail {
		padding-bottom:0;
	}
	ul.suckerfish li.mi-anchor-img,
	ul.suckerfish li.mi-anchor-text_and_icon {
		padding-top:0;
		padding-bottom:0;
	}
	ul.suckerfish img {
		display:block;
	}
	ul.suckerfish .mi-anchor-text_and_icon img {
		float:left;
	}
	ul.suckerfish .mi-anchor-text_and_icon .icon-align-left img {
		padding-right:0.4em;
	}
	ul.suckerfish .mi-anchor-text_and_icon .icon-align-right img {
		padding-left:0.4em;
	}
	ul.suckerfish .mi-anchor-text_and_icon span.icon-text {
		float:left;
	}
	ul.suckerfish li ul li.mi-anchor-text_and_icon {
		padding:0 !important;
	}
	ul.suckerfish li.mi-twitter ul {
		width:170px;
	}
	ul.suckerfish li.mi-twitter li {
		padding:5px 8px 3px 8px;
		width:184px;
	}
	ul.suckerfish li.mi-twitter ul li a {
		padding-left:0;
		width:154px;
	}
	ul.suckerfish li.mi-twitter ul li span a {
		display:inline;
		padding-right:0;
		text-decoration:underline !important;
	}
	.nav-ajax-receptacle {
		display:none;
		padding-top:20px;
		padding-bottom:20px;
	}
	.nav-ajax-receptacle .article-content {
		border-top-width:0;
		padding-top:0 !important;
	}
	body.pc li.mi-search-inline input {
		font-size:85%;
	}
CSS;
	
}









