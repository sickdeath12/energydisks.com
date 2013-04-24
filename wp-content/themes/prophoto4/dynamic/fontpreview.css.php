<?php
/* ----------------------------------------------------------------- */
/* -- dynamic css for initial states of admin font preview groups -- */
/* ----------------------------------------------------------------- */

$previewCss = '';





/* global stuff */
$previewCss .= ppCss::background( 'body_bg' )->rule( 
	'#tab-section-fonts-link .font-preview,
	 #tab-section-bio-link .font-preview,
	 #tab-section-content-link .font-preview,
	 #tab-section-sidebar #subgroup-fixed .font-preview' 
);
$previewCss .= ppCss::adminPreviewArea( 'gen', 
	'#tab-section-fonts-link    .font-preview,
	 #tab-section-bio-link      .font-preview,
	 #tab-section-content-link  .font-preview,
	 #tab-section-comments-link .font-preview,
	 #tab-section-sidebar-link  .font-preview,
	 #tab-section-footerz-link  .font-preview'
);
$previewCss .= '.font-preview .margin-bottom { margin-bottom:' . ppOpt::orVal( 'gen_margin_bottom', '1.2em', 'px' ) . '; }';
$previewCss .= ppCss::adminPreviewArea( 'header', 
	'#header-font-preview, 
	 #bio_header-font-preview, 
	 #post_title_link-font-preview a, 
	 #sidebar_headlines-font-preview, 
	 #drawer_widget_headlines-font-preview'
);

for ( $i = 1; $i <= pp::num()->maxCustomFonts; $i++ ) { 
	$previewCss .= ppFontUtil::fontFaceCss( 'custom_font_' . $i );
}



/* fonts tab */
$previewCss .= ppCss::adminPreviewArea( 'gen' );
$previewCss .= ppCss::adminPreviewArea( 'gen_link', '
	#tab-section-fonts-link    .font-preview,
	#tab-section-bio-link      .font-preview,
	#tab-section-content-link  .font-preview,
	#tab-section-comments-link .font-preview,
	#tab-section-sidebar-link  .font-preview,
	#tab-section-footerz-link  .font-preview'
);
$previewCss .= ppCss::adminPreviewArea( 'header' );
$previewCss .= '#gen-font-preview { padding:8px ' . max( 10, ppOpt::id( 'content_margin' ) ) . 'px; }';




/* bio tab */
$previewCss .= ppCss::background( 'bio_bg' )->rule( '#bio_header-font-preview, #bio_para-font-preview, #bio_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'bio_header' );
$previewCss .= ppCss::adminPreviewArea( 'bio_para', '#bio_para-font-preview, #bio_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'bio_link' );





/* content tab */
$previewCss .= ppCss::adminPreviewArea( 'post_title_link' );

// post title meta
$previewCss .= ppCss::adminPreviewArea( 'post_header_meta_link' );
$previewCss .= ppCss::font( 'post_header_meta_link' )->rule( '#post_header_meta_link-font-preview' );

// post title date/time
$previewCss .= ppCss::font( 'post_header_meta_link' )->rule( '#post_header_postdate-font-preview' );
$previewCss .= ppPostHeader::advancedDateCss( '#post_header_postdate-font-preview span' );
$previewCss .= ppCss::adminPreviewArea( 'post_header_postdate' );

// post text & links
$previewCss .= ppCss::adminPreviewArea( 'post_text', '#post_text-font-preview, #post_text_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'post_text_link' );

// post footer
$previewCss .= ppCss::adminPreviewArea( 'post_footer_meta_link' );
$previewCss .= ppCss::font( 'post_footer_meta_link' )->rule( '#post_footer_meta_link-font-preview' );

$previewCss .= ppCss::adminPreviewArea( 'nav_below_link' );
$previewCss .= ppCss::adminPreviewArea( 'archive_h2' );
$previewCss .= ppCss::link( 'call_to_action_link' )->withNonLink()->rules( '#call_to_action_link-font-preview' );





/* comments tab */
// all individual comment background preview areas
$previewCss .= <<< CSS
#comment_author_link-font-preview,
#comment_timestamp-font-preview,
#comment_text_and_link-font-preview {
	[~comment_bg_color,bgcolordec]
	ppCss::background( 'comments_body_area_bg' )->decs();
	background-image:none;
}
CSS;

$previewCss .= ppCss::background( 'comments_header_bg' )->rule( '#comments_header_link-font-preview, #comments_header_post_interaction_link-font-preview' );

// comments header postauthor/show/hide
$previewCss .= ppCss::font( 'comments_header_link' )->rule( '#comments_header_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'comments_header_link' );

// post interaction links
$previewCss .= ppCss::adminPreviewArea( 'comments_header_post_interaction_link', '', 'a|margin-right:15px' );

// comment author link
$previewCss .= ppCss::adminPreviewArea( 'comment_text_and_link', '#comment_author_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'comment_author_link' );

// comment timestamp
$previewCss .= ppCss::adminPreviewArea( 'comment_timestamp' );

// individual comments
$previewCss .= ppCss::adminPreviewArea( 'comment_text_and_link' );





/* fixed sidebar */
$previewCss .= <<<CSS
#subgroup-fixed .font-preview {
	width:[~sidebar_width]px !important;
}
CSS;
$previewCss .= ppCss::background( 'sidebar_bg' )->rule( '#subgroup-fixed .font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'sidebar_headlines' );
$previewCss .= ppCss::adminPreviewArea( 'sidebar_text' );
$previewCss .= ppCss::adminPreviewArea( 'sidebar_link' );





/* sliding drawers */
// sidebar background
$drawer_tab_width = ppOpt::id( 'drawer_tab_font_size' ) * 2;
$previewCss .= "\n\n\n/* sidebar context */\n";
$previewCss .= <<<CSS
#subgroup-drawer .font-preview {
	background-color:[~drawer_default_bg_color];
	width:[~drawer_content_width_1]px;
}
#drawer_tab-font-preview {
	border-color:[~drawer_default_bg_color];
	-moz-border-radius-topright:10px;
	-moz-border-radius-bottomright:10px;
	-webkit-border-top-right-radius:10px;
	-webkit-border-bottom-right-radius:10px;
	text-align:center;
	line-height:1em;
	padding-left:0 !important;
	padding-right:0 !important;
	width:{$drawer_tab_width}px !important;
}
CSS;
$previewCss .= ppCss::adminPreviewArea( 'drawer_widget_headlines' );
$previewCss .= ppCss::adminPreviewArea( 'drawer_widget_text' );
$previewCss .= ppCss::adminPreviewArea( 'drawer_tab' );
$previewCss .= ppCss::adminPreviewArea( 'drawer_widget_text', '#drawer_widget_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'drawer_widget_link' );





/* footer tab */
$previewCss .= ppCss::background( 'footer_bg' )->rule( '#tab-section-footerz-link .font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'footer_headings' );
$previewCss .= ppCss::font( 'footer_link' )->rule( '#footer_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'footer_link' );





/* galleries tab */
$slideshowBgColor = ppOpt::cascade( 'slideshow_splash_screen_color', 'slideshow_bg_color' );
$previewCss .= <<<CSS
#subgroup-slideshow .font-preview {
	font-weight:400;
	text-transform:none;
	font-style:normal;
	text-align:center;
	background-color:$slideshowBgColor;
}
#lightbox-font-preview {
	line-height:1.5em;
	background-color:[~lightbox_bg_color];
}
CSS;

$previewCss .= ppCss::adminPreviewArea( 'slideshow_title', '#subgroup-slideshow .font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'slideshow_subtitle', '#subgroup-slideshow #slideshow_subtitle-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'lightbox' );





/* mobile */
$previewCss .= ppCss::adminPreviewArea( 'mobile', '
	#mobile-font-preview,
	#mobile_link-font-preview,
	#mobile_article_excerpt_text-font-preview,
	#mobile_article_meta_below_title_link-font-preview,
	#mobile_article_text_link-font-preview,
	#mobile_comments_area_link-font-preview'
);
$previewCss .= ppCss::adminPreviewArea( 'mobile_link', '
	#mobile_link-font-preview,
	#mobile_article_meta_below_title_link-font-preview,
	#mobile_article_text_link-font-preview,
	#mobile_comments_area_link-font-preview'
);
$previewCss .= ppCss::adminPreviewArea( 
	'mobile_headline', 
	'#mobile_headline-font-preview, #mobile_article_excerpt_title-font-preview, #mobile_article_title-font-preview'
);
$previewCss .= ppCss::adminPreviewArea( 'mobile_article_excerpt_title' );

$previewCss .= ppCss::adminPreviewArea( 'mobile_article_excerpt_text' );

$previewCss .= <<<CSS
#mobile_button-font-preview {
	font-family:[~mobile_font_family];
}
#tab-section-mobile-link #subgroup-fonts #mobile_button-font-preview {
	background-color:[~mobile_button_bg_color];
}
CSS;
$previewCss .= ppCss::adminPreviewArea( 'mobile_button', '#mobile_button-font-preview' );

$previewCss .= ppCss::adminPreviewArea( 'mobile_article_title' );

$previewCss .= ppCss::font( 'mobile_article_meta_below_title_link' )->rule( '#mobile_article_meta_below_title_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'mobile_article_meta_below_title_link' );

$previewCss .= ppCss::font( 'mobile_article_text_link' )->rule( '#mobile_article_text_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'mobile_article_text_link' );

$previewCss .= ppCss::font( 'mobile_comments_area_link' )->rule( '#mobile_comments_area_link-font-preview' );
$previewCss .= ppCss::adminPreviewArea( 'mobile_comments_area_link' );

$previewCss .= ppCss::background( 'mobile_content_bg' )->rule( '#tab-section-mobile-link #subgroup-fonts .font-preview' );



return $previewCss;

