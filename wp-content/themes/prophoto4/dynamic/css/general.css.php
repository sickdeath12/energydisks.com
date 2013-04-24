<?php
/* ---------------------------------- */
/* -------general custom css--------- */
/* ---------------------------------- */


$css .= ppCss::background( 'blog_bg' )->rule( 'body' );
$css .= ppCss::background( 'blog_bg_inner' )->rule( '#inner-body' );


/* -- overall FONT / LINK styles -- */
$css .= ppCss::font( 'gen' )->rule( 'body, p' );
$css .= ppCss::link( 'gen_link' )->rules( 'body, p' );
$css .= ppCss::font( 'header' )->rule( 'h2, h3, .article-title' );
$paragraphMarginBtm = ppOpt::orVal( 'gen_margin_bottom', '1.2em', 'px' );


/* calculate outer wrap width */
$outer_wrap_width = ppOpt::id( 'blog_width' ) + ppHelper::blogBorderWidth();



/* inner wrapper styles */
$css .= ppCss::background( 'body_bg' )->rule( 'body.single #content-wrap, body.page #content-wrap, .content-bg, body.has-sidebar #content-wrap' );


/* blog border styles */
$css .= ppCss::border( 'blog' )->onlyIf( ppOpt::test( 'blog_border', 'border' ) )->rule( '#inner-wrap' );
if ( ppOpt::test( 'blog_border_visible_sides', 'left_and_right_only' ) ) {
	$css .= '#inner-wrap { border-top-width:0; border-bottom-width:0 }';
}



/* nav below styles */
$css .= ppCss::link( 'nav_below_link' )->withNonLink()->rules( 'p#adjacent-posts-links, ul.paginated-links' );




$css .= <<<CSS
body {
	color:[~gen_font_color];
	font-family:[~gen_font_family];
	text-transform:[~gen_text_transform];
	margin-bottom:0;
}
#inner-body {
	padding:[~blog_top_margin]px 0 [~blog_btm_margin]px 0;
}
p, .pp-slideshow, .pp-lightbox {
	margin-bottom:$paragraphMarginBtm;
}
#outer-wrap-centered {
	width:{$outer_wrap_width}px;
	margin:0 auto;
}
body.has-sidebar #content-wrap .content-bg,
body.single #content-wrap .content-bg,
body.page #content-wrap .content-bg {
	background-color:transparent !important;
	background-image:none !important;
}
#inner-wrap {
	width:[~blog_width]px;
	margin:0 auto;
	overflow:hidden;
}
#logo-wrap {
	ppCss::bgColorDec( 'header_bg_color' );
}
.article-content,
.article-header,
.page-title,
.archive-meta,
.article-meta-bottom,
#content .grid-type-excerpts,
.fb-comments {
	margin-left:[~content_margin]px;
	margin-right:[~content_margin]px;
}
.article-content {
	clear:both;
}
p#adjacent-posts-links,
ul.paginated-links {
	padding: 1.6em [~content_margin]px;
	margin-bottom:0;
}

ul.paginated-links {
	text-align:center;
}
ul.paginated-links li {
	display:inline;
	padding:0 0.5em;
}
ul.paginated-links li a.prev,
ul.paginated-links li a.next {
	margin:0 1.8em;
}
ul.paginated-links li .current {
	text-decoration:underline;
}


.prev-post-link-wrap {
	float:[~older_posts_link_align];
}
.next-post-link-wrap {
	float:[~newer_posts_link_align];
}
.dropshadow-topbottom {
	display:none;
}
CSS;







/* DROPSHADOW */

// Set variables based on whether narrow or wide dropshadow is selected
if ( ppOpt::test( 'blog_border_shadow_width', 'narrow' ) ) {
	$dropshadow_img_start = 'dropshadow_';
	$dropshadow_dimension = pp::num()->blogDropshadowNarrowWidth;
} else {
	$dropshadow_img_start = 'dropshadow_wide_';
	$dropshadow_dimension  = pp::num()->blogDropshadowWideWidth;
}

if ( ppOpt::test( 'blog_border', 'dropshadow' ) ) {

	$css .= <<<CSS
	#main-wrap-outer {
		background: transparent url([~{$dropshadow_img_start}sides.png,theme_img]) repeat-y top left;
	}
	#main-wrap-inner {
		background: transparent url([~{$dropshadow_img_start}sides.png,theme_img]) repeat-y top right;
	}
CSS;
}

if ( ppOpt::test( 'blog_border', 'dropshadow' ) && ppOpt::test( 'blog_border_visible_sides', 'all_four_sides' ) ) {

	$css .= <<<CSS
	.dropshadow-topbottom,
	.dropshadow-topbottom div  {
		display:block;
		height:{$dropshadow_dimension}px;
		overflow:hidden;
	}
	.dropshadow-corner {
		background-image: url([~{$dropshadow_img_start}corners.png,theme_img]);
		width:{$dropshadow_dimension}px;
	}
	.dropshadow-center {
		background-image: url([~{$dropshadow_img_start}topbottom.png,theme_img]);
		background-repeat: repeat-x;
	}
	#dropshadow-top-left {
		float:left;
		background-position:top left;
	}
	#dropshadow-top-center {
		background-position: top left;
	}
	#dropshadow-top-right {
		float:right;
		background-position:top right;
	}
	#dropshadow-bottom-left {
		float:left;
		background-position:0px -{$dropshadow_dimension}px;
	}
	#dropshadow-bottom-center {
		background-position: 0 -{$dropshadow_dimension}px;
	}
	#dropshadow-bottom-right {
		float:right;
		background-position:{$dropshadow_dimension}px -{$dropshadow_dimension}px;
	}
CSS;

}




/* AUDIO PLAYER - HIDDEN  */
if ( ppOpt::test( 'audio_hidden', 'on' ) ) {
	$css .= <<<CSS
	#audio-player {
		height:0;
		text-indent:-9999em;
	}
CSS;
}




/* AD BANNERS */
if ( ppOpt::test( 'show_ad_banners', 'true' ) ) {
	$adMargin = ppOpt::id( 'ad_banners_margin_right', 'int' ) / 2;
	$css .= <<<CSS
	#ad-banners {
		padding:0 [~ad_banners_area_lr_margin]px;
		text-align:center;
		margin-bottom:0
	}
	#ad-banners a {
		display:inline-block;
		line-height:0;
		zoom:1;
	}
	#ad-banners img {
		border:1px solid [~ad_banners_border_color];
		margin-bottom:[~ad_banners_margin_btm]px;
		margin-right:{$adMargin}px;
		margin-left:{$adMargin}px;
	}
CSS;
}




/* BLOG SEPARATION SPLITS */
// menu
$menu_top_splitter = ppOpt::test( 'headerlayout', 'pptclassic' ) ? '0' : ppOpt::id( 'menu_top_splitter' );
$menu_btm_splitter = ppOpt::test( 'headerlayout', 'pptclassic' ) ? '0' : ppOpt::id( 'menu_btm_splitter' );

// bio
$bio_top_splitter = ppOpt::test( 'bio_include', 'yes' ) ? ppOpt::id( 'bio_top_splitter' ) : '0';
$bio_btm_splitter = ppOpt::test( 'bio_include', 'yes' ) ? ppOpt::id( 'bio_btm_splitter' ) : '0';


$css .= <<<CSS
#masthead {
	margin:[~masthead_top_splitter]px 0 [~masthead_btm_splitter]px 0;
}
nav {
	margin:{$menu_top_splitter}px 0 {$menu_btm_splitter}px 0;
}
#inner-wrap #bio {
	margin:{$bio_top_splitter}px 0 {$bio_btm_splitter}px 0;
}
#contact-form {
	margin-bottom:{$menu_btm_splitter}px;
}

.article-wrap, .page-title-wrap {
	margin-bottom:[~post_splitter]px;
}
#bio {
	margin-bottom:[~post_splitter]px;
}
body.archive .article-wrap {
	margin-bottom:[~archive_post_splitter]px;
}
body.has-sidebar .article-wrap, body.has-sidebar .page-title-wrap {
	margin-bottom:0;
}
body.single .article-wrap, body.page .article-wrap {
	margin-bottom:0;
}
CSS;

if ( !ppHelper::logoInMasthead() ) {
	$css .= <<<CSS
	#logo-wrap {
		margin:[~logo_top_splitter]px 0 [~logo_btm_splitter]px 0;
	}
CSS;
}

