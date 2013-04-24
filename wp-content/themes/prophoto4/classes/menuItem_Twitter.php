<?php


class ppMenuItem_Twitter extends ppMenuItem_Special {

	const DEFAULT_NUM_TWEETS = 8;
	protected $numTweets;
	protected $twitterID = '';
	protected $hasOwnChildren = true;


	public function url() {
		return 'http://twitter.com/' . $this->twitterID;
	}


	public function children() {
		return NrHtml::span( $this->numTweets, 'class=numTweets js-info' ) . NrHtml::span( $this->twitterID, 'class=twitterID js-info' );
	}


	protected function __construct( $ID, $itemData, $children ) {
		$this->numTweets = isset( $itemData->numTweets ) ? $itemData->numTweets : self::DEFAULT_NUM_TWEETS;
		if ( isset( $itemData->twitterID ) ) {
			$this->twitterID = $itemData->twitterID;
		}
		parent::__construct( $ID, $itemData, $children );
		$this->classes[] = 'twitter-id-' . $this->twitterID;
	}

}