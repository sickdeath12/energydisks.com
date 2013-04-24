<?php

$data = array();
$data['opts']['holdTime']       = ppOpt::id( 'masthead_slideshow_hold_time', 'microseconds' );
$data['opts']['transitionTime'] = ppOpt::id( 'masthead_slideshow_transition_time', 'microseconds' );
$data['opts']['imageOrder']     = ppOpt::id( 'masthead_slideshow_image_order' );
$data['opts']['loopImages']     = ppOpt::id( 'masthead_slideshow_loop_images', 'bool' );
$data['opts']['transitionType'] = ppOpt::id( 'masthead_slideshow_transition_type' );

if ( ppOpt::test( 'mobile_masthead_use_desktop_settings', 'true' ) ) {
	$customMobileSettings = false;
	$data['mobile_opts']  = false;
} else {
	$customMobileSettings = true;
	$data['mobile_opts']['holdTime']       = ppOpt::id( 'mobile_masthead_slideshow_hold_time', 'microseconds' );
	$data['mobile_opts']['transitionTime'] = ppOpt::id( 'mobile_masthead_slideshow_transition_time', 'microseconds' );
	$data['mobile_opts']['imageOrder']     = ppOpt::id( 'mobile_masthead_slideshow_image_order' );
	$data['mobile_opts']['loopImages']     = ppOpt::id( 'mobile_masthead_slideshow_loop_images', 'bool' );
	$data['mobile_opts']['transitionType'] = ppOpt::id( 'mobile_masthead_slideshow_transition_type' );
}

$dims = ppBlogHeader::mastheadDims( null, $considerRequestingBrowser = false );
$data['slideshowWidth']    = $dims->width;
$data['slideshowHeight']   = $dims->height;
$data['viewingAreaWidth']  = $dims->width;
$data['viewingAreaHeight'] = $dims->height;

$mobilePortraitDims = ppBlogHeader::mastheadDims( 320 );
$data['mobile_portrait_dims']['mobileSlideshowWidth']  = $mobilePortraitDims->width;
$data['mobile_portrait_dims']['mobileSlideshowHeight'] = $mobilePortraitDims->height;


$mobileLandscapeDims = ppBlogHeader::mastheadDims( ppMobileHtml::STANDARD_MOBILE_DEVICE_MAX_WIDTH );
$data['mobile_landscape_dims']['mobileSlideshowWidth']    = $mobileLandscapeDims->width;
$data['mobile_landscape_dims']['mobileSlideshowHeight']   = $mobileLandscapeDims->height;

$mobileImgDims = ppBlogHeader::mastheadDims( ppMobileHtml::STANDARD_MOBILE_DEVICE_MAX_WIDTH );
$retinaImgDims = ppBlogHeader::mastheadDims( 960 );

for ( $counter = 1; $counter <= pp::num()->maxMastheadImages; $counter++ ) {
	
	
	if ( ppImg::id( $imgName = "masthead_image{$counter}" )->exists ) {

		$constrainedImg = ppGdModify::constrainImgSize( ppImg::id( $imgName )->imgTag(), $dims->width, $dims->height );
		$data['imgs'][] = array(
			'fullsizeSrc' => $constrainedImg->src(),
			'linkToUrl' => ppOpt::test( $imgName . '_linkurl' ) ? ppUtil::userUrl( $imgName . '_linkurl' ) : false,
		);
		
		if ( !$customMobileSettings ) {
			$mobileConstrainedImg = ppGdModify::constrainImgSize( ppImg::id( $imgName )->imgTag(), $mobileImgDims->width, $mobileImgDims->height );
			$data['mobile_imgs'][] = array(
				'fullsizeSrc' => $mobileConstrainedImg->src(),
				'linkToUrl' => ppOpt::test( $imgName . '_linkurl' ) ? ppUtil::userUrl( $imgName . '_linkurl' ) : false,
			);

			$retinaConstrainedImg = ppGdModify::constrainImgSize( ppImg::id( $imgName )->imgTag(), $retinaImgDims->width, $retinaImgDims->height );
			$data['retina_imgs'][] = array(
				'fullsizeSrc' => $retinaConstrainedImg->src(),
				'linkToUrl' => ppOpt::test( $imgName . '_linkurl' ) ? ppUtil::userUrl( $imgName . '_linkurl' ) : false,
			);
		}		
	}
	
	if ( $customMobileSettings && ppImg::id( $mobileImgName = "mobile_masthead_image{$counter}" )->exists ) {
		
		$mobileConstrainedImg = ppGdModify::constrainImgSize( ppImg::id( $mobileImgName )->imgTag(), $mobileImgDims->width, $mobileImgDims->height );
		$data['mobile_imgs'][] = array(
			'fullsizeSrc' => $mobileConstrainedImg->src(),
			'linkToUrl' => ppOpt::test( $mobileImgName . '_linkurl' ) ? ppUtil::userUrl( $mobileImgName . '_linkurl' ) : false,
		);

		$retinaConstrainedImg = ppGdModify::constrainImgSize( ppImg::id( $mobileImgName )->imgTag(), $retinaImgDims->width, $retinaImgDims->height );
		$data['retina_imgs'][] = array(
			'fullsizeSrc' => $retinaConstrainedImg->src(),
			'linkToUrl' => ppOpt::test( $mobileImgName . '_linkurl' ) ? ppUtil::userUrl( $mobileImgName . '_linkurl' ) : false,
		);
	}
}


$output = stripslashes( json_encode( apply_filters( 'pp_masthead_json_data', $data ) ) );

return apply_filters( 'pp_masthead_json_output', $output ); 

