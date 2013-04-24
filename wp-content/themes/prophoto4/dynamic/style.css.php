<?php
/* -- master file for writing CSS */


$css = '/* ProPhoto4 build #' . pp::site()->svn . " */\n\n";

// include individual stylesheet sub-files
require( TEMPLATEPATH . '/dynamic/css/common.css.php' );
require( TEMPLATEPATH . '/dynamic/css/hardcoded.css.php' );
require( TEMPLATEPATH . '/dynamic/css/general.css.php' );
require( TEMPLATEPATH . '/dynamic/css/header.css.php' );
require( TEMPLATEPATH . '/dynamic/css/bio.css.php' );
require( TEMPLATEPATH . '/dynamic/css/widgets.css.php' );
require( TEMPLATEPATH . '/dynamic/css/widgetMenus.css.php' );
require( TEMPLATEPATH . '/dynamic/css/nav.css.php' );
require( TEMPLATEPATH . '/dynamic/css/contact.css.php' );
require( TEMPLATEPATH . '/dynamic/css/postheader.css.php' );
require( TEMPLATEPATH . '/dynamic/css/content.css.php' );
if ( ppOpt::test( 'comments_enable', 'true' ) ) {
	require( TEMPLATEPATH . '/dynamic/css/comments.css.php' );
}
require( TEMPLATEPATH . '/dynamic/css/gallery.css.php' );
require( TEMPLATEPATH . '/dynamic/css/lightbox.css.php' );
require( TEMPLATEPATH . '/dynamic/css/sidebar.css.php' );
require( TEMPLATEPATH . '/dynamic/css/drawer.css.php' );
require( TEMPLATEPATH . '/dynamic/css/grid.css.php' );
require( TEMPLATEPATH . '/dynamic/css/footer.css.php' );


// test rule for javascript to determine if the
// entire stylesheet has loaded
$css .= "body { border-left-color: #ff0000; }";


// extra bg images
for ( $i = 1; $i <= pp::num()->maxExtraBgImgs; $i++ ) { 
	$bg = ppImg::id( 'extra_bg_img_' . $i );
	if ( $bg->exists && ppOpt::test( 'extra_bg_img_' . $i . '_css_selector' ) ) {
		$css .=  ppCss::background( 'extra_bg_img_' . $i )->rule( trim( ppOpt::id( 'extra_bg_img_' . $i . '_css_selector' ) ) );
	}
}


// custom CSS goes after test rule so that invalid 
// CSS screw up javascript's reading of test rule
$css .= strip_tags( ppOpt::id( 'override_css' ) );

return $css;

