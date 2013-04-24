<?php
/* --- tabbed comments --- */

$iconHeight = 12;
$post_interact_fontsize = ppOpt::cascade( 'comments_header_post_interaction_link_font_size', 'comments_header_link_font_size' );


// ensure entire icon shows up, even with very small font size
if ( $post_interact_fontsize < $iconHeight ) {
	$iconPadding = ( $iconHeight - $post_interact_fontsize ) / 2;
} else {
	$iconPadding = 0;
}

// vertically center the post-interact links
$comment_header_height  = 42;
if ( $comment_header_height > $post_interact_fontsize ) {
	$post_interact_top_pad = ( $comment_header_height - max( $post_interact_fontsize, $iconHeight ) ) / 2;
} else {
	$post_interact_top_pad = 0;
}

// pad the "tab" if article author not being shown
if ( ppOpt::test( 'comments_header_show_article_author', 'false' ) ) {
	$css .= ".comments-count { margin-left: 1em; }\n";
}



$css .= <<<CSS
.comments-body div.pp-comment {
	margin-bottom:0;
	line-height:1.1em;
}
.article-comments {
	border: 1px solid #cac9c9;
	border-bottom:none;
	margin-left:[~content_margin]px;
	margin-right:[~content_margin]px;
}
.comments-header {
	background: #f4f4f4 url([~borderCAC9C9.gif,theme_img]) repeat-x bottom;
}
.comments-header p {
	margin-bottom:0;
	line-height:1.3em;
}
.article-byline {
	float:left;
	display:inline;
	padding: 14px 16px 13px 16px;
}
.comments-count, .comments-count div {
	float:left;
	display:inline;
}

.comments-shown .comments-count {
	background: url([~tab2-left.jpg,theme_img]) no-repeat bottom left;
}
.comments-shown .comments-count div {
	background: url([~tab2-right.jpg,theme_img]) no-repeat bottom right;
}
.comments-count,
.no-comments .comments-count {
	background: url([~tab1-left.jpg,theme_img]) no-repeat bottom left;
}
.comments-count div,
.no-comments .comments-count div {
	background: url([~tab1-right.jpg,theme_img]) no-repeat bottom right;
}
.comments-count p {
	line-height:1.3em;
	float:left;
	display:inline;
	background: none;
	padding: 13px 21px 13px 34px;
	margin:1px 0 0 0;
}
.comments-shown .comments-count p {
	background: url([~comments-open.gif,theme_img]) no-repeat 21px 54%;
}
.comments-count p,
.no-comments .comments-count p {
	background: url([~comments-closed.gif,theme_img]) no-repeat 21px 54%;
}
.post-interact a {
	display:block;
	line-height:1em;
}
.post-interact {
	float:right;
	display:inline;
	padding:{$post_interact_top_pad}px 16px 0 0;
	line-height: 1.3em;
}
.post-interact-div {
	padding-top:{$iconPadding}px;
	padding-bottom:{$iconPadding}px;
	margin-left:14px;
	float:left;
}
.addacomment {
	background: url([~comment.png,theme_img]) no-repeat left center;
	padding-left:15px
}
.emailafriend {
	background: url([~email.gif,theme_img]) no-repeat left center;
	padding-left:20px;
}
.linktothispost {
	background: url([~link.gif,theme_img]) no-repeat left center;
	padding-left:18px;
}
.comments-body {
	border-bottom:1px solid #cac9c9;
}
.comments-body-inner {
	padding:15px;
}	
.pp-comment {
	padding:.4em .5em;
}
.comment-author {
	margin-right:.2em;
}
CSS;
