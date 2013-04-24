<?php
	$classes = array( 'grid-selectable', 'gallery', 'all', 'sc' );
	if ( NrUtil::startsWith( $gallery->id(), '10' ) ) {
		$classes[] = 'imported_p3';
	} else {
		$classes[] = 'month-' . date( 'my', $gallery->id() );
	}
	$classes = implode( ' ', $classes );

?>
<div id="gallery-<?php echo  $ID = $gallery->id(); ?>" class="<?php echo $classes; ?>" title="<?php echo esc_attr( $gallery->title() ); ?>" rel="<?php echo $ID ?>" style="display:block">

	<?php echo $gallery->img(0)->thumb( 'thumbnail' )->markup(); ?>

	<h4>
		<?php echo $gallery->title(); ?>
	</h4>

	<p class="meta">

		<?php if ( $gallery->subtitle() ) { ?>

			<span class="sub-title">
				<?php echo $gallery->subtitle(); ?>
			</span>

		<?php } ?>

		<span class="num-imgs">
			<?php echo ' (' . count( $gallery->imgs() ) . ' imgs)'; ?>
		</span>

	</p>

	<a class="delete" title="click to delete this gallery">&#215;</a>

</div>