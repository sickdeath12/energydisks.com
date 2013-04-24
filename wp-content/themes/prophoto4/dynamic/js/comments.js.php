<?php

if ( !ppOpt::test( 'comments_enable', 'true' ) ) {
	return;
}

$cmt_border_width = ppOpt::id( 'comment_bottom_border_width' );
$jsCode .= <<<JAVASCRIPT
function ppFormatCmtMargins() {
	jQuery('.comments-body div.pp-comment p').not(':last-child').css('margin-bottom', '.6em');
	jQuery('.comments-body div.pp-comment').not(':last-child').css('border-bottom-width', '{$cmt_border_width}');
	jQuery('.comments-body div.pp-comment:last-child').addClass('last-comment');
}
JAVASCRIPT;


$cancel_reply    = ppOpt::id( 'translate_comments_cancel_reply' );
$ajax_override   = ppOpt::test( 'comments_ajax_adding_enabled', 'false' ) ? 'return true; // ajax override' : '';
$added_comment   = ppOpt::test( 'reverse_comments', 'false' ) ? 'last' : 'first';
$addCommentError = json_encode( ppOpt::translate( 'comment_form_error_message' ) );

$jsCode .=  <<<JAVASCRIPT
function ppAjaxAddComment() {

	jQuery('.addacomment a').click(function(){

		$ajax_override

		// data about comment area, passed to callbacks
		var c = new Object;
		c.clicked_btn  = jQuery(this);
		c.permalink    = c.clicked_btn.attr('href');
		c.section      = c.clicked_btn.parents('.article-comments');
		c.form_holder  = c.section.next();
		c.form_load_url     = c.permalink.replace('#', ' #');
		c.comments_load_url = c.permalink.replace('#addcomment', ' .comments-body-inner');
		c.count_load_url    = c.permalink.replace('#addcomment', ' .comments-count');

		// form already loaded, show it
		if ( jQuery('.add-comment-form-wrap', c.form_holder).length ) {
			c.form_holder.slideDown();

		// ajax load comment submission form
		} else {
			ppThrob.start(c.clicked_btn);
			c.form_holder.load(c.form_load_url, function(){
				ppCommentFormAjaxLoaded(c);
			});
		}

		return false; // disable normal click behavior
	});
}


/* when contact form is loaded via ajax: show form and bind ajax submit to it */
function ppCommentFormAjaxLoaded(c) {
	c.form = jQuery('form', c.form_holder);
	ppThrob.stop(c.clicked_btn);

	// add cancel reply button
	jQuery('input#submit', c.form)
		.after('<input class="cancel-reply" type="submit" value="{$cancel_reply}" />')
		.next()
		.click(function(){
			c.form_holder.slideUp();
			return false;
		});

	// show form
	c.form_holder.slideDown();

	// bind an ajax form submission
	c.form.submit(function(){
		ppCommentFormAjaxSubmit(c);
		return false; // disable normal submission of form
	});
}


/* our custom ajax hijack of comment form submission */
function ppCommentFormAjaxSubmit(c) {
	ppThrob.start(jQuery('#submit', c.form));
	jQuery.ajax({
		type: "POST",
		url: c.form.attr('action'),
		data: c.form.serialize(),
		timeout: 6000,
		success: function(){
			ppCommentAjaxUpdate(c);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			setTimeout( function(){
				var comment = encodeURIComponent(jQuery('textarea', c.form).val());
				window.location.href = c.permalink+'-error-'+comment;
			}, 500 );
			// try to log error
			jQuery.post( prophoto_info.url+'/', {
				'ajax_comment_error' : '1',
				'XMLHttpRequest' : XMLHttpRequest,
				'textStatus' : textStatus,
				'errorThrown' : errorThrown
			});
		}
	});
}


/* after commented submitted via ajax: update the comment area accordingly */
function ppCommentAjaxUpdate(c) {
	// reload and show the comments area via ajax, also hide comment form
	c.section.children('.comments-body').load(c.comments_load_url, function(){

		ppFormatCmtMargins();

		// update the comments count
		jQuery('.comments-count', c.section).load(c.count_load_url);

		// show the comments, including new one, scrolled to bottom
		jQuery(this).slideDown(function(){

			// scroll to bottom of comment section
			jQuery(this).attr({scrollTop:jQuery(this).attr('scrollHeight')});

			// throb the new comment for two seconds
			ppThrob.start(jQuery('.comments-body-inner .pp-comment:$added_comment', c.section));
			setTimeout(function(){
				ppThrob.stop(jQuery('.comments-body-inner .pp-comment:$added_comment', c.section));
			}, 2000);

			// hide and remove comment form
			c.form_holder.slideUp(function(){
				jQuery('.add-comment-form-wrap', this).remove();
			});
		});

		// set the comment count to active state
		jQuery(c.section).addClass('comments-shown');

	});
}


/* handle redirects from ajax comment submission errors */
function ppAjaxCmtSubmitError() {
	if ( /#addcomment-error-/.test(window.location.hash) == false ) return;
	var comment = window.location.hash.replace('#addcomment-error-', '');
	jQuery('textarea').focus().val(decodeURIComponent(comment.replace(/%0A/g, "\\n")));
	jQuery('#addcomment-error').show().find('span').text($addCommentError);
	jQuery(window).scrollTop(jQuery('#addcomment').offset().top);
}

ppAjaxAddComment();
ppAjaxCmtSubmitError();
ppFormatCmtMargins();




JAVASCRIPT;




if ( !ppOpt::test( 'comments_layout', 'boxy' ) ) {
	$jsCode .=  <<<JAVASCRIPT
	function ppShowHideComments() {
		jQuery('.comments-count').click(function(){
			var comments_section = jQuery(this).parents(".article-comments");
			if ( jQuery('.comments-body-inner div', comments_section).length ) {
				comments_section.toggleClass('comments-shown');
				jQuery('.comments-body', comments_section).slideToggle(400);
			}
		});
	}
	ppShowHideComments();
JAVASCRIPT;
}




if ( ppOpt::test( 'comments_layout', 'minima' ) ) {

	// set hover color
	$hover_color = ppOpt::cascade(
		'comments_header_link_hover_font_color',
		'comments_header_link_font_color',
		'gen_link_hover_font_color',
		'gen_link_font_color'
	);

	// set decoration style
	if ( ppOpt::test('comments_header_link_hover_decoration' ) ) {
		$decoration = ppOpt::id( 'comments_header_link_hover_decoration' );
	} else if ( ppOpt::test('gen_link_hover_decoration' ) ) {
		$decoration = ppOpt::id( 'gen_link_hover_decoration' );
	} else {
		$decoration = false;
	}

	$new_dec = ( $decoration ) ? "'$decoration'" : 'old_dec';

	$jsCode .= "\n\n" . <<<JAVASCRIPT
	function ppMinimaCountHover() {
		jQuery('.comments-count p:not(.no-comments)').mouseover(function(){
			old_hover = jQuery(this).css('color');
			old_dec = jQuery(this).css('text-decoration');
			jQuery(this).css('color', '$hover_color' );
			jQuery(this).css('text-decoration', $new_dec);
		}).mouseout(function(){
			jQuery(this).css('color', old_hover);
			jQuery(this).css('text-decoration', old_dec);
		});
	}
	ppMinimaCountHover();
JAVASCRIPT;
}



