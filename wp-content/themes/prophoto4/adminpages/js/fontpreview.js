/* -- javascript for live font previews -- */

jQuery(document).ready(function($){
	// disable clicking on font-preview links
	$('.font-preview a').click(function(){return false;});
	
	// identify the various groups of inputs
	var font_groups             = $('.pp-font-group');
	var font_size_inputs        = $('.text-input[name$=font_size]', font_groups);
	var font_family_inputs      = $('.select-input[name$=font_family]', font_groups);
	var font_style_inputs       = $('.select-input[name$=font_style]', font_groups);
	var text_transform_inputs   = $('.select-input[name$=text_transform]', font_groups);
	var font_lineheight_inputs  = $('.select-input[name$=line_height]', font_groups);
	var link_decoration_inputs  = $('.select-input[name$=link_decoration]', font_groups);
	var hover_decoration_inputs = $('.select-input[name$=link_hover_decoration]', font_groups);
	var link_hover_color_inputs = $('.color-picker[id$=link_hover_font_color]',font_groups);
	var font_weight_inputs      = $('.select-input[name$=font_weight]', font_groups);
	var margin_bottom_inputs    = $('.text-input[id$=margin_bottom]', font_groups);
	var letterspacing_inputs    = $('.select-input[id$=_letterspacing]', font_groups);
	var font_color_inputs       = $('.color-picker[id$=_font_color]',font_groups)
									.not('[id$=link_hover_font_color]')
									.not('[id$=link_visited_font_color]')
									.not('[id$=link_visited_hover_font_color]');
									
	// bind all of the normal change events
	p4_font_change(font_family_inputs, 'font-family');
	p4_font_change(font_size_inputs, 'font-size', 'px');
	p4_font_change(font_weight_inputs, 'font-weight');
	p4_font_change(font_style_inputs, 'font-style');
	p4_font_change(text_transform_inputs, 'text-transform');
	p4_font_change(font_lineheight_inputs, 'line-height', 'em');
	p4_font_change(link_decoration_inputs, 'text-decoration');
	p4_font_change(margin_bottom_inputs, 'margin-bottom', 'px');
	p4_font_change(letterspacing_inputs, 'letter-spacing');
	p4_font_color_bind(font_color_inputs);
	
	// color bind checkbox clicks
	$('.optional-color-bind-checkbox').not('[id$="hover_font_color-bind"]').click(function(){
		var checkbox = $(this);
		var color_input = $('.color-picker', checkbox.parent());
		if (checkbox.is(':checked')) {			
			color_input.blur();
		} else {
			p4_update_preview_css( color_input, 'color', '' );
		}
	});
	
	// color bind checkbox clicks for hover font colors
	$('.optional-color-bind-checkbox[id$="hover_font_color-bind"]').click(function(){
		var checkbox = $(this);
		var color_input = $('.color-picker', checkbox.parent());
		if (checkbox.is(':checked')) {			
			p4_hover_preview(color_input, 'color');
		} else {
			p4_hover_preview(color_input, 'color', true);
		}
	});
	
	// link hover decoration
	hover_decoration_inputs.change(function(){
		p4_hover_preview( $(this), 'text-decoration' );
	});
	
	// link hover color
	link_hover_color_inputs.each(function(){
		var self = $(this);
		var option_id = self.attr('id').replace(/p4-input-/, '');
		p4_bind_callback_to_color_events( option_id, function(){
			p4_hover_preview(self, 'color');
		});
	});
	
	// show each relevant font-preview area when user focuses on that section
	var showFontPreview = function(group){
		var this_font_preview = $('.font-preview', group);
		if ( this_font_preview.is(':visible') ) return;
		$('.font-preview').not(this_font_preview).css('opacity',0).slideUp('fast',function(){
			this_font_preview.css('opacity', 0).slideDown('fast',function(){
				this_font_preview.fadeTo('fast', 1);
			})
		});
	};
	$('.font-group').click(function(){
		showFontPreview($(this));
	});
	$('.font-group select').change(function(){
		showFontPreview($(this).parents('.font-group'));
	});


	/* bind a hover preview update to a link area */
	function p4_hover_preview( option, css_attr, unset ) {
		var preview_area = p4_get_update_target(option);
		var old_val = preview_area.css(css_attr);
		var new_val = option.val();
		if ( unset == undefined ) unset = false;
		if ( unset ) new_val = old_val;
		preview_area.hover(function(){
			$(this).css(css_attr, new_val);
		},function(){
			$(this).css(css_attr, old_val);
		});
	}

	/* bind preview change events to a group of color inputs */
	function p4_font_color_bind( color_type_input ) {
		color_type_input.each(function(){
			var self = $(this);
			var option_id = self.attr('id').replace(/p4-input-/, '');
			p4_bind_callback_to_color_events( option_id, function(){
				p4_update_preview_css(self, 'color', self.val());
			});
		})
	}


	/* bind change callback to all the different change events for color picker */
	function p4_bind_callback_to_color_events( id, callback ) {
		$('#p4-picker-wrap-'+id+' .farbtastic div')
			.mouseup(function(){callback()})
			.change(function(){callback()});
		$('#p4-input-'+id).blur(function(){callback()});
	}


	/* boolean test, is this option in a group with a non-link option? */
	function p4_doing_nonlink( option ) {
		var nonlink_font_color = 
			$('.color-picker', option.parents('.option') )
				.not('[id$=link_font_color]')
				.not('[id$=link_hover_font_color]')
				.not('[id$=link_visited_font_color]')
				.not('[id$=link_visited_hover_font_color]');
		if ( nonlink_font_color.length ) return true;
		return false;
	}


	/* bool test: does this font option type apply to text and links */
	function p4_is_dual_purpose( option ) {
		if ( /decoration/.test(option.attr('id'))) return false;
		if ( /link_font_color/.test(option.attr('id'))) return false;
		if ( /link_hover_font_color/.test(option.attr('id'))) return false;
		return true;
	}


	/* return preview area updating target for given font option change */
	function p4_get_update_target( option ) {
		var preview_section = $('.font-preview', option.parents('.individual-option'));

		// margin bottoms
		if ( /margin_bottom/.test(option.attr('id')) ) return $('.margin-bottom', preview_section);

		// if we're inside a link area
		if ( option.parents('.individual-option').hasClass('link-font-group')) {

			// areas with a 'nonlink' need special attention
			if ( p4_doing_nonlink( option) ) {

				// non link font color: only affect non-link text in preview area
				if (!(/link/.test(option.attr('id')))) return preview_section;

				// option types that effect both link and non-link text
				if ( p4_is_dual_purpose( option ) ) return preview_section.add($('a', preview_section));
			}

			// font size changes affect link and overall section, to keep section div size correct
			if (/_font_size/.test(option.attr('id'))) return preview_section.add($('a', preview_section));

			return $('a', preview_section);

		// not in a link section
		} else {
			return preview_section;
		}
	}


	/* update the font preview area */
	function p4_update_preview_css( option, type, val ) {
		var preview_target = p4_get_update_target( option );
		preview_target.css(type, val);
		p4_rebind_hover_decoration(option);
	}


	/* bind a change event to update the preview area */
	function p4_font_change( input_option, css_attr, val_suffix ) {
		input_option.change(function(){
			var self = $(this);
			var new_val = self.val();
			if ( val_suffix == undefined ) val_suffix = '';
			if ( new_val == '' ) {
				new_val = 'inherit';
				val_suffix = '';
			}
			p4_update_preview_css(self, css_attr, new_val+val_suffix);
		})
	}


	/* re-bind the hover decoration hover function after non-hover link decoration changed */
	function p4_rebind_hover_decoration(option) {
		if (!(/_link_decoration/.test(option.attr('id')))) return;
		var this_hover = $('.select-input[name$=link_hover_decoration]',option.parents('.individual-option'));
		p4_hover_preview(this_hover, 'text-decoration', true);
		p4_hover_preview(this_hover, 'text-decoration');
	}
});