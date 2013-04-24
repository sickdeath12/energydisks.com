<?php

class ppStaticFile {


	public static function url( $filename ) {
		$filename = strval( $filename );

		if ( !@file_exists( TEMPLATEPATH . '/dynamic/' . $filename . '.php' ) ) {
			new ppIssue( "Unknown or missing \$fileName $filename passed to ppStaticFile::url()" );
			return '';
		}

		if ( self::forceDynamic( $filename ) ) {
			return self::dynamicUrl( $filename );

		} else if ( pp::fileInfo()->wpUploadUrl == '' ) {
			new ppIssue( "Unable to set static URL for file $filename, using dynamic instead" );
			return self::dynamicUrl( $filename );

		} else if ( @file_exists( self::staticPath( $filename ) ) ) {
			return self::staticUrl( $filename );

		} else {
			new ppIssue( "Unable to find static file $filename, using dynamic instead" );
			return self::dynamicUrl( $filename );
		}
	}


	public static function html( $filename ) {
		$previewingDesign = isset( $_GET['preview_design'] );
		if ( NrUtil::isIn( '.css', $filename ) ) {
			if ( $previewingDesign ) {
				$html = NrHtml::style( NrUtil::minifyCss( self::fileContent( $filename ) ) );
			} else {
				$html = NrHtml::stylesheet( self::url( $filename ) );
			}
		} else if ( NrUtil::isIn( '.js', $filename ) ) {
			if ( $previewingDesign ) {
				$html = NrHtml::script( self::fileContent( $filename ) );
			} else {
				$html = NrHtml::scriptSrc( self::url( $filename ) );
			}
		}
		return apply_filters( 'pp_static_file_html', $html, $filename );
	}


	public static function output( $filename ) {
		$fileContent = self::fileContent( $filename );
		$extension   = NrUtil::fileExt( $filename );

		if ( $extension == 'css' ) {
			$fileContent = NrUtil::minifyCss( $fileContent );
			header( 'Content-type: text/css' );

		} else if ( $extension == 'js' ) {
			header( 'Content-type: text/javascript' );

		} else {
			new ppIssue( "Extension of \$filename $filename must be .css or .js for ppStaticFile::output()" );
		}

		echo $fileContent;
	}


	public static function generateAll() {
		self::deleteOld();
		$dynamicFiles = glob( TEMPLATEPATH . '/dynamic/*.php' );
		foreach ( $dynamicFiles as $dynamicFile ) {
			self::generateFile( $dynamicFile );
		}
	}


	public static function generateFile( $dynamicFile ) {
		if ( !pp::fileInfo()->okToWrite ) {
			return;
		}

		$filename = str_replace( '.php', '', basename( $dynamicFile ) );
		$fileContent = self::fileContent( $filename );

		if ( $filename == 'script.js' ) {
			self::write( 'script.dev.js', $fileContent );
		}

		if ( $filename == 'style.css' ) {
			$devFileContent = $fileContent;
			$devFilename = 'style.dev.css';
			self::write( $devFilename, $devFileContent );
			$fileContent = NrUtil::minifyCss( $fileContent );
		}

		self::write( $filename, $fileContent );
	}


	public static function minifyJsInit() {
		if ( !current_user_can( 'level_1' ) && !pp::browser()->isTech ) {
			return;
		}
		?>
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function($){
				$.get(prophoto_info.url+'/?minify_js=1');
			});
		</script><?php
	}


	public static function minifyJs() {
		$path_to_js_file = ppUtil::pathFromUrl( self::staticUrl( 'script.js' ) );

		if ( !pp::site()->isDev ) {
			$code = 'code_url';
			$script = ppStaticFile::url( 'script.js' ) + '&ver=' . rand( 10000, 99999 );
		} else {
			$code = 'js_code';
			$script = file_get_contents( $path_to_js_file );
		}

		$post_fields = array(
			'output_info'       => 'compiled_code',
			'output_format'     => 'text',
			'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
			$code => $script,
		);

		$request = wp_remote_post( 'http://closure-compiler.appspot.com/compile', array( 'body' => $post_fields ) );
		$mini_js = wp_remote_retrieve_body( $request );

		if ( substr( $mini_js, 0, 6 ) != 'Error(' ) {
			if ( trim( $mini_js ) == '' ) {
				echo 'No minified JS returned - likely a JS syntax/parse error';
			} else if ( ppUtil::writeFile( $path_to_js_file, $mini_js ) ) {
				echo 'JS successfully minified';
			} else {
				echo 'Error writing minified JS to file';
			}
		} else {
			echo "Error minifying JS: $mini_js";
		}
	}


	public static function parseFileContent( $in, $filename = '' ) {
		$matchArgs = "\((?:(?: )?'([^']+)'(?:,(?: )?'([^']*)')?(?: )?)?\)";
		$parsed = preg_replace_callback(
			"/(?:\")?(pp[^ \(]+)" . $matchArgs . "(?:;)?(?:\")?(?:(->)([^ \(]+)" . $matchArgs . ")?/",
			'ppStaticFile::parseFuncs',
			$in
		);

		$parsed = str_replace( '[~theme_img_folder]', pp::site()->themeUrl . '/images', $parsed );

		$parsed = preg_replace_callback( "/\[~([^,\]]+)(?:,([^\]]+))?\]/", 'ppStaticFile::parseShortcode', $parsed );

		if ( NrUtil::isIn( '.css', $filename ) ) {
			$parsed = preg_replace_callback( "/(opacity|border-radius)\:([^;]*);/", 'ppStaticFile::parseVendorCss', $parsed );
		}

		return $parsed;
	}


	protected static function fileContent( $filename ) {
		if ( !@file_exists( $filepath = TEMPLATEPATH . '/dynamic/' . $filename . '.php' ) ) {
			new ppIssue( "Unknown or missing \$fileName $filename passed to ppStaticFile::fileContent()" );
			return '';
		}

		$fileContent  = require( $filepath );
		$fileContent  = apply_filters( 'pp_static_file_content_pre_parse', $fileContent, $filename );
		$fileContent  = self::parseFileContent( $fileContent, $filename );
		$fileContent  = apply_filters( 'pp_static_file_content', $fileContent, $filename );
		return $fileContent;
	}


	protected static function deleteOld() {
		$existingFiles = glob( pp::fileInfo()->staticFolderPath . '/*' );
		sort( $existingFiles );
		$toDeleteTime = reset( explode( '_', basename( reset( $existingFiles ) ) ) );
		$toKeepTime = reset( explode( '_', basename( end( $existingFiles ) ) ) );
		foreach ( $existingFiles as $existingFile ) {
			if ( $toDeleteTime != $toKeepTime && $toKeepTime != ppOpt::id( 'updated_time' ) && NrUtil::isIn( $toDeleteTime, $existingFile ) ) {
				@chmod( $existingFile, 0777 );
				@unlink( $existingFile );
			}
		}
	}


	protected static function parseShortcode( $match ) {
		$optId = $match[1];
		if ( NrUtil::endsWith( $optId, '_color' ) ) {
			if ( isset( $match[2] ) ) {
				if ( $match[2] == 'bgcolordec' ) {
					return ppCss::bgColorDec( $optId );
				} else if ( $match[2] == 'colordec' ) {
					return ppCss::colorDec( $optId );
				} else {
					new ppIssue( "Unknown second shortcode arg '{$match[2]}'" );
					return '';
				}
			} else {
				return ppOpt::color( $optId );
			}
		} else {
			if ( isset( $match[2] ) ) {
				if ( $match[2] == 'theme_img' ) {
					return ppUtil::themeImg( $optId );
				} else {
					return ppOpt::id( $optId, $match[2] );
				}
			} else {
				return ppOpt::id( $optId );
			}
		}
	}


	protected static function parseFuncs( $match ) {
		$complete = array_shift( $match );
		$func = array_shift( $match );
		$args = $match;

		if ( !is_callable( $func ) ) {
			return $complete;

		} else if ( isset( $args[2] ) && $args[2] == '->' ) {
			$objArgs = array( $args[0], $args[1] );
			if ( empty( $objArgs[0] ) ) {
				unset( $objArgs[0] );
			}
			if ( empty( $objArgs[1] ) ) {
				unset( $objArgs[1] );
			}
			$obj = call_user_func_array( $func, $objArgs );
			if ( !is_object( $obj ) ) {
				return '';
			}
			unset( $args[0], $args[1], $args[2] );
			$method = array_shift( $args );
			return call_user_func_array( array( $obj, $method ), $args );

		} else {
			return call_user_func_array( $func, $args );
		}
	}


	protected static function parseVendorCss( $match ) {
		array_shift( $match );
		$property = array_shift( $match );
		switch ( $property ) {
			case 'opacity':
				$opacity = array_shift( $match );
				$msOpacity = $opacity * 100;
				return "
					-ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(Opacity=$msOpacity)\";
					filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=$msOpacity);
					opacity:$opacity;
					zoom:1;";
				break;
			case 'border-radius':
				$val = array_shift( $match );
				return "
					-moz-border-radius:$val;
					-webkit-border-radius:$val;
					-khtml-border-radius:$val;
					-o-border-radius:$val;
					border-radius:$val;";
				break;
		}
	}


	protected static function dynamicUrl( $filename ) {
		return apply_filters( 'pp_dynamic_file_url', pp::site()->wpurl . '/?staticfile=' . $filename, $filename );
	}


	protected static function staticUrl( $filename ) {
		return apply_filters( 'pp_static_file_url', pp::fileInfo()->staticFolderUrl . '/' . self::staticFilename( $filename ) );
	}


	protected function staticPath( $filename ) {
		return apply_filters( 'pp_static_filepath', pp::fileInfo()->staticFolderPath . '/' . self::staticFilename( $filename ) );
	}


	protected function staticFilename( $filename ) {
		return apply_filters( 'pp_static_filename', ppOpt::id( 'updated_time' ) . '_' . $filename );
	}


	protected static function write( $filename, $fileContent) {
		ppUtil::writeFile( self::staticPath( $filename ), $fileContent );
		@chmod( self::staticPath( $filename ), 0755 );
	}


	protected static function forceDynamic( $filename ) {
		if ( $filename == 'fontpreview.css' ) {
			$return = true;
		} else if ( isset( $_GET['force_dynamic'] ) ) {
			$return = true;
		} else {
			$return = (
				pp::site()->isDev
				&& !class_exists( 'PpUnitTestCase' )
				&& !isset( $_GET['pp_support'] )
				&& !isset( $_GET['force_static'] )
				&& $filename !== 'fontpreview.css'
			);
		}
		return apply_filters( 'pp_force_dynamic_static_file', $return );
	}
}

