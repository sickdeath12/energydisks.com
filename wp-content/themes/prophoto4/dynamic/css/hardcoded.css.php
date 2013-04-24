<?php


$css .= <<<CSS

/* --------------------- */
/* -- POST FORMATTING -- */
/* --------------------- */
#content .article-content img.has-caption {
	margin-bottom:0;
}
.pp-caption {
	text-align:center;
	margin-bottom:2em;
	font-style:italic;
}
.post {
	clear:both;
}
br.p4br {
	clear:both;
}
body.home .last-post,
body.archive .last-post {
	background-image:none;
	margin-bottom:0;
	border-bottom-width:0;
}
body.single .article-wrap,
body.page .article-wrap {
	padding-bottom:0;
	border-bottom-width:0;
}
.aligncenter,
.pp-img-protect-aligncenter,
div.aligncenter img {
	display:block !important;
	margin-left:auto !important;
	margin-right:auto !important;
}
.pp-img-protect-aligncenter,
.pp-img-protect-alignnone {
	clear:both;
}
.alignright, .pp-img-protect-alignright {
	margin: 0 0 15px 30px !important;
	display: inline !important;
	float:right !important;
}
.alignleft, .pp-img-protect-alignleft {
	margin: 0 30px 15px 0 !important;
	display:inline !important;
	float:left !important;
}
.pp-img-protect .alignleft,
.pp-img-protect .alignright,
.pp-img-protect .aligncenter {
	margin: 0 !important;
}
div.wp-caption {
	max-width:100%;
	height:auto;
}
.wp-caption-text {
	text-align:center !important;
}
.article-meta-bottom {
	margin-bottom:15px;
}


/* ---------- */
/* -- MENU -- */
/* ---------- */
ul.suckerfish { /* all lists */
	padding: 0;
	margin: 0;
	list-style: none;
}
ul.suckerfish li li {
	margin-right: 0;
}
ul.suckerfish a,
ul.suckerfish li.sfhover {
	display: block;
}
ul.suckerfish li { /* all list items */
	float: left;
}
ul.suckerfish li:hover {
	position:static; /* help IE7 a bit */
}
ul.suckerfish li ul { /* second-level lists */
	position: absolute;
	width: 130px;
	left: -999em;
}
ul.suckerfish li ul a {
	width:114px;
	padding: 5px 8px;
	font-size: 80%;
}
ul.suckerfish li:hover ul,
ul.suckerfish li.sfhover ul { /* lists nested under hovered list items */
	left: auto;
}
ul.suckerfish li.topnavright {
	margin-right: 0;
}
li.mi-search-dropdown ul {
	padding:9px 8px 10px 8px !important;
	width:auto !important;
}
li.mi-search-dropdown ul li input#s-top {
	margin-bottom:8px;
}
ul.suckerfish li ul ul,
ul.suckerfish li ul ul ul,
ul.suckerfish li ul ul ul ul,
ul.suckerfish li ul ul ul ul ul {
	margin: -1.90em 0 0 130px;
	#margin-top:-1.85em;
}
ul.suckerfish li:hover ul ul, ul.suckerfish li.sfhover ul ul,
ul.suckerfish li:hover ul ul ul, ul.suckerfish li.sfhover ul ul ul,
ul.suckerfish li:hover ul ul ul ul, ul.suckerfish li.sfhover ul ul ul ul,
ul.suckerfish li:hover ul ul ul ul ul, ul.suckerfish li.sfhover ul ul ul ul ul {
	left: -999em;
}
ul.suckerfish li li:hover ul, ul.suckerfish li li.sfhover ul,
ul.suckerfish li li li:hover ul, ul.suckerfish li li li.sfhover ul,
ul.suckerfish li li li li:hover ul, ul.suckerfish li li li li.sfhover ul,
ul.suckerfish li li li li li:hover ul, ul.suckerfish li li li li li.sfhover ul {
	left: auto;
}
/* we start higher than 150 to avoid problems with slideshow */
ul.suckerfish {
    z-index:155; /* z-155 */
}
ul.suckerfish ul {
    z-index:156; /* z-156 */
}
ul.suckerfish ul li {
    z-index:157; /* z-157 */
}
ul.suckerfish ul li ul {
    z-index:158; /* z-158 */
}
ul.suckerfish ul li ul li {
    z-index:159; /* z-159 */
}
ul.suckerfish a {
    z-index:160; /* z-160 */
}



/* ------------------ */
/* -- CONTACT FORM -- */
/* ------------------ */
body.home .protected {
	padding-bottom:0px;
}
body.single .comments-body,
body.ipad .comments-body,
body.iphone .comments-body {
	max-height: none !important;
}




/* ---------- */
/* -- MISC -- */
/* ---------- */
.ppIssue {
	position:fixed;
	top:0;
	left:0;
	background:#fff;
	color:#111;
	font-size:11px;
	width:100%;
	padding:8px 20px;
	line-height:17px;
	display:none;
	opacity:0.8;
	z-index:100000000;
}
.pp-tech .ppIssue {
	display:block;
}
li.widget {
	list-style-type:none;
}
body.single .post {
	border-bottom: none;
	background-image: none;
	margin-bottom: 0;
	padding-bottom: 0;
}
.article-title {
	margin-bottom:.2em;
}
#audio-player-wrap {
	text-align:center;
	margin:0 auto;
}
#copyright-footer #audio-player-wrap {
	margin-bottom:0.5em;
}
.protected p input {
	margin-bottom:8px;
}
.article-meta-bottom .article-category-list {
	display: inline;
	margin-right:1em;w
}
.edit-link-top {
	margin-left:1.3em;
}
.article-meta-top p {
	margin-bottom:0;
}
body.single p#adjacent-posts-links {
	padding-top:.3em;
	margin-bottom: .5em;
}
#link-removal-txn-id {
	display:none;
}


body.gallery-quasi-post .article-date,
body.gallery-quasi-post .article-meta,
body.gallery-quasi-post .post-edit-link,
body.gallery-quasi-post .article-comments,
body.gallery-quasi-post .paginated-links,
body.gallery-quasi-post .adjacent-posts-links {
	display:none;
}

.js-info {
	display:none;
}
.force-width {
	opacity:0;
}


/* popup slideshows */
body.popup-slideshow,
body.popup-slideshow #inner-body {
	background-color:#000 !important;
	background-image:none !important;
	padding:0;
}
body.popup-slideshow #dropshadow-bottom,
body.popup-slideshow #copyright-footer {
	display:none;
}
body.popup-slideshow .pp-slideshow {
	margin-bottom:0;
}

/* fixes strange bug where ipad initial device scale
   messed up by a short content  */
@media only screen and (orientation:portrait) {
	body.ipad {
		min-height:1140px;
	}
	body.mobile-site-disabled {
		min-height:950px;
	}
}
@media only screen and (orientation:landscape) {
	body.mobile-site-disabled {
		min-height:650px;
	}
}

/* fixes mobile-safari scaling rendering artifacts, see:
   http://stackoverflow.com/questions/4780896/thin-gray-black-lines-on-web-page-viewed-with-ipad
   http://www.oddodesign.com/2010/css-tip-how-to-prevent-div-seam-lines-from-appearing-in-apples-mobile-safari/ */
body.mobile-safari article,
body.mobile-safari .article-wrap,
body.mobile-safari #content,
body.mobile-safari #content-wrap,
body.mobile-safari #masthead,
body.mobile-safari .page-title-wrap {
	margin-bottom:-1px;
}

/* mobile safari resizes certain font elements relative to each other */
body.mobile-safari {
	-webkit-text-size-adjust:none;
}

#pp-flash-music-player-wrap {
	position:absolute;
	bottom:0;
	left:0;
	width:1px;
	height:1px;
	z-index:50000;
}


CSS;

