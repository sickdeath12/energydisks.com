/* lazyloader modified, from: http://pastebin.com/eegD9UHf */
(function($) {

    $.fn.lazyload = function(options) {
        var settings = {
            threshold : 2250
        };
                
        if( options ) {
            $.extend(settings, options);
        }
		
        var elements  = this;
		var positions = {};
		var cantFade  = $('body').hasClass('cant-fade-imgs-with-black');
		var lazyloadShow = function(loadedImg){
			cantFade ? loadedImg.show() : loadedImg.css('opacity',0).animate({opacity:1},300);
		};
		
		// on window scroll, check position of images + cause those within threshold to appear
		$(window).bind("scroll", function(event) {
			var _top = $(window).scrollTop();
			var _bottom = _top + $(window).height() + settings.threshold;
			for (var pos in positions) {
				if ( pos <= _bottom ) {
					for (var ele in positions[pos]) {
						ele = positions[pos][ele]
						$(ele).addClass("lazyload-loading").trigger("appear");
					}
					// unset triggered position so that we don't trigger it again
					positions[pos] = [];
				}
			}

			// remove loaded images from elements to loop through
			var temp = $.grep(elements, function(element) {
				return !element.loaded;
			});
			elements = $(temp);
		});
        
		// initialize every lazy-loaded image.  hide, store loaded var, then bind appear event
        this.each(function() {
            var _self = this;
            _self.loaded = false;
            
            // bind the actual appear event
            $(_self).one("appear", function() {
				/* we call the slideshow function before actually loading to workaround
				   an IE8 issue where the imageLoaded function was being called before
				   lazyloader was able to clean up by removing lazyloading class */
				if ( $(_self).hasClass('ss-first-img' ) ) {
					$(_self).addClass('lazy-load-initiated');
					ppSetupSlideshows($(_self).parent());
				}
                if (!this.loaded) {
                    $("<img />").bind("load", function() {
    		            $(_self)
		                    .attr("src", $(_self).attr("lazyload-src"))
							.removeClass("lazyload-loading");
						lazyloadShow($(_self));
		                _self.loaded = true;
						
		            })
		            .attr("src", $(_self).attr("lazyload-src"));
                };
            });
        });

		// create lookup table of positions for all lazyloaded imgs
		elements.each(function() {
			_p = $(this).offset().top;
			if(positions[_p]==undefined) {
				positions[_p] = [];
			}
			positions[_p].push(this);
		});
        
        // trigger window scroll to force any images to appear before any scrolling
        $(window).trigger("scroll");
        return this;
    };
})(jQuery);