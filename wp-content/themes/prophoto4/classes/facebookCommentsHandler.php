<?php


class ppFacebookCommentsHandler {


	protected $db;
	protected static $instance;


	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new ppFacebookCommentsHandler( new ppDb() );
		}
		return self::$instance;
	}


	public function __construct( ppDB $db ) {
		$this->db = $db;
	}


	public function processNewComment( $articleID, $articleFacebookHref, ppFacebookAPIReader $APIReader ) {
		if ( is_numeric( $articleID ) ) {
			if ( is_string( $articleFacebookHref ) ) {
				if ( $fbComments = $APIReader->getCommentsByHref( $articleFacebookHref ) ) {
					return $this->syncArticleComments( $articleID, $fbComments );
				} else {
					$error = new ppIssue( "No FB comments could be found for href $articleFacebookHref" );
					return $error->message();
				}
			} else {
				$error = new ppIssue( 'Invalid non-string article href passed' );
				return $error->message();
			}
		} else {
			$error = new ppIssue( 'Invalid non-numeric article ID passed' );
			return $error->message();
		}
	}


	public function addClasses( $comments, ppPost $article ) {
		foreach ( (array) $comments as $comment ) {
			if ( $comment instanceof ppComment ) {
				if ( $fbMeta = $this->db->facebookCommentMeta( $comment->id() ) ) {
					$comment->addClass( 'added-via-fb' );
					if ( $fbMeta->permalinkWhenAdded != $article->permalink() ) {
						$comment->addClass( 'from-fb-legacy-permalink' );
					}
				}
			} else {
				new ppIssue( '$comment passed to ppFacebookCommentsHandler::addClasses() not instanceof "ppComment"' );
			}
		}
		return $comments;
	}


	public function syncMissingArticleComments( ppPost $article, ppFacebookAPIReader $APIReader ) {
		if ( $this->db->getTransient( 'pp_fb_missing_comments_synced_within_hour' ) ) {
			return;
		}
		$transPrefix = 'pp_fb_missing_comments_synced_';
		if ( !$this->db->getTransient( $transPrefix . $article->id() ) ) {
			if ( $fbComments = $APIReader->getCommentsByHref( $article->permalink() ) ) {
				$this->syncArticleComments( $article->id(), $fbComments );
			}
			$this->db->setTransient( $transPrefix . $article->id(), 'checked_recently', 60*60*24*21 );
			$this->db->setTransient( 'pp_fb_missing_comments_synced_within_hour', 'checked_within_hour', 60*60 );
		}
	}


	protected function syncArticleComments( $articleID, $fbComments ) {
		$errors   = array();
		$addedIDs = array();

		foreach ( (array) $fbComments as $fbComment ) {

			if ( !$this->db->facebookCommentAlreadyAdded( $fbComment->ID ) ) {

				$wpCommentArray = $this->prepCommentDataForWpInsert( $articleID, $fbComment );

				if ( $insertedCommentID = $this->db->insertNewComment( $wpCommentArray ) ) {

					$fbCommentMeta = $fbComment->ID . '|' . $this->db->permalinkByArticleID( $articleID );
					if ( $this->db->addFacebookCommentMeta( $insertedCommentID, $fbCommentMeta ) ) {
						$addedIDs[] = $fbComment->ID;
					} else {
						$this->db->deleteCommentByID( $insertedCommentID );
						$error = new ppIssue( "Failed to associate WP comment $insertedCommentID with FB comment $fbComment->ID, WP comment deleted" );
						$errors[] = $error->message();
					}

				} else {
					$error = new ppIssue( "Failure adding comment with FB ID $fbComment->ID to WP DB" );
					$errors[] = $error->message();
				}
			}
		}

		if ( $errors ) {
			return "There were errors syncing article comments:\n" . implode( "\n", $errors );
		} else {
			$msg = "FB comments for article $articleID synced successfully. ";
			if ( $addedIDs ) {
				$msg .= 'New FB comment/s ' . implode( ', ', $addedIDs ) . ' synced to WP db.';
			} else {
				$msg .= 'No unsynced FB comments found.';
			}
			return $msg;
		}
	}


	protected function prepCommentDataForWpInsert( $articleID, stdClass $fbComment ) {
		return array(
			'comment_post_ID' => $articleID,
			'comment_author'  => $fbComment->authorName,
			'comment_date'    => str_replace( array( 'T', '+0000' ), array( ' ', '' ), $fbComment->createdTime ),
			'comment_content' => $fbComment->text,
			'comment_agent'   => 'synced by ProPhoto theme from scraped Facebook comment',
			'comment_parent'  => $fbComment->parentCommentID,
		);
	}

}


