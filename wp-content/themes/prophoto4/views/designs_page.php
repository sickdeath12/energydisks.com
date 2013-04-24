<div class="wrap pp-designs-page">

	<div class="icon32" id="icon-themes">
		<br/>
	</div>

	<h1 id="prophoto-page-title">
	 	<b>Manage</b> ProPhoto Designs
		<?php echo ppAdmin::videoIconLink( 'manage-designs-page', 'Manage Designs page' ); ?>
	</h1>

	<h2 style="display:none;"></h2>

	<?php echo $page->manuallyImportedDesignsMsg; ?>

	<div id="top-group" class="sc">


		<div id="active-design-wrap" class="sc">

			<h3>Active design</h3>

			<p class="secondary">
				Whenever you are saving customization decisions or uploading custom images, 
				all of your design choices are being saved to your <strong>active design</strong>. 
				Information about your current active design is below:
			</p>

			<?php

			if ( $page->activeDesign ) {
				 ppUtil::renderView( 'designs_page_design', array( 'design' => $page->activeDesign ) );
			 } else {
				echo NrHtml::p( 'Error loading active design.', 'style=color:red;' );
			}

			 ?>

		</div>


		<div id="new-designs-wrap" class="sc">

			<h3>Create new designs</h3>

			<p class="secondary">
				Click below to start a new design. You can always re-activate the current design if you change your mind.
			</p>

			<div>
				<a href='<?php echo ppIFrame::url( 'new_design_form', '800', '690' ); ?>' class='button-primary thickbox pp-design-btn'>Start a New Design</a>
			</div>

			<?php if ( $page->importDesignUrl ) { ?>
				<div>
					<a href="<?php echo $page->importDesignUrl; ?>" class='button-primary thickbox pp-design-btn'>Import P4 Designs</a>
				</div>
			<?php } ?>

			<div>
				<a href="<?php echo ppIFrame::url( "design_zip_upload&upload_type=design_zip", '410', '110' ); ?>" class='button-primary thickbox pp-design-btn'>Upload Design Zip</a>
			</div>

			<?php if ( $page->importP3DesignsUrl ) { ?>
				<div>
					<a href="<?php echo $page->importP3DesignsUrl; ?>" class='button-primary thickbox pp-design-btn'>Import P3 Designs</a>
				</div>
			<?php } ?>

			<?php if ( $page->exportEverythingUrl ) { ?>
				<a class='button-secondary thickbox' href="<?php echo $page->exportEverythingUrl; ?>">Export everything</a>
			<?php } ?>

			<?php if ( $page->showReset ) { ?>
				<form id='reset-everything' action='' method='post'>
					<input type='submit' value='Reset everything' class='button-secondary' />
					<?php echo ppUtil::idAndNonce( 'designs_page_reset_all' ); ?>
				</form>
			<?php } ?>

		</div><!-- #new-designs-wrap  -->

	</div><!-- #top-group  -->



	<?php if ( !empty( $page->inactiveDesigns ) ) { ?>

		<h3>
			Inactive designs
		</h3>

		<div id="inactive-designs-wrap" class="sc">
			<?php

			$counter = 0;
			foreach ( $page->inactiveDesigns as $design ) {
				ppUtil::renderView( 'designs_page_design', compact( 'design' ) );
				$counter++;
				if ( is_int( $counter / 2 ) ) {
					echo '<br style="clear:both" />';
				}
			}

			?>
		</div>

	<?php } ?>


	<h3>
		Starter-designs
	</h3>

	<p class="secondary">
		ProPhoto4 comes with <?php echo count( (array) ppStarterDesigns::data() ); ?> <b>built-in starter-designs</b> 
		that you can use as a template to customize your own design:
	</p>

	<div id="starter-designs" class="sc">
		<?php foreach ( ppStarterDesigns::data() as $starterDesign )
			ppUtil::renderView( 'designs_page_starter', compact( 'starterDesign' ) ); ?>
	</div>

	<form id="misc_form" action="" method="post">
		<input id="action" type="hidden" name="action" value="">
		<input id="value"  type="hidden" name="value"  value="">
		<input type="submit" value="misc"/>
		<?php echo ppUtil::idAndNonce( 'designs_page_misc' ); ?>
	</form>

</div>