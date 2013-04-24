<?php

$configArray = array(

	'tutorial' => array(

		// background
		'blog_bg'         => 'main-background',
		'blog_bg_inner'   => 'main-background-inner',
		'blog_width'      => 'blog-width',
		'blog_border'     => 'blog-border-style',
		'splitter_note'   => 'section-separation',
		'blog_top_margin' => 'site-top-bottom-margins',

		// fonts
		'gen_font_group'      => 'overall-font-appearance',
		'gen_link_font_group' => 'overall-link-font-appearance',
		'header_font_group'   => 'overall-headline-appearance',
		'custom_font_*num*'   => 'uploading-custom-fonts',

		// header area
		'headerlayout'                 => 'header-layout',
		'header_bg_color'              => 'header-background-color',
		'masthead_top_border'          => 'custom-masthead-lines',
		'logo'                         => 'main-blog-logo',
		'logo_swf_switch'              => 'custom-flash-logo',
		'logo_swf'                     => 'custom-flash-logo',
		'masthead_display'             => 'masthead-slideshow',
		'masthead_slideshow_hold_time' => 'masthead-slideshow-options',
		'masthead_custom_flash'        => 'custom-flash-masthead',
		'masthead_image*num*'          => 'creating-masthead-images',
		'masthead_reorder'             => 'reordering-masthead-images',

		// menus
		'primary_nav_menu_admin'                       => 'menus-overview',
		'primary_nav_menu_align'                       => 'menu-alignment',
		'primary_nav_menu_bg'                          => 'menu-background',
		'secondary_nav_menu_admin'                     => 'menus-overview',
		'secondary_nav_menu_align'                     => 'menu-alignment',
		'secondary_nav_menu_bg'                        => 'menu-background',
		'primary_nav_menu_dropdown_bg_color'           => 'menu-dropdown-appearance',
		'primary_nav_menu_link_font_group'             => 'menu-link-appearance',
		'primary_nav_menu_dropdown_link_textsize'      => 'dropdown-link-appearance',
		'primary_nav_menu_link_spacing_between'        => 'menu-link-custom-spacing',
		'primary_nav_menu_border_top_onoff'            => 'custom-menu-lines',
		'primary_nav_menu_onoff'                       => 'menu-display-toggle',
		'secondary_nav_menu_dropdown_bg_color'         => 'menu-dropdown-appearance',
		'secondary_nav_menu_link_font_group'           => 'menu-link-appearance',
		'secondary_nav_menu_dropdown_link_textsize'    => 'dropdown-link-appearance',
		'secondary_nav_menu_link_spacing_between'      => 'menu-link-custom-spacing',
		'secondary_nav_menu_border_top_onoff'          => 'custom-menu-lines',
		'secondary_nav_menu_onoff'                     => 'menu-display-toggle',
		'secondary_nav_menu_placement'                 => 'secondary-nav-menu-placement',
		'widget_menu_*num*_admin'                      => 'vertical-widget-menus',
		'widget_menu_*num*_location'                   => 'vertical-widget-menus',
		'widget_menu_*num*_li_margin_btm'              => 'vertical-widget-menu-item-spacing',
		'widget_menu_*num*_li_link_font_group'         => 'vertical-widget-menu-link-appearance',
		'widget_menu_*num*_sub_li_link_font_group'     => 'vertical-widget-menu-link-appearance',
		'widget_menu_*num*_sub_sub_li_link_font_group' => 'vertical-widget-menu-link-appearance',
		'widget_menu_*num*_li_list_style'              => 'vertical-widget-menu-list-decoration',

		// contact form
		'contact_bg'                       => 'contact-form-background',
		'contact_btm_border'               => 'contact-form-bottom-border',
		'contact_header_color'             => 'contact-form-text-colors',
		'contact_error_msg'                => 'contact-form-error-message',
		'contact_success_msg'              => 'contact-form-success-message',
		'contactform_emailto'              => 'contact-form-email-address',
		'contact_email_custom_subject'     => 'contact-form-email-subject',
		'contact_note'                     => 'contact-form-area-widget-content',
		'contactform_ajax'                 => 'contact-form-troubleshooting-tweaks',
		'contact_customfield*num*_label'   => 'contact-form-custom-fields',
		'contactform_yourinformation_text' => 'contact-form-text',
		'contactform_antispam_enable'      => 'anti-spam-challenges',
		'anti_spam_question_*num*'         => 'anti-spam-questions',
		'contact_log_note'                 => 'contact-form-log',
		'contact_log'                      => 'contact-form-log',

		// bio area
		'bio_include'           => 'bio-area',
		'use_hidden_bio'        => 'bio-area-display-type',
		'bio_pages_options'     => 'bio-on-page-types',
		'bio_bg'                => 'bio-background',
		'bio_inner_bg'          => 'bio-background',
		'bio_border'            => 'border-below-bio',
		'biopic_display'        => 'bio-picture',
		'biopic_border'         => 'bio-picture-border',
		'biopic*num*'           => 'bio-picture-upload',
		'bio_top_padding'       => 'bio-spacing',
		'bio_widget_margin_btm' => 'bio-widget-spacing',
		'bio_header_font_group' => 'bio-headline-appearance',
		'bio_para_font_group'   => 'bio-text-appearance',
		'bio_link_font_group'   => 'bio-link-appearance',
		'bio_content'           => 'bio-area',
		'bio_separator'         => 'border-below-bio',

		// content appearance
		'body_bg'                          => 'content-and-post-backgrounds',
		'post_bg'                          => 'content-and-post-backgrounds',
		'page_bg'                          => 'content-and-post-backgrounds',
		'post_header_align'                => 'post-header-styling',
		'post_title_link_font_group'       => 'post-title-appearance',
		'postdate_display'                 => 'post-date-time-display',
		'post_header_postdate_font_group'  => 'post-date-text-appearance',
		'postdate_advanced_switch'         => 'advanced-date-options',
		'post_title_below_meta'            => 'post-header-info-display',
		'post_header_meta_link_font_group' => 'post-header-info-appearance',
		'post_header_border'               => 'line-below-post-header',
		'post_header_separator'            => 'image-below-post-header',
		'post_text_font_group'             => 'post-text-appearance',
		'post_text_link_font_group'        => 'post-text-appearance',
		'post_pic_margin_top'              => 'post-picture-appearance',
		'post_pic_shadow_enable'           => 'post-picture-dropshadows',
		'lazyload_imgs'                    => 'image-lazyloader',
		'image_protection'                 => 'deter-image-theft',
		'watermark'                        => 'watermarking-images',
		'like_btn_enable'                  => 'like-button',
		'post_footer_meta'                 => 'post-bottom-info-display',
		'post_footer_meta_link_font_group' => 'post-bottom-info-appearance',
		'paginate_post_navigation'         => 'post-navigation-links',
		'nav_below_link_font_group'        => 'post-navigation-links-styling',
		'post_divider'                     => 'post-separator',
		'post_sep'                         => 'post-separator-image',
		'call_to_action_enable'            => 'call-to-action',
		'call_to_action_link_font_group'   => 'call-to-action-text-appearance',
		'call_to_action_separator'         => 'call-to-action-item-separators',
		'call_to_action_*num*'             => 'call-to-action-item',
		'category_list_divider'            => 'category-links-dislay-options',
		'tag_list_prepend'                 => 'tag-options',
		'excerpts'                         => 'post-excerpts',
		'excerpt_style'                    => 'excerpt-style',
		'show_excerpt_image'               => 'post-excerpts',
		'excerpt_grid_cols'                => 'grid-excerpts',
		'archive_h*num*_font_group'        => 'other-pages-header-styling',
		'archive_post_divider'             => 'other-pages-post-separator',

		// comments
		'comments_enable'                                  => 'comments-layout-display',
		'comments_show_on_archive'                         => 'comments-on-archives',
		'comments_scrollbox_height'                        => 'comments-body-height',
		'comments_area_lr_margin_control'                  => 'overall-comments-margins',
		'comments_area_bg_color'                           => 'comments-area-background-colors',
		'comments_area_border_group'                       => 'comments-area-border',
		'comments_show_avatars'                            => 'gravatars',
		'comments_ajax_adding_enabled'                     => 'ajaxed-comments',
		'reverse_comments'                                 => 'sort-comments',
		'comments_header_bg'                               => 'comments-header-background',
		'comments_header_show_article_author'              => 'comments-header-options',
		'comments_header_link_font_group'                  => 'comments-header-font-link-appearance',
		'comments_header_post_interaction_link_font_group' => 'comments-post-interaction-appearance',
		'comments_show_hide_method'                        => 'show-hide-comments-link',
		'comments_post_interact_display'                   => 'comments-post-interaction-links',
		'comments_header_addacomment_link_text'            => 'comments-post-interaction-links',
		'comments_header_linktothispost_link_include'      => 'comments-post-interaction-links',
		'comments_header_emailafriend_link_include'        => 'comments-post-interaction-links',
		'comments_body_area_bg'                            => 'comments-content-background',
		'comment_meta_position'                            => 'comment-author-links',
		'comment_timestamp_display'                        => 'comment-timestamp-display',
		'comment_text_and_link_font_group'                 => 'inividual-comment-font-appearance',
		'comment_tb_padding'                               => 'individual-comment-spacing',
		'comment_bottom_border_onoff'                      => 'line-separating-comments',
		'comment_alt_bg_color'                             => 'alternate-styling-comments',
		'comment_byauthor_bg_color'                        => 'post-author-comment-styling',
		'comment_awaiting_moderation_text'                 => 'comment-moderation-text-style',
		'fb_comments_enable'                               => 'facebook-comments',

		// sidebars & footers
		'sidebar'                            => 'fixed-sidebar-display',
		'sidebar_width'                      => 'fixed-sidebar-dimensions',
		'sidebar_bg'                         => 'fixed-sidebar-background',
		'sidebar_headlines_font_group'       => 'fixed-sidebar-headline-appearance',
		'sidebar_text_font_group'            => 'fixed-sidebar-font-appearance',
		'sidebar_link_font_group'            => 'fixed-sidebar-link-appearance',
		'sidebar_border_switch'              => 'fixed-sidebar-border-options',
		'sidebar_widget_sep_img'             => 'fixed-sidebar-widget-separator',
		'sliding_drawer_sidebar_note'        => 'sliding-sidebar-drawers',
		'drawer_default_bg_color'            => 'sliding-sidebar-background-options',
		'drawer_widget_headlines_font_group' => 'sliding-sidebar-headline-appearance',
		'drawer_widget_text_font_group'      => 'sliding-sidebar-font-appearance',
		'drawer_tab_font_group'              => 'sliding-sidebar-tab-font',
		'drawer_widget_link_font_group'      => 'sliding-sidebar-link-appearance',
		'drawer_tab_text_*num*'              => 'sliding-sidebar-individual-options',
		'show_ad_banners'                    => 'sponsor-banner-link-options',
		'banner*num*'                        => 'sponsor-banner-link-options',
		'footer_include'                     => 'footer-area',
		'footer_bg'                          => 'footer-options',
		'footer_btm_cap'                     => 'footer-bottom-cap-image',
		'footer_left_padding'                => 'footer-custom-spacing',
		'footer_headings_font_group'         => 'footer-headings-text-appearance',
		'footer_link_font_group'             => 'footer-links-text-appearance',
		'custom_copyright'                   => 'custom-copyright',
		'link_removal_txn_id'                => 'unbranded-license',

		// galleries
		'slideshow_bg_color'               => 'slideshow-general-options',
		'slideshow_splash_screen_height'   => 'slideshow-overlay-options',
		'slideshow_title_font_group'       => 'slideshow-overlay-text-appearance',
		'slideshow_subtitle_font_group'    => 'slideshow-overlay-text-appearance',
		'slideshow_splash_screen_logo'     => 'slideshow-overlay-logo',
		'slideshow_start_playing'          => 'slideshow-options',
		'slideshow_controls_position'      => 'slideshow-thumbstrip-position',
		'slideshow_thumb_paging_animation' => 'slideshow-thumbstrip-settings',
		'slideshow_thumb_size'             => 'slideshow-thumbnail-appearance',
		'lightbox_thumb_default_size'      => 'lightbox-gallery-thumbnail-options',
		'lightbox_border_width'            => 'lightbox-gallery-general-options',
		'lightbox_font_group'              => 'lightbox-gallery-info-appearance',
		'lightbox_overlay_color'           => 'lightbox-gallery-overlay-options',
		'lightbox_fixed_navigation'        => 'lightbox-gallery-navigation-options',
		'lightbox_loading'                 => 'lightbox-gallery-loading-image',
		'lightbox_close'                   => 'lightbox-gallery-close-image',
		'lightbox_next'                    => 'lightbox-gallery-next-image',
		'lightbox_prev'                    => 'lightbox-gallery-previous-image',
		'audio*num*'                       => 'uploading-audio-files',
		'slideshow_mp*num*_autostart'      => 'slideshow-audio-options',

		// grids
		'grid_img_text_below_gutter'                   => 'grid-item-spacing',
		'grid_img_rollover_text_gutter'                => 'grid-item-spacing',
		'grid_img_text_below_title_link_font_group'    => 'grid-style-text-below-image-options',
		'grid_img_rollover_text_overlay_bg_color'      => 'grid-style-rollover-text-options',
		'grid_img_rollover_text_title_link_font_group' => 'grid-style-rollover-text-options',
		'grid_img_rollover_text_text_link_font_group'  => 'grid-style-rollover-text-options',
		'grid_article_img_fallback'                    => 'grid-post-image-fallback',
		'grid_category_images'                         => 'grid-category-images',

		// mobile
		'mobile_enable'                                   => 'mobile-site-enable',
		'mobile_logo_use_desktop'                         => 'mobile-logo-switch',
		'mobile_logo'                                     => 'mobile-logo-image',
		'mobile_masthead_use_desktop_settings'            => 'mobile-masthead-switch',
		'mobile_masthead_display'                         => 'masthead-slideshow',
		'mobile_masthead_slideshow_hold_time'             => 'masthead-slideshow-options',
		'mobile_masthead_image*num*'                      => 'mobile-masthead-images',
		'mobile_content_bg'                               => 'mobile-content-background',
		'mobile_excerpt_list_border_group'                => 'mobile-excerpt-list-border',
		'mobile_button_bg_color'                          => 'mobile-button-backgrounds',
		'mobile_font_group'                               => 'mobile-overall-text-appearance',
		'mobile_link_font_group'                          => 'mobile-overall-link-appearance',
		'mobile_article_excerpt_title_font_group'         => 'mobile-excerpt-title-appearance',
		'mobile_headline_font_group'                      => 'mobile-headline-appearance',
		'mobile_article_excerpt_text_font_group'          => 'mobile-excerpt-text-appearance',
		'mobile_button_font_group'                        => 'mobile-button-text-appearance',
		'mobile_article_title_font_group'                 => 'mobile-post-title-appearance',
		'mobile_article_meta_below_title_link_font_group' => 'mobile-post-meta-appearance',
		'mobile_article_text_link_font_group'             => 'mobile-post-text-appearance',
		'mobile_comments_area_link_font_group'            => 'mobile-comments-text-appearance',
		'mobile_comments_area_bg_color'                   => 'mobile-comments-area-background',
		'mobile_comment_header_bg_color'                  => 'mobile-comment-header-colors',
		'mobile_comment_bg_color'                         => 'mobile-comment-colors',
		'mobile_comment_inputs_bg_color'                  => 'mobile-add-comment-form-colors',
		'mobile_post_comment_btn_bg_color'                => 'mobile-add-comment-button-colors',
		'mobile_footer_color_scheme'                      => 'mobile-footer-color',
		'mobile_footer_menu_items'                        => 'mobile-footer-menu',

		// site settings
		'google_analytics_code'       => 'google-analytics',
		'statcounter_analytics_code'  => 'statcounter',
		'twitter_name'                => 'twitter-info',
		'facebook_static_front_page'  => 'facebook-preview-options',
		'fb_home'                     => 'facebook-preview-options',
		'like_btn_verb'               => 'facebook-like-button-site-settings',
		'widget_images_note'          => 'understanding-widgets',
		'widget_custom_image_*num*'   => 'custom-widget-images',
		'favicon'                     => 'favicon',
		'apple_touch_icon'            => 'iphone-webclip-icon',
		'maintenance_mode'            => 'under-construction-mode',
		'backup_reminder'             => 'backup-reminder',
		'auto_auto_upgrade'           => 'automatic-prophoto-updates',
		'secure_download_link'        => 'downloading-prophoto-updates',
		'unregistered'                => 'prophoto-registration',
		'registered'                  => 'prophoto-registration',
		'dev_test_mode'               => 'unregistered-test-mode',
		'gd_img_downsizing'           => 'gd-image-downsizing',

		// advanced
		'feedburner'                    => 'feedburner-url',
		'modify_feed_images'            => 'feed-image-protection',
		'seo_disable'                   => 'search-engine-optimization-options',
		'seo_title_home'                => 'search-engine-optimization-titles',
		'seo_meta_desc'                 => 'search-engine-optimization-descriptions',
		'seo_meta_keywords'             => 'search-engine-optimization-keywords',
		'noindexoptions'                => 'search-engine-optimization-no-index',
		'override_css'                  => 'custom-css',
		'insert_into_head'              => 'insert-into-head',
		'post_signature_placement'      => 'post-signature',
		'custom_js'                     => 'custom-js',
		'translate_password_protected'  => 'translation',
		'translate_by'                  => 'translation',
		'translate_commentform_message' => 'translation',
		'translate_archives_monthly'    => 'translation',
		'translate_search_results'      => 'translation',
		'translate_*num*_header'        => 'translation',
		'translate_lightbox_image'      => 'translation',
		'translate_mobile_loading'      => 'translation',
		'subscribebyemail_lang'         => 'translation',
		'dev_hide_options'              => 'developer-options',
		'designed_for_prophoto_store'   => 'designer-options',
		'extra_bg_img_*num*'            => 'extra-bg-images',
	),

	'video' => array(
		'call_to_action_enable'        => 'call_to_action',
		'customize_overview_mobile'    => 'mobile_sites',
		'mobile_enable'                => 'mobile_sites',
		'manage-designs-page'          => 'managing_designs',
		'customize_overview_menus'     => 'menus_overview',
		'primary_nav_menu_admin'       => 'menus_overview',
		'secondary_nav_menu_admin'     => 'menus_overview',
		'widget_menu_1_admin'          => 'menus_overview',
		'widget_menu_2_admin'          => 'menus_overview',
		'widget_menu_3_admin'          => 'menus_overview',
		'mobile_footer_menu_items'     => 'menus_overview',
		'customize_overview_grids'     => 'grids_overview',
		'customize_overview_galleries' => 'images_galleries_overview',
		'custom_font_*num*'            => 'custom_font_overview',
		'understanding-widgets'        => 'widgets_overview',
		'understanding-wordpress'      => 'using_wordpress',
		'customize-overview'           => 'customize_overview',
		'images-galleries-overview'    => 'images_galleries_overview',
	),

	'blurb' => array(

		// background
		'blog_bg' => 'In this section you can customize the main outermost background of your entire site, behind the entire area that holds your images, content, header, footer, etc.  You can set a background color and upload a background image.  If you upload a background image, you can then control where it is placed, how it tiles, and whether it should scroll with the page (normal behavior) or stay fixed in place when the page is scrolled, creating the appearance that the main content is moving while the background stays in place.',

		'blog_bg_inner' => 'This secondary background image is also for the outermost background of your site.  It overlays any color or background image you have set in the blog outer background #1 section (above).  Adding a second image in this area allows for more advanced background effects. Semi-transparent .png images can be set to tile completely, since the outer background #1 will still be somewhat visible beneath it.  Non-transparent images should be set to not tile, or to only tile in one direction so that they don\'t completely cover the background image or color set above.',

		'blog_top_margin' => 'Set margins here if you want to create space between the top or bottom of the browser\'s viewing area and the area containing all of your site content.  Any background color or image you have set will be visible in the extra space created by these margins.  Set these to zero if you want no space between your site\'s content and the top and/or bottom of the browser window.',

		'blog_width' => 'Here you can control the width of your entire site. It is not recommended to use a width greater than 1200, as many monitors do not have enough resolution to display so many pixels without horizontal scrolling.<hr />Content margin is the spacing or padding between your site content (post text, images, comments) and the left and right edges of your site. If you have a fixed-sidebar, the content margin will be the spacing between the content and the sidebar as well.',

		'blog_border' => 'This option area allows you to set the border-style around the outside of your entire site. You can choose a narrow or wide built-in dropshadow effect, or set the width, color and style of a custom border.  Your border choice can be applied to just the left and right sides of the site, or to all four sides including the top and bottom. Set this to "no border or dropshadow" if you are using any of the "Site section separation" options below, since your border will not be wrapped around sections that are separated.',

		// fonts
		'gen_font_group' => 'These options allow you to set the default appearance of normal, non-link text (not including headlines) throughout your site. These settings will apply to many portions of your site, including post text, comment text, sidebar text, bio text, footer text, and more. Font settings for these more specifically-defined areas of text can be set individually on the other relevant pages within the "Customize ProPhoto" area (for example: comment text appearance can be controlled individually in the "Comments" section).  In any of the individual font customization areas, any options that are <em>not set</em> will inherit the settings from this "Overall font appearance" area.',

		'gen_link_font_group' => 'These options allow you to set the default appearance for non-headline links throughout your site. These settings will apply to links in many portions of your site, including post text, comment text, sidebar text, bio text, footer text, and more. Customizations for links within these more specifically-defined areas can be set individually on the other relevant pages within the "Customize ProPhoto" area (for example: appearance of links within comments can be controlled individually in the "Comments" section).  In any of the other more-specific individual link customization areas, any options that are <em>not set</em> will inherit the settings from this "Overall link font appearance" area.',

		'header_font_group' => 'These options allow you to set the default appearance of all headline text throughout your site. This includes post and page titles and widget headlines in your bio, sidebar, footer, etc. Most areas that have headlines also allow you to individually customize the text appearance of headlines in just that area.  For instance, for widget headlines in the a fixed sidebar, you can set specific customization choices that affect only those headlines.  The settings in this "Overall headline appearance" customization area will be used anywhere you do not make more specific customization choices that apply only to headlines within a specific area.',

		'custom_font_*num*' => 'Here you can upload a custom font for use throughout your site.  Fonts must be uploaded in a very specific format, as a "Font Squirrel web-font kit" in .zip file format. For detailed, step-by-step directions, click the video icon or tutorial link above.',

		// header
		'headerlayout' => 'In this area you can choose the overall arrangement of the main portions of your site\'s header area. This includes the logo, masthead image/slideshow, and navigation menu.<hr /> If you choose one of the first three layouts in which the masthead and logo are in the same horizontal area, your masthead image/s will be limited to the height of your logo. For all other header layouts, the masthead image/s can be any height, as long as they are consistent.<hr />ProPhoto also allows you to add an optional secondary navigation menu (also in the header area), but the placement of that secondary menu is not controlled by this option area.',

		'header_bg_color' => 'This is the optional background color beneath your entire header area (logo, masthead, and navigation menu). This color is only visible when you\'ve chosen a header layout where the logo does not share the same horizontal space as the masthead, <b>and</b> the logo you\'ve uploaded is less than the full width of the blog. Uncheck the "set color" checkbox if you want the main blog outer background color/image to show through any areas where the header background color would normally be seen.',

		'masthead_top_border' => 'In this customization area you can choose to display custom decorative lines above and/or below your header masthead area. These can be any color or width, and can be styled as solid, dashed, dotted, or double-lined.',

		'logo' => 'This is your site\'s main logo.  Depending on your header layout choice, it can appear in several places in your header area.<hr />Your logo can be any height you want, but the width should not be greater than the width of the entire site. In certain header layouts where the logo shares the same horizontal space block with your masthead image/slideshow, the height of your logo also becomes the maximum height of your masthead images.<hr />By default, the logo is also a link to your site\'s home page, although here you can also set a different URL for it link to.',

		'logo_swf_switch' => 'Advanced users can replace their logo with a stand-alone <code>.swf</code> file (flash movie).  To do so, you need to: 1) upload a normal logo image above as a fall-back image for devices that don\'t support flash, 2) create a stand-alone <code>.swf</code> file with explicit dimensions that are are the exact same as the above logo, and 3) upload the <code>.swf</code> file in the below upload area.',

		'logo_swf' => ppString::id( 'blurb_swf_file', 'logo' ),
		'masthead_custom_flash' => ppString::id( 'blurb_swf_file', 'masthead #1' ),

		'masthead_display' => ppString::id( 'blurb_masthead_display', ' (for most header layouts)' ),

		'masthead_slideshow_hold_time' => ppString::id( 'blurb_masthead_slideshow', '' ),

		'masthead_image*num*' => 'Masthead images must be uploaded at a certain width which is calculated based on your header layout and overall site width. The gray "Recommended Image Size" box on the left below will tell you what the correct width is.<hr />If you are uploading more than one image, all of the images must be the same dimensions, so size them properly before uploading.<hr />For most header layouts, the height of your masthead image can be whatever you like, provided they are all the same height.  If you want a taller or shorter masthead area, upload an image of your desired height in the first masthead image upload area. For the three possible header layouts where the logo shares the same horizontal space as the masthead, the height of the logo determines the maximum height of the masthead image. Otherwise the maximum or recommended height will always be the height of the first uploaded masthead image.',

		'masthead_reorder' => 'Here you can drag and drop to re-order your masthead slideshow images, represented in thumbnail-size below.',

		// menus
		'primary_nav_menu_admin'   => ppString::id( 'blurb_menu_structure', 'your main, horizontal',     'dropdown', ' area' ),
		'secondary_nav_menu_admin' => ppString::id( 'blurb_menu_structure', 'your secondary horizontal', 'dropdown', ' area' ),
		'widget_menu_*num*_admin'  => ppString::id( 'blurb_menu_structure', 'a widget-area vertical',    'sub', '' ),

		'primary_nav_menu_align'   => ppString::id( 'blurb_menu_align', 'main' ),
		'secondary_nav_menu_align' => ppString::id( 'blurb_menu_align', 'secondary' ),

		'primary_nav_menu_bg'   => ppString::id( 'blurb_menu_bg', 'main' ),
		'secondary_nav_menu_bg' => ppString::id( 'blurb_menu_bg', 'secondary' ),

		'primary_nav_menu_link_font_group'   => ppString::id( 'blurb_menu_font', 'your primary horizontal navigation menu', '' ),
		'secondary_nav_menu_link_font_group' => ppString::id( 'blurb_menu_font', 'your secondary horizontal navigation menu', '' ),

		'primary_nav_menu_dropdown_bg_color'   => ppString::id( 'blurb_menu_dropdown_bg', 'main' ),
		'secondary_nav_menu_dropdown_bg_color' => ppString::id( 'blurb_menu_dropdown_bg', 'secondary' ),

		'primary_nav_menu_dropdown_link_textsize'   => ppString::id( 'blurb_menu_dropdown_links', 'primary' ),
		'secondary_nav_menu_dropdown_link_textsize' => ppString::id( 'blurb_menu_dropdown_links', 'secondary' ),

		'primary_nav_menu_link_spacing_between'   => ppString::id( 'blurb_menu_spacing', 'primary' ),
		'secondary_nav_menu_link_spacing_between' => ppString::id( 'blurb_menu_spacing', 'secondary' ),

		'primary_nav_menu_border_top_onoff'   => ppString::id( 'blurb_menu_custom_lines', 'primary' ),
		'secondary_nav_menu_border_top_onoff' => ppString::id( 'blurb_menu_custom_lines', 'secondary' ),

		'primary_nav_menu_onoff'   => ppString::id( 'blurb_menu_onoff', 'primary' ),
		'secondary_nav_menu_onoff' => ppString::id( 'blurb_menu_onoff', 'secondary' ),

		'secondary_nav_menu_placement' => 'Your secondary horizontal navigation menu can be located in several places in your header, although the most common placement is directly below your primary horizontal navigation menu.  Read from the descriptions available to choose where you would like your secondary menu to appear within the context of your header area.',

		'widget_menu_1_location' => ppString::id( 'blurb_widget_menu_location', '1' ),
		'widget_menu_2_location' => ppString::id( 'blurb_widget_menu_location', '2' ),
		'widget_menu_3_location' => ppString::id( 'blurb_widget_menu_location', '3' ),

		'widget_menu_*num*_li_margin_btm' => 'Control the spacing below each menu item. ' . ppString::id( 'blurb_widget_menu_levels' ),

		'widget_menu_*num*_li_list_style' => 'Here you can control the decoration of the three levels of menu link items for this vertical navigation menu. List decoration is the presence or absence or style of bullets or numbering to the left of each item. ' . ppString::id( 'blurb_widget_menu_levels' ),

		'widget_menu_*num*_li_link_font_group' => ppString::id( 'blurb_menu_font', 'this vertical navigation menu', 'first level (not nested)' ),

		'widget_menu_*num*_sub_li_link_font_group' => ppString::id( 'blurb_menu_font', 'this vertical navigation menu', 'second level (nested inside a first-level)' ),

		'widget_menu_*num*_sub_sub_li_link_font_group' => ppString::id( 'blurb_menu_font', 'this vertical navigation menu', 'third level (nested inside a second level)' ),

		// contact form
		'contact_bg' => ppString::id( 'blurb_bg_area', 'your built-in contact form area' ),

		'contact_btm_border' => 'Here you can turn on/off and customize a border for the bottom of your contact form. This can give some separation between the form and the content below it.',

		'contact_header_color' => 'Here you can override the default colors of your contact form headline and body text. If you don\'t set these colors here, the default color of the headlines will be whatever you set in the "Overall headline appearance" section on the <a href="' . ppUtil::customizeURL( 'fonts' ) . '">"Fonts" customization page</a>, while the default color of the body text will be whatever you set in the "Overall font appearance" section on the same page.',

		'contact_success_msg' => 'If your visitor to your site fills out your contact form correctly, they will be shown a congratulatory confirmation message. Here you can customize the text of that message, as well as the text color and background color. <em>(<b>TIP:</b> to see what it looks like, submit the form with all required fields filled out correctly.)</em>',

		'contact_error_msg' => 'If a visitor to your site makes a mistake while submitting a request through your contact form, like not giving you their email, they will be shown an error message.  Here you can customize the text of that message, as well as the text color and background color. <em>(<b>TIP:</b> to see what it looks like, submit the form with a required field missing.)</em>',

		'contactform_emailto' => 'Successful contact form submissions will be sent to the email address you specify here. If you leave this field blank, the form will use the email address you entered when you installed WordPress, which can and modified on the <a href="' . admin_url( 'options-general.php' ) . '">Settings > General</a> WordPress admin screen.',

		'contact_email_custom_subject' => 'If set, this text will be used as the <b>email subject</b> of any emails generated and sent to you when someone submits your contact form.  There are four special words you can used that will be replaced dynamically with information from the individual contact form submission: <br /><br /><code>%name%</code> - replaced by the name of the submitter <br /><code>%email%</code> - replaced by the email address of the submitter <br /><code>%date%</code> - replaced by the date of the contact form submission <br /><code>%time%</code> - replaced by the time of the contact form submission ',

		'contactform_ajax' => 'This section contains some options you can try if you are having trouble with your contact form.  If the contact form never loads when a contact form link is clicked, try turning on simple mode.  If the form loads but you are not receiving emails, you can sometimes fix this by setting a valid, real email address from the same domain where your site is installed. If that doesn\'t work, try enabling remote sending, which should work on even the toughest servers.  For more information, see our <a href="' . pp::tut()->fixContactForm . '">tutorial here</a>.',

		'contact_note' => ppString::id( 'blurb_contact_form_widget_content' ),

		'contact_customfield1_label' => 'The contact form comes with only a few built-in fields for you to gather information from people contacting you.  These are extra fields you can add to get more specific information.  You can choose to make them required for the user to fill out (the form will not submit successfully and they will get an error message if they leave it blank), or make it optional.',

		'contactform_yourinformation_text' => 'Here you can modify the headlines and form field labels of the contact form itself.',

		'contactform_antispam_enable' => 'By default, anti-spam challenges are included in your contact form to prevent spam from coming through your contact form.  You may disable this if you choose, as there are other layers of non-intrusive anti-spam protection that may be sufficient for your site.',

		'anti_spam_question_1' => 'To prevent your contact form from getting spammed, you need to have a simple, required question that your user must fill out to prove they are human, and not a spambot. One of three anti-spam questions will be displayed randomly each time the form is shown.  Customize those questions and answers here. The answers won\'t be case-sensitive, so "Blue" or "BLUE", or "blue" will all be accepted.  If your answer has more than one correct answer or possible spelling, just put both answers separated by an <code>*</code> (like <code>four*4*IV</code>) and any of them will work. Do not leave these questions or answers blank -- if you want to disable the anti-spam challenges, instead disable them with the option above. Leaving them blank will result in the form not submitting successfully.',

		'contact_log_note' => ppString::id( 'blurb_contact_log' ),
		'contact_log'      => ppString::id( 'blurb_contact_log' ),

		// bio area
		'bio_include' => 'This is the main on/off switch for the built in ProPhoto "Bio" area (like an "About Me" or "Get to know me" section near the top of your page).',

		'use_hidden_bio' => 'The normal behavior is for your bio section to be immediately visible to your blog viewers.  Here you can choose to minimize it instead.  If you do so, visitors to your site will not immediately be able to see your bio section.  Rather, they will have to click on an "About Me" link in the navigation menu bar to see the bio section, which will then gracefully slide down into view.',

		'bio_pages_options' => 'Normally, this blog is set up to only show your bio section on your blog "home" page.  This means if visitors are viewing something other than your main site home page, like a single-post page, or a category archive page or monthly archive page, they won\'t see your bio. Here you can choose exactly which types of pages your bio will be shown on. If you choose "bio section minimized on unchecked", the unchecked type of pages will have a link added to the navigation menu bar which will reveal your bio when clicked.',

		'bio_bg' => ppString::id( 'blurb_bg_area', 'your bio area' ),

		'bio_inner_bg' => 'This is a second background image option for your bio area, applied to the inside of the area. It can be used on it\'s own, or in conjunction with the "Bio area main background image" to create more complicated visual effects.',

		'bio_border' => 'Between the bio area and the main body of your site, you can choose to have no separating line, a customizable separating line, or a custom image to separate the sections.',

		'bio_separator' => 'You can upload an image here that will appear centered beneath your bio area. ProPhoto will accommodate whatever size image you upload, up to the width of your blog.',

		'biopic_display' => 'Here, choose whether to display a picture of yourself for the bio area, and what side you want it aligned to. You can also upload multiple bio pictures and have one inserted at random each time the page reloads. If you do choose to show a random bio picture, be sure to upload every picture with same dimensions.',

		'biopic_border' => 'Choose whether or not to add a border around your biopic. You can customize the color, thickness, and style of the border.',

		'biopic1' => 'This is your optional bio/profile picture, used in the bio section of your blog. If you want to upload multiple pictures, size them exactly the same, and then select "Random bio picture on each page load" in the option area above.',

		'biopic*num*' => 'In these areas, upload as many bio pictures as you want. Please note, they must be sized with the exact same dimensions as your first, main bio picture above.',

		'bio_top_padding' => 'In this area you can take precise control of the space between your bio content and the edges of your bio area, as well as the space between columns of bio content.',

		'bio_widget_margin_btm' => 'Here you can adjust the amount of vertical space between widgets that are added to the same column of your bio area. You can also tweak the vertical space between the widget title and the widget content for widgets that have a defined title.',

		'bio_header_font_group' => 'Most widgets have the option to add a title. This area allows you to customize the text of any widget titles in your bio area. ' . ppString::id( 'blurb_font_inheritance', 'titles', 'headline' ),

		'bio_para_font_group' => 'Here you can customize the general body text within any widgets you have added to your bio area. ' . ppString::id( 'blurb_font_inheritance', 'text areas', 'font' ),

		'bio_link_font_group' => 'Here you can customize any links within widgets you have added to your bio area. ' . ppString::id( 'blurb_font_inheritance', 'links', 'link font' ),

		'bio_content' => ppString::id( 'blurb_bio_content' ),

		// content appearance
		'body_bg' => 'This background customization area affects your entire blog content area, between your header and footer. ' . ppString::id( 'blurb_bg_area', 'this entire area' ),

		'post_bg' => ppString::id( 'blurb_article_bg', 'Post', 'post' ),
		'page_bg' => ppString::id( 'blurb_article_bg', 'Page', 'page' ),

		'post_header_align' => 'Each post or page header (which consists of the post title, post date, and optionally the "categorized as" list) can be aligned to the left, center or right. You can also adjust the amount of spacing above and below the header.',

		'post_title_link_font_group' => 'Here you can customize the appearance of your of your post titles (which, on certain page types are also links that take the user to the single permalink page for that post). ' . ppString::id( 'blurb_font_inheritance', 'titles', 'headline' ),

		'postdate_display' => 'Here you can customize how and where your post published-date appears in each post header. "Boxy style" provides additional styling and color options for your date display and shows your post published-dates in an attractive and simple box.',

		'post_header_postdate_font_group' => 'These font customization options affect the published post date and time in your post headers, when your post date display style is not set to "boxy" (see above).',

		'postdate_advanced_switch' => 'By enabling post date advanced features, you get access to additional customization controls over the appearance of your published post date in your post headers, including spacing, background color, and border appearance.',

		'post_title_below_meta' => 'Pick which, if any, extra post info items you want to display under your post title. These items can be customized further under the "Category & Tag Lists" sub-tab of this page.',

		'post_header_meta_link_font_group' => 'Customize the text appearance of your categories, date, tags and comment count items located in your post header. ' . ppString::id( 'blurb_font_inheritance', 'items', 'link font appearance" and the "Overall font' ),

		'post_header_border' => 'Here you can add and customize a decorative line below each post header, or choose to upload a custom image to display under your post header.',

		'post_header_separator' => 'Upload a custom image that will display centered between your post header and post content.',

		'post_text_font_group' => 'This font customization area affects the non-link text content of your WordPress posts and pages. ' . ppString::id( 'blurb_font_inheritance', 'text areas', 'font' ),

		'post_text_link_font_group' => 'This area styles specifically links that you create within your posts, in the WordPress post/page editing screen. ' . ppString::id( 'blurb_font_inheritance', 'links', 'link font' ),

		'post_pic_margin_top' => 'Add and customize an optional border around the pictures you post, plus set the spacing above and below your pictures within your posts and pages. If you don\'t want a border around your images, set the border width to 0px.',

		'post_pic_shadow_enable' => 'This option section allows you to add customized dropshadows to your post images.  It only works in supported browsers - currently Firefox, Safari, Chrome, IE9, iPad, iPhone - NOT in any older versions of Internet Explorer. On unsupported browsers there will be no difference in appearance, so it\'s safe to add to enhance the experience of people using good browsers.' ,

		'lazyload_imgs' => '"Lazyloading" of images means that if you have a long page full of many images or multiple posts, ProPhoto will only load the images that will be immediately visible to your site\'s visitor, plus the images within about 1000 pixels of the bottom of their browser screen.  Then, as they scroll down, ProPhoto will keep loading additional images, usually fast enough that the user never notices that images below their viewing area have not been loaded yet.  This can dramatically speed up the page loading time of your site if you show a lot of posts, or post a lot of images.  There is also a throbber image that shows should a visitor scroll down fast enough that an image has not finished loading, letting them know that an image is coming.',

		'image_protection' => 'Many photographers are concerned about image theft from their blogs when they post large images.  This option allows you to select from a few different levels of image theft protection. Disabling only right click prevents users from right-clicking on images and seeing the "Save image as" window appear.  Disabling left clicks prevents links that go directly to your uploaded image from working.  Disabling dragging overlays a blank, transparent image over your image, so that if a visitor tries to drag and drop an image from their browser to their computer, they get the blank image.  Finally, adding a watermark allows ProPhoto to create a new, watermarked image made by meshing your uploaded image with your watermarked image.',

		'watermark' => 'An image uploaded here will be laid on top of your posted images according to the settings in the "Post image theft protection" area above. It is important to note that this image will not be stretched or shrunk to fit your images. So if you want your watermark to span the entirety of all your images, create your watermark image with dimensions that are a bit larger than the largest possible dimension of your uploaded images, created in such a way that some cropping of the watermark image will not ruin the effect.',

		'like_btn_enable' => 'This section allows you to add Facebook "Like" buttons below posts and pages. You can also control where the buttons appear, and their basic appearance.  The "Filter priority" setting is intended for to modify where the like buttons appear in relationship to items added to your post/page content footer like your post-signature or third-party plugin content.<hr />Like buttons can also be added (with fewer customization options) as a <a href="javascript:jQuery(\'#subgroup-nav-cta a\').click();return false;">Call to action item</a>.<hr />A few more customization options that apply both to Like buttons in post footer and in Call to action items can be found <a href="' . ppUtil::customizeUrl( 'settings', 'social_media' ) . '">here</a>.',

		'post_footer_meta' => 'Select which, if any, post info items you want included under the content of each post.',

		'post_footer_meta_link_font_group' => 'Customize the text appearance of your categories and/or tags if you have set them to display below your post content. ' . ppString::id( 'blurb_font_inheritance', 'items', 'link font appearance" and the "Overall font' ),

		'paginate_post_navigation' => 'At the bottom of page types where multiple posts are displayed, there are links to show older/newer posts.  Here you can choose what format you want these links to appear in, plus make some simple customization choices.  Choosing numbered links gives you a list of numbered links to all the relevant pages of post content for that page type, plus previous and next buttons.  Alternatively, you can set it just to show two simple older and newer links on each page.',

		'nav_below_link_font_group' => 'Here you can set the font link appearance of the newer/older posts links at the bottom of your site pages. ' . ppString::id( 'blurb_font_inheritance', 'links', 'font link' ),

		'post_divider' => 'Several page-types of your site display more than one post per page. On these pages it can be desirable to have some space, a line, or an image visually separate these posts.  Here you can customize the spacing between posts, add a customizable line, or choose to use an uploaded image as a separator.  If selected, you can upload a custom image in the option area below this one.',

		'post_sep' => 'Upload a custom post separator image that will display horizontally centered between each of your posts.',

		'call_to_action_enable' => 'This area is the master switch and main controls for the optional "Call to Action" area below your WordPress posts.  The "Call to Action" area is meant to give your site visitors simple and concrete response actions they can take after reading one of your posts, such as "Contact me", "Tweet this post", "Scroll back to top", "View my portfolio", etc.  If enable, you may choose the location of your Call to Action section, on which page types they appear, their alignment, and spacing',

		'call_to_action_link_font_group' => 'This link font customization area allows you to set styles for any text-based Call to Action link items. ' . ppString::id( 'blurb_font_inheritance', 'links', 'font link' ),

		'call_to_action_separator' => 'For many designs, it can look nice to have something visually separating the individual call to action items.  This option area lets you set whether to have no separator, a bit of text as a separator, or an uploaded image as separator between items',

		'call_to_action_*num*' => 'This area allows you to enable and customize one of your individual Call to Action items.  On the left, turn from "off" to one of the main Call to Action item types, then set the display type to text or image.  If you select image, you\'ll need to upload an image to be displayed for this item.  For several item types, there will additional customization options.<hr />For the <code>Email post to friend</code> type, when customizing the default email subject and body text, you can also use four special words that will be replaced with the corresponding information from your site or the specific post in question: <code>%post_url%</code>, <code>%post_name%</code>, <code>%site_url%</code>, <code>%site_name%</code>',

		'category_list_divider' => 'Your blog displays a bit of text showing how every post is categorized. Here you can customize this text, and what character(s) separates multiple category links if a post has more than one category.',

		'tag_list_prepend' => 'Tags are another way of organizing, labeling, and grouping your posts.  You can add tags to a post when you create the post, or afterward in the admin area.  Here you can choose how the list of a post\'s tags appears, what character(s) separate a list of multiple tag links, and on which type of pages these lists will appear.',

		'excerpts' => 'On archive pages (like monthly archives, category archives, author archives, tag archives, and search results pages), the default behavior is to only show excerpts of each post, not the whole post. This is a little better for search-engine-optimization, and also makes it easier for your users to scan lots of posts. You can, alternatively, make it so these pages display the complete content instead by unchecking the page-types where you want full content shown.  For posts that are excerpted the text used to link to the full post may also be customized here.',

		'excerpt_style' => 'There are two main ways to show excerpts. The standard is to show a small portion of any text in the post, plus optionally a single image from the post.  You may also select "image grid" if you would like to instead display your excerpts as a ProPhoto Grid.  See the <a href="' . ppUtil::customizeURL( 'grids' ) . '">"Grids" customization area</a> for more information on Grids.',

		'excerpt_grid_cols' => 'Here you can make some basic choices about the display of your excerpts as a grid, including the number of rows and columns (which ultimately determines how many excerpts are shown per page, and the size of the grid items), as well as the grid "style" to be used.',

		'show_excerpt_image' => 'ProPhoto can try to include a single image from each post to make your post excerpts more attractive.  This option area allows you to make choices about excerpt images.  You can control the size and placement of these images, plus whether ProPhoto should only use designated Featured Images when available, or always try to show an excerpt image by using the first available image if no Featured Image is set for that post.',

		'archive_h2_font_group' => 'On archive, category, search, tag, and author pages, there will be another header above your first post title which you can customize here.  Example: on the monthly archives page it will say "Monthly Archives: June',

		'archive_post_divider' => 'In the "Post Footer" tab section of this page are the settings for what visually divides posts when multiple posts are shown on one page.  Here you can choose to have these same settings apply to non-home pages (like monthly archives and category archives), or to create a custom line just for these pages.',

		// comments
		'comments_enable' => 'If you want no comments anywhere on your site, choose "disable & hide all comments".  Otherwise, here you can select which overall type of comments layout you\'d like. ProPhoto comes with three basic comment layout types: tabbed, boxy, and flexible. The flexible layout is the most customizable of the three, but you can customize colors and aspects of all three types. Also choose here whether you want your comments to be hidden until your viewers click the "show comments" button, or have them always visible.',

		'comments_show_on_archive' => 'On archive pages (like monthly archives, category archives, author archives, tag archives, and search results pages), the default behavior is to not show the comments for each post. You can, alternatively, make it so that comments area displayed on these types of pages as well.',

		'comments_scrollbox_height' => 'For the "Tabbed" and "Flexible" comment layouts, comments are shown in a fixed-height scrolling box.  Here you can set the height of that scrolling area, and whether you want instead to show all comments without scrolling on your home-type pages or on single-post pages.',

		'comments_area_lr_margin_control' => 'Define a specific amount of horizontal space between your comments area and the left/right edges of your blog, or inherit the same spacing you set for your content margin in the "Blog width and content margin" area of the <a href="' . ppUtil::customizeURL( 'background' ) . '">"Background" customization page</a>.',

		'comments_area_bg_color' => 'Here you can set the background area of the entire comments area, and the background color of each individual comment. These background settings can be overridden by more specific background settings on the "Comments header" and "Comment options" tab sections of this page.',

		'comments_area_border_group' => 'Customize the color and settings of the border surrounding the entire comment area. For no border, just set the border width to <code>0</code>.',

		'comments_show_avatars' => 'Enable comment author avatars within your comment content. Enabling this option will display an author-specific avatar icon next to each comment according your settings on the <a href="' . admin_url( 'options-discussion.php' ) . '">"Settings" > "Discussion"</a> page.',

		'reverse_comments' => 'Here you can set whether your comments are displayed from top to bottom as newest to oldest, or oldest to newest.',

		'comments_ajax_adding_enabled' => 'Ajax comment submission is what allows your site visitors on your home page to add comments without ever leaving the page -- new comments are posted and inserted immediately into the page.  Occasionally, on certain server setups, or when used in conjunction with certain third-party plugins, this can cause problems, which disabling can resolve.  Try disabling this if you are having trouble with comments not being added and inserted correctly.',

		'comments_header_bg' => ppString::id( 'blurb_bg_area', 'the comments header area.  This is the area that shows the current comment count, the "add comment" link, and optionally the "email a friend" and "link to this post" links' ),

		'comments_header_show_article_author' => 'This area provides some more options for your comments area header. Choose whether or not to display the name of the post author, as well as define the space left and right, above and below, your comments area header.',

		'comments_header_link_font_group' => 'This area gives specific control to the text/link appearance of the items on the left-hand side of your comments header area, including the optional post-author name, comments count, and possibly the show/hide comments link. ' . ppString::id( 'blurb_font_inheritance', 'text/link areas', 'font link' ),

		'comments_header_post_interaction_link_font_group' => 'This area gives specific control to the text appearance of the items on the right-hand side of your comments header area, including the "Add a comment" link, and optionally the "Email a Friend" and "Link to this post" links. ' . ppString::id( 'blurb_font_inheritance', 'links', 'font link' ),

		'comments_show_hide_method' => 'Visitors click on a link in your comments area header to show or hide your comments (when applicable). Choose here whether to display this show/hide link as a small arrow icon that turns up and down, or as simple text of your choice.',

		'comments_post_interact_display' => 'Post interaction links ("Add a comment", "Link to this post", and "Email a friend") are displayed on the right side of your comments area header. This area gives you three different options for displaying these links, plus the ability to explicitly set the horizontal spacing between these link items. Settings for the specific options are configurable in the option areas below.',

		'comments_header_addacomment_link_text' => 'On non-single blog pages (like a standard blog home page), there are "Add a Commment" links below each post when comments are enabled.  Here you can change the text of this link, and optionally upload an icon to be shown next to the text, or an image to be used instead of the text, based on your choice in the "Post interaction links options" section directly above.',

		'comments_header_linktothispost_link_include' => 'The "Link to this post" link is a direct link to the single-post, "permalink" page for each WordPress post.  Here you can choose whether or not you want to include this link in your comments header area.  You can also customize the text of this link, and optionally upload an icon to be shown next to the text, or an image to be used instead of the text, based on your choice in the "Post interaction links options" section above.',

		'comments_header_emailafriend_link_include' => 'When clicked, the "Email a friend" link opens the users default email program and starts a new email. The new email will have it\'s subject and body filled in, allowing them to quickly send a link to your post to any of their friends.<hr />When customizing the default email subject and body text, you can also use four special words that will be replaced with the corresponding information from your site or the specific post in question: <code>%post_url%</code>, <code>%post_name%</code>, <code>%site_url%</code>, <code>%site_name%</code><hr />You can also customize the text of this link, and optionally upload an icon to be shown next to the text, or an image to be used instead of the text, based on your choice in the "Post interaction links options" section above.',

		'comments_body_area_bg' => ppString::id( 'blurb_bg_area', 'the comments area (not the comment header).  This is the entire area behind where all of the individual comments are displayed' ),

		'comment_meta_position' => 'This section deals with the names and optional links to comment authors.  They can be displayed inline with the comment, or on their own line above the comment text.  Links to websites can open in a new window, or in the same window.  You can also customize the link appearance, and spacing below the comment author line, if you choose to have it on it\'s own line',

		'comment_timestamp_display' => 'Here you may choose to show or not show the time and date a comment was posted, and also it\'s alignment. Also you can set some basic font customizations for the comment time.  Note: if, further down, you choose to enable alternate comment styling, you may have to customize this comment time and date stamp further for alternate comments.',

		'comment_text_and_link_font_group' => 'This font customization area allows you to control the appearance of the text and links within comments. ' . ppString::id( 'blurb_font_inheritance', 'text areas', 'font' ),

		'comment_tb_padding' => 'Here you can fine-tune the spacing between various elements of your comment body area.',

		'comment_bottom_border_onoff' => 'Add and customize a border between each individual comment.',

		'comment_alt_bg_color' => 'By enabling alternate styling, you can give every other individual comment a different color palette. This produces a nice "striped" effect, and can make your comments easier to read.',

		'comment_byauthor_bg_color' => 'Here you can choose to make any comments you leave on your blog have a unique color palette from any other comments.  This allows your comments back to your readers to stand out of the crowd.',

		'comment_awaiting_moderation_text' => 'If you have turned on comment moderation on the  <a href="' . admin_url( 'options-discussion.php' ) . '">"Settings" > "Discussion"</a> page, your commenters may receive a notice when they comment that their comment is waiting to be approved.  Here you can customize the appearance and text of that message.',

		'fb_comments_enable' => 'Here you can optionally enable Facebook comments integration and customize it\'s basic settings. ' . ppString::id( 'fb_comments_requires_fb_admins' ) . '\<hr />You can choose whether to use <em>only</em> Facebook comments, or to <em>also</em> show traditional WordPress comments not created through Facebook if there are any for a given post.<hr />If you use only Facebook comments, your users may only submit new Facebook comments. If you also show WordPress comments, the standard ProPhoto comment area will be shown if there are non-Facebook comments present for a post, or if you are accepting new comments through Facebook and WordPress.<hr />Try the <em>dark color scheme</em> if your site has a predominantly black or dark background color.',

		// sidebars & footers
		'sidebar' => 'ProPhoto allows you to have a fixed-width sidebar column either on the right or left of your site, effectively making it two columns.  Here you set what side you would like it on. If you do not want a fixed sidebar at all, just don\'t drag any widgets into the "Fixed sidebar" widget area on the widgets page.  All fixed-sidebar content is handled by widgets. Here you can also choose on which page types you want your fixed sidebar to appear.',

		'sidebar_width' => 'Set your sidebar width, as well as fine-tune your sidebar padding and the vertical space between widgets placed in the sidebar.',

		'sidebar_bg' => ppString::id( 'blurb_bg_area', 'your fixed sidebar' ),

		'sidebar_headlines_font_group' => 'If the widgets in your fixed-sidebar have titles, you can customize the text appearance of those titles in this area. ' . ppString::id( 'blurb_font_inheritance', 'titles', 'headline' ),

		'sidebar_text_font_group' => 'This font customization area affects the non-link text of widgets in your fixed sidebar. ' . ppString::id( 'blurb_font_inheritance', 'areas of widget text', 'font' ),

		'sidebar_link_font_group' => 'This font customization area affects the text links within widgets in your fixed sidebar. ' . ppString::id( 'blurb_font_inheritance', 'links', 'font link' ),

		'sidebar_border_switch' => 'Optionally apply and customize a line to divide your sidebar from your content area.',

		'sidebar_widget_sep_img' => 'You can choose here to upload a custom image as a vertical divider below each widget in your fixed sidebar.',

		'sliding_drawer_sidebar_note' => ppString::id( 'blurb_sliding_drawers' ),

		'drawer_default_bg_color' => 'These are some general settings that will apply to the appearance of all of your sliding drawer sidebars including background color, opacity, tab corner rounding, and spacing.  Drawer tabs are the small bits of the drawer that are always visible at the edge of the browser window.',

		'drawer_widget_headlines_font_group' => 'If the widgets in your sliding drawers have titles, you can customize the text appearance of those titles in this area. ' . ppString::id( 'blurb_font_inheritance', 'titles', 'headline' ),

		'drawer_widget_text_font_group' => 'This font customization area affects the non-link text of widgets in your sliding drawers. ' . ppString::id( 'blurb_font_inheritance', 'areas of widget text', 'font' ),

		'drawer_widget_link_font_group' => 'This font customization area affects the text links within widgets in your sliding drawers. ' . ppString::id( 'blurb_font_inheritance', 'links', 'font link' ),

		'drawer_tab_font_group' => 'This text customization area affects the text in the "tabs" of your sliding drawers. Drawer tabs are the small bits of the drawer that are always visible at the edge of the browser window.',

		'drawer_tab_text_*num*' => 'This option section allows you to specifically customize drawer title, width, and colors for this specific drawer',

		'show_ad_banners' => 'Here you can choose to enable the ProPhoto "Ad banners" feature, which is a simple way to add sponsor or advertising banner images to the bottom of your blog, just above the main footer content area.  If you choose to enable ad banners, you can then customize the spacing of the banners and the overall area, as well as the color of the 1 pixel border around banner images',

		'banner*num*' => 'Here you upload a banner message and set a URL for it to link to.  The banners can be any size, although this area usually looks best when you upload all of your banners with the same dimensions',

		'footer_include' => 'The "Footer Area" is the large portion of the bottom of your site that can shows things like archives, categories, RSS links, etc. Here you can choose to include this area or not. Below this, you can also customize the appearance and some of the items in the footer.  For control of what content actually appears in your footer, you will need to manage widgets from the <a href="' . admin_url( 'widgets.php' ) . '">widgets page</a>.',

		'footer_bg' => ppString::id( 'blurb_bg_area', 'footer area' ),

		'footer_btm_cap' => 'This is an optional image displayed below all of your footer content. It will be centered directly beneath your footer, as a bottom "cap" below the footer.',

		'footer_left_padding' => 'Fine-tune the space between your footer content and the left and right edges of your blog, and the space between each column of footer content.',

		'footer_headings_font_group' => 'If the widgets in your footer columns have titles, you can customize the text appearance of those titles in this area. ' . ppString::id( 'blurb_font_inheritance', 'titles', 'headline' ),

		'footer_link_font_group' => 'This font customization area affects the text and links within widgets in your footer area. ' . ppString::id( 'blurb_font_inheritance', 'links', 'font appearance" and "Overall link font' ),

		'custom_copyright' => 'Here you can customize the text at the very bottom of your site, to the left of the ProPhoto attribution links.  If you leave this blank, ProPhoto will show a generic copyright notice with the current year.',

		'link_removal_txn_id' => 'The footer attribution links to ProPhoto and NetRivet (the parent company) are mandatory, as per the conditions of the EULA (End User License Agreement) you agreed to when purchasing.  If you wish to remove them, you must purchase an "Unbranded License" for $99. Once you have done so, enter the transaction ID for the purchase of your Unbranded License into this option area -- the ID will be sent to you in an email, to the address you used when purchasing (often your PayPal address) and will be from the email address noreply@netrivet.com. If you can\'t find it, be sure to check your spam folder.',

		// galleries
		'slideshow_bg_color' => 'Set some general appearance and behavior options for all of your slideshow galleries. The background color will be seen behind images that do not take up the full width and height of the slideshow, and will be the background color for the initial overlay screen and thumbstrip screen, unless you specifically override those colors further down this page.  Control buttons are the initial overlay "play" button image, and all of the control buttons on the thumbstrip, like play/pause, fullscreen, etc.',

		'slideshow_splash_screen_height' => 'Slideshow galleries by default load with a semi-transparent overlay of color across the middle containing a play button, and the gallery title and optional subtitle. Here you can customize the appearance and placement of that overlay. Note: if you set the height of the initial overlay to a low number, it will always be tall enough to wrap around the text and images in your overlay area.',

		'slideshow_title_font_group' => 'Here you can customize the appearance of the titles that appear with the initial overlay of each slideshow gallery. The actual text of each title is set when you create or edit a slideshow from within the post/page create or edit screens.',

		'slideshow_subtitle_font_group' => 'Here you can customize the appearance of any sub-titles that appear with the initial overlay of each slideshow gallery. The actual text of each sub-title is set when you create or edit a slideshow from within the post/page create or edit screens.',

		'slideshow_splash_screen_logo' => 'Here you can upload a custom image to appear in the initial overlay of each slideshow gallery. The image will appear actual size, horizontally centered above your gallery title.',

		'slideshow_start_playing' => 'This section gives you options to control slideshow playback.',

		'slideshow_controls_position' => 'The "thumbstrip" is a strip containing thumbnails of the images in your slideshow gallery, plus buttons to control the playback of the slideshow.  It can appear overlaid over the top of the main slideshow viewing area. The default behavior for when it is overlaid is that the thumbstrip hides automatically after a few seconds of inactivity, and shows again when the user moves the mouse over the slideshow.  You can also change it to always be shown, or customize the amount of time the slideshow waits before hiding the thumbstrip.',

		'slideshow_thumb_paging_animation' => 'When you have more images in your slideshow gallery than can be shown at one time in the thumbstrip area, ProPhoto breaks the thumbnails into "pages" which can be navigated through by means of "forward" and "back" buttons.  In this area you can control the animation of these "pages" of thumbnails.  You can also override the background color of the thumbstrip area, and set the opacity of both the control buttons and the entire thumbstrip area.',

		'slideshow_thumb_size' => 'In this option section you can customize the appearance of the thumbnails shown of each image in the thumbstrip area of your slideshow galleries.  This includes thumbnail size, border, spacing between thumbnails, and opacity of thumbnails when not hovered over, and when being hovered over.',

		'lightbox_thumb_default_size' => 'Here you can adjust the appearance of your lightbox gallery thumbnails, including the specific settings of the mouseover fade effect seen when one of the lightbox thumbnails is hovered over.',

		'lightbox_border_width' => 'When a lightbox gallery thumbnail is clicked, an elegant pop-up window will display that image full-size in the center of the browser window. These options allow you to customize the border around the edge of this display window, as well as the background color of the text area at the bottom of the image display area. For no border, enter a width of <code>0</code>.',

		'lightbox_font_group' => 'At the bottom of the image display window, under each image, the image title and image number (eg. 2 of 7) will be displayed. Here you can adjust the appearance of this text.',

		'lightbox_overlay_color' => 'Under the popup image display window, there is an overlay that covers and partially darkens the entire browser window. Here you can adjust the color and opacity of this overlay, as well as tweak the settings of the image display window transitions when navigating from image to image.',

		 'lightbox_fixed_navigation' => 'Once the image display window is opened, visitors will navigate your images using "prev" and "next" image buttons within the popup image display window.  By default, these "prev" and "next" image buttons only appear when hovered over, but you can change that hear so that they are always visible. You can also adjust their opacity, and the speed at which they fade in and out if they are set to show only when moused-over.',

		'lightbox_loading' => 'During image transitions, the next image will need to be loaded. During the pause while the image is being loaded, this loading image will be displayed in the center of the image display window, so visitors won\'t think something is broken. You can upload a static image here, or you can get upload an animated gif (like the default image). This image will be displayed actual size.',

		'lightbox_close' => 'Upload a custom button that visitors can click to close the popup image display window.',

		'lightbox_next' => 'Upload a custom button that visitors can click to proceed to the next image in your gallery. This button appears on the right side of your image display window. There are additional settings that apply to this button in the "Lightbox overlay image navigation" option area within this tab.',

		'lightbox_prev' => 'Upload a custom button that visitors can click to go to the previous image in your gallery. This button appears on the left side of your image display window. There are additional settings that apply to this button in the "Lightbox overlay image navigation" option area within this tab.',

		'audio*num*' => 'Here you can upload an <code>MP3</code> file for playback in a ProPhoto slideshow gallery.  Only <code>MP3</code> files may be uploaded and used, so convert any different file types to <code>MP3</code> before uploading.<hr />Once an <code>MP3</code> is uploaded, it will be available for selection when customizing a specific ProPhoto slideshow gallery. When you are creating or editing a ProPhoto gallery, go to the "Slideshow options" tab, and there will be a dropdown menu allowing you to select from uploaded <code>MP3s</code>.  All files you have uploaded in this section will be available.<hr />Adding a "song name" will make it much easier to identify which song is which when selecting a song from the dropdown menu for playback in a slideshow.',

		'slideshow_mp*num*_autostart' => 'When set to auto-start, any slideshow music will begin playing as soon as the slideshow begins playing. If set to require a click, the user would have to directly click on the speaker icon button in the slideshow controls area to initiate music playback.<hr />Music can also be set to loop (repeat) continuously when the end of the song is reached, or play once and then stop',

		// grids
		'grid_img_text_below_gutter' => ppString::id( 'blurb_grid_gutter' ),

		'grid_img_text_below_title_link_font_group' => 'This font customization area allows you to customize the grid item titles shown below the grid images for this grid style.',

		'grid_img_rollover_text_gutter' => ppString::id( 'blurb_grid_gutter' ),

		'grid_img_rollover_text_overlay_bg_color' => 'Grids of this type show just an image by default, and when hovered over, an overlay fades in with a solid-colored background and text.  Here you can set the color of that overlay, and the opacity it reaches when hovered over.',

		'grid_img_rollover_text_title_link_font_group' => 'This font customization area allows you to customize the grid item titles, shown in the overlay for this grid style.',

		'grid_img_rollover_text_text_link_font_group' => 'This font customization area allows you to customize the grid item text, shown below the grid item titles in the overlay for this grid style.',

		'grid_article_img_fallback' => 'When a post or page has no images in it, and no "Featured image" set, this image will be used for the creation of the grid item.',

		'grid_category_img' => 'When you create a "Categories grid", this category-specific image will be used as the grid image for it\'s respective category, if present.  If no image is uploaded here, ProPhoto will attempt to load the most recent image uploaded to a post in that category',

		// mobile
		'mobile_enable' => 'ProPhoto has the ability to serve a customizable mobile-friendly version of your site when it detects that a visitor is using a small mobile device.  However, you can also choose to show everyone your standard site.  The non-mobile site will still work on a mobile device, it will just require some pinching and zooming to be read.<br /><br />If you experience problems with your site not loading internal pages, try disabling ajax page loading. Some server configurations and certain plugins interfere with ProPhoto\'s ability to ajax-load and then smoothly transition to the clicked internal page link.  Disabling this option should resolve these sorts of issues.',

		'mobile_logo_use_desktop' => 'The mobile-version of your site can use the same logo as your non-mobile site, or you can choose to upload a mobile-specific logo that would look better at a small screen resolution size.  Here you can also choose whether the mobile logo appears on all mobile pages, or just on certain page types.',

		'mobile_logo' => 'This is the mobile-specific logo for your site. ' . ppString::id( 'mobile_980px_explanation' ),

		'mobile_masthead_use_desktop_settings' => 'The mobile version of your site can use the same masthead settings as your main, non-mobile site, or you can override those settings on this page by setting this option to "set specific mobile masthead settings".',

		'mobile_masthead_display' => ppString::id( 'blurb_masthead_display', '' ),

		'mobile_masthead_slideshow_hold_time' => ppString::id( 'blurb_masthead_slideshow', 'mobile' ),

		'mobile_masthead_image*num*' => 'Your mobile masthead image should be uploaded at 980px wide. ' . ppString::id( 'mobile_980px_explanation' ) . ' Also, be sure to upload all of your mobile masthead images at the same height as the first one you upload. If you wish the mobile slideshow to be taller, re-upload a taller image for the first image, then upload taller images for all of the rest of your masthead images.',

		'mobile_content_bg' => ppString::id( 'blurb_bg_area', 'the content area of your mobile site.  This is most of the mobile site, except the logo, slideshow, and footer' ),

		'mobile_excerpt_list_border_group' => 'On mobile page types where more than one post is displayed, you can choose to have a simple, customizable line separate each individual post excerpt, providing greater visual separation.  For no line, set the width to <code>0</code>.',

		'mobile_button_bg_color' => 'In this area you can set the overall background and border color of buttons on your mobile site.  This would affect previous/next post navigation buttons, comment submission buttons, and footer menu buttons. These choices can be overridden in more specific customization option areas in other tabs within the Mobile customization area.',

		'mobile_font_group' => 'This font customization area allows you to select the main mobile font family and color.  These settings will be inherited by all fonts on your mobile site, unless you override them with more specific settings below.',

		'mobile_link_font_group' => 'This font customization area allows you to customize some aspects of all of the links on your mobile site.  These settings will be inherited by all links on your mobile site, unless you override them with more specific settings below.',

		'mobile_headline_font_group' => 'These font settings apply to all headlines and titles (such as post excerpt titles and single page post titles) throughout your mobile site.  The choices you make here can be overridden by more specific customization options below.',

		'mobile_article_excerpt_title_font_group' => 'These font settings apply to post titles on mobile page types where multiple posts are shown, such as the blog home page, archive, and category pages.  These titles inherit settings from the "Overall headline/title appearance" area above, unless you override those settings here.',

		'mobile_article_excerpt_text_font_group' => 'These font settings apply to post text excerpts, which are the small bit of post text shown on page types where multiple posts are listed on one page. These text excerpts inherit settings from the "Overall font appearance" area above, unless you override those settings here.',

		'mobile_button_font_group' => 'These font settings apply to all mobile buttons (except footer menu buttons), including prev/next posts links, comment form submit button, unless overridden by a more specfic setting.',

		'mobile_article_title_font_group' => 'These font settings apply to post or page titles on single-post pages.  These titles inherit settings from the "Overall headline/title appearance" area above, unless you override those settings here.',

		'mobile_article_meta_below_title_link_font_group' => 'These font settings apply to the information items below the post title on single-post pages.  This information is usually the published date of the post, and the category links.',

		'mobile_article_text_link_font_group' => 'These font settings apply to text and links in the content of your posts and pages on single-post type pages.  The text and links inherit settings from the "Overall font appearance" and "Overall link appearance" areas above, unless you override those settings here.',

		'mobile_comments_area_link_font_group' => 'These font settings apply to all of the text in the comments area and add a comment form.',

		'mobile_comments_area_bg_color' => 'Here you can optionally set a background color for just the overall comments area (where the comments are shown, and where the "add a comment" form is seen).  Any background color set here will override the "Content area background" color and/or image from the "Backgrounds & colors" tab of this page.',

		'mobile_comment_header_bg_color' => 'In the mobile version of your ProPhoto site, every comment has a small comment header indicating the name of the comment author and the time the comment was posted.  This option area lets you set the background color of that comment header, and optionally override the text color for the same.',

		'mobile_comment_bg_color' => 'Here you can set the background color for each individual comment, that is, the text of the comment itself, as well as set the color of the text within each comment.',

		'mobile_comment_inputs_bg_color' => 'In this area, you can set background and text colors for the part of your single-post pages where visitors can leave a comment.  This includes all labels of the mobile comment form (e.g. name, email address, comment).',

		'mobile_post_comment_btn_bg_color' => 'Here you can specifically set a background color, border color, and font color for just the "Post comment" button in the add-a-comment form on your mobile site.  These settings would override any less-specific button settings made elsewhere.',

		'mobile_footer_color_scheme' => 'Select the color scheme for your mobile footer area.',

		'mobile_footer_menu_items' => ppString::id( 'blurb_menu_structure', 'your mobile footer', ' second-level (popup) sub', '' ),

		// site settings
		'google_analytics_code' => 'Google analytics is a free web analytics and tracking software from google. Paste in your tracking code here and that\'s all you need to do -- no plugin or manual pasting of the code into your theme files is necessary.  Your own visits to your blog will not be tracked. It is not recommended to use both Google Analytics and Statcounter at the same time.',

		'statcounter_analytics_code' => 'tatcounter is a free hit-counter service that can be found at <a href="http://www.statcounter.com" target="_blank">www.statcounter.com</a>.  While not as powerful as Google Analytics, it is still widely used by photographers, especially those transferring from Blogger who had previously used Statcounter.  Just paste your statcounter code into this area and you\'re done. No plugin or additional configuration necessary.  Your own visits to your blog will not be counted.  It is not recommended to use both Google Analytics and Statcounter at the same time.',

		'twitter_name' => 'Enter your main Twitter username here and it will automatically be pulled into any ProPhoto Twitter features or widgets, where possible. You can always change this value for each widget instance if you want to use a different or multiple accounts. Note: This info is will only integrate with the built-in ProPhoto  features and widgets, not with any third party ones.',

		'facebook_static_front_page' => 'When Facebook displays a link to your site, either through a user status update or when a certain post is "Liked", a preview of the link URL is shown containing the page title, URL, thumbnail image, and a description.  This area lets you customize the image, title and description for your blog posts home page and your static front page (if you have one).<hr />For links to or "Likes" of individual posts and pages, the image used will be that particular posts "Featured image" (if set), and then the first image in the post if a Featured image is not set.  The title will be the post title, and the description will be the post excerpt.<hr />All these options can be a bit confusing, so be sure to click the help tutorial icon upper left of this option area to get read a full tutorial with screenshots.',

		'fb_home' => 'When Facebook displays a link to your site, either through a user status update or when a certain post is "Liked", a preview of the link URL is shown containing the page title, URL, thumbnail image, and a description.  This area lets you customize the image, title and description for your blog posts home page and your static front page (if you have one).<hr />For links to or "Likes" of individual posts and pages, the image used will be that particular posts "Featured image" (if set), and then the first image in the post if a Featured image is not set.  The title will be the post title, and the description will be the post excerpt.<hr />All these options can be a bit confusing, so be sure to click the help tutorial icon upper left of this option area to get read a full tutorial with screenshots.',


		'like_btn_verb' => 'These settings apply anywhere on your site where you include Facebook "Like" buttons.  You can add like buttons in your <a href="' . ppUtil::customizeUrl( 'content', 'footerz' ) . '">post footer area</a> or as a <a href="' . ppUtil::customizeUrl( 'content', 'cta' ) . '">call to action item</a>. Dark color scheme helps the Like button show up more clearly on sites that have a mostly black or dark content background.<hr />Facebook recommends that your numeric personal ID be incorporated into your site code so that you can administrate and gain insight into Facebook actions that take place on your site.',

		'widget_custom_image_*num*' => 'Uploading an image here will make it available for use with various widgets in your blog\'s <a href="' . admin_url( 'widgets.php' ) . '">widget admin page</a>, like the "ProPhoto Custom Icon" widget and the "ProPhoto Sliding Twitter" widget.',

		'favicon' => ppString::id( 'blurb_favicon' ),

		'apple_touch_icon' => ppString::id( 'blurb_apple_touch_icon' ),

		'maintenance_mode' => 'If you don\'t want people to see your blog while you are in the midst of customizing, activate "Under Construction" mode.  When you do, you will still be able to see your site to check your work, but everyone else will get a temporary "Under Construction" message until you turn this off.  To test that the message is displaying, log out of WordPress (link in upper-right of this screen) and try to view your blog.',

		'backup_reminder' => 'It is critically important that you are backing up your blog on a regular basis. Internet security is a complicated thing and occasionally hackers find a loophole in Wordpress, and we start to see peoples blogs get hacked, and even deleted. The Wordpress team is very good at getting these issues fixed quickly, but by far the best way to protect yourself is to regularly backup both your blog files and your blog database. To help you remember this important step, you can have your blog email you a reminder to backup on a monthly basis. If you don\'t want to get these monthly emails from us, turn off this reminder.',

		'auto_auto_upgrade' => 'On nearly all web-hosting server setups, ProPhoto is able to update itself automatically whenever there is a free update or bugfix available.  Here, you may turn off this auto-updating feature.  It is not recommended that you do so, however, because there is usually never a good reason not to stay up to date.  If you directly edit your theme files to make advanced customizations, you may want to turn this off so those direct edits don\'t get overwritten by an auto-update.  Just know that if you do disable auto-updates and choose to not to stay on the latest version of ProPhoto, we will not provide technical support.<hr />Here you can also generate a secure download link if you ever for some reason also need to download a <code>.zip</code> file of the very latest ProPhoto build.',

		'secure_download_link' => 'Here you can click to generate a secure download link to download a <code>.zip</code> file of the very latest build of ProPhoto.',

		'gd_img_downsizing' => 'When ProPhoto displays an image somewhere in your blog, it checks to see if it is too large be displayed full-size in that area.  If it is too large, ProPhoto creates a new version of the same image sized at the correct size for that area.  This can speed up page load significantly, since your site\'s visitors won\'t have to download images larger than necessary. However, downsized images are slightly lower in quality than the original file, so this can cause some loss in image quality. Loss in image quality is most noticeable in posts when you upload images much larger than the content width of your site. The more ProPhoto has to downsize, the worse the quality gets. Uploading images at a size closer to their display size can mitigate this problem, but if you prefer to disable all image downsizing, you can do so here.<hr />The max-size threshold sets an upper limit for ProPhoto to attempt image downsizing. If the height plus width of the image (in pixels) is larger than the threshold, ProPhoto will not attempt to create a smaller image. Very large images can cause out-of-memory fatal errors, making certain blog pages just stop rendering, and often showing warning messages. If you post a lot of very large collage-type images and your site is not working correctly, or showing PHP fatal "out-of-memory" errors, try reducing this number.',

		'unregistered' => 'This copy of ProPhoto has not yet been registered.  Please click the link below to register with the information that was emailed to you when you purchased. ProPhoto is not usable until you register',

		'registered' => 'Below is the information you used to successfully register this copy of ProPhoto.  Don\'t worry if your email address is no longer valid or not your main address, we do not use that address to contact you, only to verify your original purchase.  Thanks for purchasing ProPhoto!',

		'dev_test_mode' => 'ProPhoto requires registration, and limits the number of active, registered blogs per license to 2.  If you are want to use an additional, unregistered installation of ProPhoto just as a test-site, then turn this option on.  It will allow only you to view your ProPhoto site, and make customizations.  No one except for logged-in administrators will be able to view the site',

		// advanced
		'feedburner' => 'While the built-in WordPress RSS feeds work great, many people choose to transfer their RSS feed to a free Google service called <a href="http://feedburner.google.com/" target="_blank">feedburner</a>.  This gives the benefit of being able to track how many people have subscribed to your feed, add in advanced feed options, and enable features like subscribing to your feed via email.  If you have transferred your feed to feedburner, enter your feedburner feed URL here and anyone who subscribes to your blog RSS feed will use the feedburner feed, not the built-in WordPress feed. Note: if you enter your feedburner URL here, you won\'t have to use the WordPress plugin that the feedburner site recommends you use, ProPhoto will take care of everything',

		'modify_feed_images' => 'While ProPhoto can deter theft of images from your actual blog, images within your RSS feed can\'t be protected in the same way. Here you can choose to remove posted images from your feed, or replace them with smaller images. Modified feeds by default contain a message with a link back to the original post.',

		'seo_disable' => 'Here you can completely disable the ProPhoto SEO (Search Engine Optimization) features if you would rather let a third-party plugin handle your site\'s SEO.',

		'seo_title_home' => 'Search engines use your blog\'s title tags both in evaluating your blog and when displaying content from your blog in search results. Here you can fine-tune the information that is added to your titles based on each page type. To customize your title tags, make use of the following code phrases that are replaced dynamically with the appropriate contextual content:<br /><br /><code>%blog_name%</code> will be replaced by the blog title you entered in your <a href="' . admin_url( 'options-general.php' ) . '">"Settings" > "General"</a> area. <br /><code>%blog_description%</code> will be replaced by the tagline you entered on the same page.  <br /><code>%category_name%</code> will be replaced by the name of the category page that you are on. <br /><code>%post_title%</code> will be replaced by the title of that particular post. <br /><code>%archive_date%</code> will be replaced by the date of that particular archive page.<br /><code>%page_title%</code> will be replaced by the title of that Wordpress page. <br /><code>%search_query%</code> will be replaced by the search string entered in that particular search.',

		'seo_meta_desc' => 'This text is often, but not always, displayed under the link to your site in a search engine query results page.',

		'seo_meta_keywords' => 'These are of dubious value, but may still be relevant in small ways.',

		'noindexoptions' => 'If you have chosen to display full posts instead of excerpts on your category, archive and other pages, then you essentially have the same content in different areas of your blog. Search engines frown on this behavior as spammy and can even penalize your sote for this. A solution is to check those pages here, which will prevent the redundant content from getting indexed by search engines. Only check these options if you have a good understanding of what you are doing.',

		'override_css' => 'If you are skilled and knowledgable with writing CSS, or a ProPhoto support tech instructs you to paste code here, you may use this area to add extra, custom CSS to your site. Please type or paste only valid CSS rules here, as invalid or improper code can cause any custom CSS below the error to not be appled.',

		'insert_into_head' => 'Anything you paste in here gets added directly to your site\'s <code>&lt;head>&lt;/head></code> section.  Be sure you know what you\'re doing before pasting into this area, as mistakes can cause your site to load incorrectly. You can confidently add "meta" code supplied by Google Webmaster tools here.',

		'post_signature_placement' => 'This option area allows you to add text and HTML that will be added below posts and pages where you choose. Anywhere you use any of the following special words exactly: <code>%post_title%</code>, <code>%permalink%</code>, <code>%post_id%</code>, <code>%post_author_name%</code>, <code>%post_author_id%</code>, ProPhoto will swap them out for their actual value for that post or page. You can also create a direct link that scrolls to and opens your contact form by using the url <code>#contact-form</code> in a link. Note: there is no need to use this area for <a href="' . ppUtil::customizeURL( 'content', 'cta' ) . '">"Call to Action" post items</a>, as there is a dedicated feature for that.',

		'custom_js' => 'Advanced users who are familiar with Javascript can use this field to add Javascript to their blog. Anything added here will be put between script tags in the document head.',

		'extra_bg_img_*num*' => 'In this area you can upload a custom background image to be applied with CSS to an area of your ProPhoto site.  Enter a CSS selector to which you want the background image to be attached.',

		'designed_for_prophoto_store' => 'Enable advanced export if you are a member of the ProPhoto designer network and want to export this design for submission to the ProPhoto design store.  Once enabled, you may add your own custom copyright text/link, but you may put only one link in that area. When advanced export is enabled, you will see a new export button for this design in the "Manage Designs" page.',

		'dev_hide_options' => 'Licensed developers who have purchased developer licenses may choose to hide the ProPhoto option areas from the end user, if desired.',

		'translate_password_protected'  => ppString::id( 'blurb_translation' ),
		'translate_by'                  => ppString::id( 'blurb_translation' ),
		'translate_commentform_message' => ppString::id( 'blurb_translation' ),
		'translate_search_results'      => ppString::id( 'blurb_translation' ),
		'translate_archives_monthly'    => ppString::id( 'blurb_translation' ),
		'translate_404_header'          => ppString::id( 'blurb_translation' ),
		'translate_lightbox_image'      => ppString::id( 'blurb_translation' ),
		'translate_mobile_loading'      => ppString::id( 'blurb_translation' ),
		'subscribebyemail_lang'         => ppString::id( 'blurb_translation' ),

	),
);


