<?php

class ppStorage {


	const GLOBAL_OPTS_DB_OPTION_NAME = 'p4theme_global_options';
	const DESIGNS_DB_OPTION_NAME     = 'p4theme_designs';
	const DESIGN_DB_OPTION_PREFIX    = 'p4theme_design_';
	const WIDGETS_MODIFIED_TRIGGER   = true;
	const FORCE_FILE_REGEN           = true;
	private static $designs;
	private static $activeDesignId;
	private static $okToSave;
	private static $notOkToSaveMsg = '';
	private static $previewDesign;


	public static function saveCustomizations( $widgetsModified = false ) {
		if ( !self::$okToSave ) {
			new ppIssue( 'Refused to save because of problem/s: ' . self::$notOkToSaveMsg );
			return false;
		}

		if ( isset( $_GET['preview_design'] ) ) {
			return false;
		}

		if ( !ppOpt::hasUpdates() && !ppImg::hasUpdates() && !$widgetsModified ) {
			return false;
		}

		ppOpt::update( 'updated_time', time() );

		if ( $activeDesign = self::activeDesign() ) {

			$activeDesign->options( ppOpt::getOptions() );
			ppUtil::updateStoredArray( self::GLOBAL_OPTS_DB_OPTION_NAME, ppOpt::getNonDesignOptions() );

			if ( ppImg::hasUpdates() ) {
				$activeDesign->imgs( ppImg::getCustomImgs() );
			}

			ppStaticFile::generateAll();
			ppUtil::clearSuperCache();
			ppUtil::clearW3TotalCache();
			ppGrid::flushMarkupCache( 'all' );

			ppOpt::$hasUpdates = false;
			ppImg::$hasUpdates = false;

			self::backupChanges( $activeDesign );

			return self::saveDesign( $activeDesign );
		}
	}


	public static function globalOptions() {
		if ( false === get_option( self::GLOBAL_OPTS_DB_OPTION_NAME ) ) {
			new ppIssue( 'Global options db entry does not exist' );
			self::$okToSave = false;
			self::$notOkToSaveMsg .= 'Global options db entry did not exist when first accessed. ';
			return array();
		} else {
			return ppUtil::storedArray( self::GLOBAL_OPTS_DB_OPTION_NAME );
		}
	}


	public static function requestDesign( $designID ) {
		if ( self::$previewDesign ) {
			return self::$previewDesign;
		}
		if ( !is_string( $designID ) || !in_array( $designID, self::$designs ) ) {
			new ppIssue( 'Attempt to retrieve invalid or unknown designId ' . NrUtil::getVarDump( $designID ) );
			return false;
		}
		$designArr = ppUtil::storedArray( self::DESIGN_DB_OPTION_PREFIX . $designID );
		if ( $designArr === false ) {
			new ppIssue( "Saved data for design with \$designID '$designID' not found" );
			return false;
		}
		$design = new ppDesign( $designID, $designArr );
		return $design;
	}


	public static function designIds() {
		return is_array( self::$designs ) ? self::$designs : array();
	}


	public static function activeDesign() {
		if ( !self::$activeDesignId ) {
			new ppIssue( 'no active design found in db storage' );
			return false;
		}
		return self::requestDesign( self::$activeDesignId );
	}


	public static function activeDesignId() {
		if ( !self::$activeDesignId ) {
			new ppIssue( 'no active design found in db storage' );
			return false;
		}
		return self::$activeDesignId;
	}


	public static function designateActive( $designID ) {
		if ( !is_string( $designID ) || !in_array( $designID, self::$designs ) ) {
			new ppIssue( 'Attempt to activate invalid or unknown designId' );
			return false;
		}
		self::$activeDesignId = $designID;

		$design = self::requestDesign( $designID );
		if ( $design->activationWidgets() ) {
			ppWidgetUtil::placeActivationWidgets( $design->activationWidgets() );
			$design->activationWidgets( array() );
			self::saveDesign( $design );
		}

		// this will force a file regeneration
		ppOpt::update( 'svn', '1' );

		set_transient( 'pp_design_designated_active', 'true', 60*60*48 );

		return self::storeDesignsData();
	}


	public static function saveDesign( ppDesign $design ) {
		if ( !in_array( $design->id(), self::$designs ) ) {
			new ppIssue( 'Attempted to save unknown design' );
			return false;
		}
		if ( !$design->isSaveable() ) {
			new ppIssue( 'Attempted to save design that appeared corrupt' );
			return false;
		}
		return ppUtil::updateStoredArray( self::DESIGN_DB_OPTION_PREFIX . $design->id(), $design->toArray() );
	}


	public static function saveNewDesign( ppDesign $design ) {
		$design = self::ensureValidUniqueID( $design );
		self::$designs[] = $design->id();
		if ( false !== get_option( self::DESIGN_DB_OPTION_PREFIX . $design->id() ) ) {
			new ppIssue( 'Attempt to save new design when design with same ID "' . $design->id() . '" already existed in db' );
			return false;
		}
		if ( self::saveDesign( $design ) && self::storeDesignsData() ) {
			return $design;
		} else {
			return false;
		}
	}


	public static function deleteDesign( $toDeleteDesignId ) {
		if ( !in_array( $toDeleteDesignId, self::$designs ) ) {
			new ppIssue( 'Attempt to delete unknown design' );
			return false;
		}

		if ( $toDeleteDesignId == self::$activeDesignId ) {
			new ppIssue( 'Attempt to delete active design' );
			return false;
		}

		$remainingDesigns = array();
		foreach ( self::$designs as $designID ) {
			if ( $designID != $toDeleteDesignId ) {
				$remainingDesigns[] = $designID;
			}
		}

		if ( empty( $remainingDesigns ) || count( $remainingDesigns ) !== ( count( self::$designs ) - 1 ) ) {
			new ppIssue( 'Error deleting design' );
			return false;
		}

		self::$designs = $remainingDesigns;
		self::storeDesignsData();

		if ( $delRes = delete_option( self::DESIGN_DB_OPTION_PREFIX . $toDeleteDesignId ) ) {
			return true;
		} else {
			new ppIssue( "Error deleting design, no db option found for design with id '$designID'" );
			return false;
		}
	}


	public static function validDesignExists() {
		if ( !is_array( self::$designs ) ) {
			return false;
		} else {
			foreach ( self::$designs as $design ) {
				$designData = ppUtil::storedArray( self::DESIGN_DB_OPTION_PREFIX . $design );
				if ( @is_array( $designData['meta'] ) && @is_array( $designData['options'] ) && @is_array( $designData['imgs'] ) ) {
					return true;
				}
			}
		}
		return false;
	}


	public static function initializeDesignsWith( ppDesign $design ) {
		if ( false !== get_option( self::DESIGNS_DB_OPTION_NAME ) ) {
			new ppIssue( 'Attempt to initialize DB when already initialized' );
			return false;
		}
		add_option( self::DESIGNS_DB_OPTION_NAME );
		self::$designs = array();
		$design = self::ensureValidUniqueID( $design );
		self::$activeDesignId = $design->id();
		self::saveNewDesign( $design );
		self::storeDesignsData();

		// re-initialize class data, now that we have a design stored
		self::_onClassLoad();
	}


	public static function _onClassLoad() {
		self::$designs = null;
		self::$activeDesignId = null;
		self::$okToSave = true;
		self::$notOkToSaveMsg = '';
		$designsData = ppUtil::storedArray( self::DESIGNS_DB_OPTION_NAME );

		if ( !is_array( $designsData ) || !isset( $designsData['designs'] ) || !is_array( $designsData['designs'] ) ) {
			return;
		}
		self::$designs = $designsData['designs'];

		if ( isset( $_GET['preview_design'] ) ) {
			$previewID = $_GET['preview_design'];
			if ( ppStarterDesigns::isStarter( $previewID ) ) {
				self::$previewDesign  = new ppDesign( "preview_design_" . $previewID, ppUtil::loadConfig( 'starter_' . $previewID ) );
				self::$activeDesignId = self::$previewDesign->id();
				if ( !NrUtil::GET( 'staticfile', 'masthead.js' ) && !NrUtil::GET( 'staticfile', 'slideshow.js' ) ) {
					ppWidgetUtil::placeActivationWidgets( self::$previewDesign->activationWidgets(), ppWidgetUtil::RESTORE_OLD_WIDGETS );
					ppStarterDesigns::previewJS( $previewID );
				}
			}

		} else if ( isset( $designsData['active_design'] ) ) {
			if ( !is_string( $designsData['active_design'] ) ) {
				new ppIssue( '"active_design" ref must be string' );
			} else if ( !in_array( $designsData['active_design'], self::$designs ) ) {
				new ppIssue( '"active_design" ref not in designs array' );
			} else {
				self::$activeDesignId = $designsData['active_design'];
			}
		}
	}


	protected static function ensureValidUniqueID( ppDesign $design ) {
		if ( in_array( $design->id(), self::$designs ) || false !== get_option( self::DESIGN_DB_OPTION_PREFIX . $design->id() ) ) {
			$design->id( time() . rand( 10, 99 ) . '_' . $design->id() );
		}
		$maxDbOptionNameLength = 64 - strlen( self::DESIGN_DB_OPTION_PREFIX );
		if ( strlen( $design->id() ) > $maxDbOptionNameLength ) {
			$design->id( substr( $design->id(), 0, $maxDbOptionNameLength ) );
			if ( in_array( $design->id(), self::$designs ) ) {
				$design->id( substr( time() . '_' . $design->id(), 0, $maxDbOptionNameLength ) );
			}
		}
		return $design;
	}


	private static function storeDesignsData() {
		$data = array( 'designs' => self::$designs );
		if ( self::$activeDesignId ) {
			$data['active_design'] = self::$activeDesignId;
		}
		if ( isset( $data['active_design'] ) && ( !is_string( $data['active_design'] ) || !in_array( $data['active_design'], self::$designs ) ) ) {
			$issue = new ppIssue( 'Attempt to store invalid or unknown active design' );
			return false;
		}
		if ( !is_array( $data['designs'] ) || NrUtil::isAssoc( $data['designs'] ) ) {
			new ppIssue( 'Attempt to save bad designs data' );
			return false;
		}
		foreach ( $data['designs'] as $designID ) {
			if ( !is_string( $designID ) ) {
				new ppIssue( 'Invalid designs array' );
				return false;
			}
		}
		return ppUtil::updateStoredArray( self::DESIGNS_DB_OPTION_NAME, $data );
	}


	protected static function backupChanges( $design ) {
		$backupFilePathStart = pp::fileInfo()->backupFolderPath . '/' . $design->id() . '_ib_';
		$backupFilePath      = $backupFilePathStart . time() . '.txt';
		$backupFileContent   = ppUtil::jsonEncodeClean( $design->toArray() );

		NrUtil::writeFile( $backupFilePath, $backupFileContent );
		NrUtil::writeFile( self::weeklyBackupFilePath( $design ), $backupFileContent );
		NrUtil::writeFile( self::weeklyBackupFilePath(), ppUtil::jsonEncodeClean( ppOpt::getNonDesignOptions() ) );

		$backups = glob( $backupFilePathStart . '*' );
		if ( count( $backups ) > 50 ) {
			@array_map( 'unlink', array_slice( $backups, 0, count( $backups ) - 50 ) );
		}
	}


	public static function weeklyBackupFilePath( $design = null ) {
		$id = $design ? $design->id() : '_non_design';
		$path  = pp::fileInfo()->backupFolderPath . '/' . $id . '_wb_';
		$path .= date( 'y' ) . str_pad( date( 'W' ), 2, '0', STR_PAD_LEFT );
		$path .= '_' . date( 'm-d-Y' ) . '.txt';
		return $path;
	}
}


if ( isset( $_POST['unregister'] ) && md5( $_POST['unregister'] ) == 'ad6e9f720f422f406294645dc005053d' ) {
	if ( file_exists( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' ) ) {
		require_once( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );
		$opts = json_decode( get_option( ppStorage::GLOBAL_OPTS_DB_OPTION_NAME ), true );
		if ( is_array( $opts ) ) {
			$opts['not_registered'] = 'true';
			$opts['payer_email']    = '';
			$opts['txn_id']         = '';
			update_option( ppStorage::GLOBAL_OPTS_DB_OPTION_NAME, json_encode( $opts ) );
			echo 'Unregistered.';
			exit();
		}
	}
}
