<?php

/* custom icon image link */
class ppCustomIcon_Widget extends ppWidget {


	// widget setup
	function __construct() {
		$this->prettyName = 'Custom Icon';
		$this->description = 'Use a custom-uploaded image icon and link it to whatever you want.';
		$this->formWidth = 400;
		parent::__construct();
	}


	// widget output
	function widget( $args, $instance ) {
		extract( $args );

		$number = $instance['number'];
		$link   = $instance['link'];
		$target = ( $instance['target'] == '_blank' ) ? 'target="_blank"' : '';

		$icon = ppImg::id( 'widget_custom_image_' . $number );
		$note     = ( $instance['note'] ) ? "<p class='icon-note'>{$instance['note']}</p>" : '';

		echo $before_widget;

		echo $this->titleMarkup( $args, $instance );

		if ( !empty( $link ) && $link != 'http://' ) {
			if ( NrUtil::validEmail( $link ) ) {
				$link = 'mailto:' . $link;
			}
			$open_a = "<a id='{$widget_id}' href='{$link}' class='icon-link'{$target}>";
			$close_a = '</a>';
		} else {
			$open_a = '';
			$close_a = '';
		}

		$img_tag = "<img src='$icon->url' class='p4-custom-icon p4-png' alt='' $icon->htmlAttr />";

		if ( NrUtil::validEmail( $link ) )
			$markup = ppHtml::obfuscatedEmailLink( $open_a . $img_tag . $close_a ) . $note;
		else
			$markup = $open_a . $img_tag . $close_a . $note;

		echo apply_filters( 'p4_customicon_markup', $markup, $instance );

		echo $after_widget;
	}

	// update widget settings
	function update( $new_instance, $old_instance ) {
		if ( !empty( $new_instance['link'] ) && NrUtil::invalidEmail( $new_instance['link'] ) && $new_instance['link'] != '#' ) {
			$new_instance['link'] = ppUtil::prefixUrl( $new_instance['link'] );
		}
		return apply_filters( 'p4_customicon_update', $new_instance, $old_instance );
	}

	// widget admin form
	function form( $instance ) {
		$defaults = array(
			'number' => '1',
			'link' => '',
			'target' => 'self',
			'title' => '',
			'note' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$number = $instance['number'];
		$widgetImg = ppImg::id( 'widget_custom_image_' . $number );
		$preview_src = $widgetImg->url;
		$no_img_display = ( $widgetImg->exists ) ? 'none' : 'block';

		// currently available upload slots
		$upload_slots = '';
		for ( $i = 1; $i <= pp::num()->maxCustomWidgetImages; $i++ ) {
			if ( !ppImg::id( 'widget_custom_image_' . $i )->exists ) continue;
			$upload_slots .= "<option value='$i'";
			$upload_slots .= selected( $instance['number'], $i, NO_ECHO );
			$upload_slots .= ">$i</option>";
		}

		// show explanation or widget form
		if ( $upload_slots ) {
			$instructions_display = 'none';
			$form_display = 'block';
		} else {
			$instructions_display = 'block';
			$form_display = 'none';
		}

		echo $this->helpLink();

		ob_start(); ?>

	<div class="p4-custom-icon">

		<p class="no-imgs-uploaded" style="font-style:italic;display:<?php echo $instructions_display ?>;">To use this widget, first go <a href="<?php echo ppUtil::customizeURL( 'settings', 'widget_images' ) ?>">here</a> and upload one or more custom images for icons.  Then come back to select a custom image.</p>

		<div class="p4-custom-icon-preview" style="display:<?php echo $form_display ?>;">
			<p style="margin:0;padding:0;text-align:center;cursor:pointer;">
				<img class="p4-custom-icon-preview-image" <?php echo $widgetImg->htmlAttr ?> src="<?php echo $preview_src ?>" style="padding-bottom:10px"/>
			</p>
		</div>

		<div class="p4-custom-icon-form" style="display:<?php echo $form_display ?>;">
			<p>
				<select name="<?php echo $this->get_field_name( 'number' ); ?>" id="<?php echo $this->get_field_id( 'number' ); ?>" class="p4-number" onchange="javascript:ppCustomIconPreview(jQuery(this).parents('.p4-custom-icon'));">
					<?php echo $upload_slots ?>
					<option value="add">add...</option>
				</select>
				<label for="<?php echo $this->get_field_id( 'number' ); ?>">image number</label>

			</p>

			<p class="add-image-explain" style="font-style:italic;display:<?php echo $no_img_display ?>;">To add a custom image, go <a href="<?php echo ppUtil::customizeURL( 'settings', 'widget_images' ); ?>">here</a> and upload a new Custom Widget Image. Then return here and you will be able to select it from the list above.</p>

			<?php echo $this->labledTextInput( 'link', 'Links to:', $instance, 'column first-column' ); ?>

			<p class="column">
				<label for="<?php echo $this->get_field_id( 'target' ); ?>">Links open:</label>
				<select name="<?php echo $this->get_field_name( 'target' ); ?>" id="<?php echo $this->get_field_id( 'target' ); ?>" class="widefat">
					<option value="self"<?php selected( $instance['target'], 'self' ); ?>>in the same window</option>
					<option value="_blank"<?php selected( $instance['target'], '_blank' ); ?>>in a new window</option>
				</select>
			</p>
			<h3>Text:</h3>

			<?php echo $this->labledTextInput( 'title', 'Title: <em>(optional, above icon)</em>', $instance ); ?>

			<?php echo $this->labledTextInput( 'note', 'Label: <em>(optional, below icon)</em>', $instance ) ?>

			<p class="note">NOTE: <strong>Title &amp; Label</strong> will often be constrained to the <strong>width of the icon</strong>, so use only with a relatively large icon size.</p>
		</div> <!-- .p4-custom-icon-form -->
	</div> <!-- .p4-custom-icon -->
<?php
		$form = ob_get_clean();
		echo apply_filters( 'p4_customicon', $form, $instance );
	}

}


?>