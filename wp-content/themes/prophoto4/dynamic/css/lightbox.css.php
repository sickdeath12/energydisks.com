<?php 


if ( ppOpt::test( 'lightbox_main_img_center', 'true' ) ) {
	$css .= '.pp-lightbox img { margin-left:auto; margin-right:auto; }';
}
if ( ppOpt::test( 'lightbox_thumbs_center', 'true' ) ) {
	$css .= '.pp-lightbox-thumbs { margin-left:auto; margin-right:auto; }';
}


$css .= <<<CSS



/* initial state */
.pp-lightbox {
	clear:both;
}
#main-wrap-inner .article-content .pp-lightbox img,
#main-wrap-inner #content .article-content .pp-lightbox .pp-img-protect img {
	margin-bottom:[~lightbox_thumb_margin]px;
}
.article-content .pp-lightbox-thumbs {
	font-size:0;
	line-height:0;
}
.pp-lightbox-thumbs .pp-img-protect { 
	display:inline !important;
}
#main-wrap-inner .article-content .pp-lightbox a img {
	display:block;
}
#main-wrap-inner .article-content .pp-lightbox-thumbs a img {
	margin: 0 [~lightbox_thumb_margin]px [~lightbox_thumb_margin]px 0;
	display:inline;
}
.article-content .pp-lightbox-thumbs a {
	opacity:[~lightbox_thumb_opacity,%];
}
#main-wrap-inner .article-content .pp-lightbox-thumbs .last a img {
	margin-right:0;
}



/* mobile initial state */
body.not-mobile .pp-lightbox img {
	display:inline;
	cursor:pointer !important;
}
.landscape body.mobile .pp-lightbox .pp-lightbox-thumbs {
	width:450px !important;
}
.portrait body.mobile .pp-lightbox .pp-lightbox-thumbs {
	width:296px !important;
}
#mobile-content .pp-lightbox-thumbs a {
	line-height:0;
	margin:0;
	padding:0;
	display:inline;
	font-size:0;
}
#mobile-content .pp-lightbox-thumbs a img {
	margin:0 4px 2px 0;
}
#mobile-content .pp-lightbox-thumbs a {
	opacity:1 !important;
}
.portrait #mobile-content .pp-lightbox-thumbs a img {
	width:94px !important;
	height:94px !important;
}
.landscape #mobile-content .pp-lightbox-thumbs a img {
	width:108px !important;
	height:108px !important;
}
body.ipad .pp-lightbox-thumbs a {
	opacity:1 !important;
}



/* overlay and image display */
#jquery-overlay {
	position: absolute;
	top: 0;
	left: 0;
	z-index:198; /* z-198 */
	width: 100%;
	height: 500px;
}
#jquery-lightbox {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	z-index:200; /* z-200 */
	text-align: center;
	line-height: 0;
}
#jquery-lightbox a img { 
	border: none;
}
#lb-img-wrap.loaded {
	background-color:#000 !important;
}
#lightbox-container-image-box {
	position: relative;
	width: 450px;
	height: 450px;
	margin: 0 auto;
	background-color:[~lightbox_bg_color];
}
#lightbox-container-image-data-box {
	font-size:[~lightbox_font_size]px;
	font-family:[~lightbox_font_family];
	background-color:[~lightbox_bg_color];
	padding: 0 [~lightbox_border_width]px;
	padding-bottom:[~lightbox_border_width]px;
	margin: 0 auto;
	line-height: 1.4em;
	overflow: auto;
	width: 100%;
}
#lightbox-container-image { 
	padding:[~lightbox_border_width]px; 
}
#lightbox-container-image-data {
	color:[~lightbox_font_color];
	padding: 0 10px; 
}
#lightbox-loading {
	position: absolute;
	top: 40%;
	left: 0%;
	height: 25%;
	width: 100%;
	text-align: center;
	line-height: 0;
}
#lightbox-nav {
	position: absolute;
	top: 0;
	left: 0;
	height: 100%;
	width: 100%;
	z-index:10;
}
#lightbox-container-image-box > #lightbox-nav { 
	left: 0;
}
#lightbox-nav a { 
	outline: none;
}
#lightbox-nav-btnPrev, 
#lightbox-nav-btnNext {
	width: 49%;
	height: 100%;
	zoom: 1;
	display: block;
}
#lightbox-nav-btnPrev { 
	left: 0; 
	float: left;
}
#lightbox-nav-btnNext { 
	right: 0; 
	float: right;
}
#lightbox-container-image-data #lightbox-image-details { 
	width: 70%; 
	float: left; 
	text-align: left; 
}	
#lightbox-image-details-caption { 
	font-weight: bold; 
}
#lightbox-image-details-currentNumber {
	display: block; 
	clear: left; 
}			
#lightbox-secNav-btnClose {
	width: 66px; 
	float: right;
	padding-bottom: 0.7em;	
}
CSS;



/* mobile overlay and img display */
$nextDisplayWidth = intval( ppImg::id( 'lightbox_next' )->width / 1.5 );
$prevDisplayWidth = intval( ppImg::id( 'lightbox_prev' )->width / 1.5 );


$css .= <<<CSS
.landscape .mobile #jquery-lightbox {
	width:480px;
}
.portrait .mobile #jquery-lightbox {
	width:320px;
}
.landscape .mobile #lightbox-container-image-box.unsized {
	max-height:235px;
	max-width:460px;
}
.portrait .mobile #lightbox-container-image-box.unsized {
	max-width:265px;
	max-height:320px;
}
.mobile #lightbox-nav-btnNext {
	background-size:{$nextDisplayWidth}px;
}
.mobile #lightbox-nav-btnPrev {
	background-size:{$prevDisplayWidth}px;
}
.landscape body.mobile #lightbox-image {
	max-height:225px !important;
	width:auto !important;
}
.portrait body.mobile #lightbox-image {
	max-width:255px !important;
	height:auto !important;
}
.mobile #lightbox-container-image {
	padding:5px;
}
.mobile #lightbox-container-image-data-box {
	padding: 0 5px 5px 5px;
}
.mobile #lightbox-secNav-btnClose {
	width:39px;
	height:13px;
	padding-bottom:0.2em;
}

CSS;



