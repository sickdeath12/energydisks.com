<div id="gallery-visual-summary">

	<h3 class="media-title">Adding to gallery: <em><?php echo $gallery->title(); ?></em></h3>

	<div id="thumbs">

		<?php

		$showThumbs = array_slice( $gallery->imgs(), 0, 26 );

		if ( count( $showThumbs ) < count( $gallery->imgs() ) ) {
			$notAllShownMsg =
			'<span class="not-all-shown">
				(previewing just 26 out of ' . count( $gallery->imgs() ) . ' total images)
			</span>';
		} else {
			$notAllShownMsg = '';
		}

		echo NrHtml::p( count( $gallery->imgs() ) . " Images currently in gallery: $notAllShownMsg" );

		foreach ( $showThumbs as $img ) {
			echo $img->thumb()->width( 46 )->height( 46 )->markup();
		}

		?>

	</div>

</div><!-- #gallery-visual-summary -->