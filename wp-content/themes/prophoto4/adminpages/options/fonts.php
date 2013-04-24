<?php
/* ------------------------- */
/* FONT/LINK  CUSTOMIZATIONS */
/* ------------------------- */

ppOptionHeader( 'Overall Font & Link Font Options', 'fonts' );

//text appearance
ppFontGroup( array( 
	'key' => 'gen',
	'title' => 'Overall font appearance',
	'add' => array( 'lineheight', 'margin_bottom' ),
	'margin_bottom_comment' => 'paragraphs',
) );

// generic link styling
ppFontGroup( array( 
	'key' => 'gen_link',
	'title' => 'Overall link font appearance',
	'not' => array( 'family', 'size' ),
	'inherit' => 'all',
) );

// headlines
ppFontGroup( array( 
	'key' => 'header',
	'title' => 'Overall headline appearance',
	'add' => array( 'letterspacing' ),
) );
	
	
echo NrHtml::script( 'jQuery(document).ready(function($){ ppOption.uploadReveal( "custom_font_" ); });' );


for ( $i = 1; $i <= pp::num()->maxCustomFonts; $i++ ) { 
	$fontUpload = new ppUploadBox_Font( 'custom_font_' . $i, 'Custom uploaded font #' . $i );
	$fontUpload->render();
}

	