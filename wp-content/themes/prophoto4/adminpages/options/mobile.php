<?php 

ppSubgroupTabs( array(
	'general'    => 'General',
	'logo'       => 'Logo',
	'masthead'   => 'Masthead',
	'bgs'        => 'Backgrounds &amp; colors',
	'fonts'      => 'Fonts',
	'comments'   => 'Comments',
	'footer_nav' => 'Footer menu',
) );
ppOptionHeader( 'Mobile Site Customization', 'mobile' );

echo <<<HTML
<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function($){
		ppOption.valToClass( 'mobile_enable' );
		ppOption.valToClass( 'mobile_masthead_use_desktop_settings' );
		ppOption.uploadReveal( 'mobile_masthead_image' );
	});
</script>
<style type="text/css" media="screen">
	#subgroup-nav li,
	.mobile_enable-false .subgroup,
	.mobile_enable-false #subgroup-nav li {
		display:none !important;
	}
	.mobile_enable-false #subgroup-general,
	.mobile_enable-true #subgroup-nav li {
		display:block !important;
	}
</style>
HTML;

ppBlogHeader::mastheadOptionJs();



/* mobile General */
ppOptionSubgroup( 'general' );
	
	ppStartMultiple( 'Mobile general options' );
		ppO( 'mobile_enable', 'radio|true|use customized mobile site|false|use desktop site on mobile devices', '', 'Mobile-version site enable' );
		ppO( 'mobile_ajax_links_enabled', 'radio|true|enable ajax page loading|false|disable ajax page loading', 'disable to resolve linking problems with certain servers or plugins' );
	ppStopMultiple();

ppEndOptionSubgroup();


/* mobile LOGO */
ppOptionSubgroup( 'logo' );

	ppStartMultiple( 'Mobile Logo Options' );
		$logo = NrHtml::a( ppUtil::customizeURL( 'header', 'logo' ), 'logo' );
		ppO( 'mobile_logo_use_desktop', "radio|true|use same $logo for desktop and mobile|false|set specific mobile logo" );
		ppO( 'mobile_show_logo_on_single', 'checkbox|mobile_show_logo_on_single|true|show logo on all page types' );
	ppStopMultiple();

ppUploadBox::renderLinkedImg( 'mobile_logo', 'Mobile logo' );

ppEndOptionSubgroup();



/* mobile MASTHEAD */
ppOptionSubgroup( 'masthead' );
  
	$mastheadSettings = NrHtml::a( ppUtil::customizeURL( 'header', 'masthead' ), 'masthead images and settings' );
	ppO( 'mobile_masthead_use_desktop_settings', ppUtil::radioParams( array(
		'true' => "use same $mastheadSettings for desktop and mobile",
		'false' => 'set specific mobile masthead settings',
	) ) );
	
	ppBlogHeader::mastheadOptions( 'mobile' );
	
	for ( $i = 1; $i <= pp::num()->maxMastheadImages; $i++ ) {
		ppUploadBox::renderLinkedImg( 'mobile_masthead_image' . $i, "Mobile masthead image $i" );
	}

ppEndOptionSubgroup();



/* mobile Backgrounds */
ppOptionSubgroup( 'bgs' );
 
	ppUploadBox::renderBg( 'mobile_content_bg', 'Content area background' );
	
	ppStartMultiple( 'Line between post excerpts' );
		ppBorderGroup( array( 'key' => 'mobile_excerpt_list_border', 'comment' => 'Appearance of line between post excerpts', 'minwidth' => '0' ) );
	ppStopMultiple();
	
	ppStartMultiple( 'Buttons' );
		ppO( 'mobile_button_bg_color',     'color', 'background color of non-footer buttons' );
		ppO( 'mobile_button_border_color', 'color', 'border color of non-footer buttons' );
	ppStopMultiple();

ppEndOptionSubgroup();



/* mobile FONTS */
ppOptionSubgroup( 'fonts' );

	ppFontGroup( array(
		'key'     => 'mobile',
		'title'   => 'Overall font appearance',
		'inherit' => 'none',
		'not'     => array( 'size', 'weight', 'style', 'transform' ),
	) );
	
	ppFontGroup( array(
		'key'     => 'mobile_link',
		'title'   => 'Overall link appearance',
		'inherit' => 'none',
		'not'     => array( 'size', 'weight', 'style', 'family', 'transform' ),
	) );
	
	ppFontGroup( array(
		'key'     => 'mobile_headline',
		'title'   => 'Overall headline/title appearance',
		'inherit' => 'none',
		'not'     => array( 'size' ),
	) );
  
	ppFontGroup( array(
		'key'     => 'mobile_article_excerpt_title',
		'title'   => 'Post excerpt titles',
		'inherit' => 'all',
		'not'     => array( 'size' ),
	) );
	
	ppFontGroup( array(
		'key'     => 'mobile_article_excerpt_text',
		'title'   => 'Post excerpt text preview',
		'inherit' => 'all',
		'not'     => array( 'size' ),
	) );
	
	ppFontGroup( array(
		'key'     => 'mobile_button',
		'title'   => 'Button text',
		'inherit' => array( 'family', 'weight', 'style', 'transform' ),
		'not'     => array( 'size' ),
	) );
	
	ppFontGroup( array(
		'key'     => 'mobile_article_title',
		'title'   => 'Post title',
		'inherit' => 'all',
	) );
	
	ppFontGroup( array(
		'key'     => 'mobile_article_meta_below_title_link',
		'title'   => 'Date/categories info below post title',
		'inherit' => 'all',
		'add'     => array( 'nonlink_color' ),
	) );
	
	ppFontGroup( array(
		'key'     => 'mobile_article_text_link',
		'title'   => 'Post text',
		'inherit' => 'all',
		'add'     => array( 'nonlink_color', 'lineheight' ),
	) );
	
	ppFontGroup( array(
		'key'     => 'mobile_comments_area_link',
		'title'   => 'Comments &amp; comment form area',
		'inherit' => 'all',
		'not'     => array( 'size', 'weight', 'style', 'transform' ),
		'add'     => array( 'nonlink_color' ),
	) );

ppEndOptionSubgroup();





/* mobile COMMENTS */
ppOptionSubgroup( 'comments' );

	ppO( 'mobile_comments_area_bg_color',  'color|optional', 'background color of entire comment area', 'Comment area background color' );
	
	ppStartMultiple( 'Individual comment headers colors' );
		ppO( 'mobile_comment_header_bg_color',   'color',          '<b>background</b> color of individual comment headers' );
		ppO( 'mobile_comment_header_font_color', 'color|optional', '<b>text</b> color of individual comment headers' );
	ppStopMultiple();
	
	ppStartMultiple( 'Individual comment area colors' );
		ppO( 'mobile_comment_bg_color',   'color|optional', '<b>background</b> color of individual comments' );
		ppO( 'mobile_comment_font_color', 'color|optional', '<b>text</b> color of individual comments' );
	ppStopMultiple();

	ppStartMultiple( 'Add comment form colors' );
		ppO( 'mobile_comment_inputs_bg_color',   'color',          '<b>background</b> color of add-comment form input areas' );
		ppO( 'mobile_comment_inputs_font_color', 'color|optional', '<b>text</b> color of add-comment form input areas' );
	ppStopMultiple();
 
	ppStartMultiple( 'Post comment submit button colors' );
		ppO( 'mobile_post_comment_btn_bg_color',     'color|optional', 'background color' );
		ppO( 'mobile_post_comment_btn_border_color', 'color|optional', 'border color' );
		ppO( 'mobile_post_comment_btn_font_color',   'color|optional', 'font color' );
	ppStopMultiple();

ppEndOptionSubgroup();




/* mobile FOOTER NAV */
ppOptionSubgroup( 'footer_nav' );
 
	ppO( 'mobile_footer_color_scheme', ppUtil::radioParams( array(
		'black'  => 'black',
		'blue'   => 'blue',
		'white'  => 'white',
		'gray'   => 'light gray',
		'yellow' => 'yellow',
	) ) );
	ppO( 'mobile_footer_menu_items', 'function|ppMenuAdmin::markup|mobile_nav_menu', '', 'Mobile footer menu items' );

ppEndOptionSubgroup();



