<h3>
	Start a new design:
</h3>

<?php echo NrHtml::openForm( ppIFrame::url( 'designs_page_data_POSTed' ) ); ?>

	<p class="pp-form-required">
		New design name:<br />
		<?php echo NrHtml::textInput( 'new_design_name' ); ?>
		<span class="pp-form-error-msg-inline">* design name is required</span>
	</p>

	<p>
		Design description:<br />
		<?php echo NrHtml::textarea( 'new_design_desc', '', 4 ); ?>
	</p>

	<p>
		New design is based on <b><?php echo $starter_name; ?></b>:
	</p>

	<p>
		<img src="<?php echo pp::site()->extResourceUrl; ?>/img/starter_design_<?php echo $starter_id ?>.jpg" />
	</p>

	<p>
		<input type="submit" value="Save new design">
	</p>

	<?php echo NrHtml::hiddenInput( 'template', $starter_id ); ?>

	<?php echo ppUtil::idAndNonce( 'designs_page_create_new' ); ?>

</form>