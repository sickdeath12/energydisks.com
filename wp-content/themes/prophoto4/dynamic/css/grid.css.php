<?php 


$css .= ppCss::link( 'grid_img_text_below_title_link' )->withNonLink()->rules( '#inner-body .grid-style-img_text_below .grid-item h3' );
$css .= ppCss::link( 'grid_img_rollover_text_title_link' )->withNonLink()->rules( '#inner-body .grid-style-img_rollover_text .grid-item h3' );
$css .= ppCss::link( 'grid_img_rollover_text_text_link' )->withNonLink()->rules( '#inner-body .grid-style-img_rollover_text .grid-item p' );

if ( intval( ppOpt::id( 'grid_img_rollover_text_title_link_font_size' ) ) !== 0 ) {
	$overlayTitleLineHeight = intval( ppOpt::id( 'grid_img_rollover_text_title_link_font_size', 'int' ) * 1.08 ) . 'px';
} else {
	$overlayTitleLineHeight = '1.1em';
}

$css .= <<<CSS

.excerpts-grid-wrap {
	padding-top:20px;
}
.grid .grid-item {
	float:left;
}
.grid-item {
	overflow:hidden;
	line-height:0.8em;
}
.grid .row .last-in-row {
	margin-right:0;
}
.grid-item img,
.grid div.grid-item .pp-img-protect {
	margin:0;
}
.grid div.grid-item div.aligncenter img,
.grid div.grid-item .pp-img-protect-aligncenter {
	margin:0 !important;
}
#content .article-content .grid .grid-item img,
#content .article-content .grid .grid-item .pp-img-protect {
	border-width:0;
	-moz-box-shadow:none;
	-webkit-box-shadow:none;
	box-shadow:none;
}

/* style: text below */
.grid-style-img_text_below .grid-item {
	margin:0 [~grid_img_text_below_gutter]px [~grid_img_text_below_gutter]px 0;
}
.grid-style-img_text_below .grid-item h3 {
	margin-top:0.65em;
	line-height:1.5em;
}


/* style: rolover text */
.grid-style-img_rollover_text .grid-item {
	position:relative;
	cursor:pointer;
	overflow:hidden;
	margin:0 [~grid_img_rollover_text_gutter]px [~grid_img_rollover_text_gutter]px 0;
}
.grid-style-img_rollover_text .grid-overlay {
	display:none;
	position:absolute;
	top:0;
	left:0;
	width:91%;
	height:92%;
	padding:5% 6% 5% 5%;
	overflow:hidden;
}
.grid-style-img_rollover_text .grid-overlay h3,
.grid-style-img_rollover_text .grid-overlay p {
	position:relative;
	z-index:10; /* z-10 */
	line-height:1.3em;
}
.grid-style-img_rollover_text .grid-overlay p span.read-more-wrap {
	display:block;
	margin:0.75em 0 0 0;
}
.grid-style-img_rollover_text .grid-overlay h3 {
	margin-bottom:0.5em;
	line-height:$overlayTitleLineHeight;
}
.grid-style-img_rollover_text .grid-overlay .overlay-bg {
	position:absolute;
	display:block;
	width:2000px;
	height:2000px;
	[~grid_img_rollover_text_overlay_bg_color,bgcolordec]
	opacity:[~grid_img_rollover_text_overlay_bg_opacity,%];
	z-index:8; /* z-8 */
	top:0;
	left:0;
}
.grid-style-img_rollover_text .grid-overlay p.subtitle {
	font-size:0.9em;
}




CSS;

