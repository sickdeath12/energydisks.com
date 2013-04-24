<?php

if ( $item->children ) {
	$options = array( $item->text() => $item->text() );

	if ( is_array( $item->children ) && NrUtil::validUrl( end( $item->children ) ) ) {
		$options = array_merge( $options, $item->children );
	} else {
		foreach ( (array) $item->children as $child => $ignoreGrandchildren ) {
			$childItem = ppMenuUtil::menuItem( $child );
			$options[$childItem->text()] = $childItem->url();
		}
	}

	echo NrHtml::select( $item->id(), $options, null, array( 'data' => 'native-menu|false' ) );

} else {

	echo $item->aTag();
}

 ?>