<article id="article-<?php echo $article->id(); ?>" class="<?php echo ppHtml::postClasses( $article ); ?>">

	<div class="article-wrap sc content-bg">

		<div class="article-wrap-inner">

			<?php

			/* article header */
			ppPostHeader::render( $article );

			?>

			<div class="article-content sc" data-role="content">

				<?php

				/* excerpt */
				if ( ppOpt::test( 'excerpts_on_' . ppUtil::pageType(), 'true' ) && !ppGallery::isGalleryQuasiPage() ) {
					echo $article->excerpt();

				/* full content */
				} else {

					if ( ppOpt::test( 'lazyload_imgs', 'true' ) ) {
						echo ppLazyLoad::filter( $article->content() );

					} else {
						echo $article->content();
					}
				}

				 ?>

			</div><!-- .article-content -->

			<?php

			/* post footer meta */
			ppHtml::renderPostFooterMeta();

			/* call to action btns */
			ppCallToAction::render( 'above_comments' );

			/* comments */
			$commentsRenderer = new ppCommentsRenderer( $article, ppQuery::instance() );
			$commentsRenderer->render();

			/* call to action btns */
			ppCallToAction::render( 'below_comments' );


			 ?>

		</div><!-- .article-wrap-inner -->

		<div class="article-footer"></div>

	</div><!-- .article-wrap -->

</article><!-- #article-<?php echo $article->id() ?>-->

