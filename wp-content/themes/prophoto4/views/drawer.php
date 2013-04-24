<div id="drawer_<?php echo $drawerNum ?>" class="drawer">

	<div id="tab_<?php echo $drawerNum ?>" class="tab">

		<?php echo ppDrawer::tabTextMarkup( ppOpt::id( 'drawer_tab_text_' . $drawerNum ) ) ?>

	</div><!-- .tab -->

	<ul id="drawer_content_<?php echo $drawerNum ?>" class="drawer_content">

		<?php echo ppWidgetUtil::areaContent( 'drawer-' . $drawerNum ); ?>

	</ul><!-- .drawer_content -->

</div><!-- .drawer -->
