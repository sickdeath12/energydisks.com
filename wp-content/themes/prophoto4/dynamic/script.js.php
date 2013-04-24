<?php 


$jsCode  = '// ProPhoto4 build #' . pp::site()->svn . ", generated at " . date( 'r' ) . "\n";
$jsCode .= "// do not edit this file, it is created by the theme, any edits will be lost\n\n";

$jsCode .= <<<JAVASCRIPT
	var isTouchDevice = ( "ontouchstart" in document.documentElement );
	var ppSetupSlideshows, ppLightboxGallery, ppJSForContext, ppGrid;
	var ppCssIsLoaded = function() {
		var cssTestRule = jQuery('body').css( 'border-left-color' );
		return ( cssTestRule == 'rgb(255, 0, 0)' || cssTestRule == '#ff0000' ); 
	};
JAVASCRIPT;


// start main jquery conditional
$jsCode .= 'if ( typeof jQuery != "undefined" ) {';


/* -- the one ready function to rule them all --  */
$jsCode .= 'jQuery(document).ready(function($){';

	$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/ajax-fetch-slidedown.js' );
	$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/slideshow.js' );
	$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/lazyloader.js' );
	
	require( TEMPLATEPATH . '/dynamic/js/contactForm.js.php' );
	require( TEMPLATEPATH . '/dynamic/js/comments.js.php' );
	require( TEMPLATEPATH . '/dynamic/js/twitter.js.php' );	
	require( TEMPLATEPATH . '/dynamic/js/lightbox.js.php' );
	require( TEMPLATEPATH . '/dynamic/js/navMenu.js.php' );
	require( TEMPLATEPATH . '/dynamic/js/misc.js.php' );
	require( TEMPLATEPATH . '/dynamic/js/drawers.js.php' );	
	require( TEMPLATEPATH . '/dynamic/js/grid.js.php' );
	
	$jsCode .= <<<JAVASCRIPT

	
	ppJSForContext = function( context ) {
		window.ppSetupSlideshows( context );
		window.ppLightboxGallery( context );
		ppImageProtection( context );
		ppUnObfuscate( context );
		ppGrid.events( context );
		context.trigger('contextual_js_complete');
	};
	
	ppJSForContext($('body'));
	
	
});
JAVASCRIPT;




$jsCode .= ppUtil::siteData( $returnJson = true );
$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/console.js' );
$jsCode .= file_get_contents( TEMPLATEPATH . '/adminpages/js/easing.js' );
$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/imageloaded.js' );
$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/lightboxPlugin.js' );
$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/throb.js' );
$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/iLogger.js' );
require( TEMPLATEPATH . '/dynamic/js/randomImages.js.php' );
require( TEMPLATEPATH . '/dynamic/js/flash.js.php' );




$customJs = addslashes( str_replace( array( "\r", "\n", "\t" ), ' ', preg_replace( '/<(\/)?script([^>]*)?>/', '', ppOpt::id( 'custom_js' ) ) ) );
$jsCode .= "\n\n/* user-js */\ntry { eval('$customJs'); } catch(e) {}";


// end main jquery conditional
$jsCode .= '} else { console.warn( "ProPhoto javascript did not run because jQuery was not defined. Try de-activating plugins to fix." ); }';


return $jsCode;


