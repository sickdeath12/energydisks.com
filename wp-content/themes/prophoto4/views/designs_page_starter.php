<div class="design-template thickbox sc gray-gradient-button">

	<img src="<?php echo pp::site()->extResourceUrl ?>/img/starter_design_<?php echo $starterDesign->id ?>.jpg" width="180" />

	<div class="desc">

		<h4>
			<?php echo stripslashes( $starterDesign->name ) ?>
		</h4>

		<p>
			<?php echo stripslashes( $starterDesign->desc ) ?>
		</p>

	</div>

	<div class="action-btns">
		<a href="<?php echo ppIFrame::url( 'new_design_from_starter_form&starter=' . $starterDesign->id, '420', '470' ) ?>" class="button-secondary thickbox">
			Create new design
		</a>
		<a href="<?php echo pp::site()->url ?>/?preview_design=<?php echo $starterDesign->id ?>" class="button-secondary" target="_blank">
			Preview new design
		</a>

		<?php if ( in_array( $starterDesign->id, array( 'late_august', 'emilie', 'vandelay', 'hayden', 'mercury', 'sunny_california' ) ) ) { ?>
			<a href="<?php echo PROPHOTO_SITE_URL ?>remote-files/<?php echo $starterDesign->id ?>_design_resource_kit.zip" class="button-secondary">
				Download resource kit
			</a>
		<?php } ?>

	</div>

</div>
