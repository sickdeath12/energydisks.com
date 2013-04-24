var ppThrobIntervals = [];

var ppThrob = {
	
	start: function(item){
		if ( item == undefined ) {
			return;
		}
		item.addClass('throbbing');
		item.fadeOut(350);
		setInterval(function(){
			item.fadeIn(350);
		},350);
		ppThrobIntervals[this._uniqueID(item)] = setInterval(function(){
			if ( item.hasClass('throbbing') ) {
				item.fadeOut(350);
			}
		},700);
	},
	
	stop: function(item){
		if ( item == undefined ) {
			return;
		}
		item.removeClass('throbbing');
		clearInterval( ppThrobIntervals[this._uniqueID(item)] );
	},
	
	_uniqueID: function(item){
		return item.text() + item.attr('class') + item.parent().attr('id') + item.text().length + item.attr('class').length;
	}
}
