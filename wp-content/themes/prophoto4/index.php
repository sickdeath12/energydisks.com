<?php do_action( 'get_header' ); ?><!DOCTYPE html>
<html <?php language_attributes() ?>><!-- p4 build #<?php echo pp::site()->svn ?>  -->
<head><?php

	$thisPage = ppQuery::instance();
	$article  = $thisPage->isArticle() ? ppPost::fromGlobal() : null;
	$ppSEO    = new ppSeo( $thisPage, $article );

	/* title tag */
	echo $ppSEO->titleTag();

	/* meta tags */
	echo NrHtml::meta( 'charset', get_bloginfo( 'charset' ) );
	echo NrHtml::meta( 'http-equiv', 'imagetoolbar', 'content', 'no' );
	echo NrHtml::meta( 'http-equiv', 'X-UA-Compatible', 'content', 'IE=edge' );
	echo $ppSEO->metaDesc();
	echo $ppSEO->metaKeywords();
	echo $ppSEO->metaRobots();

	echo ppFacebook::meta( $thisPage, $article, $ppSEO->desc() );

	if ( ppOpt::test( 'pinterest_prevent_pins', 'true' ) ) {
		echo NrHtml::meta( 'name', 'pinterest', 'content', 'nopin' );
	}

	if ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) {
		return require( TEMPLATEPATH . '/mobile-index.php' );
	}
	echo ppHtml::ipadMeta();


	/* scripts */
	ppAdmin::loadScript( 'jquery' );
	echo ppHtml::wpHead();
	echo NrHtml::lessThanIE( 9, NrHtml::scriptSrc( pp::site()->themeUrl . '/dynamic/js/html5shiv.js?ver=' . pp::site()->svn ) );
	echo ppStaticFile::html( 'script.js' );

	/* css */
	echo ppStaticFile::html( 'style.css' );

	/* links, misc */
	echo ppRss::link();
	echo NrHtml::link( 'pingback', get_bloginfo( 'pingback_url' ) );
	echo ppHtml::appleTouchIcon();
	echo ppHtml::favicon();
	echo ppHtml::insertIntoHead();

	?> 
</head>
<body class="<?php echo ppHtml::bodyClasses(); ?>">
	<div id="inner-body">

	<?php do_action( 'pp_begin_body' ); ?>

	<div id="outer-wrap-centered">

		<div id="dropshadow-top" class="dropshadow-topbottom">
			<div id="dropshadow-top-left" class="dropshadow-corner"></div>
			<div id="dropshadow-top-right" class="dropshadow-corner"></div>
			<div id="dropshadow-top-center" class="dropshadow-center"></div>
		</div>

		<div id="main-wrap-outer">

			<div id="main-wrap-inner">

				<div id="inner-wrap">

					<?php

					/* main site header */
					echo ppBlogHeader::markup();

					/* optional header areas */
					ppContact::render();
					ppBio::render();

					/* main content */
					if ( ppSidebar::onThisPage() ) {
						ppUtil::renderView( 'content_sidebar_' . ppOpt::id( 'sidebar' ) );
					} else {
						ppUtil::renderView( 'content_no_sidebar' );
					}

					/* ad banners */
					echo ppHtml::adBannerMarkup();

					/* footer */
					ppFooter::render();

					?>

				</div><!-- #inner-wrap -->

			</div><!-- #main-wrap-inner -->

		</div><!-- #main-wrap-outer -->

		<div id="dropshadow-bottom" class="dropshadow-topbottom">
			<div id="dropshadow-bottom-left" class="dropshadow-corner"></div>
			<div id="dropshadow-bottom-right" class="dropshadow-corner"></div>
			<div id="dropshadow-bottom-center" class="dropshadow-center"></div>
		</div>

	</div><!-- #outer-wrap-centered -->

	<?php

	/* sliding drawers */
	ppDrawer::renderAll();

	/* google analytics */
	echo ppHtml::googleAnalyticsCode();

	?>

	<?php do_action( 'pp_end_body' ); ?>

	</div><!-- #inner-body -->

	<?php ppHtml::lateConditionalJavascript(); ?>

</body>
</html>