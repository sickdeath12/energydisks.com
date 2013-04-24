<?php


class WpDbFacade {


	public function insertNewComment( $comment ) {
		return wp_insert_comment( $comment );
	}


	public function deleteCommentByID( $commentID ) {
		return wp_delete_comment( $commentID, $forceDeleteNotTrash = true );
	}


	public function installedPluginsData() {
		return get_plugins();
	}


	public function permalinkByArticleID( $articleID ) {
		return get_permalink( $articleID );
	}


	public function getTransient( $transientName ) {
		return get_transient( $transientName );
	}


	public function setTransient( $transientName, $value, $expiration = 0 ) {
		return set_transient( $transientName, $value, $expiration );
	}

}

