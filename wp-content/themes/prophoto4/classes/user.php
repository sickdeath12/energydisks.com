<?php

class ppUser {


	protected static $wpObj;


	public static function loggedIn() {
		return ( isset( self::$wpObj->ID ) && self::$wpObj->ID );
	}


	public static function name() {
		return ( isset( self::$wpObj->display_name ) ) ? self::$wpObj->display_name : '';
	}


	public static function _onClassLoad() {
		self::$wpObj = wp_get_current_user();
	}

}
