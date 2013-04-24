<?php
/* ------------------------------------------------ */
/* --- global flash gallery settings generation --- */
/* ------------------------------------------------ */

$global_data = array();
$global_data['thumbSize']              = ppOpt::id( 'slideshow_thumb_size', 'int' );
$global_data['thumbBorderWidth']       = ppOpt::id( 'slideshow_thumb_border_width', 'int' );
$global_data['thumbBorderColor']       = ppOpt::id( 'slideshow_thumb_border_color' );
$global_data['thumbPadding']           = ppOpt::id( 'slideshow_thumb_padding', 'int' );
$global_data['splashScreenHeight']     = ppOpt::id( 'slideshow_splash_screen_height', '%' );
$global_data['splashScreenPosition']   = ppOpt::id( 'slideshow_splash_screen_position' );
$global_data['disableFullScreen']      = ppOpt::id( 'slideshow_disable_full_screen', 'bool' );
$global_data['btnsSrcs']               = ppSlideshowGallery::btnsSrcs();
$global_data['startPlaying']           = ppOpt::id( 'slideshow_start_playing', 'bool' );
$global_data['showTimer']              = ppOpt::id( 'slideshow_show_timer', 'bool' );
$global_data['controlsOverlaid']       = ppOpt::id( 'slideshow_controls_overlaid', 'bool' );
$global_data['controlsAutoHide']       = ppOpt::id( 'slideshow_controls_autohide', 'bool' );
$global_data['controlsAutoHideTime']   = ppOpt::id( 'slideshow_controls_autohide_time', 'microseconds' );
$global_data['controlsPosition']       = ppOpt::id( 'slideshow_controls_position' );

$global_data['opts']['holdTime']       = ppOpt::id( 'slideshow_hold_time', 'float' );
$global_data['opts']['transitionTime'] = ppOpt::id( 'slideshow_transition_time', 'microseconds' );
$global_data['opts']['loopImages']     = ppOpt::id( 'slideshow_loop_show', 'bool' );
$global_data['opts']['transitionType'] = ppOpt::id( 'slideshow_transition_type' );
$global_data['opts']['bgColor']        = ppOpt::id( 'slideshow_bg_color' );


@list( $easing, $speed ) = explode( '@', ppOpt::id( 'slideshow_thumb_paging_animation' ) );
$global_data['thumbsPagingAnimation'] = array(
	'easing' => $easing,
	'speed' => intval( $speed ),
);

$splashScreenLogo = ppImg::id( 'slideshow_splash_screen_logo' );
if ( $splashScreenLogo->exists ) {
	$global_data['splashScreenLogo'] = array(
		'src'      => $splashScreenLogo->url,
		'width'    => $splashScreenLogo->width,
		'height'   => $splashScreenLogo->height,
		'htmlAttr' => $splashScreenLogo->htmlAttr,
	);
}

return json_encode( apply_filters( 'p4_galleryjson_output', $global_data ) );




