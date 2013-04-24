<?php


/* SECTION: postheader alignment */ 

//  standard stuff
$css .= <<<CSS
.article-title {
	line-height:1em;
}
.article-title, 
.article-meta-top, 
.article-date {
	text-align:[~post_header_align];
}
.article-header {
	margin-bottom:[~post_header_margin_below]px;
}
.article-wrap-inner {
	padding-top:[~post_header_margin_above]px;
}
CSS;

// post date sameline with title
if ( ppOpt::test( 'postdate_display', 'normal' ) && ppOpt::test( 'postdate_placement', 'withtitle' ) && !ppOpt::test( 'post_header_align', 'center' ) ) {
	$titleFontSize  = ppOpt::cascade( 'post_title_link_font_size', 'header_font_size' );
	$dateFontSize   = ppOpt::cascade( 'post_header_postdate_font_size', 'gen_font_size' );
	$dateTopPadding = intval( ( $titleFontSize - $dateFontSize ) / 1.5 ); // 1.5 roughly aligns baselines
	if ( ppOpt::test( 'post_header_align', 'left' ) ) {
		$dateFloat  = 'right';
		$titleFloat = 'left';
	} else {
		$dateFloat  = 'left';
		$titleFloat = 'right';
	}
	$css .= <<<CSS
	.article-title-wrap .article-date {
		float:$dateFloat;
		margin-top:{$dateTopPadding}px;
	}
	.article-title { 
		text-align:$titleFloat;
	}
CSS;

}

//  post date above
if ( ppOpt::test( 'postdate_placement', 'above' ) ) {
	$css .= '.article-date { display:block; }';
}



/* SECTION: post title font/link */
$css .= ppCss::font( 'header'          )->rule(  '#content .article-title a, #content .article-title' );
$css .= ppCss::font( 'post_title_link' )->rule(  '#content .article-title' );
$css .= ppCss::link( 'post_title_link' )->rules( '#content .article-title' );


$css .= <<<CSS
.article-title a {
	line-height:1em;
}
#content a.post-edit-link {
	font-size:10px !important;
	font-weight:400 !important;
	letter-spacing:normal !important;
	font-family: Arial, sans-serif;
	text-transform:uppercase;
	text-decoration:none;
	font-style:normal;
	margin: 0 8px;
}
body.search-no-results a.post-edit-link,
body.error404 a.post-edit-link {
	display:none;
}
CSS;
// if post title link isn't set, use general header color for visited and hover 
// states so we don't get default browser blue
if ( !ppOpt::color( 'post_title_link_font_color' ) ) {
	$css .= <<<CSS
	.article-title a:visited,
	.article-title a:hover {
		color:[~header_font_color];
	}
CSS;
}




/* post header meta styles - category list, tags list, comment count */
$css .= ppCss::font( 'post_header_meta_link' )->rule(  '.article-header .article-meta-item' );
$css .= ppCss::link( 'post_header_meta_link' )->rules( '.article-header .article-meta-item' );
$css .= ppCss::font( 'post_header_postdate'  )->rule(  '.article-header .article-date' );
$entryMetaSpacingSide = ppOpt::test( 'post_header_align', 'right' ) ? 'left' : 'right';
$css .= <<<CSS
.article-meta-top span {
	margin-{$entryMetaSpacingSide}:1.1em;
}
.article-header-comment-count span {
	display:none;
}
CSS;




/* boxy dates styles */
if ( ppOpt::test( 'postdate_display', 'boxy' ) ) {
	$boxyDateFontSize      = ppOpt::id( 'boxy_date_font_size' );
	$boxyDateHeight        = intval( $boxyDateFontSize * 3.6 );
	$boxyDateLetterspacing = intval( $boxyDateFontSize / 7 );
	$boxyDateWidth         = intval( $boxyDateFontSize * 4.3 );
	$boxyDatePadding       = intval( $boxyDateFontSize / 2 );
	$boxyDateOffset        = $boxyDateWidth + $boxyDateFontSize;
	
	$css .= <<<CSS
	.article-header {

	}
	.boxy-date-wrap {
		font-size:{$boxyDateFontSize}px;
		width:{$boxyDateWidth}px;
		padding:{$boxyDatePadding}px 0;
		[~boxy_date_bg_color,bgcolordec]
	}
	.boxy .article-date {
		float:[~boxy_date_align];
		margin-top:0;
		display:inline;
	}
	.boxy .article-title,
	.boxy .article-meta-top {
		margin-[~boxy_date_align]:{$boxyDateOffset}px;
	}
	body.page .article-title,
	body.page .article-meta-top {
		margin-[~boxy_date_align]:0;
	}
	body.page .boxy-date-wrap {
		display:none;
	}
	body.page .article-header {
		padding-left:0;
		padding-right:0;
	}
	.boxy-date-wrap span {
		margin-right:0 !important;
		display:block;
		line-height:.8em;
		text-align:center;
		text-transform:uppercase;
		font-family:Arial;
	}
	.boxy-month {
		letter-spacing:2px;
		color:[~boxy_date_month_color];
	}
	.boxy-day {
		font-size:2.3em;
		margin-top:1px;
		#margin-top:2px;
		font-weight:700;
		color:[~boxy_date_day_color];
	}
	.boxy-year {
		margin-top:.1em;
		font-size:.9em;
		letter-spacing:{$boxyDateLetterspacing}px;
		color:[~boxy_date_year_color];
	}
	.boxy-year {
		margin-left:5%;
	}
	.boxy-month {
		margin-left:3%;
	}
CSS;
}



/* post header border below */
if ( ppOpt::test( 'post_header_border', 'on' ) ) {
	$css .= <<<CSS
	.article-content {
		ppCss::border( 'post_header', 'top' )->decs();
		padding-top:[~post_header_border_margin]px !important;
	}
CSS;
}



/* post header image separator */
if ( ppOpt::test( 'post_header_border', 'image' ) && ppImg::id( 'post_header_separator' )->exists ) {
	$img = ppImg::id( 'post_header_separator' );
	$padding = $img->height + ppOpt::id( 'post_header_margin_below' );
	$css .= <<<CSS
	.article-header {
		background: transparent url($img->url) no-repeat bottom center;
		padding-bottom:{$padding}px;
	}
CSS;
}


/* post date advanced features */
$css .= ppPostHeader::advancedDateCss();

