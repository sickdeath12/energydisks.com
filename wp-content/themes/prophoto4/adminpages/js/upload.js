/* Scripts for the Options page : upload functions only */

// what: url, where: newImg.id


function p4_hide_reset_button(newImg) {
	jQuery('#'+newImg.id+' a.delete-uploaded-file-btn').hide();
}
