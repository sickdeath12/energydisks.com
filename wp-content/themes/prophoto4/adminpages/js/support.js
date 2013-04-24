jQuery(document).ready(function($){

	$('#support-actions input').click(function(){
		$('#support-actions input[name="support_action"]').val($(this).attr('value').replace(/ /g, '_'));
	});

	setInterval(function(){
		var setting = $('#check_settings input').val();
		if ( setting.length < 3 ) {
			return;
		}
		$('.options-data').hide();
		if ( setting === '' ) {
			return;
		}
		if ( setting == 'all' ) {
			$('.options-data').show();
			return;
		}
		$('p[id*="'+setting+'"]').show();
	}, 500);

	$('#change_settings label[for="option_val"]').bind('contextmenu', function(){
		$('#change_settings label, #change_settings input').css( 'display', 'block' );
		$('#change_settings input[name="option_key"]').attr('size', '40');
		$('#change_settings textarea').attr('name', 'option_val').show().text($('#change_settings input[name="option_val"]').val());
		$('#change_settings input[name="option_val"]').remove();
		return false;
	});

	$('p.options-data').click(function(){
		$('#change_settings input[name="option_key"]').val($(this).attr('id'));
		$('#change_settings input[name="option_val"]').val($(this).attr('val'));
	});

	$.get( window.location.href + '&nslookup=1', function(response){
		var msg = ( response === '' ) ? 'not found' : response;
		$('#ns-loading').html(msg).addClass('loaded');
		if ( response && $('#ns').length && response.indexOf( $('#ns').text() ) === -1 ) {
			$('#ns').text(response);
		}
	} );
});