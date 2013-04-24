/* need to use jQuery not $ because this gets ajaxed into global namespace */


p_mp = {};

prophoto_MusicPlayer = {


	playerObject: false,
	noSlideshowsAlreadyAutoStarted: true,
	useHtml5Player_cache: 'unset',
	slideshows: {},

	
	registerSlideshow: function(slideshow,slideshowID){
		p_mp = this;
		
		p_mp.slideshows[slideshowID] = {
			
			id: slideshowID,
			file: jQuery(slideshow).attr('data-music-file'),
			loop: ( jQuery(slideshow).attr('data-music-loop') == 'true' ),
			autostart: ( jQuery(slideshow).attr('data-music-autostart') == 'true' ),
			button: jQuery('.mp3player',slideshow),
			hasOverlay: ( jQuery('.initialOverlay',slideshow).length == 1 ),
			playing: false,
			
			
			init: function(){
				var id = this.id
				var click = isTouchDevice ? 'touchstart' : 'click';
				
				this.button.bind(click,function(){
					p_mp.slideshows[id].togglePlayState();
				});
				
				if ( this.autostart && p_mp.noSlideshowsAlreadyAutoStarted && !this.hasOverlay ) {
					p_mp.noSlideshowsAlreadyAutoStarted = false;
					this.play();
				}
				
				if ( this.hasOverlay ) {
					jQuery('.initialOverlay',slideshow).bind(click,function(){
						p_mp.slideshows[id].play();
					});
				}
				
				jQuery('a.fullscreen',slideshow).bind(click,function(){
					if ( p_mp.slideshows[id].playing ) {
						p_mp.slideshows[id].pause();
					}
				});
			},
			
			partialPause:function(){
				this.button.removeClass('playing').addClass('paused');
				this.playing = false;
			},
			
			
			pause: function(){
				this.partialPause();
				p_mp.player().pause();
			},
			
			
			play: function(){
				this.button.addClass('playing').removeClass('paused');
				this.playing = true;
				p_mp.pauseOtherSlideshows(this.id);
				p_mp.player().play(this.file,this.loop);
			},
			
			
			togglePlayState: function(){
				this.playing ? this.pause() : this.play();
			}
		};
		p_mp.slideshows[slideshowID].init();
	},
	
	
	pauseOtherSlideshows: function(playingID){
		for ( var id in p_mp.slideshows ) {
			if ( p_mp.slideshows.hasOwnProperty(id) && id != playingID ) {
				p_mp.slideshows[id].partialPause();
			}
		}
	},
	
	
	player: function(){
		if ( !p_mp.playerObject ) {
			if ( p_mp.useHtml5Player() ) {
				p_mp.playerObject = p_mp.html5Player;
			} else {
				p_mp.playerObject = p_mp.flashPlayer;
			}
			p_mp.playerObject.init();
		}
		return p_mp.playerObject;
	},
	
	
	useHtml5Player: function(){
		if ( p_mp.useHtml5Player_cache == 'unset' ) {
			var inUa = function(string){
				return ( navigator.userAgent.indexOf(string) !== -1 );
			};
			var useHtml5 = false;
			if ( inUa( 'iPod' ) || inUa( 'iPhone' ) || inUa( 'iPad' ) || inUa( 'MSIE 9.0' ) ) {
				useHtml5 = true;
			} else if ( inUa( 'Safari' ) && !inUa( 'Chrome' ) ) {
				useHtml5 = true;
			}
			p_mp.useHtml5Player_cache = useHtml5;
		}
		return p_mp.useHtml5Player_cache;
	},
	
	
	html5Player: {
		
		audioElement: false,
		filePlaying: false,
		
		init: function(){
			this.audioElement = new Audio();
		},
		
		pause: function(){
			this.audioElement.pause();
		},
		
		play: function(file,loop){
			if ( this.filePlaying == file ) {
				this.audioElement.play();
			} else {
				this.filePlaying       = file;
				this.audioElement.src  = file;
				this.audioElement.loop = loop;
				this.audioElement.load();
				this.audioElement.play();
			}
		}
	},
	
	
	flashPlayer: {
		
		filePlaying: false,
		 
		init: function(){
			jQuery('body').append('<div id="pp-flash-music-player-wrap"></div>');
			var loadFlash = function(){
				swfobject.embedSWF( prophoto_info.theme_url+'/includes/FlashAudioPlayer.swf', "pp-flash-music-player-wrap", "1", "1", "7.0.0" );
			};
			( typeof swfobject !== "undefined" ) ? loadFlash() : jQuery.getScript( prophoto_info.wpurl+'/wp-includes/js/swfobject.js',loadFlash);
		},
		
		play: function(file,loop){
			if ( this.ready() ) {
				if ( this.filePlaying == file ) {
					prophoto_playFlashAudio();
				} else {
					this.filePlaying = file;
					loop ? prophoto_loopOn() : prophoto_loopOff();
					prophoto_loadFlashAudio(file);
					prophoto_playFlashAudio();
				}
			} else {
				setTimeout( function(){ p_mp.flashPlayer.play(file,loop); }, 100 );
			}
		},
		
		pause: function(){
			if ( this.ready() ) {
				prophoto_stopFlashAudio()
			} else {
				setTimeout( function(){ p_mp.flashPlayer.pause(); }, 100 );
			}
		},
		
		ready: function(){
			if ( typeof prophoto_loadFlashAudio == "undefined" ) {
				return false;
			} else if ( typeof prophoto_loopOn == "undefined" ) {
				return false;
			} else if ( typeof prophoto_loopOff == "undefined" ) {
				return false;
			} else if ( typeof prophoto_playFlashAudio == "undefined" ) {
				return false;
			} else if ( typeof prophoto_stopFlashAudio == "undefined" ) {
				return false;
			} else {
				return true;
			}
		}
	}
	
};
