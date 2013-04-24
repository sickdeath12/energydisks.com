<?php 


/* nav dropdown opacity */
if ( ppOpt::id( 'primary_nav_menu_dropdown_opacity' ) < 100 ) {
	$primary_nav_menu_dropdown_opacity =  ppOpt::id( 'primary_nav_menu_dropdown_opacity' );
	$jsCode .= <<<JAVASCRIPT
	function ppNavMenuOpacity() {	
		if (!$.browser.msie) {	
			$('nav .primary-nav-menu li ul').css('opacity', 0.{$primary_nav_menu_dropdown_opacity});
		}
	}
	ppNavMenuOpacity();
JAVASCRIPT;
}


/* nav menu formatting */
$jsCode .= <<<JAVASCRIPT
function ppFormatNavMenu() {
	// remove tooltips
	$('nav .primary-nav-menu li ul a').attr('title','');
	
	// add arrow to dropdown with nested menu
	$('nav .primary-nav-menu li ul li:has(ul)').each(function(){
		var link = $(this).children('a');
		var linktext = link.html();
		link.html(linktext+' &raquo;');
	});
	// add underline to parent while child is being viewed
	$('nav .primary-nav-menu li ul li ul').hover(function(){
		$(this).parent().children('a').css('text-decoration','underline');
	},function(){
		$(this).parent().children('a').css('text-decoration','none');
	});

	$('body.headerlayout-pptclassic #primary-nav-ajax-receptacle').remove().insertAfter('#logo-wrap');
	
}
ppFormatNavMenu();
JAVASCRIPT;


if ( ppOpt::test( 'primary_nav_menu_align', 'center' ) || ppOpt::test( 'secondary_nav_menu_align', 'center' ) ) { 

	$jsCode .= <<<JAVASCRIPT
	
	var centerMenuCount = 0;
	
	var centerMenu = function(context){
		
		if ( ppCssIsLoaded() ) {
			
			var menu      = $('ul.'+context+'-nav-menu'),
			    wrapWidth = menu.parent().width(),
				rightPad  = $('li:first',menu).css('margin-right'),
				menuWidth = menu.width() - parseInt( rightPad );
				
			menu.css( 'padding-left', parseInt( (wrapWidth - menuWidth) / 2 ) + 'px' );
			
			// run additional times to correct miscalculations based on 
			// custom fonts that have not completely loaded
			centerMenuCount = centerMenuCount + 1;
			if ( centerMenuCount < 5 ) {
				setTimeout( function(){ centerMenu(context); }, 150 );
			} else if ( centerMenuCount > 100 ) {
				return;
			}
			
		} else {
			setTimeout( function(){ centerMenu(context); }, 50 );
		}
		
	};
	
JAVASCRIPT;


	if ( ppOpt::test( 'primary_nav_menu_align', 'center' ) && !ppOpt::test( 'headerlayout', 'pptclassic' ) ) {
		$jsCode .= "centerMenu('primary');\n";
	}
	if ( ppOpt::test( 'secondary_nav_menu_align', 'center' ) ) {
		$jsCode .= "centerMenu('secondary');\n";
	}
}



$jsCode .= <<<JAVASCRIPT

	if ( "ontouchstart" in document.documentElement ) {
		
		var touchstartTimeout = false;
		$('.suckerfish li').bind('touchstart',function(e){
			clearTimeout(touchstartTimeout);
			var touchedItem = $(this);
			$('.suckerfish li').not(touchedItem.parents()).removeClass('sfhover');
			touchedItem.addClass('sfhover');
			if ( !$(e.target).parent().hasClass('has-children') ) {
				e.stopPropagation();
				return true;
			}
			touchstartTimeout = setTimeout(function(){
				$('.suckerfish li').removeClass('sfhover');
			},6000);
			e.stopPropagation();
			return false;
		});
		$(document).bind('touchstart', function(){
			if ( $('.suckerfish li.sfhover').length ) {
				$('.suckerfish li').removeClass('sfhover');
			}
		});
	}


JAVASCRIPT;

