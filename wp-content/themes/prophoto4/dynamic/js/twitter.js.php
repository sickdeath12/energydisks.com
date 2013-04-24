<?php 



$jsCode .= ppTwitterSlider_Widget::js();



$jsCode .= <<<JAVASCRIPT


// subroutine for building the list of tweets from the .json data
function ppTwitterHtml( tweets, li_height ) {
	var height = ( li_height ) ? ' style="height:'+li_height+'px;"' : '';
	var twitter_html = [];
	for ( var i = 0; i<tweets.length; i++ ) {
		var username = tweets[i].user.screen_name;
		var status = tweets[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
	 		return '<a href="'+url+'">'+url+'</a>';
		}).replace(/\B@([_a-z0-9]+)/ig, function(reply) {
	 		return  reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';
		});
		twitter_html.push('<li'+height+'><span>'+status+'</span> <a class="twitter-time" href="http://twitter.com/#!/'+username+'/status/'+tweets[i].id_str+'">'+ppTwitterTime(tweets[i].created_at)+'</a></li>');
	}
	return twitter_html.join('');
}



// subroutine for returning nicely formatted time since tweeting
function ppTwitterTime(time_value) {
	var values = time_value.split(" ");
	time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
	var parsed_date = Date.parse(time_value);
	var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
	var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
	delta = delta + (relative_to.getTimezoneOffset() * 60);

	if (delta < 60) {
		return 'less than a minute ago';
	} else if (delta < 120) {
		return 'about a minute ago';
	} else if (delta < (60*60)) {
		return (parseInt(delta / 60)).toString() + ' minutes ago';
	} else if (delta < (120*60)) {
		return 'about an hour ago';
	} else if (delta < (24*60*60)) {
		return 'about ' + (parseInt(delta / 3600)).toString() + ' hours ago';
	} else if (delta < (48*60*60)) {
		return '1 day ago';
	} else {
		return (parseInt(delta / 86400)).toString() + ' days ago';
	}
}



// requests .json twitter info for each html widget, passes object to correct callback
function ppTwitterWidgetsGetTweets( context ) {
	$('.pp-html-twitter-widget',context).each(function(){
		var twitter_name  = $('.twitter_name',this).text();
		var twitter_count = $('.twitter_count',this).text();
		var this_widget   = $(this);
		// set context - which widget area?
		if ( this_widget.parents('#bio').length ) {
			var context = 'Bio';
		} else if ( this_widget.parents('#sidebar').length ) {
			var context = 'Sidebar';
		} else if ( this_widget.parents('.drawer').length ) {
			var context = 'Drawer';
		} else if ( this_widget.parents('#contact-form').length ) {
			var context = 'Contact';
		} else {
			var context = 'Footer';
		}
		$.getScript( 'https://api.twitter.com/1/statuses/user_timeline/' + twitter_name + '.json?callback=pp'+context+'TwitterHtml&include_rts=0&count='+ twitter_count );
	});
}
ppTwitterWidgetsGetTweets($('body'));



// wrapper functions to call the twitter html function for the right widget context
window.ppBioTwitterHtml = function(tweets) {
	ppLoadTwitterHtml(tweets, '#bio');
}
window.ppSidebarTwitterHtml = function(tweets) {
	ppLoadTwitterHtml(tweets, '#sidebar');
}
window.ppDrawerTwitterHtml = function(tweets) {
	ppLoadTwitterHtml(tweets, '.drawer');
}
window.ppFooterTwitterHtml = function(tweets) {
	ppLoadTwitterHtml(tweets, '#footer');
}
window.ppContactTwitterHtml = function(tweets) {
	ppLoadTwitterHtml(tweets, '#contact-form');
}



// subroutine for printing twitter html update list in correct widget
function ppLoadTwitterHtml(tweets, id) {
	var this_widget = $(id+' .pp-html-twitter-widget-'+tweets[0].user.screen_name.toLowerCase());
	if ( $('.tweet_height', this_widget ).length ) {
		var height = parseInt( $( '.tweet_height', this_widget ).text());
	} else {
		var height = '';
	}
	$('ul', this_widget ).html( ppTwitterHtml( tweets, height ) );
	$('.sliding .controls a', this_widget ).css('display','block');
}
JAVASCRIPT;


// below function just for sliding twitter widget
if ( ppWidgetUtil::instanceOfTypeExists( 'pp-twitter-slider' ) ) {
	$jsCode .=  ppTwitterSlider_Widget::js();
}

// just for nav menu twitter dropdown
$jsCode .= <<<JAVASCRIPT
window.ppNavTwitter = function(tweets) {
	var ID = tweets[0].user.screen_name;
	$('li.twitter-id-'+ID+' ul').html(ppTwitterHtml(tweets));
}
function ppNavTwitterDropdown() {
	$('li.mi-twitter').each(function(){
		var twitterID = $('.twitterID',$(this)).text();
		var numTweets = $('.numTweets',$(this)).text();
		$.getScript( 'https://api.twitter.com/1/statuses/user_timeline/'+twitterID+'.json?callback=ppNavTwitter&include_rts=0&count='+numTweets );
	});
}
ppNavTwitterDropdown();
JAVASCRIPT;
