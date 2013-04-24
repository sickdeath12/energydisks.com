(function(){
	tinymce.create('tinymce.plugins.pp_edit_gallery', {
		
		init: function(ed, url){

			ed.onMouseDown.add(function(ed, e) {
				
				var targetClass = ed.dom.getAttrib(e.target, 'class');
				var isGallery   = ( targetClass.indexOf('pp-gallery-placeholder') !== -1 ) ? true : false;
				var isGrid      = ( targetClass.indexOf('pp-grid-placeholder')    !== -1 ) ? true : false;
				
				if ( e.target.nodeName == 'IMG' && ( isGallery || isGrid ) ) {
					if ( tinymce.isIE ) {
						e.layerX = e.offsetX;
						e.layerY = e.offsetY;
					} else {
						ed.execCommand('enableObjectResizing', false, false);
						setTimeout(function(){
							ed.execCommand('enableObjectResizing', false, true);
						},1000);	
					}
					
					if ( ( e.layerX > 5 && e.layerX < 55 ) && ( e.layerY > 25  && e.layerY < 42 ) ) {
						ed.dom.remove(e.target);
						if ( isGrid ) {
							jQuery.get(ajaxurl+'?action=pp&delete_article_grid='+e.target.id.match(/[0-9]+/)[0]);
						}
					
					} else {
						if ( isGallery ) {
							tb_show( '', 
								jQuery('#pp-admin-dropdown-galleries a')
									.attr('href')
									.replace( 'tab=pp_galleries', 'tab=pp_galleries&pp_gallery_id='+e.target.id.match(/[0-9]+/)[0] ) );
		
						} else {
							tb_show( '', 
								jQuery('#pp-admin-dropdown-new-grid a')
									.attr('href')
									.replace( 'grid_id=new', 'grid_id='+e.target.id.match(/[0-9]+/)[0] ) );
						}
					}
				
				}
			});
		}
	});
	
	tinymce.PluginManager.add('pp_edit_gallery', tinymce.plugins.pp_edit_gallery);
})();