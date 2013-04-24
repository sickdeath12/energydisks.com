<?php

$configArray = array(

	'meta' => array(
		'name' => 'ProPhoto2',
		'desc' => 'Originally the default design, the ProPhoto2 template provides you with the basics.   Its slate grey and concrete palette keeps things simple and classy.  The up-front bio area makes sure your customers get a feel for who you are right from the beginning.  Classic navigation and dominant logo area fill out the header.',
	),

	'options' => array(
		'bio_bg_img_repeat'                          => 'repeat-x',
		'bio_bg_img_position'                        => 'bottom center',
		'bio_bg_color'                               => '#ffffff',
		'bio_inner_bg_img_repeat'                    => 'no-repeat',
		'bio_inner_bg_img_position'                  => 'top right',
		'masthead_top_border_color'                  => '#000000',
		'masthead_top_border_style'                  => 'solid',
		'masthead_btm_border_color'                  => '#000000',
		'masthead_btm_border_style'                  => 'solid',
		'gen_link_font_color_bind'                   => 'on',
		'comments_area_border_style'                 => 'solid',
		'comments_header_bg_img_repeat'              => 'repeat',
		'comments_header_bg_img_position'            => 'top left',
		'comments_header_post_interact_link_spacing' => '12',
		'comment_timestamp_font_color'               => '#000000',
	),


	'activation_widgets' => array(
		'bio-spanning-col' => array(
			'1' => array(
				'pp-text' => reset( ppUtil::loadConfig( 'default_widgets' ) ),
			),
		),
		'bio-col-1'     => 'empty',
		'bio-col-2'     => 'empty',
		'bio-col-3'     => 'empty',
		'bio-col-4'     => 'empty',
		'fixed-sidebar' => 'empty',
	),


	'imgs' => array(
		'bio_bg'          => 'prophoto2_bio_bg.jpg',
		'bio_inner_bg'    => 'prophoto2_bio_inner_bg.jpg',
		'logo'            => 'prophoto2_logo.jpg',
		'biopic1'         => 'prophoto2_biopic1.jpg',
		'masthead_image1' => 'prophoto2_masthead_image1.jpg',
	),


	'remote_files' => array(

		'prophoto2_bio_bg.jpg' => array(
			'hash'   => 'ba6506fff49b95cccc1c338452c332f6',
			'width'  => '18',
			'height' => '381',
			'size'   => '1',
		),

		'prophoto2_bio_inner_bg.jpg' => array(
			'hash'   => 'b9e89b0b9a59ebf2ec63f533b8cd2dc8',
			'width'  => '75',
			'height' => '77',
			'size'   => '2',
		),

		'prophoto2_bio_inner_bg_alt.jpg' => array(
			'hash'   => 'fb7deaf425045deab56f7da7ebfaa153',
			'width'  => '60',
			'height' => '62',
			'size'   => '1',
		),

		'prophoto2_biopic1.jpg' => array(
			'hash'   => 'eccf75010aeb035bafcdc520998522c2',
			'width'  => '285',
			'height' => '327',
			'size'   => '20',
		),

		'prophoto2_logo.jpg' => array(
			'hash'   => 'd934621ae5e7ba8f53a5d89aec0a073e',
			'width'  => '245',
			'height' => '236',
			'size'   => '6',
		),

		'prophoto2_masthead_image1.jpg' => array(
			'hash'   => '8a07f1544d7748591ffc786b3dbc60a1',
			'width'  => '717',
			'height' => '236',
			'size'   => '31',
		),
	),
);

