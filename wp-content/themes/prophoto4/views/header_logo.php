<div id="logo-wrap">
	<div id="logo">

		<a href="<?php echo $logo->linkurl ?>" title="<?php echo pp::site()->name ?>" rel="home" id="logo-img-a">
			<img id="logo-img" src="<?php echo $logo->url ?>" <?php echo $logo->htmlAttr ?> alt="<?php echo pp::site()->name ?> logo" />
		</a>

		<<?php echo $h1or2 ?>>
			<a href="<?php echo $logo->linkurl ?>" title="<?php echo pp::site()->name ?>" rel="home"><?php echo pp::site()->name ?></a>
		</<?php echo $h1or2 ?>>

		<p>
			<?php echo pp::site()->tagline ?> 
		</p>

	</div><!-- #logo -->

	<?php

	if ( ppOpt::test( 'headerlayout', 'pptclassic' ) ) {
		echo ppBlogHeader::nav();
	}

	?>

</div><!-- #logo-wrap -->
