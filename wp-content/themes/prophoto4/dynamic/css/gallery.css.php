<?php


$prophoto_info = ppUtil::siteData();


$css .= ppCss::font( 'slideshow_title' )->rule( '.initialOverlay h3, .initialOverlay h4' );
$css .= ppCss::font( 'slideshow_subtitle' )->rule( '.initialOverlay h4' );

if ( ppOpt::test( 'image_protection', 'clicks || right_click || none' ) ) {
	$css .= '.pp-slideshow .blankOverlay { display:none; }';
}
$iPadThumbPadding = ppOpt::id( 'slideshow_thumb_padding', 'int' ) > 15 ? ppOpt::id( 'slideshow_thumb_padding' ) : 15;


$css .= <<<CSS


/* main styles */
.nav-ajax-receptacle .pp-slideshow,
#content .pp-slideshow,
.pp-slideshow-not-loaded img, 
.ss-first-img {
	margin-left:auto;
	margin-right:auto;
	display:block;
}
.article-content .ss-first-img,
img.loadingSpinner {
	margin-top:0;
	border-width:0 !important;
	-moz-box-shadow:none;
	-webkit-box-shadow:none;
	box-shadow:none;
}
body img.p3-placeholder,
#content img.p3-placeholder,
#mobile-content img.p3-placeholder,
#content .article-content img.pp-gallery-placeholder,
#mobile-content .article-content img.pp-gallery-placeholder,
body img.pp-gallery-placeholder,
body img.pp-grid-placeholder {
	display:none !important;
}
.showWrap {
	position:relative;
	overflow:hidden;
}
#content .showWrap,
body.popup-slideshow .showWrap {
	background:"ppOpt::id( 'slideshow_bg_color' )";
}
#masthead-image-wrapper .showWrap {
	ppCss::bgColorDec( 'masthead_slideshow_bg_color' );
}
.imgViewingArea {
	position:relative;
	overflow:hidden;
}
a.imgWrap.no-link {
	cursor:default;
    -webkit-tap-highlight-color:rgba(0,0,0,0); /* disables iPad/iPhone top focus state */
}
.imgWrap {
	position:absolute;
	top:0;
	left:0;
}
.imgWrap img {
	margin:0 auto;
	display:block;

}
/* this is to fix png transparency fading artifacts, see http://stackoverflow.com/questions/1251416/png-transparency-problems-in-ie8 */
.cant-fade-imgs-with-black .imgWrap.png img {
	background: transparent;
    -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#00FFFFFF,endColorstr=#00FFFFFF)"; /* IE8 */   
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#00FFFFFF,endColorstr=#00FFFFFF);   /* IE6 & 7 */      
    zoom: 1;
}
.nextImg {
	display:none;
}
.pp-slideshow .blankOverlay {
	width:200px;
	height:200px;
	position:absolute;
	top:0;
	left:0;
}
body.ipad .pp-slideshow .blankOverlay {
	display:none;
}
body.fullscreen-slideshow .pp-slideshow-not-loaded img {
	display:none;
}


/* timer */
.imgViewingArea .timer {
	position:absolute;
	background:#fff;
	z-index:130; /* z-130 */
}
.timer {
	width:0;
	height:5px;
	left:0;
}
.mobile .timer {
	height:3px;
}
.controlsVertical .timer {
	height:0;
	width:5px;
	top:0;
}
.mobile .controlsVertical .timer {
	width:3px;
}
.controlsPos-bottom .timer {
	top:0;
}
.controlsPos-top .timer {
	bottom:0;
}
.controlsPos-left .timer {
	right:0;
}
.controlsPos-right .timer {
	left:0;
}
.showingSplashScreen .timer {
	display:none;
}


/* controls area */
.pp-slideshow .controls {
	margin:0;
	overflow:hidden;
	position:relative;
	padding:"ppOpt::id( 'slideshow_thumb_padding', 'px' )";
}
body.ipad .pp-slideshow .controls {
	padding:{$iPadThumbPadding}px;
}
.controlsNotOverlaid.controlsPos-left .controls {
	float:left;
}
.controlsNotOverlaid.controlsPos-left .imgViewingArea {
	float:right;
}
.controlsNotOverlaid.controlsPos-right .controls {
	float:right;
}
.controlsNotOverlaid.controlsPos-right .imgViewingArea {
	float:left;
}
.controlsHorizontal .controls {
	left:0;
}
.controlsVertical .controls {
	top:0;
}
.controlsOverlaid .controls {
	position:absolute;
	z-index:122; /* z-122 */
}
.controls-bg {
	position:absolute;
	top:0;
	left:0;
	width:10000px;
	height:10000px;
	opacity:"ppOpt::id( 'slideshow_controls_bg_opacity', '%' );";
	background:"ppOpt::cascade( 'slideshow_controls_bg_color', 'slideshow_bg_color' );";
}
.controlsNotOverlaid .controls-bg {
	opacity:1;
}
.pp-slideshow .controls a {
	cursor:pointer;
	opacity:"ppOpt::id( 'slideshow_btns_opacity', '%' )";
}
.pp-slideshow .controls a:hover {
	opacity:1;
}
	body.ipad .pp-slideshow .controls a:hover {
		opacity:"ppOpt::id( 'slideshow_btns_opacity', '%' )";
	}
.pp-slideshow .controls .btns {
	position:absolute;
	text-align:center;
	line-height:1em;
}
.pp-slideshow .controls .btns a {
	display:inline-block;
	position:relative;
	width:20px;
	height:20px;
	overflow:hidden;
	margin:0 5px 4px;
	#margin-bottom:6px;
}
.pp-slideshow .controls .btns a img {
	height:20px;
	width:220px;
	position:absolute;
	top:0;
	left:0;
	margin:0;
}
	body.ipad .pp-slideshow .controls .btns a {
		width:40px;
		height:40px;
	}
	body.ipad .pp-slideshow .controls .btns a img {
		height:40px;
		width:440px;
	}
.thumbStrip {
	position:relative;
}
.pp-slideshow .controls img {
	border-width:0;
	-moz-box-shadow:0 0 0 #fff;
	-webkit-box-shadow:0 0 0 #fff;
	box-shadow:0 0 0 #fff;
}
.controlsHorizontal .pagedThumbs .thumbStrip {
	padding:0 22px;
}
.controlsVertical .pagedThumbs .thumbStrip {
	padding:22px 0;
}
	body.ipad .controlsHorizontal .pagedThumbs .thumbStrip {
		padding:0 44px;
	}
	body.ipad .controlsVertical .pagedThumbs .thumbStrip {
		padding:44px 0;
	}
.thumbsViewport {
	position:relative;
	overflow:hidden;
}
.thumbsViewport img {
	margin:0;
	cursor:pointer;
	width:"ppOpt::id( 'slideshow_thumb_size', 'px' )";
	height:"ppOpt::id( 'slideshow_thumb_size', 'px' )";
	margin-right:"ppOpt::id( 'slideshow_thumb_padding', 'px' )";
	margin-bottom:"ppOpt::id( 'slideshow_thumb_padding', 'px' )";
	opacity:"ppOpt::id( 'slideshow_thumb_opacity', '%' )";
}
body.ipad .thumbsViewport img {
	min-width:100px;
	min-height:100px;
	margin-right:{$iPadThumbPadding}px;
	margin-bottom:{$iPadThumbPadding}px;
}
.controlsVertical .thumbsViewport img {
	float:left;
}
.pp-slideshow .thumbsViewport img.active {
	border-color:"ppOpt::id( 'slideshow_active_thumb_border_color' )" !important;
	opacity:"ppOpt::id( 'slideshow_active_thumb_opacity', '%' )" !important;
}
.thumbsWrap {
	position:absolute;
	top:0;
	left:0;
}
.pp-slideshow .thumbsWrap img {
	border:[~slideshow_thumb_border_color] [~slideshow_thumb_border_width]px solid !important;
}
CSS;

$thumb_unit_size = ppOpt::id( 'slideshow_thumb_size' ) + 2 * ppOpt::id( 'slideshow_thumb_border_width' );

$css .= <<<CSS

.controlsHorizontal .thumbsWrap {
	width:10000px;
	height:{$thumb_unit_size}px;
}
.controlsVertical .thumbsWrap {
	height:10000px;
	width:{$thumb_unit_size}px;
}
.controlsHorizontal .prevPage,
.controlsHorizontal .nextPage {
	top:0;
	width:22px;
	height:{$thumb_unit_size}px;
}
	body.ipad .controlsHorizontal .prevPage,
	body.ipad .controlsHorizontal .nextPage {
		width:44px;
	}
.controlsVertical .prevPage,
.controlsVertical .nextPage {
	left:0;
	height:22px;
	width:{$thumb_unit_size}px;
}
	body.ipad .controlsVertical .prevPage,
	body.ipad .controlsVertical .nextPage {
		height:44px;
	}
.prevPage,
.nextPage {
	display:none;
	position:absolute;
	overflow:hidden;
}
.pagedThumbs .prevPage,
.pagedThumbs .nextPage {
	display:block;
}
.prevPage div, 
.nextPage div {
	position:relative;
	width:22px;
	height:22px;
	overflow:hidden;
}
	body.ipad .prevPage div, 
	body.ipad .nextPage div {
		width:44px;
		height:44px;
	}
.prevPage img,
.nextPage img {
	height:20px;
	width:220px;
	left:-97px;
	position:absolute;
	top:0;
	margin:0;
}
.nextPage img {
	left:-82px;
}
	body.ipad .prevPage img,
	body.ipad .nextPage img {
		height:40px;
		width:440px;
	}
	body.ipad .nextPage img {
		left:-164px;
	}
	body.ipad .prevPage img {
		left:-196px;
	}
	body.ipad .controlsVertical .nextPage img {
		left:-400px;
	}
	body.ipad .controlsVertical .prevPage img {
		left:-360px;
	}
.controlsVertical .prevPage img {
	left:-180px;
	top:3px;
}
.controlsVertical .nextPage img {
	left:-200px;
	top:auto;
	bottom:3px;
}
.prevPage {
	left:0;
}
.controlsVertical .prevPage {
	top:0;
	bottom:auto;
}
.nextPage {
	right:0;
}
.controlsVertical .nextPage {
	top:auto;
	bottom:0;
}

.thumbStrip a.disabled img {
	display:none;
}
.pp-slideshow .playPause,
.pp-slideshow .fullscreen {
	width:16px;
	height:16px;
}
.playing .controls .btns a.playPause img {
	left:-20px;
}
	body.ipad .playing .controls .btns a.playPause img {
		left:-40px;
	}
.controls .btns a.fullscreen img {
	left:-40px;
}
	body.ipad .controls .btns a.fullscreen img {
		left:-80px;
	}
.fullscreen-slideshow .controls .btns a.fullscreen img {
	left:-60px;
}
.fullscreen-slideshow.ipad .controls .btns a.fullscreen img {
		left:-120px;
	}
.controls .btns a.cart-url img {
	left:-120px;
}
.controls .btns a.mp3player img {
	left:-140px;
}
.controls .btns a.mp3player.paused img {
	left:-160px;
}
	body.ipad .controls .btns a.cart-url img {
		left:-240px;
	}
	body.ipad .controls .btns a.mp3player img {
		left:-280px;
	}
	body.ipad .controls .btns a.mp3player.paused img {
		left:-320px;
	}
.disabled {
	background-image:none;
	cursor:default;
}

/* initial overlay area */
.showingSplashScreen .controls {
	display:none;
}
.controlsNotOverlaid .controls {
	display:block;
}
.initialOverlay {
	position:absolute;
	color:#fff;
	text-align:center;
	top:0;
	left:0;
	z-index:126; /* z-126 */
	overflow:hidden;
	cursor:pointer;
}
.initialOverlay .content {
	position:absolute;
	top:0;
	left:0;
	z-index:126; /* z-126 */
}
.initialOverlay .startBtn {
	width:35px;
	height:35px;
}
	body.ipad .initialOverlay .startBtn {
		width:70px;
		height:70px;
	}
.pp-slideshow .startBtn,
.imgWrap img,
.btns img,
.thumbsWrap img,
.initialOverlay .content img.logo {
	border-width:0 !important;
	-moz-box-shadow:0 0 0 #fff;
	-webkit-box-shadow:0 0 0 #fff;
	box-shadow:0 0 0 #fff;
}
.initialOverlay .content img.logo {
	margin-bottom:8px;
}
.initialOverlay .content img {
	margin:0;
}
.initialOverlay .bg {
	position:absolute;
	top:0;
	left:0;
	width:10000px;
	height:1000px;
	z-index:124; /* z-124 */
	background-color:"ppOpt::cascade( 'slideshow_splash_screen_color', 'slideshow_bg_color' )";
	opacity:"ppOpt::id( 'slideshow_splash_screen_opacity', '%' )";
}

body .initialOverlay h3,
body .initialOverlay h4 {
	font-weight:400;
	margin:0 0 0.3em 0 !important;
	line-height:1em !important;
}
.initialOverlay h4 {
	margin-top:0.75em;
}

img.loadingSpinner {
	position:absolute;
	top:50%;
	left:50%;
	z-index:128; /* z-128 */
	background:#fff;
	opacity:0.45;
	border-radius:17px;
	padding:2px;
	display:none;
	height:18px;
	width:18px;
}


/* mobile controls */
.mobileControls {
	position:absolute;
	width:100%;
	height:100%;
	top:0;
	left:0;
	z-index:116; /* z-116 */
}
.mobileControls a {
	display:none;
	opacity:0.65;
}
.mobileControls .playPause,
.mobileControls .mp3player,
.mobileControls .prevNext span {
	background:"ppOpt::cascade( 'slideshow_controls_bg_color', 'slideshow_bg_color' );";
}
.mobileControls .playPause {
	position:absolute;
	bottom:8px;
	left:39%;
	width:50px;
	height:50px;
	z-index:120; /* z-120 */
	padding:7px;
	-webkit-border-radius:80px;
}
.mobileControls .playPause span {
	-webkit-background-size:550px 50px;
	background-position:-50px 0;
}
.paused .mobileControls .playPause span {
	background-position:0 0;
}
.mobileControls .playPause span, 
.mobileControls em {
	display:block;
	height:100%;
	text-indent:-999em;
}
.mobileControls span em {
	width:50px;
	height:50px;
	-webkit-background-size:550px 50px;
	background-size:550px 50px;
}
.mobileControls .mp3player {
	z-index:121; /* z-121 */
	-webkit-border-radius:80px;
	padding:7px 7px 7px 5px;
	position:absolute;
	top:8px;
}
.mobileControls .mp3player em {
	background-position:-350px 0;
}
.mobileControls .mp3player.paused em {
	background-position:-400px 0;
}
.paused .mobileControls .playPause {
	background-position:0 0;
}
.mobileControls .prev {
	left:0;
}
	.mobileControls .prev span {
		left:4px;
	}
	.mobileControls .prev span em {
		background-position:-250px 0;
	}
.mobileControls .next {
	right:0;
}
	.mobileControls .next span {
		right:4px;
	}
	.mobileControls .next span em {
		background-position:-200px 0;
	}
.mobileControls .prevNext {
	position:absolute;
	top:37%;
	z-index:118; /* z-118 */
}
.mobileControls .prevNext span {
	display:block;
	position:absolute;
	-webkit-border-radius:50px;
}



/* MSIE faux-console */
#ielog {
	display:none;
	border-top:3px solid #444;
	background:#fff;
	color:#333;
	position:absolute;
	bottom:0;
	left:0;
	padding:5px 5px 10px 5px;
	font-family:monospace;
	font-size:12px;
	line-height:0.6em;
	height:200px;
	overflow-y:scroll;
	width:99%;
	margin:0;
}
#ielog strong {
	display:block;
	border-bottom:1px solid black;
	margin-bottom:7px;
	padding-bottom:6px;
	padding-top:2px;
	font-size:120%;
	font-weight:bold;
}


CSS;

