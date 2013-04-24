<?php 

$thumbOpacity           = ppOpt::id( 'lightbox_thumb_opacity', '%' );
$thumbMouseoverSpeed    = ppOpt::id( 'lightbox_thumb_mouseover_speed' );
$lightboxLoadingImgUrl  = ppImg::id( 'lightbox_loading' )->url;
$lightboxPrevBtnImgUrl  = ppImg::id( 'lightbox_prev' )->url;
$lightboxNextBtnImgUrl  = ppImg::id( 'lightbox_next' )->url;
$lightboxCloseBtnImgUrl = ppImg::id( 'lightbox_close' )->url;
$lightboxBlankBtnImgUrl = pp::site()->themeUrl . '/images/blank.gif';

$jsCode .= <<<JAVASCRIPT

var ppLightBoxInfo = {
	img_loading      : '$lightboxLoadingImgUrl',
	img_btn_prev     : '$lightboxPrevBtnImgUrl',
	img_btn_next     : '$lightboxNextBtnImgUrl',
	img_btn_close    : '$lightboxCloseBtnImgUrl',
	img_blank        : '$lightboxBlankBtnImgUrl',
	img_fadespeed    : ppOpt::id( 'lightbox_image_fadespeed' ),
	border_width     : 'ppOpt::id( 'lightbox_border_width' )',
	resize_speed     : ppOpt::id( 'lightbox_resize_speed'),
	fixed_navigation : ppOpt::id( 'lightbox_fixed_navigation' ),
	btns_opacity     : ppOpt::id( 'lightbox_nav_btns_opacity', '%' ),
	btn_fadespeed    : ppOpt::id( 'lightbox_nav_btns_fadespeed' ),
	overlay_color    : 'ppOpt::id( 'lightbox_overlay_color' )',
	overlay_opacity  : ppOpt::id( 'lightbox_overlay_opacity', '%' ),
	translate_image  : 'ppOpt::id( 'translate_lightbox_image' )',
	translate_of     : 'ppOpt::id( 'translate_lightbox_of' )'
};


ppLightboxGallery = function(context) {

	$('.pp-lightbox-not-loaded',context).each(function(){

		$('a',$(this)).prophotoLightbox(ppLightBoxInfo);
		
		$('.pp-lightbox-thumbs a',$(this)).css('opacity', $thumbOpacity).hover(function(){
			$(this).stop().animate({opacity:1},{$thumbMouseoverSpeed});
		}, function(){
			$(this).stop().animate({opacity:$thumbOpacity},{$thumbMouseoverSpeed});
		});
		
		$(this).removeClass('pp-lightbox-not-loaded');
	});
	
};

JAVASCRIPT;


