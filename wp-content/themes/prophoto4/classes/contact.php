<?php

class ppContact {

	public static function render() {
		do_action( 'pp_pre_contact_form_outer' ); ?>
		<div id="pp-contact-success-msg" class="pp-contact-submit-msg">
		</div><!-- formsuccess -->
		<div id="pp-contact-error-msg" class="pp-contact-submit-msg">
		</div><!-- formerror -->
		<div id="contact-form" class="content-bg sc" style="display:none">
		<?php if ( ppOpt::test( 'contactform_ajax', 'off' ) ) include( TEMPLATEPATH . '/includes/contact-form.php' ); ?>
		</div><!-- #contact-form--><?php
		do_action( 'pp_post_contact_form_outer' );
	}

}
