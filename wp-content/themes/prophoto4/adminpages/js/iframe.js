jQuery(document).ready(function($) {
	
	// Modify the default <h3> element
	$('#file_upload_form h3').html('Select a file to upload').css('display','block' );
	$('#design_zip_upload h3').html('Select a design zip file to upload').css('display','block' );
	
	// remove the "Save all changes" button
	$('input').filter(function(){return jQuery(this).attr('name')=='save';}).remove();
	
	$('form:has(.pp-form-required)').each(function(){
		var form = $(this);
		$('.pp-form-required input',form).focus(function(){
			$(this).parents('.pp-form-required').removeClass('pp-form-has-error');
		})
		form.submit(function(){
			$('.pp-form-required').each(function(){
				var requiredArea = $(this);
				if ( $('input',requiredArea).val() == '' ) {
					requiredArea.addClass('pp-form-has-error');
				}
			});
			return ( $('.pp-form-has-error',form).length ) ? false : true;
		});
	});
});


