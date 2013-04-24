<div id="article-comments-<?php echo $article->id(); ?>" class="<?php echo $commentsRenderer->areaClasses(); ?>">

	<div class="comments-header sc">

		<?php

		/* open boxy extra divs */
		if ( ppOpt::test( 'comments_layout', 'boxy' ) ) {
			echo '<div class="comments-header-inner"><div class="comments-header-inner-inner">';
		}

		?>

		<div class="comments-header-left-side-wrap sc">

			<?php

			/* optional article author link */
			if ( ppOpt::test( 'comments_header_show_article_author', 'true' ) ) {  ?>

				<p class="article-byline">
					<?php echo ppOpt::translate( 'by' ) . ' '; ?>
					<a href="<?php echo $article->authorArchiveUrl(); ?>" title="View posts by <?php echo esc_attr( $article->authorName() ) ?>">
						<?php echo $article->authorName(); ?>
					</a>
				</p>

				<?php
			}

			?>

			<div class="comments-count" id="comments-count-<?php echo $article->id(); ?>">

				<div class="comments-count-inner">

					<p>
						<?php echo $commentsRenderer->countMarkup(); ?>
					</p>

					<?php

					/* optional show/hide comments button */
					if ( ppOpt::test( 'comments_layout', 'minima' ) && ppOpt::test( 'comments_show_hide_method', 'button' ) ) {
						echo '<div id="show-hide-button"></div>';
					}

					?>

				</div><!-- .comments-count-inner -->

			</div><!-- .comments-count -->

		</div><!-- .comments-header-left-side-wrap -->

		<div class="post-interact">
			<?php echo $commentsRenderer->interactLinksMarkup(); ?>
		</div><!-- .post-interact -->

		<?php

		/* close boxy extra divs */
		if ( ppOpt::test( 'comments_layout', 'boxy' ) ) {
			echo '</div></div>';
		}

		?>

	</div><!-- .comments-header -->

	<div id="comments-body-<?php echo $article->id(); ?>" class="comments-body">

		<div class="comments-body-inner-wrap">

			<div class="comments-body-inner">
				<?php

				/* render comments */
				$comments = $commentsRenderer->articleComments();
				foreach ( $comments as $comment ) {
					ppUtil::renderView( 'comment', compact( 'comment' ) );
				}

				 ?>

			</div> <!-- .comments-body-inner -->

		</div> <!-- .comments-body-inner-wrap -->

	</div><!-- .comments-body -->

</div><!-- .article-comments -->

<div id="addcomment-holder-<?php echo $article->id(); ?>" class="addcomment-holder"></div>

