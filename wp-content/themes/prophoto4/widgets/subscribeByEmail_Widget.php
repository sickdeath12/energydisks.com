<?php


class ppSubscribeByEmail_Widget extends ppWidget {

	// widget setup
	function __construct() {
		$this->prettyName = 'Subscribe by Email';
		$this->description = 'Feedburner "Subscribe by Email" widget';
		parent::__construct();
	}

	// widget output
	function widget( $args, $instance ) {
		if ( $feedburnerID = ppRss::feedburnerId() ) {
			extract( $args );

			$message       = $instance['message'] ? '<p>' . $instance['message'] . '</p>' : '';
			$feedburner_id = $feedburnerID;
			$language      = ppOpt::id( 'subscribebyemail_lang' );
			$submit_text   = $instance['submit_text'];

			echo $before_widget;
			echo $this->titleMarkup( $args, $instance );

			$markup = <<<HTML
			<form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="_blank">
				<p><input type="text" style="width:150px" name="email" /></p>
				<p style="margin-top:-8px;">$message</p>
				<input type="hidden" value="{$feedburner_id}" name="uri" />
				<input type="hidden" name="loc" value="{$language}" />
				<input type="submit" value="{$submit_text}" />
			</form>
HTML;
			echo apply_filters( 'p4_subscribebyemail_markup', $markup, $instance );

			echo $after_widget;
		}
	}

	// update widget settings
	function update( $new_instance, $old_instance ) {
		return apply_filters( 'p4_subscribebyemail_update', $new_instance, $old_instance );
	}

	// widget admin form
	function form( $instance ) {


		// option defaults
		$defaults = array(
			'title' => 'Subscribe by Email',
			'message' => 'Enter your email below to get notifications of blog updates by email.',
			'submit_text' => 'Subscribe',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		// build options for language select
		$lang_params = explode( '|', FEEDBURNER_LANG_OPTIONS );
		$num_params  = count( $lang_params );
		$lang_options = '';
		for ( $i = 1; $i <= $num_params; $i = $i + 2 ) {
			$value = $lang_params[$i-1];
			$name  = $lang_params[$i];
			$lang_options .= "<option value='$value'>$name</option>\n";
		}

		echo $this->helpLink();

		if ( !ppOpt::test( 'feedburner' ) ) {
			echo ppString::id( 'requires_feedburner_feed', ppUtil::customizeURL( 'content', 'feed' ) ) . '</p>';
			return;
		}

		ob_start(); ?>

	<div class="p4-subscribe-by-email">

		<div class="p4-subscribe-by-email-form">

			<p style="font-style:italic;font-size:.9em"><?php echo ppString::id( 'requires_email_subscription_enabled' ) ?></p>

			<?php echo $this->titleField( $instance ); ?>

			<?php echo $this->labledTextInput( 'submit_text', 'Submit button text:', $instance ); ?>

			<p>
				<label for="<?php echo $this->get_field_id( 'message' ); ?>">Message (optional):</label>
				<textarea class="widefat" rows="6" id="<?php echo $this->get_field_id( 'message' ); ?>" name="<?php echo $this->get_field_name( 'message' ); ?>"><?php echo $instance['message'] ?></textarea>
			</p>

		</div> <!-- .p4-subscribe-by-email-form -->
	</div> <!-- .p4-subscribe-by-email -->
<?php
		$form = ob_get_clean();
		echo apply_filters( 'p4_subscribebyemail_form', $form, $instance );
	}
}
?>