var ppAjaxFetchSlideDown = {

	init: function(context){
		afs = this;
		
		$('a.ajax-fetch-slidedown',context).click(function(){
			afs.clicked = $(this);
			
			var target = $(this).parent().attr('id').match(/primary_nav_/) ? $('#primary-nav-ajax-receptacle') : $('#secondary-nav-ajax-receptacle')
			
			if ( afs.clicked.hasClass('loaded-and-showing' ) ) {
				target.removeClass('slideshow-init-complete');
				target.slideUp();
				afs.clicked.removeClass('loaded-and-showing' );
			
			} else {
				if ( target.is(':visible') ) {
					$('a.ajax-fetch-slidedown').removeClass('loaded-and-showing');
					target.removeClass('slideshow-init-complete');
					target.slideUp(function(){
						afs.ajaxLoad(target);
					});
				} else {
					afs.ajaxLoad(target);
				}
			}
			return false;
		});
	},
	
	ajaxLoad: function(target){
		
		ppThrob.start(afs.clicked);
		
		var ajaxUrl = afs.clicked.attr('href'),
			getSep  = ( ajaxUrl.indexOf( '?' ) === -1 ) ? '?' : '&';
		
		$.ajax({
			type: 'GET',
			url: ajaxUrl + getSep + 'ajaxFetching=1',
			timeout: 5000,
			success: function(response){
				var markup = $('.article-content',response);
				if ( !markup.length ) {
					markup = $(response).filter('.article-content');
				}
				if ( markup.length ) {
					target.html(markup).delay(750).slideDown();
					afs.clicked.addClass('loaded-and-showing');
					ppJSForContext(target);
				} else {
					this.error();
				}
				ppThrob.stop(afs.clicked);
			},
			error: function(){
				afs.clicked.text('Loading error');
			}
		});
	}
};

ppAjaxFetchSlideDown.init($('body'));
