<?php
/* ----------------------------------------------------------- */
/* ------------------- BOXY COMMENT LAYOUT ------------------- */
/* ----------------------------------------------------------- */

/* boxy comment layout specific vars */

$boxy_header_fontsize_big   = ppOpt::id( 'comments_header_link_font_size' ) * 1.2;
$boxy_header_fontsize_small = ppOpt::id( 'comments_header_link_font_size' ) * .85;


/* echo out boxy comment CSS */

$css .= <<<CSS
		
/* boxy comments */
.comments-header-left-side-wrap {
	float:none !important;
}
.comments-header div.post-interact-div {
	margin-left:0 !important;
}
.article-comments .comments-header p {
	font-size:{$boxy_header_fontsize_big}px !important;
}
.article-comments .comments-header p.article-byline, 
.article-comments .comments-header p.article-byline a, 
.comments-header p.post-interact a {
	font-size:{$boxy_header_fontsize_small}px !important;
}
.comments-body h3 {
	display:none;
}
.article-comments {
	ppCss::border( 'comments_area' )->decs();
	height:150px;
	margin-left:[~content_margin]px;
	margin-right:[~content_margin]px;
}
.article-comments p {
	margin-bottom:0;
}
.comments-header {
	font-size:14px;
	width:200px;
	ppCss::border( 'comments_area', 'right' )->decs();
	float:left;
	height:150px !important;
	max-height:150px;
	text-align:center;
	display:table;
	#position:relative; 
	overflow:hidden;
	background-color:[~comments_header_bg_color];
}
#content .comments-header-left-side-wrap {
	display:block;
}
* html .comments-header {
	margin-right:-3px;
}
.comments-header-inner {
	#position:absolute;
	#top: 50%;
	#left: 0;
	display:block;
	#width:200px;
	display:table-cell;
	vertical-align:middle;
}
.comments-header-inner-inner {
	#position: relative;
	#top: -50%;
}
.comments-count {
	margin:8px 0 4px 0;
}
.comments-body {
	height:137px;
	border-left:none;
	overflow:auto;
	padding:5px 8px 8px 8px;
}
.comments-body div.pp-comment {
	margin-bottom:0;
}
p.post-interact {
	line-height:1.2em;
}
.post-interact span a {
	padding:3px 0;
	text-decoration:none;
	display:block;
	margin:0px 30px 0 30px;
}
.pp-comment {
	padding: 5px;
}
CSS;
