<div <?php echo $grid->itemAttributes( $gridItem ) ?>>

	<a href="<?php echo $gridItem->url() ?>" title="permalink to <?php echo esc_attr( $gridItem->title() ); ?>"<?php echo $gridItem->aAttr(); ?>>
		<?php echo $gridItem->imgTag( $grid->imgDims() )->markup(); ?>
	</a>

	<h3>
		<a href="<?php echo $gridItem->url() ?>" title="permalink to <?php echo esc_attr( $gridItem->title() ); ?>"<?php echo $gridItem->aAttr(); ?>>
			<?php echo $gridItem->title(); ?>
		</a>
	</h3>

</div>
