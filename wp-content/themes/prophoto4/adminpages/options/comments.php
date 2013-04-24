<?php
/* ----------------------- */
/* ----COMMENTS OPTIONS--- */
/* ----------------------- */



// HTML;

// CSS and JS for complicated "post interact" links interface
ppPostInteractionInterface();


// tabs and header
ppSubgroupTabs( array(
	'general' => 'General',
	'header' => 'Comments header',
	'body' => 'Comment options' )
);
ppOptionHeader('Comments', 'comments' );


/* options subgroup */
ppOptionSubgroup( 'general' );

// comments layout & display
ppStartMultiple( 'Comments layout & display' );
echo NrHtml::script( 'jQuery(document).ready(function($){ ppOption.valToClass("comments_enable")});' );
ppO( 'comments_enable', 'radio|true|enable comments|false|disable &amp; hide all comments', 'disable if you don\'t want any comments anywhere on your blog' );
ppO( 'comments_layout', 'radio|tabbed|Tabbed layout|boxy|Boxy layout|minima|Flexible', 'overall comment layout appearance' );
ppO( 'comments_on_home_start_hidden', 'radio|true|Comments are hidden by default|false|Comments are open by default', 'comments area open or hidden by default' );
ppStopMultiple();

/* Facebook Comments */
$home = pp::site()->hasStaticFrontPage ? '<em>blog posts page</em>' : 'site home page';
echo NrHtml::script( 'jQuery(document).ready(function($){
	ppOption.valToClass("fb_comments_also_show_unique_wp");
	ppOption.valToClass("fb_comments_enable");
});' );
ppStartMultiple( 'Facebook comments' );
if ( !ppOpt::test( 'facebook_admins' ) ) {
	ppO( 'fb_comments_enable', 'note', ppString::id( 'fb_comments_requires_fb_admins' ) );
} else {
	ppO( 'fb_comments_enable', 'radio|false|disable Facebook comments|true|enable Facebook comments' );
	ppO( 'fb_comments_also_show_unique_wp', 'radio|false|use only Facebook comments|true|also show any WordPress comments' );
	ppO( 'fb_comments_add_new', ppUtil::radioParams( array(
		'fb_only'   => 'only thru Facebook',
		'fb_and_wp' => 'thru Facebook or traditionally',
	) ), 'how to accept new comments' );
	ppO( 'fb_comments_num_shown_nonsingle', 'slider|0|50| comments', "number FB comments to show per post on $home - if <code>0</code>, FB comments area will not be shown" );
	ppO( 'fb_comments_num_shown_single', 'slider|1|100| comments', "number FB comments to show by default on posts and pages" );
	ppO( 'fb_comments_colorscheme', 'radio|light|light|dark|dark', 'FB comments color scheme' );
}
ppStopMultiple();

// comments on template pages
ppStartMultiple( 'Archive pages comments' );
ppO( 'comments_show_on_archive', 'radio|true|include comments|false|no comments (recommended)', 'include comments on archive-type pages' );
ppO( 'comments_on_archive_start_hidden', 'radio|false|comments visible by default|true|comments not visible by default', 'comments on other pages visible or hidden by default' );
ppStopMultiple();

// scrollbox
ppStartMultiple( 'Comments area scrollbox height' );
ppO( 'comments_scrollbox_height', 'slider|50|600| px|5', 'height of comment area scrollbox' );
ppO( 'comments_in_scrollbox_on_home', 'radio|true|comments in fixed-height scrollbox|false|no scrollbox - show all comments', 'comment display on home pages' );
ppO( 'comments_in_scrollbox_on_singular', 'radio|true|comments in fixed-height scrollbox|false|no scrollbox - show all comments', 'comment display on single post pages' );
ppStopMultiple();


// comments overall left/right margin
ppStartMultiple( 'Overall comment area spacing' );
ppO( 'comments_area_lr_margin_control', 'radio|inherit|inherit from post content|set|set pixel margin', 'spacing between comments area and the left and right edges of blog' );
ppO( 'comments_area_lr_margin', 'slider|0|100| px', 'set specific spacing amount' );
ppStopMultiple();

// bg colors
ppStartMultiple( 'Comments area background colors' );
ppO( 'comments_area_bg_color', 'color|optional', 'background color of the entire comments area - (header and comments)' );
ppO( 'comment_bg_color', 'color|optional', 'background color of individual comments', 'Comment background' );
ppStopMultiple();

// boxy comments line color
ppStartMultiple( 'Comments area borders' );
ppBorderGroup( array( 'key' => 'comments_area_border', 'comment' => 'appearance of overall comment area borders', 'minwidth' => '0' ) );
ppO( 'comments_header_border_lines', 'checkbox|comment_header_border_top|on|show border above comments header|comment_header_border_bottom|on|show border below comments header', 'where to show borders' );
ppStopMultiple();

// avatars
ppStartMultiple( 'Comment avatars' );
ppO( 'comments_show_avatars', 'radio|true|show comment avatars|false|do not show comment avatars' );
ppO( 'comment_avatar_size', 'text|4', 'display size (in pixels) of avatars' );
ppO( 'comment_avatar_align', 'radio|left|left|right|right', 'alignment of avatar in comment' );
ppO( 'comment_avatar_padding', 'text|3', 'spacing (in pixels) on the sides and below avatar' );
ppStopMultiple();

// reverse comments (SHOW - ALL)
ppO('reverse_comments', 'radio|false|oldest comments on top|true|newest comments on top', 'sort your comments, most recent comment on the bottom of the list, or on top', 'Sort comments' );

// disable ajax comments
ppO( 'comments_ajax_adding_enabled', 'radio|true|enable ajax|false|disable ajax', 'set to disable for compatibility with poorly coded plugins', 'Ajax comment submission' );

ppEndOptionSubgroup();



/* comments header subgroup */
ppOptionSubgroup( 'header' );

// comments header bg area
ppUploadBox::renderBg( 'comments_header_bg', 'Comments header area background' );


// comment header gen options
ppStartMultiple( 'Comment header options' );
ppO( 'comments_header_show_article_author', 'radio|true|show post author|false|do not show post author', 'include or remove the post author from the comments header' );
ppO( 'comments_header_lr_padding', 'slider|0|100| px', 'spacing between text/links in comments header and edges of blog' );
ppO( 'comments_header_tb_padding', 'text|3', 'override spacing above/below comments header (in pixels)' );

ppStopMultiple();

// post author, comments count, and show/hide link options (SHOW - ???)
ppFontGroup( array(
	'title' => 'Post author and show/hide comments appearance',
	'key' => 'comments_header_link',
	'inherit' => 'all',
	'add' => array( 'nonlink_color' ),
	'not' => array( 'visited_color' ),
) );

// force remove outdated option
echo '<input type="hidden" value="" name="p_comments_header_link_visited_font_color_bind" id="p4-input-comments_header_link_visited_font_color-bind" />';

// post interaction font
ppFontGroup( array(
	'title' => 'Post interaction link appearance',
	'key' => 'comments_header_post_interaction_link',
	'inherit' => 'all',
) );

// minima show/hide text
ppStartMultiple( 'Show/hide comments link display' );
ppO( 'comments_show_hide_method', 'radio|button|use button|text|use text', 'Select a button or text interface for showing/hiding your comments' );
ppO( 'comments_minima_show_text', 'text|15', '"show" text' );
ppO( 'comments_minima_hide_text', 'text|15', '"hide" text' );
ppStopMultiple();

// post interaction display
ppStartMultiple( 'Post interaction links options' );
ppO( 'comments_post_interact_display', 'radio|text|text &amp; optional icons|button|text &amp; optional icons in P4 buttons|images|use custom images', 'Select the display type for your post interaction links' );
ppO( 'comments_header_post_interact_link_spacing', 'slider|0|50| px', 'spacing between post interaction links' );
ppStopMultiple();

// add a comment link
ppStartMultiple( 'Add a comment link' );
ppO( 'comments_header_addacomment_link_text', 'text|29', 'text used for the "add a comment" link' );
ppO( 'comments_header_addacomment_link_icon', 'image', 'optional "Add a comment" icon' );
ppO( 'comments_header_addacomment_link_image', 'image', 'custom "Add a comment" image' );
ppStopMultiple();

// link to this post
ppStartMultiple( '"Link to this Post" link' );
ppO( 'comments_header_linktothispost_link_include', 'radio|yes|include|no|do not include', 'include or remove "link to this post" permalink option' );
ppO( 'comments_header_linktothispost_link_text', 'text|29', 'text used for the "link to this post" permalink link' );
ppO( 'comments_header_linktothispost_link_icon', 'image', 'optional "Link to this post" icon' );
ppO( 'comments_header_linktothispost_link_image', 'image', 'Custom "Link to this post" image' );
ppStopMultiple();

// email a friend link
ppStartMultiple( '"Email a friend" link' );
ppO( 'comments_header_emailafriend_link_include', 'radio|yes|include|no|do not include', 'include or remove "email a friend" links in comment header' );
ppO( 'comments_header_emailafriend_link_text', 'text|29', 'text used for the "email a friend" links' );
ppO( 'comments_header_emailafriend_link_body', 'text|31', 'default text for the body of the generated email' );
ppO( 'comments_header_emailafriend_link_subject', 'text|31', 'default text for the subject line of the generated email' );
ppO( 'comments_header_emailafriend_link_icon', 'image', 'custom "Email a friend" icon' );
ppO( 'comments_header_emailafriend_link_image', 'image','Custom "Email a friend" image' );
ppStopMultiple();

ppEndOptionSubgroup();



/* options subgroup */
ppOptionSubgroup( 'body' );

// comments area background
ppUploadBox::renderBg( 'comments_body_area_bg', 'Comments area (not comment header) background' );

// comment link author display
ppStartMultiple( 'Comment author link options' );
ppO( 'comment_meta_position', 'radio|inline|comment author inline with comment|above|comment author on it\'s own line', 'how to display the comment author' );
ppO('comment_author_link_target', 'radio|_self|open in same window|_blank|open in new window', 'links to comment author websites open in same window or in a new window' );
// comment author
ppFontGroup( array(
	'title' => 'Comment author appearance',
	'key' => 'comment_author_link',
	'inherit' => 'all',
	'not' => array( 'size', 'family', 'hover_color', 'visited_color' ),
	'comment' => 'comment author link appearance',
) );
ppO( 'comment_meta_margin_bottom', 'slider|0|20| px', 'spacing below comment author line' );

ppStopMultiple();

// comment time
ppStartMultiple( 'Comment time display' );
ppO( 'comment_timestamp_display', 'radio|right|right-aligned|left|left-aligned|off|do not show comment time', 'if/where to show the time of the comment' );
ppFontGroup( array(
	'title' => 'Comment date/time appearance',
	'key' => 'comment_timestamp',
	'not' => array( 'size', 'family', 'hover_color', 'visited_color' ),
	'comment' => 'text appearance of comment time',
	'preview' => date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ),
) );
ppStopMultiple();

// comment font
ppFontGroup( array(
	'title' => 'Comment font appearance',
	'key' => 'comment_text_and_link',
	'inherit' => 'all',
	'add' => array( 'lineheight', 'nonlink_color' ),
) );

// flex comment side margins (SHOW - MINIMA, )
ppStartMultiple( 'Comment body margins' );
ppO( 'comment_tb_padding', 'slider|0|40| pixels', 'vertical padding above and below comment text' );
ppO( 'comment_lr_padding', 'slider|0|40| pixels', 'horizontal padding left and right of comment text' );
ppO( 'comments_body_area_lr_margin', 'slider|0|150| pixels', 'space between comments and left/right edge of comment area' );
ppO( 'comments_body_area_tb_margin', 'slider|0|150| pixels', 'space between comments and top/bottom edge of comment area' );
ppO( 'comment_tb_margin', 'slider|0|40| pixels', 'vertical spacing between comments' );
ppStopMultiple();

// comment separator line (SHOW - ALL)
ppStartMultiple( 'Optional line separating individual comments' );
ppO( 'comment_bottom_border_onoff', 'radio|on|add a line|off|no line', 'add and customize a line to separate each individual comment' );
ppBorderGroup( array( 'key' => 'comment_bottom_border', 'comment' => 'appearance of separating line' ) );
ppStopMultiple();


// alt comment styling (SHOW - ALL)
ppStartMultiple( 'Alternate comment styling' );
ppO( 'comment_alt_bg_color', 'color|optional', 'override the inherited <em>background</em> color of every other comment' );
ppO( 'comment_alt_font_color', 'color|optional', 'override the inherited comment <em>text</em> color on every other comment' );
ppO( 'comment_alt_link_font_color', 'color|optional', 'override the inherited color for <em>links inside of comments</em> on every other comment' );
ppO( 'comment_alt_author_link_font_color', 'color|optional', 'override the inherited text color of the comment <em>author\'s name</em> on every other comment' );
ppO( 'comment_alt_timestamp_font_color', 'color|optional', 'override the inherited text color of the comment <em>timestamp</em> on every other comment' );
ppStopMultiple();

// by author styling (SHOW - ALL)
ppStartMultiple( 'Alternate styling for comments by post author' );
ppO( 'comment_byauthor_bg_color', 'color|optional', 'override the <em>background</em> color for your own comments' );
ppO( 'comment_byauthor_font_color', 'color|optional', 'override the comment <em>text</em> color for your own comments' );
ppO( 'comment_byauthor_link_font_color', 'color|optional', 'override the color of <em>links in comments</em> on your own comments' );
ppO( 'comment_byauthor_author_link_font_color', 'color|optional', 'override the text color of <em>your own name</em> on your own comments' );
ppO( 'comment_byauthor_timestamp_font_color', 'color|optional', 'override the text color of the comment <em>timestamp</em> of your own comments' );
ppStopMultiple();

// awaiting moderation (SHOW - ALL)
ppStartMultiple( 'Comment awaiting moderation text/style' );
ppO( 'comment_awaiting_moderation_text', 'text|35', 'Text shown to comment submitter when comment moderation is turned on' );
ppO( 'comment_awaiting_moderation_font_style', 'select|normal|normal|italic|italic', 'style of message text' );
ppO( 'comment_awaiting_moderation_font_color', 'color|optional', 'override default color of message text' );
ppO( 'comment_alt_awaiting_moderation_font_color', 'color|optional', 'override default color of message text on every other comment' );
ppStopMultiple();


ppEndOptionSubgroup();



?>