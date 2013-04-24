<?php

class ppDesignUtil {


	public static function processPOST( $post ) {
		if ( !NrUtil::isAssoc( $post ) || !isset( $post['pp_POST_identifier'] ) || !is_string( $post['pp_POST_identifier'] ) ) {
			new ppIssue( 'Invalid input data' );
			return false;
		}

		switch ( $post['pp_POST_identifier'] ) {

			case 'designs_page_create_new':
			case 'designs_page_copy':
				return self::createNew( $post );

			case 'designs_page_misc':
				return self::handleMisc( $post );

			case 'designs_page_edit_meta':
				return self::editMeta( $post );

			case 'designs_page_reset_all':
				return self::resetEverything();
		}
	}


	public static function exportEverything() {

		$designDataFiles = array();
		foreach ( ppStorage::designIds() as $designId ) {

			$design = ppStorage::requestDesign( $designId );

			if ( false === $design ) {
				new ppIssue( "Unable to retrieve design with id '$designId' for export everything" );
				return 'Error exporting everything. Please try again.';
			}

			$designFilePath = self::writeDesignData( $design );

			if ( $designFilePath ) {
				$designDataFiles[] = $designFilePath;
			} else {
				new ppIssue( 'Unable to create text file for design: ' . $design->id() . ' while exporting everything' );
				return ppString::id( 'cant_create_export_txt_file', pp::fileInfo()->designsSubDir, ppZip::workingDir() );
			}
		}

		$uploadedImgs = glob( pp::fileInfo()->imagesFolderPath . '/*.{jpg,JPG,jpeg,JPEG,gif,GIF,png,PNG}', GLOB_BRACE );
		$exportFiles = array_merge( $designDataFiles, $uploadedImgs );

		$zip = new ppZip( 'export_everything_' . time() . '.zip', $exportFiles );
		$zip->create();

		foreach ( $designDataFiles as $designDataFile ) {
			@unlink( $designDataFile );
		}

		return ppString::id( 'zip_everything_success', $zip->zipUrl() );
	}


	public static function import( $importZipPath ) {
		if ( !is_string( $importZipPath ) || !@file_exists( $importZipPath ) || 'zip' !== NrUtil::fileExt( $importZipPath ) ) {
			new ppIssue( "Invalid \$importZipPath '$importZipPath'" );
			return false;
		}

		$return = false;
		$zip = new ppZip( $importZipPath );
		$zip->extract();

		$fileContents = @file_get_contents( $zip->dataFilePath() );
		$designData   = @json_decode( $fileContents, true );

		if ( $designData && !is_serialized( $fileContents ) && is_array( $designData ) ) {
			if ( !isset( $designData['meta'] ) || !isset( $designData['imgs'] ) || !isset( $designData['options'] ) ) {
				new ppIssue( 'Invalid design data to import extracted from "' . $zip->dataFilePath() . '"' );
			} else {
				if ( isset( $designData['widgets'] ) ) {
					$oldFormatWidgetData = $designData['widgets'];
					unset( $designData['widgets'] );
					$designData['activation_widgets'] = ppWidgetUtil::updateDesignWidgetData( $oldFormatWidgetData );
				}
				$importedDesign = new ppDesign( self::nameToId( $designData['meta']['name'] ), $designData );
				$savedDesign = ppStorage::saveNewDesign( $importedDesign );
			}

		} else {
			if ( $p3DesignData = @unserialize( $fileContents ) ) {
				$importedDesign = ppImportP3::uploadedDesign( $p3DesignData );
				if ( isset( $p3DesignData['widgets'] ) ) {
					$importedDesign->activationWidgets( ppImportP3::transformExportedWidgetData( unserialize( $p3DesignData['widgets'] ) ) );
				}
				$savedDesign = ppStorage::saveNewDesign( $importedDesign );

			} else {
				new ppIssue( 'Unable to extract design data from "' . $zip->dataFilePath() . '"' );
			}
		}

		if ( $savedDesign ) {
			foreach ( $zip->extractedFiles() as $extracted ) {
				$fileExt = NrUtil::fileExt( $extracted['stored_filename'] );
				if ( NrUtil::isWebSafeImg( $extracted['stored_filename'] ) || $fileExt == 'ico' ) {
					ppUtil::moveFile( $extracted['filename'], pp::fileInfo()->imagesFolderPath . '/' . $extracted['stored_filename'] );
				} else if ( in_array( $fileExt, ppFontUtil::fontFileExts() ) ) {
					ppUtil::moveFile( $extracted['filename'], pp::fileInfo()->fontsFolderPath  . '/' . $extracted['stored_filename'] );
				} else if ( preg_match( '/\.mp3$/i', $extracted['filename']) ) {
					ppUtil::moveFile( $extracted['filename'], pp::fileInfo()->musicFolderPath  . '/' . $extracted['stored_filename'] );
				}
			}
			$return = $savedDesign;
		}

		@unlink( $zip->dataFilePath() );
		return $return;
	}


	public static function importManuallyUploadedDesigns() {
		if ( $zips = glob( pp::fileInfo()->imagesFolderPath . '/{exported_design,design_}*.zip', GLOB_BRACE ) ) {
			$imported = array();
			foreach ( $zips as $zip ) {
				$imported[] = self::import( $zip );
				@unlink( $zip );
			}
			return $imported;
		} else {
			return false;
		}
	}


	public static function export( $designId, $activationWidgets = null ) {
		$design = ppStorage::requestDesign( $designId );

		if ( false === $design ) {
			new ppIssue( "Unable to retrieve design with id '$designId' for export" );
			return 'Error exporting design. Please try again.';
		}

		$designFilePath = self::writeDesignData( $design, $activationWidgets );

		if ( $designFilePath ) {

			$files = array( $designFilePath );
			foreach ( $design->imgs() as $fileID => $filename ) {
				if ( !NrUtil::startsWith( $fileID, 'audio' ) ) {
					$img = ppImg::id( $fileID );
					if ( $img->filename != $img->defaultFilename ) {
						$files[] = $img->path;
					}
				} else {
					if ( preg_match( '/.mp3$/i', $filename ) && @file_exists( $audioFilepath = pp::fileInfo()->musicFolderPath . '/' . $filename ) ) {
						$files[] = $audioFilepath;
					}
				}
			}

			$designOptions = $design->options();
			for ( $i = 1; $i <= pp::num()->maxCustomFonts; $i++ ) {
				if ( isset( $designOptions['custom_font_'.$i] ) ) {
					$fontData = (array) json_decode( $designOptions['custom_font_'.$i] );
					if ( isset( $fontData['name'] ) && isset( $fontData['slug'] ) ) {
						foreach ( ppFontUtil::fontFileExts() as $fontFileExt ) {
							$files[] = pp::fileInfo()->fontsFolderPath . '/' . $fontData['slug'] . '-webfont.' . $fontFileExt;
						}
					}
				}
			}

			$zip = new ppZip( 'exported_design_' . self::nameToId( $design->name() ) . '.zip', $files );
			$zip->create();

			unlink( $designFilePath );

			if ( !$zip->errors() ) {
				return ppString::id( 'zip_design_success', $design->name(), $zip->zipUrl() );
			} else {
				return $zip->errorMsg();
			}

		} else {
			new ppIssue( 'Unable to create text file for export' );
			return ppString::id( 'cant_create_export_txt_file', pp::fileInfo()->designsSubDir, ppZip::workingDir() );
		}
	}


	private static function writeDesignData( ppDesign $design, $activationWidgets = null ) {
		$filePath   = ppZip::workingDir() . 'design_data_' . $design->id() . '.txt';
		$designData = $design->toArray();
		if ( is_array( $activationWidgets ) ) {
			$designData['options']['exported_for_prophoto_store'] = 'true';
		}
		if ( $activationWidgets ) {
			$designData['activation_widgets'] = $activationWidgets;
		}
		$writeSuccess = ppUtil::writeFile( $filePath, json_encode( $designData ) );
		if ( $writeSuccess ) {
			return $filePath;
		} else {
			return false;
		}
	}


	public static function resetEverything() {
		global $wpdb;
		$options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE 1" );

		foreach ( (array) $options as $option ) {
			if ( NrUtil::isIn( ppStorage::DESIGN_DB_OPTION_PREFIX, $option->option_name ) ) {
				delete_option( $option->option_name );
			}
		}
		delete_option( ppStorage::DESIGNS_DB_OPTION_NAME );
		delete_option( ppStorage::GLOBAL_OPTS_DB_OPTION_NAME );
		delete_option( pp::wp()->dbContactLog );
		ppImportP3::deleteImportRecords();
		ppActivate::init( ppActivate::NO_AUTO_IMPORT_P3 );
		delete_transient( 'pp_recommended_svn' );
		delete_transient( 'delay_download_remote_files_attempt' );
		delete_transient( 'pp_delay_next_auto_upgrade_attempt' );
	}


	private static function editMeta( $post ) {
		if ( !isset( $post['design_name'] ) || !isset( $post['design_desc'] ) ) {
			new ppIssue( 'Insufficient data POSTed' );
			return false;
		}

		if ( trim( $post['design_name'] == '' ) ) {
			new ppIssue( 'Design name empty' );
			return false;
		}

		$design = ppStorage::requestDesign( $post['design_id'] );
		$design->name( $post['design_name'] );
		$design->desc( $post['design_desc'] );
		return ppStorage::saveDesign( $design );
	}


	private static function handleMisc( $post ) {
		if ( !isset( $post['action'] ) || !isset( $post['value'] ) ) {
			new ppIssue( 'Insufficient data POSTed' );
			return false;
		}

		switch ( $post['action'] ) {

			case 'delete_design':
				return ppStorage::deleteDesign( $post['value'] );

			case 'activate_design':
				return ppStorage::designateActive( $post['value'] );

			default:
				new ppIssue( 'Unknown "action" POSTed' );
				return false;
		}
	}


	private static function createNew( $post ) {
		if ( !isset( $post['new_design_name'] ) || !isset( $post['new_design_desc'] ) || !isset( $post['template'] ) ) {
			new ppIssue( 'Insufficient data POSTed' );
			return false;
		}

		if ( in_array( $post['template'], ppStorage::designIds() ) ) {
			$template = ppStorage::requestDesign( $post['template'] );

		} else if ( ppStarterDesigns::isStarter( $post['template'] ) ) {
			$template = new ppDesign( 'template', ppUtil::loadConfig( 'starter_' . $post['template'] ) );

		} else {
			new ppIssue( "Unknown template '{$post['template']}'" );
			return false;
		}

		$newDesign = new ppDesign( self::nameToId( $post['new_design_name'] ) );
		$newDesign->name( $post['new_design_name'] );
		$newDesign->desc( $post['new_design_desc'] );
		$newDesign->imgs( $template->imgs() );
		$newDesign->options( $template->options() );
		$newDesign->activationWidgets( $template->activationWidgets() );

		return ppStorage::saveNewDesign( $newDesign );
	}


	private static function nameToId( $name ) {
		$sanitized = ltrim( rtrim( strtolower( preg_replace( '/[^A-Za-z0-9]/', '_', $name ) ), '_' ), '_' );
		$sanitized = str_replace( array( '____', '___', '__' ), '_', $sanitized );
		if ( '' == trim( $sanitized ) ) {
			$sanitized = 'untitled_design_' . time();
		}
		return $sanitized;
	}

}
