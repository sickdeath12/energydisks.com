<div id="article-comments-<?php echo $article->id(); ?>" class="<?php echo $commentsRenderer->areaClasses(); ?>">

	<h3 class="comments-count" id="comments-count-<?php echo $article->id(); ?>">
		<?php echo $commentsRenderer->countMarkup(); ?>
	</h3>

	<div id="comments-body-<?php echo $article->id(); ?>" class="comments-body">

		<?php

		/* render comments */
		$comments = ppOpt::test( 'reverse_comments', 'false' ) ? $article->comments() : array_reverse( $article->comments() );
		foreach ( $comments as $comment ) {
			ppUtil::renderView( 'mobile_comment', compact( 'comment' ) );
		}

		 ?>

	</div><!-- .comments-body -->

</div><!-- .article-comments -->
