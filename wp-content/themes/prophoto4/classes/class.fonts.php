<?php
/* ------------------------------------------------------- */
/* -- class for printing groupings of font/link options -- */
/* ------------------------------------------------------- */



/* helper function to set up the instantiation of the p4_font_group class */
function ppFontGroup( $args ) {
	global $p4_font_args;
	$p4_font_args = $args;
	$comment = isset( $args['comment'] ) ? $args['comment'] : null;
	$title = isset( $args['title'] ) ? $args['title'] : null;
	ppO( $args['key'] . '_font_group', 'function|ppNewFontGroup', $comment, $title );
}


/* helper function to instantiate the p4_font_group class from within an option area */
function ppNewFontGroup() {
	global $p4_font_args;
	$font_group = new ppFontGroup( $p4_font_args );
	return $font_group->markup();
}


/* font group class */
class ppFontGroup{

	const FONT_STYLE_SELECT = 'select||select...|normal|normal|italic|italic';
	const LINK_DECORATION_SELECT = 'select||select...|none|no decoration|underline|underlined|overline|overlined|line-through|line through';
	private $did_nonlink_fontcolor = false;
	private $size_markup;
	private $family_markup;
	private $weight_markup;
	private $style_markup;
	private $transform_markup;
	private $decoration_markup;
	private $lineheight_markup;
	private $letterspacing_markup;
	private $color_markup;
	private $nonlink_color_markup;
	private $margin_bottom_markup;
	private $link_extras;
	private $link_color_markup;
	private $preview;
	private $visited_color_markup;
	private $visited_state;
	private $hover_color_markup;


	/* php 5 constructor */
	function __construct( $args ) {
		$this->args = $args;
		$this->key = $args['key'];
		$key = $this->key;
		$this->include_options = $this->included_options();
		$this->get_inheritance();
		$this->preview_area();

		// stored values
		$this->transform_val     = ppOpt::id( $key . '_text_transform' );
		$this->style_val         = ppOpt::id( $key . '_font_style' );
		$this->weight_val        = ppOpt::id( $key . '_font_weight' );
		$this->lineheight_val    = ppOpt::id( $key . '_line_height' );
		$this->letterspacing_val = ppOpt::id( $key . '_letterspacing' );

		// css display vals
		$this->css_transform_val  =  str_replace( '.', '', $this->transform_val );
		$this->css_style_val      =  str_replace( '.', '', $this->style_val );
		$this->css_weight_val     =  str_replace( '.', '', $this->weight_val );
	    $this->css_lineheight_val =  str_replace( '.', '', $this->lineheight_val );

		// font size markup
		if ( $this->include_options['size'] ) {
			$size = new ppOptionBox( $key . '_font_size', 'text|2', '', '' );
			$this->size_markup = '<div class="inline-option inline-text-option">' . $size->input_markup . '<span>px &nbsp;</span></div>';
		}

		// font family markup
		if ( $this->include_options['family'] ) {
			$family = new ppOptionBox( $key . '_font_family', $this->fontFamilySelect(), '', '' );
			$this->family_markup = $family->input_markup;
		}

		// non-link font color
		if ( isset( $this->include_options['nonlink_color'] ) && $this->include_options['nonlink_color'] ) {
			$optional = ( $this->can_inherit( 'nonlink_color' ) ) ? '|optional' : '';
			$nonlink_key = str_replace( '_link', '', $key );
			$nonlink_color = new ppOptionBox( $nonlink_key . '_font_color', 'color' . $optional, '', '' );
			$this->nonlink_color_markup = '<div class="inline-option inline-color-option">' . $nonlink_color->input_markup . '</div>';
			$this->did_nonlink_fontcolor = true;
		}

		// font color markup
		if ( $this->include_options['color'] && !$this->did_nonlink_fontcolor ) {
			$optional = ( $this->can_inherit( 'color' ) ) ? '|optional' : '';
			$color = new ppOptionBox( $key . '_font_color', 'color' . $optional, '', '' );
			$this->color_markup = '<div class="inline-option inline-color-option">' . $color->input_markup . '</div>';
		}

		// font weight markup
		if ( $this->include_options['weight'] ) {
			$optional = ( $this->can_inherit( 'weight' ) ) ? '|' : '';
			$weight = new ppOptionBox( $key . '_font_weight', 'select||select...|400|normal|700|bold', '', '' );
			$this->weight_markup = '<a class="font-button font-button-weight font-button-weight-val-' . $this->css_weight_val . '" type="weight" val="' . $this->weight_val . '" options="700|400' . $optional . '"></a><div class="hidden-input font-group-hidden-input-weight">' . $weight->input_markup . '</div>';
		}

		// font style markup
		if ( $this->include_options['style'] ) {
			$optional = ( $this->can_inherit( 'style' ) ) ? '|' : '';
			$style = new ppOptionBox( $key . '_font_style', self::FONT_STYLE_SELECT, '', '' );
			$this->style_markup = '<a class="font-button font-button-style font-button-style-val-' . $this->css_style_val . '" type="style" val="' . $this->style_val . '" options="italic|normal' . $optional . '"></a><div class="hidden-input font-group-hidden-input-style">' . $style->input_markup . '</div>';
		}

		// text transform markup
		if ( $this->include_options['transform'] ) {
			$optional = ( $this->can_inherit( 'transform' ) ) ? '|' : '';
			$transform = new ppOptionBox( $key . '_text_transform', 'select||select...|none|Normal|uppercase|UPPERCASE|lowercase|lowercase', '', '' );
			$this->transform_markup = '<a class="font-button font-button-transform font-button-transform-val-' . $this->css_transform_val . '" type="transform" val="' . $this->transform_val . '" options="uppercase|lowercase|none' . $optional . '"></a><div class="hidden-input font-group-hidden-input-transform">' . $transform->input_markup . '</div>';
		}

		// lineheight markup
		if ( isset( $this->include_options['lineheight'] ) && $this->include_options['lineheight'] ) {
			$optional = ( $this->can_inherit( 'lineheight' ) ) ? '|' : '';
			$line_height = new ppOptionBox( $key . '_line_height', 'select||line spacing...|1|1.0|1.25|1.25|1.5|1.5|1.75|1.75|2|2.0', '', '' );
			$this->lineheight_markup = '<a class="font-button font-button-lineheight font-button-lineheight-val-' . $this->css_lineheight_val . '" type="lineheight" val="' . $this->lineheight_val . '" options="1|1.25|1.5|1.75|2' . $optional . '"></a><div class="hidden-input font-group-hidden-input-lineheight">' . $line_height->input_markup . '</div>';
		}

		// letterspacing markup
		if ( isset( $this->include_options['letterspacing'] ) && $this->include_options['letterspacing'] ) {
			$letterspacing = new ppOptionBox( $key . '_letterspacing', 'select|normal|normal|1px|1px|2px|2px|-2px|-2px|-1px|-1px', '', '' );
			$this->letterspacing_markup = '<a class="font-button font-button-letterspacing font-button-letterspacing-val-' . $this->letterspacing_val . '" type="letterspacing" val="' . $this->letterspacing_val . '" options="normal|1px|2px|-2px|-1px"></a><div class="hidden-input font-group-hidden-input-letterspacing">' . $letterspacing->input_markup . '</div>';
		}

		// margin bottom markup
		if ( isset( $this->include_options['margin_bottom'] ) && $this->include_options['margin_bottom'] ) {
			$margin_bottom = new ppOptionBox( $key . '_margin_bottom', 'text|2', '', '' );
			$marginBtmComment = ( isset( $this->args['margin_bottom_comment'] ) ) ? $this->args['margin_bottom_comment'] : '';
			$this->margin_bottom_markup = '<div class="inline-option inline-text-option">' . $margin_bottom->input_markup . '<span>px below ' . $marginBtmComment . '&nbsp;</span></div>';
		}

		// link markup
		if ( $this->is_link() ) {
			$this->setup_link_markup();
		}
	}


	function fontFamilySelect() {
		return ppFontUtil::familyParams();
	}


	/* build markup for link groupings */
	function setup_link_markup() {
		// stored values
		$this->decoration_val = ppOpt::id( $this->key . '_decoration' );
		$this->hover_decoration_val = ppOpt::id( $this->key . '_hover_decoration' );

		// css display vals
		$this->css_decoration_val = str_replace( '.', '', $this->decoration_val );
		$this->css_hover_decoration_val = str_replace( '.', '', $this->hover_decoration_val );

		// normal link decoration
		if ( $this->include_options['decoration'] ) {
			$decoration = new ppOptionBox( $this->key . '_decoration', self::LINK_DECORATION_SELECT, '', '' );
			$this->decoration_markup = '<a class="font-button font-button-decoration font-button-decoration-val-' . $this->css_decoration_val . '" type="decoration" val="' . $this->decoration_val . '" options="underline|none|overline|line-through|"></a><div class="hidden-input font-group-hidden-input-decoration">' . $decoration->input_markup . '</div>';
		}

		// hover link decoration
		if ( $this->include_options['hover_decoration'] ) {
			$hover_decoration = new ppOptionBox( $this->key . '_hover_decoration', self::LINK_DECORATION_SELECT, '', '' );
			$this->hover_decoration_markup = '<a class="font-button font-button-decoration font-button-hover_decoration-val-' . $this->css_hover_decoration_val . '" type="hover_decoration" val="' . $this->hover_decoration_val . '" options="underline|none|overline|line-through|"></a><div class="hidden-input font-group-hidden-input-hover_decoration">' . $hover_decoration->input_markup . '</div>';
		}

		// hover color
		if ( $this->include_options['hover_color'] ) {
			$hover_color = new ppOptionBox( $this->key . '_hover_font_color', 'color|optional', '', '' );
			$this->hover_color_markup = '<div class="inline-option inline-color-option">' . $hover_color->input_markup . '</div>';
		}

		// visited color
		if ( $this->include_options['visited_color'] ) {
			$visited_color = new ppOptionBox( $this->key . '_visited_font_color', 'color|optional', '', '' );
			$this->visited_color_markup = '<div class="inline-option inline-color-option">' . $visited_color->input_markup . '</div>';
		}

		// link-color (only shown when non-link color included)
		if ( $this->doingNonLink() ) {

			if ( $this->decoration_markup ) {
				$decorationMarkup = $this->decoration_markup;
				$this->decoration_markup = null;
			} else {
				$decorationMarkup = '';
			}

			$link_color = new ppOptionBox( $this->key . '_font_color', 'color|optional', '', '' );
			$this->link_color_markup = '<span class="font-group-subsection-label font-group-subsection-label-link">links:</span>' . $decorationMarkup . '<div class="inline-option inline-color-option nonlink-section">' . $link_color->input_markup . '</div>';

		}

		// hover state options
		if ( $this->hover_decoration_markup || $this->hover_color_markup ) $this->hover_state = <<< HTML
		<span class="font-group-subsection-label">hovered links:</span>
		$this->hover_decoration_markup
		$this->hover_color_markup
HTML;

		// visited state options
		if ( $this->visited_color_markup ) $this->visited_state = <<< HTML
		<span class="font-group-subsection-label font-group-subsection-label-visited">visited links:</span>
		$this->visited_color_markup
HTML;

		// link extra markup
		if ( $this->hover_state || $this->visited_state || $this->link_color_markup )
			$this->link_extras = <<< HTML
		<div class="pp-font-group-subgroup pp-font-group-subgroup-links">
			$this->link_color_markup
			$this->hover_state
			$this->visited_state
		</div>
HTML;
	}


	function is_link() {
		return NrUtil::isIn( '_link', $this->key );
	}


	function doingNonLink() {
		return ( isset( $this->include_options['nonlink_color'] ) && $this->include_options['nonlink_color'] );
	}


	/* return full markup for option section */
	function markup() {
		return <<< HTML
		<div id="pp-font-group-{$this->key}" class="pp-font-group sc">
			<div class="pp-font-group-subgroup pp-font-group-subgroup-normal">
				$this->size_markup
				$this->family_markup
				$this->weight_markup
				$this->style_markup
				$this->transform_markup
				$this->decoration_markup
				$this->lineheight_markup
				$this->letterspacing_markup
				$this->color_markup
				$this->nonlink_color_markup
				$this->margin_bottom_markup
			</div>
			$this->link_extras
		</div>
		$this->preview
HTML;
	}


	/* return array of which options to include */
	function included_options() {
		// set up defaults
		$default_options = array(
			'size'             => true,
			'family'           => true,
			'color'            => true,
			'weight'           => true,
			'style'            => true,
			'transform'        => true,
			'decoration'       => true,
			'hover_decoration' => true,
			'hover_color'      => true,
			'visited_color'    => true,
		);


		// start by setting custom options to default
		$included_options = $default_options;

		// remove specified options from default
		$removed_options = isset( $this->args['not'] ) ? $this->args['not'] : null;
		if ( is_array( $removed_options ) ) {
			foreach ( $removed_options as $removed_option ) {
				$included_options[$removed_option] = false;
			}
		}

		// add extra options
		$added_options = isset( $this->args['add'] ) ? $this->args['add'] : null;
		if ( is_array( $added_options ) ) {
			foreach ( $added_options as $added_option ) {
				$included_options[$added_option] = true;
			}
		}

		return $included_options;
	}


	/* boolean test, does this option inherit */
	function can_inherit( $option ) {
		if ( $this->inheriting == 'all' ) {
			return true;
		}
		if ( isset( $this->inheriting[$option] ) ) {
			return true;
		}
		return false;
	}


	/* setup inheritance info */
	function get_inheritance() {
		$inherit = isset( $this->args['inherit'] ) ? $this->args['inherit'] : null;

		// everything inherits
		if ( $inherit == 'all' ) return $this->inheriting = 'all';

		// nothing inherits
		if ( !$inherit ) return $this->inheriting = false;

		// individual items are specified to inherit
		if ( is_array( $inherit ) ) {
			foreach ( $inherit as $inheriting_option ) {
				$this->inheriting[$inheriting_option] = true;
			}
			return $this->inheriting;
		}

		// catch errors, do not inherit
		$this->inheriting = false;
	}


	/* fetch font live preview text */
	function preview_area() {
		global $p4_font_preview_text;
		if ( isset( $this->args['preview'] ) ) {
			$text = $this->args['preview'];
		} else if ( isset( $p4_font_preview_text[$this->key] ) ) {
			$text = $p4_font_preview_text[$this->key];
		} else if ( NrUtil::isIn( 'nav_menu_link', $this->key ) || NrUtil::isIn( 'widget_menu_', $this->key ) ) {
			return;
		} else {
			$text = $p4_font_preview_text['default'];
		}
		$this->preview = "<div id='{$this->key}-font-preview' class='font-preview sc'>$text</div>";
	}


	/* php 4 constructor */
	function ppFontGroup( $args ) {
		$this->__construct( $args );
	}
}


