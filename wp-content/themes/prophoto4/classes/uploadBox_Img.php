<?php


class ppUploadBox_Img extends ppUploadBox {


	protected $uploadBtnLabel  = 'Upload Image';
	protected $replaceBtnLabel = 'Replace Image';
	protected $deleteBtnLabel  = 'Delete Image';


	public function img() {
		return $this->file();
	}


	public function uploadBtnHref() {
		return ppIFrame::url( 'file_upload_form&upload_type=img&file_id=' . $this->id(), '410', '110' );
	}


	public function deleteBtnHref() {
		return ppIFrame::url( 'file_reset_form&upload_type=img&file_id=' . $this->id(), '410', '110' );
	}


	public function fileDisplay() {
		$img = new ppImgTag( $this->img()->url . '?width=' . $this->img()->width );
		$img->id( 'uploaded-img-' . $this->id() );
		$img->addClass( 'uploaded-file' );
		$img->addClass( 'uploaded-img' );
		if ( $this->img()->width > $this->maxImgDisplayWidth() ) {
			$img->width( $this->maxImgDisplayWidth() );
		}
		return $img->markup() . NrHtml::span( '<b>&larr;</b> image', 'class=highlight-small-img' );
	}


	public function maxImgDisplayWidth() {
		return isset( $_GET['menu_item_id'] ) ? 325 : 568;
	}


	public function fileStatsBox() {
		$this->renderView( 'upload_box_img_data' );
	}


	protected function setup() {
		if ( $this->hasUploadedFile() && ( $this->img()->width + $this->img()->height ) < 50 ) {
			$this->classes[] = 'very-small-img';
		}
	}
}

