<?php

class ppWelcome {

	public static function showRegistrationForm() {
		add_action( 'admin_notices', ppUtil::func( "ppUtil::renderView( 'welcome_registration_form' );" ) );
	}


	public static function doStep( $step ) {
		switch ( $step ) {
			case 'register_response':
				if ( NrUtil::GET( 'status', 'success' ) ) {
					ppOpt::updateMultiple( array(
						'txn_id'         => $_GET['txn_id'],
						'payer_email'    => urldecode( $_GET['payer_email'] ),
						'not_registered' => 'false',
					) );
					self::introductoryInfo();
				} else {
					self::showRegistrationForm();
				}
				break;
		}
	}


	public static function introductoryInfo() {
		add_action( 'admin_notices', ppUtil::func( "ppUtil::renderView( 'welcome_successful_registration' );" ) );
	}


	public static function _onClassLoad() {
		ppAdmin::loadFile( 'welcome.css' );
		ppAdmin::loadFile( 'welcome.js' );
	}
}

