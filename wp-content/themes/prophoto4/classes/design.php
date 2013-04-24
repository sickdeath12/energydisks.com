<?php

class ppDesign {

	protected $id;
	protected $name;
	protected $desc = '';
	protected $activationWidgets = array();
	protected $options           = array();
	protected $imgs              = array();


	public function __construct( $designId, $config = null ) {
		if ( !is_string( $designId ) || empty( $designId ) ) {
			new ppIssue( '$designId must be non-empty string' );
			$designId = 'design_' . strval( time() );
		}
		$this->id = $designId;

		if ( is_array( $config ) ) {
			if ( isset( $config['meta'] ) ) {
				if ( isset( $config['meta']['name'] ) ) {
					$this->name( $config['meta']['name'] );
				}
				if ( isset( $config['meta']['desc'] ) ) {
					$this->desc( $config['meta']['desc'] );
				}
			}
			if ( isset( $config['options'] ) ) {
				$this->options( $config['options'] );
			}
			if ( isset( $config['imgs'] ) ) {
				$this->imgs( $config['imgs'] );
			}
			if ( isset( $config['activation_widgets'] ) ) {
				$this->activationWidgets = $config['activation_widgets'];
			}
		}
	}


	public function id( $set = null ) {
		if ( is_string( $set ) ) {
			$this->id = $set;
		} else {
			return $this->id;
		}
	}



	public function name( $set = null ) {
		if ( is_string( $set ) ) {
			$this->name = $set;
		} else {
			return $this->name ? $this->name : ucwords( strtolower( str_replace( '_', ' ', $this->id ) ) );
		}
	}


	public function desc( $set = null ) {
		if ( is_string( $set ) ) {
			$this->desc = $set;
		} else {
			return $this->desc;
		}
	}


	public function options( $set = null ) {
		if ( is_array( $set ) ) {
			$this->options = $set;
		} else {
			return $this->options;
		}
	}


	public function imgs( $set = null ) {
		if ( is_array( $set ) ) {
			$this->imgs = $set;
		} else {
			return $this->imgs;
		}
	}


	public function activationWidgets( $set = null ) {
		if ( is_array( $set ) ) {
			$this->activationWidgets = $set;
		} else {
			return $this->activationWidgets;
		}
	}


	public function toArray() {
		return array(
			'meta' => array(
				'name' => $this->name(),
				'desc' => $this->desc(),
				'pp_version' => 'P4',
				'pp_svn' => pp::site()->svn,
			),
			'imgs' => $this->imgs(),
			'options' => $this->options(),
			'activation_widgets' => $this->activationWidgets(),
		);
	}


	public function isSaveable() {
		if ( !NrUtil::isAssoc( $this->imgs ) || !NrUtil::isAssoc( $this->options ) ) {
			return false;
		}
		foreach ( $this->imgs as $imgId => $imgFilename ) {
			if ( !ppImg::isValidFilename( $imgFilename ) ) {
				new ppIssue( "Invalid filename '$imgFilename' in design prevented save" );
				return false;
			}
		}
		foreach ( $this->options as $optionKey => $optionVal ) {
			if ( is_array( $optionVal ) || is_object( $optionVal ) || is_resource( $optionVal ) ) {
				return false;
			}
		}
		return true;
	}
}
