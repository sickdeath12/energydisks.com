<?php
/* --------------------------------------------------------------- */
/* -- library of functions related to debugging and development -- */
/* --------------------------------------------------------------- */


/* check for and advise of common p4 installation/permissions problems */
function ppSelfCheck( $write_result = false ) {
	
	// check for nested prophoto4/prophoto4 problem which throws a wp-config require error
	if ( NrUtil::isIn( '/prophoto4/prophoto4', pp::site()->themeUrl ) ) {
		return ppString::adminError( 'nested_theme_folder' );
	}
	
	// check for misnamed theme folder
	if ( !NrUtil::isIn( '/prophoto4', pp::site()->themeUrl ) ) {
		return ppString::adminError( 'misnamed_theme_folder' );
	}
	
	// fix potentially screwed-up standard-looking upload path
	if ( NrUtil::isIn( 'wp-content/uploads', get_option( 'upload_path' ) ) && get_option( 'upload_path' ) != 'wp-content/uploads' ) {
		update_option( 'upload_path', 'wp-content/uploads' );
	}
	
	if ( !$write_result ) return;
	
	// static files created successfully
	if ( !NrUtil::isIn( '0', implode( '', $write_result ) ) ) {
		return ( pp::site()->isDev ) ? '' : ppUpdatedMsg( 'Options updated.' );
	
	// static file writing problem
	} else {
		return ppString::adminError( 'static_file_write_error' );
	}
}




/* the debug box printing out stored values in the DB */
function ppDebugReport() {
	if ( !pp::browser()->isTech ) {
		return;
	}
	
	echo '<div id="debug-wrap" class="wrap">
	<div id="jared_debug">
	<p><pre class="updated">'."\n\n";
	
	$debug_array = ( $_GET['page'] == 'pp-customize' ) 
		? array_merge( ppActiveDesign::toArray() ) : get_option( pp::wp()->dbStorageName );
	ppDebugPrintarray( $debug_array );
	
	echo "\n".'</pre>';
	
	$show = NrUtil::GET( 'show', 'ids' ) ? 'true' : 'false';
	
	echo <<<HTML
	<style type="text/css" media="screen">
		#devsave { display:inline; }
		#debug-wrap {
			display:none;
			border:1px solid #ccc;
			background:#ececec;
			padding:5px;
		}
		#show-debug {
			margin-left:15px;
			color:#999 !important;
			font-size:10px !important;
		}
		pre.updated {
			margin:0 5px 5px 5px !important;
			overflow:auto;
			max-height: 320px;
		}
		#form-weight-warn {
			opacity:.8;
			font-size:.5em;
			color:red;
			background:#fff;
			margin-left:20px;
			padding:1px 5px;
		}
	</style>
	<script>
		var p4_debug = true;
		var show_ids = '$show';
		jQuery(document).ready(function($){
			$('a[title="Visit Site"]').after('<a id="show-debug">P4 DEBUG</span>');
			$('#show-debug').live('click', function(){
				$('#debug-wrap').slideToggle();
			});
			if ( show_ids == 'true' ) $('tt').show();
			$('#icon-themes').click(function(){
				$('tt').toggle();
			});
			$('.option-section-label, .upload-box-label').click(function(){
				var option = $(this).parents('.option');
				$('tt', option).toggle();
			});

		});
	</script>
	</p></div></div>
HTML;
}


/* recursive debug box subroutine */
function ppDebugPrintarray($ar, $group = '') {
	$var = ( $_GET['page'] == 'pp-customize' ) ? 'p4' : 'p4_store';
	// design meta info first
	if ( isset( $ar['active_design'] ) ) {
		echo "\${$var}['active_design'] = \"{$ar['active_design']}\"\n***\n";
		unset( $ar['active_design'] );
		if ( $designs = $ar['designs'] ) {
			unset( $ar['designs'] );
			ppDebugPrintarray( $designs, "['designs']" );
		}
		if ( $design_meta = $ar['design_meta'] ) {
			unset( $ar['design_meta'] );
			ppDebugPrintarray( $design_meta, "['design_meta']" );
			echo "***\n";
		}
	}
	// rest of the data
	ksort( $ar );
	foreach( (array) $ar as $key => $val ) {
		if ( is_array( $val ) ) {
			ppDebugPrintarray( $val, "{$group}['$key']" );
		} else {
			$val = str_replace( '<','&lt;', (string) $val );
			if ( $val ) {
				echo "\${$var}{$group}['$key'] = \"$val\"\n";
			}
		}
	}
}


