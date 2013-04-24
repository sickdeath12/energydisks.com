<?php

class ppUpgrader {


	public function setupUpgrade() {
		ppUtil::changeWPText( 'Downloading update from <span class="code">%s</span>&#8230;', 'Downloading latest version of ProPhoto&#8230;' );
		ppUtil::changeWPText( 'Removing the old version of the theme&#8230;', 'Removing the old version&#8230;' );
		ppUtil::changeWPText( 'Theme updated successfully.', '<b>ProPhoto updated successfully!</b>' );
		add_action( 'load-update.php', array( &$this, 'modifyUpdateTransient' ), 10000 );
	}


	public function modifyUpdateTransient() {
		$url = 'http://www.prophotoblogs.com/?' . http_build_query( array(
			'requestHandler' => 'AutoUpgrade::process',
			'payer_email'    => ppOpt::id( 'payer_email' ),
			'txn_id'         => ppOpt::id( 'txn_id' ),
			'svn'            => pp::site()->svn,
			'url'            => pp::site()->url,
			'uid'            => ppUid::get(),
		) );

		$siteTransient = (object) get_site_transient( 'update_themes' );

		if ( !isset( $siteTransient->last_checked ) ) {
			$siteTransient->last_checked = time() - 1;
		}

		if ( !isset( $siteTransient->response ) ) {
			$siteTransient->response = array( get_option( 'template' ) => array( 'package' => $url ) );
		} else {
			$siteTransient->response[ get_option( 'template' ) ] = array( 'package' => $url );
		}

		set_site_transient( 'update_themes', $siteTransient );
	}


	public static function checkRecommendedSvn() {
		$storedSvn = get_transient( 'pp_recommended_svn' );
		$retrievedSvn = self::retrieveRecommendedSvn();
		set_transient( 'pp_recommended_svn', $retrievedSvn ? $retrievedSvn : $storedSvn, 60*60 * 48 );
	}


	protected static function retrieveRecommendedSvn() {
		$file     = ( ppUtil::isAutoUpgradeCapable() && ppOpt::test( 'auto_auto_upgrade', 'true' ) ) ? 'p4svn.html' : 'p4_bad_host_svn.html';
		$request  = wp_remote_get( PROPHOTO_SITE_URL . $file );
		$response = trim( wp_remote_retrieve_body( $request ) );
		if ( is_numeric( $response ) && intval( $response ) > 200 ) {
			return intval( $response );
		} else {
			return false;
		}
	}


	protected static function recommendedSvn() {
		$svn = intval( get_transient( 'pp_recommended_svn' ) );

		// workaround for IIS 6 servers that don't fire cron jobs correctly
		if ( !$svn && ( ( ppCron::scheduled( 'ppUpgrader::checkRecommendedSvn' ) - time() ) < 0 ) ) {
			if ( !get_transient( 'pp_delay_iis_check_recommended_svn' ) ) {
				$retrievedSvn = self::retrieveRecommendedSvn();
				set_transient( 'pp_delay_iis_check_recommended_svn', 'true', 60*60 * 48 );
				if ( $retrievedSvn ) {
					$svn = $retrievedSvn;
					set_transient( 'pp_recommended_svn', $svn, 60*60 * 48 );
				}
			}
		}

		return $svn;
	}


	public static function showUpgradeNotice() {
		if ( NrUtil::GET( 'activated', 'true' ) || in_array( $GLOBALS['pagenow'], array( 'update.php', 'update-core.php', 'plugins.php' ) ) ) {
			return;
		}

		if ( ppOpt::test( 'not_registered', 'true' ) || ppOpt::id( 'txn_id' ) === '' || ppOpt::id( 'payer_email' ) === '' ) {
			return;
		}

		$recoSvn = self::recommendedSvn();
		if ( self::timeForAutoUpdate( $recoSvn ) ) {
			if ( ppUtil::isAutoUpgradeCapable() && ppOpt::test( 'auto_auto_upgrade', 'true' ) ) {
				self::autoAutoUpgrade();
			} else {
				ppAdmin::advise( 'upgrade_available', ppOpt::id( 'payer_email' ), ppOpt::id( 'txn_id' ), $recoSvn );
			}
		}
	}


	protected static function timeForAutoUpdate( $recoSvn ) {
		if ( !current_user_can( 'edit_themes' ) ) {
			return false;
		}

		if ( get_transient( 'pp_force_immediate_auto_update' ) ) {
			if ( ppUtil::prophotoSiteReachable() && ppUtil::isAutoUpgradeCapable() && ppOpt::test( 'auto_auto_upgrade', 'true' ) ) {
				delete_transient( 'pp_force_immediate_auto_update' );
				return true;
			}
		}

		if ( pp::site()->svn >= $recoSvn ) {
			return false;

		} else if ( get_transient( 'pp_delay_next_auto_upgrade_attempt' ) ) {
			return false;

		} else if ( !ppUtil::prophotoSiteReachable() ) {
			return false;

		} else {
			return true;
		}
	}


	public static function updateUrl() {
		$url = admin_url( 'update.php?auto_upgrade_prophoto=1&action=upgrade-theme&theme=' . get_option( 'template' ) );
		return wp_nonce_url( $url, 'upgrade-theme_' . get_option( 'template' ) );
	}


	protected static function autoAutoUpgrade() {
		ppAdmin::cssToHead( '.wrap div.pp-admin-msg { display:none; }' );
		ppAdmin::notify( 'attempting_auto_upgrade' );
		ppAdmin::notify( 'auto_upgrade_success');
		ppAdmin::warn( 'auto_upgrade_timeout' );
		ppAdmin::warn( 'auto_upgrade_bad_response' );

		$url = str_replace( '&amp;', '&', self::updateUrl() );

		$js = <<<JAVASCRIPT
		jQuery(document).ready(function($){
			$('body').addClass('attempting-pp-auto-upgrade');
			$('a').click(function(event){
				if ( $('body').hasClass('attempting-pp-auto-upgrade') ) {
					event.stopPropagation();
					event.preventDefault();
					return false;
				}
			});
			$('form').submit(function(event){
				if ( $('body').hasClass('attempting-pp-auto-upgrade') ) {
					event.stopPropagation();
					event.preventDefault();
					return false;
				}
			});
			$.ajax({
				type: 'GET',
				url: '$url',
				timeout: 120000,
				success: function(response){
					$.get( ajaxurl + '?action=pp&set_auto_upgrade_timeout_transient=4' );
					if ( response.indexOf( 'ProPhoto updated successfully' ) !== -1 ) {
						$.get(ajaxurl+'?action=pp&current_svn=1',function(svn){
							$('body').removeClass('attempting-pp-auto-upgrade');
							$('#current-svn,.auto_upgrade_success b span').text(svn);
							$('.auto_upgrade_success').show();
							$('.wp-pointer').hide();
						});

					} else {
						$('body').removeClass('attempting-pp-auto-upgrade');
						$('.auto_upgrade_bad_response').show();
					}
				},
				error: function(response){
					$('body').removeClass('attempting-pp-auto-upgrade');
					$('.auto_upgrade_timeout').show();
					$.get( ajaxurl + '?action=pp&set_auto_upgrade_timeout_transient=12' );
				}
			});
		});
JAVASCRIPT;

	ppAdmin::jsToHead( $js );

	}
}


