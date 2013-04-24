<?php

/* create sprited gallery buttons image */
class Gallery_Sprite extends GD_Img {

	function __construct( $btn_color, $btn_size ) {
		$this->btn_size  = $btn_size;
		$this->set_width();
		$this->height    = $this->btn_size;
		$this->type      = 'png';
		$this->half_btn  = $this->btn_size / 2;
		$this->create();
		$this->bg_hex    = ( $btn_color == '#000000' ) ? '#fff' : '#000';
		$this->btn_hex   = $btn_color;
		$this->bg_color  = $this->colorAllocate( $this->bg_hex );
		$this->btn_color = $this->colorAllocate( $this->btn_hex );
		$this->fillBg();
		$this->draw_buttons();
		$this->makeTransparent( $this->bg_color );
	}

	function up_down_arrows( $x_offset = 0 ) {
		$temp_arrow = new Gallery_Arrow( $this->btn_hex, $this->btn_size );
		$temp_arrow->img = imagerotate( $temp_arrow->img, 90, 0 );
		imagecopymerge( $this->img, $temp_arrow->img, $x_offset, 0, 0, 0, $temp_arrow->width, $temp_arrow->height, 100 );
		$temp_arrow->img = imagerotate( $temp_arrow->img, 180, 0 );
		imagecopymerge( $this->img, $temp_arrow->img, $x_offset + $this->btn_size, 0, 0, 0, $temp_arrow->width, $temp_arrow->height, 100 );
		$temp_arrow->destroy();
	}

	function draw_buttons() {

		// play btn
		$this->btn_containing_circle();
		$this->arrow();

		// // pause btn
		$this->btn_containing_circle( $this->btn_size );
		$this->pause_btn_inner( $this->btn_size );

		// fullscreen btn
		$this->btn_containing_circle( $this->btn_size * 2 );
		$this->fullscreen_btn_inner( $this->btn_size * 2, 'fullscreen' );

		// un-fullscreen btn
		$this->btn_containing_circle( $this->btn_size * 3 );
		$this->fullscreen_btn_inner( $this->btn_size * 3, 'unfullscreen' );

		// paging arrows
		$this->arrow( $this->btn_size * 4, 'right', 'large' );
		$this->arrow( $this->btn_size * 5, 'left', 'large' );

		// shopping cart
		$this->shopping_cart_btn( $this->btn_size * 6 );

		// mp3 buttons
		$this->mp3_playing_btn( $this->btn_size * 7 );
		$this->mp3_paused_btn( $this->btn_size * 8 );

		// up and down arrows for vert thumbnails
		$this->up_down_arrows( $this->btn_size * 9 );

	}


	function set_width() {
		$this->width = $this->btn_size * 11;
	}


	function mp3_paused_btn( $x_offset ) {
		$this->set_brush( $this->btn_size * 0.07 );
		$this->mp3_speaker( $x_offset );
		$this->brush->destroy();
	}


	function mp3_playing_btn( $x_offset = 0 ) {
		$this->set_brush( $this->btn_size * 0.07 );
		$this->mp3_speaker( $x_offset );
		$this->mp3_sound_waves( $x_offset );
		$this->brush->destroy();
	}


	function mp3_sound_waves( $x_offset = 0 ) {
		imagearc(
			$this->img,
			$x_offset + $this->btn_size * 0.39,
			$this->half_btn,
			$this->btn_size * 0.4,
			$this->btn_size * 0.45,
			-45,
			45,
			IMG_COLOR_BRUSHED
		);
		imagearc(
			$this->img,
			$x_offset + $this->btn_size * 0.45,
			$this->half_btn,
			$this->btn_size * 0.65,
			$this->btn_size * 0.8,
			-45,
			45,
			IMG_COLOR_BRUSHED
		);
		imagearc(
			$this->img,
			$x_offset + $this->btn_size * 0.65,
			$this->half_btn,
			$this->btn_size * 0.60,
			$this->btn_size * 1.0,
			-53,
			53,
			IMG_COLOR_BRUSHED
		);
	}


	function mp3_speaker( $x_offset = 0 ) {
		$spkr_back_height = $this->btn_size * 0.18;
		$spkr_back_width  = $this->btn_size * 0.13;
		$spkr_from_left   = $x_offset + $this->btn_size * 0.05;
		$spkr_cone_join_x = $x_offset + $this->btn_size * 0.32;
		$spkr_cone_join_y = 0.82;
		$this->brushedLine(
			$spkr_from_left + $spkr_back_width, $this->half_btn - $spkr_back_height / 2,
			$spkr_from_left, $this->half_btn - $spkr_back_height / 2
		);
		$this->brushedLine(
			$spkr_from_left, $this->half_btn - $spkr_back_height / 2,
			$spkr_from_left, $this->half_btn + $spkr_back_height / 2
		);
		$this->brushedLine(
			$spkr_from_left, $this->half_btn + $spkr_back_height / 2,
			$spkr_from_left + $spkr_back_width, $this->half_btn + $spkr_back_height / 2
		);
		$this->brushedLine(
			$spkr_from_left + $spkr_back_width, $this->half_btn + $spkr_back_height / 2,
			$spkr_cone_join_x, $this->btn_size * $spkr_cone_join_y
		);
		$this->brushedLine(
			$spkr_from_left + $spkr_back_width, $this->half_btn - $spkr_back_height / 2,
			$spkr_cone_join_x, $this->btn_size * ( 1 - $spkr_cone_join_y )
		);
		imagearc(
			$this->img,
			$x_offset + $this->btn_size * 0.11,
			$this->half_btn,
			$this->btn_size * 0.6,
			$this->btn_size * 0.9,
			-45,
			45,
			IMG_COLOR_BRUSHED
		);
	}


	function set_brush( $size ) {
		$this->brush = new GD_Img( $size );
		$this->brush->create();
		$this->brush->bg_color = $this->brush->colorAllocate( $this->bg_hex );
		$this->brush->color = $this->brush->colorAllocate( $this->btn_hex );
		$this->brush->fillBg();
		imagefilledellipse(
			$this->brush->img,
			$this->brush->height / 2,
			$this->brush->height / 2,
			$this->brush->height * 0.9,
			$this->brush->height * 0.9,
			$this->brush->color
		);
		$this->brush->makeTransparent( $this->brush->bg_color );
		imagesetbrush( $this->img, $this->brush->img );
	}


	function shopping_cart_btn( $x_offset = 0 ) {

		// left wheel
		imagefilledellipse(
			$this->img,
			$x_offset + $this->btn_size * 0.3,
			$this->btn_size * 0.865,
			$this->btn_size * 0.15,
			$this->btn_size * 0.15,
			$this->btn_color
		);

		// right wheel
		imagefilledellipse(
			$this->img,
			$x_offset + $this->btn_size * 0.81,
			$this->btn_size * 0.865,
			$this->btn_size * 0.15,
			$this->btn_size * 0.15,
			$this->btn_color
		);

		// polygon
		$width        = $this->btn_size * 0.475;
		$height       = $this->btn_size * 0.15;
		$top_from_top = $this->btn_size * 0.52;
		$btm_from_top = $top_from_top + $height;
		$from_left    = $x_offset + $this->btn_size * 0.28;
		$tilt         = $this->btn_size * 0.055;
		imagefilledpolygon( $this->img, array(
			$from_left, $btm_from_top,
			$from_left + $tilt, $top_from_top,
			$from_left + $tilt + $width, $top_from_top,
			$from_left + $width, $btm_from_top,
		), 4, $this->btn_color );

		// outline
		$this->set_brush( $this->btn_size * 0.06 );
		$top_from_top         = $this->btn_size * 0.2;
		$height               = $this->btn_size * 0.53;
		$width                = $this->btn_size * 0.68;
		$back_bump            = $this->btn_size * 0.06;
		$front_concave_amt    = $this->btn_size * 0.013;
		$front_concave_height = $this->btn_size * 0.15;
		$front_tilt           = $this->btn_size * 0.075;
		$btm_left_from_left   = $x_offset + $this->btn_size * 0.21;
		$handle_height        = $this->btn_size * 0.08;
		$handle_offset        = $this->btn_size * 0.032;
		$handle_width         = $this->btn_size * 0.097;
		$back_bump_offset     = $this->btn_size * 0.037;
		$back_bump_height     = $this->btn_size * 0.065;
		$back_flat_height     = $this->btn_size * 0.34;
		imagepolygon( $this->img, array(
			$btm_left_from_left, $top_from_top + $height - 2 * $back_bump_height - $back_flat_height,
			$btm_left_from_left, $top_from_top + $height - 2 * $back_bump_height,
			$btm_left_from_left - $back_bump_offset, $top_from_top + $height - $back_bump_height,
			$btm_left_from_left, $top_from_top + $height, // *start* //
			$btm_left_from_left + $width, $top_from_top + $height,
			$btm_left_from_left + $width - $front_concave_amt, $top_from_top + $height - $front_concave_height,
			$btm_left_from_left + $width - $front_concave_amt + $front_tilt, $top_from_top,
			$btm_left_from_left - $front_concave_amt + $front_tilt, $top_from_top,
			$btm_left_from_left - $handle_offset, $top_from_top - $handle_height,
			$btm_left_from_left - $handle_offset - $handle_width, $top_from_top - $handle_height,
			$btm_left_from_left - $handle_offset - $handle_width / 3, $top_from_top - $handle_height / 4,
		), 11, IMG_COLOR_BRUSHED );
	}


	function fullscreen_btn_inner( $x_offset = 0, $action = 'fullscreen' ) {
		imagefilledrectangle(
			$this->img,
			$x_offset + $this->btn_size * 0.4,
			$this->btn_size * 0.4,
			$x_offset + $this->btn_size * 0.6,
			$this->btn_size * 0.6,
			$this->btn_color
		);

		$distance_a    = $this->btn_size * 0.275;
		$distance_b    = $this->btn_size * 0.398;
		$distance_c    = $this->btn_size * 0.6059;
		$distance_d    = $this->btn_size * 0.73;
		$triangle_size = $this->btn_size * 0.345;
		$fullscreen    = ( $action === 'fullscreen' );

		imagefilledarc(
			$this->img,
			$fullscreen ? $x_offset + $distance_a : $x_offset + $distance_c,
			$fullscreen ? $distance_a : $distance_c,
			$triangle_size,
			$triangle_size,
			0,
			90,
			$this->btn_color,
			IMG_ARC_CHORD
		);
		imagefilledarc(
			$this->img,
			$fullscreen ? $x_offset + $distance_a : $x_offset + $distance_c,
			$fullscreen ? $distance_d : $distance_b,
			$triangle_size,
			$triangle_size,
			0,
			-90,
			$this->btn_color,
			IMG_ARC_CHORD
		);
		imagefilledarc(
			$this->img,
			$fullscreen ? $x_offset + $distance_d : $x_offset + $distance_b,
			$fullscreen ? $distance_a : $distance_c,
			$triangle_size,
			$triangle_size,
			-180,
			-270,
			$this->btn_color,
			IMG_ARC_CHORD
		);
		imagefilledarc(
			$this->img,
			$fullscreen ? $x_offset + $distance_d : $x_offset + $distance_b,
			$fullscreen ? $distance_d : $distance_b,
			$triangle_size,
			$triangle_size,
			-90,
			-180,
			$this->btn_color,
			IMG_ARC_CHORD
		);
	}


	function arrow( $x_offset = 0, $dir = 'right', $size = 'small' ) {

		if ( $size == 'small' ) {
			$arrow_point_left_offset = 0.6625;
			$secondary_arrow_point_offset = $this->btn_size * 0.1775;
			$arrows_height = $this->btn_size * 0.7;
			$inner_arrow_width = $this->btn_size * 0.2;
		} else {
			$arrow_point_left_offset = 0.76;
			$secondary_arrow_point_offset = $this->btn_size * 0.262;
			$arrows_height = $this->btn_size * 1.15;
			$inner_arrow_width = $this->btn_size * 0.41;
		}

		if ( $dir == 'right' ) {
			$main_triangle_start_x = $this->btn_size * $arrow_point_left_offset;
			$angle_offset = 0;
		} else {
			$main_triangle_start_x = $this->btn_size * ( 1 - $arrow_point_left_offset );
			$secondary_arrow_point_offset = -$secondary_arrow_point_offset;
			$angle_offset = 180;
		}

		imagefilledarc(
			$this->img,
			$x_offset + $main_triangle_start_x,
			$this->half_btn,
			$arrows_height,
			$arrows_height,
			135 + $angle_offset,
			225 + $angle_offset,
			$this->btn_color,
			IMG_ARC_CHORD
		);
		imagefilledarc(
			$this->img,
			$x_offset + $main_triangle_start_x - $secondary_arrow_point_offset,
			$this->half_btn,
			$inner_arrow_width,
			$arrows_height,
			135 + $angle_offset,
			225 + $angle_offset,
			// $this->colorAllocate( '#f90' ), /* test color */
			$this->bg_color,
			IMG_ARC_CHORD
		);
	}


	function pause_btn_inner( $x_offset = 0 ) {
		imagefilledrectangle(
			$this->img,
			$x_offset + $this->btn_size * 0.36,
			$this->btn_size * 0.3,
			$x_offset + $this->btn_size * 0.44,
			$this->btn_size * 0.7,
			$this->btn_color
		);
		imagefilledrectangle(
			$this->img,
			$x_offset + $this->btn_size * 0.56,
			$this->btn_size * 0.3,
			$x_offset + $this->btn_size * 0.64,
			$this->btn_size * 0.7,
			$this->btn_color
		);
	}


	function btn_containing_circle( $x_offset = 0 ) {
		imagefilledellipse(
			$this->img,
			$x_offset + $this->half_btn,
			$this->half_btn,
			$this->btn_size * 0.95,
			$this->btn_size * 0.95,
			$this->btn_color
		);
		imagefilledellipse(
			$this->img,
			$x_offset + $this->half_btn,
			$this->half_btn,
			$this->btn_size * 0.80,
			$this->btn_size * 0.80,
			$this->bg_color
		);
	}


	function debug() {
		$debugPath = pp::fileInfo()->wpUploadPath . '/debug_gallery_sprite.png';
		$debugUrl  = ppUtil::urlFromPath( $debugPath );
		NrDump::it( $debugUrl );
		$this->writeToFile( $debugPath )->destroy();
		echo "<img src='$debugUrl' />";
	}
}


class Gallery_Start_Btn extends Gallery_Sprite {

	function set_width() {
		$this->width = $this->btn_size;
	}

	function draw_buttons() {
		$this->btn_containing_circle();
		$this->arrow();
	}
}


class Gallery_Arrow extends Gallery_Start_Btn {
	function draw_buttons() {
		$this->arrow( 0, 'right', 'large ' );
	}
}



?>