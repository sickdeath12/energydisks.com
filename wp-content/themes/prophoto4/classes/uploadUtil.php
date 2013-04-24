<?php

class ppUploadUtil {


	public static function processUpload( $post, $files ) {

		if ( isset( $post['upload_type'] ) && !empty( $files ) ) {

			switch ( $post['upload_type'] ) {
				case 'img':
					self::imgUpload( $post, $files );
					break;
				case 'design_zip':
					self::designZipUpload( $post, $files );
					break;
				case 'font_zip':
					self::fontZipUpload( $post, $files );
					break;
				case 'audio_file':
					self::audioFileUpload( $post, $files );
					break;
				default:
					new ppIssue( "Unknown upload_type '{$post['upload_type']}'" );
					self::renderUploadFail();
			}

		} else {
			new ppIssue( 'Missing upload data' );
			self::renderUploadFail( 'Missing upload data.' );
		}
	}


	protected function imgUpload( $post, $files ) {

		if ( $post['file_id'] == 'favicon' ) {
			$uploadClass = 'ppUploadFavicon';

		} else if ( $post['file_id'] == 'logo_swf' || $post['file_id'] == 'masthead_custom_flash' ) {
			$uploadClass = 'ppUploadSwf';

		} else {
			$uploadClass = 'ppUploadImg';
		}

		$imgUpload = new $uploadClass( $post['file_id'], $files );

		if ( $imgUpload->success() ) {
			$success = new ppIFrame( array(
				'pp_iframe' => 'img_upload_success',
				'file_id'    => $imgUpload->id()
			) );
			$success->render();

		} else {
			self::renderUploadFail( $imgUpload->errorMsg() );
		}
	}


	protected function designZipUpload( $post, $files ) {

		$zipUpload = new ppUploadDesignZip( $files );

		if ( $zipUpload->success() ) {
			$importedDesign = ppDesignUtil::import( $zipUpload->uploadedZipPath() );

			if ( $importedDesign !== false ) {
				$success = new ppIFrame( array(
					'pp_iframe'       => 'design_import_success',
					'imported_design' => $importedDesign,
				) );
				$success->render();

			} else {
				new ppIssue( 'Uploaded design failed to import' );
				self::renderUploadFail( 'Uploaded design failed to import.' );
			}

		} else {
			new ppIssue( 'Design zip upload failed with error: ' . $zipUpload->errorMsg() );
			self::renderUploadFail( $zipUpload->errorMsg() );
		}
	}


	protected function fontZipUpload( $post, $files ) {

		$fontUpload = new ppUploadFontZip( $post['font_zip_id'], $files );

		if ( $fontUpload->success() ) {

			$fontUpload->process();

			if ( $fontUpload->processSuccess() ) {
				$success = new ppIFrame( array(
					'pp_iframe' => 'font_upload_success',
					'font'      => $fontUpload,
				) );
				$success->render();
			} else {
				self::renderUploadFail( $fontUpload->errorMsg() );
			}

		} else {
			new ppIssue( 'Font zip upload failed with error: ', $fontUpload->errorMsg() );
			self::renderUploadFail( $fontUpload->errorMsg() );
		}
	}


	protected function audioFileUpload( $post, $files ) {

		$audioUpload = new ppUploadAudio( $post['file_id'], $files );

		if ( $audioUpload->success() ) {
			$success = new ppIFrame( array(
				'pp_iframe' => 'audio_upload_success',
				'file_id'   => $audioUpload->id()
			) );
			$success->render();

		} else {
			self::renderUploadFail( $audioUpload->errorMsg() );
		}
	}


	protected static function renderUploadFail( $msg = '' ) {
		$fail = new ppIFrame( array(
			'pp_iframe' => 'msg',
			'msg'       => 'An error occurred, please try again. ' . $msg,
		) );
		$fail->render();
	}
}

