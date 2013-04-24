<?php

echo <<<HTML
<script>
jQuery(document).ready(function(){
	ppImageDependent('blog_bg', 'blog_bg_img_repeat' );
});	
</script>
HTML;

ppOptionHeader('Background Options', 'background' );

/* bg colors & appearance subgroup */

// blog bg
ppUploadBox::renderBg( 'blog_bg', 'Site outer background #1 <span>main color and background image behind your entire site</span>' );
ppUploadBox::renderBg( 'blog_bg_inner', 'Site outer background #2 <span>overlays background #1</span>' );

// top & bottom margins
ppStartMultiple( 'Site top and bottom margins' );
ppO( 'blog_top_margin', 'slider|0|120| px|2', 'margin at the top of site' );
ppO( 'blog_btm_margin', 'slider|0|120| px|2', 'margin at the bottom of site' );
ppStopMultiple();


// blog width & content margin
ppStartMultiple( 'Site width and content left/right margins' );
ppO( 'blog_width', 'slider|600|1600| pixels', 'Overall width of site, not counting border or dropshadow. <em>Note: Use arrow keys for precise control</em>' );
ppO( 'content_margin', 'slider|0|150| pixels', 'Content margins: left/right spacing between site edges and content (post text, images, etc.)' );
ppStopMultiple();


// blog border
ppStartMultiple( 'Site border style' );
ppO( 'blog_border', 'radio|border|custom border|dropshadow|dropshadow|none|no border or dropshadow', 'optional border or dropshadow on the outside of your entire site' );
ppO( 'blog_border_visible_sides', 'radio|left_and_right_only|left and right only|all_four_sides|all four sides of site', 'show border/dropshadow just on left and right, or all four sides' );
ppO( 'blog_border_shadow_width', 'radio|narrow|narrow|wide|wide', 'set the width of the dropshadow effect' );
ppBorderGroup( array( 'key' => 'blog_border', 'comment' => 'custom border appearance' ) );
ppStopMultiple();


if ( 0 ) {
	// prophoto classic bar
	ppStartMultiple( 'ProPhoto classic colored bar' );
	ppO( 'prophoto_classic_bar', 'radio|on|add colored bar|off|do not add colored bar', 'add a solid colored bar on the top of the blog like the original ProPhoto theme design' );
	ppO( 'prophoto_classic_bar_height', 'text|3', 'height (in pixels) of ProPhoto Classic colored bar' );
	ppO( 'prophoto_classic_bar_color', 'color', 'color of ProPhoto Classic colored bar' );
	ppStopMultiple();
}


// splitters
ppStartMultiple( 'Site section separation <span>splitting these areas causes site outer background color &amp; image to be seen between sections</span>' );
$sidebar_note = ( ppWidgetUtil::areaHasWidgets( 'fixed-sidebar' ) ) ? ' <em>(where no fixed sidebar shown)</em>' : ''; 
ppO( 'splitter_note', 'note', '<b>Important:</b> if you are separating any of your site sections using the below options, you should set the "Site border style" above to "no border or dropshadow" as any border or dropshadow will not wrap around the separated sections of your site.' );
ppO( 'logo_top_splitter', 'slider|0|120| px', 'separation above logo section' );
ppO( 'logo_btm_splitter', 'slider|0|120| px', 'separation below logo section' );
ppO( 'blank', 'blank' );
ppO( 'masthead_top_splitter', 'slider|0|120| px', 'separation above masthead section' );
ppO( 'masthead_btm_splitter', 'slider|0|120| px', 'separation below masthead section' );
ppO( 'blank', 'blank' );
ppO( 'menu_top_splitter', 'slider|0|120| px', 'separation above primary menu' );
ppO( 'menu_btm_splitter', 'slider|0|120| px', 'separation below primary menu' );
ppO( 'blank', 'blank' );
ppO( 'bio_top_splitter', 'slider|0|120| px', 'separation above bio section' );
ppO( 'bio_btm_splitter', 'slider|0|120| px', 'separation below bio section' );
ppO( 'blank', 'blank' );
ppO( 'post_splitter', 'slider|0|120| px', 'separation between posts' . $sidebar_note );
ppO( 'archive_post_splitter', 'slider|0|120| px', 'separation between posts on archive page types' . $sidebar_note );



ppStopMultiple();

?>