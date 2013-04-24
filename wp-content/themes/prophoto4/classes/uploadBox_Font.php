<?php


class ppUploadBox_Font extends ppUploadBox {


	protected $uploadBtnLabel  = 'Upload Font';
	protected $replaceBtnLabel = 'Replace Font';
	protected $deleteBtnLabel  = 'Delete Font';
	protected $fontName;
	protected $fontSlug;


	public function __construct( $ID, $name ) {
		$fontData = ppOpt::id( $ID, 'array' );

		if ( isset( $fontData['name'] ) ) {
			$this->fontName = $fontData['name'];
		}
		if ( isset( $fontData['slug'] ) ) {
			$this->fontSlug = $fontData['slug'];
		}

		$comment = 'Fonts must be uploaded individually as a zipped <em>Font Squirrel</em> font kit. For information on how
					to download or create one of these, see <a href="' . pp::tut()->customFonts . '">tutorial here</a>.';
		parent::__construct( $ID, $name, $comment );

		$this->classes[] = 'upload-font';

		$this->name .= ' <em>-</em> <em class="font-name">' . $this->fontName() . '</em>';
	}


	public function uploadBtnHref() {
		return ppIFrame::url( 'file_upload_form&upload_type=font_zip&font_zip_id=' . $this->id(), '410', '110' );
	}


	public function deleteBtnHref() {
		return ppIFrame::url( 'file_reset_form&upload_type=font_zip&font_zip_id=' . $this->id(), '410', '110' );
	}


	public function fileDisplay() {
		echo NrHtml::p( 'A very bad quack might jinx zippy fowls', "class=preview&style=font-family:" . $this->fontName() );
	}


	public function leftBtmOptions() {
		$select = str_replace( 'select font...', 'select fallback font...', ppFontUtil::websafeFamilyParams() );
		$fallback = new ppOptionBox( $this->id() . '_fallback', $select, 'font to use if custom font fails to load' );
		return NrHtml::div( $fallback->option_markup, 'class=below-options only-if-file sc' );
	}


	protected function fontName() {
		return $this->fontName;
	}


	protected function fontSlug() {
		return $this->fontSlug;
	}


	protected function hasUploadedFile() {
		return ppFontUtil::hasAvailableFontFile( $this->fontName(), $this->fontSlug() );
	}

}

