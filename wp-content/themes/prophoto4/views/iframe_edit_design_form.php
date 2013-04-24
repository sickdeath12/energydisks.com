<h3>Update Design Meta Info</h3>

<form action="<?php echo trailingslashit( pp::site()->wpurl ); ?>?pp_iframe=designs_page_data_POSTed" method="post" accept-charset="utf-8">

	<p>Design name:<br />
		<?php echo NrHtml::textInput( 'design_name', $design->name(), 41 ); ?>
	</p>

	<p>Design description:<br />
		<?php echo NrHtml::textarea( 'design_desc', $design->desc(), 6 ); ?>
	</p>

	<p>
		<input type="submit" value="Update design info">
	</p>

	<?php echo NrHtml::hiddenInput( 'design_id', $design->id() ); ?>

	<?php echo ppUtil::idAndNonce( 'designs_page_edit_meta' ); ?>

</form>