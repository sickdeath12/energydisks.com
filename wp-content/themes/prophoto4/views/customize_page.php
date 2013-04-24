<div class="wrap pp-customize-page">

	<div class="icon32" id="icon-themes">
		<br/>
	</div>

	<?php

	echo ppAdmin::videoIconLink( 'customize-overview', 'Customizations Overview' );
	ppAdmin::showVideoFirstTime( 'customize-overview' );

	?>

	<h1 id="prophoto-page-title">
		<b>Customize</b> ProPhoto
	</h1>

	<h2 style="display:none"></h2> <!-- for javascript to move admin notices after  -->

	<p id="design-info">
		You are customizing your current active design: <b><?php echo ppActiveDesign::name(); ?></b>.
		<a href="<?php echo ppUtil::manageDesignsURL() ?>">Manage designs &raquo;</a>
	</p>

	<?php foreach ( $areas as $id => $area ) { ?>

		<a href="<?php echo ppUtil::customizeURL( $id ) ?>" id="section-button-<?php echo $id; ?>" class="section-button gray-gradient-button">
			<h3 class="title"><?php echo $area->title ?></h3>
			<p class="desc"><?php echo $area->desc ?></p>
			<span class="icon"></span>
		</a>

	<?php } ?>

</div>