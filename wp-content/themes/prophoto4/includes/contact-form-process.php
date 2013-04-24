<?php
/*
Server side processing of the contact form
*/


/* Access only on POST */
if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	@header('HTTP/1.1 405 Method Not Allowed' );
	echo '<h1>Method Not Allowed</h1><p>The requested method is not allowed</p>';
	die();
}

// Load WordPress environment allow for incorrectly nested "prophoto4/prophoto4"
if ( file_exists( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' ) ) {
	require_once( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );
} else if ( file_exists( dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/wp-load.php' ) ) {
	require_once( dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/wp-load.php' );
} else {
	echo '<p>Error processing contact form.</p>';
}

// Check origin
check_admin_referer( 'contact-form', '_wpnonce_p4' );

// allow plugins to do stuff here
do_action( 'pp_contact_pre_form_process' );

// set the variables
$name    = esc_attr( $_POST['lastname'] );
$email   = esc_attr( $_POST['email'] );
$message = esc_attr( $_POST['message'] );
$_POST['referpage'] = preg_replace( '/#.*$/', '', $_POST['referpage'] );


// referpage includes error hash unless it passes all the test
$referpage = apply_filters( 'pp_contact_early_referpage', esc_attr( urldecode( $_POST['referpage'] ) ) . '#error' );


// did the spambot fill out the invisible field?  ha ha!  caught you!
if ( !empty($_POST['firstname'] ) ) {
	@header( 'HTTP/1.1 403 Forbidden' );
	echo '<h1>Forbidden</h1><p>If you feel you got this message and should not, please try again.</p>';
	die();
}


// do some validation, first the spam question
if ( ppOpt::test( 'contactform_antispam_enable', 'on' ) ) {
	if ( empty( $_POST['anti-spam'] ) ) {
		ppUtil::redirect( $referpage );
	}
	$user_input = esc_attr( strtolower( trim( $_POST['anti-spam'] ) ) );
	if ( !$user_input ) {
		ppUtil::redirect( $referpage );
	}
	// we have some input check it against acceptible answers
	$answers = ppOpt::id( 'anti_spam_answer_' . intval( $_POST['spam_question'] ) );
	$answers = explode( '*', strtolower( $answers ) );
	$spam = true;
	foreach ( $answers as $answer ) {
		if ( $user_input == $answer ) $spam = false;
	}
	if ( $spam ) {
		ppUtil::redirect( $referpage );
	}
}

// check for required fields
if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
   ppUtil::redirect( $referpage );
}

// is it an email address?
if ( NrUtil::invalidEmail( $email ) ) {
   ppUtil::redirect( $referpage );
}

// if custom fields are present and required, validate
for ( $i = 1; $i <= pp::num()->maxContactFormCustomFields; $i++ ) {
	if (
		ppOpt::id( 'contact_customfield' . $i . '_label' ) &&
		ppOpt::id( 'contact_customfield' . $i . '_required' ) == 'yes' &&
		empty( $_POST['custom-field' . $i] )
	) {
	   ppUtil::redirect( $referpage );
	}
}

// successful submission, change the hash
$referpage = urldecode( $_POST['referpage'] ) . '#success';


if ( get_magic_quotes_gpc() ) {
	$message = stripslashes( $message );
}

// use custom email address, or snag the admin's email address
if ( ppOpt::test( 'contactform_emailto' ) ) {
	$mailto = ppOpt::id( 'contactform_emailto' );
} else {
	$user_info = get_userdata( 1 );
	$mailto = $user_info->user_email;
}

// make the subject
if ( ppOpt::test( 'contact_email_custom_subject' ) ) {
	$subject = str_replace( array( '%name%', '%email%', '%date%', '%time%' ), array( $name, $email, date( get_option( 'date_format' ) ), date( get_option( 'time_format' ) ) ), ppOpt::id( 'contact_email_custom_subject' ) );
} else {
	$subject = pp::site()->name . ' - site contact form submission';
}

// pull in the field labels
$name_label    = ppOpt::id( 'contactform_name_text' );
$email_label   = ppOpt::id( 'contactform_email_text' );
$message_label = ppOpt::id( 'contactform_message_text' );
$custom_label  = ppOpt::id( 'contact_customfield_label' );

// create the email body
$email_text =
	$name_label . ": " .
	$name
	."\n\n " . $email_label . ": " .
	$email;

// custom fields
for ( $i = 1; $i <= pp::num()->maxContactFormCustomFields; $i++ ) {
	if ( ppOpt::test( 'contact_customfield' . $i . '_label' ) ) {
		$email_text .= "\n\n" . ppOpt::id( 'contact_customfield' . $i . '_label' ) . ':  ' . $_POST['custom-field' . $i];
	}
}

// message
$email_text .= "\n\n " . $message_label . ": \n\n " . $message;

// scrub funky characters
$email_text = ppUtil::escEmail( $email_text );
$subject    = ppUtil::escEmail( $subject );
$name       = ppUtil::escEmail( $name );

// log to the db
if ( !$contact_log = get_option( pp::wp()->dbContactLog ) ) {
	add_option( pp::wp()->dbContactLog, '', '', 'no'  );
	$contact_log = array();
}
array_unshift( $contact_log, array( 'time' => time(), 'data' => $email_text ) );
update_option( pp::wp()->dbContactLog, $contact_log );

// more plugin hooks
$mailto = apply_filters( 'pp_contact_mailto', $mailto );
$subject = apply_filters( 'pp_contact_subject', $subject );
$email_text = apply_filters( 'pp_contact_email_text', $email_text );
$referpage = apply_filters( 'pp_contact_late_referpage', $referpage );
do_action( 'pp_contact_pre_email' );


// remote send for troublesome hosts
if ( ppOpt::test( 'contact_remote_send', 'on' ) ) {
	wp_remote_post( 'http://prophotoblogtech.com/theme_email_send.php', array(
		'body' => array(
			'email_to' => $mailto,
			'email_from' => $email,
			'subject' => $subject,
			'name' => $name,
			'message' => $email_text
		)
	));
}

// ship it
if ( ppOpt::test( 'contact_sendmail_from' ) ) ini_set( 'sendmail_from', ppOpt::id( 'contact_sendmail_from' ) );
wp_mail( $mailto, $subject, $email_text, "From: \"$name\" <$email>\nReply-To: \"$name\" <$email>" );

ppUtil::redirect( $referpage );

