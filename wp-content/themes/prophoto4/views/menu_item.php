<li id="<?php echo $item->id() ?>" class="<?php echo $item->classes() ?>"<?php echo $item->liAttr ?>>


	<?php echo $item->aTag() ?>

	<?php

	if ( $item->children ) {
		echo '<ul' . $item->childUlStyle . '>';
		if ( is_array( $item->children ) ) {
			foreach ( $item->children as $child => $grandchildren ) {
				$childItem = ppMenuUtil::menuItem( $child, $grandchildren );
				$childItem->render();
			}
		} else {
			echo $item->children;
		}
		echo '</ul>';
	}

	 ?>

</li>