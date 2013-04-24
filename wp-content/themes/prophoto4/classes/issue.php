<?php

class ppIssue {


	protected static $_issues = array();
	protected $message;
	private $issueDump;


	function __construct() {
		$issueData = debug_backtrace();

		$message       = $issueData[0]['args'][0];
		$this->message = $message;

		$level        = 'tech';
		$thrownByFile = $issueData[0]['file'];
		$thrownByLine = $issueData[0]['line'];
		$stackTrace   = array();

		array_shift( $issueData );

		foreach ( $issueData as $stackTraceLevel ) {
			$file = '';
			if ( isset( $stackTraceLevel['file'] ) ) {
				$file .= self::_shortenPath( $stackTraceLevel['file'] );
			}
			if ( isset( $stackTraceLevel['line'] ) ) {
				$file  .= ':' . $stackTraceLevel['line'];
			}
			$func = $stackTraceLevel['function'] . '(';
			if ( isset( $stackTraceLevel['class'] ) && isset( $stackTraceLevel['type'] ) ) {
				$func = $stackTraceLevel['class'] . $stackTraceLevel['type'] .$func;
			}
			$args = array();
			foreach ( $stackTraceLevel['args'] as $arg ) {
				$args[] = "'" . self::_shortenPath( $arg ) . "'";
			}
			$func .= implode( ',', $args ) . ');';
			$stackTrace[] = "$file $func";
			$file = $func = $args = '';
		}

		$issue = compact( 'message', 'level', 'stackTrace' );
		$this->issueDump = $issue;

		// this allows us to test that ppIssue's were thrown
		if ( class_exists( 'PpUnitTestCase' ) || NrUtil::GET( 'unitTesting', 'true' ) ) {
			trigger_error( "ppIssue thrown - $message $thrownByFile $thrownByLine" );
		} else {
			self::$_issues[] = $issue;
		}
	}


	protected static function shouldBeLogged( $issue ) {
		if ( preg_match( '/Img with \$id \'[0-9]+\' not found in db/', $issue['message'] ) ) {
			return false;
		} else {
			return true;
		}
	}


	protected static function logIssues() {
		$issues = self::$_issues;
		krsort( $issues );

		$writeDir = ( pp::fileInfo()->issuesFolderPath ) ? pp::fileInfo()->issuesFolderPath : ABSPATH . 'wp-content/uploads/p4/issues';
		$filepath = $writeDir . '/' . date( 'Y' ) . date( 'm' ) . date( 'W' ) . '_issues.txt';
		$fileContents = strval( @file_get_contents( $filepath ) );

		$addIssueString = '';
		foreach ( $issues as $issue ) {
			if ( self::shouldBeLogged( $issue ) && !NrUtil::isIn( print_r( $issue, true ), $fileContents ) ) {
				$addIssueString .= date( 'r' ) . "  " .  print_r( $issue, true ) . "\n\n";
			}
		}

		if ( $addIssueString ) {
			@NrUtil::writeFile( $filepath, $addIssueString, 'prepend' );
		}
	}


	public function message() {
		return $this->message;
	}


	public function getIssues() {
		return self::$_issues;
	}


	public static function reportAll() {
		if ( $GLOBALS['pagenow'] != 'admin-ajax.php' ) {
			self::logIssues();
		}
		if ( !pp::browser()->isTech || $GLOBALS['pagenow'] == 'admin-ajax.php' ) {
			return;
		}
		$issueTypes = array();
		foreach ( self::$_issues as $issue ) {
			$issueTypes[$issue['level']][] = 'Issue: <code>' . $issue['message'] . '</code> in <code>' . $issue['stackTrace'][0] . '</code>';
		}
		foreach ( $issueTypes as $issueType ) {
			$issue = implode( '<br />', $issueType );
			self::showNotice( $issue, NrUtil::firstArrayKey( $issueTypes ) );
		}
	}


	public function showNotice( $text, $type ) {
		echo "<div class='ppIssue error ppError $type' onclick='javascript:jQuery(this).remove();'>$text</div>";
	}


	public function stacktrace() {
		return $this->issueDump['stackTrace'];
	}


	protected function _shortenPath( $path ) {
		if ( is_object( $path ) ) {
			return get_class( $path );
		}
		$path = str_replace( array( TEMPLATEPATH, ABSPATH, '/Library/WebServer/Documents/simpletest' ), array( '{pp}', '{wp}/', '{st}' ), $path );
		if ( is_string( $path ) &&strlen( $path ) > 200 ) {
			$path = substr( $path, 0, 20 ) . '~TRUNCATED';
		}
		return $path;
	}
}



