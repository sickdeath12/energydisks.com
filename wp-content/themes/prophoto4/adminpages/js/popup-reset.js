/* Scripts for the pp_after_render_contents iframed popup */

// We're sending some information to the opening page (to scripts in upload.js)
function ppSendtopage(url, shortname, width, height, size) {
	var win = window.opener ? window.opener : window.dialogArguments;
	if (!win) win = top;
	win.pp_updateImgUploadBox(url, shortname, width, height, size, false);
}
