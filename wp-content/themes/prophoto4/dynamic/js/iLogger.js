var iconsole;

jQuery(document).ready(function($){
	
	window.iconsole = {

		initialized: false,

		log: function(msg,size){
			if ( !$('#iconsole').length ) {
				this.initialize();
			}
			this.console.append('<div>'+msg+'</div>');
			if ( size != "undefined") {
				$('#iconsole,#iconsole div').css('font-size',size+'px');
			}
		},

		initialize: function(){
			$('body').prepend('<div id="iconsole"></div>');
			this.console = $('#iconsole');
			$('div',this.console).live('touchend',function(){
				$(this).remove();
				return false;
			});
			$('<style>'+this.css+'</style>').appendTo('head');
			this.initialized = true;
		},
		
		css: 'body.ipad #console {font-size:6px;} body.ipad #iconsole div {padding:4px 5px; margin:0;} #iconsole { font-size:13px; position:absolute; background:#7490A3; top:0; right:0; color:#fff; width:100%; display:block; text-shadow:0 1px 0 #444; z-index:5000; overflow:none; font-family:Courier; font-weight:700; -webkit-box-shadow: 0 2px 5px #333; } #iconsole div { border-bottom:1px #333 solid; padding:6px 10px; line-height:1em; }'

	};
});

