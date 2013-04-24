(function(){
	tinymce.create('tinymce.plugins.ppInsertBreakTag', {
		init: function(ed, url){
			ed.addCommand('insertP4Br', function(){
				ed.selection.setContent('<br class="p4br">');
			});
			ed.addButton('ppInsertBreakTag', {
				title:'Force a new line when left/right aligning images', 
				cmd:'insertP4Br', 
				image: 'http://prophoto.s3.amazonaws.com/img/break.gif'
			});
		}
	});
	tinymce.PluginManager.add('ppInsertBreakTag', tinymce.plugins.ppInsertBreakTag);
})();