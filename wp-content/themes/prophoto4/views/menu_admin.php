<div id="<?php echo $handle; ?>-menu-admin-wrap" class="menu-admin-wrap" rel="<?php echo $handle; ?>">

	<div class="new-menu-items-wrap">
		<?php

		for ( $i = 0; $i <= 15; $i++ ) {
			$newItem = ppMenuItem::newUntitled( $handle . '_item_' . ( $highestID + $i ) );
			ppUtil::renderView( 'menu_admin_item', array( 'menuItem' => $newItem, 'children' => null ) );
		}

		?>
	</div>

	<div class="add-new-link-wrap">
		<a href="#" class="add-new-link button">Add new menu item...</a>
		<span>Drag and drop menu items to reorder and nest</span>
	</div>

	<?php echo NrHtml::p( '<span></span>', 'class=gmail-warn menu-warn' ); ?>

	<div class="menu-item-viewer sc">
		<div class="menu-item-wrap sc">
			<?php

			foreach ( $menuItems as $menuItem => $maybeChildren ) {
				$children = is_array( $maybeChildren ) ? $maybeChildren : null;
				ppUtil::renderView( 'menu_admin_item', array( 'menuItem' => ppMenuUtil::menuItem( $menuItem ), 'children' => $children ) );
			}

			?>
		</div>
	</div>

</div><!-- .menu-admin-wrap -->