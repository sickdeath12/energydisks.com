<?php

class ppWidget extends WP_Widget {


	protected $prettyName;
	protected $slugname;
	protected $description;
	protected $formWidth;
	protected $formOptions = array();


	public function __construct() {
		if ( !$this->slugname ) {
			$this->slugname = strtolower( str_replace( array( ' Widget', '.', ' ' ), array( '', '-', '-' ), $this->prettyName ) );
		}
		if ( $this->formWidth ) {
			$this->formOptions['width'] = strval( $this->formWidth );
		}
		parent::__construct(
			'pp-' . $this->slugname,
			'ProPhoto ' . $this->prettyName,
			array( 'description' => $this->description ),
			$this->formOptions
		);
	}


	public function helpLink() {
		$url = PROPHOTO_SITE_URL . "support/about/{$this->slugname}-widget/";
		return <<<HTML
		<a class="pp-widget-help" href="$url" target="_blank" title="click for tutorial on P4 $this->prettyName Widget">&nbsp;&nbsp;</a>
HTML;
	}


	protected function titleMarkup( $args, $instance ) {
		if ( isset( $instance['title'] ) && $instance['title'] ) {
			return $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		} else {
			return '';
		}
	}


	protected function titleField( $instance ) {
		return $this->labledTextInput( 'title', 'Title (optional):', $instance );
	}


	public function labledTextInput( $id, $label, $instance, $class = null, $onChange = '' ) {
		$formId   = $this->get_field_id( $id );
		$formName = $this->get_field_name( $id );

		if ( $onChange ) {
			$onChange = 'onchange="javascript:' . $onChange . '"';
		}

		return NrHtml::p(
			NrHtml::label( $label, $formId ) .
			'<input id="' . $formId . '" name="' . $formName . '" value="' . $instance[$id] . '" type="text" class="widefat" ' . $onChange . '/>',
			'class=' . $class
		);
	}


	public function checkbox( $id, $label, $value, $checked ) {
		?>
		<input type="checkbox" name="<?php echo $this->get_field_name( $id ); ?>" value="<?php echo $value; ?>" id="<?php echo $this->get_field_id( $id ); ?>" <?php checked( $checked ); ?> />&nbsp;<label for="<?php echo $this->get_field_name( $id ); ?>"><?php echo $label; ?></label>
		<?php
	}


	public static function adminEditLink( $params ) {
		if ( current_user_can( 'edit_theme_options' ) && isset( $params[0] ) ) {
			$link = '<a href="' . admin_url( 'widgets.php?pp_edit_widget=' . $params[0]['widget_id'] ) . '" class="pp-edit-widget-link" title="Only logged-in admins see this link">edit widget</a>';
			$params[0]['after_widget'] = $link . $params[0]['after_widget'];
		}
		return $params;
	}

}

