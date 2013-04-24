<?php
/* --- comments area ---- */



/* load correct layout-specific comment styles */
require( TEMPLATEPATH . '/dynamic/css/' .  ppOpt::id( 'comments_layout' ) . '.css.php' );


/* general comment css for all layouts */
$css .= ppCss::background( 'comments_body_area_bg' )->rule( '.comments-body' );


// comment timestamp
$timestampFloat = ppOpt::test( 'comment_timestamp_display', 'right' ) ? 'float:right;' : '';
$css .= ppCss::font( 'comment_timestamp' )->rule( '.comment-time' );

// comment author link
$css .= ppCss::link( 'comment_author_link' )->rules( '.pp-comment .comment-author' );
$css .= ppCss::font( 'comment_author_link' )->rule(  '.pp-comment .comment-author' );


// general individual comment styling
$css .= ppCss::link( 'comment_text_and_link' )->withNonLink()->rules( '.pp-comment, .pp-comment p' );



/* COMMENTS HEADER */
// header: left-side fonts
$css .= ppCss::font( 'comments_header_link' )->rule(  '.comments-header .comments-header-left-side-wrap p' );
$css .= ppCss::link( 'comments_header_link' )->rules( '.comments-header .comments-header-left-side-wrap' );

// header: right side post-interact font
$css .= ppCss::link( 'comments_header_link' )->withNonLink()->rules( '.comments-header .post-interact' );
$css .= ppCss::link( 'comments_header_post_interaction_link' )->withNonLink()->rules( '.comments-header .post-interact' );

// header: background
$css .= ppCss::background( 'comments_header_bg' )->onlyIf( !ppOpt::test( 'comments_layout', 'tabbed' ) )->rule( '.comments-header' );

// overall comment area margin
$overallLRMargin = ppOpt::test( 'comments_area_lr_margin_control', 'set' ) ? ppOpt::id( 'comments_area_lr_margin' ) : ppOpt::id( 'content_margin' );


if ( ppOpt::test( 'comment_bottom_border_onoff', 'on' ) ) {
	$css .= ppCss::border( 'comment_bottom', 'bottom' )->rule( '.pp-comment' );
}





/* echo out general comment CSS */

$css .= <<<CSS

/* -- comments css -- */

.article-comments {
	clear:both;
	margin-top:16px;
	margin-left:{$overallLRMargin}px;
	margin-right:{$overallLRMargin}px;
	[~comments_area_bg_color,bgcolordec]
}
body.single .article-comments {
	margin-top: 15px;
	margin-bottom: 40px;
}
/* comments header */
.comments-header-left-side-wrap {
	float:left;
	display:inline;
}
#content .comments-header .comments-header-left-side-wrap .comments-count p {
	color:"ppOpt::cascade( 'comments_header_link_font_color', 'gen_link_font_color' );"
}
.comments-header div.post-interact-div {
	margin-left:[~comments_header_post_interact_link_spacing]px;
}
.not-accepting-comments .addacomment {
	display:none;
}
.has-comments .comments-count {
	cursor:pointer;
}
.article-comments .no-comments .comments-body-inner-wrap {
	display:none;
}


/* toggling display of comment-body area */
.article-comments .comments-body {
	display:block;
}
.comments-hidden .comments-body {
	display:none;
}
.single .comments-body,
.page .comments-body {
	display:block;
}
.layout-boxy .comments-body {
	display:block;
}
.no-comments .comments-body {
	display:none;
}



/* general individual comments */
.comments-body-inner {
	margin:[~comments_body_area_tb_margin]px [~comments_body_area_lr_margin]px;
}
.comments-body div.pp-comment {
	clear:both; /* for floated comment avatar layout */
	[~comment_bg_color,bgcolordec]
	margin-bottom:[~comment_tb_margin]px;
	padding:[~comment_tb_padding]px [~comment_lr_padding]px;
}
.comments-body div.pp-comment p {
	margin-bottom:0;
}

.pp-comment img.wp-smiley {
	height:[~comment_text_and_link_font_size]px;
}
.last-comment {
	margin-bottom:0 !important;
	border-bottom-width:0 !important;
}
/* comment timestamp */
.comment-time {
	$timestampFloat
	margin-left:10px;
}
.comment-meta-above {
	margin-bottom:[~comment_meta_margin_bottom]px;
}
.comment-meta-above .comment-time {
	margin-left:0;
}
.comment-meta-above .comment-author span {
	padding:0 2px;
}
#content .alt .comment-time {
	[~comment_alt_timestamp_font_color,colordec]
}
.article-comments .bypostauthor .comment-time {
	[~comment_byauthor_timestamp_font_color,colordec]
}
/* alt comments */
.comments-body div.alt {
	[~comment_alt_bg_color,bgcolordec]
}
.comments-body div.alt,
.comments-body div.alt p {
	[~comment_alt_font_color,colordec]
}
.comments-body .alt a:link,
.comments-body .alt a:visited {
	[~comment_alt_link_font_color,colordec]
}
.comments-body div.alt .comment-author,
.comments-body div.alt .comment-author a:link,
.comments-body div.alt .comment-author a:visited {
	[~comment_alt_author_link_font_color,colordec]
}
/* by author comment styles */
.comments-body div.bypostauthor {
	[~comment_byauthor_bg_color,bgcolordec]
}
.comments-body div.bypostauthor,
.comments-body div.bypostauthor p {
	[~comment_byauthor_font_color,colordec]
}
.comments-body div.bypostauthor a:link,
.comments-body div.bypostauthor a:visited,
.comments-body div.bypostauthor a:hover {
	[~comment_byauthor_link_font_color,colordec]
}
.comments-body div.bypostauthor .comment-author,
.comments-body div.bypostauthor .comment-author a:link,
.comments-body div.bypostauthor .comment-author a:visited {
	[~comment_byauthor_author_link_font_color,colordec]
}
/* comment awaiting moderation style */
.pp-comment .awaiting-moderation {
	margin-left:0.5em;
	font-style:[~comment_awaiting_moderation_font_style];
	[~comment_awaiting_moderation_font_color,colordec]
}
.alt .awaiting-moderation {
	[~comment_alt_awaiting_moderation_font_color,colordec]
}
/* add comment form styles */
.addcomment-holder {
	display:none;
	margin:0px [~content_margin]px;
}
.add-comment-form-wrap {
	margin:0px [~content_margin]px;
}
.addcomment-holder .add-comment-form-wrap {
	margin:0;
}
form#add-comment p {
	margin:18px 0 2px 0;
}
form#add-comment input#submit {
	margin-top:5px;
}
#addcomment-error {
	display:none;
	margin:20px 0;
}
#addcomment-error span {
	background:#fff;
	border:1px solid red;
	color:red;
	font-weight:bold;
	padding:4px;
	display:inline;
}
.cancel-reply {
	margin-left:5px;
}
textarea#comment {
	max-width:90%;
}
CSS;



/* comment avatars */
if ( ppOpt::test( 'comments_show_avatars', 'true' ) ) {
	$avatarAlignOpposite = ppOpt::test( 'comment_avatar_align', 'right' ) ? 'left' : 'right';
	$avatarOffset = ppOpt::id( 'comment_avatar_size' ) + ppOpt::id( 'comment_avatar_padding' );

	$css .= <<<CSS
	.avatar {
		float:[~comment_avatar_align];
		margin-{$avatarAlignOpposite}:[~comment_avatar_padding]px;
		margin-bottom:[~comment_avatar_padding]px;
		margin-top:3px;
	}
	.comment-text {
		padding-[~comment_avatar_align]:{$avatarOffset}px;
	}
CSS;
}



/* comment scrollbox */
if ( ppOpt::test( 'comments_layout', 'tabbed || minima' ) ) {
	$selector = ppOpt::test( 'comments_layout', 'tabbed' ) ? '.comments-body' : '.comments-body-inner-wrap';

	// home page
	if ( ppOpt::test( 'comments_in_scrollbox_on_home', 'true' ) ) {
		$height   = ppOpt::id( 'comments_scrollbox_height' ) . 'px';
		$overflow = 'auto';
	} else {
		$height   = 'none';
		$overflow = 'visible';
	}

	// single-type pages
	if ( ppOpt::test( 'comments_in_scrollbox_on_singular', 'true' ) ) {
		$singular_height   = ppOpt::id( 'comments_scrollbox_height' ) . 'px';
		$singular_overflow = 'auto';
	} else {
		$singular_height   = 'none';
		$singular_overflow = 'visible';
	}

	//css
	$css .= <<<CSS
	$selector {
		max-height:$height !important;
		overflow:$overflow;
	}
	body.single $selector,
	body.page $selector {
		max-height:$singular_height !important;
		overflow:$singular_overflow;
	}
CSS;
}

