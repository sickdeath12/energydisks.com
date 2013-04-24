<div <?php echo $grid->itemAttributes( $gridItem ); ?>>

	<?php echo $gridItem->imgTag( $grid->imgDims() )->markup(); ?>

	<div class="grid-overlay">

		<span class="overlay-bg"></span>


		<h3>
			<a href="<?php echo $gridItem->url() ?>" <?php echo $gridItem->aAttr(); ?>>
				<?php echo $gridItem->title(); ?>
			</a>
		</h3>

		<?php if ( $gridItem->text() && $grid->itemSize() > 320 ) { ?>

			<p class="text">
				<?php echo $gridItem->text( $grid->itemSize() ); ?>
			</p>

		<?php } ?>

	</div>

</div>
