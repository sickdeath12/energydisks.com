<?php


class ppFacebookAPIReader {


	const API_BASE = 'https://graph.facebook.com/';
	protected $http;


	public function __construct( WpHttpRequest $http ) {
		$this->http = $http;
	}


	public function getCommentsByHref( $href ) {
		$comments = array();
		$response = $this->http->get( self::API_BASE . 'comments/?ids=' . $href  );
		if ( $graphObj = $response->jsonDecodedBody() ) {
			if ( isset( $graphObj->{$href} ) && isset( $graphObj->{$href}->comments->data ) && is_array( $graphObj->{$href}->comments->data ) ) {
				$commentsArray = $graphObj->{$href}->comments->data;
				foreach ( $commentsArray as $commentObject ) {
					$comments = $this->pushComment( $comments, $commentObject, 0 );
				}
			}
		}
		return $comments;
	}


	protected function pushComment( $comments, $comment, $parentCommentID ) {
		$comments[] = (object) array(
			'ID'              => $comment->id,
			'authorName'      => $comment->from->name,
			'authorID'        => $comment->from->id,
			'text'            => $comment->message,
			'parentCommentID' => $parentCommentID,
			'createdTime'     => $comment->created_time,
		);
		if ( isset( $comment->comments ) ) {
			foreach ( $comment->comments->data as $childComment ) {
				$comments = $this->pushComment( $comments, $childComment, $comment->id );
			}
		}
		return $comments;
	}

}

