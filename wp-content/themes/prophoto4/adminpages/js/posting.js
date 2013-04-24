/* javascript inserted into pages where the user is posting */

jQuery(document).ready(function(){
	
	jQuery('#media-buttons a:contains("New gallery")').click(function(){
		if ( jQuery('#title').val() !== '' && jQuery(this).attr('href').indexOf('title=') === -1 ) {
			jQuery(this).attr('href',jQuery(this).attr('href').replace('TB_','title='+jQuery('#title').val()+'&TB_'));
		}
	});
	
	// keep the post custom meta closed
	jQuery('#postcustom,#pagecustomdiv').addClass('closed');

});