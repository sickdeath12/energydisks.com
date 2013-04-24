<?php if ( $menuItem->ID == null ) return; ?>

<div id="<?php echo $menuItem->ID ?>" class="menu-item sc draggable <?php echo $menuItem->type; if ( $menuItem->hasOwnChildren ) echo ' has-own-children' ?>" type="<?php echo $menuItem->type ?>" subtype="<?php echo $menuItem->className() ?>">

	<h3><?php echo $menuItem->text() ?></h3>

	<p class="nested-items-forbidden">no dropping</p>

	<?php

	if ( $children ) {
		foreach ( $children as $child => $maybeGrandchildren ) {
			$grandchildren = is_array( $maybeGrandchildren ) ? $maybeGrandchildren : null;
			ppUtil::renderView( 'menu_admin_item', array( 'menuItem' => ppMenuUtil::menuItem( $child ), 'children' => $grandchildren ) );
		}
	}

	?>

	<div class="drop-nested droppable">+drop here</div>

	<a class="delete edit-link" title="Delete menu item">x</a>
	<a href="<?php echo ppIFrame::url( 'edit_menu_item&menu_item_id=' . $menuItem->ID, 630, 560 )  ?>" class="edit edit-link edit-menu-item thickbox" title="Edit menu item">edit</a>
</div>