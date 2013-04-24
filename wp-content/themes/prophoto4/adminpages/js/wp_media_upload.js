jQuery(document).ready(function($){
	

	switch ( galleryHelper.currentTab() ) {
		case 'From Computer':
		
			galleryHelper.watchForUploads();
		
			var crunching = function(){ return $('div.progress:visible').length; };
		
			$('.pp-media-btn').click(function(){
				if ( crunching() ) {
					var clicked = $(this),
					    form = clicked.parents('form'),
					    name = clicked.attr('name');
					form.append('<input type="hidden" name="'+name+'" value="'+name+'" />');
					clicked.val('waiting for upload to complete...');
					setInterval( function(){
						if ( !crunching() ) {
							form.submit();
						}
					}, 500 );
					return false;
				}
			});
			
			$('input[name="post_title_text"]').val(parent.jQuery('#title').val());
			
			$('.savebutton #save').attr('disabled', true);
			$('.media-item input').live('change',function(){
				$('.savebutton #save').removeAttr('disabled');

			});
			break;
			
		case 'ProPhoto Galleries':
		
			if ( $('#tabs').length ) {
				$('#tabs').tabs();
			}
			
			if ( $('#reorder-pp-gallery').length ) {
				var recordGalleryOrder = function(){
					$('input[name="pp_gallery_reorder"]').val($('#reorder-pp-gallery').sortable('serialize'));
				};
				$('#reorder-pp-gallery').sortable({
					update: function(){
						enableSaveChanges();
						recordGalleryOrder();
					}
				});
				$('#reorder-pp-gallery li span').click(function(){
					if ( confirm( 'Delete image from gallery?' ) ) {
						$(this).parent().remove();
						enableSaveChanges();
						recordGalleryOrder();
					}
				});
			}
			
			var enableSaveChanges = function() {
				$('input#pp-gallery-save-changes').removeAttr('disabled').removeClass('disabled');
			}
			$('#edit-pp-gallery').change(enableSaveChanges);
			$('#edit-pp-gallery input').bind('keydown',enableSaveChanges);
			
			if ( $('#edit-pp-gallery').length ) {
				if ( typeof parent.tinyMCE != 'undefined' && parent.tinyMCE.activeEditor ) {
					var galleryID = $('input[name="pp_gallery_id"]').val();
					if ( parent.tinyMCE.activeEditor.getContent().indexOf('slideshow-'+galleryID) !== -1 ) {
						$('input[value="Insert as slideshow"]').attr('disabled','disabled').addClass('disabled');
					}
					if ( parent.tinyMCE.activeEditor.getContent().indexOf('lightbox-'+galleryID) !== -1 ) {
						$('input[value="Insert as lightbox"]').attr('disabled','disabled').addClass('disabled');
					}
				}
			}
			
			$('#done-editing input').click(function(){
				try{parent.tb_remove();}catch(e){}
				return false;
			});
			
			
			$('.insert-as').change(function(){
				if ( $(this).val() != '' ) {
					$(this).parent().submit();
				}
			});
			
			$('.delete-gallery').click(function(){
				return confirm( 'This deletes this gallery completely, it will no longer be loaded anywhere that you have inserted it.' );
			});
			break;
			
		case 'Uploaded':
			$('body').addClass('uploaded-tab');
			galleryHelper.moveMediaBtnsIntoForm();
			jQuery('#create-new-pp-gallery,#insert-all-imgs').show();
			break;
	}
	

});




var galleryHelper = {
	
	watchForUploads: function() {
		galleryHelper.watchForUploadsInterval = setInterval( 'galleryHelper.checkForUploads()', 750 );
	},
	
	checkForUploads: function(){
		if ( jQuery('.media-item').length > 1 ) {
			clearInterval( galleryHelper.watchForUploadsInterval );
			galleryHelper.onUpload();
		}
	},
	
	onUpload: function(){
		
		jQuery('#media-items').sortable({
			axis: 'y'
		});
		
		galleryHelper.moveMediaBtnsIntoForm();
		
		if ( !jQuery('#add-to-gallery-wrap').length ) {
			jQuery('#create-new-pp-gallery,#insert-all-imgs').show();

		} else {
			jQuery('#go-to-edit-pp-gallery, .gmail-notice').show();
		}
		
	},
	
	currentTab: function(){
		return jQuery('ul#sidemenu a.current').text().replace(/ \([0-9]*\)/,'');
	},
	
	moveMediaBtnsIntoForm: function(){
		jQuery('#insert-all-imgs,#go-to-edit-pp-gallery,#create-new-pp-gallery,input[name="post_title_text"],input[name="pp_gallery_id"]').insertAfter(jQuery('p.ml-submit'));
	}
};

