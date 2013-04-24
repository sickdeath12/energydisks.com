<?php


class ppGrid_Galleries extends ppGrid {


	protected $galleryIDs  = array();
	protected $displayType = 'popup_slideshow';


	public function __construct( $ID, $data ) {
		if ( isset( $data->galleryIDs ) ) {
			if ( is_array( $galleryIDs = explode( ',', $data->galleryIDs ) ) ) {
				$this->galleryIDs = array_map( 'intval', $galleryIDs );
			} else {
				new ppIssue( 'Invalid galleryIDs data passed to ppGrid_Galleries::__contruct()' );
			}
		}
		if ( isset( $data->displayType ) ) {
			$this->displayType = $data->displayType;
		}
		parent::__construct( $ID, $data );
	}


	public function displayType() {
		return $this->displayType;
	}


	public function galleryIDs() {
		return $this->galleryIDs;
	}


	protected function loadGridItems() {
		foreach ( $this->galleryIDs as $galleryID ) {
			$this->gridItems[] = new ppGridItem( ppGallery::load( $galleryID ), $this->displayType() );
		}
	}

}

