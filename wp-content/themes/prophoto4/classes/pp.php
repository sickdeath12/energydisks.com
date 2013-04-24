<?php
/**
 * Registry class for ProPhoto
 * Provides access to what used to be global data
 *
 * @package default
 * @author Jared
 */
class pp {

	protected static $_fileInfo         = null;
	protected static $_site             = null;
	protected static $_wp               = null;
	protected static $_browser          = null;
	protected static $_numbers          = null;
	protected static $_tutorials        = null;


	/* setter method to register data */
	public function register( $what, $registered ) {
		switch ( $what ) {
			case 'constants':
				self::$_site      = $registered->site;
				self::$_wp        = $registered->wp;
				self::$_browser   = $registered->browser;
				self::$_numbers   = $registered->numbers;
				self::$_tutorials = $registered->tutorials;
				break;
			case 'folders':
				self::$_fileInfo = $registered;
				break;
			default:
				new ppIssue( 'unknown param:($what) passed to pp::register()' );
				break;
		}
	}


	/* accessor methods to retrieve registered data */
	public function fileInfo() {
		return self::$_fileInfo;
	}

	public function site() {
		if ( self::$_site == null ) {
			new ppIssue( 'pp::site() accessed before registry populated' );
		}
		return self::$_site;
	}

	public function wp() {
		if ( self::$_wp == null ) {
			new ppIssue( 'pp::wp() accessed before registry populated' );
		}
		return self::$_wp;
	}

	public function browser() {
		if ( self::$_browser == null ) {
			new ppIssue( 'pp::browser() accessed before registry populated' );
		}
		return self::$_browser;
	}

	public function num() {
		if ( self::$_numbers == null ) {
			new ppIssue( 'pp::num() accessed before registry populated' );
		}
		return self::$_numbers;
	}

	public function tut() {
		if ( self::$_tutorials == null ) {
			new ppIssue( 'pp::tut() accessed before registry populated' );
		}
		return self::$_tutorials;
	}
}

