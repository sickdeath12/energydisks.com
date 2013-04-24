<?php


class ppCustomize {


	public static function render() {
		ppDebugReport();
		$areas = ppCustomize::areas();
		if ( isset( $_GET['area'] ) ) {
			ppUtil::renderView( 'customize_page_area', array( 'selectedArea' => $_GET['area'], 'areas' => $areas ) );
		} else {
			ppUtil::renderView( 'customize_page', compact( 'areas' ) );
		}
	}


	protected static function areas() {
		return (object) array(
			'background' => (object) array(
				'title'  => 'Background',
				'desc'   => 'Site background images, colors, borders, and margins' ),

			'fonts'      => (object) array(
				'title'  => 'Fonts',
				'desc'   => 'Site-wide font settings, plus custom font uploads' ),

			'header'     => (object) array(
				'title'  => 'Header Area',
				'desc'   => 'Header layout and appearance, logo, masthead image/slideshow' ),

			'menus'      => (object) array(
				'title'  => 'Menus',
				'desc'   => 'Main horizontal navigation menus, plus vertical widget-area menus' ),

			'contact'    => (object) array(
				'title'  => 'Contact Form',
				'desc'   => 'Built in contact form area settings, fields, and appearance' ),

			'bio'        => (object) array(
				'title'  => 'Bio Area',
				'desc'   => 'Bio area below header above main content appearance, and picture' ),

			'content'    => (object) array(
				'title'  => 'Content Appearance',
				'desc'   => 'Post and page titles, dates, text, images, tags & category lists, etc.' ),

			'comments'   => (object) array(
				'title'  => 'Comments',
				'desc'   => 'Post and page comments area settings and appearance' ),

			'sidebars'   => (object) array(
				'title'  => 'Sidebars & Footer',
				'desc'   => 'Fixed sidebar column, sliding sidebar drawers, and footer area' ),

			'galleries'  => (object) array(
				'title'  => 'Galleries',
				'desc'   => 'ProPhoto slideshow and lightbox-style gallery options' ),

			'grids'      => (object) array(
				'title'  => 'Grids',
				'desc'   => 'ProPhoto Grid settings and style options' ),

			'mobile'     => (object) array(
				'title'  => 'Mobile',
				'desc'   => 'Mobile phone site settings and appearance' ),

			'settings'   => (object) array(
				'title'  => 'Site Settings',
				'desc'   => 'Analytics, social media, widget images, favicon',
			),

			'advanced'   => (object) array(
				'title'  => 'Advanced',
				'desc'   => 'RSS, SEO, translation, custom CSS, HTML, javascript and more'
			)
		);
	}


}

