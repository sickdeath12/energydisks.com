<?php

class ppComment {


	protected $classes = array( 'sc', 'pp-comment' );
	protected $wpObj;


	public function __construct( $wpObj ) {
		if ( !is_object( $wpObj ) ) {
			new ppIssue( 'Non-object passed to ppComment::__construct()' );
			return;
		}
		$this->wpObj = $wpObj;
	}


	public function id() {
		return $this->wpObj->comment_ID;
	}


	public function text( $prepend = '', $append = '' ) {
		$text = trim( get_comment_text( $this->id() ) . $append );
		$text = apply_filters( 'comment_text', $text, $this->wpObj );
		if ( $prepend ) {
			$text = $this->prepend( $prepend, $text );
		}
		return trim( $text );
	}


	public function approved() {
		return ( $this->wpObj->comment_approved == 1 );
	}


	public function date() {
		return get_comment_date( $dateFormat = '', $this->id() );
	}


	public function time() {
		$GLOBALS['comment'] = $this->wpObj;
		$commentTime = get_comment_time();
		unset( $GLOBALS['comment'] );
		return $commentTime;
	}


	public function timeMarkup() {
		if ( !ppOpt::test( 'comment_timestamp_display', 'off' ) ) {
			$time = $this->time() ? ' - ' . $this->time() : '';
			return NrHtml::span( $this->date() . $time, 'class=comment-time' );
		} else {
			return '';
		}
	}


	public function authorName() {
		return get_comment_author( $this->id() );
	}


	public function authorUrl() {
		return get_comment_author_url( $this->id() );
	}


	public function classes() {
		return implode( ' ', get_comment_class( $this->classes, $this->id() ) );
	}


	public function addClass( $class ) {
		$this->classes[] = $class;
	}


	public function authorMarkup() {
		if ( $this->authorUrl() ) {
			$markup = NrHtml::a(
				$this->authorUrl(),
				$this->authorName(),
				'class=url&rel=external nofollow&target=' . ppOpt::id( 'comment_author_link_target' )
			);
		} else {
			$markup = $this->authorName();
		}
		if ( ppOpt::test( 'comment_meta_position', 'inline' ) || ppOpt::test( 'comment_timestamp_display', 'left' ) ) {
			$markup .= ' <span>-</span> ';
		}
		return '<span class="comment-author">' . apply_filters( 'get_comment_author_link', $markup ) . '</span>';
	}


	public function avatar( $size ) {
		return get_avatar( get_comment_author_email( $this->id() ), $size );
	}


	protected function prepend( $prepend, $text ) {
		if ( $text[0] != '<' || !preg_match( '/^(<p|<div)/i', $text ) ) {
			return $prepend . $text;
		}
		return preg_replace( "/(<(?:div|p)[^>]*>)/i", '\1' . $prepend, $text, $limit = 1 );
	}

}