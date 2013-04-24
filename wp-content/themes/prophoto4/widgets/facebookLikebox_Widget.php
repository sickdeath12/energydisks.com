<?php


class ppFacebookLikebox_Widget extends ppWidget {


	protected $defaultWidth = 292;


	function __construct() {
		$this->slugname    = 'facebook-likebox';
		$this->prettyName  = 'Facebook Like Box';
		$this->description = 'Add a "Like Box" for your Facebook business page.';
		$this->formWidth   = 612;
		parent::__construct();
	}


	function widget( $args, $instance ) {
		echo $args['before_widget'];
		echo apply_filters( 'pp_facebook_like_box_markup', ppFacebook::likeBoxMarkup( $this->convertLegacy( $instance ) ), $instance, $this );
		echo $args['after_widget'];
	}


	function update( $new_instance, $old_instance ) {
		$new_instance['show-faces'] = isset( $new_instance['show-faces'] ) ? 'true' : 'false';
		$new_instance['header']     = isset( $new_instance['header'] )     ? 'true' : 'false';
		$new_instance['stream']     = isset( $new_instance['stream'] )     ? 'true' : 'false';
		if ( !is_numeric( $new_instance['width']) || $new_instance['width'] < 75 || $new_instance['width'] > 1000 ) {
			$new_instance['width'] = $this->defaultWidth;
		}
		return apply_filters( 'pp_facebook_like_box_update', $new_instance, $old_instance );
	}


	function form( $instance ) {
		$defaults = array(
			'href'         => 'http://www.facebook.com/prophotoblogs',
			'width'        => $this->defaultWidth,
			'show-faces'   => 'true',
			'header'       => 'true',
			'colorscheme'  => 'light',
			'stream'       => 'false',
		);

		$instance = wp_parse_args( (array) $this->convertLegacy( $instance ), $defaults );

		ppUtil::renderView( 'widget_form_facebook_like_box', array( 'likeBox' => $this, 'instance' => $instance ) );
	}


	protected function convertLegacy( $instance ) {
		if ( isset( $instance['box_code'] ) ) {
			$instance = ppLegacy::updateLikeBoxWidgetInstance( $instance['box_code'], $this->id_base, $this->number );
		}
		return $instance;
	}

}


