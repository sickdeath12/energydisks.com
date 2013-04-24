<div id="addcomment" class="add-comment-form-wrap">

	<form id="add-comment" action="<?php echo pp::site()->wpurl ?>/wp-comments-post.php" method="post" data-ajax="false">

		<?php

		/* logged-in user */
		if ( ppUser::loggedIn() ) {

		?>

			<p id="login">

				<span class="loggedin">Currently logged in as
					<?php echo NrHtml::a( pp::site()->wpurl . '/wp-admin/profile.php', esc_html( ppUser::name() ) ); ?>.
				</span>

				<span class="logout">You may log out by
					<?php echo NrHtml::a( wp_logout_url( $article->permalink() ), 'clicking here' ); ?>.
				</span>


			</p>

		<?php

		/* standard visitor - not logged in - identity portion of form */
		} else {

			if ( get_option('require_name_email') ) {
				$requiredMark =  '<span class="required">*</span>';
				$requiredNote =  ' ' . ppOpt::translate( 'comments_required' ) . ' ' . $requiredMark;
			} else {
				$requiredMark = '';
				$requiredNote = '';
			}

		?>

			<p id="comment-notes">
				<?php echo ppOpt::translate( 'commentform_message' ) . $requiredNote; ?>
			</p>

			<div class="cmt-name">
				<p>
					<label for="author"><?php echo ppOpt::translate( 'comment_form_author_label' ); ?></label>
					<?php echo $requiredMark; ?>
				</p>
			</div>
			<div class="cmt-name">
				<input id="author" name="author" type="text" value="" size="40" maxlength="60" />
			</div>

			<div class="cmt-email">
				<p>
					<label for="email"><?php echo ppOpt::translate( 'comment_form_email_label' ); ?></label>
					<?php echo $requiredMark; ?>
				</p>
			</div>
			<div class="cmt-email">
				<input id="email" name="email" type="<?php echo pp::browser()->isMobile ? 'email' : 'text' ?>" value="" size="40" maxlength="60" />
			</div>

			<div class="cmt-url">
				<p>
					<label for="url"><?php echo ppOpt::translate( 'comment_form_url_label' ) ?></label>
				</p>
			</div>
			<div class="cmt-url">
				<input id="url" name="url" type="<?php echo pp::browser()->isMobile ? 'url' : 'text' ?>" value="" size="40" maxlength="60" />
			</div>


		<?php

		}

		?>

		<div id="addcomment-error">
			<span></span>
		</div>

		<div class="cmt-comment">
			<p>
				<label for="comment"><?php echo ppOpt::translate( 'comment_form_comment_text_label' ) ?></label>
			</p>
		</div>

		<div class="cmt-comment">
			<textarea id="comment" name="comment" cols="65" rows="12" tabindex="6"></textarea>
		</div>

		<div class="cmt-submit">
			<input id="submit" name="submit" type="submit" value="<?php echo ppOpt::translate( 'comment_form_submit_button_label' ); ?>" />
			<?php echo NrHtml::hiddenInput( 'comment_post_ID', $article->id() ); ?>
		</div>

		<?php do_action( 'comment_form', $article->id() ); ?>

	</form>

</div>