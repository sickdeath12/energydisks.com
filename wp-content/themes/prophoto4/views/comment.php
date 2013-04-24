<div id="comment-<?php echo $comment->id(); ?>" class="<?php echo $comment->classes(); ?>">

	<?php

	/* comment avatars */
	if ( ppOpt::test( 'comments_show_avatars', 'true' ) ) {
		echo $comment->avatar( ppOpt::id( 'comment_avatar_size' ) );
	}


	/* right-aligned comment timestamp */
	if ( ppOpt::test( 'comment_meta_position', 'inline' ) && ppOpt::test( 'comment_timestamp_display', 'right' ) ) {
		echo $comment->timeMarkup();
	}


	/* comment meta above actual comment */
	if ( ppOpt::test( 'comment_meta_position', 'above' ) ) {
		echo NrHtml::div( $comment->authorMarkup() . $comment->timeMarkup(), 'class=comment-meta-above' );
	}

	 ?>

	<div class="comment-text">

		<?php

		/* comment text */
		if ( ppOpt::test( 'comment_meta_position', 'inline' ) ) {
			$prepend = $comment->authorMarkup();
			$append  = ppOpt::test( 'comment_timestamp_display', 'left' ) ? $comment->timeMarkup() : '';
		} else {
			$prepend = $append = '';
		}
		if ( !$comment->approved() ) {
			$append .= NrHtml::span( ppOpt::id( 'comment_awaiting_moderation_text' ), 'class=awaiting-moderation' );
		}
		echo $comment->text( $prepend, $append );

		?>

	</div>

</div>