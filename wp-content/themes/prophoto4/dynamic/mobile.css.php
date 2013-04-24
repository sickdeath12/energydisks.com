<?php

$css  = '';
$css .= file_get_contents( TEMPLATEPATH . '/dynamic/css/jquery.mobile.css' );
require( TEMPLATEPATH . '/dynamic/css/common.css.php' );
require( TEMPLATEPATH . '/dynamic/css/gallery.css.php' );
require( TEMPLATEPATH . '/dynamic/css/lightbox.css.php' );
require( TEMPLATEPATH . '/dynamic/css/grid.css.php' );



/* fonts and links */
$css .= ppCss::font( 'mobile'          )->rule(  'body .article-list p.ui-li-desc, #mobile-content article, #mobile-content article p, .article-comments, #addcomment' );
$css .= ppCss::link( 'mobile_link'     )->rules( 'body #mobile-content' );
$css .= ppCss::font( 'mobile_headline' )->rule(  'body .article-list h2, h1.article-title' );
$css .= ppCss::font( 'mobile_article_excerpt_title' )->rule( 'body .article-list h2' );
$css .= ppCss::font( 'mobile_article_excerpt_text' )->rule( 'body .article-list p.ui-li-desc' );
$css .= ppCss::font( 'mobile_button' )->rule( '
	body .mobile-prev-next-links .ui-btn-text,
	body #mobile-footer .ui-btn-text,
	body .ui-input-search input,
	body #addcomment .cmt-submit .ui-btn-text'
);
$css .= ppCss::font( 'mobile_article_title' )->rule( 'h1.article-title' );
$css .= ppCss::link( 'mobile_article_meta_below_title_link' )->withNonLink()->rules( '#mobile-content .article-header p' );
$css .= ppCss::link( 'mobile_article_text_link' )->withNonLink()->rules( '#mobile-content article, #mobile-content article p' );
$css .= ppCss::link( 'mobile_comments_area_link' )->withNonLink()->rules( 'body #mobile-content .article-comments, body #mobile-content #addcomment' );
$css .= ppCss::font( 'mobile_comment_header' )->rule( '.comment-meta-above' );
$css .= ppCss::font( 'mobile_comment' )->rule( '.comment-text' );
$css .= ppCss::font( 'mobile_comment_input' )->rule( '#addcomment input, #addcomment textarea' );




/* backgrounds and borders */
$css .= ppCss::background( 'mobile_comments_area_bg' )->rule( '#comments-area' );
$css .= ppCss::background( 'mobile_comment_bg' )->rule( '.comment' );
$css .= ppCss::border( 'mobile_excerpt_list', 'top' )->rule( '.article-list li.ui-li, #adjacent-paged-posts-links' );
if ( ppImg::id( 'mobile_content_bg' )->exists || ppOpt::test( 'mobile_content_bg_color_bind', 'on' ) ) {
	$css .= ppCss::background( 'mobile_content_bg' )->rule( '#mobile-wrap, .article-list li' );
}
if ( ppOpt::test( 'mobile_post_comment_btn_border_color' ) ) {
	$css .= '#mobile-wrap #addcomment .cmt-submit .ui-btn { border-color: [~mobile_post_comment_btn_border_color]; }';
}



$css .= <<<CSS

/* general */
html, body {
	max-width:320px;
}
header, #masthead-image-wrapper {
	line-height:0.5em;
}
body.mobile .ui-body-c {
	background:#fff;
}
#mobile-wrap .ui-content {
	padding:0;
}
.article-header {
	padding:15px 15px 0 15px;
}

/* content */
body.single #mobile-content {
	overflow-x:visible;
}
#mobile-content,
#mobile-content .article-content {
	background:transparent;
}
#mobile-wrap .article-content {
	padding:15px;
	overflow-x:visible;
}
.article-content p {
	margin-bottom:0.75em;
}
.article-content ol,
.article-content ul {
	margin-left:3em;
}
blockquote,
blockquote p,
blockquote div {
	font-style:italic;
}
h2.page-title {
	margin:0.6em;
	text-align:center;
	line-height:1.2em;
}
.archive-meta p {
	padding:0 0.85em 1.1em 0.85em;
	font-size:0.8em;
	line-height:1.25em;
	font-style:italic;
}
.ui-li .ui-btn-inner a.ui-link-inherit,
.ui-li-static.ui-li {
	padding-right:45px;
}
.article-list li.ui-li:first-of-type {
	border-top-width:0;
}
.article-list .ui-li:last-child {
	border-bottom-width:0;
}
body.search-results li.ui-li:first-of-type,
body.archive li.ui-li:first-of-type {
	border-top-width:[~mobile_excerpt_list_border_width]px;
}
div.wp-caption {
	width:auto !important;
}
.article-list {
	padding:15px 15px 0 15px;
}
.article-list li.ui-btn {
	height:82px;
}
.article-list h2.ui-li-heading,
.article-list p.ui-li-desc {
	line-height:1.25em;
}
.article-list .ui-btn-inner {
	border-width:0;
}
.ui-content .ui-listview {
	margin: -15px -15px 0 -15px;
}
header img, .pp-slideshow,
#masthead-img,
#masthead_image img,
.pp-slideshow img,
#logo-img,
.grid-item,
.grid-item img,
.article-content img,
.article-content iframe {
	max-width:100% !important;
	height:auto;
}
#mobile-content img.aligncenter,
#mobile-content img.alignright,
#mobile-content img.alignleft {
	display:block;
	margin:10px auto;
}

/* temporarily removed while we experiment with not constraining mobile
   content images, and see if customers complain - 11/14/11 */

/*html.landscape #mobile-content img.alignright,
html.landscape #mobile-content img.aligncenter,
html.landscape #mobile-content img.alignleft {
	max-height:290px;
	width:auto;
}*/

.pp-slideshow .blankOverlay {
	display:none;
}
.initialOverlay h3, .initialOverlay h4 {
	font-size:20px;
}





/* post header */
.article-header .article-title {
	margin:0;
	line-height:1.2em;
	margin-bottom:0.1em;
}
.article-header p {
	margin:0;
	font-size:0.8em;
	line-height:1.35em;
}


/* adjacent post links */
body #mobile-content .mobile-prev-next-links a:link,
body #mobile-content .mobile-prev-next-links a:visited,
#addcomment .cmt-submit .ui-btn {
	color:#444444;
	text-decoration:none;
	border-color:[~mobile_button_border_color];
	background-image:none;
	background-color:transparent;
}
.mobile-prev-next-links .ui-btn-inner,
.cmt-submit .ui-btn-inner {
	border-top-width:0;
	background-color:[~mobile_button_bg_color];
}
.cmt-submit .ui-btn-inner {
	[~mobile_post_comment_btn_bg_color,bgcolordec]
}
#mobile-wrap #addcomment .cmt-submit .ui-btn-text {
	[~mobile_post_comment_btn_font_color,colordec]
}
.mobile-prev-next-links .ui-block-b {
	float:right;
}
.ui-btn-inner {
	padding:0.4em 15px;
}
.mobile-prev-next-links .ui-btn-text,
#mobile-footer .ui-btn-text,
.ui-input-search input {
	font-family:[~mobile_headline_font_family];
}



/* comments */
#comments-area {
	padding:15px 15px 10px 15px;
}
.comment {
	margin:8px 0 5px 0;
}
.comments-count {
	margin-top:8px;
	margin-bottom:4px;
}
.comments-count span {
	display:none;
}
.comment-text {
	font-size:12px;
	line-height:1.2em;
}
.comment-author {
	font-weight:bold;
}
.comment-author span {
	display:none;
}
.comment-time {
	float:right;
	font-size:11px;
	font-style:italic;
	line-height:1.9em;
}
.comment-text {
	padding:0 5px;
	margin-top:6px;
}
.comment-text p {
	margin:0 0 6px 0;
}
.comment-meta-above {
	padding:2px 5px 3px 5px;
	background:[~mobile_comment_header_bg_color];
}
.avatar {
	float:left;
	margin:8px 0 0 0;
}
.with-avatars .comment-text {
	margin-left:45px;
}
#addcomment-error {
	display:none;
}
form#add-comment p {
	margin:10px 0 0 0;
}
form#add-comment label.ui-input-text {
	width:auto;
	display:inline;
	margin-right:0;
	padding-right:0;
}
form#add-comment input.ui-input-text,
form#add-comment textarea.ui-input-text {
	font-size:14px;
	padding:0.3em;
	background-color:[~mobile_comment_inputs_bg_color];
}
form#add-comment textarea {
	height:90px;
	width:96%;
}
#comment-notes {
	font-size:12px;
	font-style:italic;
}


/* footer */
#mobile-footer {
	padding:0.85em 0 0.5em 0;
}
#mobile-footer.ui-bar-a a .ui-btn-text,
#mobile-footer.ui-bar-b a .ui-btn-text {
	color:#fff;
}
#mobile-footer.ui-bar-c a .ui-btn-text,
#mobile-footer.ui-bar-d a .ui-btn-text,
#mobile-footer.ui-bar-e a .ui-btn-text {
	color:#111;
}
#mobile-footer .ui-btn-inner {
	line-height:1.2em;
}
.mobile-search {
	margin:0.5em 5px;
}
.mobile-search input {
	color:#fff;
	padding:0.4em 0 0.4em 1.8em !important;
	width:90% !important;
}
.min-width-480px .ui-input-search {
	width:auto;
	display:block;
}
.ui-input-search {
	padding:0;
	width:99%;
}
.min-width-480px .ui-select {
	width:auto;
	display:block;
}
.ui-dialog .ui-header {
	min-height:2.25em;
}
.ui-select .ui-btn-icon-right .ui-btn-inner {
	padding:0.4em 35px;
}
#mobile-user-copyright {
	text-align:center;
	color:#555;
	font-size:11px;
	line-height:15px;
	font-weight:400;
	margin:25px 45px 15px 45px;
}
#mobile-user-copyright a:link,
#mobile-user-copyright a:visited {
	text-decoration:none;
}
#mobile-user-copyright a:hover {
	text-decoration:underline;
}
#mobile-user-copyright a,
#mobile-user-copyright a:link,
#mobile-user-copyright a:visited {
	color:#333;
}
.ui-bar-a #mobile-user-copyright {
	color:#888;
}
.ui-bar-a #mobile-user-copyright a,
.ui-bar-a #mobile-user-copyright a:link,
.ui-bar-a #mobile-user-copyright a:visited {
	color:#9a9a9a;
}
.ui-bar-b #mobile-user-copyright {
	color:#fff;
}
.ui-bar-b #mobile-user-copyright a,
.ui-bar-b #mobile-user-copyright a:link,
.ui-bar-b #mobile-user-copyright a:visited {
	color:#333;
}


body.search .article-header p,
body.search #adjacent-posts-links {
	display:none;
}
#s-no-results {
	margin-top:1em;
}


/* override jquery mobile css */
.ui-bar-a, .ui-body-a, .ui-btn-up-a, .ui-btn-hover-a, .ui-btn-down-a,
.ui-bar-b, .ui-body-b, .ui-btn-up-b, .ui-btn-hover-b, .ui-btn-down-b,
.ui-bar-c, .ui-body-c, .ui-btn-up-c, .ui-btn-hover-c, .ui-btn-down-c,
.ui-bar-d, .ui-body-d, .ui-btn-up-d, .ui-btn-hover-d, .ui-btn-down-d,
.ui-bar-e, .ui-body-e, .ui-btn-up-e, .ui-btn-hover-e, .ui-btn-down-e,
.ui-btn-active {
	text-shadow:none;
}
.ui-icon, .ui-icon-searchfield:after {
	background-image: url(http://code.jquery.com/mobile/1.0rc1/images/icons-18-white.png);
}
.ui-icon-alt {
	background-image: url(http://code.jquery.com/mobile/1.0rc1/images/icons-18-black.png);
}
@media only screen and (-webkit-min-device-pixel-ratio: 1.5),
       only screen and (min--moz-device-pixel-ratio: 1.5),
       only screen and (min-resolution: 240dpi) {

	.ui-icon-plus, .ui-icon-minus, .ui-icon-delete, .ui-icon-arrow-r,
	.ui-icon-arrow-l, .ui-icon-arrow-u, .ui-icon-arrow-d, .ui-icon-check,
	.ui-icon-gear, .ui-icon-refresh, .ui-icon-forward, .ui-icon-back,
	.ui-icon-grid, .ui-icon-star, .ui-icon-alert, .ui-icon-info, .ui-icon-home, .ui-icon-search,
	.ui-icon-checkbox-off, .ui-icon-checkbox-on, .ui-icon-radio-off, .ui-icon-radio-on {
		background-image: url(http://code.jquery.com/mobile/1.0rc1/images/icons-36-white.png);
	}
	.ui-icon-alt {
		background-image: url(http://code.jquery.com/mobile/1.0rc1/images/icons-36-black.png);
	}
}
.ui-icon-loading {
	background-image: url(http://code.jquery.com/mobile/1.0rc1/images/ajax-loader.png);
}
.ui-icon-searchfield {
	background-repeat:no-repeat;
	background-position:8px 9px;
}
CSS;



$css .= strip_tags( ppOpt::id( 'override_css' ) );


return $css;
