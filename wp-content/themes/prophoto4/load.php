<?php 


function ppClassLoader( $className ) {
	if ( preg_match( '/^(Nr|Wp)/', $className ) ) {
		require_once( dirname( __FILE__ ) . '/classes/' . $className . '.php' );
	} else if ( substr( $className, 0, 2 ) == 'pp' ) {
		$filename = substr( $className, 2 );
		$filename[0] = strtolower( $filename[0] );
		$classPath  = TEMPLATEPATH . '/classes/' . $filename . '.php';
		$widgetPath = TEMPLATEPATH . '/widgets/' . $filename . '.php';
		if ( @file_exists( $classPath ) ) {
			require_once( $classPath );
		} else if ( @file_exists( $widgetPath ) ) {
			require_once( $widgetPath );
		}
		if ( class_exists( $className ) && method_exists( $className, '_onClassLoad' ) ) {
			call_user_func( array( $className, '_onClassLoad' ) );
		}
	}
}
spl_autoload_register( 'ppClassLoader' );


require_once( dirname( __FILE__ ) . '/classes/pp.php' );
require_once( dirname( __FILE__ ) . '/classes/option.php' );
require_once( dirname( __FILE__ ) . '/includes/constants.php' );


/* load more general functions */
require_once( dirname( __FILE__ ) . '/functions/debug.php' );


// only loaded on "P4 Options" page
require_once( dirname( __FILE__ ) . '/functions/options.php' );
require_once( dirname( __FILE__ ) . '/classes/class.fonts.php' );
require_once( dirname( __FILE__ ) . '/classes/class.borders.php' );
require_once( dirname( __FILE__ ) . '/classes/class.options.php' );
require_once( dirname( __FILE__ ) . '/includes/settings/interface.php' );



do_action( 'pp_classes_loaded' );


