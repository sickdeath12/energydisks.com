<?php

class ppString  {

	public static $strings = null;

	public static function id( $stringID ) {
		if ( self::$strings == null ) {
			self::$strings = ppUtil::loadConfig( 'strings' );
		}
		if ( !is_string( $stringID ) ) {
			new ppIssue( "Non-string \$stringID $stringID passed to ppString::id()" );
			return '';
		} else if ( !isset( self::$strings[$stringID] ) ) {
			new ppIssue( "Unknown \$stringID '$stringID' passed to ppString::id()" );
			return $stringID;
		}

		$string = self::$strings[$stringID];

		$replacements = func_get_args();
		unset( $replacements[0] );
		if ( !empty( $replacements ) ) {
			foreach ( $replacements as $num => $replacement ) {
				$string = str_replace( "%{$num}", $replacement, $string );
			}
		}

		return $string;
	}


	public static function adminError( $stringID, $r1 = null, $r2 = null ) {
		$msg = self::id( $stringID, $r1, $r2 );
		echo "<div class=\"error pp-error pp-admin-msg $stringID\">$msg</div>";
	}


	public static function email( $stringID, $subject, $to = null ) {
		if ( !is_string( $subject ) ) {
			new ppIssue( 'ppString::email() requires string for $subject param' );
			return false;
		}

		if ( $body = self::id( $stringID ) ) {

			if ( $to == null ) {
				$to = get_option( 'admin_email' );

			} else if ( !NrUtil::validEmail( $to ) ) {
				new ppIssue( "Invalid email '$to' passed to ppString::email()" );
				return false;
			}

			$headers  = "Content-type: text/html; charset=UTF-8\r\n";
			$headers .= "From: <$to>\r\n";
			$headers .= "Reply-To: <$to>\r\n";

			return mail( $to, $subject, $body, $headers );
		}
	}

}


