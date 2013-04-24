<?php
/* ----------------------- */
/* -- BIO AREA OPTIONS --- */
/* ----------------------- */

echo <<<HTML
<style type="text/css" media="screen">
.bio_include-no .option,
.bio_include-no #subgroup-nav {
	display:none;
}
.bio_include-no #bio_include-option-section {
	display:block;
}
</style>
<script>
jQuery(document).ready(function($){
	ppOption.valToClass( 'bio_include' );
});
</script>
HTML;

$subgroups =  array(
	'general'    => 'General',
	'background' => 'Background',
	'biopic'     => 'Picture',
	'appearance' => 'Appearance',
	'content'    => 'Content',
);


ppSubgroupTabs( $subgroups );
ppOptionHeader('Bio Area Options', 'bio' );


/* bio general subgroup */
ppOptionSubgroup( 'general' );

// include / don't include bio area
ppO( 'bio_include', 'radio|yes|Include bio area|no|Do not include bio area', '', 'Include bio area?' );

// hidden (minimized) bio
ppStartMultiple( 'Bio area display type' );
ppO( 'use_hidden_bio', 'radio|no|shown normally|yes|minimized', 'Bio area shown normally, or "minimized" to an "About Me" link in the navigation menu' );
ppStopMultiple();

// bio on which pages?
ppStartMultiple( 'Bio on page types' );
if ( pp::site()->hasStaticFrontPage ) {
	$static = '|bio_front_page|on|Static front page';
	$home = 'Posts page';
} else {
	$static = '';
	$home = 'Home';
}
ppO( 'bio_pages_options', "checkbox{$static}|bio_home|on|$home|bio_single|on|Single post|bio_page|on|Pages|bio_archive|on|Archive|bio_category|on|Category|bio_tag|on|Tag archive|bio_search|on|Search results|bio_author|on|Author archives", 'choose which types of pages you want the bio to appear on' );
ppO( 'bio_pages_minimize', 'radio|none|no bio section on unchecked|minimized|bio section minimized on unchecked', 'on unchecked pages, choose to either not show bio at all, or have bio "minimized" to a "about me" link in nav menu' );
ppStopMultiple();

ppEndOptionSubgroup();



/* background subgroup */
ppOptionSubgroup( 'background' );

// main bg
ppUploadBox::renderBg( 'bio_bg', 'Bio area main background' );

// inner bg
ppUploadBox::renderBg( 'bio_inner_bg', 'Bio area optional inner background image' );

// bio separator (border or image)
ppStartMultiple( 'Separator below bio area' );
ppO( 'bio_border', 'radio|border|custom line below bio area|image|upload image as a bottom border|noborder|no border or image' );
ppBorderGroup( array( 'key' => 'bio_border', 'comment' => 'custom line appearance' ) );
ppStopMultiple();

// bio separator image
ppUploadBox::renderImg( 'bio_separator', 'Bio section custom separator', 'Upload a custom image to be displayed centered beneath your bio.' );

ppEndOptionSubgroup();



/* biopic subgroup */
ppOptionSubgroup( 'biopic' );

echo <<<HTML
<style type="text/css" media="screen">
	.biopic_display-off #subgroup-biopic .upload-box {
		display:none;
	}
	.biopic_display-normal #subgroup-biopic .upload-box.empty {
		display:none;
	}
	.biopic_display-normal #subgroup-biopic #upload-box-biopic1 {
		display:block;
	}
</style>
<script>
jQuery(document).ready(function($){
	
	ppOption.uploadReveal( 'biopic' );
	ppOption.valToClass( 'biopic_display' );
	
});
</script>
HTML;

// biopic display options
ppStartMultiple( 'Bio picture' );
ppO( 'biopic_display', 'radio|normal|Always show same bio picture|random|Random bio picture on each page load|off|Do not show bio picture' );
ppO( 'biopic_align', 'radio|left|on left side of bio area|right|on right side of bio area', 'Alignment of bio picture', 'choose left or right alignment for your bio picture' );
ppStopMultiple();


// bio picture border options
ppStartMultiple( 'Border around bio picture' );
ppO( 'biopic_border', 'radio|on|show border|off|no border', 'show/hide custom border around bio picture' );
ppBorderGroup( array( 'key' => 'biopic_border', 'comment' => 'biopic border appearance' ) );
ppStopMultiple();


// bio pictures
ppUploadBox::renderImg('biopic1', 'Bio picture' );
for ( $i = 2; $i <= pp::num()->maxBioImages; $i++ ) { 
	ppUploadBox::renderImg( 'biopic' . $i, "Bio picture $i", 'Bio picture #' . $i . '. Must match the dimensions of the first bio picture to display correctly.' );
}
ppEndOptionSubgroup(); // END biopic



/* appearance subgroup */
ppOptionSubgroup( 'appearance' );

// bio area padding & margins
ppStartMultiple( 'Bio area spacing' );
ppO( 'bio_top_padding', 'slider|0|100| px', 'spacing above bio content' );
ppO( 'bio_btm_padding', 'slider|0|100| px', 'spacing below bio content' );
ppO( 'bio_gutter_width', 'text|3', 'override default spacing (in pixels) between bio content columns' );
ppO( 'bio_lr_padding', 'text|3', 'override default spacing (in pixels) on left/right of bio content' );
ppStopMultiple();


// widget spacing
ppStartMultiple( 'Bio content (widget) spacing' );
ppO( 'bio_widget_margin_btm', 'text|3', 'spacing (in pixels) below bio area content chunks (widgets)' );
ppO( 'bio_headline_margin_btm', 'text|3', 'override space (in pixels) below bio content (widget) headlines' );
ppStopMultiple();


// bio headlines appearance
ppFontGroup( array(
	'key' => 'bio_header',
	'title' => 'Bio area headline text appearance',
	'inherit' => 'all',
) );


// bio text appearance
ppFontGroup( array(
	'key' => 'bio_para',
	'title' => 'Bio area text appearance',
	'inherit' => 'all',
) );


// bio text appearance
ppFontGroup( array(
	'key' => 'bio_link',
	'title' => 'Bio area link appearance',
	'inherit' => 'all',
) );

ppEndOptionSubgroup();



/* bio content subgroup */
ppOptionSubgroup( 'content' );

ppO( 'bio_content', 'note', ppString::id( 'blurb_bio_content' ) );



ppEndOptionSubgroup();

