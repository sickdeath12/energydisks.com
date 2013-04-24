<?php

	/* build up section classes */
	$classes = array( 'grid-selectable', 'article', 'sc' );
	$classes[] = 'type-' . $article->type();
	if ( $isRecent ) {
		$classes[] = 'recent';
	}
	if ( $article->type() == 'post' ) {
		foreach ( $article->categoryIDs() as $categoryID ) {
			$classes[] = 'category-' . $categoryID;
		}
	}


?>
<div id="article-<?php echo $ID = $article->id(); ?>" class="<?php echo implode( ' ', $classes ); ?>" title="<?php echo esc_attr( $article->title() ); ?>" rel="<?php echo $ID ?>">

	<?php

	/* img thumbnail */
	if ( $excerptImg = $article->excerptImgTag( 'thumbnail' ) ) {
		echo $excerptImg;
	} else {
		echo NrHtml::img( 'http://prophoto.s3.amazonaws.com/img/grid-available-no-img.png' );
	}

	 ?>


	<h4>
		<?php echo $article->title(); ?>
	</h4>

	<p class="meta">

		<?php if ( $article->type() == 'post' ) { ?>

			<span class="published-date">
				<?php echo $article->publishedDate( 'n/d/y' ); ?>
			</span>

			<span class="categories">
				<?php echo implode( ', ', $article->categoryNames() ); ?>
			</span>

		<?php } else { ?>

			<span class="page">Page</span>

		<?php } ?>

	</p>

	<a class="delete" title="click to delete this post/page">&#215;</a>

</div>