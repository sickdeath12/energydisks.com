jQuery(document).ready(function($){
	$('form').submit(function(){
		
		var upload = $('input[type="file"]');
		if ( upload.val() == '' ) {
			$('#html-upload-ui').addClass('error');
			alert( 'You must upload a design thumbnail.' );
			return false;
		}
		
		
		$('input[type="radio"]:checked').each(function(){
			if ( 'no_choice' == $(this).val() ) {
				$(this).parents('.radio-btns-wrap').addClass('error');
			}
		});
		if ( $('.error').length < 1 ) {
			$('input[type="file"]').prependTo($('#main-form'));
			return true;
		} else {
			alert( 'You must make a selection for every option.' );
			return false;
		}
	});
	
	$('input[type="radio"]').click(function(){
		if ( $(this).val() != 'no_choice' ) {
			$(this).parents('.radio-btns-wrap').removeClass('error');
		}
	});
	
	$('input[type="file"]').click(function(){
		$('#html-upload-ui').removeClass('error');
	});
});