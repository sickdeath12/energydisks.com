<?php


$configArray = array(



	/* bio text */
	'bio-text' => array(

		'title'  => 'Welcome to my blog!',

		'text'   => 'This is some default text for your bio area.  To change this text, go to the <a href="' . pp::site()->wpurl . '/wp-admin/widgets.php">Widgets page</a> and edit or delete the default text in the topmost Bio widget column, called <em>"Bio area spanning column"</em>.

		ProPhoto4 is really flexible when it comes to what sort of content you can put in your bio, as well as how it is arranged.  To really understand all of the possibilities, we recommend checking out both of these important tutorials: <a href="' . pp::tut()->understandingWidgets . '">Understanding Widgets</a> and <a href="' . pp::tut()->customizeBioArea . '">Customizing the Bio Area</a>.

		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',

		'wpautop' => 1,

		'p4_default' => 1,
	),



	/* contact form text */
	'contact-text' => array(

		'title'  => 'Contact Me',

		'text'   => 'This is a default text widget for your contact area.  To change this text, go to the <a href="' . pp::site()->wpurl . '/wp-admin/widgets.php">Widgets page</a> and edit or delete the default widget in the <em>"Contact Form Content Area"</em>.

		For a an explanation of how widgets work, see this tutorial: <a href="' . pp::tut()->understandingWidgets . '">Understanding Widgets</a>. For more on how to customize and configure your contact form area, see here: <a href="' . pp::tut()->contactForm . '">Customizing the Contact Form</a>.

		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',

		'wpautop' => 1,

		'p4_default' => 1,
	),



	/* search */
	'search' => array( 'title' => 'Search' ),



	/* pages */
	'pages' => array(
		'title'   => 'Pages',
		'sortby'  => 'post_title',
		'exclude' => '',
	),



	/* categories */
	'categories' => array(
		'title'        => 'Categories',
		'count'        => 0,
		'hierarchical' => 0,
		'dropdown'     => 0,
	),



	/* recent posts */
	'recent-posts' => array( 'title' => '', 'number' => 5 ),



	/* blogroll */
	'links' => array(
		'images'      => 1,
		'name'        => 1,
		'description' => 0,
		'rating'      => 0,
		'category'    => 0,
	),


	/* archive */
	'archives' => array( 'title' => 'Archives', 'count' => 0, 'dropdown' => 0 ),



	/* meta */
	'meta' => array( 'title' => 'Meta' ),

);


