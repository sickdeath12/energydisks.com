<?php
/* -------------------------------- */
/* -- gallery options input page -- */
/* -------------------------------- */

// tabs and header
ppSubgroupTabs( array(
	'slideshow' => 'Slideshow Galleries',
	'lightbox'  => 'Lightbox Overlay Galleries',
	'audio'     => 'Audio files'
) );
ppOptionHeader( 'Galleries Options', 'galleries' );



/* gallery slideshow subgroup */
ppOptionSubgroup( 'slideshow' );


// general options
ppStartMultiple( 'General options' );
ppO( 'slideshow_bg_color', 'color', 'background color of slideshow player' );
ppO( 'slideshow_btns_color', 'color', 'color of control buttons' );
ppO( 'slideshow_disable_full_screen', 'radio|false|allow slideshow to fullscreen|true|do not allow fullscreen' );
ppStopMultiple();


// overlay options
ppStartMultiple( 'Initial overlay options' );
ppO( 'slideshow_splash_screen_height', 'slider', 'height of initial overlay as a percentage of the overall slideshow height' );
ppO( 'slideshow_splash_screen_position', 'radio|top|top of gallery|middle|middle of gallery|bottom|bottom of gallery', 'vertical position of initial overlay' );
ppO( 'slideshow_splash_screen_opacity', 'slider', 'opacity of initial overlay background color' );
ppO( 'slideshow_splash_screen_color', 'color|optional', 'background color of initial overlay' );
ppStopMultiple();


// overlay main-title font
ppFontGroup( array(
	'title' => 'Initial overlay main title text',
	'key' => 'slideshow_title',
	'not' => array( 'weight', 'style', 'transform' ),
) );

// overlay sub-title font
ppFontGroup( array(
	'title' => 'Initial overlay sub-title text',
	'key' => 'slideshow_subtitle',
	'inherit' => 'all',
	'not' => array( 'weight', 'style', 'transform' ),
) );



// logo
ppUploadBox::renderImg( 'slideshow_splash_screen_logo', 'Initial overlay logo' );


// slideshow options
ppStartMultiple( 'Slideshow options' );
ppO( 'slideshow_start_playing', 'radio|true|slideshow plays when overlay clicked|false|slideshow paused after overlay clicked', 'after user clicks to see the slideshow, should it start playing immediately or do they start by browsing static images, with the slideshow paused' );
ppO( 'slideshow_loop_show', 'radio|true|loop slideshow|false|do not loop slideshow', 'what to do when slideshow finishes' );
ppO( 'slideshow_transition_type', 'radio|fade|fade out, fade in|crossfade|cross-fade|slide|slide horizontally|topslide|slide vertically', 'transition effect style between images' );
ppO( 'slideshow_show_timer', 'radio|false|do not show timer|true|show timer', 'show/hide the animated timer bar during slideshow playback' );
ppO( 'slideshow_hold_time', 'slider|0.1|7.0| seconds|0.1', 'time each slide is shown during slideshow' );
ppO( 'slideshow_transition_time', 'slider|0.1|4.0| seconds|0.1', 'time of slideshow transition effect' );
ppStopMultiple();


// filmstrip location
ppStartMultiple( 'Thumbnail filmstrip &amp controls location' );
ppO( 'slideshow_controls_position', 'radio|top|top|bottom|bottom|right|right|left|left', 'position of filmstrip/controls area' );
ppO( 'slideshow_controls_overlaid', 'radio|true|overlay|false|separate', 'show filmstrip/controls overlaid on top of main images, or separately' );
ppO( 'slideshow_controls_autohide', 'radio|true|yes, show and hide|false|no, always show', 'filmstrip/controls area shows and hides based on user mouse movement' );
ppO( 'slideshow_controls_autohide_time', 'slider|0.6|4| seconds|0.2', 'how long (in seconds) until filmstrip/controls auto-hides after user\'s last mouse movement' );
ppStopMultiple();


// thumbstrip appearance
ppStartMultiple( 'Thumbnail filmstrip &amp controls appearance' );
ppO( 'slideshow_thumb_paging_animation', 'radio|swing@900|linear|easeOutExpo@900|swing|easeOutBounce@1000|bounce|easeOutElastic@2000|elastic', 'thumbstrip paging animation effect' );
ppO( 'slideshow_controls_bg_color', 'color|optional', 'background color of filmstrip/controls area' );
ppO( 'slideshow_btns_opacity', 'slider', 'opacity of controls buttons (play/pause, fullscreen, music, shopping cart)' );
ppO( 'slideshow_controls_bg_opacity', 'slider', 'opacity of filmstrip/controls area background' );
ppStopMultiple();

// thumbnail images
ppStartMultiple( 'Thumbnail images' );
ppO( 'slideshow_thumb_size', 'slider|10|' . pp::wp()->imgThumbWidth . '| pixels', 'size of thumbnail images' );
ppO( 'slideshow_thumb_padding', 'slider|0|60| pixels', 'spacing between thumbnail images' );
ppO( 'slideshow_thumb_border_width', 'slider|0|10| pixels', 'width of border around thumbnail images' );
ppO( 'slideshow_thumb_border_color', 'color', 'color of border around thumbnail images when not active' );
ppO( 'slideshow_active_thumb_border_color', 'color', 'color of border around thumbnail images when active' );
ppO( 'slideshow_blank1', 'blank' );
ppO( 'slideshow_thumb_opacity', 'slider|25|100| percent', 'opacity of thumbnail images when not active or hovered over' );
ppO( 'slideshow_active_thumb_opacity', 'slider|25|100| percent', 'opacity of thumbnail images when active or hovered over' );

ppStopMultiple();

// mp3 options
ppStartMultiple( 'Audio playback' );
ppO( 'slideshow_mp3_autostart', 'radio|true|autostart|false|click to play', 'slideshow audio starts automatically upon play, or requires additional click' );
ppO( 'slideshow_mp3_loop', 'radio|true|loops|false|does not loop', 'slideshow audio should loop after ending, or just end and not loop' );
ppStopMultiple();

ppEndOptionSubgroup();




/* javascript lightbox subgroup */
ppOptionSubgroup( 'lightbox' );

// thumbnails
ppStartMultiple( 'Lightbox gallery thumbnails' );
ppO( 'lightbox_thumb_default_size', 'slider|30|200| px', 'requested size of Lightbox thumbnails - actual display sizes will vary slightly to create neatly-lined up rows' );
ppO( 'lightbox_thumb_opacity', 'slider', 'opacity of thumbnails when not hovered over' );
ppO( 'lightbox_thumb_mouseover_speed', 'slider|0|1500| milliseconds|50', 'time it takes for thumbnail to fade to full opacity when hovered over' );
ppO( 'lightbox_thumb_mouseout_speed', 'slider|0|1500| milliseconds|50', 'time it takes for thumbnail to fade back to partial opacity after mouse stops hovering' );
ppO( 'lightbox_thumb_margin', 'slider|0|60| px', 'spacing between thumbnails' );
ppO( 'lightbox_centering', 'checkbox|lightbox_main_img_center|true|center main image|lightbox_thumbs_center|true|center thumbnails', 'horizontal centering of gallery images' );
ppStopMultiple();


// general options
ppStartMultiple( 'Lightbox overlay general appearance' );
ppO( 'lightbox_border_width', 'slider|0|50| px', 'width of border around lightbox overlay image' );
ppO( 'lightbox_bg_color', 'color', 'color of border/background around lightbox images' );
ppStopMultiple();

// image info text
ppFontGroup( array(
	'title' => 'Image info text appearance',
	'key' => 'lightbox',
	'not' => array( 'weight', 'style', 'transform' ),
) );

// overlay
ppStartMultiple( 'Lightbox overlay effects' );
ppO( 'lightbox_overlay_color', 'color', 'color of background faded area when image is clicked' );
ppO( 'lightbox_overlay_opacity', 'slider', 'opacity of background overlay faded area' );
ppO( 'lightbox_resize_speed', 'slider|0|1500| milliseconds|50', 'box resize speed between images' );
ppO( 'lightbox_image_fadespeed', 'slider|0|1500| milliseconds|50', 'main image fade in/fade out speed' );
ppStopMultiple();


// image navigation
ppStartMultiple( 'Lightbox overlay image navigation' );
ppO( 'lightbox_fixed_navigation', 'radio|true|always show "prev/next" buttons|false|show only when mousing over image' );
ppO( 'lightbox_nav_btns_opacity', 'slider', 'opacity of prev/next image navigation buttons' );
ppO( 'lightbox_nav_btns_fadespeed', 'slider|0|1500| milliseconds|50', 'speed of fade in/fade out of prev/next buttons when image is moused over' );
ppStopMultiple();


// lightbox images
ppUploadBox::renderImg('lightbox_loading', 'Lightbox overlay loading image' );
ppUploadBox::renderImg('lightbox_close', 'Lightbox overlay close image button' );
ppUploadBox::renderImg('lightbox_next', 'Lightbox overlay next image button' );
ppUploadBox::renderImg('lightbox_prev', 'Lightbox overlay previous image button' );

ppEndOptionSubgroup();


echo <<<HTML
<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function($){
		ppOption.uploadReveal( 'audio' );
	});
</script>
HTML;
ppOptionSubgroup( 'audio' );

for ( $i = 1; $i <= pp::num()->maxAudioUploads; $i++ ) {
	$audioUploadBox = new ppUploadBox_Audio( $i );
	$audioUploadBox->render();
}


ppEndOptionSubgroup();
