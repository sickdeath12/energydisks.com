<?php
/* --------------------- */
/* HEADER CUSTOMIZATIONS */
/* --------------------- */


/* Define subgroups */
$subgroups =  array(
	// 'shortname' => 'Long Pretty Name',
	'layout' => 'Layout &amp; Appearance',
	'logo' => 'Logo',
	'masthead' => 'Masthead Image &amp; Slideshow',
);
// How many masthead images defined?
global $mastheads_array;
for ( $i = 1; $i <= pp::num()->maxMastheadImages; $i++ ) {
	if ( ppImg::id( 'masthead_image' . $i )->exists ) {
		$mastheads_array[] = 'masthead_image' . $i;
	}
}
if ( count($mastheads_array) > 1 )
	$subgroups['reorder-masthead'] = 'Reorder Masthead images';


echo <<<HTML
<script>
jQuery(document).ready(function($){
	
	ppOption.uploadReveal( 'masthead_image' );

	// header layout custom javascript
	$('.header-thumb-button').mouseover(function(){
		var left = $(this).attr('id' );
		left = layouts[left] * 200;
		$('#headerlayout-viewer').css('background-position', '-'+left+'px 0' );
		$(this).addClass('hovered');
	});
	$('.header-thumb-button').mouseout(function(){
		var selected = $('#headerlayout-input').attr('value' );
		left = layouts[selected] * 200;
		$('#headerlayout-viewer').css('background-position', '-'+left+'px 0' );
		$(this).removeClass('hovered');
	});
	$('.header-thumb-button').click(function(){
		var selected = $(this).attr('id');
		$('#headerlayout-input').attr('value', selected);
		left = layouts[selected] * 200;
		$('#headerlayout-viewer').css('background-position', '-'+left+'px 0' );
		$('.header-thumb-button').removeClass('active-thumb');
		$(this).addClass('active-thumb');
	});
});

</script>
HTML;

echo ppBlogHeader::mastheadOptionJs( 'desktop' );


/* give extra explanation about masthead height and width, context-sensitive */
function ppMastheadHeightExplain() {
	$explain = ( ppHelper::logoInMasthead() )
		? "<span id='mhe'> Because of the header layout you've chosen, the height of this image must be the height of the logo you have uploaded" 
		: "<span id='mhe'> Because of the header layout you've chosen, this image <span class='first-image'>can be any height</span><span class='non-first-image'>must be the <strong>same height as your first masthead image</strong></span>";
	$explain .= ".</span>";
	return $explain;
}


/* draws header options form */
function ppHeaderOptions() { 
	// get current stored layout
	$stored_layout = ppOpt::id( 'headerlayout' );
	
	// array of layout names and positions sprite
	$layout = array(
			'logomasthead_nav'           => 0,
			'mastlogohead_nav'           => 1,
			'mastheadlogo_nav'           => 2,
			'logoleft_nav_masthead'      => 12,
			'logocenter_nav_masthead'    => 10,
			'logoright_nav_masthead'     => 14,
			'logoleft_masthead_nav'      => 13,
			'logocenter_masthead_nav'    => 11,
			'logoright_masthead_nav'     => 15,
			'nav_masthead_logoleft'      => 6,
			'nav_masthead_logocenter'    => 4,
			'nav_masthead_logoright'     => 8,
			'masthead_nav_logoleft'      => 7,
			'masthead_nav_logocenter'    => 5,
			'masthead_nav_logoright'     => 9,
			'nav_masthead'               => 16,
			'masthead_nav'               => 17,
			'pptclassic'                 => 3,
			'masthead_logoleft_nav'      => 18,
			'masthead_logocenter_nav'    => 19,
			'masthead_logoright_nav'     => 20,
			'nav_logoleft_masthead'      => 21,
			'nav_logocenter_masthead'    => 22,
			'nav_logoright_masthead'     => 23,
		);
	
	// starting css offset for main image sprite
	$main_img_offset = $layout[$stored_layout] * 200; /* 200 = img width of header preview */	
	
	// use $layout array to echo javascript array
	ob_start();
	echo '<script>var layouts = new Object();';
	foreach ( $layout as $layout_name => $layout_position ) {
		echo "layouts['$layout_name'] = $layout_position;\n";
	}
	echo '</script>';
	
	// start HTML
	echo <<<HTML
	<input id="headerlayout-input" type="hidden" name="p_headerlayout" value="$stored_layout">
	<div id="headerlayout-viewer-wrapper">
		<p>Currently selected header layout:</p>
		<div id="headerlayout-viewer" style="background-position:-{$main_img_offset}px 0"></div>
	</div>
	<div id="header-thumbs" class="sc">
HTML;
	
	// thumb divs
	foreach ( $layout as $layout_name => $layout_position ) {
		
		// give a special class to the current stored choice
		$class = ( $layout_name == $stored_layout ) ? ' active-thumb': '';
		
		// calculate CSS offset to show right part of sprite
		$left = $layout_position * 55; /* 55 = thumb width of header previews */
		
		// print markup including CSS offset
		echo "<div id='$layout_name' class='header-thumb-button sc$class'>
			     <div style='cursor:pointer;background-position:-{$left}px 0;'></div>
		     </div>";
	} 
	
	// close header-thumbs div		
	echo '</div><!-- #header-thumbs -->';			

	// get buffered output into var and return
	$markup = ob_get_clean();
	return $markup;
	
} // end function ppHeaderOptions()





ppSubgroupTabs( $subgroups );
ppOptionHeader('Header Area Options', 'header' );

/* layout subgroup */
ppOptionSubgroup( 'layout' );

// header layout
ppO( 'headerlayout', 'function|ppHeaderOptions', '', 'Header area layout' );

// header bg color
ppO('header_bg_color', 'color|optional', 'background color behind header area (only seen with logo images narrower than blog width and not on all header layouts)', 'Header background color' );

// masthead border top/bottom
ppStartMultiple( 'Custom lines above/below masthead' );
ppO( 'masthead_top_border', 'radio|on|custom line above masthead|off|no custom line above masthead', 'custom line <em>above</em> masthead area' );
ppO( 'masthead_btm_border', 'radio|on|custom line below masthead|off|no custom line below masthead', 'custom line <em>below</em> masthead area' );
ppO( 'masthead_top_blank', 'blank' );
ppBorderGroup( array( 'key' => 'masthead_top_border', 'comment' => 'appearance of line <em>above</em> masthead' ) );
ppBorderGroup( array( 'key' => 'masthead_btm_border', 'comment' => 'appearance of line <em>below</em> masthead' ) );
ppStopMultiple();
ppEndOptionSubgroup();




/* logo subgroup */
ppOptionSubgroup( 'logo' );
ppUploadBox::renderLinkedImg( 'logo', 'Logo image' );
ppO( 'logo_swf_switch', 'radio|off|use a normal logo image|on|upload a custom flash movie logo', 'optionally replace a simple static logo image with your own custom flash movie', 'Flash movie logo override' );
ppUploadBox::renderSwf( 'logo_swf', 'Custom logo flash movie' );
ppEndOptionSubgroup();



/* masthead subgroup */
ppOptionSubgroup( 'masthead' );

// masthead options
ppBlogHeader::mastheadOptions( 'desktop' );


$masthead_title = '
	<span class="masthead-conditional mc-static">Static masthead image</span>
	<span class="masthead-conditional mc-random">Masthead image %NUM%</span>
	<span class="masthead-conditional mc-slideshow">Masthead slideshow image %NUM%</span>
	<span class="masthead-conditional mc-custom">Masthead flash fallback image</span>';
	
$masthead_comment = '
	<span class="masthead-conditional mc-static">This is your single static masthead image.</span>
	<span class="masthead-conditional mc-random">This is one of your randomly-displayed masthead images.</span>
	<span class="masthead-conditional mc-slideshow">This is one of your masthead slideshow images.</span>
	<span class="masthead-conditional mc-custom">This is your fallback masthead image, displayed if the user does not have flash enabled. The dimensions of this image must be exactly the same as your custom flash movie.</span> ' . ppMastheadHeightExplain();


// print masthead images	
for ( $i = 1; $i <= pp::num()->maxMastheadImages; $i++ ) {
	$numbered_masthead_title = str_replace( '%NUM%', $i, $masthead_title );
	ppUploadBox::renderMastheadImg( 'masthead_image' . $i, $numbered_masthead_title, $masthead_comment );
}
ppUploadBox::renderSwf( 'masthead_custom_flash', 'Masthead custom flash movie', 'Custom flash movie (in .swf format) to replace masthead image area. Must be exactly the same dimensions as the fallback image' );
ppEndOptionSubgroup();

/* custom option function for re-ordering masthead images */
function ppMastheadReorderOption() {
	global $mastheads_array;
	$masthead_img_list = '';
	foreach( $mastheads_array as $image ) {
			$masthead_img_list .= '<li id="' . $image . '">';
			$masthead_img_list .= '<img src="' . ppImg::id( $image )->url . '" /></li>'."\n";
		}
	return <<<HTML
	<input type="hidden" name="masthead_order_string" value="" id="masthead_order_string" size="180"><br />
	<input type="hidden" name="masthead_order_reordered" value="false" id="masthead_order_reordered">
	<ul id="masthead_order">
		$masthead_img_list
	</ul>
	<script>
	jQuery(document).ready(function($){
		$('#masthead_order_reordered').val('false');
		$('#masthead_order').sortable({
			opacity: 0.4,
			scroll: true,
			containment: '#masthead_order',
			update: function() {
				$('#masthead_order_reordered').val('true');
				$('#masthead_order_string')
					.val($('#masthead_order').sortable('serialize',{key:'ppMastheadOrder[]'}));
			}
		});
	});
	</script>
HTML;
}


/* Reorder Masthead Images */
if ( count( $mastheads_array ) > 1 ) {
	ppOptionSubgroup( 'reorder-masthead' );
	ppO( 'masthead_reorder', 'function|ppMastheadReorderOption', 'drag and drop your masthead images to reorder them', 'Reorder masthead images' );
	ppEndOptionSubgroup();
}

?>