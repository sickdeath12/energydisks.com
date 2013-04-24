<?php





$css .= <<<CSS

/* reset styles - props eric meyer */
html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td{margin:0;padding:0;border:0;outline:0;font-weight:inherit;font-style:inherit;font-size:100%;font-family:inherit;vertical-align:baseline;}
:focus{outline:0;}
body{line-height:1;color:black;background:white;}
ol,ul{list-style:none;}
table{border-collapse:separate;border-spacing:0;}
caption,th,td{text-align:left;font-weight:normal;}
blockquote:before,blockquote:after,
q:before,q:after{content:"";}
blockquote,q{quotes:"" "";}
section,article,header,footer,nav,aside,hgroup{display:block;}
/* end reset */

body div img.wp-smiley {
	border:none !important;
	padding:0 0.1em !important;
	margin:0 !important;
	float:none !important;
	display:inline !important;
}

.sc:after {
	content: ".";
	display: block;
	height: 0;
	clear: both;
	visibility: hidden;
}
*:first-child+html .sc {
	min-height: 1px;
}

#user-copyright .pipe,
#mobile-user-copyright .pipe {
	padding:0 0.6em;
}
.pp-fb-like-btn-wrap {
	clear:both;
	margin:[~like_btn_margin_top]px 0 [~like_btn_margin_btm]px 0;
}
.comments-area-hidden,
.pp-comment.added-via-fb,
.fb-comments .pp-comment {
	display:none;
}
.pp-comment.from-fb-legacy-permalink {
	display:block;
}
#maintenance-mode-remind {
	opacity:0.75;
	padding:1%;
	width:98%;
	z-index:100000;
	text-align:center;
	color:red;
	position:absolute;
	top:0;
	left:0;
	background-color:yellow;
	border:2px solid orange;
	border-left-width:0;
	border-right-width:0;
}


/* standard article formatting elements */
.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4,
.article-content h5,
.article-content h6 {
	font-style:normal;
	color:inherit;
	font-family:inherit;
	font-size:inherit;
	font-weight:bold;
	margin-bottom:.6em;
}
.article-content h1 {
	font-size:160%;
}
.article-content h2 {
	font-size:140%;
}
.article-content h3 {
	font-size:120%;
}
.article-content h4 {
	font-size:110%;
}
.article-content h5 {
	font-size:105%;
}
#main-wrap-inner .article-content .sociable img {
	border:none !important;
	margin:0;
}
.article-content ol,
.article-content ul {
	margin-bottom:1.5em;
}
.article-content ol {
	list-style:decimal;
}
.article-content ul {
	list-style:disc;
}
strong {
	font-weight:700;
}
em {
	font-style:italic;
}


CSS;



if ( !ppOpt::test( 'image_protection', 'none' ) ) {
	$css .= <<<CSS
	img {
		-webkit-touch-callout:none;
		-webkit-user-select: none;
	}
CSS;
}



/* custom font faces */
for ( $i = 1; $i <= pp::num()->maxCustomFonts; $i++ ) {
	$css .= ppFontUtil::fontFaceCss( 'custom_font_' . $i );
}

