<?php

class ppUtil {


	const NO_ARCHIVE_TYPE = false;
	const RETURN_VIEW = false;
	public static $textChanges = array();


	public static function isAutoUpgradeCapable() {
		if ( $isAutoUpgradeCapable = get_transient( 'pp_is_auto_upgrade_capable' ) ) {
			return ( $isAutoUpgradeCapable == 'true' ) ? true : false;
		} else {
			$response = wp_remote_get( PROPHOTO_SITE_URL . 'p4svn.html', array( 'timeout' => 5 ) );
			if ( is_wp_error( $response ) && in_array( 'name lookup timed out', $response->get_error_messages() ) ) {
				ppAdmin::warn( 'dns_resolution_problem' );
				$isAutoUpgradeCapable = 'false';
			} else if ( ppUtil::server() == 'Zues' ) {
				$isAutoUpgradeCapable = 'false';
			} else if ( NrUtil::isIn( 'APLUS.NET', ppUtil::nameservers() ) ) {
				// possible additional NFS zeus-like dns: MEGANAMESERVERS.COM
				// another possible problem DNS: NSX.CARRIERZONE.COM
				$isAutoUpgradeCapable = 'false';
			} else {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				$isAutoUpgradeCapable = ( get_filesystem_method( array(), ABSPATH ) == 'direct' ) ? 'true' : 'false';
			}
			set_transient( 'pp_is_auto_upgrade_capable', $isAutoUpgradeCapable, 60*60*24 );
			return ( $isAutoUpgradeCapable == 'true' ) ? true : false;
		}
	}


	public static function server() {
		$raw = strtolower( $_SERVER['SERVER_SOFTWARE'] );
		if ( NrUtil::isIn( 'ideawebserver', $raw ) ) {
			return 'IdeaWebServer';
		} else if ( NrUtil::isIn( 'apache', $raw ) ) {
			return 'Apache';
		} else if ( NrUtil::isIn( 'iis', $raw ) ) {
			return 'IIS';
		} else if ( NrUtil::isIn( 'zeus', $raw ) ) {
			return 'Zues';
		} else if ( NrUtil::isIn( 'nginx', $raw ) ) {
			return 'nginx';
		} else if ( NrUtil::isIn( 'lighttpd', $raw ) ) {
			return 'lighttpd';
		} else if ( NrUtil::isIn( 'webserverx', $raw ) ) {
			return 'Masked';
		} else if ( NrUtil::isIn( 'litespeed', $raw ) ) {
			return 'LiteSpeed';
		} else {
			return 'Unknown';
		}
	}


	public static function lookupNameservers( $whoisScrape = null ) {
		if ( !$whoisScrape ) {

			$domain = NrUtil::extractDomain( trailingslashit( pp::site()->url ) );
			if ( function_exists( 'dns_get_record' ) && is_array( $dns = dns_get_record( $domain, DNS_NS ) ) ) {
				sort( $dns );
				$nameservers = array();
				foreach ( $dns as $entry ) {
					if ( isset( $entry['target'] ) && preg_match( '/[A-Za-z0-9]+\.[A-Za-z0-9]+\.[A-Za-z0-9]+/', $entry['target'] ) ) {
						$nameservers[] = $entry['target'];
					}
					if ( count( $nameservers ) == 2 ) {
						$nameservers = strtoupper( implode( ', ', $nameservers ) );
						set_transient( 'nameservers', $nameservers, $sixtyDays = 60*60*24 * 60 );
						return $nameservers;
					}
				}
			}

			$whoisScrape = wp_remote_retrieve_body( wp_remote_get(
				'http://reports.internic.net/cgi/whois?whois_nic=' . $domain . '&type=domain',
				array( 'timeout' => 15 )
			) );
		}

		if ( !empty( $whoisScrape ) ) {
			if ( preg_match_all( "/(Name Server:(?: )?)[^\. ]+\.[^\. ]+\.[^ \r\n]+/i", $whoisScrape, $matches ) ) {
				$ns1 = trim( str_replace( $matches[1][0], '', $matches[0][0] ) );
				$ns2 = trim( str_replace( $matches[1][1], '', $matches[0][1] ) );
				$nameservers = strtoupper( $ns1 . ', ' . $ns2 );
				set_transient( 'nameservers', $nameservers, $sixtyDays = 60*60*24 * 60 );
				return $nameservers;
			}
		}

		return false;
	}


	public static function nameservers() {
		return get_transient( 'nameservers' );
	}


	public function webHost() {
		if ( NrUtil::isIn( '/kunden/', $_SERVER['DOCUMENT_ROOT'] ) ) {
			return '1and1';
		} else if ( !$nameservers = strtoupper( strval( ppUtil::nameservers() ) ) ) {
			return 'Unknown';
		} else {
			$dnsMap = array(
				'BLUEHOST'      => 'Bluehost',
				'DIZINC'        => 'Bludomain',
				'BLUDOMAIN'     => 'Bludomain',
				'DOMAINCONTROL' => 'GoDaddy',
				'MEDIATEMPLE'   => 'MediaTemple',
				'1AND1'         => '1and1',
				'1UND'          => '1and1',
				'DREAMHOST'     => 'Dreamhost',
				'HOSTGATOR'     => 'Hostgator',
				'CMDWEBSITES'   => 'CMD',
				'HOSTMONSTER'   => 'Hostmonster',
				'RACKSPACE'     => 'Rackspace',
				'WEST-DATACENT' => 'Westhost',
				'WESTSERVERS'   => 'Westhost',
				'.ONE.COM'      => 'One.com',
				'HOSTPAPA'      => 'HostPapa',
				'.IPAGE.COM'    => 'iPage',
				'SITE5.COM'     => 'Site5',
				'INMOTIONHOSTI' => 'Inmotion',
				'YAHOO'         => 'Yahoo',
				'.HOME.PL'      => 'Home.pl/IdeaWebserver',
			);
			foreach ( $dnsMap as $dnsFragment => $hostName ) {
				if ( NrUtil::isIn( $dnsFragment, $nameservers ) ) {
					return $hostName;
				}
			}
			return 'Unknown';
		}
	}


	public static function storedArray( $id ) {
		if ( !is_string( $id ) ) {
			new ppIssue( 'Non-string $id passed to ppUtil::storedArray()' );
			return false;
		}

		if ( is_array( $raw = get_option( $id ) ) ) {
			new ppIssue( "Non-json-formatted array retrieved with id: '$id'" );
			self::updateStoredArray( $id, $raw );
			return $raw;
		}

		if ( $raw !== false && !is_string( $raw ) ) {
			new ppIssue( "Non-string non-json-formatted value retrieved with id: '$id'" );
			return array();
		}

		return (array) @json_decode( $raw, true );
	}


	public static function updateStoredArray( $id, $array ) {
		if ( !is_string( $id ) ) {
			new ppIssue( 'Non-string $id passed to ppUtil::updateStoredArray()' );
			return false;
		}
		if ( !is_array( $array ) ) {
			new ppIssue( 'Non-array $array passed to ppUtil::updateStoredArray()' );
			return false;
		}

		return update_option( $id, self::jsonEncodeClean( $array ) );
	}


	public static function jsonEncodeClean( $array ) {
		$arrayCount       = self::recursiveCount( $array );
		$basicEncoded     = json_encode( $array );
		$sanitizedEncoded = $basicEncoded;

		$sanitizedEncoded = preg_replace( '~\\\\+"~', '\"', $sanitizedEncoded );
		$sanitizedEncoded = str_replace(  "\\\\'",    "'",  $sanitizedEncoded );
		$sanitizedEncoded = str_replace(  "\\\\'",    "'",  $sanitizedEncoded );
		$sanitizedEncoded = preg_replace( "~\\\\+/~", "\/", $sanitizedEncoded );

		$sanitizedDecoded = json_decode( $sanitizedEncoded, true );
		if ( is_array( $sanitizedDecoded ) && $arrayCount === self::recursiveCount( $sanitizedDecoded ) ) {
			return $sanitizedEncoded;
		} else {
			return $basicEncoded;
		}
	}


	protected static function recursiveCount( $array ) {
		$arrayCount = count( $array );
		foreach ( $array as $subItem ) {
			if ( is_array( $subItem ) ) {
				foreach ( $subItem as $subSubItem ) {
					$arrayCount++;
				}
			} else {
				$arrayCount++;
			}
		}
		return $arrayCount;
	}


	public static function changeWPText( $textToChange, $changeToThis ) {
		self::$textChanges[ __( $textToChange ) ] = $changeToThis;
		add_filter( 'gettext', create_function( '$txt', 'return isset( ppUtil::$textChanges[$txt] ) ? ppUtil::$textChanges[$txt] : $txt;' ) );
	}


	public static function ob() {
		$args = func_get_args();
		$funcName = array_shift( $args );
		ob_start();
		if ( is_object( $funcName ) ) {
			$obj = $funcName;
			$method = array_shift( $args );
			$obj->$method();
		} else {
			call_user_func_array( $funcName, $args );
		}
		$return = ob_get_clean();
		return $return;
	}


	public static function isStaticFrontPage() {
		return ( pp::site()->hasStaticFrontPage && is_front_page() );
	}


	public static function pageType( $returnArchiveType = true ) {
		if ( ppUtil::isStaticFrontPage() )
			return 'front_page';

		if ( is_home() )
			return 'home';

		if ( is_single() )
			return 'single';

		if ( is_page() )
			return 'page';

		if ( $returnArchiveType ) {

			if ( is_category() )
				return 'category';

			if ( is_tag() )
				return 'tag';

			if ( is_search() )
				return 'search';

			if ( is_author() )
				return 'author';
		}

		/* technically search is not an archive, but we consider it in the archive-type bucket */
		if ( is_archive() || is_search() )
			return 'archive';

		if ( is_admin() )
			return 'admin';

		if ( is_404() )
			return '404';

		return 'unknown_page_type';
	}


	public static function renderView( $file, $vars = null, $render = true ) {
		$path = TEMPLATEPATH . "/views/$file.php";
		if ( !@file_exists( $path ) ) {
			new ppIssue( "Unknown \$file '$file' requested in ppUtil::renderView()" );
			return;
		}
		if ( is_array( $vars ) ) {
			extract( $vars );
		}

		do_action( "pp_pre_render_$file" );

		if ( $render ) {
			include( $path );
			do_action( "pp_post_render_$file" );

		} else {
			ob_start();
			include( $path );
			return ob_get_clean();
		}
	}


	public static function redirect( $location ) {
		if ( self::unitTesting() ) {
			return $location;
		}
		wp_redirect( $location );
		exit();
	}


	public static function rootUrl( $url ) {
		// temp urls like http://12.34.56.7/~foo look like sub-dirs but are root
		if ( preg_match( '/~[^\/]+(\/)?$/', $url ) ) {
			return trailingslashit( $url );
		} else if ( NrUtil::validUrl( dirname( $url ) ) ) {
			return self::rootUrl( dirname( $url ) );
		} else {
			return trailingslashit( $url );
		}
	}


	public static function rootPath( $wpabspath, $wpurl ) {
		if ( !$diff = str_replace( self::rootUrl( $wpurl ), '', trailingslashit( $wpurl ) ) ) {
			return $wpabspath;
		} else {
			if ( NrUtil::endsWith( $wpabspath, $diff ) ) {
				return str_replace( $diff, '', $wpabspath );
			} else {
				return $wpabspath;
			}
		}
	}


	public static function pathFromUrl( $url ) {
		$path = str_replace( pp::site()->wpurl, untrailingslashit( ABSPATH ), $url );
		if ( $path === $url ) {
			$path = str_replace( ROOTURL, ROOTPATH, $url );
			if ( $path === $url ) {
				return apply_filters( 'pp_util_pathfromurl_fail', false, $url );
			}
		}
		return apply_filters( 'pp_util_pathfromurl', $path, $url );
	}


	public static function urlFromPath( $path, $ABSPATH = ABSPATH ) {
		$url = str_replace( trailingslashit( str_replace( '\\', '/', $ABSPATH ) ), pp::site()->wpurl . '/', str_replace( '\\', '/', $path ) );
		if ( $url === $path ) {
			$url = str_replace( ROOTPATH, ROOTURL, $path );
			if ( $url === $path ) {
				return apply_filters( 'pp_util_urlfrompath_fail', false, $path );
			}
		}
		return apply_filters( 'pp_util_urlfrompath', $url, $path );
	}


	public static function siteData( $js = false ) {
		$info = (object) array(
			'url'                  => pp::site()->url,
			'theme_url'            => pp::site()->themeUrl,
			'wpurl'                => pp::site()->wpurl,
			'folder_url'           => pp::fileInfo()->folderUrl,
			'static_resource_url'  => pp::site()->extResourceUrl,
			'content_margin'       => ppOpt::id( 'content_margin' ),
			'is_dev'               => pp::site()->isDev,
			'is_tech'              => pp::browser()->isTech,
			'wpversion'            => self::wpVersion(),
			'svn'                  => pp::site()->svn,
			'payer_email'          => ppOpt::id( 'payer_email' ),
			'txn_id'               => ppOpt::id( 'txn_id' ),
			'purch_time'           => ppOpt::id( 'purch_time' ),
			'plugins'              => join( ' ', (array) get_option( 'active_plugins' ) ),
			'auto_upgrade_capable' => ppUtil::isAutoUpgradeCapable(),
			'facebook_language'    => ppOpt::id( 'facebook_language' ),
			'admin_ajax_url'       => admin_url( 'admin-ajax.php' ),
		);
		if ( $js ) {
			return 'var prophoto_info = ' . stripslashes( json_encode( $info ) ) . ';';
		} else {
			return $info;
		}
	}


	public static function wpVersion() {
		$ver = str_pad( intval( str_replace( '.', '', $GLOBALS['wp_version'] ) ), 3, '0' );
		return ( $ver == '000' ) ? 999 : $ver;
	}


	public static function writeFile( $filepath, $fileContent, $attemptNum = 1 ) {
		if ( !is_string( $filepath ) || NrUtil::validUrl( $filepath ) ) {
			new ppIssue( "Invalid \$filepath $filepath passed to ppUtil::writeFile()" );
			return false;
		}

		if ( !$resource = @fopen( $filepath, 'w+' ) ) {
			if ( $attemptNum == 1 ) {
				@chmod( dirname( $filepath ), 0777 );
				@chmod( $filepath, 0777 );
				@unlink( $filepath );
				return self::writeFile( $filepath, $fileContent, 2 );
			} else {
				new ppIssue( "ppUtil::writeFile() could not fopen() \$filepath '$filepath'" );
				return false;
			}
		}

		if ( @fwrite( $resource, $fileContent ) === false ) {
			if ( $attemptNum == 1 ) {
				@chmod( dirname( $filepath ), 0777 );
				@chmod( $filepath, 0777 );
				@unlink( $filepath );
				return self::writeFile( $filepath, $fileContent, 2 );
			} else {
				new ppIssue( "pUtil::writeFile() could not fwrite() \$filepath $filepath" );
				return false;
			}
		}

		@fclose( $resource );
		return true;
	}


	public static function userUrl( $optionId ) {
		$url = ppOpt::id( $optionId );
		if ( $url == '' ) {
			return '';
		}
		$url = ppUtil::prefixUrl( $url );
		if ( NrUtil::validUrl( $url ) ) {
			return $url;
		} else {
			return '';
		}
	}


	public static function prefixUrl( $url ) {
		if ( !is_string( $url ) ) {
			new ppIssue( "Non-string \$url '$url' passed to ppUtil::prefixUrl()" );
			return '';
		}
		if ( NrUtil::startsWith( $url, 'http://' ) || NrUtil::startsWith( $url, 'https://' ) ) {
			return $url;
		}
		return ( $url[0] == '#' ) ? $url : 'http://' . $url;
	}


	public static function formatVal( $value, $format ) {
		if ( is_array( $value ) || is_object( $value ) || is_resource( $value ) || is_null( $value ) ) {
			new ppIssue( "Unexpected \$value " . NrUtil::getVarDump( $value ) . 'passed to ppUtil::formatVal()' );
			return '';
		}
		switch ( strtolower( $format ) ) {
			case 'int':
			case 'integer':
				return intval( $value );
			case 'float':
				return floatval( $value );
			case 'bool':
			case 'boolean':
				if ( function_exists( 'filter_var' ) ) {
					return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				} else {
					/* some rare servers have PHP 5.2+ with no filter_var() */
					return (bool) preg_match( '/^(on|yes|true|1)$/', $value );
				}
			case 'percent':
			case 'percentage':
			case '%':
				return intval( $value ) / 100;
			case 'microseconds':
			case 'microsecs':
				return intval( floatval( $value ) * 1000 );
			case 'string':
				return strval( $value );
			case 'array':
				return (array) json_decode( $value, true );
			case 'obj':
			case 'object':
				return (object) json_decode( $value, false );
			case 'csspx':
			case 'css px':
			case 'csspixels':
			case 'css pixels':
			case 'pixels':
			case 'px':
				$value = intval( $value );
				return ( $value !== 0 ) ? $value . 'px' : '0';
			default:
				new ppIssue( "Unexpected \$format $format passed to ppUtil::formatVal()" );
				return '';
		}
	}


	public static function unitTesting() {
		return (
			defined( 'PP_UNIT_TESTING' ) &&
			PP_UNIT_TESTING == true &&
			md5( $_COOKIE['ppUnitTestingAuth'] ) == 'ba10d27990011326047ec2d03e3eac04' &&
			pp::site()->isDev
		);
	}


	public static function setWpOption( $optionName, $optionVal ) {
		if ( !is_string( $optionName ) ) {
			new ppIssue( 'ppUtil::setWpOption expects string for $optionName' );
			return;
		}
		if ( get_option( $optionName ) != $optionVal ) {
			update_option( $optionName, $optionVal );
		}
	}


	public static function loadConfig( $file ) {
		$configFile  = TEMPLATEPATH . "/config/conf.$file.php";

		if ( @file_exists( $configFile ) ) {
			require( $configFile );
			if ( empty( $configArray ) || !is_array( $configArray ) ) {
				new ppIssue( "No config array found in $configFile in ppUtil::loadConfig()" );
				return array();
			} else {
				return $configArray;
			}
		} else {
			new ppIssue( "Non-existant file $configFile requested by ppUtil::loadConfig()" );
			return array();
		}
	}


	public static function idAndNonce( $formHandle ) {
		if ( !is_string( $formHandle ) ) {
			new ppIssue( 'Non-string $formHandle' );
			return '';
		}
		return NrHtml::hiddenInput( 'pp_POST_identifier', $formHandle ) . ppNonce::field( $formHandle );
	}


	public static function clearSuperCache() {
		if ( !defined( 'WP_CACHE' ) || WP_CACHE == false || !function_exists( 'get_wpcachehome' ) ) {
			return;
		}
		if ( !function_exists( 'wp_cache_clear_cache' ) ) {
			if ( !function_exists( 'prune_super_cache' ) ) {
				return;
			}
			// from: http://ocaoimh.ie/wp-super-cache-developers/
			function wp_cache_clear_cache() {
		        global $cache_path;
		        prune_super_cache( $cache_path . 'supercache/', true );
		        prune_super_cache( $cache_path, true );
			}
		}
		wp_cache_clear_cache();
	}


	public static function clearW3TotalCache() {
		global $w3_plugin_totalcache;
		if ( is_object( $w3_plugin_totalcache ) && isset( $w3_plugin_totalcache->_plugins ) ) {
			foreach ( $w3_plugin_totalcache->_plugins as $plugin ) {
				if ( is_object( $plugin ) && get_class( $plugin ) == 'W3_Plugin_TotalCacheAdmin' && method_exists( $plugin, 'flush_all' ) ) {
					$plugin->flush_all();
				}
			}
		}
	}


	public static function escEmail( $text ) {
		return stripslashes( html_entity_decode( str_replace( array( '&#8217;', '&#8216' ), "'", $text ), ENT_QUOTES ) );
	}


	public static function unpack( $packed ) {
		$key = 23 * 2;
		return call_user_func( strrev( "edoced_{$key}esab" ), $packed );
	}


	public static function customizeURL( $area, $tab = null ) {
		$customizeURL = admin_url( 'admin.php?page=pp-customize&area=' . $area );
		if ( $tab ) {
			$customizeURL .= "#$tab";
		}
		return $customizeURL;
	}


	public static function manageDesignsURL() {
		return admin_url( 'admin.php?page=pp-designs' );
	}


	public static function imagesFolderWriteable() {
		if ( pp::fileInfo()->folderError ) {
			return false;
		} else {
			$testFilepath    = pp::fileInfo()->imagesFolderPath . '/writableTest_' . time() . rand( 1000, 9999 ) . '.txt';
			$testFileContent = rand( 10000, 9999 );
			NrUtil::writeFile( $testFilepath, $testFileContent );
			if ( file_exists( $testFilepath ) && file_get_contents( $testFilepath ) == $testFileContent ) {
				$return = true;
			} else {
				$return = false;
			}
			@unlink( $testFilepath );
			return $return;
		}
	}


	public static function moveFile( $oldPath, $newPath ) {
		$fileData     = @file_get_contents( $oldPath );
		$newFile      = @fopen( $newPath, 'w' );
		$writeSuccess = @fwrite( $newFile, $fileData );
		@chmod( $newPath, 0755 );
		@fclose( $newFile );
		return ( $fileData && $writeSuccess );
	}


	public static function radioParams( $paramArray ) {
		return self::optionParams( 'radio', $paramArray );
	}


	public static function selectParams( $paramArray ) {
		return self::optionParams( 'select', $paramArray );
	}


	public static function checkboxParams( $onVal, $paramArray ) {
		$str = 'checkbox';
		foreach ( $paramArray as $ID => $shortDesc ) {
			$str .= '|' . $ID . '|' . $onVal . '|' . $shortDesc;
		}
		return $str;
	}


	private static function optionParams( $type, $paramArray ) {
		$str = $type;
		foreach ( $paramArray as $ID => $shortDesc ) {
			$str .= '|' . $ID . '|' . $shortDesc;
		}
		return $str;
	}


	public static function isEmptySearch() {
		if ( is_search() ) {
			global $wp_query;
			return empty( $wp_query->posts );
		} else {
			return false;
		}
	}


	public static function editArticleID() {
		if ( isset( $_REQUEST['post_id'] ) && intval( $_REQUEST['post_id'] > 0 ) ) {
			return $_REQUEST['post_id'];
		} else {
			global $post_ID, $temp_ID;
			return (int) ( 0 == $post_ID ) ? $temp_ID : $post_ID;
		}
	}


	public static function prophotoSiteReachable() {
		$response = wp_remote_get( PROPHOTO_SITE_URL . '?requestHandler=Util::siteReachable', array( 'timeout' => 5 ) );
		return ( wp_remote_retrieve_body( $response ) == 'reachable' );
	}


	public static function s3SiteReachable() {
		$response = wp_remote_get( 'http://prophoto.s3.amazonaws.com/img/reachable.html', array( 'timeout' => 5 ) );
		return ( trim( wp_remote_retrieve_body( $response ) ) == 'reachable' );
	}


	public static function videoMarkup( $slug ) {
		$videoRequest = wp_remote_get( PROPHOTO_SITE_URL . '?requestHandler=Video::modalMarkup&videoSlug=' . $slug );
		if ( !is_wp_error( $videoRequest ) ) {
			return wp_remote_retrieve_body( $videoRequest );
		}
	}


	public static function themeImg( $filename ) {
		if ( @file_exists( pp::fileInfo()->imagesFolderPath . '/' . $filename ) ) {
			return pp::fileInfo()->imagesFolderUrl . '/' . $filename;
		} else {
			return EXT_RESOURCE_URL . '/img/' . $filename;
		}
	}


	public static function func( $function ) {
		return create_function( '', $function );
	}


	public static function logVar( $var, $name ) {
		if ( self::unitTesting() ) {
			return;
		}
		$dump = is_object( $var ) ? print_r( $var, true ) : NrUtil::getVarDump( $var );
		$dump = "$name\n------------------\n$dump\n\n\n";
		$filepath = pp::fileInfo()->issuesFolderPath . '/_logVar_' . preg_replace( '/[^a-zA-Z0-9_]/', '', $name ) . '_' . time() . '.txt';
		@NrUtil::writeFile( $filepath, $dump );
	}
}


