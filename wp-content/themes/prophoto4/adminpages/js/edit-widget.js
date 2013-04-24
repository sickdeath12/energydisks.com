jQuery(document).ready(function($){
	
	try {
		var widgetID   = window.location.href.split('=').pop();
		var widgetForm = $('div[id$="'+widgetID+'"]');
		var widgetArea = widgetForm.parents('.widgets-holder-wrap');
		
		$('#widgets-right .widgets-holder-wrap').addClass('closed');
		widgetArea.removeClass('closed').show();
		widgetForm.css({
			borderColor:'#000',
			borderWidth:'2px',
			boxShadow:'3px 3px 5px #777'
		});

		$('a.widget-action',widgetForm).click();
		$('input:visible').first().focus();
	} 
	
	catch(e){}
	
});