<h3>Import ProPhoto version 3 Designs:</h3>

<p>Select which designs you would like to import and click "import".</p>

<form action="" method="post" accept-charset="utf-8">

	<?php

	foreach ( $designs as $designID => $designName ) {
		echo NrHtml::labledCheckbox( stripslashes( $designName ), $designID ) . '<br />';
	}

	echo NrHtml::hiddenInput( 'import_p3_designs', 1 );

	?>

	<p>
		<?php echo NrHtml::submit( 'Import' ); ?>
	</p>

</form>