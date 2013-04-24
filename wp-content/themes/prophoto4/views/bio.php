<div id="bio" class="sc" style="display:<?php echo ppBio::minimized() ? 'none' : 'block' ?>;">

	<div id="bio-inner-wrapper" class="sc">

		<div id="bio-content" class="sc">

			<?php

			/* bio picture */
			echo ppBio::picMarkup();

			/* spanning column */
			if ( ppWidgetUtil::areaHasWidgets( 'bio-spanning-col' ) ) {
				echo NrHtml::ul( ppWidgetUtil::areaContent( 'bio-spanning-col' ), 'id=bio-widget-spanning-col' );
			}

			?>

			<div id="bio-widget-col-wrap" class="sc">

				<?php

				/* numbered widget columns */
				for ( $i = 1; $i <= pp::num()->maxBioWidgetColumns; $i++ ) {
					if ( ppWidgetUtil::areaHasWidgets( "bio-col-$i" ) ) {
						echo NrHtml::ul( ppWidgetUtil::areaContent( "bio-col-$i" ), "id=bio-col-{$i}&class=bio-col bio-widget-col" );
					}
				}

				?>

			</div><!-- #bio-widget-col-wrap -->

		</div><!-- #bio-content -->

	</div><!-- #bio-inner-wrapper -->

	<?php

	if ( ppOpt::test( 'bio_border', 'image' ) ) {
		echo NrHtml::div( '', 'id=bio-separator&class=sc' );
	}

	?>

</div><!-- #bio-->