<?php  /* --- fixed sidebar css --- */

if ( !ppWidgetUtil::areaHasWidgets( 'fixed-sidebar' ) ) {
	return;
}


$sidebar = ppSidebar::data();
$site    = ppUtil::siteData();


$separatorImg = ppImg::id( 'sidebar_widget_sep_img' );
if ( $separatorImg->exists ) {
	$widgetSepImage = 'background:url(' . $separatorImg->url . ') no-repeat bottom center;';
	$widgetPaddingBottom = $separatorImg->height;	
} else {
	$widgetPaddingBottom = 0;
	$widgetSepImage = '';
}


$contentWrapWidth = ppHelper::contentWidth() + ( $site->content_margin * 2 );

$borderDec = ppCss::border( 'sidebar', $sidebar->content_side )->onlyIf( $sidebar->using_border )->decs();


$css .= <<<CSS

table#content-wrap {
	border-collapse:collapse;
}
body.has-sidebar #content {
	width:{$contentWrapWidth}px;
	float:$sidebar->content_side;
}
#sidebar {
	vertical-align:top;
	padding-top:[~post_header_margin_above]px;
	overflow:hidden;
	width:{$sidebar->content_width}px;
	max-width:{$sidebar->content_width}px;
	padding-{$sidebar->outer_side}:{$sidebar->outer_padding_width}px;
	padding-{$sidebar->inner_side}:{$sidebar->inner_padding_width}px;
	$borderDec
	border-top-width:0;
	border-bottom-width:0;
	border-{$sidebar->outer_side}-width:0;
	ppCss::background( 'sidebar_bg' )->decs()
}
#sidebar .widget {
	margin-bottom:[~sidebar_widget_margin_bottom]px;
	padding-bottom:{$widgetPaddingBottom}px;
	$widgetSepImage
}
#sidebar img {
	height:auto;
	max-width:{$sidebar->content_width}px;
}
#sidebar img.p4-social-media-icon {
	max-height:{$sidebar->content_width}px !important;
}
CSS;

// fonts, links
$css .= ppCss::font( 'sidebar_headlines' )->rule( '#sidebar h3.widgettitle' );
$css .= ppCss::font( 'sidebar_text' )->rule( '#sidebar, #sidebar p' );
$css .= ppCss::link( 'sidebar_link' )->rules( '.sidebar' );


if ( $sidebar->move_post_footer_items ) {
	$css .= <<<CSS
	body.has-sidebar .article-comments,
	body.has-sidebar .article-footer {
		margin-{$sidebar->side}:{$site->content_margin}px;
	}
CSS;
}

