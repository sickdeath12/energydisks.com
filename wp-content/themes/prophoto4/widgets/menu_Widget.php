<?php


class ppMenu_Widget extends ppWidget {

	// widget setup
	function __construct() {
		$this->prettyName = 'Vertical Nav Menu';
		$this->description = 'Insert a custom, vertical ProPhoto navigation menu.';
		$this->formWidth = 300;
		parent::__construct();
	}

	// widget output
	function widget( $args, $instance ) {

		if ( !isset( $instance['widget_menu_num'] ) ) {
			return;
		}

		extract( $args );

		echo $before_widget;

		echo $this->titleMarkup( $args, $instance );

		$menuItems = ppMenuUtil::menuItems( 'widget_menu_' . $instance['widget_menu_num'] );
		if ( $menuItems ) {
			echo '<ul class="pp-widget-menu pp-widget-menu-' . $instance['widget_menu_num'] . '">';
			foreach ( $menuItems as $menuItem => $maybeChildren ) {
				$item = ppMenuUtil::menuItem( $menuItem, $maybeChildren );
				$item->render();
			}
			echo '</ul>';
		}

		echo $after_widget;
	}

	// update widget settings
	function update( $new_instance, $old_instance ) {
		return apply_filters( 'pp_menu_widget_update', $new_instance, $old_instance );
	}

	// widget admin form
	function form( $instance ) {

		$defaults = array(
			'title' => '',
			'widget_menu_num' => '1',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );


		$selectOptions = array();
		for ( $i = 1; $i <= pp::num()->maxWidgetMenus; $i++ ) {
			if ( ppMenuUtil::menuHasItems( 'widget_menu_' . $i ) ) {
				$selectOptions['Vertical Nav. Menu #'.$i] = $i;
			}
		}

		$menuCustomizationURL = ppUtil::customizeURL( 'menus', 'widget_menu_1' );

		if ( $selectOptions ) {

			$form = $this->titleField( $instance );

			$form .= NrHtml::select(
				$this->get_field_name( 'widget_menu_num' ),
				$selectOptions,
				$instance['widget_menu_num'],
				'id=' . $this->get_field_id( 'widget_menu_num' )
			);

			$form .= NrHtml::p(
				'Select above one of the custom menu structures you created from the ProPhoto 
				 <a href="' . $menuCustomizationURL . '">menu customization</a> page.',
				'class=pp-widget-form-note&style=margin-top:1em;'
			);



		} else {
			$form = NrHtml::p(
				'You must create menu items in at least one of the <b>Vertical Nav Menu</b> 
				 sub-sections of the ProPhoto <a href="' . $menuCustomizationURL . '">menu 
				 customization</a> page, then refresh this page and you will be able to 
				 select it from within this widget.'
			);
		}

		echo apply_filters( 'pp_menu_widget_form', $form, $instance );
	}
}


