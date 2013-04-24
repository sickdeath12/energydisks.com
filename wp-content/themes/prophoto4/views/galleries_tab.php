<div id="galleries-tab-wrap">

	<div id="create-new">

		<h1 class="pp-iframe-title pp-iframe-title-galleries">
			<span class="icon"></span>
			<b>Create new</b> ProPhoto gallery:
		</h2>

		<a href="<?php echo ppMediaAdmin::uploadIFrameUrl( 'type', 'new_pp_gallery=1' ) ?>" class="button" id="new-pp-gallery">
			Upload images for a new ProPhoto gallery
		</a>

	</div>


	<?php


	/* galleries from this post */
	if ( $associatedGalleries = ppGallery::galleriesAssociatedWithArticle( $_GET['post_id'] ) ) {

		echo NrHtml::h2( '<span class="icon"></span>ProPhoto galleries created from <b>this post</b>:', 'class=pp-iframe-title pp-iframe-title-galleries' );

		foreach ( $associatedGalleries as $gallery ) {
			if ( !$gallery->imgs() ) {
				$gallery->delete();
			} else {
				ppUtil::renderView( 'gallery_edit_or_insert', compact( 'gallery' ) );
			}
		}
	}


	/* all other galleries */
	$associatedGalleryIDs = array_map( create_function( '$gallery', 'return $gallery->id();' ), $associatedGalleries  );
	$allGalleryIDs        = ppGalleryAdmin::allGalleryIDs();
	$otherGalleryIDs      = array();
	foreach ( $allGalleryIDs as $galleryID ) {
		if ( !in_array( $galleryID, $associatedGalleryIDs ) ) {
			$otherGalleryIDs[] = intval( $galleryID );
		}
	}

	if ( $otherGalleryIDs ) {
		rsort( $otherGalleryIDs, SORT_NUMERIC );
		echo NrHtml::h2( '<span class="icon"></span><b>Other</b> ProPhoto galleries:', 'class=pp-iframe-title pp-iframe-title-galleries' );
		foreach ( $otherGalleryIDs as $otherGalleryID ) {
			$gallery = ppGallery::load( $otherGalleryID );
			if ( $gallery ) {
				if ( !$gallery->imgs() ) {
					$gallery->delete();
				} else {
					ppUtil::renderView( 'gallery_edit_or_insert', compact( 'gallery' ) );
				}
			}
		}
	}


	?>

</div>
