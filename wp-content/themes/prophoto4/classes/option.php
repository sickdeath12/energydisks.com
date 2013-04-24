<?php

class ppOpt {

	public static $initialized         = false;
	public static $hasUpdates          = false;
	protected static $nonDesignOptions = null;
	protected static $nonDesignOptIds  = null;
	protected static $designOptions    = null;
	protected static $optionDefaults   = null;


	public static function id( $optionID, $format = null, $stripslashes = true ) {
		$optionID = self::validateOptionId( $optionID );
		if ( !$optionID ) {
			return '';
		}

		// check non-design options first
		if ( self::isNonDesign( $optionID ) && isset( self::$nonDesignOptions[$optionID] ) ) {
			$val = self::$nonDesignOptions[$optionID];

		// then stored design options
		} else if ( isset( self::$designOptions[$optionID]) ) {
			$val = self::$designOptions[$optionID];

		// finally, use default
		} else if ( isset( self::$optionDefaults[$optionID] ) ) {
			$val = self::$optionDefaults[$optionID];

		// un-defined options default to empty string
		} else {
			$val = '';
		}

		$val = $stripslashes ? stripslashes( $val ) : $val;

		if ( $format ) {
			$val = ppUtil::formatVal( $val, $format );
		}

		return $val;
	}


	public static function menuData( $menuID ) {
		return self::id( $menuID, null, false );
	}


	public static function color( $optionID ) {
		if ( ppOpt::exists( "{$optionID}_bind" ) ) {
			return ppOpt::test( "{$optionID}_bind", 'on' ) ? self::id( $optionID ) : '';
		} else {
			return self::id( $optionID );
		}
	}


	public static function defaultVal( $optionID ) {
		$optionID = self::validateOptionId( $optionID );
		if ( !$optionID ) {
			return '';
		}
		return isset( self::$optionDefaults[$optionID] ) ? self::$optionDefaults[$optionID] : '';
	}


	public static function getDefaults() {
		self::init();
		return self::$optionDefaults;
	}


	public static function getOptions() {
		self::init();
		return self::$designOptions;
	}


	public static function getNonDesignOptions() {
		self::init();
		return self::$nonDesignOptions;
	}


	public static function update( $optionID, $newVal ) {
		$optionID = self::validateOptionId( $optionID );
		if ( !$optionID ) {
			return false;
		}
		if ( is_array( $newVal ) || is_object( $newVal ) || is_resource( $newVal ) ) {
			new ppIssue( "Invalid \$newVal " . NrUtil::getVarDump( $newVal ) . " passed to ppOpt::update()" );
			return false;
		}

		if ( self::isNonDesign( $optionID ) ) {
			$oldVal = isset( self::$nonDesignOptions[$optionID] ) ? self::$nonDesignOptions[$optionID] : null;
			self::$nonDesignOptions[$optionID] = $newVal;
		} else {
			$oldVal = isset( self::$designOptions[$optionID] ) ? self::$designOptions[$optionID] : null;
			self::$designOptions[$optionID] = $newVal;
		}
		if ( $newVal !== $oldVal ) {
			self::$hasUpdates = true;
		}

		$facebookLintTriggers = array(
			'facebook_blog_posts_page_title',
			'facebook_blog_posts_page_desc',
			'facebook_static_front_page_title',
			'facebook_static_front_page_desc'
		);
		if ( in_array( $optionID, $facebookLintTriggers ) && $newVal !== $oldVal ) {
			ppFacebook::refreshNonArticleOGCache_onOptUpdate( $optionID );
		}
	}


	public static function delete( $optionID ) {
		$optionID = self::validateOptionId( $optionID );
		if ( !$optionID || !self::exists( $optionID ) ) {
			return false;
		}
		if ( self::isNonDesign( $optionID ) ) {
			unset( self::$nonDesignOptions[$optionID] );
		} else {
			unset( self::$designOptions[$optionID] );
		}
		self::$hasUpdates = true;
	}


	public static function updateMultiple( $array ) {
		if ( !is_array( $array ) ) {
			new ppIssue( 'ppOpt::updateMultiple() requires array as input' );
			return false;
		}
		foreach ( $array as $key => $val ) {
			self::update( $key, $val );
		}
	}


	public static function isNonDesign( $optionID ) {
		$optionID = self::validateOptionId( $optionID );
		if ( !$optionID ) {
			return false;
		}
		return ( in_array( $optionID, self::$nonDesignOptIds ) || preg_match( '/^grid_(widget|article)_/', $optionID ) );
	}


	public static function hasUpdates() {
		self::init();
		return self::$hasUpdates;
	}


	public static function test( $optionID, $value = null ) {
		if ( $value !== null ) {
			if ( NrUtil::isIn( ' || ', $value ) ) {
				$possibles = explode( ' || ', $value );
				foreach ( $possibles as $possible ) {
					if ( self::test( $optionID, $possible ) ) {
						return true;
					}
				}
				return false;
			} else {
				return ( self::id( $optionID ) == $value );
			}
		} else {
			return ( self::id( $optionID ) !== '' );
		}
	}


	public static function orVal( $optionID, $val, $suffix = '' ) {
		$option = ppOpt::id( $optionID );
		if ( $option != '' ) {
			return $option . $suffix;
		} else {
			return $val;
		}
	}


	public static function cascade() {
		if ( func_num_args() < 2 ) {
			new ppIssue( 'Incorrect num args passed to ppOpt::cascade' );
			return '';
		}
		$ids = func_get_args();
		foreach ( $ids as $id ) {
			if ( self::exists( "{$id}_bind" ) ) {
				if ( $color = self::color( $id ) ) {
					return $color;
				}
			} else if ( self::test( $id ) ) {
				return self::id( $id );
			}
		}
	}


	public static function exists( $optionID ) {
		$optionID = self::validateOptionId( $optionID );
		if ( !$optionID ) {
			return false;

		} else if ( isset( self::$nonDesignOptions[$optionID] ) ) {
			return true;

		} else if ( isset( self::$designOptions[$optionID]) ) {
			return true;

		} else if ( isset( self::$optionDefaults[$optionID] ) ) {
			return true;

		} else {
			return false;
		}
	}


	public static function translate( $optionID ) {
		return self::id( 'translate_' . $optionID );
	}


	protected static function validateOptionId( $optionID ) {
		self::init();
		if ( !is_string( $optionID ) ) {
			new ppIssue( "Non-string \$optionID " . NrUtil::getVarDump( $optionID ) . " passed to ppOpt" );
			return '';
		}
		return $optionID;
	}


	protected static function init() {
		if ( !self::$initialized ) {
			self::$nonDesignOptions = ppStorage::globalOptions();
			self::$designOptions    = ppActiveDesign::options();
			self::$nonDesignOptIds  = ppUtil::loadConfig( 'non_design_option_ids' );
			self::$optionDefaults   = ppUtil::loadConfig( 'options' );
			self::$initialized = true;
			self::$hasUpdates  = false;
		}
	}
}

