<?php
// Load WordPress environment allow for incorrectly nested "prophoto4/prophoto4"
if ( file_exists( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' ) ) {
	require_once( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );
} else if ( file_exists( dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/wp-load.php' ) ) {
	require_once( dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/wp-load.php' );
} else {
	echo '<p>Error loading contact form.</p>';
}
do_action( 'p4_pre_contact_form' );

$p4_theme_url = pp::site()->themeUrl;
$name_text    = ppOpt::id( 'contactform_name_text' );
$required     = '<span class="required">' . ppOpt::id( 'contactform_required_text' ) . '</span>';
$email_text   = ppOpt::id( 'contactform_email_text' );
$message_text = ppOpt::id( 'contactform_message_text' );
$submit_text  = ppOpt::id( 'contactform_submit_text' );
$refer_page   = urlencode( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
$valid_email  = ppOpt::id( 'contact_form_invalid_email' );
$nonce        = wp_nonce_field( 'contact-form', '_wpnonce_p4', true, NO_ECHO );

if ( ppOpt::test( 'contactform_yourinformation_text' ) ) {
	$yourinformation_header = '<h2>' . ppOpt::id( 'contactform_yourinformation_text' ) . '</h2>';
}

if ( ppOpt::test( 'contactform_yourmessage_text' ) ) {
	$yourmessage_header = '<h2>' . ppOpt::id( 'contactform_yourmessage_text' ) . '</h2>';
}

// custom fields
$custom_fields_markup = '';
for ( $i = 1; $i <= pp::num()->maxContactFormCustomFields; $i++ ) {
	if ( !ppOpt::id( 'contact_customfield' . $i . '_label' ) ) continue;
	$custom_field_label = ppOpt::id( 'contact_customfield' . $i . '_label' );
	if ( ppOpt::test( 'contact_customfield' . $i . '_required', 'yes' ) ) {
		$this_required_text = $required;
		$this_reauired_class = ' pp-required-field';
	} else {
		$this_required_text = $this_reauired_class = '';
	}
	$custom_fields_markup .= <<<HTML
	<div class="pp-field{$this_reauired_class}">
		<p><label for="custom-field{$i}">$custom_field_label $this_required_text</label></p>
		<input id="custom-field{$i}" size="35" name="custom-field{$i}" type="text"  />
	</div>
HTML;
}

// anti-spam
if ( ppOpt::test( 'contactform_antispam_enable', 'on' ) ) {
	$spam_num = rand( 1, 3 );
	$spam_label = ppOpt::id( 'anti_spam_question_' . $spam_num );
	$spam_required = '<span class="required">' . ppOpt::id( 'anti_spam_explanation' ) . '</span>';
	$anti_spam = <<<HTML
	<div class="pp-field pp-required-field">
		<p><label for="anti-spam">$spam_label $spam_required</label></p>
		<input id="anti-spam" size="35" name="anti-spam" type="text"  />
		<input type="hidden" name="spam_question" value="$spam_num" />
	</div>
HTML;
}


// widgetized content
if ( ppWidgetUtil::areaHasWidgets( 'contact-form' ) ) {
	$form_class = ' class="with-widget-content"';
	echo '<ul id="widget-content">';
		echo ppWidgetUtil::areaContent( 'contact-form' );
	echo '</ul>';
} else {
	$form_class = '';
}


// output form
$form_markup = <<<HTML

<form id="contactform" action='{$p4_theme_url}/includes/contact-form-process.php' method='post'{$form_class}>

	$yourinformation_header

	<div class="pp-field">
		<p class="firstname"><label for="firstname">First name (required)</label></p>
	<input id="firstname" size="35" name="firstname" type="text" class="firstname" />
	</div>

	<div class="pp-field pp-required-field">
		<p><label for="lastname">$name_text $required</label></p>
		<input id="lastname" size="35" name="lastname" type="text" />
	</div>

	<div class="pp-field pp-required-field">
		<p><label for="email">$email_text $required</label></p>
		<input id="email" size="35" name="email" type="text" /><span id="invalid-email">$valid_email</span>
	</div>

	$custom_fields_markup

	$anti_spam

	$yourmessage_header

	<fieldset>
		<div class="pp-field pp-required-field">
			<p><label for="message">$message_text $required</label></p>
			<textarea id="message" name="message" rows="10"></textarea>
		</div>
	</fieldset>

	<input type="hidden" id="referpage" name="referpage" value="$refer_page" />

	<input type='submit' name='submit' value='$submit_text' />

	$nonce
</form>
HTML;
echo apply_filters( 'pp_contact_form_filter', $form_markup );
do_action( 'pp_post_contact_form' );

?>