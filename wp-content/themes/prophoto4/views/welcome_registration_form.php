<div class="wrap welcome-wrap">

	<?php screen_icon( 'themes' ); ?><h2>ProPhoto Registration</h2>

	<?php

	if ( NrUtil::GET( 'status', 'failure' ) ) {

		$contactUs = '<a href="' . pp::tut()->contactUs . '">contact us</a>';

		switch ( urldecode( $_GET['msg'] ) ) {

			case 'Missing registration data':
				$errorMsg = 'Some required information was not submitted, please retry.';
				break;

			case 'Invalid transaction ID':
				$errorMsg = 'Invalid Transaction ID format detected, please double-check and retry.';
				unset( $_GET['txn_id'] );
				break;

			case 'No matching purchase record found':
				$errorMsg = "That combination of Transaction ID and Email address <b>was not found in our database</b>.
							 Please double-check and try again or $contactUs if you feel there has been an error.";
				break;

			case 'Incorrect product type':
				$errorMsg = 'We found a record for that transaction, but <b>it is not the correct product type</b>.
							 Please enter information from your purchase of either ProPhoto, ProPhoto with installation,
							 or a ProPhoto upgrade.';
				unset( $_GET['txn_id'] );
				unset( $_GET['payer_email'] );
				break;

			case 'Too many registrations':
				$errorMsg = 'It appears that you are running more installations of ProPhoto than the two (2) allowed per license.
							 Please contact us if you feel there has been an error.';
				break;

			default:
				$errorMsg = 'Sorry, and unknown error has occurred.  Please try again.';
		}

		echo NrHtml::p( $errorMsg, 'class=registration-error welcome-error pp-admin-msg' );
	 }

	?>


	<h3>Register your copy of ProPhoto:</h3>

	<p>
		Before you do anything else, you must enter the email address and transaction ID associated with 
		your purchase of ProPhoto. You cannot use ProPhoto until you have entered this information.
	</p>

	<h4>How do I find these?</h4>

	<p>
		The moment your purchase was completed, <strong>we sent you an email with both of these pieces 
		of info</strong>.  Just find that email and copy/paste in the two items below. Make sure you're 
		checking the email address you used when purchasing (if you have a paypal account, this would 
		be your PayPal account email).
	</p>

	<p>
		<strong>Still no joy?</strong> If your email program/site has a search feature, search for
		the word <code>_pp_purchase</code>.
	</p>


	<form action="<?php echo PROPHOTO_SITE_URL ?>" method="post" id="register-form">

		<?php

		echo NrHtml::hiddenInput( 'requestHandler', 'Registration::request' );
		echo NrHtml::hiddenInput( 'return_url', admin_url( 'themes.php?pp_welcome=register_response' ) );
        echo NrHtml::hiddenInput( 'blog_url', pp::site()->url );
        echo NrHtml::hiddenInput( 'pp_version', 'P4' );
		echo NrHtml::hiddenInput( 'uid', ppUid::get() );

		?>

		<div id="payer-email-wrap">
			<?php echo NrHtml::labledTextInput( 'Payer Email:', 'payer_email', isset( $_GET['payer_email'] ) ? $_GET['payer_email'] : '', 31 ); ?>
			<span class="hidden-msg">Please enter a valid email</span>
		</div>

		<div id="txn-id-wrap">
			<?php echo NrHtml::labledTextInput( 'Transaction ID:', 'txn_id', isset( $_GET['txn_id'] ) ? $_GET['txn_id'] : '', 31 ); ?>
			<span class="hidden-msg">Must be 17 letters/numbers, like: <code>5PH528963D057T007</code></span>
		</div>

		<?php if ( !ppImportP3::isP3User() ) { ?>

			<div id="bluehost-wrap">
				<?php echo NrHtml::labledCheckbox( 'I signed up with <em>Bluehost.com</em> and would like to receive my $30 rebate. <a id="bluehost-explanation" title="what does this mean?">?</a>', 'bluehost_rebate' ); ?>
				<p id="bluehost-more-info" class="hidden-msg">
					ProPhoto offers a $30 rebate for anyone who signs up for a new hosting account with our hosting partner, 
					<a href="http://www.bluehost.com/track/netrivet/in-theme-activation/">Bluehost.com</a>. Full information 
					about the offer <a href="<?php echo PROPHOTO_SITE_URL; ?>support/about/bluehost-rebate/">is here</a>.
				</p>
			</div>

		<?php } ?>

		<?php echo NrHtml::submit( 'Submit', 'class=button-secondary' ); ?>

	</form>


</div>