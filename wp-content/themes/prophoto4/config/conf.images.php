<?php

$configArray = array(

	// these have defaults
	'lightbox_close'            => 'lightbox-btn-close.gif',
	'lightbox_next'             => 'lightbox-btn-next.gif',
	'lightbox_prev'             => 'lightbox-btn-prev.gif',
	'lightbox_loading'          => 'lightbox-ico-loading.gif',
	'watermark'                 => 'watermark.png',
	'lazyload_loading'          => 'ajaxLoadingSpinner.gif',
	'unit_test_1'               => 'nodefaultimage.gif',
	'grid_article_img_fallback' => 'notepaper.jpg',

	// no defaults
	'fallback'                                  => '',
	'comments_header_bg'                        => '',
	'bio_inner_bg'                              => '',
	'bio_bg'                                    => '',
	'post_bg'                                   => '',
	'page_bg'                                   => '',
	'comments_body_area_bg'                     => '',
	'logo'                                      => '',
	'post_sep'                                  => '',
	'blog_bg'                                   => '',
	'blog_bg_inner'                             => '',
	'apple_touch_icon'                          => '',
	'bio_separator'                             => '',
	'biopic1'                                   => '',
	'bio_signature'                             => '',
	'slideshow_splash_screen_logo'              => '',
	'sidebar_bg'                                => '',
	'sidebar_widget_sep_img'                    => '',
	'comments_header_linktothispost_link_icon'  => '',
	'comments_header_addacomment_link_icon'     => '',
	'comments_header_emailafriend_link_icon'    => '',
	'comments_header_linktothispost_link_image' => '',
	'comments_header_addacomment_link_image'    => '',
	'comments_header_emailafriend_link_image'   => '',
	'post_header_separator'                     => '',
	'body_bg'                                   => '',
	'footer_bg'                                 => '',
	'footer_btm_cap'                            => '',
	'contact_bg'                                => '',
	'primary_nav_menu_bg'                       => '',
	'like_btn_homepage_image'                   => '',
	'fb_home'                                   => '',
	'favicon'                                   => '',
	'logo_swf'                                  => '',
	'masthead_custom_flash'                     => '',
	'design_thumb'                              => '',
	'facebook_static_front_page'                => '',
	'unit_test_2'                               => '',
	'visual_test_content'                       => '',
);
for ( $i = 1; $i <= pp::num()->maxAdBanners; $i++ ) {
	$configArray['banner'.$i] = '';
}
for ( $i = 1; $i <= pp::num()->maxMastheadImages; $i++ ) {
	$configArray['masthead_image'.$i] = '';
}
for ( $i = 2; $i <= pp::num()->maxBioImages; $i++ ) {
	$configArray['biopic'.$i] = '';
}
for ( $i = 1; $i <= pp::num()->maxCustomWidgetImages; $i++ ) {
	$configArray['widget_custom_image_'.$i] = '';
}
for ( $i = 1; $i <= pp::num()->maxAudioUploads; $i++ ) {
	$configArray['audio'.$i] = '';
}
for ( $i = 1; $i <= pp::num()->maxExtraBgImgs; $i++ ) {
	$configArray['extra_bg_img_'.$i] = '';
}
