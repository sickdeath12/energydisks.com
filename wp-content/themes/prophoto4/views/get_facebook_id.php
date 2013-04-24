<style type="text/css" media="screen">
	label { display:block; margin:20px 0 7px; }
	code { margin-left:15px; }
	.error { color:red; font-weight:bold; }
</style>
<form action="" method="post">

	<?php if ( $errorMsg ) echo NrHtml::p( $errorMsg, 'class=error' ); ?>

	<p>
		Paste the full URL of your Facebook <b>personal profile page</b> <em>(not a "Page" you've 
		created for your business)</em> into this form. It will look like one of these two examples:
	</p>

	<p>
		<code>http://www.facebook.com/YourProfileName</code>
	</p>

	<p>
		<code>http://www.facebook.com/profile.php?id=123456789</code>
	</p>

	<label for="fb_profile_url" class="text-input-label fb_profile_url-text-input-label">
		<b>Paste your personal Facebook profile URL here:</b>
	</label>

	<input type="text" name="fb_profile_url" value="" class="nr-text" size="45" placeholder="http://www.facebook.com/YourProfileName" />

	<p>
		<input type="submit" value="Lookup numeric ID..." class="button-primary">
	</p>

</form>