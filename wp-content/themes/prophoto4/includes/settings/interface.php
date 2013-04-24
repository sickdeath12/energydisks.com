<?php
/* -------------------------------------------- */
/* -- settings for interface/user experience -- */
/* -------------------------------------------- */


/* this array holds interface dependency relationships data */
$p4_interface = array(

	// call to action
	'call_to_action_location'         => 'hide if call_to_action_enable false',
	'call_to_action_placement'        => 'hide if call_to_action_enable false',
	'call_to_action_separator'        => 'hide if call_to_action_enable false',
	'call_to_action_items_align'      => 'hide if call_to_action_enable false',
	'call_to_action_items_lr_spacing' => 'hide if call_to_action_enable false',
	'call_to_action_items_tb_spacing' => 'hide if call_to_action_enable false',
	'call_to_action_link_font_group'  => 'hide if call_to_action_enable false',
	'call_to_action_area_top_padding' => 'hide if call_to_action_enable false',
	'call_to_action_area_btm_padding' => 'hide if call_to_action_enable false',
	'call_to_action_1'                => 'hide if call_to_action_enable false',
	'call_to_action_2'                => 'hide if call_to_action_enable false',
	'call_to_action_3'                => 'hide if call_to_action_enable false',
	'call_to_action_4'                => 'hide if call_to_action_enable false',
	'call_to_action_5'                => 'hide if call_to_action_enable false',
	'call_to_action_6'                => 'hide if call_to_action_enable false',
	'call_to_action_7'                => 'hide if call_to_action_enable false',
	'call_to_action_8'                => 'hide if call_to_action_enable false',
	'call_to_action_separator_text'   => 'hide if call_to_action_separator off|image',
	'call_to_action_separator_img'    => 'hide if call_to_action_separator off|text',

	// excerpts
	'excerpt_grid_cols'     => 'hide if excerpt_style standard',
	'show_excerpt_image'    => 'hide if excerpt_style grid',

	// mobile
	'mobile_logo'               => 'hide if mobile_logo_use_desktop true',
	'mobile_ajax_links_enabled' => 'hide if mobile_enable false',

	// background
	'blog_border_group'           => 'hide if blog_border dropshadow|none',
	'blog_border_shadow_width'    => 'hide if blog_border border|none',
	'blog_border_visible_sides'   => 'hide if blog_border none',
	'prophoto_classic_bar_height' => 'hide if prophoto_classic_bar off',
	'prophoto_classic_bar_color'  => 'hide if prophoto_classic_bar off',
	'masthead_top_splitter'       => 'hide if masthead_display off',
	'masthead_btm_splitter'       => 'hide if masthead_display off',
	'bio_top_splitter'            => 'hide if bio_include no',
	'bio_btm_splitter'            => 'hide if bio_include no',
	'menu_top_splitter'           => 'hide if headerlayout pptclassic',
	'menu_btm_splitter'           => 'hide if headerlayout pptclassic',
	'logo_top_splitter'           => 'hide if headerlayout logomasthead_nav|mastlogohead_nav|mastheadlogo_nav',
	'logo_btm_splitter'           => 'hide if headerlayout logomasthead_nav|mastlogohead_nav|mastheadlogo_nav',

	// header
	'masthead_top_border_group'           => 'hide if masthead_top_border off',
	'masthead_btm_border_group'           => 'hide if masthead_btm_border off',
	'masthead_slideshow_hold_time'        => 'hide if masthead_display static|random|custom|off',
	'mobile_masthead_slideshow_hold_time' => 'hide if mobile_masthead_display static|random|custom|off',
	'logo_swf'                            => 'hide if logo_swf_switch off',

	// menu
	'primary_nav_menu_top_border_group'         => 'hide if primary_nav_menu_border_top_onoff off',
	'primary_nav_menu_btm_border_group'         => 'hide if primary_nav_menu_border_bottom_onoff off',
	'secondary_nav_menu_top_border_group'       => 'hide if secondary_nav_menu_border_top_onoff off',
	'secondary_nav_menu_btm_border_group'       => 'hide if secondary_nav_menu_border_bottom_onoff off',
	'primary_nav_menu_border_top_onoff'         => 'hide if headerlayout pptclassic',
	'primary_nav_menu_edge_padding'             => 'hide if primary_nav_menu_align center',
	'primary_nav_menu_align'                    => 'hide if headerlayout pptclassic',
	'primary_nav_menu_edge_padding'             => 'hide if headerlayout pptclassic',
	'primary_nav_menu_admin'                    => 'hide if primary_nav_menu_onoff off',
	'primary_nav_menu_align'                    => 'hide if primary_nav_menu_onoff off',
	'primary_nav_menu_bg_color'                 => 'hide if primary_nav_menu_onoff off',
	'primary_nav_menu_dropdown_bg_color'        => 'hide if primary_nav_menu_onoff off',
	'primary_nav_menu_link_font_group'          => 'hide if primary_nav_menu_onoff off',
	'primary_nav_menu_dropdown_link_textsize'   => 'hide if primary_nav_menu_onoff off',
	'primary_nav_menu_link_spacing_between'     => 'hide if primary_nav_menu_onoff off',
	'primary_nav_menu_border_top_onoff'         => 'hide if primary_nav_menu_onoff off',
	'primary_nav_menu_bg'                       => 'hide if primary_nav_menu_onoff off',
	'secondary_nav_menu_admin'                  => 'hide if secondary_nav_menu_onoff off',
	'secondary_nav_menu_align'                  => 'hide if secondary_nav_menu_onoff off',
	'secondary_nav_menu_bg_color'               => 'hide if secondary_nav_menu_onoff off',
	'secondary_nav_menu_dropdown_bg_color'      => 'hide if secondary_nav_menu_onoff off',
	'secondary_nav_menu_link_font_group'        => 'hide if secondary_nav_menu_onoff off',
	'secondary_nav_menu_dropdown_link_textsize' => 'hide if secondary_nav_menu_onoff off',
	'secondary_nav_menu_link_spacing_between'   => 'hide if secondary_nav_menu_onoff off',
	'secondary_nav_menu_border_top_onoff'       => 'hide if secondary_nav_menu_onoff off',
	'secondary_nav_menu_bg'                     => 'hide if secondary_nav_menu_onoff off',
	'secondary_nav_menu_placement'              => 'hide if secondary_nav_menu_onoff off',

	// contact
	'anti_spam_question_1'     => 'hide if contactform_antispam_enable off',
	'anti_spam_explanation'    => 'hide if contactform_antispam_enable off',
	'contact_btm_border_group' => 'hide if contact_btm_border off',

	// bio
	'bio_border_group'    => 'hide if bio_border image|noborder',
	'biopic_border'       => 'hide if biopic_display off',
	'bio_separator'       => 'hide if bio_border border|noborder',
	'biopic_border_group' => 'hide if biopic_border off',
	'biopic_align'        => 'hide if biopic_display off',
	'bio_pages_options'   => 'hide if use_hidden_bio yes',

	// content
	'newer_posts_link_text'             => 'hide if paginate_post_navigation true',
	'older_posts_link_text'             => 'hide if paginate_post_navigation true',
	'older_posts_link_align'            => 'hide if paginate_post_navigation true',
	'newer_posts_link_align'            => 'hide if paginate_post_navigation true',
	'older_newer_link_blank'            => 'hide if paginate_post_navigation true',
	'pagination_prev_text'              => 'hide if paginate_post_navigation false',
	'pagination_next_text'              => 'hide if paginate_post_navigation false',
	'max_paginated_links'               => 'hide if paginate_post_navigation false',
	'postdate_advanced_switch'          => 'hide if postdate_display boxy|off',
	'feed_thumbnail_type'               => 'hide if modify_feed_images false|remove',
	'modify_feed_images_alert'          => 'hide if modify_feed_images false',
	'watermark_position'                => 'hide if image_protection none|right_click|clicks|replace',
	'watermark_alpha'                   => 'hide if image_protection none|right_click|clicks|replace',
	'watermark_startdate'               => 'hide if image_protection none|right_click|clicks|replace',
	'watermark_size_threshold'          => 'hide if image_protection none|right_click|clicks|replace',
	'watermark'                         => 'hide if image_protection none|right_click|clicks|replace',
	'post_header_separator'             => 'hide if post_header_border on|off',
	'postdate_bg_color'                 => 'hide if postdate_advanced_switch off',
	'postdate_border_group'             => 'hide if postdate_advanced_switch off',
	'postdate_lr_padding'               => 'hide if postdate_advanced_switch off',
	'postdate_tb_padding'               => 'hide if postdate_advanced_switch off',
	'postdate_border_sides'             => 'hide if postdate_advanced_switch off',
	'dig_for_excerpt_image'             => 'hide if show_excerpt_image false',
	'excerpt_image_size'                => 'hide if show_excerpt_image false',
	'excerpt_image_position'            => 'hide if show_excerpt_image false',
	'post_header_postdate_font_group'   => 'hide if postdate_display boxy|off',
	'postdate_placement'                => 'hide if postdate_display boxy|off',
	'show_post_published_time'          => 'hide if postdate_display boxy|off',
	'dateformat'                        => 'hide if postdate_display boxy|off',
	'dateformat_custom'                 => 'hide if postdate_display boxy|off',
	'boxy_date_font_size'               => 'hide if postdate_display normal|off',
	'boxy_date_bg_color'                => 'hide if postdate_display normal|off',
	'boxy_date_align'                   => 'hide if postdate_display normal|off',
	'boxy_date_month_color'             => 'hide if postdate_display normal|off',
	'boxy_date_day_color'               => 'hide if postdate_display normal|off',
	'boxy_date_year_color'              => 'hide if postdate_display normal|off',
	'post_header_border_group'          => 'hide if post_header_border off|image',
	'post_header_border_margin'         => 'hide if post_header_border off|image',
	'post_sep_border_group'             => 'hide if post_divider none|image',
	'post_sep'                          => 'hide if post_divider line|none',
	'comments_on_archive_start_hidden'  => 'hide if comments_show_on_archive false',
	'archive_post_sep_border_group'     => 'hide if archive_post_divider same',
	'archive_padding_below_post'        => 'hide if archive_post_divider same',
	'like_btn_layout'                   => 'hide if like_btn_enable false',
	'like_btn_placement'                => 'hide if like_btn_enable false',
	'like_btn_show_advanced'            => 'hide if like_btn_enable false',
	'like_btn_filter_priority'          => 'hide if like_btn_enable false',
	'like_btn_margin_top'               => 'hide if like_btn_enable false',
	'like_btn_margin_btm'               => 'hide if like_btn_enable false',
	'like_btn_with_send_btn'            => 'hide if like_btn_enable false',
	'post_pic_shadow_vertical_offset'   => 'hide if post_pic_shadow_enable false',
	'post_pic_shadow_horizontal_offset' => 'hide if post_pic_shadow_enable false',
	'post_pic_shadow_blur'              => 'hide if post_pic_shadow_enable false',
	'post_pic_shadow_color'             => 'hide if post_pic_shadow_enable false',
	'dateformat_custom'                 => 'hide if dateformat long|medium|short|longabrvdash|shortabrvdash|longabrvast|shortabrvast|longdot|shortdot',
	'lazyload_loading'                  => 'hide if lazyload_imgs false',
	'lazyload_loading_opacity'          => 'hide if lazyload_imgs false',

	// comments
	'comments_header_tb_padding'                       => 'hide if comments_layout tabbed',
	'comments_scrollbox_height'                        => 'hide if comments_layout boxy',
	'comments_on_home_start_hidden'                    => 'hide if comments_layout boxy',
	'comments_post_interact_display'                   => 'hide if comments_layout boxy|tabbed',
	'comments_show_hide_method'                        => 'hide if comments_layout boxy|tabbed',
	'comments_header_addacomment_link_image'           => 'hide if comments_layout boxy|tabbed',
	'comments_header_bg'                               => 'hide if comments_layout tabbed',
	'comments_area_lr_margin'                          => 'hide if comments_area_lr_margin_control inherit',
	'comments_area_border_group'                       => 'hide if comments_layout tabbed',
	'comments_header_border_lines'                     => 'hide if comments_layout boxy',
	'comments_header_post_interaction_link_font_group' => 'hide if comments_post_interact_display images',
	'comments_minima_show_text'                        => 'hide if comments_show_hide_method button',
	'comments_minima_hide_text'                        => 'hide if comments_show_hide_method button',
	'comment_avatar_size'                              => 'hide if comments_show_avatars false',
	'comment_avatar_align'                             => 'hide if comments_show_avatars false',
	'comment_avatar_padding'                           => 'hide if comments_show_avatars false',
	'comment_timestamp_font_group'                     => 'hide if comment_timestamp_display off',
	'comments_header_bg_color'                         => 'hide if comments_layout tabbed',
	'comment_byauthor_timestamp_font_color'            => 'hide if comment_timestamp_display off',
	'comment_alt_timestamp_font_color'                 => 'hide if comment_timestamp_display off',
	'comments_header_lr_padding'                       => 'hide if comments_layout tabbed|boxy',
	'comment_meta_margin_bottom'                       => 'hide if comment_meta_position inline',
	'comment_bottom_border_group'                      => 'hide if comment_bottom_border_onoff off',

	// footer
	'footer_bg'                  => 'hide if footer_include no',
	'footer_left_padding'        => 'hide if footer_include no',
	'footer_headings_font_group' => 'hide if footer_include no',
	'footer_link_font_group'     => 'hide if footer_include no',
	'ad_banners_area_lr_margin'  => 'hide if sponsors off',
	'ad_banners_margin_right'    => 'hide if sponsors off',
	'ad_banners_margin_btm'      => 'hide if sponsors off',
	'ad_banners_border_color'    => 'hide if sponsors off',

	// galleries
	'lightbox_nav_btns_fadespeed'      => 'hide if lightbox_fixed_navigation true',
	'slideshow_controls_autohide'      => 'hide if slideshow_controls_overlaid false',
	'slideshow_controls_autohide_time' => 'hide if slideshow_controls_overlaid false',
	'slideshow_controls_bg_opacity'    => 'hide if slideshow_controls_overlaid false',
	'slideshow_controls_autohide_time' => 'hide if slideshow_controls_autohide false',
	'slideshow_transition_time'        => 'hide if slideshow_transition_type jump',

	// settings
	'maintenance_message'   => 'hide if maintenance_mode off',
	'pathfixer_new'         => 'hide if pathfixer off',
	'backup_email'          => 'hide if backup_reminder off',
	'seo_title_home'        => 'hide if seo_disable true',
	'seo_meta_desc'         => 'hide if seo_disable true',
	'seo_meta_keywords'     => 'hide if seo_disable true',
	'noindexoptions'        => 'hide if seo_disable true',
	'audio_upload_note'     => 'hide if audioplayer off',
	'audioplayer_center_bg' => 'hide if audioplayer off',
	'audiooptions'          => 'hide if audioplayer off',
	'audio_where'           => 'hide if audioplayer off',
	'audio_hidden'          => 'hide if audioplayer off',
	'audio_ftp_files'       => 'hide if audioplayer off',
	'des_html_mark'         => 'hide if designed_for_prophoto_store false',

	// facebook
	'fb_comments_also_show_unique_wp' => 'hide if fb_comments_enable false',
	'fb_comments_add_new'             => 'hide if fb_comments_enable false',
	'fb_comments_colorscheme'         => 'hide if fb_comments_enable false',
	'fb_comments_num_shown_nonsingle' => 'hide if fb_comments_enable false',
	'fb_comments_num_shown_single'    => 'hide if fb_comments_enable false',

	// sidebar
	'sidebar_on_which_pages'       => 'hide if sidebar false',
	'sidebar_border_group'         => 'hide if sidebar_border_switch off',
	'sidebar_width'                => 'hide if sidebar false',
	'sidebar_bg_color'             => 'hide if sidebar false',
	'sidebar_headlines_font_group' => 'hide if sidebar false',
	'sidebar_text_font_group'      => 'hide if sidebar false',
	'sidebar_link_font_group'      => 'hide if sidebar false',
	'sidebar_border_switch'        => 'hide if sidebar false',
	'sidebar_widget_sep_img'       => 'hide if sidebar false',
	'sidebar_bg'                   => 'hide if sidebar false',

	// ad banners
	'ad_banners_area_lr_margin' => 'hide if show_ad_banners false',
	'ad_banners_margin_right'   => 'hide if show_ad_banners false',
	'ad_banners_margin_btm'     => 'hide if show_ad_banners false',
	'ad_banners_border_color'   => 'hide if show_ad_banners false',
);



/* live font area preview text */
$paragraphs = '<div class="margin-bottom">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

$textWithLinks = 'Lorem ipsum dolor sit amet, <a href="">consectetur adipisicing</a> elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. <a href="">Excepteur sint</a> occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

$p4_font_preview_text = array(

	// mobile
	'mobile_article_excerpt_title'         => 'This is a medium-length sample headline',
	'mobile_article_excerpt_text'          => 'This represents a small bit of post text shown in excerpt..',
	'mobile_button'                        => 'older posts',
	'mobile_article_title'                 => 'This is a sample post/page title',
	'mobile_article_meta_below_title_link' => 'February 23, 2011<br />Posted in <a href="">Weddings</a>',
	'mobile_article_text_link'             => $textWithLinks,
	'mobile_headline'                      => '<span style="font-size:18px;">A Sample Headline Title</span>',
	'mobile_link'                          => '<a href="">A link</a> right next to <a href="">another awesome link</a>',
	'mobile_comments_area_link'            => $textWithLinks,

	// bio
	'bio_header' => '<span style="font-size:16px;">This is a medium-length sample headline</span>',
	'bio_link'   => 'Some sample bio text <a href="">with a link</a> and <a href="">another text link</a>.',

	// comments
	'comments_header_link'                  => 'by <a href="">Joe Postauthor</a> &nbsp; <a href="">show 5 comments</a>',
	'comment_author_link'                   => '<a href="">Joe Commenter</a> - a comment',
	'comments_header_post_interaction_link' => '<a href="">Add a Comment</a><a href="">Link to this post</a><a href="">Email a friend</a>',

	// content
	'post_title_link'       => '<a class="margin-bottom" href="">This is a sample post title link</a>',
	'post_header_meta_link' => 'February 23, 2010 &nbsp;&nbsp; Posted in <a href="">Weddings</a> &nbsp;&nbsp; Tagged: <a href="">Holland</a>, <a href="">Bridal</a>',
	'post_footer_meta_link' => 'Posted in <a href="">Weddings</a> &nbsp;&nbsp; Tagged: <a href="">Holland</a>, <a href="">Bridal</a>',
	'post_text'             => $paragraphs,
	'archive_h2'            => 'Monthly Archives: February 2010',
	'post_text_link'        => 'Some post text <a href="">with a link</a> and <a href="">another link</a>.',

	// fonts
	'gen_link' => 'Some text <a href="">with a nice big link</a> followed by even more random text <a href="">and another example link</a>.',
	'header'   => 'This is a medium-length sample headline',
	'gen'      => $paragraphs,

	// footer
	'footer_headings' => 'A Sample Footer Heading',
	'footer_link'     => 'Some footer plain text &nbsp;&nbsp;&nbsp;&nbsp;<a href="">A sample footer link</a>',

	// galleries
	'slideshow_title'    => 'Rick and Michelle\'s Slideshow',
	'slideshow_subtitle' => 'Friday, February 23, 2010',
	'lightbox'           => '<strong>Wedding_01</strong><br />Image 1 of 11',

	// sidebar
	'sidebar_headlines'       => 'Sidebar Headline',
	'sidebar_link'            => 'Some sidebar text <a href="">with a link</a> and <a href="">another text link</a>.',
	'drawer_widget_headlines' => 'Drawer Headline',
	'drawer_tab'              => 'M<br />o<br />r<br />e<br /> <br />I<br />n<br />f<br />o',
	'drawer_widget_link'      => 'Some drawer widget text <a href="">with a link</a> and <a href="">another text link</a>.',
	'call_to_action_link'     => '<a href="">Contact Me</a> | <a href="">Back to Top</a> | <a href="">Share on Facebook</a>',

	// default
	'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',

);



