<?php
/* ---------------------------------------------------- */
/* -- class for printing groupings of border options -- */
/* ---------------------------------------------------- */


/* helper function to set up the instantiation of the p4_border_group class */
function ppBorderGroup( $args ) {
	global $p4_border_args;
	$p4_border_args = $args;
	ppO( $args['key'] . '_group', 'function|ppNewBorderGroup', $args['comment'], isset( $args['title'] ) ? $args['title'] : '' );
}


/* helper function to instantiate the p4_border_group class from within an option area */
function ppNewBorderGroup() {
	global $p4_border_args;
	$border_group = new ppBorderGroup( $p4_border_args );
	return $border_group->markup;
}


/* border group class */
class ppBorderGroup {

	/* php 5 constructor */
	function __construct( $args ) {
		$this->key = $args['key'];
		$this->minwidth = ( isset( $args['minwidth'] ) ) ? (string) $args['minwidth'] : '1';
		$this->maxwidth = ( isset( $args['maxwidth'] ) ) ? (string) $args['maxwidth'] : '40';
		$this->setup_options();
		$this->build_markup();
	}


	/* prepare markup for individual items */
	function setup_options() {
		// border color
		$color = new ppOptionBox( $this->key . '_color', 'color', '', '' );
		$this->color_markup = $color->input_markup;

		// border width
		$width = new ppOptionBox( $this->key . '_width', 'slider|' . $this->minwidth . '|' . $this->maxwidth . '|px width', '', '' );
		$this->width_markup = $width->input_markup;

		// border style
		$style = new ppOptionBox( $this->key . '_style', 'select|solid|solid line|dashed|dashed line|dotted|dotted line|double|double line &nbsp;', '', '' );
		$this->style_markup = $style->input_markup;
	}


	/* return html markup of border group */
	function build_markup() {
		$classes_array = ppGetInterfaceClasses( $this->key );
		$classes_array[] = 'border-group';
		$classes = implode( ' ', $classes_array );
		$this->markup = <<<HTML
		<div class="$classes">
			<div class="border-options-top sc">
				<div class="border-option">$this->style_markup</div>
				<div class="border-option border-color">$this->color_markup</div>
			</div>
			<div class="border-option">$this->width_markup</div>
		</div>
HTML;
	}


	/* php 4 constructor */
	function ppBorderGroup( $args ) {
		$this->__construct( $args );
	}
}



