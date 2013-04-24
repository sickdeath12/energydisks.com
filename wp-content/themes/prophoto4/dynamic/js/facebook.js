var ppFacebook = {


	parse: function(context){
		if ( context.length !== 1 ) {
			console.warn('ppFacebook.init() accepts context with only 1 DOM node');
			context = jQuery('body');
		}

		if ( typeof window.fbAsyncInit == "undefined" ) {
			window.fbAsyncInit = function() {
				FB.init({logging:false,xfbml:true});
				FB.Event.subscribe('comment.create',function(comment){
					ppFacebook.newCommentAdded(comment);
				});
			};
		} else {
			try {
				FB.XFBML.parse(context[0]);
			} catch(e) {}
		}

		if ( !jQuery('#fb-root').length) {
			jQuery('body').append('<div id="fb-root"></div>');
		}

		(function(d){
			var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement('script'); js.id = id; js.async = true;
			js.src = "//connect.facebook.net/"+prophoto_info.facebook_language+"/all.js";
			ref.parentNode.insertBefore(js, ref);
		}(document));
	},


	newCommentAdded: function(fbComment){
		var articleID;
		try {
			articleID = parseInt(jQuery('div[data-href="'+fbComment.href+'"]').parents('article').attr('id').replace('article-',''),10);
		} catch(e) {}
		if ( articleID ) {
			jQuery.ajax({
				type: "POST",
				url: prophoto_info.admin_ajax_url,
				data: {
					"action": "pp_nopriv",
					"articleID": articleID,
					"permalink": fbComment.href,
					"fb_comment_added": 1
				},
				success: function(response){
					if ( response.indexOf( 'No FB comments could be found for' ) !== -1 ) {
						setTimeout(function(){
							ppFacebook.newCommentAdded(fbComment);
						},3000);
					}
				}
			});
		}
	}
};

ppFacebook.parse(jQuery('body'));