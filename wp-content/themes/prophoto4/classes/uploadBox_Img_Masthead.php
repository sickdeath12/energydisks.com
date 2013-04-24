<?php


class ppUploadBox_Img_Masthead extends ppUploadBox_Img {


	protected $recommendedHeight;
	protected $recommendedWidth;
	protected $sizingExplanation;
	protected $classes = array( 'masthead-upload-box' );


	public function sizingBox() {
		if ( $this->isMobile() ) {
			$this->setupMobile();
		} else {
			$this->setupDesktop();
		}
		$this->renderView( 'upload_box_sizing' );
	}


	public function sizingExplanation() {
		return $this->sizingExplanation;
	}


	public function leftBtmOptions() {
		$linkUrl = new ppOptionBox( $this->id() . '_linkurl', 'text|35', 'optional URL link for this image', '' );
		return NrHtml::div( $linkUrl->option_markup, 'class=below-options only-if-file sc' );
	}


	public function recommendedWidth() {
		return $this->recommendedWidth;
	}


	public function recommendedHeight() {
		return $this->recommendedHeight;
	}


	protected function setupMobile() {
		$this->recommendedWidth = 960;
		if ( $this->id() != 'mobile_masthead_image1' ) {
			$firstImg = ppImg::id( 'mobile_masthead_image1' );
			if ( $firstImg->exists ) {
				$this->recommendedHeight = intval( ( $firstImg->height / $firstImg->width ) * $this->recommendedWidth );
			}
		}
		$this->sizingExplanation = 'This image should be <b>exactly</b>' . $this->specifiedDimensionText();
	}


	protected function setupDesktop() {
		$logo = ppImg::id( 'logo' );

		switch ( ppOpt::id( 'headerlayout' ) ) {

			case 'logomasthead_nav':
			case 'mastheadlogo_nav':
				$this->recommendedWidth  = ppOpt::id( 'blog_width' ) - $logo->width;
				$this->recommendedHeight = $logo->height;
				break;

			case 'mastlogohead_nav':
				$this->recommendedWidth  = ppOpt::id( 'blog_width' );
				$this->recommendedHeight = $logo->height;
				break;

			default:
				$this->recommendedWidth  = ppOpt::id( 'blog_width' );
				$firstImg = ppImg::id( 'masthead_image1' );
				if ( $this->id() != 'masthead_image1' && $firstImg->exists ) {
					$this->recommendedHeight = $firstImg->height;
				}
				break;
		}

		$text  = 'Based on your header layout choice';
		$text .= ppHelper::logoInMasthead() ? ' and logo dimensions' : '';
		$text .= ', this image should be <b>exactly</b>';

		$this->sizingExplanation = $text . $this->specifiedDimensionText();
	}


	protected function specifiedDimensionText() {
		if ( $this->recommendedWidth  && !$this->recommendedHeight ) {
			return ' this width:';
		}
		if ( $this->recommendedHeight && !$this->recommendedWidth  ) {
			return ' this height""';
		}
		if ( $this->recommendedWidth  &&  $this->recommendedHeight ) {
			return ' these dimensions:';
		}
	}


	protected function isMobile() {
		return NrUtil::startsWith( $this->id(), 'mobile_' );
	}
}

