<?php 

/* listener for slideshows with music */
$jsCode .= <<<JAVASCRIPT

$('body').bind('slideshow_with_music_ready',function(event,slideshow){
	slideshowID = $(slideshow).attr('id').replace('pp-slideshow-','');
	var registerSlideshow = function(){
		window.prophoto_MusicPlayer.registerSlideshow(slideshow,slideshowID);
	};
	if ( typeof window.prophoto_MusicPlayer != "undefined" ) {
		registerSlideshow();
	} else {
		$.getScript(prophoto_info.theme_url+'/dynamic/js/musicPlayer.js',registerSlideshow);
	}
});

JAVASCRIPT;



if ( ppBio::mightBeMinimized() ) {
	
	$jsCode .= <<<JAVASCRIPT
	
	$('a.show-hidden-bio').click(function(){
		$('#bio').slideToggle(350);
		return false;
	});	
	
JAVASCRIPT;
}



if ( ppOpt::test( 'maintenance_mode', 'on' ) ) {

	$jsCode .= <<<JAVASCRIPT

	$('#maintenance-mode-remind').delay(3500).fadeOut(3000);

JAVASCRIPT;
}



if ( !ppOpt::test( 'image_protection', 'none' ) ) {
	
	$jsCode .= <<<JAVASCRIPT
	
	function ppImageProtection(context) {
		if ( !$('body').is('.pp-dev, .pp-tech') ) {
			$('.article-content img',context)
				.not('.exclude, .thumbnail, .attachment-thumbnail')
				.add('.pp-lightbox-thumbs img')
				.bind('contextmenu', function(){return false;});
			$('#lightbox-nav a,#lightbox-nav,.pp-slideshow').live('contextmenu',function(){return false;});
		}
	}
	
JAVASCRIPT;

} else {
	$jsCode .= "function ppImageProtection(){}\n";
}



$jsCode .= <<<JAVASCRIPT


$('.post:last').addClass('last-post');


$('a.no-link').click(function(){
	return false;
});


 if ( !$("body").hasClass("mobile") ) {
	$('.article-content img[lazyload-src]').lazyload();
 }

function ppUnObfuscate(context) {
	var toHTML = function(codes){
		var chars = codes.split(','), HTML = '';
		for ( i = 0; i <= chars.length; i++ ) {
			if ( chars[i] ) {
				HTML = HTML + String.fromCharCode(chars[i]);
			}
		}
		return HTML;
	};
	$('.jsobf',context).each(function(){
		var aTag  = $(this).parents('a');
		var inner = ( $(this).hasClass('img') ) ? $(this).html() : toHTML($(this).text());
		aTag.attr('href',toHTML(aTag.attr('href')));
		aTag.html(inner);
	});
}


$('a[href="#top"]').click(function(){
	$('html, body').animate( { scrollTop:0 }, 500, 'easeOutQuad' );
	return false;
});

$('a.type-share_on_facebook,a.type-tweet_this_url,a.type-subscribe_by_email').click(function(){
	var height = $(this).hasClass('type-subscribe_by_email') ? 550 : 275;
	var width  = $(this).hasClass('type-share_on_facebook')  ? 665 : 600;
	window.open($(this).attr('href'),$(this).attr('href'),'location=0,menubar=0,height='+height+',width='+width+',toolbar=0,scrollbars=0,status=0');
	return false;
});


JAVASCRIPT;

