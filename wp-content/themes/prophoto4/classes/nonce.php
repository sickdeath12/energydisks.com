<?php

class ppNonce {

	public static function field( $id ) {
		return wp_nonce_field( $id, 'pp_nonce_' . $id, true, false );
	}

	public static function get( $id ) {
		return wp_create_nonce( $id );
	}

	public static function check( $id ) {
		return check_admin_referer( $id, 'pp_nonce_' . $id );
	}

}

