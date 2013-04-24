<?php /* -- footer css -- */


// general footer styles
$copyrightExtraTopPadding = ppOpt::test( 'footer_include', 'no' ) ? 'padding-top:20px;' : '';
$css .= ppCss::background( 'footer_bg' )->rule( '#footer' );

// footer bottom cap bg/image
$cap = ppImg::id( 'footer_btm_cap' );
if ( $cap->exists ) {
	$css .= "#outer-wrap-centered { background: url($cap->url) no-repeat bottom center; padding-bottom: {$cap->height}px }";
}


// footer heading styles
$css .= ppCss::font( 'footer_headings' )->rule( '#footer h3' );

// footer text styles
$css .= ppCss::link( 'footer_link' )->withNonlink()->rules( '#footer, #footer p' );


$footerColumn = ppFooter::columnInfo();



$css .= <<<CSS

#footer {
	padding: 30px {$footerColumn->rightPadding}px 30px {$footerColumn->leftPadding}px;
	line-height:1.4em !important;
}
.footer-spanning-col {
	clear:both;
}
#footer .footer-non-spanning-col {
	width:{$footerColumn->columnWidth}px;
	margin-right:{$footerColumn->columnPadding}px;
	float:left;
	overflow:hidden;
}
#footer #footer-col-{$footerColumn->lastColumnNum} {
	margin-right:0;
}
#copyright-footer-sep {
	padding:0 .3em;
}
#footer li {
	margin-bottom:[~footer_widget_margin_below]px;
}
#footer li li {
	margin-bottom:0.4em;
	margin-left:20px;
	line-height:1.2em;
}
#footer .pp-widget-menu li {
	margin-left:0;
}
#copyright-footer {
	text-align:center;
	padding:11px [~content_margin]px 10px [~content_margin]px;
}
#copyright-footer p {
	color:[~gen_font_color];
	font-size:11px;
	margin-bottom:0;
	$copyrightExtraTopPadding
}
span.statcounter {
	display:inline;
}
#wp-footer-action-output {
	margin:0;
	padding:0;
}

CSS;
