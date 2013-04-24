<article id="article-<?php echo $article->id(); ?>" class="<?php echo ppHtml::postClasses( $article ); ?>">

	<div class="article-header">

		<h1 class="article-title article-title-wrap">
			<?php echo $article->title() ?> 
		</h1>

		<?php if ( !is_page() ) { ?>

			<p>
				<?php

				if ( ppOpt::test( 'dateformat', 'custom' ) ) {
					echo $article->publishedDate( ppOpt::id( 'dateformat_custom' ) );

				} else {
					echo $article->publishedDate( ppOpt::id( 'dateformat' ) );
				}

				?>
			</p>

			<p>
				<?php echo  ppHtml::categoryList(); ?> 
			</p>

		<?php } ?>

	</div>

	<div class="article-content sc" data-role="content">

		<?php echo $article->content(); ?>

	</div>


	<?php ppMobileHtml::renderPrevNextArticleLinks(); ?>

	<div id="comments-area">

		<?php
			$commentsRenderer = new ppCommentsRenderer( $article, ppQuery::instance() );
			$commentsRenderer->renderMobile();
		?>

	</div><!-- #comments-area -->

</article><!-- #article-<?php echo $article->id() ?>-->

