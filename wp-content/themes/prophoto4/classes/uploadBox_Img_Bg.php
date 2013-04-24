<?php


class ppUploadBox_Img_Bg extends ppUploadBox_Img {


	protected $uploadBtnLabel  = 'Upload Background Image';
	protected $replaceBtnLabel = 'Replace Background Image';
	protected $deleteBtnLabel  = 'Delete Background Image';


	public function maxImgDisplayWidth() {
		return 394;
	}


	public function leftTopOptions() {
		$bgColor = new ppOptionBox( $this->id() . '_color', 'color|optional', 'background color', '' );
		return $bgColor->option_markup;
	}


	public function aboveOptions() {
		if ( $this->id() == 'body_bg' ) {
			return;
		}

		$repeat = new ppOptionBox(
			$this->id() . '_img_repeat',
			'select|repeat|tile|repeat-y|tile only vertically|repeat-x|tile only horizontally|no-repeat|do not tile',
			'background image tiling'
		);
		$aboveOptionsMarkup = $repeat->option_markup;

		$position = new ppOptionBox(
			$this->id() . '_img_position',
			'select|top left|top left|top center|top center|top right|top right|center left|center left|center center|
			 center center|center right|center right|bottom left|bottom left|bottom center|bottom center|bottom right|bottom right',
			'background image starting position'
		);
		$aboveOptionsMarkup .= $position->option_markup;

		if ( $this->id() == 'blog_bg' || $this->id() == 'comments_body_area_bg' || NrUtil::startsWith( $this->id(), 'extra_bg_img_' ) ) {
			$attachment = new ppOptionBox(
				$this->id() . '_img_attachment',
				'radio|scroll|scroll with page (normal)|fixed|stay fixed in place',
				'background image scrolling behavior'
			);
			$aboveOptionsMarkup .= $attachment->option_markup;
		}

		return NrHtml::div( $aboveOptionsMarkup, 'class=above-options bg-img-options sc' );
	}


	protected function setup() {
		$this->classes[] = 'bg-option-group';
	}

}

