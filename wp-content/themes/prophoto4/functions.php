<?php


/* test for PHP 5+ and WordPress 3.2+ */
if ( function_exists( 'spl_autoload_register' ) && function_exists( '_xmlrpc_wp_die_handler' ) && function_exists( 'imagecreatetruecolor' ) ) {
	
	@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

	if ( file_exists( TEMPLATEPATH . '/load.php' ) ) {
		
		require_once( TEMPLATEPATH . '/load.php' );

		// don't init during automated unit tests
		if ( defined( 'PP_UNIT_TESTING' ) && PP_UNIT_TESTING ) {

		// initialiazation routine: runs when WP and PP are completely loaded	
		} else {
			ppReady::init();
		}
	
	/* attempt to recover from failed auto-upgrade */
	} else {
	
		if ( current_user_can( 'level_1' ) ) {
			
			if ( file_exists( TEMPLATEPATH . '/classes/upgrader.php' ) ) {
				require_once( TEMPLATEPATH . '/classes/upgrader.php' );
				
				if ( !isset( $_GET['auto_upgrade_prophoto'] ) ) {
					$failMsg = '<b>ProPhoto has experienced an error</b> upgrading itself.  Please <a href="' . ppUpgrader::updateUrl() . '">
								click here</a> to retry the upgrade. If the problem does not resolve, contact us immediately at 
								<a href="http://www.prophotoblogs.com/support/contact/">this address</a> for help.';
					wp_die( $failMsg );
				
				} else {
					add_action( 'load-update.php', 'pp_auto_upgrade_recover_set_transient', 10000 );
				}
			
			} else {
				wp_die( '<b>ProPhoto has experienced an error</b> upgrading itself.  Please contact us immediately at 
						 <a href="http://www.prophotoblogs.com/support/contact/">this address</a> for help.' );
			}
			
		} else {
			wp_die( 'Site temporarily down for maintenance and upgrade.' );
		}
	}
	
	
} else {
	
	$activate_a_different_theme = '<a href="' . admin_url( 'themes.php' ) . '">activate a different theme</a>';
	
	if ( !function_exists( 'spl_autoload_register' ) || !function_exists( '_xmlrpc_wp_die_handler' ) ) {
		$failMsg = "<b>ProPhoto 4 requires</b> that you are running WordPress <b>version 3.2 or higher</b>. 
		            Please $activate_a_different_theme, upgrade your WordPress installation, and retry.";
		
	} else {
		$failMsg = "<b>ProPhoto 4 requires</b> that PHP be compiled with the <em>GD Library module</em> installed. 
		            Please $activate_a_different_theme, and contact your web-host to have them enable this required library.";
	}
	
	
	if ( is_admin() || $GLOBALS['pagenow'] == 'wp-login.php' ) {
		add_action( 'admin_notices', create_function( '', "echo '<div class=\"error\" style=\"padding:5px 12px;\">" . $failMsg . "</div>';" ) );
		
	} else {
		wp_die( $failMsg, '', array( 'response' => 200 ) );
	}
}


function pp_auto_upgrade_recover_set_transient() {
	set_site_transient( 'update_themes', (object) array(
		'last_checked' => time() - 1,
		'response' => array(
			'prophoto4' => array( 
				'package' => 'http://www.prophotoblogs.com/?requestHandler=AutoUpgrade::process&txn_id=auto_upgrade_fail_recover'
			)
	) ) );
}
