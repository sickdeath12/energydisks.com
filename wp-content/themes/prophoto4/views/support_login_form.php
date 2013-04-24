<?php echo NrHtml::openForm( $support->remoteAuthUrl ); ?>

	<p>
		Only authenticated ProPhoto employees can access this page
	</p>

	<?php echo NrHtml::labledTextInput( 'Password: ', 'pp_support_auth_pass', '' ); ?>

	<?php echo NrHtml::hiddenInput( 'blog_url', $support->blogUrl ); ?>
	<?php echo NrHtml::hiddenInput( 'refer_url', $support->blogUrl . '?pp_support=true' ); ?>

	<p>
		<input type="submit" value="submit &rarr;">
	</p>

</form>