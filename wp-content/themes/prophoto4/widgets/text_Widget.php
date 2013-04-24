<?php

/* custom icon image link */
class ppText_Widget extends ppWidget {

	// widget setup
	function __construct() {
		$this->prettyName = 'Text';
		$this->description = 'Enhanced text widget with buttons for adding links and formatting text';
		$this->formWidth = 500;
		parent::__construct();
	}

	// widget output
	function widget( $args, $instance ) {
		extract( $args );

		$text  = $instance['text'];
		$text = str_replace( array( '<b>', '</b>', '<i>', '</i>' ), array( '<strong>', '</strong>', '<em>', '</em>' ), $text );
		if ( $instance['wpautop'] == 'true' || intval( $instance['wpautop'] ) == 1 ) $text = wpautop( $text );

		// obfuscate email addresses
		if ( NrUtil::isIn( 'href="mailto:', $text ) && preg_match( '/<a href\=\"mailto\:([^"]*)">([^<]*)<\/a>/', $text, $match ) ) {
			if ( !isset( $_GET['ajax'] ) ) {
				list( $full_link, $email, $linktext ) = $match;
				$text = str_replace( $full_link, ppHtml::obfuscatedEmailLink( NrHtml::a( 'mailto:' . $email, $linktext ) ), $text );
			}
		}

		echo $before_widget;

		echo $this->titleMarkup( $args, $instance );

		echo apply_filters( 'p4_p4text_markup', $text, $instance );

		echo $after_widget;
	}

	// update widget settings
	function update( $new_instance, $old_instance ) {
		$new_instance['wpautop'] = isset( $new_instance['wpautop'] );
		return apply_filters( 'p4_p4text_markup', $new_instance, $old_instance );
	}

	// widget admin form
	function form( $instance ) {

		$defaults = array( 'wpautop' => 1, 'text' => '', 'title' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		if ( !$instance['text'] || !NrUtil::isIn( '<', $instance['text'] ) ) $preview_class = ' hidden';

		$preview_text = force_balance_tags( $instance['text'] );
		if ( preg_match( '/(<script|<style|<form)/i', $preview_text ) ) {
			$preview_text = '';
		}

		if ( $instance['wpautop'] == 1 ) $preview_text = str_replace( "\n", '<br />', $preview_text );

		//echo $this->helpLink();

		ob_start(); ?>

	<div class="p4-text">

		<?php echo $this->titleField( $instance ); ?>

		<p class="p4-text-textarea-holder">
			<input class="p4_tag_btn" tabindex="-1" type="button" value="Email" tag="e" />
			<input class="p4_tag_btn" tabindex="-1" type="button" value="Link" tag="a" />
			<input class="p4_tag_btn" tabindex="-1" type="button" value="Italic" tag="i" />
			<input class="p4_tag_btn" tabindex="-1" type="button" value="Bold" tag="b" />
			<label for="<?php echo $this->get_field_id('text'); ?>">Text:</label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" rows="9"><?php echo $instance['text']; ?></textarea>
		</p>
		<input type="checkbox" name="<?php echo $this->get_field_name('wpautop'); ?>" value="true" id="<?php echo $this->get_field_id('wpautop'); ?>" <?php checked( $instance['wpautop'] ); ?> />&nbsp;<label for="<?php echo $this->get_field_name('wpautop'); ?>">Automatically add paragraphs.</label>

		<div class="p4-text-preview<?php echo $preview_class ?>">
			<h3>HTML Preview:</h3>
			<div class="p4-text-target">
				<?php echo $preview_text; ?>
			</div>
		</div>
	</div> <!-- .p4-text -->
<?php
		$form = ob_get_clean();
		echo apply_filters( 'p4_p4text_form', $form, $instance );
	}
}


