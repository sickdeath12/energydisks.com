var menuAdmin, menuAlign;

jQuery(document).ready(function($){


	menuAlign = {

		init: function(){
			mal = this;
			$('#primary_nav_menu_align-individual-option input,#secondary_nav_menu_align-individual-option input').unbind('click').click(function(){
				var option = $(this).parent();
				if ( $(this).val() == 'split' ) {
					option.addClass('do-split');
					$('.split-explain',option).show();
					menuAlign.splitMarkup(option);
					menuAlign.draggable(option);
					menuAlign.droppable(option);
				} else {
					$('.dropper',option).remove();
					$('.split-explain',option).hide();
					$('.menu-align-box',option).css('float','none');
					$('.menu-align-wrap',option).css('text-align',$(this).val());
					option.removeClass('do-split');
				}
			});
			$('#primary_nav_menu_align-individual-option input:checked,#secondary_nav_menu_align-individual-option input:checked').click();
		},

		splitMarkup: function(parent){
			var beforeSplit;
			$('.menu-align-wrap',parent).css('text-align','left');
			$('.dropper',parent).remove();
			var splitAfter = $('.menu-align-box[id="aligner_'+$('.split_after input',parent).val()+'"]',parent);
			if ( $('.menu-align-box', parent).length < 2 || ( splitAfter.length && splitAfter.attr('id') != $('.menu-align-box:last',parent).attr('id') ) ) {
				beforeSplit = splitAfter;
			} else {
				beforeSplit = $('.menu-align-box:last',parent).prev();
				$('.split_after input',parent).val(beforeSplit.attr('id').replace('aligner_',''));
			}
			if ( $('.menu-align-box',parent).length == 1 ) {
				$('.split-explain span',parent).hide();
				$('.only-one').show();
				parent.removeClass('do-split');
			} else if ( $('.menu-align-box',parent).length == 2 ) {
				$('.split-explain span',parent).hide();
				$('.only-two').show();
				$('.menu-align-box:last',parent).css('float','right');
				parent.removeClass('do-split');
			} else {
				$('.split-explain span',parent).hide();
				$('.normal').show();
				beforeSplit.after('<span class="dropper left">&larr;</span><span class="dropper spacer"></span><span class="dropper right">&rarr;</span>');
			}
		},

		draggable: function(parent){
			$('.dropper',parent).show();
			$('.menu-align-box',parent).removeClass('align-draggable');
			$('.dropper:first',parent).prev().addClass('align-draggable');
			$('.dropper:last',parent).next().addClass('align-draggable');

			if ( $('.dropper:first',parent).prev().attr('id') == $('.menu-align-box:first',parent).attr('id') ) {
				$('.dropper:first',parent).prev().removeClass('align-draggable');
				$('.dropper:last',parent).hide();
			}

			if ( $('.dropper:last',parent).next().attr('id') == $('.menu-align-box:last',parent).attr('id') ) {
				$('.dropper:last',parent).next().removeClass('align-draggable');
				$('.dropper:first',parent).hide();
			}

			$('.align-draggable',parent).draggable({
				revert: 'invalid',
				helper: 'clone',
				containment: 'document',
				revertDuration: 100,
				opacity: 0.45,
				delay: 150,
				start: function(e,ui){
					$('.menu-align-wrap',parent).addClass('dragging');
					$('#'+e.currentTarget.id).addClass('ui-dragged-from');
					var which = $(e.target).next().hasClass('dropper') ? 'first' : 'last';
					$('.dropper:'+which,parent).css('opacity','0');
				},
				stop: function(e,ui){
					$('.dropper',parent).css('opacity','1');
					$('.menu-align-wrap',parent).removeClass('dragging');
					$('#'+ui.helper.context.id).removeClass('ui-dragged-from');
					$('.align-draggable',parent).draggable('destroy');
					menuAlign.draggable(parent);
				}
			});
		},

		droppable: function(parent){
			$('.dropper',parent).not('.spacer').droppable({

				tolerance: 'pointer',

				drop: function( event, ui ) {
					ui.helper.remove();
					var pos = $(this).hasClass('right') ? 'after' : 'before';
					$(this)[pos](ui.draggable);
					$('.split_after input',parent).val($('.dropper:first').prev().attr('id').replace('aligner_',''));
				},

				over: function( event, ui ){},
				out: function( event, ui ){}
			});
		},


		rebuildItems: function(context){
			context = context.parents('.subgroup');
			var alignWrap = $('.menu-align-wrap',context);
			alignWrap.empty();
			$('.menu-item-wrap .top-level',context).each(function(){
				alignWrap.append('<span id="aligner_'+$(this).attr('id')+'" class="menu-align-box">'+$(this).find('h3:first').text()+'</span>')
			});
			menuAlign.init();
		}
	};


	menuAlign.init();






	var ma = {};

	menuAdmin = {


		dropzone: '<div class="inbetween droppable"></div>',


		init: function(menuInstance){
			var context = $('#'+menuInstance+'-menu-admin-wrap');
			if ( menuInstance.match(/widget_menu_/) || menuInstance.match(/mobile_/) ) {
				context.addClass('vertical-menu-admin');
			}
			ma = this;
			ma.setupMarkupCss(context);
			ma.setupDragDrop(context);
			ma.addNewLink(context);
			ma.deleteLink(context);
			ma.hoverClasses(context);
			ma.IEFix();
		},



		hoverClasses: function(context){
			$('.menu-item',context).mouseenter(function(){
				$('.menu-item',context).removeClass('hovered');
				$(this).addClass('hovered');
			});
			$('.menu-item',context).mouseleave(function(){
				$('.menu-item',context).removeClass('hovered');
				$(this).parent('.menu-item').addClass('hovered');
			});
		},


		deleteLink: function(context){
			$('.menu-item a.delete',context).click(function(){
				$('#tTips').hide();
				if ( !confirm('Delete menu item?') ) {
					return;
				}
				var menuItem = $(this).parent();
				var menuItemID = menuItem.attr('id');
				$.ajax({
					type: 'POST',
					url: ajaxurl+'?action=pp',
					data: {
						delete_menu_item: true,
						menu_item_id: menuItemID
					},
					success: function(response){
						if ( response.indexOf( 'menu item deleted' ) !== -1 ) {
							if ( $('.menu-item',menuItem).length ) {
								alert('Nested menu items will not be deleted.');
								$('.menu-item',menuItem).insertAfter(menuItem);
							}
							menuItem.remove();
							ma.recordNewStructure(context);
							ma.resetUI(context);
						} else {
							this.error();
						}
					},
					error: function(){
						ma.errorMsg('Error deleting menu item.',context);
					}
				});
				return false;
			});
		},


		isVertical: function(context) {
			return context.hasClass('vertical-menu-admin');
		},


		addNewLink: function(context){
			$('.add-new-link',context).click(function(){
				$('.new-menu-items-wrap .menu-item:first',context).hide().prependTo($('.menu-item-wrap',context));
				$('.menu-item-wrap',context).prepend(ma.dropzone);
				var newMenuItem = $('.menu-item-wrap .menu-item:first',context);
				$.ajax({
					type: 'POST',
					url: ajaxurl+'?action=pp',
					data: {
						create_new_menu_item: true,
						id: newMenuItem.attr('id')
					},
					showIFrame: function(){
						$('a.edit-menu-item',newMenuItem).click();
					},
					success: function(response){
						var _this = this;
						if ( response.indexOf( 'new menu item created' ) !== -1 ) {
							ma.recordNewStructure(context);
							ma.resetUI(context);
							var makeRoom,hideRoom;
							if ( ma.isVertical(context) ) {
								makeRoom = {paddingTop:'48px'};
								hideRoom = {paddingTop:'0'};
							} else {
								makeRoom = {marginLeft:'113px'};
								hideRoom = {marginLeft:'0'};
							}
							$('.menu-item-wrap',context).animate(makeRoom,650,'easeOutBounce',function(){
								$(this).css(hideRoom);
								newMenuItem.fadeIn();
							});

						} else {
							this.error();
						}
					},
					error: function(){
						newMenuItem.remove();
						ma.resetUI(context);
						ma.errorMsg('Error adding new link.',context);
					}
				});
				return false;
			});
		},


		resetUI: function(context){
			$('.droppable',context).droppable('destroy');
			ma.setupMarkupCss(context);
			ma.setupDragDrop(context);
		},


		recordNewStructure: function(context){
			var done = {};
			var buildNode = function(group){
				var obj = {};
				group.each(function(){
					if ( done[$(this).attr('id')] !== true ) {
						done[$(this).attr('id')] = true;
						if ( $('.menu-item',$(this)).length ) {
							obj[$(this).attr('id')] = buildNode($('.menu-item',$(this)));
						} else {
							obj[$(this).attr('id')] = $(this).attr('id');
						}
					}
				});
				return obj;
			};
			$.ajax({
				type: 'POST',
				url: ajaxurl+'?action=pp',
				data: {
					update_menu_structure: true,
					menu_id: context.attr('rel') + '_structure',
					new_structure: ma.objectToString(buildNode($('.menu-item-wrap .menu-item',context)))
				},
				success: function(response){
					if ( response.indexOf( 'menu structure recorded' ) === -1 ) {
						this.error();
					}
				},
				error: function(){
					ma.errorMsg('Error updating menu structure.',context);
				}
			});
		},


		errorMsg: function(txt,context){
			$('.menu-warn',context).show().find('span').text(txt).parent().delay(7000).fadeOut();
		},


		setupMarkupCss: function(context){
			$('.menu-item',context).css({
				marginRight: '0',
				marginLeft: '0',
				marginTop: '0'
			});
			$('.menu-item .menu-item',context).css({
				marginTop: '0',
				marginLeft: '26px',
				marginRight: '10px'
			});
			$('.inbetween',context).remove();
			var wrapWidth = 0;
			$('.menu-item-wrap .menu-item',context).each(function(){
				if ( $(this).parent().hasClass('menu-item-wrap') ) {
					$(this).addClass('top-level');
					$(this).css('background-color','#eaeaea')
					wrapWidth += $(this).width() + 15;
				} else {
					$(this).removeClass('top-level');
				}
				if ( $('.menu-item', $(this)).length ) {
					$(this).addClass('has-children');
				} else {
					$(this).removeClass('has-children');
				}
				if ( !$(this).prev().hasClass('inbetween') ) {
					$(this).before(ma.dropzone);
				}
				if ( !$(this).next().hasClass('inbetween') ) {
					$(this).after(ma.dropzone);
				}
			});
			$('.droppable',context).removeClass('droppable-hovered');
			wrapWidth += 125;
			$('.menu-item-wrap',context).width(wrapWidth).css('width',wrapWidth+'px');

			if ( ma.isVertical(context) ) {
				$('.top-level:first',context).prev().height(30).css('height','30px');
				$('.top-level:last',context).next().height(35).css('height','35px');
			} else {
				$('.top-level:last',context).next().width(80).css('width','80px');
			}

			if ( !/mobile_/.test( context.selector ) ) {

				if ( $('.menu-item-wrap .menu-item',context).length ) {
					$('.option:not(".start-hidden")',context.parents('.subgroup')).show();
				} else {
					$('.option:not(":first")',context.parents('.subgroup')).hide();
				}
			}

			menuAlign.rebuildItems(context);
		},


		draggingClasses: function(item){
			return 'dragging dragging-' + item.attr('type') + ' dragging-' + item.attr('subtype');
		},


		setupDragDrop: function(context){
			$('.menu-item-wrap .draggable',context).draggable({
				revert: 'invalid',
				helper: 'clone',
				containment: 'document',
				revertDuration: 100,
				opacity: 0.45,
				delay: 150,
				start: function(e,ui){
					$('.menu-item-wrap',context).addClass(ma.draggingClasses(ui.helper));
					$('#'+ui.helper.attr('id'),context).addClass('ui-dragged-from');
				},
				stop: function(e,ui){
					$('.menu-item-wrap',context).removeClass(ma.draggingClasses(ui.helper));
					$('#'+ui.helper.attr('id'),context).removeClass('ui-dragged-from');
				}
			});

			$('.menu-item-wrap .droppable',context).droppable({

				tolerance: 'pointer',

				drop: function( event, ui ) {
					ui.helper.remove();
					$(this).before(ui.draggable);
					ma.recordNewStructure(context);
					ma.resetUI(context);
				},


				over: function( event, ui ){
					var next, prev, cursor;
					next = $(this).next();
					prev = $(this).prev();
					if ( prev.attr('id') == ui.draggable.attr('id') || next.attr('id') == ui.draggable.attr('id') ) {
						return;
					}

					$(this).addClass('droppable-hovered');

					if ( next.hasClass( 'menu-item' ) ) {
						if ( $(this).parents('.menu-item').length || ma.isVertical(context) ) {
							next.animate({marginTop:'12px'},50);
						} else {
							next.animate({marginLeft:'12px'},50);
						}
					}

					if ( $(this).hasClass('drop-nested') ) {
						cursor = 'crosshair';
					} else {
						cursor = $(this).parents('.menu-item').length ? 'row-resize' : 'col-resize';
					}
					ui.helper.css('cursor',cursor).find('h3').css('cursor',cursor).css('background-color','#5DA7B3');
				},


				out: function( event, ui ){
					$(this).removeClass('droppable-hovered');
					var next = $(this).next();
					if ( next.hasClass( 'menu-item') ) {
						if ( $(this).parents('.menu-item').length || ma.isVertical(context)  ) {
							setTimeout(function(){
								next.animate({marginTop:'0'},100);
							},500);
						} else {
							setTimeout(function(){
								next.animate({marginLeft:'0'},100);
							},500);
						}

					}
					ui.helper.css('cursor','move').find('h3').css('cursor','move').css('background-color','#999');
				}
			});
		},


		objectToString: function(o) {
			var parse = function( _o ) {
				var a = [], t;
				for ( var p in _o ) {
					if ( _o.hasOwnProperty( p ) ) {
						t = _o[p];
						if ( t && typeof t == "object" ) {
							a[a.length]= "\"" + p + "\":{ " + arguments.callee(t).join(",") + "}";
						} else {
							if ( typeof t == "string" ) {
								a[a.length] = [ "\"" + p + "\":\"" + t.toString() + "\"" ];
							} else {
								a[a.length] = [ "\"" + p + "\":" + t.toString()];
							}
						}
					}
				}
				return a;
			}
			return "{" + parse(o).join(", ") + "}";
		},


		/* http://bugs.$ui.com/ticket/4333#comment:18 */
		IEFix: function(){
			$.extend($.ui.draggable.prototype, (function (orig) {
			  return {
			    _mouseCapture: function (event) {
			      var result = orig.call(this, event);
			      if (result && $.browser.msie) event.stopPropagation();
			      return result;
			    }
			  };
			})($.ui.draggable.prototype["_mouseCapture"]));
		}

	};

	menuAdmin.init( 'primary_nav_menu' );
	menuAdmin.init( 'secondary_nav_menu' );
	menuAdmin.init( 'widget_menu_1' );
	menuAdmin.init( 'widget_menu_2' );
	menuAdmin.init( 'widget_menu_3' );
	menuAdmin.init( 'mobile_nav_menu' );




	var widgetMenuUI = {

		init: function(){
			$('#widget_menu_1_li_list_style-option-section,#widget_menu_2_li_list_style-option-section').each(function(){
				var section = $(this);
				$('select',this).each(function(){
					$(this).change(function(){
						var correspondingImgUpload = $('.mini-img-wrap',section).eq($('select',section).index($(this))).parent();
						if ( $(this).val() == 'image' ) {
							correspondingImgUpload.show();
						} else {
							correspondingImgUpload.hide();
						}
					}).change();
				});
			});
		}

	};

	widgetMenuUI.init();


});