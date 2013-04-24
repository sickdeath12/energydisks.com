<div class="grid-admin grid-admin-not-loaded <?php echo $grid->type() ?>">

	<h1 class="pp-iframe-title pp-iframe-title-grids">
		<span class="icon"></span>
		Customize ProPhoto <b>Grid</b>
	</h1>

	<?php

	if ( NrUtil::GET( 'context', 'article' ) ) {
		echo NrHtml::p( ppString::id( 'what_is_grid' ) . ' <a href="' . pp::tut()->grids .'">Tutorial here</a>.' );
	}


	if ( isset( $_GET['post_id'] ) ) {
		echo NrHtml::hiddenInput( 'article_id', $_GET['post_id'] );
	}


	if ( $grid->formMsg() ) {
		echo NrHtml::p( NrHtml::span( $grid->formMsg() ), 'class=gmail-notice gmail-notice-show' );
	}

	?>

	<fieldset class="grid_type"<?php if ( NrUtil::GET( 'context', 'article' ) && $grid->type() != 'empty' ) echo 'style="display:none;"'; ?>>
		<?php

		echo NrHtml::label( 'Select type of grid: &nbsp;', 'grid_type' );
		echo NrHtml::select( 'grid_type', array(
			'Recent posts'        => 'recent_posts',
			'Galleries'           => 'galleries',
			'Categories'          => 'categories',
			'Select posts/pages'  => 'selected_articles',
		), $grid->type() );

		?>
	</fieldset>


	<fieldset class="grid_style">
		<?php

		echo NrHtml::label( 'Style of grid display:', 'grid_style' );
		echo NrHtml::select( 'grid_style', array(
			'Text below' => 'img_text_below',
			'Overlaid text on rollover' => 'img_rollover_text',
		), $grid->style() );

		 ?>
	</fieldset>


	<fieldset class="grid_cols">
		<?php echo NrHtml::labledTextInput( 'Number of columns:', 'grid_cols', $grid->cols(), 2 ); ?>
	</fieldset>



	<fieldset class="grid_rows">
		<?php echo NrHtml::labledTextInput( 'Number of rows:', 'grid_rows', $grid->rows() ? $grid->rows() : 3, 2 ); ?>
	</fieldset>


	<fieldset class="exclude_categories">
		<?php

		echo NrHtml::label( 'Check any categories you wish to <b>exclude</b>:', 'excluded_categories' );

		$categories = ppCategory::getAll();
		foreach ( $categories as $category ) {
			echo NrHtml::labledCheckbox(
				'exclude <em>' . $category->name() . '</em>',
				'exclude_category_id_' . $category->id(),
				$grid->categoryExcluded( $category->id() ),
				$category->id()
			);
		}

		?>
	</fieldset>


	<fieldset class="selected_articles">

		<?php echo NrHtml::hiddenInput( 'selected_articles_ids', $grid->type() == 'selected_articles' ? implode( ',', $grid->postIDs() ) : '' ); ?>

		<p class="loading-selectables">
			please wait, loading posts and pages
		</p>

		<div class="available available-articles">

			<h2>
				<b>Available</b> posts/pages:
			</h2>

			<div class="grid-selectables articles">

				<div class="filter-by">

					<?php

					echo NrHtml::label( 'Show:', 'filter_selected_articles' );
					$filters = array(
						'Most recent posts' => 'recent',
						'Pages' => 'type-page',
						'Search all' => 'search',
					);
					foreach ( ppCategory::getAll() as $category ) {
						$filters[ substr( 'Category: '.$category->name(), 0, 35 ) ] = 'category-' . $category->id();
					}
					$filters['All posts/pages'] = 'article';
					echo NrHtml::select( 'filter_selected_articles', $filters );

					?>

					<div class="filter-search filter-search-articles">
						<?php echo NrHtml::labledTextInput( 'Search for:', 'filter_search_articles' ); ?>
					</div>

				</div>

			</div>

		</div>

		<div class="in-grid">

			<h2>
				<b>Selected</b> posts/pages:
			</h2>

			<div class="grid-selectables articles">

				<p class="no-selected-selectables">
					Drag posts or pages from the <b>available</b> section (to the left) 
					into this area to begin.
				</p>

				<p class="cant-submit-empty-msg">
					You must select at least one post or page before continuing.
				</p>

				<?php

				if ( $grid->type() == 'selected_articles' ) {
					$selectedPostIDs = $grid->postIDs();
					foreach ( $selectedPostIDs as $postID ) {
						ppUtil::renderView( 'grid_admin_available_article', array( 'article' => new ppPost( $postID ), 'isRecent' => false ) );
					}
				}

				?>
			</div>

		</div>

	</fieldset>


	<fieldset class="gallery-display-type">

		<?php

		echo NrHtml::label( 'Gallery display type:', 'gallery_display' );
		echo NrHtml::select( 'gallery_display', array(
			'Popup slideshow'            => 'popup_slideshow',
			'Fullscreen popup slideshow' => 'fullscreen_popup_slideshow',
			'Slideshow in page'          => 'slideshow_in_page',
			'Lightbox in page'           => 'lightbox_in_page',
		), ( $grid->type() == 'galleries' ) ? $grid->displayType() : 'popup_slideshow' );

		?>

	</fieldset>


	<fieldset class="galleries">

		<div class="selected-galleries">

			<?php echo NrHtml::hiddenInput( 'selected_galleries_ids', $grid->type() == 'galleries' ? implode( ',', $grid->galleryIDs() ) : '' ); ?>

			<p class="loading-selectables">
				please wait, loading available galleries
			</p>

			<div class="available available-galleries">

				<h2>
					<b>Available</b> galleries:
				</h2>

				<div class="grid-selectables galleries">

					<div class="filter-by">

						<?php

						echo NrHtml::label( 'Show:', 'filter_selected_galleries' );
						$filters = array(
							'Most recent' => 'recent',
							'All' => 'all',
							'Search all' => 'search',
						);

						$galleryIDs = ppGalleryAdmin::allGalleryIDs();
						foreach ( $galleryIDs as $galleryID ) {
							if ( NrUtil::startsWith( $galleryID, '10' ) ) {
								$filters['Imported P3 galleries'] = 'imported_p3';
							} else {
								$filters[ date( 'F, Y', $galleryID ) ] = 'month-' . date( 'my', $galleryID );
							}
						}

						echo NrHtml::select( 'filter_selected_galleries', $filters );

						?>

						<div class="filter-search filter-search-galleries">
							<?php echo NrHtml::labledTextInput( 'Search for:', 'filter_search_galleries' ); ?>
						</div>

					</div>

				</div>

			</div>

			<div class="in-grid in-grid-galleries">

				<h2>
					<b>Selected</b> galleries:
				</h2>

				<div class="grid-selectables galleries">

					<p class="no-selected-selectables">
						Drag galleries from the <b>available</b> section (to the left) 
						into this area to begin.
					</p>

					<p class="cant-submit-empty-msg">
						You must select at least one gallery before continuing.
					</p>

					<?php

					if ( $grid->type() == 'galleries' ) {
						$selectedGalleryIDs = $grid->galleryIDs();
						foreach ( $selectedGalleryIDs as $galleryID ) {
							if ( $gallery = ppGallery::load( $galleryID ) ) {
								ppUtil::renderView( 'grid_admin_available_gallery', compact( 'gallery' ) );
							}
						}
					}

					?>

				</div>

			</div>

		</div>

	</fieldset>


		<?php

		echo NrHtml::hiddenInput( 'grid_id', $grid->id() );


		if ( NrUtil::GET( 'context', 'article' ) ) {

			echo ppUtil::idAndnonce( 'grid_admin' );

			if ( NrUtil::GET( 'grid_id', 'new' ) ) {
				echo NrHtml::hiddenInput( 'save_and_insert_grid', 'true' );
				echo NrHtml::submit( 'Insert grid', 'class=button' );

			} else {
				echo NrHtml::submit( 'Save changes', 'class=button' );
			}

			echo NrHtml::submit( 'Close popup', 'class=button&id=close-popup' );

		}

		 ?>

</div><!--- .grid-admin -->
