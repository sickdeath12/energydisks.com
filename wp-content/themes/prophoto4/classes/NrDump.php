<?php

class NrDump {

	private static $cssDumped = false;
	private static $methods = array();
	private $input;
	private $expr;


	public static function it( $var, $echo = true ) {
		if ( !self::isNrTech() ) {
			return;
		}
		$dump = new NrDump( $var );
		if ( $echo ) {
			echo $dump->markup();
		}
		return $dump->markup();
	}


	public static function inCss( $var, $echo = true ) {
		if ( !self::isNrTech() ) {
			return;
		}
		$dump = new NrDump( $var );
		if ( $echo ) {
			echo '</style>' . $dump->markup() . '<style type="text/css" media="screen">';
		}
		return '</style>' . $dump->markup() . '<style type="text/css" media="screen">';
	}


	public static function html( $var, $echo = true ) {
		if ( !self::isNrTech() ) {
			return;
		}
		$dump = new NrDump( $var );
		$markup = $dump->markup();
		$markup = str_replace( '&gt;&lt;', "&gt;<br />&lt;", $markup );
		if ( $echo ) {
			echo $markup;
		}
		return $markup;
	}


	public static function css( $var, $echo = true ) {
		if ( !self::isNrTech() ) {
			return;
		}
		$dump = new NrDump( $var );
		$markup = str_replace( array( '{', '}', ';' ), array( "{<br />\t", '}<br />', ";<br />\t" ), $dump->markupWithoutCss() );
		$markup = $dump->maybeGetCss() . str_replace( array( "\t}", ')</span> "' ), array( '}', ')</span>"<br /><br />' ), $markup );
		if ( $echo ) {
			echo $markup;
		}
		return $markup;
	}


	private function __construct( $var ) {
		$this->input = $var;
		$this->locate();
		$this->getExpr();
		$this->getDump();
	}


	private function getExpr() {
		$codeFile = file( $this->file );
		$codeLine = $codeFile[ $this->line - 1 ];
		$match = array();
		preg_match( "/(?:NrDump::(?:it|inCss|html)\( )(.+) \)/i", $codeLine, $match );
		$this->expr = isset( $match[1] ) ? $match[1] : null;
	}


	private function locate() {
		$bt = debug_backtrace();
		$this->file = $bt[2]['file'];
		$this->filename = basename( $this->file );
		$this->line = $bt[2]['line'];
	}


	private function getMethods( $class ) {
		$r = new ReflectionClass( $class );
		$methods = $r->getMethods();

		foreach ( $methods as $method ) {

			if ( !$method->isPublic() ) {
				continue;
			}

			$methodName = $method->name;
			if ( $method->isStatic() ) {
				$methodName = "$class<span class='colons'>::</span>$methodName";
				$methodType = 'static';
			} else if ( $methodName == '__construct' ) {
				$methodName = "<span class='new'>new</span> $class";
				$methodType = 'construct';
			} else {
				$methodName = "<span class='this'>\$this</span><span class='objarr'>-></span>$methodName";
				$methodType = 'normal';
			}

			$methodArray = array( 'name' => $methodName, 'type' => $methodType );
			$params = $method->getParameters();

			if ( !empty( $params ) ) {

				foreach ( $params as $param ) {
					$paramArray = array( 'name' => '$' . $param->name );

					if ( $param->isOptional() ) {
						$paramArray['default'] = ( is_null( $param->getDefaultValue() ) ) ? 'null' : $param->getDefaultValue()	;
					}
					$methodArray['params'][] = $paramArray;
					unset( $paramArray );
				}

			}
			self::$methods[] = $methodArray;
			unset( $methodArray );
		}

		if ( !empty( self::$methods ) ) {
			$this->dump = preg_replace_callback( "/($class) Object\n[ ]*\(/", 'NrDump::insertMethods', $this->dump, 1 );
		}
		self::$methods = array();
	}


	private static function insertMethods( $matches ) {
		$parenPadCount = substr_count( $matches[0], " " ) - 2;
		$parenPad = '';
		for ( $i = 0; $i <= $parenPadCount; $i++ ) {
			$parenPad .= ' ';
		}
		$r = $matches[1] . " Object\n$parenPad(";
		foreach ( self::$methods as $method ) {
			if ( isset( $method['params'] ) ) {
				$args = ' ';
				foreach ( $method['params'] as $param ) {
					if ( $args != ' ' ) {
						$args .= ', ';
					}
					$args .= "<span class='paramName'>{$param['name']}</span>";
					if ( isset( $param['default'] ) ) {
						$val = ( $param['default'] == 'null' ) ? '<span class="paramDefault null">null</span>' : "<span class='paramDefault'>\"{$param['default']}\"</span>";
						$args .= " = $val";
					}
				}
				$args .= ' ';
			} else {
				$args = '';
			}
			if ( $method['type'] == 'static' ) {
				$methodTypeClass = ' static-method';
			} else {
				$methodTypeClass = '';
			}
			$r .= "\n$parenPad    <span class='methodWrap'>[method] => <span class='method{$methodTypeClass}'>{$method['name']}($args)</span></span>";
		}
		return $r;
	}


	private function getAllMethods() {
		$matches = array();
		preg_match_all( "/([^ ]+) Object\n/", $this->dump, $matches );
		$classes = array_unique( $matches[1] );
		foreach ( $classes as $class ) {
			$this->getMethods( $class );
		}
	}


	private function getDump() {
		if ( is_array( $this->input ) || is_object( $this->input ) ) {
			$this->dump = print_r( $this->input, true );
			$this->dump = urldecode( htmlspecialchars( $this->dump ) );
			if ( is_object( $this->input ) ) {
				$this->getAllMethods();
			}
			$this->dump = str_replace( array( '] => ', '] =&gt; ' ), '] <span class="arr">=</span> ', $this->dump );
			$this->dump = preg_replace( '/\[(([^:\]]*):protected)\]/', '<span class="protected">#[$2]</span>', $this->dump );
			$this->dump = preg_replace( '/\[(([^:\]]*):[^:]*:private)\]/', '<span class="private">-[$2]</span>', $this->dump );
			$this->dump = preg_replace( "/Array[ \t\r\n]+\([ \t\r\n]+\)[ \t\r\n]/", "Array()", $this->dump );
			$this->dump = preg_replace( "/([^ ]+) Object\n/", '<span class="objType">$1</span> <span class="prim">Object</span>' . "\n", $this->dump );
			$this->dump = str_replace( "\n\n", "\n", $this->dump );
			$this->dump = str_replace( "=</span> Array", "=</span> <span class='array'>Array</span>", $this->dump );

		} else {
			$this->dump = NrUtil::getVarDump( $this->input );
			$this->dump = urldecode( htmlspecialchars( $this->dump ) );
			$this->dump = preg_replace( "/(string\([0-9]+\)) &quot;([^\n]+)/", '<span class="prim">$1</span> "$2', $this->dump );
			$this->dump = preg_replace( "/int\(([0-9]+)\)\n/", '<span class="prim">int</span> $1', $this->dump );
			$this->dump = preg_replace( "/float\(([.0-9]+)\)\n/", '<span class="prim">float</span> $1', $this->dump );
			$this->dump = preg_replace( "/bool\((true|false)\)\n/", '<span class="prim">bool</span> $1', $this->dump );
			$this->dump = preg_replace( "/resource\([0-9]+\) of type \(([^)]+)\)/", '<span class="prim">resource</span> $1', $this->dump );
		}
		if ( strlen( $this->dump ) > 80  || strlen( $this->expr ) > 40 ) {
			$this->dump = "<hr />" . $this->dump;
		}
	}


	private function markup() {
		$markup  = $this->maybeGetCss();
		$markup .= $this->markupWithoutCss();
		return $markup;
	}


	private function maybeGetCss() {
		if ( !self::$cssDumped ) {
			self::$cssDumped = true;
			return $this->styles();
		} else {
			return '';
		}
	}


	private function markupWithoutCss() {
		$markup = '<pre class="NrDumped" oncontextmenu="javascript:jQuery(this).css(\'overflow-x\',\'scroll\');return false;">';
			$markup .= "<p class='in' onclick='javascript:jQuery(this).parent().remove();'><b>$this->filename</b>:<b>$this->line</b></p>";
			$markup .= "<span class='varName'>$this->expr</span> $this->dump";
		$markup .= '</pre>';
		return $markup;
	}


	private function isNrTech() {
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], '(prophototech)' ) !== false ) {
			return true;
		} else if ( isset( $_SERVER['SERVER_ADMIN'] ) && $_SERVER['SERVER_ADMIN'] == 'netrivet@devmachine.com' ) {
			return true;
		} else if ( isset( $_GET['show_dump'] ) ) {
			return true;
		} else {
			return false;
		}
	}


	private function styles() {
		$css = <<<HTML
		<style type="text/css" media="screen">
			.NrDumped {
				background-color:lightgray;
				position:relative;
				padding:5px;
				margin:5px;
				font-size:12px;
				line-height:1.2em;
				overflow:hidden;
				font-weight:400;
				text-shadow:0 0 0 #fff;
			}
			.NrDumped, .NrDumped p {
				font-family:Courier;
				color:#000;
			}
			.NrDumped .in {
				font-size:10.5px;
				font-weight:400;
				position:absolute;
				top:6px;
				right:4px;
				margin:0;
				opacity:0.8;
			}
			.NrDumped .varName {
				font-weight:700;
				text-decoration:underline;
			}
			.NrDumped .private {
				color:#63010D;
			}
			.NrDumped .protected {
				color:#875401;
			}
			.NrDumped .objType {
				font-style:italic;
				color:#fff;
				background-color:#787878;
				padding:0 3px 1px;
				-moz-border-radius:2px;
			}
			.NrDumped .prim {
				opacity:0.55;
			}
			.NrDumped .methodWrap {
				color:#121212;
				opacity:0.8;
			}
			.NrDumped .method {
				color:blue;
			}
			.NrDumped .paramName {
				color:green;
			}
			.NrDumped .paramDefault {
				color:#EA05F2;
			}
			.NrDumped .null {
				color:red;
			}
			.NrDumped .arr {
				opacity:0.35;
			}
			.NrDumped .objarr,
			.NrDumped .colons  {
				color:#444;
			}
			.NrDumped .new {
				color:#A82A07;
			}
			.NrDumped .static-method {
				color:#7D0887;
			}
			.NrDumped .array {
				color:#C40C22;
			}
			.NrDumped hr {
				border-style: inset;
				border-width: 1px;
			}
		</style>
HTML;
		return NrUtil::minifyCss( $css );
	}
}


