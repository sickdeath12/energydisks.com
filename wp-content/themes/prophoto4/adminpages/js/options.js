/* Scripts for the Options page */
var p4_debug = false;
var p4_form_changed = false;


function ppRefreshModifiedFont(font,del) {
	var $ = jQuery;
	var box = $('#upload-box-'+font.id);
	var deleting = ( del == 'delete' );

	if ( !deleting ) {
		box.removeClass('empty no-file').addClass('has-file');
		$('.font-name',box).show().text(font.name);
		$('.uploaded-file-display',box).find('p').css('font-family',font.name);
		$('<style type="text/css">'+font.css+'</style>').appendTo("head");
		$('body').trigger('filemodified',font.id);
		$('select:contains("select font...")').append('<option value="'+font.name+', Arial, Helvetica, sans-serif">'+font.name+'</option>');
	} else {
		box.addClass('empty no-file').removeClass('has-file');
		var fontName = $('.font-name',box).text();
		$('.font-name',box).text('');
		$('option:contains("'+fontName+'")').remove();
		$('body').trigger('filedeleted',font.id);
	}
}


function ppRefreshModifiedAudioFile(audioFile,del) {
	var $ = jQuery;
	var box = $('#upload-box-'+audioFile.id);
	var deleting = ( del == 'delete' );
	if ( !deleting ) {
		box.removeClass('empty no-file').addClass('has-file');
		$('.uploaded-file-display a',box).attr('href',audioFile.url).text(audioFile.filename);
		$('.uploaded-file-ext',box).text(audioFile.ext);
		$('.uploaded-file-size',box).text(audioFile.fileSize);
		$('body').trigger('filemodified',audioFile.id);
		$('input[type="text"]',box).val(audioFile.songName);
	} else {
		box.addClass('empty no-file').removeClass('has-file');
		$('input[type="text"]',box).val('');
	}
}


function ppRefreshModifiedFile(file,del) {
	var $ = jQuery;
	var deleting = ( del == 'delete' );
	var box      = $('#upload-box-'+file.id);
	var isBg     = box.hasClass( 'bg-option-group' );

	$('.uploaded-img-width', box).text(file.width);
	$('.uploaded-img-height',box).text(file.height);
	$('.uploaded-file-size', box).text(file.fileSize);
	$('.uploaded-file-ext',  box).text(file.ext);

	var showHide = deleting ? 'fadeOut' : 'fadeIn';
	$('.uploaded-file-data, .delete-uploaded-file-btn, .bg-img-options',box)[showHide]();

	if ( deleting ) {
		$('.uploaded-file',box).hide();
		box.addClass( 'no-file' ).removeClass( 'has-file' );
		$('body').trigger('filedeleted',file.id);

	} else {
		$('.uploaded-file',box).fadeOut(function(){
			box.addClass( 'has-file' ).removeClass( 'no-file' );
			$('.uploaded-file-display img',box).attr('src',file.url);

			if ( !box.hasClass( 'file' ) ) {
				var maxSize = ( box.hasClass('bg-option-group') ) ? 394 : 568;
				if ( $('.edit-menu-item-wrap').length ) {
					maxSize = 325;
				}
				$('.not-fullsize-msg',box).remove();
				if ( file.width > maxSize ) {
					$('.uploaded-file',box).attr('width',maxSize);
					$('li.size',box).after('<li class="not-fullsize-msg">Not shown <a href="'+file.url+'">fullsize</a></li>');
				} else {
					$('.uploaded-file',box).attr('width',file.width);
				}

			} else {
				$('.uploaded-file',box).attr('href',file.url).text(file.filename);
			}

			$('.uploaded-file',box).fadeIn();
			$('body').trigger('filemodified',file.id);
		});
	}

	p4_highlight_img_size_problems();
}



// jquery tooltips
if (typeof(jQuery.fn.tTips) != 'function') {
	(function($) {
		$.fn.tTips = function() {
			$('body').append('<div id="tTips"><p id="tTips_inside"></p></div>' );
			var TT = $('#tTips' );
			this.each(function() {
				var el = $(this), txt;
				if ( txt = el.attr('title') ) el.attr('tip', txt).removeAttr('title' );
				else return;
				el.find('img').removeAttr('alt' );
				el.mouseover(function(e) {
					if ( el.hasClass('help-icon') ) {
						TT.addClass('black');
						TT.find('p').addClass('black-glass-gradient-bg');
					} else {
						TT.removeClass('black');
						TT.find('p').removeClass('black-glass-gradient-bg');
					}
					txt = el.attr('tip'), o = el.offset();
					clearTimeout(TT.sD);
					TT.find('p').html(txt);
					TT.css({'top': o.top - 39, 'left': o.left - 15});
					TT.sD = setTimeout(function(){TT.fadeIn(150);}, 100);
				});
				el.mouseout(function() {
					clearTimeout(TT.sD);
					TT.css({display : 'none'});
				})
			});
		}
	}(jQuery));
}

// get the area=XXX part of the current URL
function p4_get_option_tab() {
	var objURL = {};
	window.location.search.replace(
		new RegExp( "([^?=&]+)(=([^&]*))?", "g" ),
		function( $0, $1, $2, $3 ){
			objURL[ $1 ] = $3;
		}
	);
	return (objURL['area']);
}

// constrains text input areas to required limits
function p4_constrain_textinput(id, bottom, top) {
	jQuery(id).blur(function(){
		var opacity = jQuery(this).val();
		if ( opacity > top ) {
			jQuery(this).val(top);
		}
		if ( opacity < bottom ) {
			jQuery(this).val(bottom);
		}
	});
}


/* crude color code validation and correction */
function p4_color_validate(color,id) {
	var color_in = color;
	if (color.charAt(0) != '#') {
		color = '#'+color;
	}
	if (color.length > 7) {
		color = color.substring(0,7);
	}
	if (color.length > 4 && color.length < 7) {
		color = color.substring(0,4);
		color = color+color.substring(1,4);
	}
	if ( !color.match(/^#([\da-fA-F]{3}$)|([\da-fA-F]{6}$)/) ) {
		color = '#ffffff';
	}
	if ( color_in != color )
		jQuery('#'+id).val(color).blur();
	return color;
}


/* functions for live font preview */
function p4_is_checked(optionid) {
	if ( jQuery(optionid).children().children('.optional-color-bind-checkbox').length > 0 ) {
		if ( jQuery(optionid).children().children('.optional-color-bind-checkbox').is(':checked') ) {
			return true;
		}
		return false;
	}
	return true;
}
function p4_get_color_input(optionid) {
	return color = jQuery(optionid).children().children().children('.color-picker').val();
}
function p4_get_sectionid(optionid) {
	return sectionid = '#'+jQuery(optionid).parents('.option-section').attr('id' );
}
function p4_get_color_setting_type(optionid) {
	var type = 'none';
	if ( jQuery(optionid).hasClass('nonlink-font-color-picker') ) {type='';}
	if ( jQuery(optionid).hasClass('font-color-picker') ) {type=' a.unvisited';}
	if ( jQuery(optionid).hasClass('visited-link-font-color-picker') ) {type=' a.visited';}
	return type;
}
function p4_update_color_preview(sectionid, type, color) {
	if ( type != 'none' ) {
		jQuery(sectionid+' .font-preview'+type).css('color', color);
	}
}
/* END functions for live font preview */



/* handle relationshiop between uploaded images and dependant option sections */
function ppImageDependent( img_shortname, dependant ) {
	jQuery('#uploaded-img-'+img_shortname).change(function(){
		if (/nodefaultimage/.test(jQuery('#uploaded-img-'+img_shortname).attr('src'))) {
			jQuery('#'+dependant+'-option-section').hide();
		} else {
			jQuery('#'+dependant+'-option-section').show();
		}
	}).change();
}



// Close color pickers when click on the document. This function is hijacked by
// farbtastic's event when a color picker is open
jQuery(document).mousedown(function(){
	p4_hideothercolorpickers();
});


// Close color pickers except "what"
function p4_hideothercolorpickers(what) {
	jQuery('.colorpicker-wrap').each(function(){
		var id = jQuery(this).attr('id' );
		if (id == what) {
			return;
		}
		var display = jQuery(this).css('display' );
		if (display == 'block') {
			jQuery(this).fadeOut(300);
			var swatch = id.replace(/picker-wrap/, 'swatch' );
			jQuery('#'+swatch).css('background-position', '0px 0px' );
		}
	});
}


// activate a sub-tab options group
function p4_subtab_activate(subtab) {

	// activate a subtab based on passed value
	if ( subtab ) {
		subtab_select   = '#subgroup-nav-'+subtab;
		subgroup_select = '#subgroup-'+subtab;

	// no passed value, activate the first one
	} else {
		subtab_select   = '#subgroup-nav li:first';
		subgroup_select = '.subgroup:first';
	}

	// shown only requested subgroup
	jQuery('.subgroup').hide();
	jQuery(subgroup_select).not('.hidden').show();

	// highlight only selected subtab
	jQuery('#subgroup-nav li').removeClass('active');
	jQuery(subtab_select).addClass('active');

	if ( subtab ) {
		// change form action to hash so save stays on same page
		jQuery('form#pp-customize-form').attr('action', '#'+subtab);
	}

	var resetMenuAdminUIs = function(){
		menuAdmin.resetUI(jQuery('#primary_nav_menu-menu-admin-wrap'));
		menuAdmin.resetUI(jQuery('#secondary_nav_menu-menu-admin-wrap'));
	};

	if ( /area=menu/.test(window.location.href) ) {
		setTimeout(function(){resetMenuAdminUIs();},300);
	}
	if ( subtab == 'main_menu' ) {
		setTimeout(function(){resetMenuAdminUIs();},300);
	}
}


function p4_handle_subtabs() {
	// load_correct subgroup based on page load hash
	var window_hash = window.location.hash.substr(1);
	// no window hash, activate first subtab
	if ( window_hash == '' ) {
		p4_subtab_activate();
	// activate tab/section based on window hash
	} else {
		p4_subtab_activate(window_hash);
	}

	// show requested subgroup on subtab click
	jQuery('.subgroup-link').click(function(){
		// get the key from the clicked subtab
		var key = jQuery(this).attr('key');
		// silently update the window hash
		window.location.hash = key;
		// activate the requested subtab/section
		p4_subtab_activate(key);
		return false;
	});
}


/* show and hide options based on radio button clicks */
function p4_show_hide_options() {
	jQuery('.radio-input').click(function(){
		var val = jQuery(this).val();
		var option_id = jQuery(this).parent().attr('id').replace('-individual-option', '');
		jQuery('.show-when-'+option_id+'-clicked').show();
		jQuery('.hide-when-'+option_id+'-val-'+val).hide();
	});
}


/* slider input areas */
function pp_slider_inputs() {
	// slider controls
	jQuery('.pp_slider').each(function(){
		var slider = jQuery(this);
		var option = slider.parent();
		if ( typeof slider.slider !== "undefined" ) {
			slider.slider({
				range: "min",
				value: parseFloat(jQuery('.val', option).text()),
				min:   parseFloat(jQuery('.min', option).text()),
				max:   parseFloat(jQuery('.max', option).text()),
				step:  parseFloat(jQuery('.step', option).text()),
				slide: function(event, ui) {
					jQuery('.pp_slider_display span', option).text(ui.value);
					jQuery('input', option).val(ui.value);
					p4_form_changed = true;
				}
			});
		} else {
			jQuery('body').addClass('sliders-broken');
			jQuery('.slider-hidden-input-wrap input',option).after(jQuery('.pp_slider_display',option).html());
		}
	});
}


/* font button clicks */
function p4_font_button_inputs() {
	jQuery('.font-button').click(function(){
		// get data from markup
		var button      = jQuery(this);
		var type        = button.attr('type');
		var old_val     = button.attr('val');
		var options     = button.attr('options').split('|');
		var last_index  = options.length - 1;

		// debug css display class
		var old_classes = button.attr('class');
		var old_classes_array = old_classes.split(' ');
		var old_display_class = old_classes_array[old_classes_array.length - 1];

		// determine index of currently selected value from options array
		var options_debug     = '';
		var old_val_index = 0;
		for ( index in options ) {
			options_debug += '&nbsp;&nbsp;&nbsp; options[' + index + '] => ' + options[index] + '<br />';
			if ( old_val == options[index] ) old_val_index = parseInt(index);
		}

		// get index of next value
		var next_val_index = 'ERROR';
		if ( old_val_index < last_index ) {
			new_val_index = old_val_index + 1;
		} else {
			new_val_index = 0;
		}

		// switch to next value
		new_val = options[new_val_index];
		button.attr('val', new_val);

		// update class for css button display toggle/change
		var new_display_class = 'font-button-' + type + '-val-' + new_val;
		new_display_class = new_display_class.replace('.','');
		var remove_class = 'font-button-' + type + '-val-' + old_val.replace('.','');
		button
			.removeClass( remove_class )
			.addClass( new_display_class );
			var new_classes = button.attr('class'); // debug

		// update hidden select box
		var context = button.parent();
		jQuery( '.font-group-hidden-input-' + type + ' select', context ).val(new_val).change();


		/* debug */
		// var debug = '<p class="debug" style="font-family:Courier,monospace; font-size:10px;clear:left;padding-top:15px;max-width:900px">';
		// debug += 'var type => ' + type + '<br />';
		// debug += 'var options => ' + options + '<br />';
		// debug += options_debug;
		// debug += 'var last_index => ' + last_index + '<br />';
		// debug += 'var old_val_index => ' + old_val_index + '<br />';
		// debug += 'var new_val_index => ' + new_val_index + '<br />';
		// debug += 'var old_val => ' + old_val + '<br />';
		// debug += 'var new_val => ' + new_val + '<br />';
		// debug += 'var old_display_class => ' + old_display_class + '<br />';
		// debug += 'var new_display_class => ' + new_display_class + '<br />';
		// debug += 'var remove_class => ' + remove_class + '<br />';
		// debug += 'var old_classes => ' + old_classes + '<br />';
		// debug += 'var new_classes => ' + new_classes + '<br />';
		// debug += '</p>';
		// button.next().show().end().parent().parent().next('.debug').remove().end().after(debug);
	});
}

function p4_unsaved_changes_warning() {
	jQuery('.tab-link').click(function(){
		if ( p4_form_changed === true ) {
			if ( p4_debug ) return true;
			if (!confirm("You have unsaved changes on this tab. Do you want to go to another tab and lose those changes?")) return false;
		}
	});
}



function p4_highlight_img_size_problems( shortname, masthead_height ) {
	jQuery('.upload-box:has(ul.sizing-box)').each(function(){
		var a_height = jQuery('.uploaded-img-height', this);
		var a_width  = jQuery('.uploaded-img-width', this);
		var r_height = jQuery('.recommended-height', this);
		var r_width  = jQuery('.recommended-width', this);
		a_height.parent().removeClass('wrong');
		a_width.parent().removeClass('wrong');
		var found_problem = false;
		if ( shortname == 'masthead_image1' && !jQuery('body').hasClass('sameline') ) {
			jQuery('#subgroup-masthead .recommended-height').text(masthead_height);
			shortname = '';
		}
		if (jQuery(this).hasClass('no-file')) return;
		if ( a_height.length && r_height.length && ( a_height.text() != r_height.text() ) ) {
			a_height.parent().addClass('wrong');
			found_problem = true;
		}
		if ( a_width.length && r_width.length && ( a_width.text() != r_width.text() ) ) {
			a_width.parent().addClass('wrong');
			found_problem = true;
		}
		jQuery('.sizing-msgs span', this).hide();
		if ( found_problem ) jQuery('.size-incorrect', this).show();
		else jQuery('.size-correct', this).show();
	});
}


var ppOption = {

	valToClass: function(id){

		var $      = jQuery;
		var option = $('#'+id+'-individual-option');

		var valClasses = [];
		$('input[type="radio"]',option).each(function(index){
			valClasses[index] = id + '-' + $(this).val();
		});

		$('input[type="radio"]',option).click(function(){
			$('body').removeClass( valClasses.join(' ') );
			$('body').addClass(id+'-'+$(this).val());
		});
		$('input[type="radio"]',option).filter(':checked').click();
	},

	uploadReveal: function(key){
		var $ = jQuery;
		var uploadBoxes = $('.no-file[id^="upload-box-'+key+'"]');
		uploadBoxes.not(':first').addClass('not-revealed');
		$('body').bind('filemodified',function(event,modifiedImgId){
			if ( modifiedImgId.indexOf( key ) !== -1 ) {
				$('.not-revealed:first').removeClass('not-revealed');
			}
		});
		$('body').bind('filedeleted',function(event,modifiedImgId){
			if ( modifiedImgId.indexOf( key ) !== -1 ) {
				$('.no-file[id^="upload-box-'+key+'"]').not('.not-revealed').filter(':last').addClass('not-revealed');
			}
		});
	}
};


jQuery(document).ready(function($) {

	$('a.tutorial').colorbox({
		width: '90%',
		height: '85%',
		maxWidth: '830px',
		fixed: true,
		scrolling:false
	});

	$('a.blurb').click(function(){
		$('.extra-explain:first',$(this).parents('.option')).slideToggle(150);
	});

	p4_highlight_img_size_problems();

	// help keep from POSTing more than 200 fields
	$('#pp-customize-form').submit(function(){
		$('.individual-option:hidden').remove();
	});

	p4_unsaved_changes_warning();

	p4_show_hide_options();

	pp_slider_inputs();

	p4_font_button_inputs();

	// watch for unsaved changes when going to different tab
	$(['input', 'select', 'textarea']).each(function() {
		$(this.toString()).change(function() {
			p4_form_changed = true;
		});
	});

	p4_handle_subtabs();

	// add a color picker to every div.p4_picker
	$('.p4_picker').each(function(){
		var id = jQuery(this).attr('id' );
		var target = id.replace(/picker/, 'input' );
		$(this).farbtastic('#'+target);
	});

	// add the toggling behavior to .colorpicker-swatch
	$('.colorpicker-swatch').click(function(){
		var id = $(this).attr('id' );
		var target = id.replace(/swatch/, 'picker-wrap' );
		p4_hideothercolorpickers(target);
		var display = $('#'+target).css('display' );
		(display == 'block') ? $('#'+target).fadeOut(300) : $('#'+target).fadeIn(300);
		var bg = (display == 'block') ? '0px 0px' : '0px -24px';
		$(this).css('background-position', bg);
		}).tTips(); // tooltipize

	// validate color entries
	$('.color-picker').blur(function(){
		$(this).val(p4_color_validate($(this).val(),$(this).attr('id')));
	});

	// inline documentation hide/reveal links
	$('a.click-for-explain').click(function(){
		var thisid = $(this).attr('id');
		var id = thisid.replace('-cfe', '' );
		if ( typeof p4_external_explain == "undefined" || typeof p4_external_slugs == "undefined" ) {
			p4_external_explain = new Object();
			p4_external_slugs = new Object();
			p4_external_explain[id] = '<p><strong>How embarrassing.</strong> The file we use to show the extra help information did not load properly. The server where it is stored might be down temporarily, or there may be a connectivity problem.  If this problem persists for more than a few hours, please <a href="http://www.prophotoblogs.com/support/contact/">notify us</a> through our contact form.</p>';
			p4_external_slugs[id] = undefined;
		}
		var speed = 1;
		if ( $(this).hasClass('help-active') ) {
			var speed = 130;
		}
		$(this).toggleClass('help-active');
		var second_para = ''
		if ( p4_external_explain[id] == undefined ) {
			p4_external_explain[id] = '';
		}

		var support_url = 'http://www.prophotoblogs.com/';
		var second_para = '<p class="explain-link">Need more help with this specific area? We got it. <a href="'+support_url+'support/about/'+p4_external_slugs[id]+'/" target="_blank">Click here</a>.</p>';
		if ( p4_external_slugs[id] == undefined ) {
			second_para = '';
		}
		var help = p4_external_explain[id] + second_para;
		$('#explain-' + id)
			.html(help)
			.animate({opacity: 0}, speed)
			.slideToggle(185)
			.animate({opacity: 1}, 130);
	});


	// this for the "tabbed" options areas
	var p4_hash = p4_get_option_tab();
	// if (p4_hash != undefined) {
	//	$('.tabbed-sections').css('display', 'none' );
	//	$('#tab-section-'+p4_hash+'-link').css('display', 'block' );
	//	$('a.tab-link').removeClass('active' );
	//	$('#'+p4_hash+'-link').addClass('active' );
	// } else {
	//	$('#background-link').addClass('active' );
	//	$('#tab-section-background-link').css('display', 'block' );
	// }


	$('.help-icon').tTips();
	$('.menu-item .edit-link').tTips();
	$('.blank-comment').next().css('margin-top', '0' );

	if ( prophoto_info.p2user ) {
		var now = new Date();
		var now_secs = parseInt( now.getTime() / 1000 );
		if ( ( now_secs - p4info.purchtime ) < ( 60 * 60 * 24 * 40 ) )
			$('body').addClass('recent-upgrade');
	}
});