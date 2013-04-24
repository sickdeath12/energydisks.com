<?php

class ppActiveDesign {


	private static $activeDesign;


	public static function id() {
		return self::$activeDesign->id();
	}


	public static function name() {
		return self::$activeDesign->name();
	}


	public static function desc() {
		return self::$activeDesign->desc();
	}


	public static function options() {
		return self::$activeDesign->options();
	}


	public static function imgs() {
		return self::$activeDesign->imgs();
	}


	public static function toArray() {
		return self::$activeDesign->toArray();
	}


	public function _onClassLoad() {
		$activeDesign = ppStorage::activeDesign();
		if ( $activeDesign === false ) {
			self::$activeDesign = new ppDesign( 'temp' );
		} else {
			self::$activeDesign = $activeDesign;
		}
	}
}
