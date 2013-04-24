<?php
/* ----------------------- */
/* ---POST AREA OPTIONS--- */
/* ----------------------- */



/* -----option required javascript/css----- */

echo <<<HTML
<script>
jQuery(document).ready(function($){
	ppOption.valToClass( 'post_header_align' );
	$('label[for="p4-input-postdate_placement-withtitle"]').addClass('option-withtitle');
	ppCustomDateformatDisplay();
	// show custom date format field when appropriate
	$('#p4-input-dateformat').change(function(){
		ppCustomDateformatDisplay();
	});
	$('#postdate_display-individual-option').change(function(){
		ppCustomDateformatDisplay();
	});
	// category links prelude
	$('#p4-input-category_list_prepend').blur(function(){
		var category_list_prepend = $(this).val();
		$('.cat-links').html(category_list_prepend);
	});
});
function ppCustomDateformatDisplay() {
	var postdate = jQuery('#postdate_display-individual-option input:checked').val();
	var choice = jQuery('#p4-input-dateformat option:selected').val();
	if ( postdate == 'normal' && choice == 'custom') {
		jQuery('#dateformat_custom-individual-option').show();
	} else {
		jQuery('#dateformat_custom-individual-option').hide();
	}
}
</script>
<style>
	.post_header_align-center #p4-input-postdate_placement-withtitle,
	.post_header_align-center .option-withtitle {
		display:none;
	}
</style>
HTML;




/* -----begin printing options------ */

$subgroups =  array(
	'background' => 'Background',
	'header'     => 'Post Header',
	'content'    => 'Text &amp; Images',
	'footerz'    => 'Post Footer',
	'cta'        => 'Call to Action',
	'meta'	     => 'Category &amp; Tag Lists',
	'excerpts'   => 'Excerpts',
	'archive'    => 'Archive Pages',
);


ppSubgroupTabs( $subgroups );
ppOptionHeader('Content Options', 'content' );



/* header subgroup */
ppOptionSubgroup( 'background' );

// content area bg
ppUploadBox::renderBg( 'body_bg', 'Content area background <span>color and background image of all your blog\'s content areas</span>', 'Background image will tile and fill <strong>all</strong> of your content areas', NO_IMG_OPTIONS );

// individual post bg
ppUploadBox::renderBg( 'post_bg', 'Individual post area background', 'Background image will be applied separately to each <strong>post</strong>' );

// individual page bg
ppUploadBox::renderBg( 'page_bg', 'Individual page area background', 'Background image will be applied separately to each <strong>page</strong>' );

ppEndOptionSubgroup();



/* header subgroup */
ppOptionSubgroup( 'header' );

// header alignment and margin below
ppStartMultiple( 'Post header alignment & spacing below' );
ppO( 'post_header_align', 'radio|left|left aligned|center|centered|right|right aligned', 'overall aligment of post/page headers' );
ppO( 'post_header_margin_above', 'slider|0|80| px', 'spacing above header' );
ppO( 'post_header_margin_below', 'slider|0|80| px', 'spacing below header (between header and beginning of content)' );
ppStopMultiple();

// post titles
ppFontGroup( array(
	'key' => 'post_title_link',
	'title' => 'Post title appearance',
	'add' => array( 'margin_bottom', 'letterspacing' ),
	'inherit' => 'all',
) );

// post date/time options
ppStartMultiple( 'Post date/time' );

// controlling option
ppO( 'postdate_display', 'radio|normal|normal|boxy|boxy style|off|do not show post date', 'post date display style', 'Post date/time' );

// boxy date options
ppO( 'boxy_date_font_size', 'slider|10|25| px', 'relative font size' );
ppO( 'boxy_date_bg_color', 'color|optional', 'background color' );
ppO( 'boxy_date_align', 'radio|left|left|right|right', 'alignment' );
ppO( 'boxy_date_month_color', 'color', 'month text color' );
ppO( 'boxy_date_day_color', 'color', 'day text color' );
ppO( 'boxy_date_year_color', 'color', 'year text color' );
ppO( 'blank', 'blank' );
ppO( 'blank', 'blank' );



// normal date options
if ( ppOpt::test( 'post_header_align', 'center' ) && ppOpt::test( 'postdate_placement', 'withtitle' )  ) {
	ppOpt::update( 'postdate_placement', 'below' );
}
ppO( 'postdate_placement', 'radio|above|above post title|below|below post title|withtitle|same line with post title', 'Post date/time placement' );
ppO( 'show_post_published_time', 'radio|yes|yes|no|no', 'also display time of day posted?' );
ppO( 'dateformat', 'select|l, F j, Y|Monday, February 23, 2009|D. F j, Y|Mon. February 23, 2009|F j, Y|February 23, 2009|m-d-Y|02-23-2009|m-d-y|02-23-09|m*d*Y|02*23*2009|m*d*y|02*23*09|m.d.Y|02.23.2009|m.d.y|02.23.09|custom|custom...', 'Choose your date display format for your posts' );
ppO( 'dateformat_custom', 'text|14', 'enter custom <a href="http://codex.wordpress.org/Formatting_Date_and_Time">PHP-syntax date format</a> here' );
ppStopMultiple();

// date/time
ppFontGroup( array(
	'title' => 'Post header date/time font appearance',
	'key' => 'post_header_postdate',
	'inherit' => 'all',
	'add' => array( 'margin_bottom', 'letterspacing' ),
	'preview' => '<span>' . date( ppOpt::id( 'dateformat' ) ) . '</span>',
) );

// post date advanced
ppStartMultiple( 'Post date advanced features' );
ppO( 'postdate_advanced_switch', 'radio|on|use advanced post date features|off|do not use advanced features' );
ppO( 'postdate_lr_padding', 'slider|0|30| px', 'background spacing on left & right side of post date' );
ppO( 'postdate_tb_padding', 'slider|0|30| px', 'background spacing on top & bottom side of post date' );
ppO( 'postdate_bg_color', 'color|optional', 'background color of post date area' );
ppO( 'postdate_border_sides', 'checkbox|postdate_border_top|on|top border|postdate_border_bottom|on|bottom border|postdate_border_left|on|left border|postdate_border_right|on|right border', 'where to show post date border' );
ppBorderGroup( array( 'key' => 'postdate_border', 'comment' => 'postdate border appearance' ) );
ppStopMultiple();

// post meta
ppO( 'post_title_below_meta', 'checkbox|categories_in_post_header|yes|include category list|tags_in_post_header|yes|include tags list|comment_count_in_post_header|yes|include comment count', 'include which items in line of info below post title', 'Post info below post title' );


// post header meta
ppFontGroup( array(
	'title' => 'Post info items appearance',
	'key' => 'post_header_meta_link',
	'comment' => 'overall font and link appearance for post info: <strong>date, categories, tags, and comment count</strong>',
	'inherit' => 'all',
	'add' => array( 'nonlink_color' ),
) );


// post header line below
ppStartMultiple( 'Line below post header' );
ppO( 'post_header_border', 'radio|off|no line|on|add a line|image|upload an image', 'use a line below the post header to separate it from the post body content' );
ppBorderGroup( array( 'key' => 'post_header_border', 'comment' => 'appearance of line below post header' ) );
ppO( 'post_header_border_margin', 'text|3', 'spacing (in pixels) between line and beginning of post' );
ppStopMultiple();

// post header sep image
ppUploadBox::renderImg( 'post_header_separator', 'Post header separator image', 'decorative image between your post/page headers and content' );

ppEndOptionSubgroup();


/* content (text and images) subgroup */
ppOptionSubgroup( 'content' );

// post text
ppFontGroup( array(
	'key' => 'post_text',
	'title' => 'Post text appearance',
	'inherit' => 'all',
	'add' => array( 'lineheight', 'margin_bottom' ),
	'margin_bottom_comment' => 'paragraphs',
) );

// post text links
ppFontGroup( array(
	'key' => 'post_text_link',
	'title' => 'Post text link appearance',
	'inherit' => 'all',
) );

// pictures
ppStartMultiple( 'Post picture appearance' );
ppO( 'post_pic_margin_top', 'slider|0|50| px', 'margin (in pixels) above posted pictures' );
ppO( 'post_pic_margin_bottom', 'slider|0|50| px', 'margin below posted pictures' );
ppBorderGroup( array( 'key' => 'post_pic_border', 'comment' => 'post picture border appearance', 'minwidth' => '0' ) );
ppStopMultiple();

// post picture dropshadow
ppStartMultiple( 'Post picture dropshadow<span>(supported browsers only)</span>' );
ppO( 'post_pic_shadow_enable', 'radio|true|enable picture dropshadows|false|disable picture dropshadows', 'dropshadows for post images - only works in browsers that support dropshadows <em>(not Internet Explorer)</em>' );
ppO( 'post_pic_shadow_color', 'color', 'dropshadow color' );
ppO( 'post_pic_shadow_blur', 'slider|0|20| px', 'dropshadow blur radius' );
ppO( 'post_pic_shadow_vertical_offset', 'slider|-20|20| px', 'dropshadow vertical offset' );
ppO( 'post_pic_shadow_horizontal_offset', 'slider|-20|20| px', 'dropshadow horizontal offset' );
ppStopMultiple();


ppStartMultiple( 'Lazyloading of images' );
ppO( 'lazyload_imgs', 'radio|true|lazyload images for faster page load times|false|do not lazyload images', '', 'Lazyload' );
ppO( 'lazyload_loading', 'image', 'throbber image seen if delay loading image'  );
ppO( 'lazyload_loading_opacity', 'slider', 'opacity of throbber image' );
ppStopMultiple();

// image protection
ppStartMultiple( 'Post image theft protection' );
ppO( 'image_protection', 'radio|none|none|right_click|disable right-click|clicks|disable left and right clicks|replace|disable clicks and dragging|watermark|no clicks/drag, add watermark', 'image protection' );
ppO( 'pinterest_prevent_pins', 'radio|false|allow pinning|true|prevent pinning', 'allow or disallow "pinning" of your sites images on Pinterest' );
ppO( 'watermark_position', 'select|top left|top left|top center|top center|top right|top right|middle left|middle left|middle center|middle center|middle right|middle right|bottom left|bottom left|bottom center|bottom center|bottom right|bottom right', 'position of watermark overlay' );
ppO( 'watermark_alpha', 'slider', 'opacity of watermark image' );
ppO( 'watermark_size_threshold', 'slider|100|1200| pixels|10', 'watermark only if image height <em>plus</em> width is <b>more</b> than this amount' );
ppO( 'watermark_startdate', 'text|10', 'do not watermark images from posts before date (format: YYYY-MM-DD)' );

ppStopMultiple();

// watermark img
ppUploadBox::renderImg('watermark', 'Post image watermark overlay' );

ppEndOptionSubgroup();



/* excerpts subgroup */
ppOptionSubgroup( 'excerpts');

// exerpts, where?
ppStartMultiple( 'Excerpts' );
ppO( 'excerpts', 'checkbox|excerpts_on_home|true|Home pages|excerpts_on_archive|true|Date archive pages|excerpts_on_category|true|Category archive pages|excerpts_on_tag|true|Tag archive pages|excerpts_on_author|true|Author archive pages|excerpts_on_search|true|Search results pages', 'show <em>excerpts instead of full post content</em> on certain types of pages' );
ppO( 'read_more_link_text', 'text|15', 'Text used as link to full post where excerpts chosen' );
ppStopMultiple();

ppO( 'excerpt_style', 'radio|standard|standard|grid|image grid', '', 'Excerpt style' );

// grid
ppStartMultiple( 'Grid-style excerpt options' );
ppO( 'excerpt_grid_cols', 'slider|2|12| columns', 'number of grid columns' );
ppO( 'excerpt_grid_rows', 'slider|2|12| rows', 'number of grid rows' );
ppO( 'excerpt_grid_style', 'radio|img_text_below|text below|img_rollover_text|overlaid text on rollover', 'style of grid excerpt display' );
ppStopMultiple();



// excerpt images
ppStartMultiple( 'Standard-style excerpt options' );
ppO( 'show_excerpt_image', 'radio|true|show image|false|do not show image', 'show one image in each excerpt' );
ppO( 'dig_for_excerpt_image', 'radio|true|always try to include an excerpt image|false|only show designated featured image' );
ppO( 'excerpt_image_size', 'radio|thumbnail|small thumbnail|medium|medium thumbnail|fullsize|fullsize', 'size of image shown in excerpt' );
ppO( 'excerpt_image_position', 'radio|before_text|before text|after_text|after text', 'placement of excerpt image' );
ppStopMultiple();

ppEndOptionSubgroup();




/* footer subgroup */
ppOptionSubgroup( 'footerz' );

// facebook "like" button
ppStartMultiple( 'Facebook "Like" button integration' );
ppO( 'like_btn_enable', 'radio|true|enable Like button|false|disable Like button' );
ppO( 'like_btn_layout', ppUtil::selectParams( array(
	'standard' => 'standard',
	'standard_with_faces' => 'standard, with profile pics',
	'button_count' => 'like count on right',
	'box_count' => 'Like count above',
) ), 'like button layout' );
ppO( 'like_btn_placement', 'checkbox|like_btn_on_home|true|on blog home page|like_btn_on_single|true|on individual post pages|like_btn_on_page|true|on WordPress static "Pages"', 'where to show the Like button' );
ppO( 'like_btn_with_send_btn', 'radio|true|include send button|false|no send button', 'optionally add a "Send" button next to the Like button, allowing users to send links to Facebook friends' );
ppO( 'like_btn_margin_top', 'slider|0|60|px', 'spacing above Like button' );
ppO( 'like_btn_margin_btm', 'slider|0|60|px', 'spacing below Like button (if mini-profile pics set to be shown there will be extra empty space below if no pics displayed)' );
ppO( 'like_btn_filter_priority', 'text|5', 'filter priority (adjust number to affect interaction with post signature & plugins)' );
ppStopMultiple();

// post footer meta
ppO( 'post_footer_meta', 'checkbox|categories_in_post_footer|yes|include category list|tags_in_post_footer|yes|include tags list', 'include which items in line of info below post', 'Post info below post content' );

// post footer meta font/links
ppFontGroup( array(
	'title' => 'Post info below post content font/link appearance',
	'key' => 'post_footer_meta_link',
	'inherit' => 'all',
	'add' => array( 'nonlink_color' ),
) );



// nav previous/next
ppStartMultiple( 'Older/Newer posts navigation links' );
  ppO( 'paginate_post_navigation', 'radio|true|numbered links to older/newer pages|false|two links to just older &amp; newer' );
  ppO( 'older_posts_link_text', 'text|18', 'text for "Older posts" links on paginated pages' );
  ppO( 'newer_posts_link_text', 'text|18', 'text for "Newer posts" links on paginated pages' );
  ppO( 'older_newer_link_blank', 'blank' );
  ppO( 'older_posts_link_align', 'radio|left|left-aligned|right|right-aligned', 'alignment of "Older posts" link' );
  ppO( 'newer_posts_link_align', 'radio|left|left-aligned|right|right-aligned', 'alignment of "Newer posts" links' );
  ppO( 'pagination_prev_text', 'text', 'text shown for previous link, before numbers' );
  ppO( 'pagination_next_text', 'text', 'text shown for next link, after numbers' );
  ppO( 'max_paginated_links', 'slider|6|60| |3', 'max number of numbered links shown' );
ppStopMultiple();

// nav below links
ppFontGroup( array(
	'title' => 'Font and link options for "Newer/Older posts" links text',
	'key' => 'nav_below_link',
	'inherit' => 'all',
	'preview' => '<a href="" style="float:left;">' . ppOpt::id( 'older_posts_link_text' ) . '</a><a href="" style="float:right;">' . ppOpt::id( 'newer_posts_link_text' ) . '</a>'
) );

//post divider
ppStartMultiple( 'Post separator' );
ppO( 'post_divider', 'radio|line|use a line to separate posts|none|no line or image separating posts|image|uploaded image to separate posts', 'which type of separator?' );
ppO( 'padding_below_post', 'slider|0|120| px', 'spacing below each post' );
ppBorderGroup( array( 'key' => 'post_sep_border', 'comment' => 'line between posts appearance' ) );
ppStopMultiple();

// post sep image
ppUploadBox::renderImgWithOption( array( 'post_sep', 'Post separator image', 'Upload a custom image to separate your posts' ), array( 'post_sep_align', 'radio|left|left|center|center|right|right', 'separator image alignment' ) );

ppEndOptionSubgroup();


/* meta subgroup */
ppOptionSubgroup( 'meta' );


// category links list display options
ppStartMultiple( 'Category links list display options' );
ppO( 'category_list_divider', 'text|5', 'When multiple categories, what should divide the category links? Default is ", "' );
ppO( 'category_list_prepend', 'text', 'text to be included before category list' );
ppStopMultiple();


// tags
ppStartMultiple( 'Tag options' );
ppO( 'tag_list_prepend', 'text|20', 'text shown before list of tags' );
ppO( 'tag_list_divider', 'text|4', 'text used to separate multiple tags' );
ppO( 'tags_where_shown', 'checkbox|tags_on_home|yes|home page|tags_on_single|yes|individual post pages|tags_on_archive|yes|archive, category, author, and search|tags_on_tags|yes|tag archives', 'display tags below posts on which types of pages?' );
ppStopMultiple();

ppEndOptionSubgroup();



/* content other_pages subgroup */
ppOptionSubgroup( 'archive' );

// header styling
ppFontGroup( array(
	'title' => 'Archive pages headline styling',
	'key' => 'archive_h2',
	'add' => array( 'margin_bottom' ),
	'comment' => 'examples of archive page headlines include on category archive pages where it says <em>Category Archives: Weddings</em>, or on monthly archive pages where it says <em>Monthly Archives: October 2009</em>',
) );

// archive post divider
ppStartMultiple( 'Archive pages post separator' );
ppO( 'archive_post_divider', 'radio|same|same as on home pages|line|use a custom line', 'use the same visual separator between posts on home page, or override with a custom line', 'first' );
ppO( 'archive_padding_below_post', 'slider|0|120| px', 'First extra space (in pixels) below post.  This is the space between the bottom of the post and the line' );
ppBorderGroup( array( 'key' => 'archive_post_sep_border', 'comment' => 'line between archive posts appearance' ) );
ppStopMultiple();

ppEndOptionSubgroup();



/* content other_pages subgroup */
echo <<<HTML
<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function($){
		$('div[id^="upload-box-call_to_action_"]').not('.mini-img-wrap').each(function(){
			var box        = $(this),
			    origClass  = 'call-to-action-item ' + box.attr('class'),
				setClasses = function(){
					box.attr('class',origClass)
						.addClass($('select:first',box).val())
						.addClass('display-'+$('select',box).eq(1).val());
				};
			$('select',box).change(function(){
				setClasses();
			});
			setClasses();
		});
	});
</script>
HTML;

ppOptionSubgroup( 'cta' );

	ppStartMultiple( '"Call to Action" buttons' );
		ppO( 'call_to_action_enable', 'radio|false|disable|true|enable', 'enable/disable "Call to Action" button display' );
		ppO( 'call_to_action_location', 'radio|above_comments|above comments|below_comments|below comments', 'where below post/page to show buttons' );
		ppO( 'call_to_action_placement', ppUtil::checkboxParams( 'true', array(
			'call_to_action_on_home'   => 'on blog posts page',
			'call_to_action_on_single' => 'on individual post pages',
			'call_to_action_on_page'   => 'on WordPress static "Pages"',
		) ), 'where to show the "Call to Action" buttons' );
		ppO( 'call_to_action_items_align', 'radio|left|left|center|center|right|right', 'alignment of buttons' );
		ppO( 'call_to_action_items_lr_spacing', 'slider|0|80| px', 'horizontal spacing between items' );
		ppO( 'call_to_action_items_tb_spacing', 'slider|0|80| px', 'vertical spacing between items' );
		ppO( 'call_to_action_area_top_padding', 'slider|0|80| px', 'extra spacing above button area' );
		ppO( 'call_to_action_area_btm_padding', 'slider|0|80| px', 'extra spacing below button area' );
	ppStopMultiple();

	ppFontGroup( array(
		'title'   => '"Call to Action" button link and separator text',
		'key'     => 'call_to_action_link',
		'inherit' => 'all',
		'add'     => array( 'nonlink_color' ),
	) );

	ppStartMultiple( 'Call to Action buttons separator' );
		ppO( 'call_to_action_separator', 'radio|off|no separator|image|image separator|text|text separator', 'visual separator between buttons' );
		ppO( 'call_to_action_separator_text', 'text|5', 'text for separator' );
		ppO( 'call_to_action_separator_img', 'image', 'image separator');
	ppStopMultiple();


	for ( $i = 1; $i <= pp::num()->maxCallToActionItems; $i++ ) {
		$callToAction = new ppUploadBox_Img_CallToAction( $i );
		$callToAction->render();
	}

ppEndOptionSubgroup();
