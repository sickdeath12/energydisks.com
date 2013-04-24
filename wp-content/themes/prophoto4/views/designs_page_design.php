<div class="design<?php if ( $design->is_active ) echo ' active-design black-glass-gradient-bg'; ?> gray-gradient-button">
	<h4>
		<?php echo stripslashes( $design->name ) ?>
	</h4>
	<p>
		<?php echo stripslashes( $design->desc ) ?>
	</p>
	<div class="edit-designs">
		<?php if ( !$design->is_active ) { ?>
			<a class="button-secondary delete_design" action="delete_design" val="<?php echo $design->id ?>">Delete</a>
			<a class="button-secondary activate_design" action="activate_design" val="<?php echo $design->id ?>">Activate</a>
		<?php } ?>
		<a class="button-secondary thickbox" href="<?php echo $design->copy_url; ?>">Copy</a>
		<a class="button-secondary thickbox" href="<?php echo $design->edit_url; ?>">Edit Info</a>
		<a class="button-secondary thickbox" href="<?php echo $design->export_url; ?>">Export</a>
		<?php if ( $design->export_for_store_url ) { ?>
			<a class="button-secondary" href="<?php echo $design->export_for_store_url; ?>" target="_blank">Export for design store</a>
		<?php } ?>
	</div>
</div>