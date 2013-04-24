<?php

class ppCron {


	public static function maybeDo( $call ) {
		$hookname = self::hookname( $call );
		if ( !NrUtil::isIn( '::', $call ) ) {
			$call = "ppCron::$call";
		}
		add_action( $hookname, $call );
	}


	public static function schedule( $call, $overrideDelay = null, $recurrance = 'daily' ) {
		$hookname = self::hookname( $call );
		wp_clear_scheduled_hook( $hookname );
		if ( is_string( $overrideDelay ) ) {
			$overrideDelay = intval( strtotime( $overrideDelay ) - time() );
		}
		$delay = ( $overrideDelay ) ? $overrideDelay : 60*60*24;
		wp_schedule_event( time() + $delay, $recurrance, $hookname );
	}


	public static function scheduled( $call ) {
		return wp_next_scheduled( self::hookname( $call ) );
	}


	public static function unSchedule( $call ) {
		$hookname = self::hookname( $call );
		wp_clear_scheduled_hook( $hookname );
	}


	public static function backupRemind() {
		if ( ppOpt::test( 'backup_reminder', 'off' ) || date( 'd', time() ) != 1 ) {
			return;
		}
		$subject = 'WordPress blog backup reminder: ' . pp::site()->url;
		return ppString::email( 'backup_remind_email', $subject, ppOpt::id( 'backup_email' ) );
	}


	public static function wpHackWarn() {
		if ( !$checkOnDate = ppOpt::id( 'wp_version_check_date', 'int' ) ) {
			ppOpt::update( 'wp_version_check_date', $checkOnDate = rand( 1, 28 ) );
		}

		if ( date( 'd', time() ) != $checkOnDate ) {
			return;
		}

		$response = wp_remote_get( PROPHOTO_SITE_URL . '?requestHandler=Util::minSafeWpVer' );
		$warnVersion = wp_remote_retrieve_body( $response );

		if ( strlen( $warnVersion ) != 3 || !ctype_digit( $warnVersion ) || ppUtil::wpVersion() > $warnVersion ) {
			return;
		}

		$subject = "Important warning about your WordPress blog at " . pp::site()->url;
		return ppString::email( 'old_wp_version_hack_warning', $subject, ppOpt::id( 'backup_email' ) );
	}


	protected static function hookname( $call ) {
		if ( !is_string( $call ) ) {
			new ppIssue( '$call must be string for ppCron::maybeDo,schedule,unSchedule' );
			$call = @strval( $call );
		}
		$func = NrUtil::isIn( '::', $call ) ? end( explode( '::', $call ) ) : $call;
		return strtolower( "pp_cron_{$func}_action" );
	}


	public static function _onClassLoad() {
		add_filter( 'cron_schedules', create_function( '$schedules', '
			$schedules["monthly"] = array(
				"interval" => 2635200,
				"display" => __("Once a month")
			);
			return $schedules;
		' ) );
	}
}

