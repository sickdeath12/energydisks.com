<?php
/* ------------------------------------------------------------------------ */
/* -- library of functions used for creating option sections (non-image) -- */
/* ------------------------------------------------------------------------ */


/* wrapper factory function to create a new instance of p4_option_box class */
function ppO( $name, $params, $comment = '', $title = '' ) {
	global $p4_multiple_option;
	
	$option = ( $p4_multiple_option ) 
		? new ppOptionBoxMultiple( $name, $params, $comment, $title )
		: new ppOptionBoxIndividual( $name, $params, $comment, $title );
		
	$option->wrap_and_print_markup();
}


/* opens a multiple option group by setting global index, and optionally title */
function ppStartMultiple( $title = '' ) {
	global $p4_multiple_option, $p4_option_group_title;
	
	// nominal case: set global indext to 1
	if ( !$p4_multiple_option ) {
		$p4_multiple_option = 1;
	
	// OOPS: forgot to close a multiple group, handle it gracefully
	} else {
		ppStopMultiple();
		$p4_multiple_option = 1;
	}
	
	// set the title for the whole following option group
	if ( $title ) $p4_option_group_title = $title;
}


/* closes a multiple option group */
function ppStopMultiple() {
	global $p4_multiple_option, $p4_option_group_title;
	
	// close out markup
	if ( $p4_multiple_option != false ) ppOptionBoxMultiple::end_multiple_section();
	
	// reset global counter and group title
	$p4_multiple_option = $p4_option_group_title = false;
}


/* creates a title header for option section */
function ppOptionHeader( $title, $id, $comment = '' ) {
	echo <<<HTML
	<div id="tab-section-$id-link" class="tabbed-sections">
HTML;
	do_action( "p4_options_pre_tab_{$id}" );
}


/* Validate a color code */
function ppValidateColor( $color ) {
    $color = substr( $color, 0, 7 );
    return preg_match( '/#[0-9a-fA-F]{6}/', $color );
}


/* print list of subgroup subtabs for option areas */
function ppSubgroupTabs( $subgroups ) {
	// keep track of subtab selected
	$helper = ( isset( $_POST['subtab'] ) ) ? $_POST['subtab'] : 'unset' ;
	echo '<input id="subtab-post" type="hidden" value="'.$helper.'" name="subtab" />';
	
	// echo out subtab nav
	echo "<ul id='subgroup-nav' class='sc'>\n";
	foreach ( $subgroups as $key => $val ) {
		echo "<li id='subgroup-nav-$key'><a key='$key' href='#$key' class='subgroup-link'>$val</a></li>\n";
	}
	echo "</ul>\n";
}

/* start an option-page subgroup div */
function ppOptionSubgroup( $shortname ) {
	echo "<div id='subgroup-$shortname' class='subgroup'>\n";
}


/* spit out a closing div to end an option subgroup */
function ppEndOptionSubgroup() {
	echo '</div>';
}


/* display contact log items */
function ppContactLog() {
	$contact_log = get_option( pp::wp()->dbContactLog );
	if ( !$contact_log ) {
		return NrHtml::p( 'There is no record of any submitted contact form requests.' );
	}
	$log = '';
	foreach ( $contact_log as $submission ) {
		$log .= '<p>Submitted: ' . date( 'm-d-Y', $submission['time'] ) . '</p>';
		$log .= '<p class="data">' . nl2br( $submission['data'] ) . '</p>';
	}
	return $log;
}


/* user array data to create and return interface classes for css and js */
function ppGetInterfaceClasses( $option_name ) {
	global $p4_interface;
	
	if ( isset( $p4_interface[$option_name] ) ) {
		$show_hide_info = $p4_interface[$option_name];
	} else {
		return array();
	}
		
	// turn interface string into useable info
	$test_parts = explode( ' ', str_replace( 'hide if ', '', $show_hide_info ) );
	list( $controlling_option, $testing_values ) = $test_parts;
	$hidden_values = explode( '|', $testing_values );
	
	foreach ( $hidden_values as $hidden_value ) {
		if ( ppOpt::test( $controlling_option, $hidden_value ) ) {
			$interface_classes[] = 'start-hidden';
		}
		$interface_classes[] = 'hide-when-' . $controlling_option . '-val-' . $hidden_value;
		$interface_classes[] = 'show-when-' . $controlling_option . '-clicked';
	}

	return (array) $interface_classes;
}


/* write js and css to control complex interface displays of post-interaction link items */
function ppPostInteractionInterface() {
	$exclude = array();
	
	// get which post-interaction link areas are currently set to be not included
	if ( ppOpt::test( 'comments_header_linktothispost_link_include', 'no' ) ) {
		$exclude[] ='#comments_header_linktothispost_link_include-option-section';
	}
	if ( ppOpt::test( 'comments_header_emailafriend_link_include', 'no' ) ) {
		$exclude[] ='#comments_header_emailafriend_link_include-option-section';
	}
	$exclude_list = implode( ',', (array) $exclude );
	
	// prepare a body class reflecting current post-interaction display stored value
	$body_class   = 'comments-layout-' . ppOpt::id( 'comments_layout' ) . ' pi-display-' . ppOpt::id( 'comments_post_interact_display' );
	if ( !ppOpt::test( 'comments_layout', 'minima' ) ) $body_class .= ' comments-layout-not-minima';
	
	// print js and css to control interface interactions
	echo <<<HTML
	<script>
	jQuery(document).ready(function($){
		// earmark dependent items under optional post interact link areas
		$('#comments_header_linktothispost_link_include-option-section,#comments_header_emailafriend_link_include-option-section').each(function(){
			$('.individual-option', this).not(':first').addClass('dependent');
		});
		// body gets class showing state of 'comments_post_interact_display'
		$('body').addClass('$body_class');
		// options shown only when pi-links is NOT set to 'images'
		$('#comments_header_emailafriend_link_icon-individual-option, #comments_header_linktothispost_link_icon-individual-option, #comments_header_addacomment_link_icon-individual-option').addClass('if-display-not-images');
		// options shown only when pi-links is set to images
		$('#comments_header_emailafriend_link_image-individual-option, #comments_header_linktothispost_link_image-individual-option, #comments_header_addacomment_link_image-individual-option').addClass('if-display-images');
		// add classes to sections that area currently excluded
		$('$exclude_list').addClass('exclude');
	
		// post interact display clicks, update body class
		$('#comments_post_interact_display-individual-option input').click(function(){
			$('body')
				.removeClass('pi-display-button')
				.removeClass('pi-display-text')
				.removeClass('pi-display-images')
				.addClass('pi-display-'+$(this).val());
		});
	
		// post interact individual option includes: update section class
		$('#comments_header_linktothispost_link_include-individual-option input, #comments_header_emailafriend_link_include-individual-option input')
			.click(function(){
				var clicked = $(this);
				var section = clicked.parents('.option-section')
				if (clicked.val() == 'yes') {
					section.removeClass('exclude');
				} else {
					section.addClass('exclude');
				}
			});
	
	});
	</script>
	<style type="text/css" media="screen">
	.if-display-images {
		display:none;
	}
	body.pi-display-images .if-display-images {
		display:block;
	}
	body.pi-display-images .if-display-not-images {
		display:none;
	}
	.exclude .dependent {
		display:none !important;
	}
	body.pi-display-images #comments_header_post_interaction_link_font_group-option-section {
		display:none !important;
	}
	body.comments-layout-not-minima #comments_header_addacomment_link_image-individual-option,
	body.comments-layout-not-minima #comments_header_emailafriend_link_image-individual-option,
	body.comments-layout-not-minima #comments_header_linktothispost_link_image-individual-option,
	body.comments-layout-not-minima #comments_header_addacomment_link_icon-individual-option,
	body.comments-layout-not-minima #comments_header_emailafriend_link_icon-individual-option,
	body.comments-layout-not-minima #comments_header_linktothispost_link_icon-individual-option {
		display:none !important;
	}
	body.comments-layout-not-minima #comments_header_post_interaction_link_font_group-option-section,
	body.comments-layout-not-minima #comments_header_post_interaction_link_font_group-individual-option {
		display:block !important;
	}
	</style>
HTML;
}

