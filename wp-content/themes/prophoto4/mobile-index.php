<?php

	echo NrHtml::meta( 'name', 'viewport', 'content', 'width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;' );

	do_action( 'pp_mobile_head' );

	echo NrHtml::stylesheet( ppStaticFile::url( 'mobile.css' ) );
	echo NrHtml::googleJQuery();
	echo NrHtml::scriptSrc( ppStaticFile::url( 'script.js' ) );
	echo NrHtml::scriptSrc( ppStaticFile::url( 'mobile.js' ) );
	echo ppHtml::appleTouchIcon();

	?> 
</head>
<body class="<?php echo ppHtml::bodyClasses(); ?>">

<?php do_action( 'pp_mobile_begin_body' ); ?>

<div id="mobile-wrap" data-role="page">

	<?php ppMobileHtml::renderBlogHeader(); ?>

	<div id="mobile-content" data-role="content">

		<?php if ( is_singular() || ppUtil::isEmptySearch() ) { ?>

			<?php ppContentRenderer::render(); ?>

		<?php } else { ?>

			<ul class="article-list" data-role="listview">
				<?php ppContentRenderer::render(); ?>
			</ul>

			<?php ppMobileHtml::renderOlderNewerPostsLinks(); ?>

		<?php } ?>

	</div>

	<div id="mobile-footer" class="<?php echo ppMobileHtml::footerColorClass(); ?>">

		<?php

		if ( $menuItems = ppMenuUtil::menuItems( 'mobile_nav_menu' ) ) {
			foreach ( (array) $menuItems as $itemID => $children ) {
				$item = ppMenuUtil::menuItem( $itemID, $children );
				$item->renderMobile();
			}
		}

		?>

		<p id="mobile-user-copyright">

			<?php

			echo ppFooter::userCopyright();
			echo ppFooter::attributionLinks();

			?>

			<span class="js-info">
				<?php

				echo ppHtml::googleAnalyticsCode();
				echo ppHtml::statcounterAnalyticsCode();

				?>
			</span>

		</p>

	</div>

</div>
<?php ppHtml::lateConditionalJavascript(); ?>
</body>
</html>