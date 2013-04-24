<?php

$prophoto_info = ppUtil::siteData();

if ( !ppDrawer::inUse() ) {
	return;
}

// the magic numbers here are pixel amounts to achieve normal-looking visual spacing
$drawer_padding       = ppOpt::id( 'drawer_padding' );
$first_tab_top        = 20;
$tab_spacing          = 5;
$tab_font_size        = ppOpt::id( 'drawer_tab_font_size' ) ? ppOpt::id( 'drawer_tab_font_size', 'int' ) : 12;
$tab_letter_margin    = ( ( $tab_font_size / 12 ) < 1 ) ? 1 : intval( $tab_font_size / 12 );
$tab_width            = $tab_font_size * 2;
$tab_vertical_padding = intval( $tab_font_size * 0.75 );

// get drawer-specific settings and calculated info
$total_prev_tabs_offset = 0;
$drawer_specific_css = '';
for ( $i = 1; $i <= pp::num()->maxSidebarDrawers; $i++ ) {
	if ( !ppWidgetUtil::areaHasWidgets( 'drawer-' . $i ) ) continue;
	
	// calculate this offset
	$drawer[$i]['tab_offset'] = $first_tab_top + $total_prev_tabs_offset;
	$drawer[$i]['tab_offset_admin_bar'] = $drawer[$i]['tab_offset'] + 28;

	// add this tab's height to total offset for next tab's calculation
	$strLen = function_exists( 'mb_strlen' ) ? 'mb_strlen' : 'strlen';
	$drawer[$i]['tab_height'] = ( $tab_font_size + ( $tab_letter_margin * 2 ) ) * $strLen( ppOpt::id( 'drawer_tab_text_' . $i ) ) + ( $tab_vertical_padding * 2 );
	$total_prev_tabs_offset = $total_prev_tabs_offset + $drawer[$i]['tab_height'] + $tab_spacing;

	// set optionial per-drawer colors
	$drawer[$i]['tab_font_color']         = ppCss::colorDec( 'drawer_tab_font_color_' . $i );
	$drawer[$i]['widget_headline_color']  = ppCss::colorDec( 'drawer_widget_headlines_font_color_' . $i );
	$drawer[$i]['widget_font_color']      = ppCss::colorDec( 'drawer_widget_text_font_color_' . $i );
	$drawer[$i]['widget_link_font_color'] = ppCss::colorDec( 'drawer_widget_link_font_color_' . $i );
	$drawer[$i]['bg_color']               = ppCss::bgColorDec( 'drawer_bg_color_' . $i );

	$drawer[$i]['content_width'] = ppDrawer::contentWidth( $i );

	// drawer total width
	$drawer[$i]['width'] = $drawer[$i]['content_width'] + ( 2 * $drawer_padding );

	// drawer specific css
	$drawer_specific_css .= <<< CSS
	#tab_{$i} {
		top:{$drawer[$i]['tab_offset']}px;
		{$drawer[$i]['bg_color']}
		{$drawer[$i]['tab_font_color']}
	}
	.admin-bar #tab_{$i} {
		top:{$drawer[$i]['tab_offset_admin_bar']}px;
	}
	#drawer_{$i} {
		left:-{$drawer[$i]['width']}px;
	}
	#drawer_content_{$i} {
		width:{$drawer[$i]['content_width']}px;
		{$drawer[$i]['bg_color']}
	}
	#drawer_content_{$i} .widgettitle {
		{$drawer[$i]['widget_headline_color']}
	}
	#drawer_content_{$i}, #drawer_content_{$i} p {
		{$drawer[$i]['widget_font_color']}
	}
	#drawer_content_{$i} a,
	#drawer_content_{$i} a:link,
	#drawer_content_{$i} a:hover,
	#drawer_content_{$i} a:visited {
		{$drawer[$i]['widget_link_font_color']}
	}
	* html #drawer_{$i} {
		position: absolute; 
		top: expression((0 + (ignoreMe = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop)) + 'px'); 
		left:-{$drawer[$i]['width']}px;
	}

CSS;
}


// drawer widgets
$css .= ppCss::font( 'drawer_widget_headlines' )->rule( '.drawer_content h3.widgettitle' );
$css .= ppCss::font( 'drawer_widget_text' )->rule( '.drawer_content, .drawer_content p' );
$css .= ppCss::link( 'drawer_widget_link' )->rules( '.drawer_content' );

// drawer tab font
$drawer_tab_font_family = ppOpt::test( 'drawer_tab_font_family' ) ? 'font-family:' . ppOpt::id( 'drawer_tab_font_family' ) . ';' : '';
$drawer_tab_font_size   = ppOpt::test( 'drawer_tab_font_size' ) ? ppOpt::id( 'drawer_tab_font_size ' ) : '12';

// drawer opacity
$drawer_opacity = floatval( ppOpt::id( 'drawer_default_opacity' ) / 100 );
$drawer_padding_admin_bar = $drawer_padding + 28;

// rounded corners for drawer tabs
if ( ppOpt::test( 'drawer_tab_rounded_corners', 'on' ) ) $tab_border_radius = 
	'-moz-border-radius-topright:10px;
	 -moz-border-radius-bottomright:10px;
	 -webkit-border-top-right-radius:10px;
	 -webkit-border-bottom-right-radius:10px;';


$css .= <<<CSS
.tab {
	$drawer_tab_font_family
	font-size:$drawer_tab_font_size;
	background:[~drawer_default_bg_color];
	position:absolute;
	right:-{$tab_width}px;
	width:{$tab_width}px;
	text-align:center;
	color:[~drawer_tab_font_color];
	padding:{$tab_vertical_padding}px 0;
	opacity:$drawer_opacity;
	$tab_border_radius	
}
	.tab span {
		display:block;
		height:{$tab_font_size}px;
		padding:{$tab_letter_margin}px 0;
		line-height:{$tab_font_size}px;
		text-transform:[~drawer_tab_text_transform];
		font-size:{$tab_font_size}px;
	}
.drawer {
	padding:0;
	z-index:180; /* higher than suckerfish, lower than lightbox */
	position:fixed;
	top:0px;
}
.drawer_content {
	opacity:$drawer_opacity;
	padding:{$drawer_padding}px;
	background:[~drawer_default_bg_color];
	overflow:hidden;
}
.admin-bar .drawer_content {
	padding-top:{$drawer_padding_admin_bar}px;
}
.drawer li.widget {
	margin-bottom:[~drawer_widget_btm_margin]px;
}
/* drawer-specific settings */
$drawer_specific_css

CSS;



