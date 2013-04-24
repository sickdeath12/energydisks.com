jQuery(document).ready(function($){

	window.likeBoxPreview = {

		SDKLoaded: false,
		likeBoxes: {},

		load: function(id){
			if ( id.indexOf('__i__') !== -1 ) {
				return;
			}
			this.loadSDK();

			this.likeBoxes[id] = {

				init: function(){
					this.id      = id;
					this.wrap    = $('div[id$="'+this.id+'"]');
					this.form    = $('.fb-like-box-form',this.wrap);
					this.preview = $('.fb-like-box-preview',this.wrap)
					this.likeBox = $('.fb-like-box',this.preview);
					this.events();
					$('.fb-like-button-widget-wrap',this.wrap).addClass('init-complete');
				},

				setWidth: function(width){
					if ( !width ) {
						width = $('#widget-'+this.id+'-width',this.form).val();
					}
					width = parseInt( width );
					this.preview.css('width',width+'px');
					var totalWidth = width + 350,
					    leftMargin = totalWidth - 265;
					this.wrap.css({
						width: totalWidth + 'px',
						marginLeft: '-' + leftMargin + 'px'
					});
				},

				events: function(){
					var _this = this;

					$('a.widget-action').click(function(){
						var setWidth = function(){
							if ( $('.widget-inside',_this.wrap).css('display') == 'block' ) {
								_this.setWidth();
							}
						}
						setTimeout(function(){ setWidth(); },10);
						setTimeout(function(){ setWidth(); },50);
						setTimeout(function(){ setWidth(); },100);
					});

					$('#widget-'+this.id+'-href').blur(function(){
						if ( $(this).val() != _this.likeBox.attr('data-href') ) {
							_this.likeBox.attr('data-href',$(this).val());
							_this.refreshLikeBox();
						}
					});
					$('#widget-'+this.id+'-width').blur(function(){
						var newWidth = $(this).val(),
						    oldWidth = _this.likeBox.attr('data-width');
						if ( newWidth != oldWidth && parseInt( newWidth ) > 75 ) {
							_this.setWidth(newWidth);
							_this.likeBox.attr('data-width',newWidth);
							_this.refreshLikeBox();
						}
					});
					$('#widget-'+this.id+'-colorscheme').change(function(){
						_this.likeBox.attr('data-colorscheme',$(this).val());
						_this.preview.removeClass('light dark').addClass($(this).val());
						_this.refreshLikeBox();
					});
					$('#widget-'+this.id+'-stream').change(function(){
						var stream = $(this).is(':checked');
						_this.form.removeClass('show-stream hide-stream').addClass( stream ? 'show-stream' : 'hide-stream' );
						_this.likeBox.attr('data-stream', stream ? 'true' : 'false' );
						_this.refreshLikeBox();
					});
					$('#widget-'+this.id+'-show-faces').change(function(){
						var showFaces = $(this).is(':checked');
						_this.form.removeClass('show-faces hide-faces').addClass( showFaces ? 'show-faces' : 'hide-faces' );
						_this.likeBox.attr('data-show-faces', showFaces ? 'true' : 'false' );
						_this.refreshLikeBox();
					});
					$('#widget-'+this.id+'-header').change(function(){
						_this.likeBox.attr('data-header',$(this).is(':checked')?'true':'false');
						_this.refreshLikeBox();
					});
				},

				refreshLikeBox: function(){
					this.likeBox.empty();
					var newLikeBoxMarkup = this.preview.html();
					this.likeBox.remove();
					this.preview.append(newLikeBoxMarkup);
					this.likeBox = $('.fb-like-box',this.preview);
					window.likeBoxPreview.parse();
				}

			};
			this.likeBoxes[id].init();

		},

		loadSDK: function(locale){
			if ( !this.SDKLoaded ) {
				$('body').append('<div id="fb-root"></div>');
				var js, fjs = document.getElementsByTagName('script')[0];
				js = document.createElement('script');
				js.id = "facebook-jssdk";
				js.src = "//connect.facebook.net/"+$('.fb-like-button-widget-wrap:first').attr('data-locale')+"/all.js#xfbml=1";
				fjs.parentNode.insertBefore(js, fjs);
				this.SDKLoaded = true;
			} else {
				this.parse();
			}
		},

		parse: function(){
			if ( typeof FB != "undefined" ) {
				try { FB.XFBML.parse(); } catch(e) {}
			}
		},

		loadDraggedIn: function(){
			$('.widget-liquid-right .fb-like-button-widget-wrap:not(".init-complete")').each(function(){
				var widget = $(this),
				id = widget.attr('data-widget-id');
				window.likeBoxPreview.load(id,'en_US');
				window.likeBoxPreview.likeBoxes[id].setWidth();
			});
		}

	};

});



jQuery(document).ready(function($){
	$('div.widgets-sortables').bind('sortstop',function(){
		setTimeout(function(){
			window.likeBoxPreview.loadDraggedIn();
		},500);
	});
});




jQuery(document).ready(function($){
	// widget farbtastic color wheel clicks
	$('.widget-liquid-right .colorpicker-swatch').live('click', function(){
		var this_click = jQuery(this);
		var id = this_click.attr('id' );
		var target = id.replace(/swatch/, 'picker-wrap' );
		var display = jQuery('#'+target).css('display' );
		(display == 'block') ? $('#'+target).fadeOut(300) : $('#'+target).fadeIn(300);
		var bg = (display == 'block') ? '0px 0px' : '0px -24px';
		$(this).css('background-position', bg);
	});
	// add spacing between bio and footer widget cols
	$('.widget-liquid-right .widgets-holder-wrap:eq(0), .widget-liquid-right .widgets-holder-wrap:eq(5), .widget-liquid-right .widgets-holder-wrap:eq(6), .widget-liquid-right .widgets-holder-wrap:eq(10)').css('padding-bottom', '30px');

	// add class to our widgets to highlight them
	$('.widget').each(function(){
		if ( /ProPhoto /.test( $( 'h4', this ).text() ) ) $( this ).addClass( 'pp-widget' );
	});

	/* html widget live preview */
	var p4_text_live_update;
	$('.p4-text textarea, .p4_tag_btn, .p4-text input[type="checkbox"]').live('click',function(){
		var clicked = jQuery(this);
		var form = clicked.parents('.p4-text');
		var preview = jQuery('.p4-text-preview', form);
		var wpautop = jQuery('input[type="checkbox"]', form);
		clearInterval(p4_text_live_update);
		p4_text_live_update = setInterval(function(){
			var html = $('textarea', form).val();
			if ( html == '' || html.indexOf('<') == -1 || html.indexOf('<script') != -1 || html.indexOf('<style') != -1 || html.indexOf('<form') != -1 ) {
				$('.p4-text-target', form).html('');
				preview.addClass('hidden');
			} else {
				if (wpautop.is(':checked')) html = html.replace(/\n/g, '<br />');
				$('.p4-text-target', form).html(html);
				preview.removeClass('hidden');
			}
		}, 500);
	});

	/* html widget */
	$('.p4_tag_btn').live('click',function(){
		// build tags
		var tag = $(this).attr('tag');
		var open_tag  = '<' + tag + '>';
		var close_tag = '</' + tag + '>';
		if ( tag == 'a' ) {
			var href = prompt('Enter URL:', 'http://');
			if (href == '' || href == null) return false;
			open_tag = open_tag.replace('>', ' href="' + href + '">')
		} else if ( tag == 'e' ) {
			open_tag  = '<a>';
			close_tag = '</a>';
			var email = prompt("Enter email address:\n(will be hidden from spambots)");
			if (email == '' || email == null) return false;
			open_tag = open_tag.replace('>', ' href="mailto:' + email + '">')
		}

		// setup browser variants
		var textbox = $('textarea', $(this).parent())[0];
		if ( typeof textbox.selectionStart != 'undefined' ) {
			var good_browser = true;
		} else if ( document.selection ) {
			var good_brower = false;
		} else {
			return alert('Sorry, browser not supported. Try using Firefox, Safari, or Internet Explorer');
		}

		// get highlighted selection
		if ( good_browser ) {
			var len = textbox.value.length;
	        var start = textbox.selectionStart;
	        var end = textbox.selectionEnd;
	        var highlighted = textbox.value.substring(start, end);
		} else {
			textbox.focus();
	        var sel = document.selection.createRange();
			var highlighted = sel.text;
		}

		// remove tags if open and close found in selection
		if (highlighted.indexOf(open_tag) >= 0 && highlighted.indexOf(close_tag) >= 0) {
			var open_tag_regex  = new RegExp(open_tag, 'gi');
			var close_tag_regex = new RegExp(close_tag, 'gi');
			highlighted = highlighted.replace(open_tag_regex, '');
			highlighted = highlighted.replace(close_tag_regex, '');
			open_tag = close_tag = '';

		// selection spans just one tag
		} else if (highlighted.indexOf(open_tag) >= 0 || highlighted.indexOf(close_tag) >= 0) {
			return false;
		}

		// update textarea
		var replace = open_tag + highlighted + close_tag;
		if ( good_browser ) {
	        textbox.value = textbox.value.substring(0, start) + replace + textbox.value.substring(end, len);
	        textbox.focus();
	        var new_end = open_tag.length + end;
	        textbox.setSelectionRange(new_end, new_end);
		} else {
			sel.text = replace;
	        var range = textbox.createTextRange();
	        range.collapse(false);
	        range.select();
		}
	});

});


/* form js for twitter slider */
var twitterSlider;

jQuery(document).ready(function($){

	twitterSlider = {

		incrementTweetBoxClick: function( clicked ){
			var increment  = clicked.parents('.incrementable'),
				adjustWhat = increment.attr('data-adjusts'),
				widget     = clicked.parents('.pp-twitter-slider-wrap'),
				currentVal = parseInt( $('input',increment).val() ),
				newVal     = clicked.hasClass('up') ? currentVal + 1 : currentVal - 1 ;

			$('input',increment).val(newVal);
			this.adjustTweetBoxPreview( adjustWhat, widget, newVal );
		},


		incrementTweetBoxDirect: function( textInput ){
			var adjustWhat = textInput.parents('.incrementable').attr('data-adjusts'),
				widget     = textInput.parents('.pp-twitter-slider-wrap'),
				newVal     = textInput.val();

			this.adjustTweetBoxPreview( adjustWhat, widget, newVal );
		},


		adjustTweetBoxPreview: function( adjustWhat, widget, newVal ) {
			if ( adjustWhat == 'tweet_height' ) {
				$('.viewer', widget).css('border-color', 'red');
				$('.badge-inner, .viewer, li', widget).css('height', newVal+'px');
				$('.tweet_height').text(newVal);

			} else if ( adjustWhat == 'tweet_width' ) {
				$('.viewer', widget).css('border-color', 'red');
				$('.viewer, ul', widget).css('width', newVal+'px');
				var boxWidth = newVal + 26;
				$('.badge-inner', widget).css('width', boxWidth+'px');

			} else if ( adjustWhat == 'pos_top' ) {
				$('.viewer', widget).css('border-color', 'transparent');
				$('.badge-inner', widget).css('top', newVal+'px');

			} else if ( adjustWhat == 'pos_left' ) {
				$('.viewer', widget).css('border-color', 'transparent');
				$('.badge-inner', widget).css('left', newVal+'px');
			}
		},


		imagePreview: function( selected ) {
			var widgetForm = selected.parents('.pp-twitter-slider-wrap'),
				previewImg = $('.pp-twitter-slider img',widgetForm),
				widget     = $('.pp-twitter-slider',widgetForm),
				newImgID   = selected.val();

			$('.twitter-slider-img-upload-msg',widgetForm).hide();
			widgetForm.addClass('has-file').removeClass('no-file');

			if ( newImgID == 'A' || newImgID == 'B' ) {
				previewImg.attr('src', previewImg.attr('data-url-start')+'twitter-thought-bubble'+newImgID+'.png');
				var width = ( newImgID == 'A' ) ? '300' : '194';
				widget.css('width',width+'px');

			} else if ( newImgID == 'add' ) {
				$('.twitter-slider-img-upload-msg',widgetForm).show();

			} else if ( newImgID == 'no' ) {
				widgetForm.addClass('no-file').removeClass('has-file');
				previewImg.attr('src', previewImg.attr('data-url-start')+'nodefaultimage.gif');
				widget.css('width',$('.badge-inner',widget).css('width') );
				widget.css('height',$('.tweet-box-height',widgetForm).val()+'px' );

			} else {
				previewImg.attr('src', pp_custom_images[newImgID]);
				widget.css('width', pp_custom_images[newImgID+'-width']+'px' );
			}
		},


		tweetCountChange: function( textInput ) {
			var widget = $('.pp-twitter-slider',textInput.parents('.pp-twitter-slider-wrap'));
			$('.controls a',widget).css('display', ( parseInt( textInput.val() ) == 1 ) ? 'none' : 'block' );
		},


		fontSizeChange: function( textInput ) {
			var widget = $('.pp-twitter-slider',textInput.parents('.pp-twitter-slider-wrap'));
			$('ul',widget).css('font-size',textInput.val()+'px');
		},


		fontFamilyChange: function( textInput ) {
			var widget = $('.pp-twitter-slider',textInput.parents('.pp-twitter-slider-wrap'));
			$('ul',widget).css('font-family',textInput.val());
		}
	}
});



/* updates the social media icon preview image */
function ppUpdateIconPreview( widget_form ) {
	var type    = jQuery('.p4-type', widget_form).val();
	var style   = jQuery('.p4-style', widget_form).val();
	var size    = jQuery('.p4-size', widget_form).val();
	var cstm_sz = jQuery('.p4-custom-size', widget_form).val();
	var img     = jQuery('.p4-social-media-icons-preview-image', widget_form);
	var path    = img.attr('rel');

	// size set to large or small
	if ( 'custom' != size ) {
		jQuery('.p4-custom-size-holder', widget_form ).hide();
		filesize = ( size == 'large' ) ? '256' : '128';
		htmlsize = filesize;
	// custom size
	} else {
		jQuery('.p4-custom-size-holder', widget_form ).show();
		// don't set width/height to 0 initially
		if ( cstm_sz == '' ) {
			cstm_sz = jQuery('.p4-social-media-icons-preview-image', widget_form ).attr('width');
		}
		filesize = ( parseFloat( cstm_sz ) > 128 ) ? '256' : '128';
		htmlsize = cstm_sz;
	}

	// update the image html
	img.attr({
		src: path+type+'_'+style+'_'+filesize+'.png',
		height: htmlsize,
		width: htmlsize
	});
}

/* update the custom icon widget preview */
function ppCustomIconPreview( widget_form ) {
	var img_num = jQuery('.p4-number', widget_form).val();
	var noimg_alert = jQuery('.add-image-explain', widget_form);
	if ( img_num == 'add') {
		noimg_alert.show();
	} else {
		jQuery('.p4-custom-icon-preview-image', widget_form ).attr('src', pp_custom_images[img_num]);
		noimg_alert.hide();
	}
}