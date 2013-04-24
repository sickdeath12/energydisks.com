<div class="wrap pp-customize-page-area area-<?php echo $selectedArea; ?>">

	<div id="area-title-wrap" class="sc">

		<div class="icon32" id="icon-themes">
			<br/>
		</div>

		<h1 id="prophoto-page-title">
			<b>Customize</b> ProPhoto
		</h1>

		<div class="pp-admin-dropdown-wrap">

			<ul class="pp-admin-dropdown">

				<li id="pp-admin-dropdown-<?php echo $selectedArea ?>" class="current">
					<?php echo $areas->{$selectedArea}->title; ?> <span class="arrow">&nbsp;</span>
				</li>

				<li id="pp-admin-dropdown-dashboard">
					<a href="<?php echo admin_url( 'admin.php?page=pp-customize' ) ?>" title="Customize ProPhoto dashboard">
						<span class="icon"></span>
						Customize&hellip;
					</a>
				</li>

				<?php foreach ( $areas as $customizeAreaID => $customizeArea ): ?>

					<?php if ( $customizeAreaID != $selectedArea )  { ?>
						<li id="pp-admin-dropdown-<?php echo $customizeAreaID ?>">
							<a href="<?php echo ppUtil::customizeURL( $customizeAreaID ); ?>" title="<?php echo $customizeArea->desc ?>">
								<span class="icon"></span>
								<?php echo $customizeArea->title; ?>
							</a>
						</li>
					<?php } ?>

				<?php endforeach; ?>

			</ul>

		</div>

		<?php

		if ( $videoURL = ppAdmin::optionVideoURL( $slug = 'customize_overview_' . $selectedArea ) ) {
			ppAdmin::showVideoFirstTime( $slug );
			echo ppAdmin::videoIconLink( $slug, $customizeArea->title );
		 }

		?>

	</div>

	<h2 style="display:none"></h2> <!-- for javascript to move admin notices after  -->

	<p id="design-info">
		You are customizing your current active design: <b><?php echo ppActiveDesign::name(); ?></b>. 
		<a href="<?php echo ppUtil::manageDesignsURL() ?>">Manage designs &raquo;</a>
	</p>

	<form id="pp-customize-form" method="post" action="" class="<?php echo ppHelper::logoInMasthead() ? 'sameline' : '' ?>">

		<?php

		include( TEMPLATEPATH . "/adminpages/options/$selectedArea.php" );
		do_action( "pp_customize_after_area_options_{$selectedArea}" );

		?>

		<?php echo ppUtil::idAndNonce( 'customize_page' ); ?>


		<p class="submit sc">
			<input id="p4-save-changes" type="submit" value="Save Changes" name="Submit" class="button-primary"/>
		</p>
	</form>




</div>