<?php


class ppGrid_Widget extends ppWidget {


	function __construct() {
		$this->prettyName = 'Grid';
		$this->description = ppString::id( 'what_is_grid' );
		$this->formWidth = 635;
		parent::__construct();
	}


	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		echo $this->titleMarkup( $args, $instance );
		$grid = ppGrid::instance( 'widget_' . $this->number );
		$grid->render();
		echo $after_widget;
	}


	function update( $new_instance, $old_instance ) {
		return apply_filters( 'pp_grid_widget_update', $new_instance, $old_instance );
	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );

		// an existing, already saved grid widget
		if ( ppOpt::exists( 'grid_widget_' . $this->number ) ) {
			$grid = ppGrid::instance( 'widget_' . $this->number );
			if ( !empty( $_POST ) ) {
				$grid = $grid->update( $_POST );
			}

		// saving a new widget
		} else if ( isset( $_POST['multi_number'] ) && !ppOpt::exists( 'grid_widget_' . $this->number ) ) {
			$grid = ppGrid::emptyInstance( 'widget_' . $this->number );
			$grid = $grid->update( $_POST );
			ppStorage::saveCustomizations();

		// an empty widget, waiting to be used
		} else {
			$grid = ppGrid::emptyInstance( 'widget_' . $this->number );
		}

		$form  = NrHtml::p( ppString::id( 'what_is_grid' ) . ' <a href="' . pp::tut()->grids .'">Tutorial here</a>.', 'class=explain' );
		$form .= $this->labledTextInput( 'title', 'Grid Title (optional):', $instance, 'grid-title' );
		$form .= ppUtil::renderView( "grid_admin", compact( 'grid' ), ppUtil::RETURN_VIEW );

		echo apply_filters( 'pp_menu_widget_form', $form, $instance );

		global $pagenow;
		if ( isset( $pagenow ) && $pagenow == 'admin-ajax.php' ) {
			echo NrHtml::script( "jQuery('body').trigger('grid-reload');" );
		}
	}


	public static function handleDelete() {
		if ( NrUtil::POST( 'id_base', 'pp-grid' ) && isset( $_POST['delete_widget'] ) && $_POST['delete_widget'] ) {
			ppOpt::delete( 'grid_widget_' . str_replace( 'pp-grid-', '', $_POST['widget-id'] ) );
			ppStorage::saveCustomizations();
		}
	}


	public static function _onClassLoad() {
		add_action( 'sidebar_admin_setup', 'ppGrid_Widget::handleDelete' );
	}
}


