<div id="comment-<?php echo $comment->id(); ?>" class="<?php echo $comment->classes(); ?>">

	<?php

	echo NrHtml::div( $comment->authorMarkup() . $comment->timeMarkup(), 'class=comment-meta-above' );


	if ( ppOpt::test( 'comments_show_avatars', 'true' ) ) {
		echo $comment->avatar( 40 );
	}

	?>

	<div class="comment-text">

		<?php

		/* comment text */
		$append = $comment->approved() ? '' : NrHtml::span( ppOpt::id( 'comment_awaiting_moderation_text' ), 'class=awaiting-moderation' );
		echo $comment->text( '', $append );

		?>

	</div>

</div>