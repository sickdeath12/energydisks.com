<h3>Start a new design:</h3>

<?php echo NrHtml::openForm( ppIFrame::url( 'designs_page_data_POSTed' ) ); ?>

	<p class="pp-form-required">
		New design name:<br />
		<?php echo NrHtml::textInput( 'new_design_name' ); ?>
		<span class="pp-form-error-msg-inline">* design name is required</span>
	</p>

	<p>Design description:<br />
		<textarea name="new_design_desc" rows="3" style="width:95%"></textarea>
	</p>

	<p>Base new design on:</p>

	<?php
	/* checkbox markup for basing new design on an EXISTING design */
	foreach ( ppStorage::designIds() as $designId ) {
		$design = ppStorage::requestDesign( $designId );
		if ( $design->id() == ppStorage::activeDesignId() ) {
			$checked = 'checked="checked"';
			$activePrefix = '<b>Current design:</b> ';
		} else {
			$checked = '';
			$activePrefix = '';
		} ?>
		<div class="option-wrap">
			<input value="<?php echo $design->id(); ?>" type="radio" name="template" <?php echo $checked ?>/>
			<label><?php echo $activePrefix . $design->name(); ?></label>
		</div>
		<?php
	}
	?>

	<div id="starter-design-wrap" class="sc">

		<?php
		/* checkbox markup for basing new design on STARTER TEMPLATE */
		foreach ( ppStarterDesigns::data() as $starterDesign ) { ?>
			<div class="option-wrap starter-design">
				<input value="<?php echo $starterDesign->id; ?>" type="radio" name="template">
				<label><i>Template:</i> <?php echo $starterDesign->name; ?></label>
				<img src="<?php echo pp::site()->extResourceUrl; ?>/img/starter_design_<?php echo $starterDesign->id ?>.jpg" />
			</div>
		<?php
		}
		?>

	</div>

	<p>
		<input type="submit" value="Save new design">
	</p>

	<?php echo ppUtil::idAndNonce( 'designs_page_create_new' ); ?>

</form>