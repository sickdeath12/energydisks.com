<div class="gallery-edit-or-insert sc" data-gallery-id="<?php echo $gallery->id() ?>">

	<?php echo $gallery->img(0)->thumb()->width( 90 )->height( 90 )->markup(); ?>

	<div class="actions">

		<form action="" method="post">
			<?php echo NrHtml::hiddenInput( 'pp_gallery_id', $gallery->id() ); ?>
			<input type="submit" value="Edit gallery" class="button">
		</form>

		<form action="" method="post">
			<?php echo NrHtml::hiddenInput( 'pp_gallery_id', $gallery->id() ); ?>
			<?php echo NrHtml::hiddenInput( 'delete_pp_gallery', 'true' ); ?>
			<input type="submit" value="Delete gallery" class="button delete-gallery">
		</form>


		<form action="<?php echo ppMediaAdmin::uploadIFrameUrl( 'pp_galleries' ); ?>" method="post">
			<?php

			echo NrHtml::hiddenInput( 'pp_gallery_id', $gallery->id() );

			echo NrHtml::select( 'insert_pp_gallery_as', array(
				'insert as...'    => '',
				'Slideshow'       => 'slideshow',
				'Lightbox'        => 'lightbox',
				'Fullsize images' => 'fullsize_imgs',
			), null, 'class=insert-as' );

			?>
		</form>


	</div>

	<div class="facts">

		<p>Title: <em><?php echo $gallery->title(); ?></em></p>

		<?php

		if ( $gallery->subtitle() ) {
			echo NrHtml::p( 'Subtitle: <em>' . $gallery->subtitle() . '</em>' );
		}

		?>

		<p><?php echo count( $gallery->imgs() ) ?> images</p>

	</div>

</div>