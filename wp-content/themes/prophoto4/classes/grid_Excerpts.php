<?php


class ppGrid_Excerpts extends ppGrid {


	protected $postObjects;


	public function __construct( $postObjects ) {
		$this->ID = 'excerpts';
		$this->type = 'excerpts';
		$this->rows = ppOpt::id( 'excerpt_grid_rows', 'int' );
		$this->cols = ppOpt::id( 'excerpt_grid_cols', 'int' );
		$this->style = ppOpt::id( 'excerpt_grid_style' );
		$this->postObjects = $postObjects;
		$this->loadGridItems();
	}


	public function loadGridItems() {
		foreach ( (array) $this->postObjects as $post ) {
			$this->gridItems[] = $this->gridItemFromWpPostObj( $post );
		}
	}


	public function render() {
		echo '<div class="excerpts-grid-wrap content-bg">';
			parent::render();
		echo '<div>';
	}
}

