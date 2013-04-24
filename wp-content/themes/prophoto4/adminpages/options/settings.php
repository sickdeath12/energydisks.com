<?php
/* ----------------------- */
/* ----ADVANCED OPTIONS--- */
/* ----------------------- */




// tabs and header
ppSubgroupTabs(  array(
	'social_media'  => 'Social media',
	'analytics'     => 'Analytics',
	'widget_images' => 'Widget images',
	'site_icons'    => 'Site icons',
	'misc'          => 'Misc.',
) );
ppOptionHeader('Settings', 'settings' );



/* social media */
ppOptionSubgroup( 'social_media' );

// facebook preview options
ppStartMultiple( 'Facebook preview options' );
if ( pp::site()->hasStaticFrontPage ) {
	ppO( 'facebook_static_front_page', 'image', "Facebook preview thumbnail image for your <em>static front page</em>" );
	ppO( 'facebook_static_front_page_desc', 'textarea|5|34', "override default <em>description</em> for Facebook preview of <em>static front page</em>" );
	ppO( 'facebook_static_front_page_title', 'text|32', "override default <em>title</em> for Facebook preview of <em>static front page</em>" );
}
$home = pp::site()->hasStaticFrontPage ? '<em>blog posts page</em>' : 'site home page';
ppO( 'fb_home', 'image', "Facebook preview thumbnail image for $home" );
ppO( 'facebook_blog_posts_page_desc', 'textarea|5|34', "override default <em>description</em> for Facebook preview of $home" );
ppO( 'facebook_blog_posts_page_title', 'text|32', "override default <em>title</em> for Facebook preview of $home" );

ppStopMultiple();

ppStartMultiple( 'Site-wide Facebook settings' );
ppO( 'like_btn_verb', 'radio|like|like|recommend|recommend', 'word used in Like button' );
ppO( 'like_btn_color_scheme', 'radio|light|light|dark|dark', 'Like button color scheme' );
ppO( 'facebook_language', ppUtil::selectParams( array(
	'af_ZA' => 'Afrikaans', 'ar_AR' => 'Arabic', 'az_AZ' => 'Azeri', 'be_BY' => 'Belarusian', 'bg_BG' => 'Bulgarian',
	'bn_IN' => 'Bengali', 'bs_BA' => 'Bosnian', 'ca_ES' => 'Catalan', 'cs_CZ' => 'Czech', 'cy_GB' => 'Welsh',
	'da_DK' => 'Danish', 'de_DE' => 'German', 'el_GR' => 'Greek', 'en_GB' => 'English (UK)', 'en_PI' => 'English (Pirate)',
	'en_UD' => 'English (Upside Down)', 'en_US' => 'English (US)', 'eo_EO' => 'Esperanto', 'es_ES' => 'Spanish (Spain)',
	'es_LA' => 'Spanish', 'et_EE' => 'Estonian', 'eu_ES' => 'Basque', 'fa_IR' => 'Persian', 'fb_LT' => 'Leet Speak',
	'fi_FI' => 'Finnish', 'fo_FO' => 'Faroese', 'fr_CA' => 'French (Canada)', 'fr_FR' => 'French (France)',
	'fy_NL' => 'Frisian', 'ga_IE' => 'Irish', 'gl_ES' => 'Galician', 'he_IL' => 'Hebrew', 'hi_IN' => 'Hindi',
	'hr_HR' => 'Croatian', 'hu_HU' => 'Hungarian', 'hy_AM' => 'Armenian', 'id_ID' => 'Indonesian', 'is_IS' => 'Icelandic',
	'it_IT' => 'Italian', 'ja_JP' => 'Japanese', 'ka_GE' => 'Georgian', 'km_KH' => 'Khmer', 'ko_KR' => 'Korean',
	'ku_TR' => 'Kurdish', 'la_VA' => 'Latin', 'lt_LT' => 'Lithuanian', 'lv_LV' => 'Latvian', 'mk_MK' => 'Macedonian',
	'ml_IN' => 'Malayalam', 'ms_MY' => 'Malay', 'nb_NO' => 'Norwegian (bokmal)', 'ne_NP' => 'Nepali', 'nl_NL' => 'Dutch',
	'nn_NO' => 'Norwegian (nynorsk)', 'pa_IN' => 'Punjabi', 'pl_PL' => 'Polish', 'ps_AF' => 'Pashto',
	'pt_BR' => 'Portuguese (Brazil)', 'pt_PT' => 'Portuguese (Portugal)', 'ro_RO' => 'Romanian', 'ru_RU' => 'Russian',
	'sk_SK' => 'Slovak', 'sl_SI' => 'Slovenian', 'sq_AL' => 'Albanian', 'sr_RS' => 'Serbian', 'sv_SE' => 'Swedish',
	'sw_KE' => 'Swahili', 'ta_IN' => 'Tamil', 'te_IN' => 'Telugu', 'th_TH' => 'Thai', 'tl_PH' => 'Filipino',
	'tr_TR' => 'Turkish', 'uk_UA' => 'Ukrainian', 'vi_VN' => 'Vietnamese', 'zh_CN' => 'Simplified Chinese (China)',
	'zh_HK' => 'Traditional Chinese (Hong Kong)', 'zh_TW' => 'Traditional Chinese (Taiwan)',
) ), 'Facebook language' );
ppO( 'facebook_admins', 'text|35', 'your Facebook personal numeric ID [<a href="' . ppIframe::url( 'get_facebook_id', 400, 290 ) . '" class="thickbox">?</a>]' );
ppStopMultiple();



// main twitter info
ppO( 'twitter_name', 'text', 'Your Twitter name. [<a style="cursor:pointer" onclick="javascript: jQuery(\'#twitter-name-explain\').slideToggle();">?</a>]</p><p id="twitter-name-explain" style="display:none">This is your twitter username, and is also the end of your twitter address. So if your twitter address is <span style="white-space:nowrap;font-family:courier, monospace;">http://twitter.com/susiephoto</span><br />then "susiephoto" is your twitter name', 'Main Twitter account info' );

ppEndOptionSubgroup();



/* anaylitics */
ppOptionSubgroup( 'analytics' );

	// google analytics
	ppO('google_analytics_code', 'textarea|8|75', 'Paste in your Google Analytics tracking code (new version - ga.gs) here. Will not count your own visits to your blog.', 'Google Analytics' );

	// statcounter
	ppO('statcounter_analytics_code', 'textarea|8|75', 'Paste in your Statcounter setup code here. Will not count your own visits to your blog.', 'Statcounter Analytics' );

ppEndOptionSubgroup();




/* widget images */
ppOptionSubgroup( 'widget_images' );

echo <<<HTML
<script>
jQuery(document).ready(function($){
	ppOption.uploadReveal('widget_custom_image_');
});
</script>
HTML;



ppO( 'widget_images_note', 'note', 'These upload areas work in conjunction with the <a href="' . admin_url( 'widgets.php' ) . '">widgets page</a>.  Here you can upload images that will, after uploading, be available for use in the <strong>ProPhoto Custom Icon</strong> and <strong>ProPhoto Sliding Twitter</strong> widgets.', 'Widget custom images' );

for ( $i = 1; $i <= pp::num()->maxCustomWidgetImages; $i++ ) {
	ppUploadBox::renderImg( 'widget_custom_image_' . $i, 'Widget custom image #' . $i );
}

ppEndOptionSubgroup();



/* Site icons */
ppOptionSubgroup( 'site_icons' );

	// favicon image
	$faviconUploadBox = new ppUploadBox_Favicon();
	$faviconUploadBox->render();


	//iphone webclip image
	ppUploadBox::renderImg( 'apple_touch_icon', 'iPhone Webclip Icon', ppString::id( 'blurb_apple_touch_icon' ) );

ppEndOptionSubgroup();





/* blog settings */
function ppSecureDownloadLink() {
	return NrHtml::a(
		admin_url( 'admin-ajax.php' ) . '?action=pp&generate_download_link=1',
		'Generate download link',
		'id=generate-download&target=_blank&class=button-secondary'
	);
}
echo <<<HTML
<style type="text/css" media="screen">
#secure_download_link-individual-option p {
	width:450px;
	max-width:4500px;
}
a.waiting-for-link {
	cursor:default !important;
	opacity:0.75;
}
a.waiting-for-link:hover {
	border-color: #bbb;
	color: #464646;
}
</style>
<script>
jQuery(document).ready(function($){

	$('a#generate-download').click(function(){
		var link = $(this);
		if ( link.hasClass('waiting-for-link') ) {
			return false;
		}
		link.text('Generating download link...');
		link.addClass('waiting-for-link');
		$.ajax({
			type:'GET',
			url: link.attr('href'),
			success: function(response){
				link.removeClass('waiting-for-link')
				if ( response.indexOf('http://') !== -1 ) {
					link.attr('href',response);
					link.text('Click to download latest build');
					link.unbind('click');
					link.removeClass('button-secondary').addClass('button-primary');
				} else {
					this.failure();
				}
			},
			error: function(){
				this.failure();
			},
			failure: function(){
				link.after('<em style="color:red;">Error generating link.</em>');
				link.remove();
			}
		});
		return false;
	});

});
</script>
HTML;


ppOptionSubgroup( 'misc' );

// maintainance mode
ppStartMultiple( 'Under construction mode' );
ppO( 'maintenance_mode', 'radio|on|under construction mode on|off|under construction mode off', 'Display an "under construction" message to all blog visitors except you. Used to prevent viewing of your blog while customizations are being worked on.' );
ppO( 'maintenance_message', 'textarea|3|32', 'message displayed to visitors when in "under construction" mode' );
ppStopMultiple();


// backup reminder
ppStartMultiple( 'Backup reminder' );
ppO( 'backup_reminder', 'radio|on|on|off|off', 'Email me monthly with a reminder to backup my blog' );
ppO( 'backup_email', 'text|37', 'Email address that backup reminders will be sent to.' );
ppStopMultiple();

// ProPhoto Updates
ppStartMultiple( 'ProPhoto Updates' );
if ( ppUtil::isAutoUpgradeCapable() ) {
	ppO( 'auto_auto_upgrade', 'radio|true|enable automatic updates|false|disable automatic updates', 'enable ProPhoto to automatically update itself whenever a new free update is available', 'Automatic ProPhoto updates' );
}

if ( ppUtil::isAutoUpgradeCapable() && ppOpt::test( 'auto_auto_upgrade', 'true' ) ) {
	$downloadComment = 'Your site is capable of auto-updating, and it set to do so whenever a new update is recommended. If for some reason you need a <code>.zip</code> file download of the most recent build, you can generate a download link by clicking above.';
} else if ( ppUtil::isAutoUpgradeCapable() && !ppOpt::test( 'auto_auto_upgrade', 'true' ) ) {
	$downloadComment = 'Your site is capable of auto-updating, but you have chosen to disable auto-updates.  If you require a <code>.zip</code> file download of the most recent build for a manual update, you can generate a download link by clicking above.';
} else {
	$downloadComment = 'Due to your current web-host and server configuration, your site is not capable of auto-updating. Whenever there is a required update, ProPhoto will notify you with a red notice in the top of your WordPress admin area. If a ProPhoto tech instructs you to, or if for some other reason you require a <code>.zip</code> file download of the most recent build for a manual update, you can generate a download link by clicking above.';
}

if ( NrUtil::isIn( 'http://localhost', pp::site()->url ) ) {
	ppO( 'secure_download_link', 'function|__return_false', 'latest build download links are not available on local installations' );
} else if ( ppOpt::test( 'not_registered', 'false' ) && NrUtil::validEmail( trim( ppOpt::id( 'payer_email' ) ) ) && preg_match( '/([A-Z0-9]){17}/', trim( ppOpt::id( 'txn_id' ) ) ) ) {
	ppO( 'secure_download_link', 'function|ppSecureDownloadLink', $downloadComment );
} else {
	ppO( 'secure_download_link', 'function|__return_false', 'You may not download a more recent ProPhoto update unless you first <a href="' . admin_url( 'themes.php?activated=true' ) . '">register</a>.' );
}
ppStopMultiple();


// disable gd img downsizing
ppStartMultiple( 'Image downsizing' );
	ppO( 'gd_img_downsizing', 'radio|enabled|enable image downsizing|disabled|disable all image downsizing', 'enable ProPhoto to automatically downsize images larger than the area they are being displayed within' );
	ppO( 'gd_img_downsizing_max_size', 'slider|1500|10000| px|100', 'downsizing max-size threshold: ProPhoto will only attempt to downsize images if their height + width is LESS than this amount' );
ppStopMultiple();

// registration
ppStartMultiple( 'P4 Registration' );
if ( !ppOpt::test( 'payer_email' ) || !ppOpt::test( 'txn_id' ) ) {
	ppO( 'unregistered', 'note', 'You have not successfully registered your copy of ProPhoto. ProPhoto will not work fully until it is registered.  To register, <a href="' . admin_url( 'themes.php?activated=true' ) . '">click here</a> and fill out the simple form.' );

} else {
	ppO( 'registered', 'note', 'You have successfully registered your copy of ProPhoto.  Your registration info is shown below:<br /><br />Purchase email: <code>' . ppOpt::id( 'payer_email' ) . '</code><br />Transaction ID: <code>' . ppOpt::id( 'txn_id' ) . '</code><br /><br />' );
}
ppStopMultiple();

ppO( 'dev_test_mode', 'radio|enabled|enabled|disabled|disabled', 'Enabling unregistered test-mode allows you to customize a ProPhoto blog that is <em>not registered</em>, but only logged-in administrators will be able to see the site.', 'Unregistered test-mode' );

ppEndOptionSubgroup();










