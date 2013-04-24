<?php 

if ( !ppDrawer::inUse() ) {
	return;
}


// drawer opacity
$drawer_opacity = floatval( ppOpt::id( 'drawer_default_opacity' ) / 100 );
// drawer padding
$drawer_padding =  ppOpt::id( 'drawer_padding' ) * 2;

$jsCode .= <<<JAVASCRIPT


/* instrument the animations for the sidebar tabs */
function ppSidebarDrawers() {
	
	// we need to know the stylesheet is loaded so we can calculate widths accurately
	if ( !ppCssIsLoaded() ) {
		setTimeout( function(){ ppSidebarDrawers(); }, 200 );
		return;
	}
	
	var drawer_padding = $drawer_padding;

	// set initial opacity and height of drawers
	$('.drawer_content, .tab').css('opacity', $drawer_opacity);
	$('.drawer_content').css('height', ($(window).height() - $drawer_padding) + 'px' ); 

	// function-scoped timeout var object
	var p4_close_drawer_timeout = new Object();

	// instrument each drawer
	$('.drawer').each(function(){
		var drawer = $(this);
		var id     = drawer.attr('id');
		var tab    = $('.tab', this);
		var width  = parseInt( $('.drawer_content', drawer).css( 'width' ) ) + $drawer_padding;
		var speed  = parseInt( width * 0.75 );

		// show the drawer on tab mouseover
		tab.mouseover(function(){
			drawer
				.css( 'z-index', '181' )
				.animate( { left:'0px' }, speed, 'swing' )
				.addClass( 'open' );
		});

		// handle iphone/ipad show/hides
		if ( isTouchDevice ) {
			$('#inner-wrap').unbind('touchstart').bind('touchstart',function(){
				drawer.mouseleave();
			});
			tab.bind('touchstart',function(){
				if ( drawer.hasClass('open') ) drawer.trigger('mouseleave');
			});
		}

		// set the timeout to close drawer on mouseleave
		drawer.mouseleave(function(){
			var delay = isTouchDevice ? 1 : 1000;
			clearTimeout( p4_close_drawer_timeout[id] );
			p4_close_drawer_timeout[id] = setTimeout( function(){
				drawer.animate( { left:'-' + width + 'px' }, speed, 'swing', function(){
					drawer.css( 'z-index', '180' ).removeClass('open');
				} );
			}, delay );
		});

		// restart the close drawer timout on mouseenter
		drawer.mouseenter(function(){
			clearTimeout( p4_close_drawer_timeout[id] );
		});
	});	
}

ppSidebarDrawers();

JAVASCRIPT;

