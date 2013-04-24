<?php
/* ------------------------------------- */
/* -- output theme option input areas -- */
/* ------------------------------------- */


/* base class: create individual option area */
class ppOptionBox {

	private $multiple_index = false;

	/* php 5 constructor */
	function __construct( $name, $option_data, $comment, $title = '' ) {
		// set up properties
		$this->name           = $name;
		$this->option_data    = $option_data;
		$this->comment        = $comment;
		$this->title          = $this->format_title( $title );
		$this->setup_other_properties();

		// prepare option markup
		$this->input_markup  = $this->build_input_markup();
		$this->option_markup = $this->wrap_input_markup();
	}


	/* wrap the markup and echo it. overridden by child classes that wrap differently */
	function wrap_and_print_markup() {
		echo apply_filters( 'p4_option_markup', $this->wrap_option_markup(), $this );
	}


	/* wrapper method: returns input markup by calling correct method based on option type */
	function build_input_markup() {
		$input_markup = call_user_func( array( $this, $this->option_type . '_input' ) );
		return apply_filters( "p_markup_{$this->option_type}", $input_markup, $this );
	}


	/* prepares markup for individual markup section by wrapping input markup */
	function wrap_input_markup() {
		$extra_explanation_holder = ( $this->is_multiple() ) ? '' : $this->get_extra_explanation();
		return <<<HTML
		$extra_explanation_holder
		<div id="{$this->name}-individual-option" class="{$this->option_classes}">
			$this->input_markup
			<p>$this->comment</p>
			$this->debug
		</div>
HTML;
	}


	/* embed an image upload area (for small images) */
	function image_input() {
		return ppUtil::renderView( 'upload_box_mini', array( 'upload' => ppUploadBox::imgInstance( $this->name, '', '' ) ), ppUtil::RETURN_VIEW );
	}


	/* text-type option input sub-method */
	function text_input() {
		// get the text input size from the $params array
		$text_input_size = array_shift( $this->option_params );

		if ( NrUtil::isIn( ' (in pixels)', $this->comment ) ) {
			$px = 'px';
			$px_class = ' px-input';
			$this->comment = str_replace( ' (in pixels)', '', $this->comment );
		} else {
			$px_class = '';
			$px = '';
		}

		// create and return the text/hidden form input markup
		return "<input type='text' size='$text_input_size' id='p4-input-{$this->name}' name='p_{$this->name}' value='{$this->stored_val_display}' class='text-input{$px_class}' />$px";
	}


	/* return HTML for slider form elements */
	function slider_input() {
		@list( $min, $max, $unit, $step ) = $this->option_params;
		if ( !$min )  $min = 0;
		if ( !$max )  $max = 100;
		if ( !$unit ) $unit = '%';
		if ( !$step ) $step = 1;
		$val = ( $this->stored_val_display ) ? floatval( $this->stored_val_display ) : 0;

		return $form_markup = <<<HTML
		<div class="slider-hidden-input-wrap">
			<input type="text" name="p_{$this->name}" value="$val" id="p_{$this->name}" class="text-input slider-text-input" size="3">
		</div>
		<span class="min js-info">$min</span>
		<span class="max js-info">$max</span>
		<span class="step js-info">$step</span>
		<span class="val js-info">$val</span>
		<div class="pp_slider"></div>
		<p class="pp_slider_display"><span>$val</span>$unit</p>
HTML;
	}


	/* return HTML for textara form elements */
	function textarea_input() {
		// get the row and column number information
		$rows = array_shift( $this->option_params );
		$cols = array_shift( $this->option_params );

		return  "<textarea id='p4-input-{$this->name}' name='p_{$this->name}' rows='$rows' cols='$cols' class='textarea-input'>{$this->stored_val_display}</textarea>";
	}


	/* return HTML for select box form elements */
	function select_input() {
		// open the select box markup
		$input_markup = "<select id='p4-input-{$this->name}' name='p_{$this->name}' class='select-input'>";

		// loop through all the passed select choices to build up markup
		while ( $this->option_params ) {

			// get select value and display text from array
			$select_value = array_shift( $this->option_params );
			$select_text  = array_shift( $this->option_params );

			// if the stored option value equals this select value, set it to selected
			$selected = ( $select_value == $this->stored_val ) ? ' selected="selected"' : '';

			// add the specific option value
			$input_markup .= "<option value='$select_value' $selected>$select_text</option>\n";
		}

		// done iterating, close and return select markup
		$input_markup .= "</select>\n";
		return $input_markup;
	}


	/* return HTML for radio button form elements */
	function radio_input() {
		// Looping through radio params has not begun
		$first_time_thru_loop = true;

		// loop through passed radio params to buildup markup
		while ( $this->option_params ) {

			// get radio button value and display text from array
			$radio_value = array_shift( $this->option_params );
			$radio_text  = array_shift( $this->option_params );

			// if the stored option value equals this radio value, set it to checked
			$checked = ( $radio_value == $this->stored_val ) ? 'checked="checked"' : '';

			// before the first radio button only, include the hidden field
			if ( $first_time_thru_loop ) {
				$input_markup = "<input type='hidden' name='p_{$this->name}' value='' class='radio-input radio-input-hidden' />";
				$first_time_thru_loop = false;
			}

			// checkbox input and label markup
			$input_markup .= "<input type='radio' id=\"p4-input-{$this->name}-{$radio_value}\" class='radio-input' name='p_{$this->name}' value='$radio_value' $checked /><label for='p4-input-{$this->name}-{$radio_value}'>$radio_text</label>";

			// debug
			if ( pp::site()->isDev ) $input_markup .= "<tt class='debug-radio'>$radio_value</tt>";

			// if we have another radio button to draw, add a <br />
			if ( @$this->option_params ) $input_markup .= "<br />\n";
		}
		return $input_markup;
	}


	/* return HTML for checkbox form elements */
	function checkbox_input() {
		$input_markup = '';

		// loop through passed checkbox params to buildup markup
		while ( $this->option_params ) {

			// get checkbox  option name, value and display text from array
			$checkbox_option = array_shift( $this->option_params );
			$checkbox_value  = array_shift( $this->option_params );
			$checkbox_text   = array_shift( $this->option_params );

			// if the stored option value equals this checkbox value, set it to checked
			if ( $checkbox_value == ppOpt::id( $checkbox_option ) ) {
				$checked ='checked="checked"';
				$jquery  = 'true';
			} else {
				$checked = '';
				$jquery  = 'false';
			}

			// add hidden value, makes sure something is always POSTed on submit, checked or not
			$input_markup .= "<div class=\"checkbox-wrap\"><input type='hidden' name='p_$checkbox_option' value='' class='checkbox-input checkbox-input-hidden' />";

			// the checkbox itself
			$input_markup .= "<input type='checkbox' class='checkbox-input' id='p4-input-{$this->name}-{$checkbox_option}' name='p_$checkbox_option' value='$checkbox_value' $checked /><label for='p4-input-{$this->name}-{$checkbox_option}'>$checkbox_text</label>";

			// debug
			if ( pp::site()->isDev ) $input_markup .= "<tt class='debug-checkbox'><span style='color:blue'>$checkbox_option</span> $checkbox_value</tt>";

			// Add a javascript force refresh to avoid browser rendering inconsistencies between page reloads
			$input_markup .= "<script type='text/javascript'>jQuery('#p4-input-{$this->name}-{$checkbox_option}').attr('checked',$jquery);</script>";

			// if we have another checkbox to draw, add a <br />
			if ( @$this->option_params ) $input_markup .= "<br />\n";

			// close wrap
			$input_markup .= '</div>';
		}
		return $input_markup;
	}


	/* return HTML for color-picker form elements */
	function color_input() {
		$input_markup = '';

		// invalid color? use default color instead
		if ( !ppValidateColor( $this->stored_val ) ) {
			$this->stored_val_display = esc_attr( ppOpt::defaultVal( $this->name ) );
		}

		// still no display-type value? reset to black to avoid problems
		if ( !$this->stored_val_display ) $this->stored_val_display = '#000000';

		// get the string which indicates if is optional color picker
		$optional = array_shift( $this->option_params );

		// add beginning of required wrapping markup if we have an optional color picker
		if ( $optional == 'optional' ) {

			// default, unchecked values for markup
			$checked  = '';                    // checkbox state
			$jquery   = 'false';               // checkbox state, 'true' or 'false'
			$display  = 'none';                // display the binded color field, 'none' or 'block'
			$disabled = 'disabled="disabled"'; // state of the bound input field, '' or 'disabled="disabled"'
			$bound_class = '';

			// change initial values if option is checked
			if ( ppOpt::test( "{$this->name}_bind", 'on' ) ) {
				$checked  = 'checked="checked"';
				$jquery   = 'true';
				$display  = 'block';
				$disabled = '';
				$bound_class = ' bound';
			}

			// optional color picker wrapping markup, hidden field and checkbox
			$input_markup .= "<input type='hidden' name='p_{$this->name}_bind' value='' />";
			$input_markup .= "<input type='checkbox' id='p4-input-{$this->name}-bind' name='p_{$this->name}_bind' value='on' $checked class='optional-color-bind-checkbox' /><label for='p4-input-{$this->name}-bind' class='color-bind $bound_class'><span class='color-optional'>set color</span></label>";


			// open the wrapping collapsible div around the color field
			$input_markup .= "<div id='p4-input-{$this->name}-wrap' class='pp-colordiv-wrap{$bound_class}' style='display:$display'>\n";

		// not optional, still open generic wrapping div so js can find picker always at same DOM depth
		} else {
			$input_markup .= '<div class="pp-colordiv-wrap">';
			$disabled = '';
		}

		// The color picker itself
		$input_markup .= "
		<input type='text' size='7' id='p4-input-{$this->name}' name='p_{$this->name}' value='{$this->stored_val_display}' $disabled class='color-picker' />
		<span class='colorpicker-swatch' id='p4-swatch-{$this->name}' title='Pick a color'>&nbsp;</span>
		<div class='colorpicker-wrap' id='p4-picker-wrap-{$this->name}'>
			<div class='p4_picker' id='p4-picker-{$this->name}'></div>
		</div>";

		// if optional color picker, close wrapping div, add js
		if ( $optional == 'optional' ) {
			// close wrapping div
			$input_markup .= "</div>\n";

			// Add javascript behavior: force refresh + toggle div & input field
			$input_markup .=
				"\n<script type='text/javascript'>
					jQuery(document).ready(function(){
						jQuery('#p4-input-{$this->name}-bind').attr('checked',$jquery).click(function(){
							var display = (jQuery('#p4-input-{$this->name}-wrap').css('display') == 'block') ? 'none' : 'block';
							var disabled = (display == 'none') ? true : false;
							jQuery('#p4-input-{$this->name}').attr('disabled', disabled);
							jQuery('#p4-input-{$this->name}-wrap')
								.toggle()
								.prev('.color-bind').toggleClass('bound');
						});
					});
				</script>\n";

		// not optional, still close the generic wrapping div
		} else {
			$input_markup .= "</div>";
		}

		return $input_markup;
	}


	/* return HTML from custom functions passed as input params */
	function function_input() {
		$custom_function_name = $this->option_params[0];
		$param = isset( $this->option_params[1] ) ? $this->option_params[1] : null;
		return call_user_func( $custom_function_name, $param );
	}


	/* inline explanatory note, just show comment */
	function note_input() {}

	/* blank input, used for info and alignment */
	function blank_input() {
		if ( pp::site()->isDev ) return '<tt class="debug">blank</tt>';
		return;
	}


	/* sets up html classes */
	function prepare_classes() {
		global $p4_interface;
		$interface_classes = $option_classes = $section_classes = array();

		// deal with contingent, dependantly shown/hidden options
		$interface_classes = ppGetInterfaceClasses( $this->name );
		if ( NrUtil::isIn( '_font_group', $this->name ) ) $interface_classes[] = 'font-group';
		if ( NrUtil::isIn( '_link_font_group', $this->name ) ) $interface_classes[] = 'link-font-group';

		// individual option classes
		$option_classes[] = 'individual-option';
		$option_classes[] = ( $this->is_multiple() ) ? 'individual-option-multiple' : 'not-multiple';
		if ( $this->option_type == 'blank' ) $option_classes[] = 'blank-option';
		if ( $this->option_type == 'note' )  $option_classes[] = 'note';
		$this->option_classes  = implode( ' ', array_merge( $option_classes, $interface_classes ) );

		// section classes
		$section_classes[] = 'sc';
		$section_classes[] = 'option';
		$section_classes[] = 'option-section';
		if ( $this->option_type == 'function' ) {
			$section_classes[] = 'function-' . str_replace( '::', '_', $this->option_params[0] );
		}
		$this->section_classes = implode( ' ', array_merge( $section_classes, $interface_classes ) );
	}


	/* return markup for help/"click for explain" button */
	function get_help_button() {
		return ppUtil::renderView( 'option_help_icons', array( 'id' => $this->name ), ppUtil::RETURN_VIEW );
	}


	/* return markup for section label/headline area */
	function get_section_label() {
		// return markup
		return <<<HTML
		<div class='option-section-label option-label'>
			$this->title
		</div>
HTML;
	}


	/* return cleaned and formatted title */
	function format_title( $title ) {
		global $p4_option_group_title;

		// for multiples, use global title var set by ppStartMultiple() if possible
		if ( $this->is_multiple() && $p4_option_group_title ) $title = $p4_option_group_title;

		if ( !$title ) $title = str_replace( '_', ' ', $this->name );
		return $title = ucfirst( $title );
	}


	/* boolean test: are we in the middle of a multiple-option section? */
	function is_multiple() {
		if ( $this->multiple_index ) return true;
		return false;
	}


	/* optionally returns holder markup for js extra hidden documentation */
	function get_extra_explanation() {
		if ( $this->option_data == 'note' ) {
			return;
		}
		$multi_class = ( $this->is_multiple() ) ? ' extra-explain-multiple' : '';
		return "<div class='extra-explain" . $multi_class . "'>" . ppAdmin::optionBlurb( $this->name ) . "</div>";
	}


	/* method for setting up assorted interal properties once we have passed data */
	function setup_other_properties() {
		$this->section_label      = $this->get_section_label();
		$this->stored_val         = ppOpt::id( $this->name );
		$this->stored_val_display = esc_attr( $this->stored_val );
		$option_data_array        = explode( '|', $this->option_data );
		$this->option_type        = array_shift( $option_data_array );
		$this->option_params      = $option_data_array;
		$this->debug = ( pp::site()->isDev || pp::browser()->isTech ) ? "<tt class='debug'>{$this->name}</tt>" : '';
		$this->prepare_classes();
	}


	/* php 4 constructor */
	function ppOptionBox( $name, $option_data, $comment, $title ) {
		$this->__construct( $name, $option_data, $comment, $title );
	}
}


/* child class: an option box that is part of a multple option area */
class ppOptionBoxMultiple extends ppOptionBox {

	/* php 5 constructor */
	function __construct( $name, $option_data, $comment, $title ) {
		global $p4_multiple_option;
		$this->multiple_index = $p4_multiple_option;
		parent::__construct( $name, $option_data, $comment, $title );
	}


	/* wraps individual option markup with markup for multiply-grouped option */
	function wrap_option_markup() {
		global $p4_multiple_option;
		$wrapped_markup = '';

		// start the whole section if this is the first of many
		if ( $this->multiple_index == 1 ) $wrapped_markup .= $this->start_multiple_section();

		// deal with spanning notes differently
		if ( $this->option_data == 'note' ) {
			$p4_multiple_option = $p4_multiple_option + 3;
			return $wrapped_markup;
		}

		$start_row = ( is_int( ( $this->multiple_index - 1 ) / 3 ) ) ? '<tr>' : '';
        $close_row = ( is_int( ( $this->multiple_index ) / 3 ) ) ? '</tr>' : '';

		$wrapped_markup .= <<<HTML
		$start_row
			<td>$this->option_markup</td>
		$close_row
HTML;
		// increment the global counter
		$p4_multiple_option++;
		return $wrapped_markup;
	}


	/* print markup for beginning of a multiple-option section */
	function start_multiple_section() {
		$extra_explanation_holder = parent::get_extra_explanation();
		$note = ( $this->option_data == 'note' ) ? "<div class='note'><p>$this->comment</p></div>" : '';
		$helpButtons = $this->get_help_button();
		return <<<HTML
		<div id="{$this->name}-option-section" class="{$this->section_classes}">
			$helpButtons
			$this->section_label
			$extra_explanation_holder
			<div class="multiple-option-table-wrapper">
				$note
				<table class="multiple-option-table">
HTML;
	}


	/* print markup for end of multiple-option section */
	function end_multiple_section() {
		echo "</table></div><!-- .individual-option -->\n</div><!-- .option-section -->\n";
	}


	/* php 4 constructor */
	function ppOptionBoxMultiple( $name, $option_data, $comment, $title ) {
		$this->__construct( $name, $option_data, $comment, $title );
	}

	function is_multiple() {
		return true;
	}
}


/* child class: an option box that is NOT part of a multple option area */
class ppOptionBoxIndividual extends ppOptionBox {

	/* php 5 constructor */
	function __construct( $name, $option_data, $comment, $title ) {
		$this->multiple_index = false;
		parent::__construct( $name, $option_data, $comment, $title );
	}


	/* wraps individual option markup with markup for an individual option */
	function wrap_option_markup() {
		$helpButtons = $this->get_help_button();
		return <<<HTML
		<div id="{$this->name}-option-section" class="{$this->section_classes}">
			$helpButtons
			$this->section_label
			$this->option_markup
		</div> <!-- .option-section  -->
HTML;
	}


	/* php 4 constructor */
	function ppOptionBoxMultiple( $name, $option_data, $comment, $title ) {
		$this->__construct( $name, $option_data, $comment, $title );
	}
}


