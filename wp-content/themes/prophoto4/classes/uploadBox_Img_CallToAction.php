<?php


class ppUploadBox_Img_CallToAction extends ppUploadBox_Img {


	protected $num;


	public function __construct( $num ) {
		$this->num = $num;
		parent::__construct( 'call_to_action_' . $this->num, 'Call to action item #' . $this->num );
	}


	public function aboveOptions() {
		$main = new ppOptionBoxMultiple( 'call_to_action_' . $this->num, ppUtil::selectParams( array(
			'off'                      => 'off',
			'back_to_top'              => 'Back to top',
			'show_contact_form'        => 'Contact me',
			'facebook_like_btn'        => 'Facebook "Like" button',
			'share_on_facebook'        => 'Share on Facebook',
			'follow_on_twitter'        => 'Follow me on Twitter',
			'tweet_this_url'           => 'Tweet this post',
			'pinterest_follow_me'      => 'Pinterest - follow me',
			'pinterest_pin_site_image' => 'Pinterest - pin site image',
			'subscribe_rss'            => 'Subscribe (RSS)',
			'subscribe_by_email'       => 'Subscribe by email',
			'email_me'                 => 'Email me',
			'email_this_url'           => 'Email post to friend',
			'custom_url'               => 'Custom URL',
		) ), '', '' );

		$c = 'call_to_action'; $em = 'emailafriend_link';
		$display    = new ppOptionBoxMultiple( "{$c}_display_"           . $this->num, 'select|text|link text|image|uploaded image', 'display type', '' );
		$url        = new ppOptionBoxMultiple( "{$c}_url_"               . $this->num, 'text|40', 'custom URL', '' );
		$target     = new ppOptionBoxMultiple( "{$c}_target_"            . $this->num, 'select|_self|same window/tab|_blank|new window/tab', 'link opens in...', '' );
		$twitter    = new ppOptionBoxMultiple( "{$c}_twittername_"       . $this->num, 'text|30', 'twitter name', '' );
		$text       = new ppOptionBoxMultiple( "{$c}_text_"              . $this->num, 'text|40', 'link text', '' );
		$email      = new ppOptionBoxMultiple( "{$c}_email_"             . $this->num, 'text|40', 'email address', '' );
		$pFollow    = new ppOptionBoxMultiple( "{$c}_pinterest_name_"    . $this->num, 'text|40', 'pinterest name', '' );
		$likeSend   = new ppOptionBoxMultiple( "{$c}_fb_like_with_send_" . $this->num, 'radio|false| just "Like" button|true|"Like" &amp; "Send" buttons', '', '' );
		$likeLayout = new ppOptionBoxMultiple( "{$c}_fb_like_layout_"    . $this->num, 'radio|button_count|count on right|box_count|count on top', '', '' );
		$emailSubj  = new ppOptionBoxMultiple( "{$c}_{$this->num}_{$em}_subject", 'text|50', 'default text for the <em>subject</em> of the generated email', '' );
		$emailBody  = new ppOptionBoxMultiple( "{$c}_{$this->num}_{$em}_body",    'text|50', 'default text for the <em>body</em> of the generated email', '' );


		$innerMarkup =
			'<div class="setting switch sc">'      . $main->option_markup       . '</div>' .
			'<div class="setting display sc">'     . $display->option_markup    . '</div>' .
			'<div class="setting text sc">'        . $text->option_markup       . '</div>' .
			'<div class="setting url sc">'         . $url->option_markup        . '</div>' .
			'<div class="setting twitter sc">'     . $twitter->option_markup    . '</div>' .
			'<div class="setting p-follow sc">'    . $pFollow->option_markup    . '</div>' .
			'<div class="setting target sc">'      . $target->option_markup     . '</div>' .
			'<div class="setting email sc">'       . $email->option_markup      . '</div>' .
			'<div class="setting email-subj sc">'  . $emailSubj->option_markup  . '</div>' .
			'<div class="setting email-body sc">'  . $emailBody->option_markup  . '</div>' .
			'<div class="setting like-layout sc">' . $likeSend->option_markup   . '</div>' .
			'<div class="setting like-send sc">'   . $likeLayout->option_markup . '</div>';

		return NrHtml::div( $innerMarkup, 'class=above-options sc' );
	}


}

