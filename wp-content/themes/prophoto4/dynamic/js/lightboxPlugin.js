(function($) {
    $.fn.prophotoLightbox = function(ppLightboxInfo) {
		var pp = ppLightboxInfo;
		var isMobile = $('body').hasClass('mobile');
        settings = {
            overlayBgColor: pp.overlay_color,
            overlayOpacity: pp.overlay_opacity,
            fixedNavigation: pp.fixed_navigation,
            imageLoading: pp.img_loading,
            imageBtnPrev: pp.img_btn_prev,
            imageBtnNext: pp.img_btn_next,
            imageBtnClose: pp.img_btn_close,
            imageBlank: pp.img_blank,
            containerBorderSize: isMobile ? 5 : pp.border_width,
            containerResizeSpeed: pp.resize_speed,
            txtImage: pp.translate_image,
            txtOf: pp.translate_of,
            keyToClose: 'c',
            keyToPrev: 'p',
            keyToNext: 'n',
            imageArray: [],
            activeImage: 0
        };
        var jQueryMatchedObj = this;

        function _initialize() {
            _start(this, jQueryMatchedObj);
			$(window).bind('orientationchange',function(){
				__resize();
				_set_image_to_view();
			});
            return false;
        }

        function _start(objClicked, jQueryMatchedObj) {
            $('embed, object, select').css({
                'visibility': 'hidden'
            });
            _set_interface();
            settings.imageArray.length = 0;
            settings.activeImage = 0;
			var href, actual_title, use_title;
            if (jQueryMatchedObj.length == 1) {
				href = objClicked.getAttribute('href');
				actual_title = objClicked.getAttribute('title');
				use_title = ( href.indexOf(actual_title) != -1 ) ? '' : actual_title;
                settings.imageArray.push(new Array(href, use_title));
            } else {
                for (var i = 0; i < jQueryMatchedObj.length; i++) {
                    settings.imageArray.push(new Array(jQueryMatchedObj[i].getAttribute('href'), jQueryMatchedObj[i].getAttribute('title')));
                }
            }
            while (settings.imageArray[settings.activeImage][0] != objClicked.getAttribute('href')) {
                settings.activeImage++;
            }
            _set_image_to_view();
        }

        function _set_interface() {
            $('body').append('<div id="jquery-overlay"></div><div id="jquery-lightbox"><div id="lightbox-container-image-box" class="unsized"><div id="lightbox-container-image"><div id="lb-img-wrap"><img id="lightbox-image"></div><div style="" id="lightbox-nav"><a href="#" id="lightbox-nav-btnPrev"></a><a href="#" id="lightbox-nav-btnNext"></a></div><div id="lightbox-loading"><a href="#" id="lightbox-loading-link"><img src="' + settings.imageLoading + '"></a></div></div></div><div id="lightbox-container-image-data-box" style="display:none"><div id="lightbox-container-image-data"><div id="lightbox-image-details"><span id="lightbox-image-details-caption"></span><span id="lightbox-image-details-currentNumber"></span></div><div id="lightbox-secNav"><a href="#" id="lightbox-secNav-btnClose"><img src="' + settings.imageBtnClose + '"></a></div></div></div></div>');
            var arrPageSizes = ___getPageSize();
            $('#jquery-overlay').css({
                backgroundColor: settings.overlayBgColor,
                opacity: settings.overlayOpacity,
                width: arrPageSizes[0],
                height: arrPageSizes[1]
            }).fadeIn();
            var arrPageScroll = ___getPageScroll();
			var topPad = isMobile ? 10 : 20;
            $('#jquery-lightbox').css({
                top: arrPageScroll[1] + 10,
                left: arrPageScroll[0]
            }).show();
            $('#jquery-overlay,#jquery-lightbox').click(function() {
                _finish();
            });
            $('#lightbox-loading-link,#lightbox-secNav-btnClose').click(function() {
                _finish();
                return false;
            });
            $(window).resize(function() {
				if ( isMobile ) {
					return;
				}
                __resize();
            });
        }

		function __resize() {
			var arrPageSizes = ___getPageSize();
            $('#jquery-overlay').css({
                width: arrPageSizes[0],
                height: arrPageSizes[1]
            });
            var arrPageScroll = ___getPageScroll();
            $('#jquery-lightbox').css({
                top: arrPageScroll[1] + (arrPageSizes[3] / 10),
                left: isMobile ? 0 : arrPageScroll[0]
            });
		}

        function _set_image_to_view() {
	
            if (settings.fixedNavigation || isTouchDevice ) {
                $('#lightbox-image').fadeTo(pp.img_fadespeed, 0,
                function() {
					if (window.attachEvent) $('#lb-img-wrap').removeClass('loaded');
                    $('#lightbox-container-image-data-box,#lightbox-image-details-currentNumber').hide();
					$('#lightbox-nav-btnPrev, #lightbox-nav-btnNext').show();
					if ( settings.activeImage == 0 ) $('#lightbox-nav-btnPrev').hide();
					if (settings.activeImage == (settings.imageArray.length - 1)) $('#lightbox-nav-btnNext').hide();
                    $('#lightbox-loading').show();
                    var objImagePreloader = new Image();
                    objImagePreloader.onload = function() {
                        $('#lightbox-image').attr('src', settings.imageArray[settings.activeImage][0]);
                        _resize_container_image_box(objImagePreloader.width, objImagePreloader.height);
                        objImagePreloader.onload = function() {};
                    };
                    objImagePreloader.src = settings.imageArray[settings.activeImage][0];
                });
            } else {
                $('#lightbox-image').fadeTo(pp.img_fadespeed, 0,
                function() {
                    $('#lightbox-nav,#lightbox-nav-btnPrev,#lightbox-nav-btnNext,#lightbox-container-image-data-box,#lightbox-image-details-currentNumber').hide();
					if (window.attachEvent) $('#lb-img-wrap').removeClass('loaded');
					$('#lightbox-loading').show();
                    var objImagePreloader = new Image();
                    objImagePreloader.onload = function() {
                        $('#lightbox-image').attr('src', settings.imageArray[settings.activeImage][0]);
                        _resize_container_image_box(objImagePreloader.width, objImagePreloader.height);
                        objImagePreloader.onload = function() {};
                    };
                    objImagePreloader.src = settings.imageArray[settings.activeImage][0];
                });
            }

        };

        function _resize_container_image_box(intImageWidth, intImageHeight) {
			if ( isMobile ) {
				if ( $('html').hasClass('portrait') ) {
					if ( intImageWidth > 255 ) {
						intImageHeight = parseInt( ( 255 * intImageHeight ) / intImageWidth );
						intImageWidth  = 255;
					}
				} else {
					if ( intImageHeight > 225 ) {
						intImageWidth  = parseInt( ( 225 * intImageWidth ) / intImageHeight );
						intImageHeight = 225;
					}
				}
			}
            var intCurrentWidth  = $('#lightbox-container-image-box').width();
            var intCurrentHeight = $('#lightbox-container-image-box').height();
            var intWidth  = (intImageWidth + (settings.containerBorderSize * 2));
            var intHeight = (intImageHeight + (settings.containerBorderSize * 2));
            var intDiffW  = intCurrentWidth - intWidth;
            var intDiffH  = intCurrentHeight - intHeight;
            $('#lightbox-container-image-box').animate({
                width: intWidth,
                height: intHeight
            },
            settings.containerResizeSpeed,
            function() {
                _show_image();
				$('#lightbox-container-image-box').removeClass('unsized');
            });
            if ((intDiffW == 0) && (intDiffH == 0)) {
                if ($.browser.msie) {
                    ___pause(250);
                } else {
                    ___pause(100);
                }
            }
            $('#lightbox-container-image-data-box').css({
                width: intImageWidth
            });
            $('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css({
                height: intImageHeight + (settings.containerBorderSize * 2)
            });
        };
        function _show_image() {
            $('#lightbox-loading').hide();
			if (window.attachEvent) $('#lb-img-wrap').addClass('loaded');
            $('#lightbox-image').fadeTo(pp.img_fadespeed, 1,
            function() {
                _show_image_data();
                _set_navigation();
            });
            _preload_neighbor_images();
        };
        function _show_image_data() {
            $('#lightbox-container-image-data-box').fadeIn(500);
            $('#lightbox-image-details-caption').hide();
            if (settings.imageArray[settings.activeImage][1]) {
                $('#lightbox-image-details-caption').html(settings.imageArray[settings.activeImage][1]).show();
            }
            if (settings.imageArray.length > 1) {
                $('#lightbox-image-details-currentNumber').html(settings.txtImage + ' ' + (settings.activeImage + 1) + ' ' + settings.txtOf + ' ' + settings.imageArray.length).show();
            }
        }
        function _set_navigation() {
			$('#lightbox-nav').show();
            $('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css({
                'background': 'transparent url(' + settings.imageBlank + ') no-repeat'
            });
            if (settings.activeImage != 0) {
                if (settings.fixedNavigation || isTouchDevice ) {
                    $('#lightbox-nav-btnPrev').css({
                        'background': 'url(' + settings.imageBtnPrev + ') left 15% no-repeat',
                        'opacity': pp.btns_opacity
                    })
                    .unbind()
                    .bind('click',
                    function() {
                        settings.activeImage = settings.activeImage - 1;
                        _set_image_to_view();
                        return false;
                    });
                } else {
                    $('#lightbox-nav-btnPrev').unbind().hover(function() {
                        $(this).css({
                            'background': 'url(' + settings.imageBtnPrev + ') left 15% no-repeat',
                            'opacity': 0
                        }).fadeTo(pp.btn_fadespeed, pp.btns_opacity);
                    },
                    function() {
                        $(this).fadeTo(pp.btn_fadespeed, 0,
                        function() {
                            $(this).css({
                                'background': 'transparent url(' + settings.imageBlank + ') no-repeat'
                            });
                        });
                    }).show().bind('click',
                    function() {
                        settings.activeImage = settings.activeImage - 1;
                        _set_image_to_view();
                        return false;
                    });
                }
            } else {
				jQuery('#lightbox-nav-btnPrev').unbind().hide();
			}

            if (settings.activeImage != (settings.imageArray.length - 1)) {
                if (settings.fixedNavigation || isTouchDevice) {
                    $('#lightbox-nav-btnNext').css({
                        'background': 'url(' + settings.imageBtnNext + ') right 15% no-repeat',
                        'opacity': pp.btns_opacity
                    })
                    .unbind()
                    .bind('click',
                    function() {
                        settings.activeImage = settings.activeImage + 1;
                        _set_image_to_view();
                        return false;
                    });
                } else {
                    $('#lightbox-nav-btnNext').unbind().hover(function() {
                        $(this).css({
                            'background': 'url(' + settings.imageBtnNext + ') right 15% no-repeat',
                            'opacity': 0
                        }).fadeTo(pp.btn_fadespeed, pp.btns_opacity);
                    },
                    function() {
                        $(this).fadeTo(pp.btn_fadespeed, 0,
                        function() {
                            $(this).css({
                                'background': 'transparent url(' + settings.imageBlank + ') no-repeat'
                            });
                        });
                    }).show().bind('click',
                    function() {
                        settings.activeImage = settings.activeImage + 1;
                        _set_image_to_view();
                        return false;
                    });
                }
            } else {
				jQuery('#lightbox-nav-btnNext').unbind().hide();
			}
            _enable_keyboard_navigation();
        }
        function _enable_keyboard_navigation() {
            $(document).keydown(function(objEvent) {
                _keyboard_action(objEvent);
            });
        }
        function _disable_keyboard_navigation() {
            $(document).unbind('keydown');
        }
        function _keyboard_action(objEvent) {
            if (objEvent == null) {
                keycode = event.keyCode;
                escapeKey = 27;
            } else {
                keycode = objEvent.keyCode;
                escapeKey = objEvent.DOM_VK_ESCAPE;
            }
            key = String.fromCharCode(keycode).toLowerCase();
            if ((key == settings.keyToClose) || (key == 'x') || (keycode == escapeKey)) {
                _finish();
            }
            if ((key == settings.keyToPrev) || (keycode == 37)) {
                if (settings.activeImage != 0) {
                    settings.activeImage = settings.activeImage - 1;
                    _set_image_to_view();
                    _disable_keyboard_navigation();
                }
            }
            if ((key == settings.keyToNext) || (keycode == 39)) {
                if (settings.activeImage != (settings.imageArray.length - 1)) {
                    settings.activeImage = settings.activeImage + 1;
                    _set_image_to_view();
                    _disable_keyboard_navigation();
                }
            }
        }

        function _preload_neighbor_images() {
            if ((settings.imageArray.length - 1) > settings.activeImage) {
                objNext = new Image();
                objNext.src = settings.imageArray[settings.activeImage + 1][0];
            }
            if (settings.activeImage > 0) {
                objPrev = new Image();
                objPrev.src = settings.imageArray[settings.activeImage - 1][0];
            }
        }

        function _finish() {
			_disable_keyboard_navigation();
            $('#jquery-lightbox').remove();
            $('#jquery-overlay').fadeOut(function() {
                $('#jquery-overlay').remove();
            });
            $('embed, object, select').css({
                 'visibility': 'visible'
            });
        }

        function ___getPageSize() {
            var xScroll,
            yScroll;
            if (window.innerHeight && window.scrollMaxY) {
                xScroll = window.innerWidth + window.scrollMaxX;
                yScroll = window.innerHeight + window.scrollMaxY;
            } else if (document.body.scrollHeight > document.body.offsetHeight) {
                xScroll = document.body.scrollWidth;
                yScroll = document.body.scrollHeight;
            } else {
                xScroll = document.body.offsetWidth;
                yScroll = document.body.offsetHeight;
            }
            var windowWidth,
            windowHeight;
            if (self.innerHeight) {
                if (document.documentElement.clientWidth) {
                    windowWidth = document.documentElement.clientWidth;
                } else {
                    windowWidth = self.innerWidth;
                }
                windowHeight = self.innerHeight;
            } else if (document.documentElement && document.documentElement.clientHeight) {
                windowWidth = document.documentElement.clientWidth;
                windowHeight = document.documentElement.clientHeight;
            } else if (document.body) {
                windowWidth = document.body.clientWidth;
                windowHeight = document.body.clientHeight;
            }
            if (yScroll < windowHeight) {
                pageHeight = windowHeight;
            } else {
                pageHeight = yScroll;
            }
            if (xScroll < windowWidth) {
                pageWidth = xScroll;
            } else {
                pageWidth = windowWidth;
            }
            arrayPageSize = new Array(pageWidth, pageHeight, windowWidth, windowHeight);
            return arrayPageSize;
        };

        function ___getPageScroll() {
            var xScroll,
            yScroll;
            if (self.pageYOffset) {
                yScroll = self.pageYOffset;
                xScroll = self.pageXOffset;
            } else if (document.documentElement && document.documentElement.scrollTop) {
                yScroll = document.documentElement.scrollTop;
                xScroll = document.documentElement.scrollLeft;
            } else if (document.body) {
                yScroll = document.body.scrollTop;
                xScroll = document.body.scrollLeft;
            }
            arrayPageScroll = new Array(xScroll, yScroll);
            return arrayPageScroll;
        };

        function ___pause(ms) {
            var date = new Date();
            curDate = null;
            do {
                var curDate = new Date();
            }
            while (curDate - date < ms);
        };
        return this.unbind('click').click(_initialize);
    };
})(jQuery);