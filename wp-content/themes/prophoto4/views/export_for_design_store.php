<h1>Exporting design "<?php echo $design->name() ?>" for ProPhoto store</h1>


<h2>Step 1: Upload a design thumbnail</h2>

<p class="explain">Upload a screenshot of your design.  This screenshot will be used inside the customer's "Manage Designs" screen.</p>

<p> &nbsp;&nbsp;&nbsp;&nbsp;<b>NOTE:</b> must be <em>exactly</em> <code>360</code> by <code>360</code> pixels</p>

<?php media_upload_type_form( 'design_thumb' ); ?>

<h2>Step 2: Specify design widgets:</h2>

<p class="explain">For every widget, you need to indicate whether it is a critical part of your design and thus should be exported with your design.  Only select to include widgets that are unique, and constitute part of what is unique visually about your design.  Do not include relatively standard, or default widgets that do not affect the design because every widget you choose to include potentially will de-activate widgets the user already has in place, which can be jarring.</p>

<p class="explain">Also, if you need a certain widget area to be empty for the design to look right, you can also indicate that here.  Please only specify areas to be empty that are important to the visual appearance of your design to remain empty.  Any widgets in areas set to be empty will be automatically de-activated when your design is activated by a user.</p>

<form enctype="multipart/form-data" action="" method="post" id="main-form">

	<?php

	global $wp_registered_widgets, $wp_registered_sidebars;

	$sidebarsWidgets = get_option( 'sidebars_widgets' );
	unset( $sidebarsWidgets['wp_inactive_widgets'], $sidebarsWidgets['array_version'] );

	foreach ( $sidebarsWidgets as $widgetArea => $widgetsInArea ) {

		$areaTitle = NrHtml::h3( 'Widget area: ' . ucwords( str_replace( array( '-', 'col', 'drawer' ), array( ' ', 'column', 'sliding drawer' ), $widgetArea ) ) );

		if ( array() == $widgetsInArea && !NrUtil::isIn( 'drawer-', $widgetArea ) ) {

			echo $areaTitle;

			echo NrHtml::p( '<em>This widget area is currently empty, please choose from the below options:</em>' );

			if ( $widgetArea == 'fixed-sidebar' ) {
				echo NrHtml::p( 'Note: there is no on/off switch for the fixed sidebar in ProPhoto4.  If you do not force this area empty, and the user activating your design has widgets in the fixed sidebar, the sidebar will be shown.  So, if your design breaks, or looks very bad with a sidebar, be sure to set this to force empty.', 'style=color:red;' );
			}

			echo NrHtml::radio( 'empty_widget_area_' . $widgetArea, array(
				'no choice made <em>(not permitted)</em>' => 'no_choice',
				'<b>DO NOT FORCE EMPTY</b> - having widgets in this area will not mess up the design, leave any widgets the user already has in this area in place' => 'false',
				'<b>FORCE EMPTY</b> - for my design to look right initially, this widget area needs to be empty - deactivate any widgets in this area when my design is activated' => 'true',
			), 'no_choice' );
		}

		if ( $widgetsInArea ) {
			echo $areaTitle;
		}

		foreach ( $widgetsInArea as $widgetHandle ) {

			$params = array_merge(
				array( array_merge( $wp_registered_sidebars[$widgetArea], array('widget_id' => $widgetHandle, 'widget_name' => $wp_registered_widgets[$widgetHandle]['name']) ) ),
				(array) $wp_registered_widgets[$widgetHandle]['params']
			);

			$classname_ = '';
			foreach ( (array) $wp_registered_widgets[$widgetHandle]['classname'] as $cn ) {
				if ( is_string($cn) )
					$classname_ .= '_' . $cn;
				elseif ( is_object($cn) )
					$classname_ .= '_' . get_class($cn);
			}
			$classname_ = ltrim($classname_, '_');
			$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $widgetHandle, $classname_);


			$callback = $wp_registered_widgets[$widgetHandle]['callback'];


			if ( is_callable($callback) ) {
				echo '<div class="widget-wrap">';
				echo NrHtml::radio( 'include_widget_' . $widgetHandle, array(
					'no choice made <em>(not permitted)</em>' => 'no_choice',
					'<b>INCLUDE</b> this widget, it is <em>critical</em> for the design' => 'true',
					'<b>DO NOT INCLUDE</b> this widget, it is not critical for the design' => 'false',
				), 'no_choice' );
				ob_start();
				call_user_func_array($callback, $params);
				$widgetCode = ob_get_clean();
				if ( NrUtil::isIn( '<form', $widgetCode ) ) {
					$widgetCode = str_replace( array( '<form', '</form' ), array( '<div', '</div' ), $widgetCode );
				}
				echo $widgetCode;
				echo '</div>';
			}
		}
	}

	?>


	<p>
		<input type="submit" value="Submit" class="button-secondary">
	</p>
</form>


<?php // NrDump::it( $design ); ?>