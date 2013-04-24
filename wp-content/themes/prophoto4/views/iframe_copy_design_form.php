<h3>Copy Design</h3>

<p>
	Copying a design creates an <strong>exact copy of a design</strong> and stores it in
	 your inactive designs, where you can <strong>activate it later if you choose</strong>.
</p>

<p>
	This is helpful if you like your current design but want to keep experimenting -- 
	<strong>copying creates a snapshot of how your design looks right now</strong>
	that you can switch back to later if you want.
</p>

<p>
	<strong>Edit the design info below</strong> and click "Copy design" to save.
</p>

<form action="<?php echo trailingslashit( pp::site()->wpurl ); ?>?pp_iframe=designs_page_data_POSTed" method="post" accept-charset="utf-8">

	<p>
		<strong>Design copy name:</strong><br />
		<?php echo NrHtml::textInput( 'new_design_name', 'Copied design: ' . $design->name(), 41 ); ?>
	</p>

	<p>
		<strong>Design copy description:</strong><br />
		<?php echo NrHtml::textarea( 'new_design_desc', 'Copied design: ' . $design->desc(), 6 ); ?>
	</p>

	<p>
		<input type="submit" value="Copy design">
	</p>

	<?php echo NrHtml::hiddenInput( 'template', $design->id() ); ?>

	<?php echo ppUtil::idAndNonce( 'designs_page_copy' ); ?>

</form>