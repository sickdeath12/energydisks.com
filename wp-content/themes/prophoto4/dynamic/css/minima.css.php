<?php
/* --- minima comments --- */


/* overall styles */
$commentsHeaderTBPadding = ppOpt::orVal( 'comments_header_tb_padding', '0.6em', 'px' );


/* border lines */
// border style
$borderStyle = 
	ppOpt::orVal( 'comments_area_border_style', 'solid' ) . ' ' . 
	ppOpt::orVal( 'comments_area_border_width', '0' ) . 'px ' . 
	ppOpt::orVal( 'comments_area_border_color', '#ffffff' );

// left/right border 
if ( !ppOpt::test( 'comments_area_lr_margin_control', 'set' ) || ppOpt::id( 'comments_area_lr_margin' ) > 0 ) {
	$css .= 
	".article-comments {
		border-left:  $borderStyle;
		border-right: $borderStyle;
	}";
}

// top border
if ( ppOpt::test( 'comment_header_border_top', 'on' ) ) {
	$css .= "
	.comments-header { 
		border-top: $borderStyle;
	}";
}

// bottom border
if ( ppOpt::test( 'comment_header_border_bottom', 'on' ) ) {
	$css .= "
	.comments-header {
		border-bottom: $borderStyle;
	}
	.article-comments {
		border-bottom-width:0;
	}
	.comments-shown.article-comments {
		border-bottom: $borderStyle;
	}
	.comments-shown .comments-header {
		border-bottom: $borderStyle;
	}";
}



/* post interaction ICONS */
$piLinkTypes = array( 'addacomment', 'linktothispost', 'emailafriend' );
if ( ppOpt::test( 'comments_post_interact_display', 'text || button' ) ) {
	foreach ( $piLinkTypes as $linkType ) {
		$iconImg = ppImg::id( "comments_header_{$linkType}_link_icon" );
		if ( $iconImg->exists ) {
			$leftPadding = $iconImg->width + 4; // 4 is arbitrary, for pleasing visual spacing
			$css .= 
			".{$linkType} a {
				padding-left:{$leftPadding}px;
				background:url($iconImg->url) no-repeat left center;
			}";
		}
	}
}




/* custom post interaction images */
if ( ppOpt::test( 'comments_post_interact_display', 'images' ) ) {
	$tallestCustomImgHeight = 0;
	foreach ( $piLinkTypes as $linkType ) {
		$customImg = ppImg::id( "comments_header_{$linkType}_link_image" );
		$tallestCustomImgHeight = max( $customImg->height, $tallestCustomImgHeight );
		$textIndent = $customImg->exists ? '-9999px' : '0';
		$css .= 
		".{$linkType} a {
			width:{$customImg->width}px;
			height:{$customImg->height}px;
			background: url($customImg->url) no-repeat left top;
			text-indent:$textIndent;
		}";
	}
}




/* VERTICAL SPACING pt 1: get comments header LEFT side info - post author link & comments count area */
$left_side_font_size  = ppOpt::id( 'comments_header_link_font_size' );
$left_side_height     = $left_side_font_size;
$left_side_use_btn    = ppOpt::test( 'comments_show_hide_method', 'button' );
$left_side_btn_height = 21;

// using button, and text SMALLER than button height
if ( $left_side_use_btn && $left_side_font_size < $left_side_btn_height ) {
	$left_side_height       = $left_side_btn_height;
	$left_side_inner_pad    = ( $left_side_height - $left_side_font_size ) / 2;
	$left_side_pad_selector = 'p';
	
// using button, text size LARGER than button height
} else if ( $left_side_use_btn ) {
	$left_side_inner_pad    = ( $left_side_font_size - $left_side_btn_height ) / 2;
	$left_side_pad_selector = '#show-hide-button';

// no button
} else {
	$left_side_inner_pad    = 0;
	$left_side_pad_selector = 'p';
}


/* VERTICAL SPACING pt 2: get comments header RIGHT side info - "post interact" links */
// not using custom uploaded images
if ( !ppOpt::test( 'comments_post_interact_display', 'images' ) ) {

	// post interaction links (add a comment, link to this post, etc)
	$link_size = ppOpt::cascade( 'comments_header_post_interaction_link_font_size', 'comments_header_link_font_size' );

	// if using P4 link buttons account for hard-coded .6em top/bottom pad & borders
	if ( ppOpt::test( 'comments_post_interact_display', 'button' ) ) {
		$link_added_height = ( ( $link_size * 0.6 ) * 2 ) + 4;
	} else {
		$link_added_height = 0;
	}
	
	// get total post interact link area height
	$right_side_height = $link_size + $link_added_height;

// using custom images
} else {
	$right_side_height = $tallestCustomImgHeight;
}


/* VERTICAL SPACING pt 3: get identity and added padding for shorter side */
// left is smaller
if ( $left_side_height < $right_side_height ) {
	$taller_side_height    = $right_side_height;
	$shorter_side_height   = $left_side_height;
	$shorter_side_selector = '.comments-header-left-side-wrap';
	
// right is smaller
} else {
	$taller_side_height    = $left_side_height;
	$shorter_side_height   = $right_side_height;
	$shorter_side_selector = '.post-interact';
}

// calculate padding for the shorter side
$shorter_side_top_padding = ( $taller_side_height - $shorter_side_height ) / 2;


/* VERTICAL SPACING pt 4: get padding amounts to vertically center custom link images of varying heights */
if ( ppOpt::test( 'comments_post_interact_display', 'images' ) ) {
	foreach ( $piLinkTypes as $linkType ) {
		$padTop = ( $right_side_height - ppImg::id( "comments_header_{$linkType}_link_image" )->height ) / 2;	
		$css .= ".{$linkType} { padding-top:{$padTop}px; }";
	}
}







$css .= <<<CSS

/* vertical centering stuff */
{$shorter_side_selector} {
	padding-top: {$shorter_side_top_padding}px;
}
.comments-header-left-side-wrap $left_side_pad_selector {
	margin-top: {$left_side_inner_pad}px;
}


.no-comments .comments-count p {
	color:"ppOpt::cascade( 'comments_header_font_color', 'gen_font_color' )";
}
.comments-count {
	float: left;
}
.article-comments span.hide-text {
	display:none;
}
.article-comments span.show-text {
	display:inline;
}
.comments-shown span.hide-text {
	display:inline;
}
.comments-shown span.show-text {
	display:none;
}
#show-hide-button {
	float: left;
	width: 21px;
	height: 21px;
	margin: 0 0 0 1em;
	background: url([~minima-comments-show-hide.png,theme_img]) no-repeat left top;
}
.comments-shown #show-hide-button {
	background-position: left bottom;
}
.no-comments #show-hide-button {
	background-position: left top;
}
.article-comments p {
	margin-bottom:0;
}
.comments-header {
	padding: $commentsHeaderTBPadding [~comments_header_lr_padding]px;
}
.comments-header p {
	line-height: 1;
	float: left;
}
.article-byline {
	margin-right: 15px;
}
.comments-header div.post-interact {
	float:right;
}
.comments-header .post-interact-div {
	float: left;
	margin-left: 14px;
}
.post-interact a {
	line-height: 1;
	display:block;
}
.pp-comment {
	line-height:1.2em;
}
CSS;


if ( ppOpt::test( 'comments_post_interact_display', 'button' ) ) {
	$css .= <<<CSS
	.post-interact a {
		padding-top: 0.6em;
		padding-bottom: 0.6em;
		margin: 0 0.6em;
	}
	.button-outer {
		border: 1px solid;
		border-color: #c0bebe #c0bebe #959595 #959595;
	}
	.button-inner {
		border: 1px solid #ffffff;
		background: #ffffff url([~post-interaction-button-bg.jpg,theme_img]) repeat-x left bottom;
	}
CSS;

}

