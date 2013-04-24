<?php

class ppLegacy {


	public static function updateAll() {
		self::updateActiveDesign();
		self::backImportP3SlideshowMusicFiles();
	}


	public static function updateActiveDesign() {
		self::removeEliminatedOptions();
		self::mergeFacebookLikeBtnShowFacesOptionIntoLayout();
		self::convertCommmentsHeaderEmailAFriendLinkBody();
	}


	public static function updateLikeBoxWidgetInstance( $oldCode, $widgetType, $widgetID ) {
		$instance = array();
		$params = array( 'href', 'width', 'show-faces', 'header', 'colorscheme', 'stream' );
		if ( NrUtil::isIn( '<iframe', $oldCode ) ) {
			foreach ( $params as $param ) {
				$hasMatch = preg_match( '/' . str_replace( '-', '_', $param ) . '=([^&"]+)/', $oldCode, $match );
				if ( $hasMatch && isset( $match[1] ) ) {
					$instance[$param] = urldecode( $match[1] );
				}
			}
		} else if ( NrUtil::isIn( '<fb:like-box', $oldCode ) || NrUtil::isIn( '<div class="fb-like-box"', $oldCode ) ) {
			foreach ( $params as $param ) {
				$useParam = NrUtil::isIn( '<fb:', $oldCode ) ? str_replace( '-', '_', $param ) : $param;
				$hasMatch = preg_match( '/' . $useParam . '="([^"]+)/', $oldCode, $match );
				if ( $hasMatch && isset( $match[1] ) ) {
					$instance[$param] = urldecode( $match[1] );
				}
			}
		}
		ppUtil::logVar( array( 'legacyCode' => $oldCode, 'updatedInstanceData' => $instance ), 'FB Like Box update' );
		ppWidgetUtil::updateWidget( $widgetType, $widgetID, $instance );
		return $instance;
	}


	protected static function convertCommmentsHeaderEmailAFriendLinkBody() {
		if ( ppOpt::test( 'comments_header_emailafriend_link_body', 'Have a look at this:' ) ) {
			ppOpt::update( 'comments_header_emailafriend_link_body', 'Check out this post: %post_url%' );
		}
	}


	protected static function backImportP3SlideshowMusicFiles() {
		if ( ppOpt::test( 'p3_legacy_mp3_import_complete' ) ) {
			return;
		}

		$p3ImportedPosts = new WP_Query();
		$p3ImportedPosts->query(
			array(
				'meta_key'            => 'p3_flash_gal_info',
				'post_type'           => array( 'post', 'page' ),
				'posts_per_page'      => 100,
				'ignore_sticky_posts' => true,
			)
		);
		wp_reset_query();

		foreach ( $p3ImportedPosts->posts as $importedPost ) {
			$data = maybe_unserialize( get_post_meta( $importedPost->ID, 'p3_flash_gal_info', true ) );
			if ( isset( $data['mp3'] ) && !empty( $data['mp3'] ) && preg_match( '/\.mp3$/i', $data['mp3'] ) ) {
				$mp3URL  = ppPathfixer::fix( urldecode( $data['mp3'] ) );
				$mp3Path = pp::fileInfo()->p3FolderPath . end( explode( '/p3/', $mp3URL ) );
				if ( @file_exists( $mp3Path ) ) {
					$associatedGalleries = ppGallery::galleriesAssociatedWithArticle( $importedPost->ID );
					foreach ( $associatedGalleries as $associatedGallery ) {
						if ( ppUtil::moveFile( $mp3Path, pp::fileInfo()->musicFolderPath . '/' . basename( $mp3Path ) ) ) {
							$associatedGallery->slideshowOption( 'musicFile', pp::fileInfo()->musicFolderUrl . '/' . basename( $mp3Path ) );
							$associatedGallery->save();
						}
					}
				}
			}
		}
		ppOpt::update( 'p3_legacy_mp3_import_complete', 'true' );
	}


	protected static function mergeFacebookLikeBtnShowFacesOptionIntoLayout() {
		if ( ppOpt::test( 'like_btn_show_faces', 'true' ) && ppOpt::test( 'like_btn_layout', 'standard' ) ) {
			ppOpt::update( 'like_btn_layout', 'standard_with_faces' );
		}
		ppOpt::delete( 'like_btn_show_faces' );
	}


	protected static function removeEliminatedOptions() {
		$eliminatedOptions = apply_filters( 'pp_legacy_eliminated_opts', array(
			'fb_set_post_image',
		) );
		foreach ( $eliminatedOptions as $eliminatedOption ) {
			ppOpt::delete( $eliminatedOption );
		}
	}

}

