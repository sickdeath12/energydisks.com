<?php

/* twitter.com javascript widget */
class ppTwitterCom_Widget extends ppWidget {

	// widget setup
	function __construct() {
		$this->prettyName = 'Twitter.com Widget';
		$this->description = 'Add a customized Twitter Profile, Search, Faves, or List widget.';
		$this->formWidth = 300;
		parent::__construct();
	}

	// widget output
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		echo $this->titleMarkup( $args, $instance );
		$markup = '<div>' . $instance['widget_code'] . '</div>';
		echo apply_filters( 'p4_twittercom_markup', $markup, $instance );
		echo $after_widget;
	}

	// update widget settings
	function update( $new_instance, $old_instance ) {

		// deal with width
		preg_match( '/width: ([^,]+),/', $new_instance['widget_code'], $matches );
		if ( !$new_instance['width'] ) {
			$new_instance['width'] = $matches[1];
		} else if ( $new_instance['width'] != $matches[1] ) {
			$new_instance['widget_code'] = preg_replace( '/width: ([^,]+),/', "width: " . $new_instance['width'] . ",", $new_instance['widget_code'] );
		}


		// add modify the footer "follow_text" js param
		if ( !NrUtil::isIn( 'footer:', $new_instance['widget_code'] ) ) {
			$new_instance['widget_code'] = str_replace( 'new TWTR.Widget({', "new TWTR.Widget({\n  footer: '" . $new_instance['follow_text'] . "',", $new_instance['widget_code'] );
		} else {
			$new_instance['widget_code'] = preg_replace( '/footer: ([^,]+),/', "footer: '" . $new_instance['follow_text'] . "',", $new_instance['widget_code'] );
		}

		return apply_filters( 'p4_twittercom_update', $new_instance, $old_instance );
	}

	// widget admin form
	function form( $instance ) {

		$defaults = array(
			'title' => '',
			'widget_code' => '',
			'width' => '',
			'follow_text' => 'Follow Me',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		echo $this->helpLink();

		ob_start();  ?>

		<p>To create a Twitter Widget, go to <a href="<?php echo pp::tut()->extTwitterWidget ?>" target="_blank">this page</a> and then click "My Website" to select and customize your Twitter widget.  When you're done, click to get the code, copy it, <strong>and paste it below</strong>.</p>

		<textarea style="font-size:.9em;font-family:Courier,monospace;margin-bottom:1em" class="widefat" rows="12" cols="20" id="<?php echo $this->get_field_id( 'widget_code' ); ?>" name="<?php echo $this->get_field_name( 'widget_code' ); ?>"><?php echo $instance['widget_code'] ?></textarea>

		<?php echo $this->titleField( $instance ); ?>

		<?php echo $this->labledTextInput( 'follow_text', 'Follow text:', $instance ); ?>

		<?php if ( $instance['width'] ) { ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>">Width:</label>
			<input type="text" size="4" class="inline" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width'] ?>" id="<?php echo $this->get_field_id( 'width' ); ?>">px
		</p>
		<?php } ?>

<?php
		$form = ob_get_clean();
		echo apply_filters( 'p4_twittercom_form', $form, $instance );
	}
}


?>