<?php 

/* logo */
if ( ppOpt::test( 'logo_swf_switch', 'on' ) && ppImg::id( 'logo_swf' )->exists ) {
	
	$logoSwf = ppImg::id( 'logo_swf' );
	$jsCode .= <<<JAVASCRIPT
	
	jQuery(document).ready(function($){
		if ( typeof swfobject !== "undefined" && !$('body').hasClass('mobile') ) {
			swfobject.embedSWF( "{$logoSwf->url}", "logo-img", "{$logoSwf->width}", "{$logoSwf->height}", "7.0.0", false, false, { wmode: "transparent" } );
		}
	});

	
JAVASCRIPT;
}



/* masthead */
if ( ppOpt::test( 'masthead_display', 'custom' ) && ppImg::id( 'masthead_custom_flash' )->exists ) {
	
	$mhSwf = ppImg::id( 'masthead_custom_flash' );
	$jsCode .= <<<JAVASCRIPT
	
	jQuery(document).ready(function($){
		if ( typeof swfobject !== "undefined" && !$('body').hasClass('mobile') && $('#masthead_image').hasClass('custom-flash') ) {
			swfobject.embedSWF( "{$mhSwf->url}", "masthead-img", "{$mhSwf->width}", "{$mhSwf->height}", "7.0.0", false, false, { wmode: "transparent" } );
		}
	});
	
	
	
JAVASCRIPT;
}


