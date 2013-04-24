<?php


$contact_success_msg = addslashes( ppOpt::id( 'contact_success_msg' ) );
$contact_error_msg   = addslashes( ppOpt::id( 'contact_error_msg' ) );


$blog_top_offset = ppOpt::id( 'blog_top_margin' );
if ( !ppOpt::test( 'blog_border', 'none' ) && ppOpt::test( 'blog_border_visible_sides', 'all_four_sides' ) ) {

	if ( ppOpt::test( 'blog_border', 'dropshadow' ) ) {
		$blog_top_offset += ppOpt::test( 'blog_border_shadow_width', 'narrow' )
			? pp::num()->blogDropshadowNarrowWidth
			: pp::num()->blogDropshadowWideWidth;
	} else {
		$blog_top_offset += ppOpt::id( 'blog_border_width' );
	}
}

$facebookLanguage = ppOpt::id( 'facebook_language' );


$jsCode .= <<<JAVASCRIPT

var cf = {};
var ppContactForm = {

	init: function(){
		cf = this;
		cf.formValid = true;
		cf.validationEvents();
		cf.menuLinkClicks();
		cf.submitEvent();
		cf.handleHash();
	},


	handleHash: function(){
		var hash = window.location.hash.substr(1);
		if ( hash === 'contact-form' ) {
			cf.toggleDisplay();
		}
		if ( hash === 'error' ) {
			$('#pp-contact-error-msg')
				.css('display', 'block')
				.animate({opacity: 1.0}, 3500, function(){cf.toggleDisplay()})
				.fadeTo(500,0)
				.slideUp(300)
				.html('<p>$contact_error_msg</p>');
		}
		if ( hash === 'success' ) {
			$('#pp-contact-success-msg')
				.css('display', 'block')
				.delay(3500)
				.fadeTo(500,0)
				.slideUp(300)
				.html('<p>$contact_success_msg</p>');
		}

		$('a[href*=#contact-form]').live('click',function(){
			cf.toggleDisplay();
		});
	},


	menuLinkClicks: function(){
		$('a.show-hidden-contact_form').click(function(){
			cf.toggleDisplay($(this));
			return false;
		});
	},


	toggleDisplay: function(clicked){
		if ( !$('#contactform').length ) {
			ppThrob.start(clicked);
			$('#contact-form').load(prophoto_info.theme_url+'/includes/contact-form.php?ajax=1', function(){
				cf.scrollToFormTop(function(){
					if ( $('#contact-form .pp-html-twitter-widget').length ) {
						ppTwitterWidgetsGetTweets( $('#contact-form' ) );
						ppSlidingTwitterControls( $('#contact-form' ) );
					 }
					ppThrob.stop(clicked);
					$('#contact-form').slideToggle(500);
					$('#referpage').val(window.location);
					$('a.show-hidden-contact_form').unbind('click').click(function(){
						$('#contact-form').slideToggle(500);
					});
					cf.validationEvents();
					cf.submitEvent();
					if ( $('#contact-form .fb-like-box').length ) {
						if ( typeof ppFacebook != "undefined" ) {
							ppFacebook.parse($('#contact-form'));
						}
					}
					ppJSForContext( $('#contact-form') );
				});
			});
		} else {
			cf.scrollToFormTop(function(){
				jQuery('#contact-form').slideToggle(500);
			});
		}
	},


	scrollToFormTop: function(callback){
		$('html,body').animate( { scrollTop:$('header').height() + $blog_top_offset }, 450, 'swing', function(){
			callback();
			callback = function(){}; // prevent it from running twice, for body and html
		});
	},


	validationEvents: function(){
		$('#contactform .pp-required-field input[type=text],#contactform textarea').blur(function(){

			if ( !$('#contactform').hasClass('submitted') ) {
				return;
			}

			var field = $(this);

			if ( field.val() == '' || ( field.attr('id') == 'email' && !cf.emailValid() ) ) {
				field.parents('div.pp-field').addClass('p4-has-error');
				cf.formValid = false;

			} else {
				field.parents('div.pp-field').removeClass('p4-has-error');
			}
		});
	},

	submitEvent: function(){
		$('#contactform').submit(function(){
			$(this).addClass('submitted');
			cf.formValid = true;
			$('#contactform input[type=text], #contactform textarea').blur();
			return cf.formValid;
		});
	},

	emailValid: function() {
		return $('#contact-form input#email').val().match(/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/);
	}

};

ppContactForm.init();





JAVASCRIPT;


