jQuery(document).ready(function() {
    jQuery('input#siteurl, input#home').click(function() {
        jQuery('input#siteurl, input#home').css('border', 'solid red 1px');
        if (!jQuery('#siteurl-change-warn').length) {
			jQuery('input#siteurl').parents('tr').before('<tr id=\"siteurl-change-warn\"><td colspan=\"2\"><p>Changing these values significantly <strong>without also making changes through FTP</strong> could cause your site to become <strong>inaccessible</strong>. If you are just adding or removing a "www" then you can ignore this warning. Be sure to fully read our <a href=\"' + ajaxurl + '?action=pp&pptut=changeBlogAddress\">tutorial here</a> before making any other types of changes.</p></td></tr>');
		}
    });
});