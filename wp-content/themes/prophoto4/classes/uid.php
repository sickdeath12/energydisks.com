<?php

class ppUid {

	const DB_OPTION_NAME = 'nr_uid';

	public static function exists() {
		return ( get_option( ppUid::DB_OPTION_NAME ) !== false );
	}

	public static function get() {
		return get_option( ppUid::DB_OPTION_NAME );
	}

	public static function set() {
		if ( ppUid::exists() ) {
			new ppIssue( 'Attempt to set Uid when already exists' );
			return false;
		} else {
			return add_option( ppUid::DB_OPTION_NAME, md5( DB_NAME . get_option( 'home' ) . time() ) );
		}
	}
}
