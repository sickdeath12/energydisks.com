<div class="fb-like-button-widget-wrap sc" data-widget-id="<?php echo $likeBox->id ?>" data-locale="<?php echo ppOpt::id( "facebook_language" ); ?>">

	<div class="fb-like-box-preview <?php echo $instance['colorscheme'] ?>" style="width:<?php echo $instance['width'] ?>px;">
		<?php echo ppFacebook::likeBoxMarkup( $instance ); ?>
	</div>

	<div class="fb-like-box-form <?php echo ( $instance['stream'] == 'true' ) ? 'show-stream ' : 'hide-stream '; echo ( $instance['show-faces'] == 'true' ) ? 'show-faces' : 'hide-faces'; ?>">

		<?php echo $likeBox->helpLink(); ?>

		<p id="fb-page-explain">
			To create a Facebook Like Box, you first need to have a Facebook "Page" set up for your business. If you don't have a 
			Facebook "Page", <a href="<?php echo pp::tut()->extFacebookBizPage ?>" target="_blank">click here</a> to create one.
		</p>

		<?php

		echo $likeBox->labledTextInput( 'href', 'Facebook page URL:', $instance );
		echo $likeBox->labledTextInput( 'width', 'Width (pixels):', $instance, 'number pp-widget-form-column pp-widget-form-left-column' );

		?>

		<p class="pp-widget-form-column pp-widget-form-right-column">
			<label for="<?php echo $likeBox->get_field_id( 'colorscheme' ); ?>">Color scheme:</label>
			<select name="<?php echo $likeBox->get_field_name( 'colorscheme' ); ?>" id="<?php echo $likeBox->get_field_id( 'colorscheme' ); ?>" class="widefat">
				<option value="light"<?php selected( $instance['colorscheme'], 'light' ); ?>>light</option>
				<option value="dark"<?php selected( $instance['colorscheme'], 'dark' ); ?>>dark</option>
			</select>
		</p>

		<p class="pp-widget-form-column pp-widget-form-left-column" style="margin-top:5px">
			<?php $likeBox->checkbox( 'stream', 'Show activity stream', 'true', ( $instance['stream'] == 'true' ) ); ?>
		</p>

		<p class="pp-widget-form-column pp-widget-form-right-column">
			<?php $likeBox->checkbox( 'show-faces', 'Show faces', 'true', ( $instance['show-faces'] == 'true' ) ); ?>
		</p>

		<p class="show-header-option">
			<?php $likeBox->checkbox( 'header', 'Show header', 'true', ( $instance['header'] == 'true' ) ); ?>
		</p>
	</div>

</div>

<script>
	jQuery(document).ready(function($){
		likeBoxPreview.load('<?php echo $likeBox->id ?>');
	});
</script>