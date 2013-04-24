jQuery(document).ready(function($){
	$('a').each(function(){
		var href = $(this).attr('href');
		if ( href && href.indexOf( prophoto_info.url ) !== -1 && href.indexOf( 'wp-admin/' ) === -1 ) {
			if ( href.indexOf( 'preview_design=' ) === -1 ) {
				var sep = ( href.indexOf( '?' ) === -1 ) ? '?' : '&';
				$(this).attr('href',href+sep+'preview_design='+pp_preview_design_id);
			}
		}
	});
});