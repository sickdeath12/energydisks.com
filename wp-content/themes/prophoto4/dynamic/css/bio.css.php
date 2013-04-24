<?php  /* -- bio area css */


// text/link css
$css .= ppCss::font( 'bio_header' )->rule( '#bio h3' );
$css .= ppCss::font( 'bio_para' )->rule( '#bio, #bio p' );
$css .= ppCss::link( 'bio_link' )->rules( '#bio' );
$headerMarginBtm = ppOpt::orVal( 'bio_headline_margin_btm', '0.5em', 'px' );


// paddings and margins
$widgetsMarginBtm =  ppOpt::id( 'bio_widget_margin_btm' );
$columnsMargin    = ppOpt::cascade( 'bio_gutter_width', 'content_margin' );
$leftRightMargins = ppOpt::cascade( 'bio_lr_padding', 'content_margin' );
$btmPadding       = ppOpt::id( 'bio_btm_padding' ) - $widgetsMarginBtm;
if ( $btmPadding < 1 ) {
	$btmPadding = 1;
}

//bio background images & border
$css .= ppCss::background( 'bio_bg' )->rule( '#bio' );
$css .= ppCss::background( 'bio_inner_bg' )->rule( '#bio-inner-wrapper' );
$css .= ppCss::border( 'bio', 'bottom' )->onlyIf( ppOpt::test( 'bio_border', 'border' ) )->rule( '#bio' );


// bio picture
$biopicRightMargin = ppOpt::test( 'biopic_align', 'left' ) ? $columnsMargin : '0';
$biopicBorder = ppCss::border( 'biopic' )->onlyIf( ppOpt::test( 'biopic_border', 'on' ) )->decs();


// bio content/widgets
$bioColumns = ppBioColumns::data();
$bioColumnsCss = $bioColumns->css();


$twitterLinkColor = ppOpt::cascade( 'bio_para_font_color', 'gen_font_color' );


if ( !ppOpt::test( 'biopic_display', 'off' ) ) {
	$biopicHeight  = ppImg::id( 'biopic1' )->height + ( ppOpt::id( 'biopic_border_width' ) * 2 );
	$contentHeight = $biopicHeight + $widgetsMarginBtm;
	$bioMinHeight  = 'min-height:' . $contentHeight . 'px;';
} else {
	$bioMinHeight  = '';
}



$css .= <<<CSS
/* -- bio area css -- */
#bio-content {
	margin:0 {$leftRightMargins}px {$btmPadding}px {$leftRightMargins}px;
	padding-top:[~bio_top_padding]px;
	$bioMinHeight
}
#biopic {
	float:[~biopic_align];
	margin-right:{$biopicRightMargin}px;
	margin-bottom:{$widgetsMarginBtm}px !important;
	$biopicBorder
}
.bio-col {
	margin-right:{$columnsMargin}px;
}
.bio-col img {
	max-width:100%;
	height:auto;
}
.bio-widget-col {
	float:left;
}
#bio-content .widget h3 {
	margin-bottom:{$headerMarginBtm};
}
#bio-content li.widget,
#bio-content li.widget span.pngfixed {
	list-style-type:none;
	margin-bottom:{$widgetsMarginBtm}px;
}
#bio-content .twitter-follow-link a {
	color:{$twitterLinkColor};
}
$bioColumnsCss
CSS;



/* custom bio separator image */
if ( ppOpt::test( 'bio_border', 'image' ) ) {
	$sepImg = ppImg::id( 'bio_separator' );
	$css .= <<<CSS
	#bio-separator {
		background-image:url($sepImg->url);
		background-position:bottom center;
		background-repeat:no-repeat;
		height:{$sepImg->height}px;	
	}
CSS;
}

