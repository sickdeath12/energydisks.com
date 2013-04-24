<?php
/* ----------------------- */
/* ----ADVANCED OPTIONS--- */
/* ----------------------- */




// tabs and header
$tabs = array(
	'rss' => 'RSS',
	'seo' => 'SEO',
	'code' => 'Custom code',
	'translation' => 'Translation',
	'bg_imgs' => 'Extra background images',
);
if ( isset( $_GET['designer'] ) ) {
	$tabs['designer'] = 'Designer options';
}
if ( NrUtil::isIn( 'pp_dev', $_SERVER['HTTP_USER_AGENT'] ) ) {
	$tabs['developer'] = 'Developer options';
}
ppSubgroupTabs( $tabs );
ppOptionHeader('Settings', 'settings' );




/* rss feed subgroup */
ppOptionSubgroup( 'rss');
echo <<<HTML
<script>
jQuery(document).ready(function($){

	ppOption.uploadReveal( 'extra_bg_img_' );


	$('#p4-input-feedburner').blur(function(){
		if (/feedburner/.test($('#p4-input-feedburner').val()) == false && $('#p4-input-feedburner').val() != '' ) {
			if ( !$('#feedburner-error').length ) {
				$('#p4-input-feedburner').css('border', 'red 1px solid').after('<p id="feedburner-error" style="color:red">This must be a feedburner address, not your blog\'s bult-in feed address. See the below linked tutorial for more info.</p>');
			}
			$('#p4-save-changes').attr('disabled', 'disabled');
		} else {
			$('#p4-input-feedburner').css('border', '#DFDFDF solid 1px');
			$('#feedburner-error').remove();
			$('#p4-save-changes').removeAttr('disabled');
		}
	});
});
</script>
HTML;

// feedburner
ppO('feedburner', 'text|69', 'Enter your <a href="http://www.feedburner.com">Feedburner</a> URL here if you have burned your blog\'s feed to feedburner and want to use that instead of the built-in feed address.', 'Feedburner URL' );

// feed image protection
ppStartMultiple( 'Feed image protection' );
ppO( 'modify_feed_images', 'radio|false|leave images untouched|remove|remove images completely|modify|downsize images', 'what to do with images that come through in your RSS feed' );
ppO( 'modify_feed_images_alert', 'textarea|4|68', 'message displayed in feed only when images are modified - words inside carets (^) will be linked to the post permalink page' );
ppO( 'blank', 'blank' );

ppO( 'feed_thumbnail_type', 'radio|medium|show medium thumbnails|thumbnail|show small thumbnails', 'show fullsize images or thumbnails - if ProPhoto can\'t find a thumbnail for an image, the image will not be shown' );

ppStopMultiple();
ppEndOptionSubgroup();




/* SEO */
ppOptionSubgroup( 'seo' );

if ( $pluginName = ppSeo::pluginDetected() ) {
	ppO( 'seo_pack_advise', 'note', 'ProPhoto has detected that you are running an SEO plugin, so it has disabled it\'s own SEO options.  If you would prefer to use ProPhoto\'s SEO options go to the <a href="' . admin_url( 'plugins.php' ) . '">plugins page</a> and deactivate "' . $pluginName . '."', 'SEO options disabled' );

} else {
	// disable SEO
	ppO( 'seo_disable', 'radio|false|SEO options enabled|true|SEO options disabled', 'disable ProPhoto4 SEO options if you would rather use a plugin to handle your SEO options', 'SEO options' );

	// title tags
	ppStartMultiple( 'Title tags' );
	ppO( 'seo_title_home', 'text|30', 'title tag on <strong>blog ' . $home = ( pp::site()->hasStaticFrontPage ) ? 'posts' : 'home' . ' page</strong>' );
	if ( pp::site()->hasStaticFrontPage ) ppO( 'seo_title_front_page', 'text|30', 'title tag on <strong>static home page</strong>' );
	ppO( 'seo_title_single', 'text|30', 'title tags on <strong>single posts</strong>' );
	ppO( 'seo_title_page', 'text|30', 'title tags on <strong>pages</strong>' );
	ppO( 'seo_title_category', 'text|30', 'title tags on <strong>category pages</strong>' );
	ppO( 'seo_title_archive', 'text|30', 'title tags on <strong>archive pages</strong>' );
	ppO( 'seo_title_search', 'text|30', 'title tags on blog <strong>search result pages</strong>' );
	ppO( 'seo_title_author', 'text|30', 'title tags on blog <strong>author archive pages</strong>' );
	ppO( 'seo_title_tag', 'text|30', 'title tags on blog <strong>tag pages</strong>' );

	ppStopMultiple();

	// meta descriptions
	ppStartMultiple( 'Meta descriptions' );
	ppO( 'seo_meta_desc', 'textarea|5|30', 'Write a custom "meta description" - this will often be what appears in search engine results pages beneath the link to your blog. If blank, the theme will use the "Tagline" for your blog from "Settings" => "General"' );
	ppO( 'seo_meta_desc_options', 'checkbox|seo_meta_use_excerpts|true|use excerpts for post/page meta descriptions|seo_meta_auto_generate|true|auto-generate meta descriptions when no excerpt' );

	// meta keywords
	ppStartMultiple( 'Meta keywords' );
	ppO( 'seo_meta_keywords', 'textarea|3|30', 'Enter a list of keywords and keyword phrases, separated by commas, for the meta keywords tag -- 10 max.' );
	ppO( 'seo_meta_keywords_tags', 'checkbox|seo_tags_for_keywords|true|use post tags as keywords when possible' );
	ppStopMultiple();

	// noindex
	ppO( 'noindexoptions', 'checkbox|noindex_archive|true|monthly archives|noindex_category|true|categories|noindex_search|true|search results|noindex_tag|true|tag archives|noindex_author|true|author archives', 'If you have elected to show full posts on archive and category pages instead of excerpts, checking these can help prevent duplicate content penalties from search engines.', 'Prevent duplicate content' );
	ppStopMultiple();
}

ppEndOptionSubgroup();




if ( 0 ) {
	/* music player */
	ppOptionSubgroup( 'audio' );

	echo <<<HTML
	<script>
	jQuery(document).ready(function($){
		ppOption.valToClass( 'audioplayer' );
		ppOption.uploadReveal( 'audio' );
	});
	</script>
HTML;

	// audio player
	ppStartMultiple( 'Audio MP3 Player' );
	ppO( 'audioplayer', 'radio|off|off|bottom|bottom of page|top|top of page', 'activate and place the built-in audio MP3 player to provide music for your blog' );
	ppO( 'audiooptions', 'checkbox|audioplayer_autostart|yes|music should autostart|audioplayer_loop|yes|music should loop when finished' );
	if ( pp::site()->hasStaticFrontPage ) {
		$home = 'Posts page';
		$static = '|audio_on_front_page|yes|Static front page';
	} else {
		$home = 'Home page';
		$static = '';
	}
	ppO( 'audio_where', "checkbox{$static}|audio_on_home|yes|$home|audio_on_single|yes|Single post pages|audio_on_pages|yes|Pages|audio_on_archive|yes|Archive, category, tag, search, & author", 'choose which types of pages you want the audio player to appear on' );
	ppO( 'audio_hidden', 'radio|on|hide audio player|off|show audio player (recommended)', 'Hide audio player.  If hidden, user will not be able to stop music.' );
	ppStopMultiple();

	// audio player colors
	ppStartMultiple( 'Audio player custom colors' );
	ppO( 'audioplayer_center_bg', 'color', 'color of center of player when playing', 'first' );
	ppO( 'audioplayer_text', 'color', 'color of text in audio player' );
	ppO( 'audioplayer_left_bg', 'color', 'color of background of left side of player' );
	ppO( 'audioplayer_left_icon', 'color', 'color of speaker icon in left side of player' );
	ppO( 'audioplayer_right_bg', 'color', 'color of background of right side of player' );
	ppO( 'audioplayer_right_icon', 'color', 'color of play/pause icon in right side of player' );
	ppO( 'audioplayer_right_bg_hover', 'color', 'color of background of right side of player when hovered over' );
	ppO( 'audioplayer_right_icon_hover', 'color', 'color of play/pause icon in right side of player when hovered over' );
	ppO( 'audioplayer_slider', 'color', 'color of slider' );
	ppO( 'audioplayer_loader', 'color', 'color of loader bar' );
	ppO( 'audioplayer_track', 'color', 'color of track bar' );
	ppO( 'audioplayer_track_border', 'color', 'color of border of track bar' );
	ppStopMultiple();

	// FTP-uploaded audio files
	if ( 0  ) {
		ppO( 'audio_ftp_files', 'function|p4_audio_player_ftped', 'List above shows the MP3 files you have uploaded with FTP into the <code>music</code> folder of <code>' . pp::fileInfo()->wpUploadRelPath . '/p4</code>. Click to select them for inclusion in your audio player, or you can upload more files and return to this page. More info <a href="' . pp::tut()->ftpUploadAudio . '" target="_blank">here</a>.', 'Include FTP-uploaded audio MP3 files' );
	} else {
		ppO( 'audio_ftp_files', 'note', 'Uploading MP3 files through this page is limited by most web hosts to <strong>files of 2mb or less</strong>. Many audio MP3 files are larger than that, and cannot be uploaded here.  If you have larger file you want to use, you <strong>can upload it using an FTP program</strong> into your <code>p4/music</code> folder.  Then refresh this page and you will be able to select that file for use in your audio player. Full tutorial is <a href="' . pp::tut()->ftpUploadAudio . '" target="_blank">here</a>.', 'About manually uploading MP3 files' );
	}

	// audio uploads
	for ( $i = 1; $i <= pp::num()->maxAudioUploads; $i++ ) {
		$mp3_upload = new ppAudioUpload( 'audio' . $i, 'Audio MP3 File ' . $i, '' );
		echo $mp3_upload->markup;
	}

	ppEndOptionSubgroup();

}


/* customizations */
ppOptionSubgroup( 'code' );

// custom css
ppO( 'override_css', 'textarea|18|105', 'Custom CSS rules', 'Custom CSS' );

// insert into head
ppO( 'insert_into_head', 'textarea|5|105', 'Custom code inserted into blogs <code>&lt;head&gt;&lt;/head&gt;</code> tag' );

// post signature
ppStartMultiple( 'Post signature' );
ppO( 'post_signature_placement', 'checkbox|post_signature_on_home|true|on blog home page|post_signature_on_single|true|on individual post pages|post_signature_on_page|true|on WordPress static "Pages"', 'where to show the post signature' );
ppO( 'post_signature_filter_priority', 'text|5', 'filter priority (adjust number to affect interaction with like button & plugins)' );
ppO( 'key', 'blank' );

ppO( 'post_signature', 'textarea|7|105', 'Text or HTML added below each post. Special strings: <code>%post_title%</code>, <code>%permalink%</code>, <code>%post_id%</code>, <code>%post_author_name%</code>, and <code>%post_author_id%</code> will be replaced with per-post values.' );

ppStopMultiple();

// custom javascript
ppO( 'custom_js', 'textarea|18|105', 'Custom javascript', 'Custom javascript' );

ppEndOptionSubgroup();




/* translation */
ppOptionSubgroup( 'translation' );

ppO( 'translate_password_protected', 'text|75', 'phrase used as intro to password-protected posts', 'Translation: password protected posts' );


// translate: comments header area
ppStartMultiple( 'Translation: comments header' );
ppO( 'translate_by', 'text|22', 'text shown before post author name link - default is "by", i.e. "<strong>by</strong> <span style="text-decoration:underline;">Admin</span>"' );
ppO( 'translate_no', 'text|22', 'text shown before "comments" when there are no comments, as in "no" comments' );
ppO( 'translate_comments', 'text|22', 'text shown in comment header area when there is zero or more than one comments' );
ppO( 'translate_comment', 'text|22', 'text shown in comment header area when there is only 1 comment' );
ppStopMultiple();

// translate: comment form
ppStartMultiple( 'Translation: comment form' );
ppO( 'translate_commentform_message', 'textarea|2|29', 'text shown at top of comment form' );
ppO( 'translate_comments_required', 'text|32', 'text shown indicating how required fields are marked' );
ppO( 'translate_comment_form_author_label', 'text|26', 'text shown above Name input area of comment form' );
ppO( 'translate_comment_form_email_label', 'text|26', 'text shown above Email input area of comment form' );
ppO( 'translate_comment_form_url_label', 'text|26', 'text shown above Website input area of comment form' );
ppO( 'translate_comment_form_comment_text_label', 'text|26', 'text shown above Comment input area of comment form' );
ppO( 'translate_comment_form_submit_button_label', 'text|26', 'text shown on "post comment" submit button of comment form' );
ppO( 'translate_comments_cancel_reply', 'text|26', 'text shown on "cancel reply" button on home page inline comment forms' );
ppO( 'translate_comment_form_error_message', 'textarea|2|29', 'message shown to user when error submitting comment' );
ppStopMultiple();

// translate: archive pages
ppStartMultiple( 'Translation: archive pages' );
ppO( 'translate_archives_monthly', 'text|33', 'text shown in header of monthly archive pages' );
ppO( 'translate_archives_yearly', 'text|33', 'text shown in header of yearly archive pages' );
ppO( 'translate_blog_archives', 'text|33', 'text shown on certain other archive pages' );
ppO( 'translate_tag_archives', 'text|33', 'text shown as header on tag archive pages' );
ppO( 'translate_category_archives', 'text|33', 'text shown as header on category archive pages' );
ppO( 'translate_author_archives', 'text|33', 'text shown as header on author archive pages' );
ppStopMultiple();

// translate: search page
ppStartMultiple( 'Translation: search page' );
ppO( 'translate_search_results', 'text|33', 'text used in header of search page when results are found' );
ppO( 'translate_search_notfound_header', 'text|33', 'text used in header of search page when results are not found' );
ppO( 'translate_search_notfound_text', 'textarea|2|29', 'text used in body of search page when results are not found' );
ppO( 'translate_search_notfound_button', 'text|33', 'text used in on search page in button of new search form when results are not found' );
ppStopMultiple();

// translate: 404 page
ppStartMultiple( 'Translation: 404 page' );
ppO( 'translate_404_header', 'text|22', 'header for "404 Not Found" template page -- shown when broken link in blog clicked or incorrect URL typed' );
ppO( 'translate_404_text', 'textarea|2|29', '', 'text for "404 Not Found" template page -- shown when broken link in blog clicked or incorrect URL typed' );
ppStopMultiple();

// lightbox text
ppStartMultiple( 'Translation: lightbox gallery' );
ppO( 'translate_lightbox_image', 'text|20', 'word for "Image" as in "<strong>Image</strong> 3 of 15"' );
ppO( 'translate_lightbox_of', 'text|20', 'word for "of" as in "Image 3 <strong>of</strong> 15"' );
ppStopMultiple();

// lightbox text
ppStartMultiple( 'Translation: mobile site' );
ppO( 'translate_mobile_loading', 'text|20', 'text displayed while a newly requested mobile site page is being loaded' );
ppO( 'translate_mobile_error_loading', 'text|20', 'text displayed if there is an error loading mobile site page' );
ppO( 'blank', 'blank' );
ppO( 'translate_mobile_prev_post_link', 'text|20', 'link text for <b>previous</b> post button on mobile single-post pages' );
ppO( 'translate_mobile_next_post_link', 'text|20', 'link text for <b>next</b> post button on mobile single-post pages' );
ppStopMultiple();

// subscribe by email form language
ppO( 'subscribebyemail_lang', 'select|' . FEEDBURNER_LANG_OPTIONS, 'Language that appears in the feedburner subscribe-by-email window', 'Translation: Feedburner subscribe by email form' );

ppEndOptionSubgroup();



/* blog settings */
if ( isset( $_GET['designer'] ) ) {

	ppOptionSubgroup( 'designer' );

	// designer license options
		ppStartMultiple( 'Designer options' );
		ppO( 'designed_for_prophoto_store', 'radio|false|disable advanced export|true|enable advanced export', 'if you intend to submit this design for sale, you must enable this option' );
		ppO( 'des_html_mark', 'text|70', 'custom HTML for added footer attribution link (one link max.)' );
		ppStopMultiple();

	ppEndOptionSubgroup();

}


/* developer options */
ppOptionSubgroup( 'bg_imgs' );

	for ( $i = 1; $i <= pp::num()->maxExtraBgImgs; $i++ ) {
		$extraBgUploadBox = new ppUploadBox_Img_Bg_Extra( 'extra_bg_img_' . $i, 'Extra background image #' . $i, '' );
		$extraBgUploadBox->render();
	}

ppEndOptionSubgroup();




/* developer options */
ppOptionSubgroup( 'developer' );

// developer license options
	ppStartMultiple( 'Developer options' );
		ppO( 'dev_hide_options', 'radio|false|do not hide options from end user|true|hide options from end user', 'optionally hide the "ProPhoto" => "Customize" and "ProPhoto" => "Manage Designs" pages from end user.' );
		ppO( 'dev_html_mark', 'text|39', 'custom HTML for added footer attribution link' );
	ppStopMultiple();

ppEndOptionSubgroup();


