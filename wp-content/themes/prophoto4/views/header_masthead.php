<div id="masthead-image-wrapper">

	<div id="masthead_image" class="<?php echo $classes ?>">

		<?php

		if ( $href && !NrUtil::isIn( 'pp-slideshow', $classes ) ) {
			echo NrHtml::a( $href, $img->markup() );
		} else {
			echo $img->markup();
		}

		?>

	</div><!-- #masthead_image -->

</div><!-- #masthead-image-wrapper -->