<?php
/* --------------------- */
/* -- sidebar options -- */
/* --------------------- */

// tabas and header
ppSubgroupTabs( array( 
	'fixed' => 'Fixed sidebar', 
	'drawer' => 'Sliding drawer sidebars',
	'ads' => 'Ad Banners', 
	'footer' => 'Footer area'
) );
ppOptionHeader( 'Sidebar Options', 'sidebar' );


/* fixed sidebar */
ppOptionSubgroup( 'fixed' );

// general
ppStartMultiple( 'Fixed sidebar' );
ppO( 'sidebar', 'radio|left|fixed sidebar on left|right|fixed sidebar on right' );
if ( pp::site()->hasStaticFrontPage ) {
	$homepage = 'posts';
	$frontpage = '|sidebar_on_front_page|yes|static front page';
} else {
	$homepage = 'home';
	$frontpage = '';
}
ppO( 'sidebar_on_which_pages', "checkbox{$frontpage}|sidebar_on_home|yes|$homepage page|sidebar_on_single|yes|individual post pages|sidebar_on_page|yes|static WordPress \"Pages\"|sidebar_on_archive|yes|archive, category, author, and search", 'display sidebar on which types of pages' );
ppStopMultiple();

// sidebar width and padding, bg color
ppStartMultiple( 'Fixed sidebar appearance' );
ppO( 'sidebar_width', 'slider|0|300| px|5', 'width of sidebar content area' );
ppO( 'sidebar_padding', 'slider|0|60| px', 'space between sidebar and edge of blog' );
ppO( 'sidebar_inner_padding_override', 'text|3', 'override padding on inner side of sidebar (in pixels)' );
ppO( 'sidebar_widget_margin_bottom', 'slider|0|100| px', 'spacing below widgets', 'Widget bottom spacing' );
ppStopMultiple();

// bg image
ppUploadBox::renderBg( 'sidebar_bg', 'Fixed sidebar background' );

// widget headlines
ppFontGroup( array(
	'key' => 'sidebar_headlines',
	'title' => 'Fixed sidebar headlines',
	'inherit' => 'all',
	'add' => array( 'margin_bottom' ),
	'is_link' => true,
) );


// widget text
ppFontGroup( array(
	'key' => 'sidebar_text',
	'title' => 'Fixed sidebar text',
	'inherit' => 'all',
	'add' => array( 'margin_bottom' ),
	'margin_bottom_comment' => 'paragraphs',
) );

// widget links
ppFontGroup( array(
	'key' => 'sidebar_link',
	'title' => 'Fixed sidebar links',
	'inherit' => 'all',
) );

// border
ppStartMultiple( 'Fixed sidebar border' );
ppO( 'sidebar_border_switch', 'radio|on|line|off|no line', 'show/hide line separating sidebar from content' );
ppBorderGroup( array( 'key' => 'sidebar_border', 'comment' => 'fixed sidebar line appearance' ) );
ppStopMultiple();

// sep image
ppUploadBox::renderImg( 'sidebar_widget_sep_img', 'Fixed sidebar widget separator image' );

ppEndOptionSubgroup();



/* sliding sidebar */
ppOptionSubgroup( 'drawer' );

// intro note
ppO( 'sliding_drawer_sidebar_note', 'note', ppString::id( 'blurb_sliding_drawers' ), 'Sliding drawers' );

// default drawer stuff
ppStartMultiple( 'Sliding drawer defaults' );
ppO( 'drawer_default_bg_color', 'color', 'default background color' );
ppO( 'drawer_default_opacity', 'slider', 'drawer opacity' );
ppO( 'drawer_tab_rounded_corners', 'radio|on|round corners|off|do not round corners', 'round tab corners for good browsers' );
ppO( 'drawer_padding', 'slider|0|50| pixels', 'drawer left/right margins' );
ppO( 'drawer_widget_btm_margin', 'slider|0|80| pixels', 'drawer widget bottom margins' );
ppStopMultiple();

// drawer sidebar widget headlines
ppFontGroup( array(
	'key' => 'drawer_widget_headlines',
	'title' => 'Drawer widget headline text',
	'inherit' => 'all',
) );

// drawer sidebar widget text
ppFontGroup( array(
	'key' => 'drawer_widget_text',
	'title' => 'Drawer widget text',
	'inherit' => 'all',
) );

// drawer tab text
ppFontGroup( array(
	'key' => 'drawer_tab',
	'title' => 'Drawer tab text appearance',
	'not' => array( 'weight', 'style' ),
) );

// drawer widget links
ppFontGroup( array(
	'title' => 'Drawer widget links',
	'key' => 'drawer_widget_link',
	'inherit' => 'all',
	'not' => array( 'size', 'family', 'weight', 'style', 'transform', 'hover_color', 'visited_color' ),
) );

// individual drawer settings
for ( $i = 1; $i <= pp::num()->maxSidebarDrawers; $i++ ) { 
	ppStartMultiple( 'Drawer #' . $i . ' settings' );
	ppO( 'drawer_tab_text_' . $i, 'text|30', 'tab title for tab #' . $i );
	ppO( 'drawer_tab_font_color_' . $i, 'color|optional', 'tab text font color' );
	ppO( 'blank', 'blank' );
	ppO( 'drawer_content_width_' . $i, 'slider|50|500| pixels', 'default width for drawer #' . $i );
	ppO( 'drawer_bg_color_' . $i, 'color|optional', 'background color for drawer #' . $i );
	ppO( 'blank', 'blank' );
	ppO( 'drawer_widget_text_font_color_' . $i, 'color|optional', 'widget text color' );
	ppO( 'drawer_widget_headlines_font_color_' . $i, 'color|optional', 'widget headlines color' );
	ppO( 'drawer_widget_link_font_color_' . $i, 'color|optional', 'widget link color' );
	ppStopMultiple();	
}

ppEndOptionSubgroup();



/* sponsor banner ads */
ppOptionSubgroup( 'ads' );
echo <<<HTML
<style type="text/css" media="screen">
	.show_ad_banners-false #subgroup-ads .upload-box {
		display:none;
	}
</style>
<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function($){
		ppOption.valToClass('show_ad_banners');
		ppOption.uploadReveal('banner');
	});
</script>
HTML;
// banner ad options
ppStartMultiple( 'Ad banners' );
ppO( 'show_ad_banners', 'radio|true|on|false|off', 'add banner links or ads in the bottom of your blog', 'first' );
ppO( 'ad_banners_area_lr_margin', 'text|3', 'spacing (in pixels) on left and right side of <em>entire sponsor banner area</em>', '' );
ppO( 'ad_banners_margin_right', 'text|3', 'spacing (in pixels) between <em>each individual sponsor banner</em> (side to side)', '' );
ppO( 'ad_banners_margin_btm', 'text|3', 'spacing (in pixels) below sponsor banners' );
ppO( 'ad_banners_border_color', 'color', 'color of border around sponsor banners' );
ppO( '', 'blank' );
ppStopMultiple();

// banner ad images
for ( $i = 1; $i <= pp::num()->maxAdBanners; $i++ ) { 
	ppUploadBox::renderLinkedImg( 'banner' . $i, 'Banner ' . $i, '', 'link banner to this address' );
}


ppEndOptionSubgroup();




/* FOOTER */
ppOptionSubgroup( 'footer' );


// include footer?
ppO( 'footer_include', 'radio|yes|Include footer area|no|Do not include footer area', '', 'Include footer area?' );

// footer options
ppUploadBox::renderBg( 'footer_bg', 'Footer content area background' );
ppUploadBox::renderImg( 'footer_btm_cap', 'Footer bottom cap image <span>optional image below footer</span>' );

// custom spacing
ppStartMultiple( 'Footer custom spacing' );
ppO( 'footer_left_padding', 'text|3', 'override default spacing (in pixels) on left side of footer' );
ppO( 'footer_right_padding', 'text|3', 'override default spacing (in pixels) on right side of footer' );
ppO( 'footer_col_padding', 'text|3', 'override default spacing (in pixels) between footer columns' );
ppO( 'footer_widget_margin_below', 'text|3', 'spacing (in pixels) below footer content chunks' );
ppStopMultiple();

// header font
ppFontGroup( array(
	'title' => 'Footer headings text appearance',
	'key' => 'footer_headings',
	'add' => array( 'margin_bottom' )
) );

// link and text
ppFontGroup( array(
	'title' => 'Footer links and text appearance',
	'key' => 'footer_link',
	'inherit' => 'all',
	'add' => array( 'nonlink_color' ),
) );

// custom copyright text
ppO( 'custom_copyright', 'text|50', 'Write your own custom footer copyright text -- shown at the very bottom of your blog. If blank, will read: <em>&copy; ' . date('Y') .  " " . pp::site()->name . '</em>', 'Custom copyright text' );

// remove footer attribution links
ppO( 'link_removal_txn_id', 'text|25', 'If you have purchased a license to remove the NetRivet, Inc. and ProPhoto links from the footer, enter the transaction ID for that purchase here.', 'Footer attribution links removal' );

ppEndOptionSubgroup();

