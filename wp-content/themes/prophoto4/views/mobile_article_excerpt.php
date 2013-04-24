<li>

	<a href="<?php echo $article->permalink(); ?>"<?php if ( $article->hasSlideshowWithMusic() ) echo ' rel="external"' ?>>

		<?php echo $article->excerptImgTag( '160x160xCROP' ); ?>

		<h2>
			<?php echo $article->title(); ?> 
		</h2>

		<?php echo $article->excerpt(); ?>
	</a>

</li>

