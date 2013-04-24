jQuery(document).ready(function($){

	var mie = {};

	var menuItemEdit = {


		init: function(){
			mie = this;

			mie.newItemProcess();
			mie.tabify();

			mie.setupValidateForm();
			mie.onChangesSaved();
			mie.gallerySelect();

			mie.onTypeChange();
			mie.onInternalTypeChange();
			mie.onSpecialTypeChange();
			mie.onAnchorChange();

			$('.type-radio-btns-wrap input:checked').click();

			mie.closeEvents();
		},


		setupValidateForm: function(){
			$('#edit-menu-item-form').submit(function(){
				if ( mie.wrap.hasClass('url-optional') ) {
					mie.enterURL.val('');
				}
				return mie.validateForm();
			});
		},


		validateForm: function(){
			var valid = true;

			// no main "TYPE" selected
			if ( !$('.type-radio-btns-wrap input:checked').length ) {
				$('.type-radio-btns-wrap').addClass('validate-fail');
				if ( !$('.vf-msg').length ) {
					$('.type-radio-btns-wrap').prepend('<span class="vf-msg">make a selection</span>');
				}
				valid = false;

			} else {
				if ( mie.type() == 'internal' ) {

					// blank "Page" ID
					if ( mie.internalType() == 'page' && $('select[name=pageID]').val() === '' ) {
						$('select[name=pageID]').addClass('validate-fail').change(function(){
							if ( $(this).val() !== '' ) {
								$(this).removeClass('validate-fail');
							}
						});
						valid = false;

					// blank ProPhoto gallery display method
					} else if ( mie.internalType() == 'gallery' && $('select[name="galleryDisplay"]').val() === '' ) {
						$('select[name="galleryDisplay"]').addClass('validate-fail').change(function(){
							if ( $(this).val() !== '' ) {
								$(this).removeClass('validate-fail');
							}
						});
						valid = false;

					// blank Category name for specific category
					} else if ( mie.internalType() == 'category' && $('select[name=categoryName]').val() === '' ) {
						$('select[name=categoryName]').addClass('validate-fail').change(function(){
							if ( $(this).val() !== '' ) {
								$(this).removeClass('validate-fail');
							}
						});
						valid = false;
					}

				}
			}

			if ( !valid ) {
				/* scroll to top to ensure warning msg seen */
				$('html,body').animate( { scrollTop:0 }, 50 );
				mie.warn('Please address the highlighted areas.');
			}
			return valid;
		},


		newItemProcess: function(){
			if ( $('.type-radio-btns-wrap input:checked').length ) {
				return;
			}
			mie.wrap.addClass('new-item');
			mie.enterText.css('opacity',0.75).attr('disabled',true);
			$('.type-radio-btns-wrap .radio-btn-wrap').hover(function(){
				if ( $('.type-radio-btns-wrap').hasClass('validate-fail') ) {
					return;
				}
				$('#link-type-blurbs-wrap p').hide();
				$('#blurb-'+$('input',$(this)).val()).fadeIn('fast');
				$('label',$(this)).addClass('highlight');
			},function(){
				$('#link-type-blurbs-wrap p').fadeOut('fast');
				$('label',$(this)).removeClass('highlight');
			});
			$('.type-radio-btns-wrap input').click(function(){
				mie.wrap.removeClass('new-item');
				mie.enterText.css('opacity',1).removeAttr('disabled');

			});
		},


		onTypeChange: function(){
			$('.type-radio-btns-wrap input').click(function(){
				$('.type-radio-btns-wrap').removeClass('validate-fail');
				$('.dependent-option-group').hide();
				$('#type-dependent-'+$(this).val()).css('display','block');
				$('.edit-menu-item-wrap')
					.removeClass('type-container type-internal type-manual type-special type-empty')
					.addClass('type-'+$(this).val());
				switch ( $(this).val() ) {
					case 'container':
						mie.usuals.show();
						mie.URLOptional();
						break;
					case 'internal':
						$('select[name=internalType]').change();
						break;
					case 'manual':
						mie.usuals.show();
						mie.URLRequired();
						break;
					case 'special':
						$('select[name=specialType]').change();
						break;
				}
			});
		},


		onInternalTypeChange: function(){
			$('select[name=internalType]').change(function(){
				mie.usuals.show();
				mie.URLOptional();
				$('#type-dependent-internal').find('select,div').not($('select[name=internalType],label[for=internalType]')).hide();
				switch ( $(this).val() ) {
					case 'page':
						$('select[name=pageID],.pageloadmethod-radio-btns-wrap,.pageloadmethod-radio-btns-wrap div').show();
						mie.enterURL.hide();
						break;
					case 'pages':
						mie.usuals.show();
						$('#exclude-pages-wrap').show();
						break;
					case 'category':
						$('select[name=categoryName]').show();
						mie.enterURL.hide();
						break;
					case 'archives':
						$('#archives-nest-wrap').show();
						break;
					case 'rss':
						mie.enterURL.hide();
						break;
					case 'recent_posts':
						$('#recent-posts-num-wrap').show();
						break;
					case 'home':
						mie.enterURL.hide();
						break;
					case 'gallery':
						mie.enterURL.hide();
						mie.setTarget.hide();
						$('select[name=galleryDisplay]').show();
						if ( !$('.gallery-preview').length ) {
							$('#load-galleries,.gallery-preview').show();
							$.ajax({
								type: 'GET',
								url: ajaxurl+'?action=pp&load_menu_gallery_previews=1',
								success: function(response){
									if ( response !== '' ) {
										$('#load-galleries').html(response);
										var select = function(selected){
											selected.addClass('selected-gallery-preview').find('input').attr('checked','checked').parent().click();
										};
										if ( $('#gal-id-'+$('input[name=galleryID]').val()).length ) {
											select($('#gal-id-'+$('input[name=galleryID]').val()));
										} else {
											select($('.gallery-preview:first'));
										}
									} else {
										$('#loading-throbber').html('You have not created any galleries yet.');
									}
								},
								error: function(){
									$('#loading-throbber').html('Error loading galleries');
								}
							});
						} else {
							$('#load-galleries,.gallery-preview').show();
						}
						break;
				}
			});
		},


		onSpecialTypeChange: function(){
			$('select[name=specialType]').change(function(){
				var types = [];
				$('option',this).each(function(){
					types.push( 'special-type-' + $(this).val() );
				});
				$('.edit-menu-item-wrap').removeClass(types.join(' ')).addClass('special-type-'+$(this).val());
				mie.URLOptional();
				mie.usuals.show();
				$('#type-dependent-special').find('select,div,input,label').not($('select[name=specialType],label[for=specialType]')).hide();
				switch ( $(this).val() ) {
					case 'email':
						$('input[name=email],label[for=email]').show();
						mie.setTarget.hide();
						mie.enterURL.hide();
						break;
					case 'twitter':
						$('input[name=twitterID],label[for=twitterID],#num-tweets-wrap,#num-tweets-wrap *').show();
						mie.enterURL.hide();
						break;
					case 'inline_search':
						$('input[name=searchBtnText],label[for=searchBtnText]').show();
						mie.usuals.hide();
						break;
					case 'dropdown_search':
						$('input[name=searchBtnText],label[for=searchBtnText]').show();
						mie.setTarget.hide();
						break;
					case 'subscribe_by_email':
						$('input[name=subscribeByEmailPrefill],label[for=subscribeByEmailPrefill]').show();
						$('input[name=subscribeByEmailBtnText],label[for=subscribeByEmailBtnText]').show();
						mie.usuals.hide();
						break;
					case 'show_bio':
					case 'show_contact_form':
						mie.setTarget.hide();
						mie.enterURL.hide();
						break;
					case 'show_custom_html':
						$('#custom-html-wrap,.label-for-customhtml').show();
						mie.setTarget.hide();
						mie.enterURL.hide();
						break;
					case 'call_telephone':
						$('input[name=telephoneNumber],label[for=telephoneNumber]').show();
						mie.enterURL.hide();
						break;
				}
			});
		},


		onAnchorChange: function(){
			var imgBox   = $('.upload-box:first');
			var iconOpts = $('.upload-box:last,.iconalign-radio-btns-wrap,label[for=iconAlign],.label-for-checkbox-iconconstrained');
			$('.anchor-radio-btns-wrap input').click(function() {
				imgBox.hide();
				iconOpts.hide();
				if ( $(this).val() == 'img' ) {
					imgBox.show();
				} else if ( $(this).val() == 'text_and_icon' ) {
					iconOpts.show();
				}
			});
			$('.anchor-radio-btns-wrap input:checked').click();
		},


		gallerySelect: function(){
			$('.gallery-preview').live('click',function(){
				$('.gallery-preview input').removeAttr('checked');
				$('input',$(this)).attr('checked','checked');
				$('.gallery-preview').removeClass('selected-gallery-preview');
				$(this).addClass('selected-gallery-preview');
				$('input[name=galleryID]').val($(this).attr('id').replace('gal-id-',''));
			});
		},


		onChangesSaved: function(){
			if ( !$("span:contains('Link updated.')").length ) {
				return;
			}
			$("span:contains('Link updated.')").parent().delay(6000).fadeOut();

			var linkText = $('input[name=text]').val();
			var menuID   = $('input[name=menu_item_id]').val();
			var setClass = $('.edit-menu-item-wrap').hasClass('has-own-children') ? 'addClass' : 'removeClass';

			if ( mie.type() == 'special' ) {
				if ( mie.specialType() == 'inline_search' ) {
					linkText = ( $('input[name=searchBtnText]').val() !== '' ) ? $('input[name=searchBtnText]').val() : 'Search';
				} else if ( mie.specialType() == 'subscribe_by_email' ) {
					linkText = ( $('input[name=subscribeByEmailBtnText]').val() !== '' ) ? $('input[name=subscribeByEmailBtnText]').val() : 'Subscribe by email';
				}
			}

			try {
				var item = parent.jQuery('.menu-item-wrap #'+menuID);
				if ( linkText !== '' ) {
					item.find('h3:first').text(linkText);
				}

				item.removeClass('manual internal container special');
				item.addClass(mie.type()).attr('type',mie.type());
				item.attr('subtype',mie.subType());

				var context = item.parents('.menu-admin-wrap');
				parent.menuAlign.rebuildItems(context);

				item[setClass]('has-own-children');
				if ( item.hasClass('has-own-children') ) {
					item.find('.menu-item').insertAfter(item);
					parent.menuAdmin.recordNewStructure(context);
					parent.menuAdmin.resetUI(context);
				}
			}
			catch(e){}
		},


		closeEvents: function(){
			var closeIFrame = function(){
				if (changesMade) {
					if ( !confirm('You have unsaved changes. Close without saving?') ) {
						return false;
					}
				}
				try{parent.tb_remove();}catch(e){}
			};
			var changesMade = false;
			$('.radio-btns-wrap input').click(function(){
				changesMade = true;
			});
			$('input,select').change(function(){
				changesMade = true;
			});
			$('#done-editing').click(function(){
				closeIFrame();
				return false;
			});
			try {
				parent.jQuery('#TB_closeWindowButton,#TB_overlay').unbind('click').click(function(){closeIFrame();});
			}
			catch(e){}
		},


		URLOptional: function(){
			if ( mie.URLInput.val() === '' ) {
				mie.wrap.removeClass('url-required');
				mie.wrap.addClass('url-optional');
				mie.enterURL.val(mie.optURLMsg);
				mie.URLInput.click(function(){
					$(this).val('');
					mie.URLRequired();
				});
			}
		},


		URLRequired: function(){
			mie.wrap.removeClass('url-optional');
			mie.wrap.addClass('url-required');
			mie.URLInput.unbind('click');
			if ( mie.URLInput.val() == mie.optURLMsg ) {
				mie.URLInput.val('');
			}
		},


		tabify: function(){
			$('#jquery-tabs').tabs({
				select: function(event,ui){
					if ( ui.tab.hash == '#link-display' ) {
						$('.anchor-radio-btns-wrap input:checked').click();
					}
					return mie.validateForm();
				}
			});
			$('.edit-menu-item-wrap').attr('title','');
		},


		type: function(){
			if ( !$('.type-radio-btns-wrap input:checked').length ) {
				return '';
			} else {
				return $('.type-radio-btns-wrap input:checked').val();
			}
		},


		internalType: function(){
			return $('select[name=internalType]').val();
		},


		specialType: function(){
			return $('select[name=specialType]').val();
		},


		subType: function(){
			if ( mie.type() == 'container' ) {
				return 'Container';
			} else if ( mie.type() == 'manual' ) {
				return 'ppMenuItem';
			} else if ( mie.type() == 'internal' ) {
				return ( mie.internalType() == 'recent_posts' ) ? 'RecentPosts' : mie.ucFirst( mie.internalType() );
			} else if ( mie.type() == 'special' ) {
				switch ( mie.specialType() ) {
					case 'email':
					case 'twitter':
						return mie.ucFirst(mie.specialType());
					case 'inline_search':
					case 'dropdown_search':
						return 'Search';
					case 'subscribe_by_email':
						return 'SubscribeByEmail';
					case 'show_contact_form':
						return 'ShowHidden';
				}
			}
			return '';
		},


		ucFirst: function(str) {
			str += '';
			var f = str.charAt(0).toUpperCase();
			return f + str.substr(1);
		},


		warn: function(txt){
			$('.gmail-warn').show().find('span').text(txt).parent().delay(9000).fadeOut();
		},


		optURLMsg : 'Optional for this link type - click to enter a URL',
		URLInput  : $('input[name=url]'),
		wrap      : $('.edit-menu-item-wrap'),
		enterURL  : $('label[for=url],input[name=url]'),
		enterText : $('label[for=text],input[name=text]'),
		setTarget : $('#target-wrap,label[for=target],select[name=target]'),
		usuals    : $('label[for=url],input[name=url],label[for=text],#target-wrap,input[name=text],label[for=target],select[name=target]')
	};


	menuItemEdit.init();
});
