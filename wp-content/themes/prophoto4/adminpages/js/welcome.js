jQuery(document).ready(function($){
	
	$('#bluehost-explanation').click(function(){
		$('#bluehost-more-info').show();
		return false;
	});

	// validate registration info
	$('#register-form').submit(function(){
		
		var email = $('#payer-email-wrap input').val().replace(/ +$/,'');
		var txnID = $('#txn-id-wrap input').val().replace(/ +$/,'');
		
		var emailValid = ( email.match(/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/) !== null );
		var txnIDValid = ( txnID.match(/([A-Z0-9]){17}/) !== null && txnID.length == 17 );

		$('#payer-email-wrap')[ emailValid ? 'removeClass' : 'addClass' ]('has-error');
		$(  '#txn-id-wrap'   )[ txnIDValid ? 'removeClass' : 'addClass' ]('has-error');

		return ( emailValid && txnIDValid );
	});
});