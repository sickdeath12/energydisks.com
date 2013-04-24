/* allow some console usage in IE, prevent js errors if console.x() calls left in production code */
if (  typeof console == 'undefined' ) {
	window.console = {
		log: function(msg){
			if ( /ieconsole/.test(window.location.href) || prophoto_info.is_dev ) {
				if ( !jQuery('#ielog').length ) {
					jQuery('body').append('<p id="ielog"></p>');
					jQuery('#ielog').append('<strong>CONSOLE:</strong>');
				}
				jQuery('#ielog').append('&nbsp;'+msg+'<hr />');
			}
		},
		error: function(){},
		trace: function(){},
		dir: function(){},
		warning: function(){}
	};
}