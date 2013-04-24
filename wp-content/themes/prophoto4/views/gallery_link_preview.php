<div class="gallery-preview" id="gal-id-<?php echo $gallery->id() ?>">

	<input type="radio" name="gallery_selector" value="<?php echo $gallery->id() ?>">

	<?php echo $gallery->img(0)->thumb()->width( 55 )->height( 55 )->markup(); ?>

	<p class="title">
		<?php echo $gallery->title() ?>
	</p>

	<p class="img-count">
		<?php echo $num = count( $gallery->imgs() ) ?> images
	</p>

</div>