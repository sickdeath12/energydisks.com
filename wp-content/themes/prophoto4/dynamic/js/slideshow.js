

var contentWidth = $('body').attr('class').match(/content-width-[0-9]+/)[0].replace('content-width-','');
var fullWidth    = $('body').attr('class').match(/full-width-[0-9]+/)[0].replace('full-width-','');




/* the main slideshow function */
ppSetupSlideshows = function(context) {
	if ( context.hasClass('slideshow-init-complete') ) {
		return;
	}
	context.addClass('slideshow-init-complete');


	if ( !isTouchDevice ) {
		$('a.popup-slideshow',context).click(function(){
			var clicked, width, height, params, dims, offset = '';
			clicked = $(this);
			if ( clicked.hasClass('fullscreen') ) {
				width  = screen.availWidth;
				height = screen.availHeight;
			} else {
				dims   = clicked.attr('rel').split('x');
				width  = dims[0];
				height = dims[1];
				offset = ',left=20,screenX=20,top=20,screenY=20';
			}
			params = "location=0,menubar=0,height="+height+",width="+width+",toolbar=0,scrollbars=0,status=0,resizable=0"+offset;
			window.open( clicked.attr('href'), 'PopupWindow', params );
			return false;
		});
	}


	$('.pp-slideshow-not-loaded img',context).imageLoaded(function(){
		if ( $(this).attr('lazyload-src') && !$(this).hasClass('lazy-load-initiated') ) {
			return;
		}

		$(this).parent().removeClass('pp-slideshow-not-loaded');

		var _this = {}; // function-scoped stand-in for 'this'

		var ppSlideshow = {

			/* some object-scoped vars */
			midTransitionDelay: 0,
			delayTime: 1000,
			simulateSlowLoad: 0,
			visibleImgIndex: 0,
			stagedImgIndex: 1,
			showPlaying: false,
			clickedIndex: null,
			browserCantAa: ( $('body').hasClass('cant-antialias-downsized-imgs') ),
			isMobile: ( $('body').hasClass('mobile') ),
			isIpad: ( $('body').hasClass('ipad') ),
			click: isTouchDevice ? 'touchend' : 'click',
			isFullScreened: ( window.location.href.indexOf('fullscreen=1') !== -1 ),
			iPadFadeControlsDelayed: function(){},

			// ---------------------------- //
			// -- INITIATIZATION METHODS -- //
			// ---------------------------- //


			/* initiation method */
			init: function($startImg){
				_this = this;
				_this.$startImg    = $startImg;
				_this.$showWrap    = _this.$startImg.parents('.pp-slideshow');
				_this.id           = _this.$showWrap.attr('id').replace('pp-slideshow-','');
				_this.isPopup      = ( window.location.href.indexOf( 'popup=1' ) !== -1 );
				_this.hasMusic     = _this.$showWrap.attr('data-music-file');
				_this.$startImg.unbind('load');

				// load data from external json file
				_this.loadJSONData(function(){
					_this.markup.init();
					_this.css.init();
					_this.sortImages();
					_this.initImgPreloading();
					_this.events.init();
					_this.defineTransition();
					if ( _this.autostart() ) {
						_this.startShow();
					}
				});
				return _this;
			},


			/* load the per-instance slideshow data from external json file/s */
			loadJSONData: function(callback){
				if ( _this.isFullScreened ) {
					contentWidth = 'fullscreen';
				}
				var slideshowWidth = _this.$showWrap.parents('.nav-ajax-receptacle').length ? fullWidth : contentWidth;

				if ( _this.isMobile ) {
					slideshowWidth = $('body').attr('class').match(/mobile-display-width-[0-9]+/)[0].replace('mobile-display-width-','');
				}

				if ( /unitTesting=1/.test(window.location.href) ) {
					var unitGet = '&unitTesting=1';
					var method = window.location.href.match(/&method=[a-zA-Z0-9_]*/)[0].replace('&method=','');
					var slideshowFile = prophoto_info.wpurl+'/wp-content/uploads/test-slideshow/slideshow-test-method-'+method+'.js';
					var mastheadFile = prophoto_info.wpurl+'/wp-content/uploads/test-masthead-slideshow/masthead-test-method-'+method+'.js?';
				} else {
					var unitGet = '';
					var slideshowFile = _this.$showWrap.attr('data-options-file');
					var mastheadFile = prophoto_info.wpurl+'/?staticfile=masthead.js&';
					if ( typeof pp_preview_design_id !== "undefined" ) {
						mastheadFile += 'preview_design=' + pp_preview_design_id + '&';
						if ( slideshowFile ) {
							var sep = ( slideshowFile.indexOf( '?' ) === -1 ) ? '?' : '&';
							slideshowFile += sep + 'preview_design=' + pp_preview_design_id;
						}
					}
				}

				// masthead slideshow
				if ( _this.$showWrap.attr('id') == 'masthead_image' ) {
					_this.isMasthead = true;
					$.getJSON(mastheadFile+'cb='+_this.currentTime()+unitGet,function(loadedData){
						_this.params = loadedData;
						_this.opts = ( _this.isMobile && _this.params.mobile_opts ) ? _this.params.mobile_opts : _this.params.opts;
						if ( _this.isMobile ) {
							_this.params.imgs = $('body').hasClass('retina-display') ? _this.params.retina_imgs : _this.params.mobile_imgs;
						}
						_this.showThumbstrip = false;
						callback();
					});

				// regular slideshow
				} else {
					_this.isMasthead = false;
					var getGlobalData = function(){
						sep = ( slideshowFile.indexOf('?') !== -1 ) ? '&' : '?'
						$.getJSON(slideshowFile+sep+'cb='+_this.currentTime()+unitGet,function(globalData){
							_this.params = globalData;
							_this.opts = _this.params.opts;
						});
					};
					var getLocalData = function(){
						var local = prophoto_info.url+'/?slideshow_gallery_js='+_this.id+'&content_width='+slideshowWidth+'&cb='+_this.currentTime();
						if ( unitGet ) {
							local = local + unitGet+'&class='+classNamePartial+'&className='+className+'&method='+testMethod+'&vistest=1';
						}
						$.getJSON(local, function(localData){
							_this.overrideParams = localData;
							_this.overrideParams.imageOrder = 'sequential';
							_this.showThumbstrip = ( !_this.isMobile && !_this.overrideParams.disableThumbstrip );
							_this.showMobileControls = true;
							mergeData();
						});
					};

					var mergeData = function(){
						if ( !_this.params ) {
							setTimeout(function(){ mergeData(); },100);
							return;
						}
						$.extend(true,_this.params,_this.overrideParams);
						if ( _this.overrideParams.holdTime && _this.overrideParams.holdTime != "" ) {
							_this.opts.holdTime = _this.overrideParams.holdTime;
						}
						_this.opts.holdTime = _this.opts.holdTime * 1000;
						callback();
						if ( _this.isMobile ) {
							_this.mobileControls.init();
						}
						if ( _this.hasMusic ) {
							$('body').trigger('slideshow_with_music_ready', _this.$showWrap );
						}
					};

					getGlobalData();
					getLocalData();
				}
			},


			/*  sort images */
			sortImages: function(){
				if ( _this.opts.imageOrder == 'random' ) {
					var nonFirstImgs = [];
					var firstImgSrc = _this.$startImg.attr('src');
					var firstImgStart = firstImgSrc.replace('.'+firstImgSrc.split('.').pop(),'');
					for (var i=0; i < _this.params.imgs.length; i++) {
						if ( _this.params.imgs[i].fullsizeSrc.indexOf(firstImgStart) !== -1 ) {
							var firstImg = [ _this.params.imgs[i] ];
						} else {
							nonFirstImgs.push(_this.params.imgs[i]);
						}
					};
					nonFirstImgs = nonFirstImgs.sort(function(){return 0.5 - Math.random();});
					nonFirstImgs = nonFirstImgs.sort(function(){return 0.5 - Math.random();});
					_this.params.imgs = ( typeof firstImg == "undefined" ) ? nonFirstImgs : firstImg.concat(nonFirstImgs);
				}
				return _this;
			},


			/* initiate all image preloading */
			initImgPreloading: function(){

				// first image we know is loaded, set it up manually
				_this.params.imgs[0].isPreloaded = true;
				_this.params.imgs[0].width  = _this.$startImg.attr('width');
				_this.params.imgs[0].height = _this.$startImg.attr('height');
				_this.setImgDimensions(_this.$startImg[0],0);
				_this.applyImgDisplayDimensions(_this.$startImg,0);

				// preload the rest of the images
				for (var i=1; i < _this.params.imgs.length; i++) {
					_this.params.imgs[i].isPreloaded = false;
				};
				setTimeout(function(){
					_this.preloadImg(1);
				},_this.simulateSlowLoad);
				return _this;
			},


			/* recursive function to preload all images in order */
			preloadImg: function(imgIndex){
				if ( !_this.params.imgs[imgIndex] ) return false;

				var preloadNextImg = function(imgIndex){
					if ( ( imgIndex + 1 ) >= _this.params.imgs.length ) {
						if ( _this.opts.loopImages && !_this.isMobile ) { // causes mobile to scroll to top of page for some reason
							var firstImg = new Image();
							$(firstImg).load().attr('src',_this.imgSrc(0));
						}
						return false;
					}
					if ( _this.$showWrap.hasClass('showingSplashScreen') && imgIndex > 0 ) {
						return false;
					}
					return true;
				};
				var preloadedImage = new Image();

				$(preloadedImage).load(function(){
					_this.params.imgs[imgIndex].isPreloaded = true;
					_this.setImgDimensions(preloadedImage,imgIndex);

					if ( preloadNextImg(imgIndex) ) {
						setTimeout(function(){
							_this.preloadImg( imgIndex + 1 );
						},_this.simulateSlowLoad);
					}

				}).attr('src',_this.imgSrc(imgIndex));
				return _this;
			},


			/* note orig and calculate display dimensions */
			setImgDimensions: function(img,imgIndex){
				var displayWidth, displayHeight;
				displayWidth  = _this.params.imgs[imgIndex].width  = img.width;
				displayHeight = _this.params.imgs[imgIndex].height = img.height;

				// get and record natural dimensions for first image, for mobile reorientation resizing
				if ( imgIndex === 0 && _this.isMobile ) {
					if ( 'naturalHeight' in img ) {
						displayWidth  = _this.params.imgs[imgIndex].width  = img.naturalWidth;
						displayHeight = _this.params.imgs[imgIndex].height = img.naturalHeight;
					}
				}


				if ( displayWidth > _this.viewingAreaWidth ) {
					displayWidth  = _this.viewingAreaWidth;
					displayHeight = Math.round( displayWidth / ( img.width / img.height ) );
				}
				if ( displayHeight > _this.viewingAreaHeight ) {
					displayHeight = _this.viewingAreaHeight;
					displayWidth  = Math.round( displayHeight / ( img.height / img.width ) );
				}


				// stretch full-screened images to fit slideshow
				if ( _this.isFullScreened && displayWidth < _this.slideshowWidth && displayHeight < _this.slideshowHeight ) {
					wDiff = _this.slideshowWidth - displayWidth;
					hDiff = _this.slideshowHeight - displayHeight;
					if ( wDiff <= hDiff ) {
						displayWidth  = _this.slideshowWidth;
						displayHeight = Math.round( displayWidth / ( img.width / img.height ) );
					} else {
						displayHeight = _this.slideshowHeight;
						displayWidth  = Math.round( displayHeight / ( img.height / img.width ) );
					}
				}

				// correct rounding errors
				if ( _this.viewingAreaWidth - displayWidth == 1 ) {
					displayWidth = _this.viewingAreaWidth;
				}
				if ( _this.viewingAreaHeight - displayHeight == 1 ) {
					displayHeight = _this.viewingAreaHeight;
				}

				_this.params.imgs[imgIndex].displayWidth  = displayWidth;
				_this.params.imgs[imgIndex].displayHeight = displayHeight;
				_this.params.imgs[imgIndex].topPadding    = ( _this.viewingAreaHeight - displayHeight ) / 2;
			},




			mobileControls: {

				hideBtnsTimeout: 0,
				showingBtns: false,

				init: function(){
					if ( !_this.isMasthead ) {
						_this.mobileControls.markup();
						_this.mobileControls.css();
						_this.mobileControls.events();
					}
				},

				markup: function(){
					_this.$showWrap.append('<div class="mobileControls"><a class="prevNext prev"><span><em>prev</em></span></a><a class="playPause"><span><em>play</em></span></a><a class="prevNext next"><span><em>next</em></a></div>');
					if ( _this.hasMusic ) {
						$('.mobileControls',_this.$showWrap).append('<a class="mp3player paused"><span><em>audio play/pause</em></a></div>')
					}
					_this.$mobileControls = $('.mobileControls',_this.$showWrap);
				},

				css: function(){
					$('.playPause span, .prevNext em, .mp3player em', _this.$mobileControls ).css( 'background-image','url('+_this.params.btnsSrcs['sprite']+')' );
					$('.playPause',_this.$mobileControls).css('left', ( _this.viewingAreaWidth - 64 ) / 2 );
					$('.mp3player',_this.$mobileControls).css('left', ( _this.viewingAreaWidth - 64 ) / 2 );
					$('.prevNext',_this.$mobileControls).css('top', ( _this.viewingAreaHeight - 50 ) / 2 );
				},

				events: function(){
					var touchstart = /force_mobile=1/.test(window.location.href) ? 'click' : 'click';

					var showBtns = function(){
						_this.mobileControls.showBtns();
						_this.$mobileControls.bind('click',function(event){
							_this.mobileControls.showingBtns ? _this.mobileControls.hideBtns() : _this.mobileControls.showBtns();
						});
					};

					( _this.$splashScreen ) ? _this.$splashScreen.bind('splashscreenremoved',showBtns) : showBtns();

					_this.$mobileControls.bind('swipeleft',function(event){
						_this.touchPrevNext(true);
					});

					_this.$mobileControls.bind('swiperight',function(event){
						_this.touchPrevNext(false);
					});

					$('.playPause',_this.$mobileControls).bind(touchstart,function(){
						if ( !_this.showPlaying ) {
							_this.mobileControls.hideBtns($('.prevNext',_this.$mobileControls));
							_this.mobileControls.showBtns($('.playPause',_this.$mobileControls));
						} else {
							_this.mobileControls.showBtns($('a',_this.$mobileControls));
						}
						_this.showPlaying ? _this.stopShow() : _this.startShow();
						return false;
					});

					$('.mp3player',_this.$mobileControls).bind(touchstart,function(){
						_this.mobileControls.showBtns($('.playPause,.mp3player',_this.$mobileControls));
						return false;
					});

					$('.prevNext',_this.$mobileControls).bind(touchstart,function(){
						_this.touchPrevNext($(this).hasClass('next'));
						_this.mobileControls.showBtns($('a',_this.$mobileControls));
						return false;
					});
				},


				showBtns: function(toShow){
					if ( undefined == toShow ) {
						toShow = _this.showPlaying ? $('.playPause,.mp3player',_this.$mobileControls) : $('a',_this.$mobileControls);
					}
					toShow.show();
					_this.mobileControls.showingBtns = true;
					clearTimeout( _this.mobileControls.hideBtnsTimeout );
					_this.mobileControls.hideBtnsTimeout = setTimeout(function(){
						_this.mobileControls.hideBtns($('a',_this.$mobileControls));
					},4000);
				},


				hideBtns: function(btns){
					if ( btns == undefined ) {
						btns = $('a',_this.$mobileControls);
					}
					btns.fadeTo('fast',0,function(){
						$(this).hide().css('opacity',0.65);
						_this.mobileControls.showingBtns = false;
					});
				}
			},


			// ----------------------- //
			// -- MARKUP/CSS/EVENTS -- //
			// ----------------------- //

			/* setup all the markup we need for the show */
			markup: {

				/* call markup-writing submethods */
				init: function(){
					this.mainShowMarkup();
					this.splashScreenMarkup();
					_this.setupDimensions(); // controls markup needs to know show dimensions
					this.controlsMarkup();
					return _this;
				},


				/* write markup for main show */
				mainShowMarkup: function(){

					// private function to wrap img tag in wrapping a tag html
					var wrapImg = function($img,imgIndex){
						var aClass = ( imgIndex == 0 ) ? 'currentImg' : 'nextImg';
						$img.wrap('<a class="imgWrap ' + aClass  + '" href="#"></a>');
						_this.setImgLinkTo($img.parent(),imgIndex);
						$img.before('<img class="blankOverlay" src="'+prophoto_info.theme_url+'/images/blank.gif" />');
						if ( _this.imgSrc(imgIndex).indexOf('.png') !== -1 ) {
							$img.parent().addClass('png');
						}
					};

					// setup main image and wrapping divs
					_this.$showWrap.addClass('showWrap');
					_this.$startImg.wrap('<div class="imgViewingArea"></div>');
					wrapImg(_this.$startImg,0);
					_this.$visibleImg     = _this.$startImg;
					_this.$visibleImgWrap = _this.$startImg.parents('.imgWrap');
					_this.$imgViewingArea = _this.$startImg.parents('.imgViewingArea');

					// setup staged image markup
					_this.$visibleImgWrap.after('<img src="'+_this.imgSrc(1)+'" />');
					_this.$stagedImg = _this.$visibleImgWrap.next();
					wrapImg(_this.$stagedImg,1);
					_this.$stagedImgWrap = _this.$visibleImgWrap.next();
					_this.$imgWraps = $('.imgWrap',_this.$showWrap);

					// ajax loading spinner
					_this.$showWrap.prepend('<img class="loadingSpinner" src="http://prophoto.s3.amazonaws.com/img/ajaxLoadingSpinner.gif" />');
					_this.$loadingSpinner = $('.loadingSpinner',_this.$showWrap);

					// timer
					if ( _this.params.showTimer ) {
						_this.$imgViewingArea.append('<div class="timer"></div>');
						_this.$timer = $('.timer',_this.$imgViewingArea);
					}
				},


				/* write markup for splash screen */
				splashScreenMarkup: function(){

					if ( !_this.renderSplashScreen() ) {
						return;
					}

					// basic markup
					_this.$showWrap.addClass('showingSplashScreen');
					_this.$imgViewingArea.prepend('<div class="initialOverlay"><div class="content"><img class="startBtn" src="'+_this.params.btnsSrcs[ _this.browserCantAa ? 'start_aa' : 'start' ]+'" /></div><div class="bg"></div></div>');
					_this.$splashScreen        = $('.initialOverlay',_this.$showWrap);
					_this.$splashScreenContent = $('.content',_this.$splashScreen);
					_this.$splashScreenBg      = $('.bg',_this.$splashScreen);

					// optionally add SubTitle
					if ( _this.params.subtitle ) {
						_this.$splashScreenContent.prepend('<h4>'+_this.params.subtitle+'</h4>');
					}

					// optionally add Title
					if ( _this.params.title ) {
						_this.$splashScreenContent.prepend('<h3>'+_this.params.title+'</h3>');
					}

					// optionally add Logo
					if ( _this.params.splashScreenLogo ) {
						_this.$splashScreenContent.prepend( '<img class="logo" src="'+_this.params.splashScreenLogo.src+'" '+_this.params.splashScreenLogo.htmlAttr+' />');
					}

					_this.splashScreenVisible = true;
				},


				/* write markup for controls area */
				controlsMarkup: function(){
					_this.c = {};
					_this.c.btnsArea = {};

					if ( !_this.showThumbstrip ) {
						_this.c.longDimName = 'width';
						_this.c.showParallelDim = _this.slideshowWidth;
						_this.c.isVertical = false;
						return;
					}

					// min sizes for iPad
					if ( _this.isIpad ) {
						_this.params.thumbSize    = _this.params.thumbSize >= 100    ? _this.params.thumbSize    : 100;
						_this.params.thumbPadding = _this.params.thumbPadding >= 15  ? _this.params.thumbPadding : 15;
					}

					// start building object-scoped controls data sub-object
					_this.c.thumbUnitSize      = _this.params.thumbSize + 2*_this.params.thumbBorderWidth;
					_this.c.shortDim           = _this.params.thumbSize + 2*( _this.params.thumbBorderWidth + _this.params.thumbPadding );
					_this.c.isVertical         = ( _this.params.controlsPosition == 'left' || _this.params.controlsPosition == 'right' );
					_this.c.isHorizontal       = !_this.c.isVertical;
					_this.c.overlaid           = _this.isFullScreened ? true : _this.params.controlsOverlaid;
					_this.c.primaryBtnHeight   = _this.isIpad ? 40 : 20;
					_this.c.thumbUnitPadded    = _this.c.thumbUnitSize + _this.params.thumbPadding;
					_this.c.thumbPageBtnWidth  = _this.isIpad ? 44 : 22;
					_this.c.currentThumbPage   = 1;
					_this.c.userControlsThumbs = false;
					_this.c.autoHide           = _this.params.controlsAutoHide;

					// build basic controls markup
					var btnsSprite = '<img src="'+_this.params.btnsSrcs[ _this.browserCantAa ? 'sprite_aa' : 'sprite' ]+'" />';
					_this.$showWrap[(_this.params.controlsPosition == 'bottom') ? 'append' : 'prepend' ]
						('<div class="controls"><div class="controls-bg"></div><div class="thumbStrip"><div class="thumbsViewport"><div class="thumbsWrap"></div></div><a class="prevPage disabled"><div>'+btnsSprite+'</div></a><a class="nextPage"><div>'+btnsSprite+'</div></a></div><div class="btns"><a class="playPause">'+btnsSprite+'</a></div></div>');
					_this.$controls       = $('.controls', _this.$showWrap);
					_this.$controlsBg     = $('.controls-bg', _this.$controls);
					_this.$thumbStrip     = $('.thumbStrip', _this.$showWrap);
					_this.$thumbsViewport = $('.thumbsViewport', _this.$showWrap);
					_this.$thumbsWrap     = $('.thumbsWrap', _this.$showWrap);
					_this.$thumbPageBtns  = $('.prevPage,.nextPage',_this.$thumbStrip);

					// thumbs markup
					for (var i=0; i < _this.params.imgs.length; i++) {
						active = ( i === 0 ) ? 'active' : '';
						_this.$thumbsWrap.append('<img src="'+_this.params.imgs[i].thumbSrc+'" index="'+i+'" class="'+active+'">' );
					}
					_this.$thumbs = $('img', _this.$thumbsWrap);
					$('img[index='+_this.visibleImgIndex+']',_this.$thumbsWrap).addClass('active');

					// primary buttons area
					_this.$primaryBtnsArea  = $('.btns',_this.$controls);
					_this.$playPauseBtn     = $('.playPause',_this.$primaryBtnsArea);
					_this.$showWrap.addClass((_this.autostart()) ? 'playing' : 'paused' );
					if ( !_this.params.disableFullScreen ) {
						_this.$primaryBtnsArea.append('<a class="fullscreen">'+btnsSprite+'</a>');
						_this.$fullscreenBtn = $('.fullscreen',_this.$primaryBtnsArea);
					}
					if ( _this.params.shoppingCartUrl ) {
						_this.$primaryBtnsArea.append('<a class="cart-url" href="'+_this.params.shoppingCartUrl+'">'+btnsSprite+'</a>');
						_this.$shoppingCartBtn = $('.cart-url',_this.$primaryBtnsArea);
					}
					if ( _this.hasMusic ) {
						_this.$primaryBtnsArea.append('<a class="mp3player paused">'+btnsSprite+'</a>');
						_this.$audioBtn = $('.mp3player',_this.$primaryBtnsArea);
					}
					_this.$controlBtns = $('a',_this.$controls);
					_this.$primaryBtns = $('.btns a',_this.$controls);

					// more info into the controls data object
					_this.c.showParallelDim       = _this.c.isHorizontal ? _this.slideshowWidth  : _this.slideshowHeight;
					_this.c.showPerpDim           = _this.c.isHorizontal ? _this.slideshowHeight : _this.slideshowWidth;
					_this.c.shortDimName          = _this.c.isHorizontal ? 'height' : 'width';
					_this.c.longDimName           = _this.c.isHorizontal ? 'width'  : 'height';
					_this.c.btnsArea.width        = ( _this.c.primaryBtnHeight * 1.5 ) * ( _this.$primaryBtns.length > 1 ? 2 : 1 );
					_this.c.btnsArea.height       =  _this.$primaryBtns.length < 3 ? _this.c.primaryBtnHeight : 2*_this.c.primaryBtnHeight + 6;
					_this.c.btnsArea.xPosDir      = _this.c.isHorizontal ? 'right' : 'bottom';
					_this.c.btnsArea.centerPosDir = _this.c.isHorizontal ? 'top'   : 'left';
					_this.c.fromOpposite          = _this.c.showPerpDim - _this.c.shortDim;
					var opposites                 = { top:'bottom', bottom:'top', left:'right', right:'left' };
					_this.c.oppositeDir           = opposites[_this.params.controlsPosition];

					// paging of thumbnaiils
					var btnsOffset = _this.c.isHorizontal ? _this.c.btnsArea.width : _this.c.btnsArea.height;
					_this.c.thumbsPerPage = Math.floor( ( _this.c.showParallelDim - btnsOffset - 2*( _this.params.thumbPadding + _this.c.thumbPageBtnWidth ) ) / _this.c.thumbUnitPadded );
					_this.c.thumbPages = Math.ceil( _this.params.imgs.length / _this.c.thumbsPerPage );
					_this.c.thumbsViewportSize = _this.c.thumbUnitPadded * _this.c.thumbsPerPage - _this.params.thumbPadding;
				}
			},


			/* apply css to created markup */
			css: {

				/* call CSS-applying sub-methods */
				init: function(){
					if ( _this.params.showTimer ) {
						_this.$timer.css('opacity',0.35);
					}
					this.splashScreenCss();
					this.controlsCss();
					return _this;
				},


				/* apply CSS to splash screen */
				splashScreenCss: function(){

					if ( !_this.renderSplashScreen() ) {
						return;
					}

					// height of contact area
					var verticalPadding, splashScreenTotalHeight, splashScreenFromTop;
					var innerHeight = _this.isIpad ? 70 : 35; // 35 is height of start button graphic
					if ( _this.params.title ) innerHeight += parseInt( $('h3',_this.$splashScreenContent).css('font-size'), 10 ) + 12;
					if ( _this.params.subtitle ) innerHeight += parseInt( $('h4',_this.$splashScreenContent).css('font-size'), 10 ) + 12;
					if ( _this.params.splashScreenLogo ) innerHeight += $('.logo',_this.$splashScreenContent).height() + 12;

					// calculate padding to add to splash screen
					var requestedHeight = parseInt( _this.viewingAreaHeight * _this.params.splashScreenHeight, 10 );
					if ( requestedHeight < innerHeight + 20 ) {
						verticalPadding = 10;
					} else if ( _this.params.splashScreenHeight == 1 ) {
						verticalPadding = ( _this.slideshowHeight - innerHeight ) / 2;
					} else {
						verticalPadding = parseInt( ( requestedHeight - innerHeight ) / 2, 10 );
					}

					// calculate position of splashscreen
					splashScreenTotalHeight = innerHeight + 2*verticalPadding;
					switch ( _this.params.splashScreenPosition ) {
						case 'top':
							splashScreenFromTop = 0;
							break;
						case 'bottom':
							splashScreenFromTop = _this.viewingAreaHeight - splashScreenTotalHeight;
							break;
						case 'middle':
							splashScreenFromTop = ( _this.viewingAreaHeight - splashScreenTotalHeight ) / 2;
							break;
					}

					// apply splash screen css
					_this.$splashScreen.css({
						width: _this.viewingAreaWidth+'px',
						top: splashScreenFromTop+'px',
						height: splashScreenTotalHeight+'px'
					});
					_this.$splashScreenContent.css({
						height: innerHeight+'px',
						padding: verticalPadding+'px 20px',
						width:(_this.viewingAreaWidth-40)+'px'
					});
				},


				/* apply CSS to controls area */
				controlsCss: function(){

					if ( !_this.showThumbstrip ) {
						return;
					}

					// controls position specific classes for css
					_this.$showWrap
						.addClass('controlsPos-'+_this.params.controlsPosition)
						.addClass( _this.c.isHorizontal ? 'controlsHorizontal' : 'controlsVertical' );

					// primary buttons area
					var btnsAreaCss = {
						width:_this.c.btnsArea.width+'px',
						height:_this.c.btnsArea.height+'px'
					};
					btnsAreaCss[_this.c.btnsArea.xPosDir] = _this.params.thumbPadding+'px';
					btnsAreaCss[_this.c.btnsArea.centerPosDir] = ( 0.5*( _this.c.thumbUnitSize - ( _this.c.isHorizontal ? _this.c.btnsArea.height : _this.c.btnsArea.width ) ) + _this.params.thumbPadding ) + 'px';
					_this.$primaryBtnsArea.css(btnsAreaCss);

					// controls areas widths and heights
					var shortSideDimCss = {}, longSideDimCss = {};
					shortSideDimCss[_this.c.shortDimName] = _this.c.thumbUnitSize+'px';
					longSideDimCss[_this.c.longDimName]  = _this.c.thumbsViewportSize+'px';
					_this.cssToArgs(shortSideDimCss,_this.$controls,_this.$thumbsViewport,_this.$thumbPageBtns);
					_this.cssToArgs(longSideDimCss,_this.$thumbsViewport,_this.$thumbStrip);

					// make room for and show thumb page buttons
					if ( _this.c.thumbPages > 1 ) {

						_this.$controls.addClass('pagedThumbs');

						// center thumb paging buttons
						var marginDir = _this.c.isVertical ? 'left' : 'top';
						$('div',_this.$thumbPageBtns).css('margin-'+marginDir,((_this.c.thumbUnitSize-_this.c.primaryBtnHeight)/2) + 'px');

						// visually adjust for prevPage button space
						var shrunkPadding       = Math.round( _this.c.thumbPageBtnWidth / 10 );
						var shrunkPaddingOffset = parseInt(_this.$controls.css('padding-left')) - shrunkPadding;
						var paddingDir          =  _this.c.isVertical ? 'top' : 'left';
						_this.$controls.css('padding-'+paddingDir,shrunkPadding+'px');

					// thumbs fit in one page
					} else {
						var shrunkPaddingOffset = 0;
					}

					// controls width and height
					var controlsCss = {};
					controlsCss[_this.c.longDimName] = ( _this.c.showParallelDim - 2*_this.params.thumbPadding + shrunkPaddingOffset ) + 'px';

					// controls overlaying slideshow
					if ( _this.c.overlaid ) {
						_this.$showWrap.addClass('controlsOverlaid');
						controlsCss[_this.c.oppositeDir] = _this.c.fromOpposite + 'px';

					// controls NOT overlaying
					} else {
						_this.$showWrap.addClass('controlsNotOverlaid');
					}
					_this.$controls.css(controlsCss);
				}
			},


			/* bind all slideshow events */
			events: {

				/* call event-binding sub-methods */
				init: function(){
					_this.$imgWraps.click(function(){
						_this.$showWrap.click();
						return ( !$(this).hasClass('no-link') );
					});

					this.splashScreenEvents();
					this.controlsEvents();
					this.orientationChange();
					if ( _this.isIpad ) {
						this.iPadEvents();
					}
					return _this;
				},


				orientationChange: function(){
					$(window).bind('orientationchange',function(){
						_this.setupDimensions();
						for (var i=0; i < _this.params.imgs.length; i++) {
							_this.setImgDimensions(_this.params.imgs[i],i);
						};

						_this.applyImgDisplayDimensions(_this.$stagedImg,_this.stagedImgIndex);
						_this.applyImgDisplayDimensions(_this.$visibleImg,_this.visibleImgIndex);

						if ( _this.$controls ) {
							_this.$controls
								.css((_this.params.controlsPosition == 'top') ? 'bottom' : 'top','')
								.css((_this.params.controlsPosition == 'top') ? 'top' : 'bottom','0');
						}
						if ( _this.$showWrap.hasClass('showingSplashScreen') ) {
							_this.css.splashScreenCss();
						}
						if ( _this.isMobile ) {
							_this.defineTransition();
							if ( !_this.isMasthead ) {
								_this.mobileControls.css();
							}
						} else if ( _this.isIpad && _this.isFullScreened ) {
							if ( _this.hasMusic ) {
								_this.$audioBtn.hide().appendTo(_this.$showWrap).addClass('cloned-mp3-btn');
							}
							_this.$controls.remove();
							_this.markup.controlsMarkup();
							_this.css.controlsCss();
							_this.events.controlsEvents();
							_this.events.iPadControlsEvents();
							if ( _this.hasMusic ) {
								_this.$audioBtn.remove();
								$('.cloned-mp3-btn',_this.$showWrap).appendTo(_this.$primaryBtnsArea).show();
								_this.$audioBtn = $('.cloned-mp3-btn',_this.$showWrap);
								_this.$audioBtn.removeClass('cloned-mp3-btn');
							}
						}
					});
				},


				iPadEvents: function(){
					_this.$showWrap.swipe({
						triggerOnTouchEnd: false,
						allowPageScroll:"vertical",
						swipe: function(e,dir){
							_this.$showWrap.addClass('swiping');
							if ( dir != 'up' && dir != 'down' ) {
								if ( _this.swipingThumbs || _this.$showWrap.hasClass('transitioning') ) {
									return false;
								}
								var isNext = ( dir == 'left' );
								_this.touchPrevNext(isNext);
								return false;
							}
						}
					});
				},


				/* bind events related to splash screen */
				splashScreenEvents: function(){

					if ( !_this.$splashScreen ) {
						return;
					}

					// func to hide splash screen and optionally start show
					_this.hideSplashScreen = function(directClick){
						_this.splashScreenVisible = false;
						_this.$showWrap.removeClass('showingSplashScreen');
						_this.preloadImg(2); // finish preloading

						_this.$splashScreen.fadeOut(function(){
							_this.$showWrap.mousemove();
							if ( _this.params.startPlaying && directClick ) {
								_this.startShow();
							}
							_this.$splashScreen.trigger('splashscreenremoved').remove();
						});
					};

					// splash screen clicks
					_this.$splashScreen.bind( _this.click, function(){
						_this.hideSplashScreen(true);
						return false;
					});
				},


				/* bind events related to controls area */
				controlsEvents: function(){

					if ( !_this.showThumbstrip ) {
						return;
					}

					// hover over thumb
					var thumbOrigOpacity = $('.thumbsWrap img:not(.active):first').css('opacity');
					_this.$thumbs.hover(function(){
						$(this).animate({opacity:1},'fast');
					},function(){
						$(this).animate({opacity:thumbOrigOpacity},'fast');
					});

					// click on a thumb
					_this.$thumbs.bind(_this.click,function(e){
						var index = parseInt($(this).attr('index'));
						if ( index == _this.clickedIndex || _this.swipingThumbs ) {
							return;
						}
						_this.clickedIndex = index;
						_this.stopShow().showImg(_this.clickedIndex);
						if ( _this.$splashScreen ) {
							_this.hideSplashScreen();
						}
					});

					// thumbnail page prev/next button clicks
					_this.$thumbPageBtns.bind(_this.click,function(e){
						if ( $(this).hasClass('disabled') ) {
							return false;
						}

						// give user control of thumb paging, return to slideshow after 3.5 sec
						_this.c.userControlsThumbs = true;
						if ( _this.returnControlTimeout ) {
							clearTimeout( _this.returnControlTimeout );
						}
						_this.returnControlTimeout = setTimeout(function(){
							_this.c.userControlsThumbs = false;
						},3500);

						// move to requested page of thumbnails
						var toPage = ( $(this).hasClass('nextPage') ) ? _this.c.currentThumbPage + 1 : _this.c.currentThumbPage - 1;
						_this.gotoThumbPage( toPage );
						e.preventDefault();
						return false;
					});
					if ( _this.isIpad ) {
						$('.thumbsViewport',_this.$controls).swipe({
							threshold: 35,
							triggerOnTouchEnd: false,
							swipe: function(e,dir){
								if ( dir == 'up' || dir == 'down' ) {
									return false;
								}
								$( ( dir == 'right' ) ? '.prevPage' : '.nextPage', _this.$controls).trigger('touchend');
								e.preventDefault();
								_this.swipingThumbs = true;
								setTimeout(function(){ _this.swipingThumbs = false; }, 850 );
								return false;
							}
						});
					}


					// play/pause button clicks
					_this.$playPauseBtn.bind(_this.click,function(){
						if ( _this.$splashScreen ) {
							_this.hideSplashScreen();
						}
						_this.showPlaying ? _this.stopShow() : _this.startShow();
						return false;
					});

					// fullscreen button click
					if ( _this.$fullscreenBtn ) {
						_this.$fullscreenBtn.bind(_this.click,function(){
							if ( _this.isFullScreened ) {
								_this.isIpad ? history.go(-1) : window.close();
							} else {
								var url = prophoto_info.url + '?pp_slideshow_id=' + _this.id + '&fullscreen=1';
								if ( !isTouchDevice ) {
									window.open( url, '', 'height='+screen.availHeight+', width='+screen.availWidth+',directories=no, scrollbars=no, menubar=no, toolbar=no, location=no, resizeable=no, status=no, personalbar=no' );
								} else {
									window.location.href = url;
								}
								_this.stopShow();
							}
						});
					}

					// fullscreen-mode events
					if ( _this.isFullScreened ) {

						// escape key to exit fullscreen
						$(window).bind('keydown', function(e){
							var keyCode = e.keyCode || e.which;
							  if ( keyCode == 27 ) {
							    window.close();
							  }
						});
					}

					// autoHiding of controls
					if ( _this.c.overlaid && _this.c.autoHide ) {

						if ( !_this.isIpad ) {

							// object holds controls show/hide methods
							var animateControls = {

								showHideControlsSpeed: 265,

								visibleControlsCss: function(){
									var css = {};
									css[_this.c.oppositeDir] = _this.c.fromOpposite + 'px';
									return css;
								},

								hiddenControlsCss: function(){
									var css = {};
									css[_this.c.oppositeDir] = _this.c.showPerpDim + 'px';
									return css;
								},

								show: function(){
									_this.$controls.animate(
										this.visibleControlsCss(),
										this.showHideControlsSpeed,
										'easeOutExpo',
										function(){ _this.controlsHidden = false; }
									);
								},

								hide: function(){
									_this.$controls.animate(
										this.hiddenControlsCss(),
										this.showHideControlsSpeed * 0.9,
										'easeInQuart',
										function(){ _this.controlsHidden = true; }
									);
								}
							};

							// mousemove event
							_this.$showWrap.mousemove(function(){
								if ( _this.splashScreenVisible ) {
									return;
								}
								if ( _this.controlsHidden ) {
									animateControls.show();
								}
								clearTimeout(_this.controlsHideTimeout);
								_this.controlsHideTimeout = setTimeout(function(){animateControls.hide();},_this.params.controlsAutoHideTime);
							}).mousemove();


						// ipad show/hide controls
						} else {
							this.iPadControlsEvents();
						}
					}
				},

				iPadControlsEvents: function(){
					_this.iPadFadeControlsTimeout = 0;
					_this.iPadFadeControlsDelayed = function(){
						_this.$controls.show();
						_this.iPadFadeControlsTimeout = setTimeout(function(){
							_this.$controls.fadeOut();
						},4500);
					}
					$('a',_this.$controls).bind('touchend',function(){
						clearTimeout(_this.iPadFadeControlsTimeout);
						_this.iPadFadeControlsDelayed();
					});
					_this.$showWrap.bind('touchend',function(e){
						if ( _this.$showWrap.hasClass('swiping') || _this.$showWrap.hasClass('showingSplashScreen') ) {
							return false;
						}
						clearTimeout(_this.iPadFadeControlsTimeout);
						if ( _this.$controls.css('display') == 'block' ) {
							_this.$controls.hide();
						} else {
							_this.iPadFadeControlsDelayed();
						}
					});
					// prevent stray clicks on controls area from hiding them
					_this.$controls.bind('touchend',function(){
						return false;
					});
				}
			},


			/* retrieve and apply dimension info */
			setupDimensions: function(){

				if ( _this.isFullScreened ) {
					if( typeof( window.innerWidth ) == 'number' ) {
						_this.slideshowWidth    = window.innerWidth;
						_this.slideshowHeight   = window.innerHeight;
						_this.viewingAreaHeight = window.innerHeight;
						_this.viewingAreaWidth  = window.innerWidth;
					} else {
						_this.slideshowWidth    = document.documentElement.clientWidth;
						_this.slideshowHeight   = document.documentElement.clientHeight;
						_this.viewingAreaHeight = document.documentElement.clientHeight;
						_this.viewingAreaWidth  = document.documentElement.clientWidth;
					}

				} else {
					var dims = {};
					if ( _this.isMobile ) {
						dims = ( $('html').hasClass('portrait') ) ? _this.params.mobile_portrait_dims : _this.params.mobile_landscape_dims;
						dims.slideshowHeight   = dims.mobileSlideshowHeight;
						dims.slideshowWidth    = dims.mobileSlideshowWidth;
						dims.viewingAreaHeight = dims.mobileSlideshowHeight;
						dims.viewingAreaWidth  = dims.mobileSlideshowWidth;
					} else if ( _this.isIpad && !_this.isMasthead ) {
						dims.slideshowHeight   = _this.params.iPadSlideshowHeight;
						dims.slideshowWidth    = _this.params.iPadSlideshowWidth;
						dims.viewingAreaHeight = _this.params.iPadViewingAreaHeight;
						dims.viewingAreaWidth  = _this.params.iPadViewingAreaWidth;
					} else {
						dims = _this.params;
					}
					_this.slideshowHeight   = dims.slideshowHeight;
					_this.slideshowWidth    = dims.slideshowWidth;
					_this.viewingAreaHeight = dims.viewingAreaHeight;
					_this.viewingAreaWidth  = dims.viewingAreaWidth;
				}

				if ( _this.isIpad && !_this.isFullScreened ) {
					_this.slideshowHeight = _this.params.iPadSlideshowHeight;
				}

				var showCss = { height:_this.slideshowHeight+'px', width:_this.slideshowWidth+'px' };
				_this.$showWrap.css( showCss );
				$('.blankOverlay',_this.$imgViewingArea).css( showCss );

				var imgViewingAreaDimsCss = { height:_this.viewingAreaHeight+'px', width:_this.viewingAreaWidth+'px' };
				_this.$imgViewingArea.css(imgViewingAreaDimsCss);
				_this.$imgWraps.css(imgViewingAreaDimsCss);
				return _this;
			},


			/* apply calculated display dimension to an img element */
			applyImgDisplayDimensions: function($img,imgIndex){
				$img.attr('width',_this.params.imgs[imgIndex].displayWidth);
				$img.attr('height',_this.params.imgs[imgIndex].displayHeight);
				$img.parent().css('padding-top',_this.params.imgs[imgIndex].topPadding+'px');
			},




			// ----------------------- //
			// -- SLIDESHOW METHODS -- //
			// ----------------------- //

			/* start the slideshow */
			startShow: function(){
				_this.showPlaying = true;
				_this.$showWrap.addClass('playing').removeClass('paused');
				_this.iPadFadeControlsDelayed();
				_this.triggerTimedAdvance();
				return _this;
			},


			/* stop the slideshow */
			stopShow: function(){
				_this.showPlaying = false;
				_this.$showWrap.removeClass('playing').addClass('paused');
				_this.timer.stop();
				clearTimeout(_this.nextAdvance);
				return _this;
			},


			/* boolean test, are we continuing the show */
			continueShow: function(){
				if ( !_this.showPlaying ) return false;
				if ( _this.nextImgInShowIndex() === 0 && _this.opts.loopImages === false ) return false;
				return true;
			},


			/* trigger hold then advance */
			triggerTimedAdvance: function(){
				_this.nextAdvance = setTimeout(function(){
					_this.showImg(_this.nextImgInShowIndex());
				},_this.opts.holdTime);

				if ( _this.params.showTimer ) {
					_this.timer.start();
				}
				return _this;
			},


			/* start/stop timer */
			timer: {
				start: function(){
					if ( !_this.$timer ) return;
					var timerCss = {};
					timerCss[_this.c.longDimName] = _this.c.showParallelDim + 'px';
					_this.$timer.show().animate(timerCss,_this.opts.holdTime,'linear',_this.timer.stop);
				},
				stop: function(){
					if ( !_this.$timer ) return;
					if ( !_this.showPlaying ) {
						_this.$timer.stop();
					}
					_this.$timer.fadeOut(function(){ _this.$timer.css(_this.c.isVertical ? 'height' : 'width','0'); });
				}
			},


			/* animate to requested thumb page */
			gotoThumbPage: function(pageNum) {
				var toWhere = ( _this.c.thumbsViewportSize + _this.params.thumbPadding ) * ( pageNum - 1 );
				var dir = _this.c.isVertical ? 'top' : 'left'
				var animateCss = {};
				animateCss[dir] = '-'+toWhere+'px';
				if ( _this.c.currentThumbPage != pageNum ) {
					_this.animatingThumbPages = true;
					_this.$thumbsWrap.animate(
						animateCss,
						_this.params.thumbsPagingAnimation.speed,
						_this.params.thumbsPagingAnimation.easing,
						function(){ _this.animatingThumbPages = false; }
					);
				}
				_this.c.currentThumbPage = pageNum;
				_this.$thumbPageBtns.removeClass('disabled');
				if ( _this.c.currentThumbPage == _this.c.thumbPages ) {
					$('.nextPage',_this.$controls).addClass('disabled');
				}
				if ( _this.c.currentThumbPage == 1 ) {
					$('.prevPage',_this.$controls).addClass('disabled');
				}
			},




			// ------------------------ //
			// -- TRANSITION METHODS -- //
			// ------------------------ //

			/* transition from one img to next */
			showImg: function(imgIndex){
				if ( _this.params.imgs[imgIndex].isPreloaded ) {
					_this.loadingDelayIndication.hide();
					_this.stageImgForTransition(imgIndex);
					_this.animateTransition(_this.showPlaying);
				} else {
					_this.loadingDelayIndication.show(imgIndex);
					_this.delayTransition = setTimeout(function(){
						_this.showImg(imgIndex);
					},_this.delayTime);
				}
				return _this;
			},


			/* preparation for animating */
			stageImgForTransition: function(imgIndex){
				var stagedCss = _this.nextImgAnimateSetup;
				if ( _this.isTouchTransition && !_this.showPlaying ) {
					stagedCss = _this.touchedStagedImgSetup;
				}
				_this.$stagedImgWrap.css(stagedCss);
				_this.$stagedImg.attr('src',_this.imgSrc(imgIndex));
				_this.setImgLinkTo(_this.$stagedImgWrap,imgIndex);
				_this.applyImgDisplayDimensions(_this.$stagedImg,imgIndex);
				if ( _this.imgSrc(imgIndex).indexOf('.png') !== -1 ) {
					_this.$stagedImgWrap.addClass('png');
				} else {
					_this.$stagedImgWrap.removeClass('png');
				}
				_this.stagedImgIndex = imgIndex;
				return _this;
			},


			/* handle optional link-to URL for slideshow images */
			setImgLinkTo: function($imgWrap,imgIndex){
				if ( _this.params.imgs[imgIndex].linkToUrl ) {
					$imgWrap.attr('href',_this.params.imgs[imgIndex].linkToUrl).removeClass('no-link');
				} else {
					$imgWrap.attr('href','#').addClass('no-link');
				}
			},


			/* the actual animation of the img */
			animateTransition: function(playingWhenStarted){
				var currentImgAnimation, nextImgAnimation, transitionTime, easing;
				if ( _this.isTouchTransition && !_this.showPlaying ) {
					currentImgAnimation = ( _this.touchTransitionDir == 'next' ) ? _this.touchedNext_currentImgAnimation : _this.touchedPrev_currentImgAnimation;
					nextImgAnimation    = ( _this.touchTransitionDir == 'next' ) ? _this.touchedNext_nextImgAnimation    : _this.touchedPrev_nextImgAnimation;
					transitionTime      = 300;
					midTransitionDelay  = 0;
					easing              = 'jswing';
				} else {
					currentImgAnimation = _this.currentImgAnimation;
					nextImgAnimation    = _this.nextImgAnimation;
					transitionTime      = _this.opts.transitionTime;
					midTransitionDelay  = _this.midTransitionDelay;
					easing              = ( _this.opts.transitionType == 'steadyslide' ) ? 'linear' : 'jswing';
				}

				_this.$showWrap.addClass('transitioning');
				setTimeout(function(){
					_this.transitionHalfComplete();
				},_this.opts.transitionTime/2);
				_this.$visibleImgWrap.animate(currentImgAnimation,transitionTime,easing);
				setTimeout(function(){
					_this.$stagedImgWrap.animate(nextImgAnimation,transitionTime,easing,function(){
						_this.transitionComplete(playingWhenStarted);
					});
				},midTransitionDelay);
				_this.isTouchTransition = false;
				return _this;
			},


			/* method _this runs when transition half complete */
			transitionHalfComplete: function(){
				_this.visibleImgIndex = _this.stagedImgIndex;
				if ( _this.showThumbstrip ) {
					if ( !_this.c.userControlsThumbs ) {
						var goToThumbPage = Math.floor( ( _this.visibleImgIndex ) / _this.c.thumbsPerPage ) + 1;
						_this.gotoThumbPage( goToThumbPage );
					}
					$('img',_this.$thumbsViewport)
						.removeClass('active')
						.filter('img[index='+_this.visibleImgIndex+']')
						.addClass('active');
				}
			},


			/* method _this runs after transition completes */
			transitionComplete: function(playingWhenStarted){
				_this.$showWrap.removeClass('transitioning swiping');
				if ( playingWhenStarted == _this.showPlaying ) {
					_this.switchVisibleStaged();
					if ( _this.continueShow() ) {
						_this.triggerTimedAdvance();
					} else {
						_this.stopShow();
					}
					return _this;
				}
			},


			/* make current next and vice-versa */
			switchVisibleStaged: function(){

				// switch $ references
				var $newVisibleImgWrap, $newStagedImgWrap, $newVisibleImg, $newStagedImg;
				$newVisibleImgWrap    = _this.$stagedImgWrap;
				$newStagedImgWrap     = _this.$visibleImgWrap;
				_this.$visibleImgWrap = $newVisibleImgWrap;
				_this.$stagedImgWrap  = $newStagedImgWrap;
				$newVisibleImg        = _this.$stagedImg;
				$newStagedImg         = _this.$visibleImg;
				_this.$visibleImg     = $newVisibleImg;
				_this.$stagedImg      = $newStagedImg;

				// switch classes and CSS
				_this.$visibleImgWrap
					.removeClass('nextImg')
					.addClass('currentImg')
					.css('z-index','1');
				_this.$stagedImgWrap
					.removeClass('currentImg')
					.addClass('nextImg')
					.hide();
				return _this;
			},


			/* define specific transition */
			defineTransition: function(){
				switch ( _this.opts.transitionType ) {
					case 'steadyslide': // fallthrough
					case 'slide':
						_this.currentImgAnimation = { left: '-'+_this.slideshowWidth+'px' };
						_this.nextImgAnimation    = { left: '0' };
						_this.nextImgAnimateSetup = { left: _this.slideshowWidth+'px', display:'block', zIndex:'2' };
						if ( _this.opts.transitionType == 'steadyslide' && _this.opts.holdTime !== 0 ) {
							_this.opts.transitionTime = _this.opts.holdTime;
							_this.opts.holdTime = 0;
						}
						break;
					case 'topslide':
						_this.currentImgAnimation = { top: _this.slideshowHeight+'px' };
						_this.nextImgAnimation    = { top: '0' };
						_this.nextImgAnimateSetup = { top: '-'+_this.slideshowHeight+'px', display:'block', zIndex:'2' };
						break;
					case 'fade': // fallthrough
					case 'crossfade':
					default:
						_this.currentImgAnimation = { opacity: ( _this.opts.transitionType == 'crossfade' && _this.isMasthead ) ? 1 : 0 };
						_this.nextImgAnimation    = { opacity: 1 };
						_this.nextImgAnimateSetup = { top:'0', left:'0', opacity:'0', display:'block', zIndex:'2' };
						if ( _this.opts.transitionType == 'fade' ) {
							_this.midTransitionDelay = _this.opts.transitionTime;
						}
						break;

				}
				if ( _this.isMobile || _this.isIpad ) {
					_this.touchedNext_currentImgAnimation = { opacity: 1, left: '-'+_this.slideshowWidth+'px', right: 'auto' };
					_this.touchedNext_nextImgAnimation    = { opacity: 1, left: '0', right: 'auto' };
					_this.touchedNext_nextImgAnimateSetup = { opacity: 1, left: _this.slideshowWidth+'px', display:'block', zIndex:'2', right: 'auto' };
					_this.touchedPrev_currentImgAnimation = { opacity: 1, left: ''+_this.slideshowWidth+'px' };
					_this.touchedPrev_nextImgAnimation    = { opacity: 1, right: '0', left: 'auto' };
					_this.touchedPrev_nextImgAnimateSetup = { opacity: 1, right: _this.slideshowWidth+'px', display:'block', zIndex:'2', left: 'auto' };
				}
				return _this;
			},


			touchPrevNext: function(isNext){
				_this.stopShow();
				if ( _this.$showWrap.hasClass('transitioning') ) {
					return false;
				}
				_this.isTouchTransition = true;
				if ( isNext ) {
					_this.touchTransitionDir = 'next';
					_this.touchedStagedImgSetup = _this.touchedNext_nextImgAnimateSetup;
					_this.showImg(_this.nextImgInShowIndex());
				} else {
					_this.touchTransitionDir = 'prev';
					_this.touchedStagedImgSetup = _this.touchedPrev_nextImgAnimateSetup;
					_this.showImg(_this.prevImgInShowIndex());
				}
			},


			/* turn on/off delaying state */
			loadingDelayIndication: {
				show: function(imgIndex){
					_this.$showWrap.addClass('delaying');
					_this.$loadingSpinner.fadeIn();
				},
				hide: function(){
					_this.$showWrap.removeClass('delaying');
					_this.$loadingSpinner.fadeOut();
				}
			},




			// --------------------- //
			// -- UTILITY METHODS -- //
			// --------------------- //

			/* return src attribute based on index */
			imgSrc: function(imgIndex){
				return _this.params.imgs[imgIndex].fullsizeSrc;
			},


			cssToArgs: function(css){
				for ( var i = 1; i < arguments.length; i++ ) {
					arguments[i].css(css);
				};
				return _this;
			},


			/* return src attribute of next image to be shown */
			nextImgSrc: function(){
				return imgIndex.imgSrc(_this.nextImgInShowIndex());
			},


			/* return index of next image in order */
			nextImgInShowIndex: function(){
				return ( ( _this.visibleImgIndex + 1 ) > ( _this.params.imgs.length - 1 ) ) ? 0 : _this.visibleImgIndex + 1;
			},


			/* return index of prev image in order */
			prevImgInShowIndex: function(){
				return ( ( _this.visibleImgIndex - 1 ) < 0 ) ? _this.params.imgs.length - 1 : _this.visibleImgIndex - 1;
			},


			/* (int) get current milleseconds */
			currentTime: function(){
				var now = new Date();
				return now.getTime();
			},


			autostart: function(){
				if ( ( _this.isIpad || _this.isMobile ) && _this.hasMusic ) {
					return false;
				} else {
					return ( _this.isFullScreened || _this.$showWrap.hasClass('autostart') );
				}
			},


			renderSplashScreen: function(){
				if ( _this.isMasthead ) {
					return false;
				} else if ( !_this.autostart() ) {
					return true;
				} else {
					if ( ( _this.isIpad || _this.isMobile ) && _this.hasMusic ) {
						return true;
					} else if ( _this.isFullScreened ) {
						return false;
					} else {
						return !_this.autostart();
					}
				}
			}
		};

		ppSlideshow.init($(this));

	});

};

