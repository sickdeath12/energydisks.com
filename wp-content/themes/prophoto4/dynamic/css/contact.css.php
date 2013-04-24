<?php
/* ----------------------------------- */
/* ----custom css for contact form---- */
/* ----------------------------------- */

// contact success/error message colors
$contact_success_bg_color   = ppOpt::id( 'contact_success_bg_color' );
$contact_success_text_color = ppOpt::id( 'contact_success_text_color' );
$contact_error_bg_color     = ppOpt::id( 'contact_error_bg_color' );
$contact_error_text_color   = ppOpt::id( 'contact_error_text_color' );

// optional contact area override colors
$css                 .= ppCss::background( 'contact_bg' )->rule( '#contact-form' );
$contact_text_color   = ppCss::colorDec( 'contact_text_color' );
$contact_header_color = ppCss::colorDec( 'contact_header_color' );

// bottom border
$css .= ppCss::border( 'contact_btm', 'bottom' )->onlyIf( !ppOpt::test( 'contact_btm_border', 'off' ) )->rule( '#contact-form' );


/* Output the CSS */
$css .= <<<CSS
#pp-contact-success-msg {
	background: $contact_success_bg_color;
}
#pp-contact-success-msg p {
	color: $contact_success_text_color;
}
#pp-contact-error-msg {
	background: $contact_error_bg_color;
}
#pp-contact-error-msg p {
	color: $contact_error_text_color;
}
#contact-form p {
	$contact_text_color
}
#main-wrap-inner #contactform div p {
	margin-bottom:0.2em;
}
#contact-form h2 {
	$contact_header_color
}
#contact-form form  {
	padding:3.5% 3.5% 1.5% 3.5%;
	max-width:600px;
}
#contact-form textarea {
	width:95%;
}
#contact-form form.with-widget-content {
	margin-left:45%;
}
#contact-form #widget-content {
	padding:3.5% 3.5% 1.5% 4.5%;
	float:left;
	width:36%;
}
#contact-form #widget-content img {
	max-width:100%;
	height:auto;
}
#contact-form div p,
#contact-form #widget-content p {
	margin-bottom:1.2em;
}
#contact-form h2 {
	margin-bottom:.4em;
}
#contact-form p {
	margin-bottom:0;
}
#contactform input, #contactform textarea {
	margin-bottom:10px;
}
.pp-contact-submit-msg p {
	padding:6px;
	text-align:center;
	margin-bottom:0;
	font-size:1.0em;
}
.pp-contact-message {
	display:none;
}
#contact-form .firstname {
	display:none !important;
}
#contact-form div.p4-has-error input, #contact-form div.p4-has-error textarea {
	border:red 2px solid;
}
div.p4-has-error span.required {
	color:red;
}
#invalid-email {
	color:red;
	margin-left:.5em;
	display:none;
}
div.p4-has-error #invalid-email {
	display:inline;
}
CSS;
?>