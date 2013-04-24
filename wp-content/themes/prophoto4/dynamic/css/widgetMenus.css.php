<?php 


$mainFontSize = 13;

for ( $i = 1; $i <= pp::num()->maxWidgetMenus; $i++ ) {
	
	
	$levels = array(
		'li'       => 'li',
		'li li'    => 'sub_li',
		'li li li' => 'sub_sub_li',
	);
	
	$marginLeft = 0;
	foreach ( $levels as $selector => $key ) {
		
		$css .= ppCss::link( "widget_menu_{$i}_{$key}_link" )->withNonLink()->rules( "li.widget ul.pp-widget-menu-$i $selector" );

		if ( ppOpt::test( "widget_menu_{$i}_{$key}_list_style", 'none || image' ) ) {
			$listStyleType = 'none';
		} else {
			$listStyleType = ppOpt::id( "widget_menu_{$i}_{$key}_list_style" );
		}
		
		if ( ppOpt::test( "widget_menu_{$i}_{$key}_list_style", 'image' ) && ppImg::id( "widget_menu_{$i}_{$key}_list_image" )->exists ) {
			$customBullet = ppImg::id( "widget_menu_{$i}_{$key}_list_image" );
			$background = "background:url($customBullet->url) no-repeat top left;";
			$paddingAmt = $customBullet->width + 6;
			$padding = "padding-left:{$paddingAmt}px;";
		} else {
			$background = 'background-image:none;';
			$padding = 'padding-left:0;';
		}
		
		$css .= <<<CSS
		#inner-body li.widget ul.pp-widget-menu-$i $selector {
			margin-bottom:[~widget_menu_{$i}_{$key}_margin_btm]px;
			list-style-type:$listStyleType;
			list-style-position:inside !important;
			margin-left:{$marginLeft}px;
			$background
			$padding
		}
		#outer-wrap-centered li.widget ul.pp-widget-menu-$i $selector ul {
			margin-top:[~widget_menu_{$i}_{$key}_margin_btm]px;
		}
CSS;

		$marginLeft = ( $mainFontSize * 2 );
		if ( $padding != 'padding-left:0;' ) {
			$marginLeft = max( 0, $marginLeft - ( $customBullet->width + 6 ) );
		}
	}

	
}




$css .= <<<CSS

li.widget ul.pp-widget-menu li {
	line-height:1em;
}
li.widget ul.pp-widget-menu li.icon-align-left img {
	margin-right:0.5em;
}
li.widget ul.pp-widget-menu li.icon-align-right img {
	margin-left:0.5em;
}

CSS;


