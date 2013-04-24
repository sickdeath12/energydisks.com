<?php


class ppUploadBox_Img_Bg_Extra extends ppUploadBox_Img_Bg {


	public function leftTopOptions() {
		$cssSelector = new ppOptionBox( $this->id() . '_css_selector', 'text|20', 'CSS selector', '' );
		return $cssSelector->option_markup;
	}

}
