<?php


$loadingMsg  = rawurlencode( ppOpt::translate( 'mobile_loading' ) );
$errorMsg    = rawurlencode( ppOpt::translate( 'mobile_error_loading' ) );
$ajaxEnabled = ppOpt::id( 'mobile_ajax_links_enabled' );

$jsCode  = <<<JAVASCRIPT
	jQuery(document).bind('mobileinit', function(){
		jQuery.mobile.loadingMessage       = decodeURIComponent('$loadingMsg');
		jQuery.mobile.pageLoadErrorMessage = decodeURIComponent('$errorMsg');
		jQuery.mobile.ajaxEnabled          = $ajaxEnabled;
	});
JAVASCRIPT;



$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/jquery.mobile.js' );
$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/cookie.js' );
$jsCode .= file_get_contents( TEMPLATEPATH . '/dynamic/js/iLogger.js' );


$jsCode .= <<<JAVASCRIPT

	if ( jQuery.cookie( 'retina_display' ) == null ) {
		var retina = false;
		if ( window.devicePixelRatio ) {
			retina = ( window.devicePixelRatio >= 2 );
		}
		jQuery.cookie( 'retina_display', retina, { path: '/', expires: 600 } );
	}

JAVASCRIPT;


$jsCode .= 'jQuery(document).ready(function($){';

	$jsCode .= <<<JAVASCRIPT


	$('body').bind('pagebeforeshow',function(){
		$('body').removeClass('slideshow-init-complete');
		ppJSForContext($('body'));
		if ( typeof ppFacebook != "undefined" ) {
			ppFacebook.parse($('body'));
		}
	});

	$('select').live('change',function(){
		var toUrl = $(this).val();
		if ( toUrl.indexOf( prophoto_info.url ) === -1 || !$ajaxEnabled ) {
			window.location.href = toUrl;
		} else {
			$(this).selectmenu('disable');
			$.mobile.changePage( toUrl, {
				transition: 'slide'
			});
		}
	});


});
JAVASCRIPT;

return $jsCode;



