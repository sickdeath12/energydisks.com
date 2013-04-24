/* Scripts for the Settings page */

jQuery(document).ready(function($){
	
	/* activate and delete designs */
	$('.activate_design').click(function(){
		var clicked = $(this);
		var form = $('#misc_form');
		$('#action', form).val(clicked.attr('action'));
		$('#value', form).val(clicked.attr('val'));
		form.submit();
	});
	$('.delete_design').click(function(){
		var permission = confirm('This completely deletes this design and all its settings.  There is no undo.  Are you sure you want to continue?');
		if (!permission) return;
		var clicked = $(this);
		var form = $('#misc_form');
		$('#action', form).val(clicked.attr('action'));
		$('#value', form).val(clicked.attr('val'));
		form.submit();
	});
	
	$('#reset-everything input').click(function(){
		if ( !confirm('This erases all of your saved designs completely.\n\nThere is no undo.\n\nAre you sure you want to DELETE ALL SAVED CUSTOMIZATIONS FOR ALL DESIGNS?' ) ) return false;
		return true;
	});
});

function p4_refresh_designs_page() {
	window.location.href = window.location.href;
}


