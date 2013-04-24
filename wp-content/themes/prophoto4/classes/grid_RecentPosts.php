<?php

class ppGrid_RecentPosts extends ppGrid {


	protected function loadGridItems() {
		$recentPosts = new WP_Query( array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $this->rows * $this->cols,
			'post__not_in'   => array( $this->articleID ? $this->articleID : 999999999 ),
		) );
		foreach ( (array) $recentPosts->posts as $post ) {
			$this->gridItems[] = $this->gridItemFromWpPostObj( $post );
		}
		wp_reset_query();
	}

}

