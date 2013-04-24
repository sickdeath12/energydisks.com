<?php





ppSubgroupTabs(  array(
	'img_text_below'    => 'Style: Text below',
	'img_rollover_text' => 'Style: Overlaid text on rollover',
	'imgs'     => 'Featured and fallback images',
) );


ppOptionHeader( 'Grids Customization', 'grids' );




/* style: text below img */
ppOptionSubgroup( 'img_text_below' );

	ppO( 'grid_img_text_below_gutter', 'slider|0|50| px', '', 'Spacing between grid items' );
	
	ppFontGroup( array(
		'key' => 'grid_img_text_below_title_link',
		'title' => 'Grid item title link appearance',
		'inherit' => 'all',
	) );

ppEndOptionSubgroup();




/* style: text on rollover */
ppOptionSubgroup( 'img_rollover_text' );

	ppO( 'grid_img_rollover_text_gutter', 'slider|0|50| px', '', 'Spacing between grid items' );

	ppStartMultiple( 'Overlay background' );
		ppO( 'grid_img_rollover_text_overlay_bg_color', 'color|optional', 'background color of text area when grid item rolled over' );
		ppO( 'grid_img_rollover_text_overlay_bg_opacity', 'slider', 'opacity of background color when grid item rolled over' );
	ppStopMultiple();
	
	ppFontGroup( array(
		'key' => 'grid_img_rollover_text_title_link',
		'title' => 'Grid item title link appearance',
		'inherit' => 'all',
	) );
	
	ppFontGroup( array(
		'key' => 'grid_img_rollover_text_text_link',
		'title' => 'Grid item text and link appearance',
		'inherit' => 'all',
		'add' => array( 'nonlink_color' )
	) );

ppEndOptionSubgroup();



/* category featured images */
ppOptionSubgroup( 'imgs' );

	ppUploadBox::renderImg( 'grid_article_img_fallback', 'Grid post/page fallback image', 'fallback image used in grid when post has no images' );


	$categories = ppCategory::getAll();
	foreach ( $categories as $category ) {
		ppUploadBox::renderImg( 'grid_category_' . $category->slug(), 'Category grid featured image for category: ' . addslashes( $category->name() ) );
	}

ppEndOptionSubgroup();




