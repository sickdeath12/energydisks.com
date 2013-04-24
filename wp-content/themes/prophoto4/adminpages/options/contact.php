<?php
/* ----------------------- */
/* ---CONTACT  OPTIONS---- */
/* ----------------------- */


// tabs and header
ppSubgroupTabs(  array(
	'options' => 'General Options',
	'form' => 'Text &amp; Fields',
	'log' => 'Log',
) );

ppOptionHeader('Contact Form Options', 'contact' );


/* options subgroup */
ppOptionSubgroup( 'options' );


// bg
ppUploadBox::renderBg( 'contact_bg', 'Contact form background <span>optional color and background image behind your contact form area</foo>' );

ppStartMultiple( 'Contact form bottom border' );
ppO( 'contact_btm_border', 'radio|on|custom border|off|no border', 'show/hide a custom border below the contact form area' );
ppBorderGroup( array( 'key' => 'contact_btm_border', 'comment' => 'bottom border appearance' ) );
ppStopMultiple();

// text and background colors
ppStartMultiple( 'Contact form text colors' );
ppO( 'contact_header_color', 'color|optional', 'color of contact form headlines' );
ppO( 'contact_text_color', 'color|optional', 'color of contact form non-headline text' );
ppStopMultiple();

// form error and success messages
ppStartMultiple( 'Contact form submitted success message' );
ppO( 'contact_success_msg', 'text|33', 'text displayed when contact form successfully submitted' );
ppO( 'contact_success_bg_color', 'color', '<em>background</em> color of success message area' );
ppO( 'contact_success_text_color', 'color', '<em>text</em> color of success message' );
ppStopMultiple();
ppStartMultiple( 'Contact form submitted error message' );
ppO( 'contact_error_msg', 'text|33', 'text displayed when contact form error' );
ppO( 'contact_error_bg_color', 'color', '<em>background</em> color of error message area' );
ppO( 'contact_error_text_color', 'color', '<em>text</em> color of error message' );
ppStopMultiple();

// contact email to
$user_info = get_userdata( 1 );
ppO( 'contactform_emailto', 'text|60', 'successful contact form submissions will be sent to this address or admin address <code>' . $user_info->user_email . '</code> if left blank', 'Contact form email address' );

// contact email subject
ppO( 'contact_email_custom_subject', 'text|100', 'custom subject line for emails generated from contact form, if blank subject will be: <code>' . pp::site()->name . ' - site contact form submission</code>', 'Contact form message custom email subject' );

// disable ajax
ppStartMultiple( 'Contact form troubleshooting' );
ppO( 'contactform_ajax', 'radio|off|enable simple mode for loading|on|use standard ajax mode for loading', 'turn on <code>simple mode</code> if your contact form is not <strong>loading correctly</strong> even after trying all of the fixes <a href="' . pp::tut()->fixContactForm . '">here</a>.', 'Contact form simple mode' );

// troubleshooting tweaks
ppO( 'contact_sendmail_from', 'text|50', 'if your contact form is submitting successfully, but <strong>you are not receiving email</strong>, enter a <span style="text-decoration:underline">valid email address</span> with the same domain name as this site, something like <strong>example@' . NrUtil::extractDomain( pp::site()->url ) . '</strong> - but the email must actually exist' );
ppO( 'contact_blank', 'blank' );
ppO( 'contact_remote_send', 'radio|off|standard email sending|on|enable remote sending', 'as a last resort, if you don\'t get emails from your contact form even after trying all of the fixes described <a href="' . pp::tut()->fixContactForm . '">here</a>, enabling remote sending may cause the emails to come through' );

ppStopMultiple();
ppEndOptionSubgroup();



/* form subgroup */
ppOptionSubgroup( 'form' );

// content
ppO( 'contact_note', 'note', ppString::id( 'blurb_contact_form_widget_content' ), 'Contact form area additional content' );


// custom fields
ppStartMultiple( 'Additional custom fields' );
for ( $i = 1; $i <= pp::num()->maxContactFormCustomFields; $i++ ) {
	ppO( 'contact_customfield' . $i . '_label', 'text|29', 'label for custom field #' . $i );
	ppO( 'contact_customfield' . $i . '_required', 'radio|yes|required, may not be left blank|no|optional, may be left blank', 'designate whether the field is optional or required' );
	ppO( 'blank', 'blank' );

}
ppStopMultiple();

// form labels and headers
ppStartMultiple( 'Contact form headlines and labels' );
ppO( 'contactform_yourinformation_text', 'text|32', 'headline text for the top portion of the contact form' );
ppO( 'contactform_yourmessage_text', 'text|32', 'headline text for the bottom, message portion of the contact form' );
ppO( 'contactform_name_text', 'text|25', 'label for the <code>Name</code> input field' );
ppO( 'contactform_email_text', 'text|25', 'label for the <code>Email</code> input field' );
ppO( 'contactform_message_text', 'text|25', 'label for the <code>Message</code> input field' );
ppO( 'contactform_required_text', 'text|25', 'text shown when a field is required' );
ppO( 'contactform_submit_text', 'text|25', 'text on <code>submit</code> button' );
ppO( 'contact_form_invalid_email', 'text|25', 'text shown when an invalid email is submitted' );
ppO( 'anti_spam_explanation', 'text|25', 'text shown to explain anti-spam challenge' );
ppStopMultiple();

// anti spam questions
ppO('contactform_antispam_enable', 'radio|on|enabled|off|disabled', 'Enable/disable the contact form anti-spam challenges.  If you are having any issues with spam coming from your contact form (as opposed to blog comment spam) then be sure to leave this enabled', 'Anti-spam challenges' );
ppStartMultiple( 'Anti-spam questions' );
ppO( 'anti_spam_question_1', 'text|32', 'random challenge #1 - asked to prove form submitted by human and not spam bot' );
ppO( 'anti_spam_answer_1', 'text|18', 'answer to anti-spam challenge #1 <br /><em>not case-sensitive, if multiple answers are correct, separate with an asterisk like &quot;4*four&quot;</em>' );
ppO( 'anti_spam_blank_1', 'blank' );
ppO( 'anti_spam_question_2', 'text|32', 'random challenge #2 - asked to prove form submitted by human and not spam bot' );
ppO( 'anti_spam_answer_2', 'text|18', 'answer to anti-spam challenge #2 <br /><em>not case-sensitive, if multiple answers are correct, separate with an asterisk like &quot;4*four&quot;</em>' );
ppO( 'anti_spam_blank_2', 'blank' );
ppO( 'anti_spam_question_3', 'text|32', 'random challenge #3 - asked to prove form submitted by human and not spam bot' );
ppO( 'anti_spam_answer_3', 'text|18', 'answer to anti-spam challenge #3 <br /><em>not case-sensitive, if multiple answers are correct, separate with an asterisk like &quot;4*four&quot;</em>' );
ppStopMultiple();

ppEndOptionSubgroup();


/* log subgroup */
ppOptionSubgroup( 'log' );

ppO( 'contact_log_note', 'note', ppString::id( 'blurb_contact_log' ), 'About the Contact Form log' );
ppO( 'contact_log', 'function|ppContactLog' );

ppEndOptionSubgroup();


?>