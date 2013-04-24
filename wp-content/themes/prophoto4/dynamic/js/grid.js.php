<?php 

$jsCode .= <<<JAVASCRIPT

	ppGrid = {
		
		events: function(context){

			if ( !isTouchDevice ) {
				$('.grid-style-img_rollover_text .grid-item',context).click(function(){
					var innerLink = $('a:first',this);
					if ( innerLink.hasClass('popup-slideshow') ) {
						innerLink.click();
					} else {
						window.location.href = innerLink.attr('href');
					}
				});
				$('.grid-style-img_rollover_text .grid-item',context).hover(function(){
					$('.grid-overlay',this).fadeIn('fast');
				},function(){
					$('.grid-overlay',this).fadeOut('fast');
				});

			} else {
				$('.grid-style-img_rollover_text .grid-item',context).bind('touchstart',function(e){
					$('.grid-overlay',this).fadeIn('fast').delay(4000).fadeOut('fast');
					return ( e.target.nodeName == 'A' );
				});
			}
		}
	};

JAVASCRIPT;


