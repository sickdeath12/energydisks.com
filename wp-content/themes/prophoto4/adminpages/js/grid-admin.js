// set up case-IN-sensitive version of contains:
jQuery.expr[':'].containsi = function(elem,index,match){
     return (elem.textContent || elem.innerText || "").toUpperCase().indexOf(match[3].toUpperCase())>=0;
};

var loadGridAdmins;

jQuery(document).ready(function($){
		
	loadGridAdmins = function(){
		
		$('.grid-admin-not-loaded').each(function(){

			var ga = {

				articleSearchInterval: null,
				gallerySearchInterval: null,

				init: function(context){
					ga = this;
					ga.context = context;
					ga.typeChange();
					ga.articleFilterChange();
					ga.galleryFilterChange();
					ga.preventSearchSubmit();
					ga.submitValidate();
					ga.closePopup();

					// selectable posts
					var selectedPostsArea = $('fieldset.selected_articles',ga.context);
					ga.selectedSelectablesSortable(selectedPostsArea);
					ga.deleteSelectable(selectedPostsArea);

					// selectable galleries
					var selectedGalleriesArea = $('fieldset.galleries',ga.context);
					ga.selectedSelectablesSortable(selectedGalleriesArea);
					ga.deleteSelectable(selectedGalleriesArea);

					ga.context.removeClass('grid-admin-not-loaded');
					if ( ga.context.parents('#available-widgets').length ) {
						ga.context.addClass('grid-admin-not-loaded');
					}
				},
				
				
				preventSearchSubmit: function(){
					$('.filter-search',ga.context).keypress(function(event){
						if ( event.which == 13 ) {
							event.preventDefault();
						}
					});
				},
				
				
				submitValidate: function(){
					ga.context.parents('form').submit(function(){
						var gridType = $('select[name="grid_type"]',ga.context).val();
						if ( gridType == 'galleries' && $('input[name="selected_galleries_ids"]',ga.context).val() == '' ) {
							$('fieldset.galleries',ga.context).addClass('submit-error');
							return false;
						}
						if ( gridType == 'selected_articles' && $('input[name="selected_articles_ids"]',ga.context).val() == '' ) {
							$('fieldset.selected_articles',ga.context).addClass('submit-error');
							return false;
						}
					});
				},
				
				
				closePopup: function(){
					$('#close-popup',ga.context).click(function(){
						try{parent.tb_remove();}catch(e){}
						return false;
					});
				},


				availableSelectablesDraggable: function(context){
					var contextID;
					if ( $('body').hasClass('widgets-php' ) ) {
						contextID = '#'+context.parents('.widget').attr('id');
					} else {
						contextID = '.grid-admin';
					}
					$('.available .grid-selectable',context).draggable({
						helper: 'clone',
						connectToSortable: contextID+' .in-grid .grid-selectables',
						start: function(e,ui){
							ui.helper.css('opacity',1);
							ga.context.addClass('dragging-selectable');
							$('fieldset',ga.context).removeClass('submit-error');
						},
						stop: function(e,ui){
							ga.context.removeClass('dragging-selectable');
						}
					});
				},


				selectedSelectablesSortable: function(context){
					if ( $('.in-grid .grid-selectable',context).length ) {
						$('p.no-selected-selectables',context).hide();
					} 
					$('.in-grid .grid-selectables',context).sortable({
						axis: 'y',
						items: 'div.grid-selectable', 
						update: function(){
							ga.updateSelectableValue(context);
						}
					});
				},


				updateSelectableValue: function(context){
					var IDs = $('.in-grid .grid-selectables',context).sortable('toArray',{attribute:'rel'}).toString();
					$('input[type="hidden"]',context).val(IDs);
					if ( IDs == '' ) {
						$('p.no-selected-selectables',ga.context).show();
					} else {
						$('p.no-selected-selectables',ga.context).hide();
					}
				},


				deleteSelectable: function(context){
					$('.in-grid a.delete',context).live('click',function(){
						$(this).parent().remove();
						ga.updateSelectableValue(context);
					});
				},


				typeChange: function(){
					$('select[name="grid_type"]',ga.context).change(function(){
						
						var gridType = $(this).val();
						ga.context.attr('class', 'grid-admin '+gridType);
						
						if ( gridType == 'selected_articles' || gridType == 'galleries' ) {
							var loadMsg = $('.'+gridType+' .loading-selectables',ga.context);
							if ( loadMsg.length ) {
								var paged = 1;
								var requestSelectables = function(paged) {
									$.ajax({
										type: 'GET',
										url: ajaxurl+'?action=pp&load_grid_selectable_'+gridType.replace('selected_','')+'=1&paged='+paged,
										success: function(response){
											$('.'+gridType+' .available .grid-selectables',ga.context).append(response);
											$('.'+gridType+' .available, .'+gridType+' .in-grid',ga.context).show();
											ga.availableSelectablesDraggable($('.'+gridType,ga.context));
											if ( ( response.split('id="article').length - 1 ) < 100 ) {
												if ( paged == 1 ) {
													loadMsg.remove();
												} else {
													loadMsg.text('loading complete').delay(1500).slideUp();
												}
											} else {
												var btmLimit = (paged * 100)+'';
												var topLimit = (paged * 100 + 100)+''; 
												loadMsg.text('loading additional posts and pages #'+btmLimit+' - #'+topLimit+'...');
												paged = paged + 1;
												requestSelectables(paged);
											}
										}
									});
								}
								requestSelectables(paged);
							}
						}
						
					}).change();
				},


				articleFilterChange: function(){
					$('select[name="filter_selected_articles"]',ga.context).change(function(){
						$('.available .article',ga.context).hide();
						if ( $(this).val() == 'search' ) {
							$('.filter-search-articles',ga.context).show();
							ga.articleSearchInterval = setInterval(function(){
								$('.available .article',ga.context).hide();
								var searchString = $('input[name="filter_search_articles"]',ga.context).val();
								if ( searchString != '' && searchString != ' ' ) {
									$('.available .article:containsi("'+searchString+'")',ga.context).show();
								} 
							},400);
						} else {
							clearInterval(ga.articleSearchInterval);
							$('.filter-search-articles',ga.context).hide();
							$('.available-articles .'+$(this).val(),ga.context).show();
						}
					});
				},
				
				
				galleryFilterChange: function(){
					$('select[name="filter_selected_galleries"]',ga.context).change(function(){
						$('.available .gallery',ga.context).hide();
						if ( $(this).val() == 'search' ) {
							$('.filter-search-galleries',ga.context).show();
							ga.gallerySearchInterval = setInterval(function(){
								$('.available .gallery',ga.context).hide();
								var searchString = $('input[name="filter_search_galleries"]',ga.context).val();
								if ( searchString != '' && searchString != ' ' ) {
									$('.available .gallery:contains("'+searchString+'")',ga.context).show();
								} 
							},400);
						} else {
							clearInterval(ga.gallerySearchInterval);
							$('.filter-search-galleries',ga.context).hide();
							if ( $(this).val() == 'recent' ) {
								$('.available .gallery',ga.context).each(function(n){
									if ( n < 8 ) {
										$(this).show();
									}
								});
							} else {
								$('.available-galleries .'+$(this).val(),ga.context).show();
							}
						}
					}).change();
				}
			};

			ga.init($(this));
		});
	};
	
	loadGridAdmins();
	
	
	$('body').bind('grid-reload',function(){
		loadGridAdmins();
	});
	
	$('div.widgets-sortables').bind('sortstop',function(){
		setTimeout(function(){loadGridAdmins();},500);
	});
	
});