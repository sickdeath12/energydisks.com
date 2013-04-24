<?php

$context = apply_filters('media_buttons_context', __('Upload/Insert %s'));

$img = '<img src="http://prophoto.s3.amazonaws.com/img/media-button.png" width="15" height="15" />';

echo '<a href="' . ppMediaAdmin::uploadIFrameUrl() . '" class="thickbox add_media" id="' . esc_attr( 'content' ) . '-add_media" title="' . esc_attr__( 'Add Media' ) . '" onclick="return false;">' . sprintf( $context, $img ) . '</a>';

?>

<div class="pp-admin-dropdown-wrap">

	<ul class="pp-admin-dropdown">

		<li id="pp-admin-dropdown-select-media-link" class="current">
			ProPhoto <span class="arrow">&nbsp;</span>
		</li>

		<li id="pp-admin-dropdown-new-gallery">
			<a href="<?php echo ppMediaAdmin::uploadIFrameUrl( 'type', 'new_pp_gallery=1' ); ?>" title="Create a new gallery by uploading images" class="thickbox">
				<span class="icon"></span>
				New Gallery
			</a>
		</li>

		<li id="pp-admin-dropdown-galleries">
			<a href="<?php echo ppMediaAdmin::uploadIFrameUrl( 'pp_galleries' ); ?>" title="Edit/insert existing galleries" class="thickbox">
				<span class="icon"></span>
				Your Galleries
			</a>
		</li>

		<li id="pp-admin-dropdown-new-grid">
			<a href="<?php echo ppIFrame::url( 'grid_admin&grid_id=new&context=article', '', '', ppUtil::editArticleID() ); ?>" title="Insert new grid" class="thickbox">
				<span class="icon"></span>
				New Grid
			</a>
		</li>

	</ul>

</div>