<?php

$logo          = ppImg::id( 'logo' );
$headerWidth   = ppOpt::id( 'blog_width' );
$mastheadWidth = $headerWidth - $logo->width;
$headerLayout  = ppOpt::id( 'headerlayout' );
$mastheadImg1  = ppImg::id( 'masthead_image1' );



/* --- logo --- */
// set logo position according to layout
if ( ppHelper::logoInMasthead() ) {
	$logo_position_type     = 'position: absolute;';
	$logo_vertical_position = 'top: 0;';
} else {                   
	$logo_position_type     = '';
	$logo_vertical_position = '';
}

// horizontally position the logo area according to layout
switch( $headerLayout ) {

	case "mastlogohead_nav":
		$left = ( $headerWidth / 2 ) - ( $logo->width / 2 );
		$logo_horizontal_position = "left: {$left}px;";
		break;
	
	case "mastheadlogo_nav":
		$logo_horizontal_position = 'right: 0;';
		break;
	
	case "logomasthead_nav":
		$logo_horizontal_position = 'left: 0;';
		break;
	
	default:
		$logo_horizontal_position = '';
}

// logo alignment
if ( NrUtil::isIn( 'logocenter', $headerLayout ) ) {
	$logo_margin = 'margin:0 auto;';
 	$logo_float  = 'float:none;';

} elseif ( NrUtil::isIn( 'logoleft', $headerLayout ) ) {
	$logo_margin = 'margin:0;';
 	$logo_float  = 'float:none;';

} elseif ( NrUtil::isIn( 'logoright', $headerLayout ) ) {
	$margin = $headerWidth - $logo->width;
	$logo_margin = "margin:0 0 0 {$margin}px;";
 	$logo_float  = 'float:none;';

} else {
	$logo_margin = '';
	$logo_float = '';
}


$css .= <<<CSS
#logo h1,
#logo h2,
#logo p {
	text-indent:-9999em;
}
h1#alt-h1,
h1#alt-h1 a,
h2#alt-h1,
h2#alt-h1 a {
	height:0 !important;
	overflow:hidden;
	width:0 !important;
	display:none !important;
}
#logo {
	overflow: hidden;
	$logo_position_type
	$logo_vertical_position
	$logo_horizontal_position
	width: {$logo->width}px;
	height: {$logo->height}px;
	$logo_margin
	$logo_float
}
CSS;



/* --- masthead --- */
$masthead_image_position = '';
$masthead_image_top = '';
$masthead_image_left = '';
$masthead_image_float  = '';
$masthead_image_width  = '';

switch( $headerLayout ) {
	
	case "logomasthead_nav":
		$masthead_image_width    = "width:{$mastheadWidth}px;";
		$masthead_image_position = 'position:absolute;';
		$masthead_image_top      = 'top:0;';
		$masthead_image_left     = "left:{$logo->width}px;";
		$masthead_image_float    = 'float:none;';
		$masthead_image_height   = $logo->height;
		break;
	
	case "mastheadlogo_nav":
		$masthead_image_width  = "width:{$mastheadWidth}px;";
		$masthead_image_height = $logo->height;
		break;
	
	case "mastlogohead_nav":
		$masthead_image_height = $logo->height;
		break;
	
	default:
		$masthead_image_width  = "width:{$headerWidth}px;";
		$masthead_image_float  = 'float:none;';
		$masthead_image_height = intval( NrUtil::constrainRectSide( $headerWidth, $mastheadImg1->width, $mastheadImg1->height ) );
}


// masthead position
switch ( $headerLayout ) {
	case "logomasthead_nav":
	case "mastlogohead_nav":
	case "mastheadlogo_nav":
	case "pptclassic":
		$masthead_position = 'position:relative;';
		break;
	default:
		$masthead_position = '';
}
	
// masthead height
$masthead_height = ppHelper::logoInMasthead() ? "height: {$logo->height}px" : '';

// masthead top/bottom border
$mastheadSelector = ( $headerLayout == 'pptclassic' ) ? '#masthead-image-wrapper' : '#masthead';
$css .= ppCss::border( 'masthead_top', 'top' )->onlyIf( ppOpt::test( 'masthead_top_border', 'on' ) )->rule( $mastheadSelector );
$css .= ppCss::border( 'masthead_btm', 'bottom' )->onlyIf( ppOpt::test( 'masthead_btm_border', 'on' ) )->rule( $mastheadSelector );
	
	
$css .= <<<CSS
.masthead-image {
	$masthead_image_width
	$masthead_image_position
	$masthead_image_top
	$masthead_image_left
	$masthead_image_float
	height: {$masthead_image_height}px;
	overflow: hidden;	
}
#masthead {
	overflow:hidden;
	$masthead_position
	$masthead_height
}
#masthead-image-wrapper {
	overflow:hidden;
	height: {$masthead_image_height}px;
}
.masthead-image a.no-link {
	cursor:default;
}
CSS;

if ( $headerLayout == 'logomasthead_nav' || $headerLayout == 'mastlogohead_nav' ) { 
	$css .= <<<CSS
	#logo-img-a {
		position:absolute;
		top:0;
		left:0;
		z-index:50; /* z-150 */
	}
CSS;
}



/* pptclassic */
if ( $headerLayout == "pptclassic") {
	$css .= <<<CSS
	#logo-wrap {
		position:relative;
	}
	nav .primary-nav-menu {
		float:left;
		display:inline;
		position:absolute;
		right:0;
		bottom:0;
	}
	nav ul.primary-nav-menu {
		padding-left:0;
	}
CSS;
}


/* prophoto classic bar */
if ( ppOpt::test( 'prophoto_classic_bar', 'on' ) ) { 
	$css .= <<<CSS
	#top-colored-bar {
		background:[~prophoto_classic_bar_color];
		height:[~prophoto_classic_bar_height]px;
	}
CSS;
}


