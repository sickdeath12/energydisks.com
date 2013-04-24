<?php


class ppStarterDesigns {


	protected static $data;


	public static function data() {
		if ( self::$data === null ) {
			$designs = array();
			$configFiles = glob( TEMPLATEPATH . '/config/conf.starter_*' );
			foreach ( $configFiles as $configFile ) {
				$starterID = str_replace( array( 'conf.starter_', '.php' ), '', basename( $configFile ) );
				$starterData = ppUtil::loadConfig( "starter_$starterID" );
				$designs[$starterID] = (object) array(
					'id' => $starterID,
					'name' => $starterData['meta']['name'],
					'desc' => $starterData['meta']['desc'],
				);
			}
			$order = array(
				'sunny_california',
				'hayden',
				'mercury',
				'vandelay',
				'late_august',
				'emilie',
				'elegant',
				'aqua',
				'grunge',
				'minimalist',
				'brown',
				'prophoto2',
			);
			foreach ( $order as $id ) {
				self::$data[$id] = $designs[$id];
				unset( $designs[$id] );
			}
			self::$data = array_merge( self::$data, $designs );
		}
		return (object) apply_filters( 'pp_starter_designs_data', self::$data );
	}


	public static function isStarter( $ID ) {
		return @file_exists( TEMPLATEPATH . '/config/conf.starter_' . $ID . '.php' );
	}


	public static function previewJS( $ID = null ) {
		static $previewID;
		if ( $ID ) {
			$previewID = $ID;
			add_action( 'wp_head', 'ppStarterDesigns::previewJS' );
		} else {
			$js  = "var pp_preview_design_id = \"$previewID\";\n";
			$js .= file_get_contents( TEMPLATEPATH . '/dynamic/js/previewDesign.js' );
			echo NrHtml::script( $js );
		}

	}


	public static function flushCache() {
		self::$data = null;
	}
}

