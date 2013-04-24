/* 
 image loaded function, works like $.load() func, except
 always fires an event, even with cached images
 marginally modified from orig here: http://gist.github.com/268257
 */


jQuery.fn.imageLoaded = function(callback){
	var elems = this.filter('img'),
	len   = elems.length;

	elems.bind('load error',function(){
		callback.call(this);
	
	}).each(function(){
		
		// cached images don't fire load sometimes, so we reset src.
		if (this.complete || this.complete === undefined || ( this.complete === false && navigator.appVersion.indexOf( 'MSIE 9' ) !== -1 ) ) {
			var src = this.src;
			
			// webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
			// data uri bypasses webkit log warning (thx doug jones)
			this.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
			this.src = src;
		}  
	}); 
	
	return this;
};