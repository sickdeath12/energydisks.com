<?php


class ppDb extends WpDbFacade {


	const FB_COMMENT_META_KEY     = 'pp_facebook_comment_id_url';
	const RETURN_STRING_NOT_ARRAY = true;


	public function addFacebookCommentMeta( $wpCommentID, $fbCommentID ) {
		// $unique = true prevents WordPress from ever adding multiple comment meta values with the same id
		// which is technially possible to do. since we have a 1:1 relationship, we force it to be unique
		return add_comment_meta( $wpCommentID, self::FB_COMMENT_META_KEY, $fbCommentID, $unique = true );
	}


	public function facebookCommentMeta( $wpCommentID ) {
		$fbMeta = get_comment_meta( $wpCommentID, self::FB_COMMENT_META_KEY, self::RETURN_STRING_NOT_ARRAY );
		if ( $fbMeta && NrUtil::isIn( '|', $fbMeta ) ) {
			list( $fbID, $permalinkWhenAdded ) = explode( '|', $fbMeta );
			return (object) compact( 'fbID', 'permalinkWhenAdded' );
		} else {
			return false;
		}
	}


	public function facebookCommentAlreadyAdded( $fbCommentID ) {
		global $wpdb;
		$query  = $wpdb->prepare(
			"SELECT comment_id
			 FROM $wpdb->commentmeta
			 WHERE
			 	meta_key = '%s'
			 		AND
			 	meta_value LIKE '%%%s%%'",
			self::FB_COMMENT_META_KEY, $fbCommentID
		);
		$result = $wpdb->get_row( $query );
		return ( $result && isset( $result->comment_id ) );
	}

}

