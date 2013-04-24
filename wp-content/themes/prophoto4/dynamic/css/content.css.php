<?php 
/* ------------------------------------------------- */
/* ------ dynamic styles for post content ---------- */
/* ------------------------------------------------- */
$prophoto_info = ppUtil::siteData();




/* post and page bg color, image */
$css .= ppCss::background( 'post_bg' )->rule( '.article-wrap-inner' );
$css .= "body.page .article-wrap-inner {\n\tbackground-image:none;\n}";
$css .= ppCss::background( 'page_bg' )->rule( 'body.page .article-wrap-inner' );




/* post content TEXT & IMAGES */
// post text
$css .= ppCss::font( 'post_text' )->rule( '.article-content, .article-content p, .article-content li' );
$css .= ppCss::link( 'post_text_link' )->rules( '.article-content, .article-content p, .article-content li' );
$general_font_size   = ppOpt::id( 'gen_font_size' );
$post_li_line_height = ppOpt::id( 'gen_line_height' ) * .85;

// blockquote
$blockquote_fontsize = intval( ppOpt::cascade( 'post_text_font_size', 'gen_font_size' ) * 0.85 );
$post_blockquote_border_color = ppOpt::test( 'post_text_font_color' ) ? ppOpt::id( 'post_text_font_color' ) : ppOpt::id( 'gen_font_color' );
	
// image borders
$css .= ppCss::border( 'post_pic' )
			->onlyIf( ( ppOpt::id( 'post_pic_border_width', 'int' ) > 0 ) )
			->rule( 'body .article-content img, body article-content .gallery img, body .pp-img-protect' );

// image margins
$post_pic_margin_top    = ppOpt::id( 'post_pic_margin_top' );
$post_pic_margin_bottom = ppOpt::id( 'post_pic_margin_bottom' );


// lazyload throbber
$lazyloadThrobberImgURL = ppImg::id( 'lazyload_loading' )->url;




// output CSS
$css .= <<<CSS
.article-content li {
	font-size: {$general_font_size}px;
	line-height: {$post_li_line_height}em;
	margin-bottom: 0.7em;
	margin-left: 3em;
}
.article-content img, 
.article-content .gallery img, 
.pp-img-protect {
	border:solid 0px #fff;
	margin-top: {$post_pic_margin_top}px;
	margin-bottom: {$post_pic_margin_bottom}px;
}
.pp-lightbox .pp-img-protect {
	margin:0;
}
#main-inner-wrap #content .article-content .p4-image-protect img {
	border:solid 0px #fff !important;
	margin-top:0 !important;
	margin-bottom:0 !important;
}
blockquote {
	padding-left:.8em;
	margin-left:3.2em;
	border-left: 1px dotted {$post_blockquote_border_color};
}
.article-content blockquote p {
	font-size: {$blockquote_fontsize}px;
}
.pp-post-sig {
	clear:both;
}
.nav-ajax-receptacle .pp-post-sig,
.nav-ajax-receptacle .pp-fb-like-btn-wrap {
	display:none;
}
#content .article-content img.lazyload-loading {
	background:transparent url($lazyloadThrobberImgURL) no-repeat center center;
	opacity:[~lazyload_loading_opacity,%];
	box-shadow:none !important;
	border-width:0 !important;
}

/* center fullsize excerpt images */
body.excerpted-posts .pp-excerpt-img-fullsize,
body.excerpted-posts .pp-img-protect-excerpt-img-fullsize {
	display:block;
	margin-left:auto;
	margin-right:auto;
}


/* image protection */
.pp-img-protect {
	position:relative;
	line-height:0.5em;
}
#content-wrap #content .pp-img-protect .pp-overlay,
#primary-nav-ajax-receptacle .pp-img-protect .pp-overlay,
#secondary-nav-ajax-receptacle .pp-img-protect .pp-overlay {
	position:absolute;
	top:0;
	left:0;
	margin:0 !important;
	padding:0 !important;
	border-width:0 !important;
}
#content-wrap #content .article-content .pp-img-protect img {
	border:solid 0px #fff !important;
	margin-top:0;
	margin-bottom:0;
}
CSS;



/* IMAGE DROPSHADOWS */
if ( ppOpt::test( 'post_pic_shadow_enable', 'true' ) ) {
	$shadow_color = ppOpt::id( 'post_pic_shadow_color' );
	$shadow_blur = ppOpt::id( 'post_pic_shadow_blur' );
	$shadow_vert_offset = ppOpt::id( 'post_pic_shadow_vertical_offset' );
	$shadow_horiz_offset = ppOpt::id( 'post_pic_shadow_horizontal_offset' );
	$shadow = "{$shadow_horiz_offset}px {$shadow_vert_offset}px {$shadow_blur}px $shadow_color";
	$css .= "\n" . <<<CSS
	.article-content img,
	.article-content .pp-img-protect {
		-moz-box-shadow:$shadow;
		-webkit-box-shadow:$shadow;
		box-shadow:$shadow;
	}
	.article-content .pp-img-protect img,
	.article-content img.wp-smiley,
	.article-content .pp-lightbox img,
	.article-content .no-dropshadow-imgs img,
	.article-content img.no-dropshadow,
	.article-content .sociable img,
	.article-content .pp-post-sig img {
		-moz-box-shadow:none;
		-webkit-box-shadow:none;
		box-shadow:none;
	}
	.pp-img-protect {
		clear:both;
	}
CSS;
}


/* EXCERPT IMAGES */
if ( ppOpt::test( 'show_excerpt_image', 'true' ) ) {
	if ( !ppOpt::test( 'excerpt_image_size', 'fullsize' ) ) {
		$excerpt_img_align = ( ppOpt::test( 'excerpt_image_position', 'before_text' ) ) ? 'left' : 'right';
		$excerpt_img_align_reverse = ( $excerpt_img_align == 'right' ) ? 'left' : 'right';
		$small_thumb_width = pp::wp()->imgThumbWidth;
		$medium_thumb_width = pp::wp()->imgMedWidth;
		$css .= <<<CSS
		body.excerpted-posts a.img-to-permalink {
			margin-top:0;
			margin-bottom:1.5em;
			margin-{$excerpt_img_align_reverse}: 1.5em;
			margin-{$excerpt_img_align}: 0;
			float:{$excerpt_img_align};
		}
		body.excerpted-posts .article-content .pp-img-protect,
		body.excerpted-posts .article-content img {
			float:{$excerpt_img_align};
			margin: 0 !important;
		}
		.pp-excerpt-img-thumbnail {
			height:auto;
			max-width:{$small_thumb_width}px !important;
		}
		.pp-excerpt-img-medium {
			height:auto;
			max-width:{$medium_thumb_width}px !important;
		}
CSS;
	}
	if ( ppOpt::test( 'excerpt_image_size', 'fullsize' ) && ( ppOpt::test( 'excerpt_image_position', 'before_text' ) )  ) {
		$css .= <<<CSS
		body.excerpted-posts .pp-excerpt-img {
			margin-top:0;
		}	
CSS;
	}
}



/* post footer meta text/link */
$css .= ppCss::link( 'post_footer_meta_link' )->withNonLink()->rules( '.article-meta-bottom' );


/* archive-type pages */
$css .= ppCss::font( 'archive_h2' )->rule( 'h2.page-title' );
$archive_h2_margin_below = ppOpt::orVal( 'archive_h2_margin_bottom', '1.5em', 'px' );

$css .= <<<CSS
body.category .page-title,
body.tag .page-title {
	margin-bottom:0;
}
.archive-meta {
	width:75%;
	font-style: italic !important;
	padding:1em 0 0 0;
}
.archive-meta p {
	margin-bottom:0;
}
.page-title-wrap {
	padding: {$archive_h2_margin_below} 0;
}

CSS;






/* POST FOOTER area: spacing, seperator lines, separator images */
$padding_below_post = $post_footer_height = $last_post_footer_height = intval( ppOpt::id( 'padding_below_post' ) );

// using an uploaded image as post divider
$post_sep_align = $bg_image_css = '';
if ( ppOpt::test( 'post_divider', 'image' ) && ppImg::id( 'post_sep' )->exists ) {
	$post_footer_height += ppImg::id( 'post_sep' )->height;
	$bg_image_css = 'background-image: url(' . ppImg::id( 'post_sep' )->url . ');';
	$last_post_footer_height = $padding_below_post;
	$post_sep_align = ppOpt::id( 'post_sep_align' );
	

// css line as post divider
} else if ( ppOpt::test( 'post_divider', 'line' ) ) {
	$css .= ppCss::border( 'post_sep', 'bottom' )->rule( '.article-footer' );
}


// output the css
$css .= <<<CSS
.article-footer {
	background-repeat:no-repeat;
	background-position:bottom $post_sep_align;
	$bg_image_css
	height:{$post_footer_height}px;
}
.last-post .article-footer,
body.single .article-footer,
body.page .article-footer,
body.post .article-footer,
body.archive .last-post .article-footer {
	background-image:none;
	border-bottom-width:0;
	height:{$last_post_footer_height}px;
}
CSS;

// if archive-ish pages are set different
if ( ppOpt::test( 'archive_post_divider', 'line' ) ) {
	$css .= ppCss::border( 'archive_post_sep', 'bottom' )->rule( 'body.archive .article-footer, body.search-results .article-footer' );
	$archive_post_footer_height = ppOpt::id( 'archive_padding_below_post' );
	$css .= <<<CSS
	body.archive .article-footer,
	body.search-results .article-footer {
		background-image:none;
		height:{$archive_post_footer_height}px;
	}
	body.archive .last-post .article-footer {
		height:{$archive_post_footer_height}px;
	}
CSS;
}




/* call to action */
if ( ppOpt::test( 'call_to_action_enable', 'true' ) ) {
	
	$css .= ppCallToAction::heightAlignCss();
	
	$css .= ppCss::link( 'call_to_action_link' )->withNonLink()->rules( '.call-to-action-wrap' );

	$css .= <<<CSS
	.call-to-action-wrap {
		text-align:[~call_to_action_items_align];
		line-height:0.75em;
		margin:[~call_to_action_area_top_padding]px [~content_margin]px [~call_to_action_area_btm_padding]px [~content_margin]px;
	}
	.call-to-action-wrap .item {
		vertical-align:top;
		display:inline-block;
		margin-right:[~call_to_action_items_lr_spacing]px;
		margin-bottom:[~call_to_action_items_tb_spacing]px;
	}
	.call-to-action-wrap .sep {
		vertical-align:top;
		margin-right:[~call_to_action_items_lr_spacing]px;
	}
	.call-to-action-wrap .item:last-child {
		padding-right:0;
	}
	.call-to-action-wrap .item img {
		margin:0;
		padding:0;
	}
	.call-to-action-wrap .fb_iframe_widget iframe {
		/* make js-generated Facebook iframes behave */
		vertical-align:baseline;
	}
CSS;

	if ( ppOpt::test( 'call_to_action_separator', 'image' ) && ppImg::id( 'call_to_action_separator_img' )->exists ) {
		$sep = ppImg::id( 'call_to_action_separator_img' );
		$css .= <<<CSS
		.call-to-action-wrap .sep {
			display:inline-block;
			background:url({$sep->url}) no-repeat top left;
			width:{$sep->width}px;
			height:{$sep->height}px;
		}
CSS;
	}
	
}




