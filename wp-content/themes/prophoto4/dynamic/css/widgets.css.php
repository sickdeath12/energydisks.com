<?php
$twitter_slider_widget_css = ( ppWidgetUtil::instanceOfTypeExists( 'pp-sliding-twitter' ) ) ? ppTwitterSlider_Widget::css() : '';
$css .= <<<CSS
p.icon-note {
	margin:0 !important;
}
.widget_calendar th {
	font-weight:bold;
}
.widget_calendar td {
	padding:0 2px;
}
li.widget li {
	margin-left:1.2em;
	line-height:1.1em;
	margin-bottom:0.7em;
	list-style-type:disc;
	list-style-position:outside;
}
li.widget .pp-html-twitter-widget li {
	margin-left:0;
	list-style-type:none;
}
li.widget #searchsubmit {
	margin-top:0.3em;
}
$twitter_slider_widget_css
h3.widgettitle {
	line-height:1em;
	margin-bottom:0.35em;
}
.twitter-interactive-badge {
	height:350px;
}
.twitter-follow-link {
	margin-top:4px;
}
.twitter-follow-link a {
	font-size:10px;
	text-decoration:none;
	line-height:1em;
}
.p4-twitter-html p {
	font-size:.8em;
	text-align:right;
	font-style:italic;
}
.p4-twitter-html p a {
	font-style:italic;
}
.p4-twitter-html li {
	font-size:.9em;
	line-height:1.2em;
	margin-bottom:.75em;
	margin-left:0 !important;
}
.twitter-interactive-badge-wrap {
	width:290px;
	height:350px;
}
.twitter-simple-badge-wrap {
	width:176px;
	min-height:176px;
}
.twitter-simple-badge-wrap a {
	font-size:10px;
	text-align:center;
	display:block;
	line-height:1em;
	margin-top:3px;
}
#outer-wrap-centered .widget_pp-twitter-com-widget a img {
	height:15px !important;
}
.widget_pp-facebook-likebox iframe {
	background:#fff;
}

body.logged-in li.widget {
	position:relative;
}
body #inner-body li.widget a.pp-edit-widget-link {
	position:absolute;
	z-index:135;
	top:3px;
	right:5px;
	background:#DDD;
	color:#000;
	line-height:1em;
	padding:1px 3px 2px 3px;
	font-size:10px;
	font-family:Arial,sans-serif;
	border-radius:2px;
	border:1px solid #000;
	box-shadow:1px 1px 1px #777;
	text-transform:uppercase;
	display:none;
	text-decoration:none;
}
body #inner-body li.widget a.pp-edit-widget-link:hover {
	background:#000;
	color:#fff;
	text-decoration:none;
}
body.pc #inner-body li.widget a.pp-edit-widget-link {
	padding-bottom:1px;
}
body #inner-body li.widget:hover a.pp-edit-widget-link {
	display:inline;
}
CSS;

