<?php

class ppCategory {


	protected $id;
	protected $name;
	protected $slug;
	protected $count;
	protected $img;
	protected $desc;
	protected $wpCat;


	public static function getAll() {
		$wpCats  = get_categories();
		$allCats = array();
		foreach ( $wpCats as $wpCat ) {
			$allCats[] = new ppCategory( $wpCat );
		}
		return $allCats;
	}


	public function __construct( $wpCatObj ) {
		$this->wpCat = $wpCatObj;
		$this->id    = $this->wpCat->cat_ID;
		$this->name  = $this->wpCat->name;
		$this->slug  = $this->wpCat->slug;
		$this->count = $this->wpCat->count;
		$this->desc  = $this->wpCat->category_description;
	}


	public function id() {
		return $this->id;
	}


	public function name() {
		return $this->name;
	}


	public function slug() {
		return $this->slug;
	}


	public function count() {
		return $this->count;
	}


	public function description() {
		return $this->desc;
	}


	public function url() {
		return pp::site()->url . '/?category_name=' . $this->wpCat->category_nicename;
	}


	public function img() {
		if ( $this->img === null ) {
			$this->img = '';

			if ( ppImg::id( 'grid_category_' . $this->slug() )->exists ) {
				$this->img = ppImg::id( 'grid_category_' . $this->slug() )->url;

			} else {
				$catPosts = new WP_Query( array(
					'type' => 'post',
					'cat' => $this->id(),
					'posts_per_page' => '1'
				) );
				wp_reset_query();

				foreach ( $catPosts->posts as $catPost ) {
					$post = new ppPost( $catPost );
					if ( $img = $post->excerptImgSrc() ) {
						$this->img = $img;
						break;
					}
				}
			}
		}
		return $this->img;
	}
}

