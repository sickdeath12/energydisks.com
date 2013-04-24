<?php

class ppMenuItem_SubscribeByEmail extends ppMenuItem_Special {


	const DEFAULT_PREFILL = 'Enter email';
	const DEFAULT_BTN_TXT = 'Subscribe by email';
	protected $subscribeByEmailPrefill;
	protected $subscribeByEmailBtnText;
	protected $hasOwnChildren = true;


	public function aTag() {
		return NrHtml::group( array(
			NrHtml::openForm( 'http://feedburner.google.com/fb/a/mailverify', 'target=_blank' ),
				NrHtml::textInput( 'email', $this->subscribeByEmailPrefill, 12 ),
				NrHtml::hiddenInput( 'uri', ppRss::feedburnerId() ),
				NrHtml::hiddenInput( 'loc', ppOpt::id( 'subscribebyemail_lang' ) ),
				NrHtml::submit( $this->subscribeByEmailBtnText ),
			NrHtml::closeForm(),
		) );
	}


	protected function __construct( $ID, $itemData, $children ) {
		$this->subscribeByEmailPrefill = isset( $itemData->subscribeByEmailPrefill ) ? $itemData->subscribeByEmailPrefill : self::DEFAULT_PREFILL;
		$this->subscribeByEmailBtnText = isset( $itemData->subscribeByEmailBtnText ) ? $itemData->subscribeByEmailBtnText : self::DEFAULT_BTN_TXT;
		parent::__construct( $ID, $itemData, $children );
		$this->text = $this->subscribeByEmailBtnText;
	}
}
