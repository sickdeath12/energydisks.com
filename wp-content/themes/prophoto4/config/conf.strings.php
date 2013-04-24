<?php

$filePerms = "<br /><br />If you're not sure how to change permissions, <a target='_blank' href='" . pp::tut()->changePermissions . "'>see this tutorial</a>.";
$ftpTut = "<br /><br />More information about using FTP can be <a target='_blank' href='" . pp::tut()->ftp . "'>found here</a>.";
$clickReloadParent = ' href="" onclick="javascript:var win = window.dialogArguments || opener || parent || top;win.location.href=win.location.href; return false;"';
$removeImportingMsg = '<script type="text/javascript" charset="utf-8">document.getElementById("importing").style.display = "none";</script>';


$configArray = array(


	'options_updated' => 'Options updated.',


	'fb_comments_requires_fb_admins' =>

		'To use Facebook comments, you must first enter your <em>Facebook personal numeric ID</em> on <a href="' . ppUtil::customizeURL( 'settings', 'social_media' ) . '">this page</a>.',



	'editing_single_widget' =>

		'You are currently <b>editing a single widget</b>. To view, edit, add and remove all widget types from all areas, <a href="' . admin_url( 'widgets.php' ) . '">click here</a>.',



	'activation_design_desc' =>

		'This design was created automatically for you as your starting design when you activated ProPhoto. Any design changes you\'ve made or images you have uploaded already are already saved to this layout. You can change the title or this description by clicking the "Edit" button below, or start a new design if you choose.',



	'dns_resolution_problem' =>

		'ProPhoto is <b>unable to update itself</b> to the latest version because of a problem with your web-host\'s DNS configuration, which is causing our site, <code>http://www.prophotoblogs.com/</code> to not be reachable through an HTTP request from your server. Please contact your web-host tech support and ask them to resolve the issue.',




	'non_webfont_kit_uploaded' =>

		'Only a "Squirrel Font Webfont Kit" zip file may be uploaded here.',




	'what_is_grid' =>

		'ProPhoto Grids allow you to insert customized "grids" of image-based links to various types of content -- like recent posts, galleries, categories, and selected posts/pages.',



	'attempting_auto_upgrade' =>

		'An <b>updated version of ProPhoto is available</b>. Please wait while ProPhoto theme attempts to update itself automatically. This can take as much as 90 seconds. Do not use your browser\'s back button or visit another page in your admin area while the update is being processed',




	'auto_upgrade_success' =>

		'ProPhoto downloaded a newer version and was able to successfully update itself to <b>build #<span></span></b>.',




	'auto_upgrade_timeout' =>

		'ProPhoto was <b>unable to complete the automatic update</b> process.  This can simply be due to network traffic or site availability.  ProPhoto will attempt to update again soon.  Only if you have seen this same error message several times in a row, <a href="' . pp::tut()->contactUs . '">contact us</a> through our website for help.',




	'auto_upgrade_bad_response' =>

		'Sorry, <b>an error occurred</b> while ProPhoto was attempting to update your blog automatically. Please click <a href="' . ppUpgrader::updateUrl() . '">this link</a> to attempt the update again.  If you encounter any errors, <a href="' . pp::tut()->contactUs . '">contact us</a> through our website with the details of the error so we can resolve it as soon as possible.',




	'upgrade_available' =>

		'There is a <b>free update</b> required for ProPhoto. You can download the update and get instructions by <a target="_blank" href="' . PROPHOTO_SITE_URL . 'download/?payer_email=%1&txn_id=%2&update_product=prophoto4&occasion=bump_%3">clicking here</a>.',




	'p3_design_import_success' =>

		'Your ProPhoto version 3 design/s imported successfully and can be found in the <b>inactive designs</b> section.',




	'design_import_success' =>

		'Design: <b>%1</b> was imported successfully and can be found in the <b>inactive designs</b> section.',




	'design_activated' =>

		'Design: <b>%1</b> was designated the active design.',




	'design_activated_widgets_deactivated' =>

		'Design: <b>%1</b> was designated the active design.<span style="display:block;height;height:7px;"></span><em><b>PLEASE NOTE:</b></em> one or more of your <em>existing widgets were removed</em> because you activated a starter design that contained widgets in the same area.  Should you wish to restore any of these widgets, they can be found in the "Inactive Widgets" section of your widgets page.',




	'design_deleted' =>

		'Design was successfully deleted.',




	'all_designs_reset' =>

		'All stored design data was deleted, and ProPhoto was reinitialized.',




	'design_meta_updated' =>

		'Design: <b>%1</b> was updated successfully.',




	'new_design_created' =>

		'A new design named <b>%1</b> was created and can be found in the inactive designs section.',




	'upload_error_ini_size' =>

		'Upload failed because your web-host has restricted uploads to maximum size of  <code>' . ini_get( 'upload_max_filesize' ) . '</code>. Please try again with a smaller file.',




	'missing_php_temp_dir' =>

		'Upload failed because your web-hosting server is missing a temporary folder for PHP to upload to. Please contact your web-hosting company\'s technical support for assistance.',




	'php_disk_write_error' =>

		'Upload failed because your web-hosting server disk could not be written to. This is most often caused when you exceed your hosting disk-space allowance.  Please contact your web-hosting company\'s technical support for assistance.',




	'only_img_types_allowed' =>

		'Only <code>.jpg</code>, <code>.gif</code>, or <code>.png</code> image file types are accepted. Please convert the image to one of those formats and re-upload.',



	'only_swf_file_allowed' =>

		'Only <code>.swf</code> files are accepted. Please upload a <code>.swf</code> file or do not use this option.',



	'only_mp3_file_allowed' =>

			'Only <code>.mp3</code> files are accepted. Please upload a <code>.mp3</code> file or do not use this option.',




	'backup_remind_email' =>

		"Hello, this is a friendly reminder from the ProPhoto theme that now would be a good time to <strong>back up your blog</strong> at this address:<br /><br />" . pp::site()->url . "<br /><br />We recommend doing a <strong>complete backup</strong> of your blog, including the database <strong>and</strong> files, once a month. Remember, as the owner of a self-hosted WordPress blog, <strong>you're responsible for your own data</strong> in case something goes wrong.<br /><br /><strong>How do I back up?</strong><br /><br />If you're looking for an in-depth tutorial on how to backup your blog, just <a href='" . pp::tut()->backupBlog . "'>click here</a>.<br /><br />This email is generated automatically each month to remind you to perform a backup. If you would like to <strong>stop receiving these emails</strong>, you can turn off this feature on <a href='" . ppUtil::customizeURL( 'settings', 'settings' ) . "'>this page</a> of your blog's admin area.",




	'wp_password_protect_phrase' =>

		'This post is password protected. To view it please enter your password below:',




	'no_postid_for_gallery' =>

		'<style type="text/css">html{overflow:hidden;}</style><p style="padding:20px">Oops! ProPhoto can\'t tell what post this is, because WordPress has not assigned it an ID yet. Close this window and save your post as a draft, and then come back here. Thanks!</p>',




	'no_available_p4_imports' =>

		'Sorry, but ProPhoto couldn\'t find any export files in your <tt>p4/<strong>images</strong></tt> folder.  See <a target="_blank" href="' . pp::tut()->importDesign . '">this tutorial</a> for information on how to import an exported design from ProPhoto4.',




	'zip_design_success' =>

		"<h3>Export Design</h3><p>A zip file of all of the settings and images associated with the <strong>\"%1\"</strong> design was successfully created. You can download it by <a target='_self' href='%2'>clicking here</a>.</p>",




	'zip_everything_success' =>

		"<h3>Export Everything</h3><p>A zip file of all of the settings and images associated with the ProPhoto theme was successfully created. You can download it by <a target='_self' href='%1'>clicking here</a>.</p>",




	'cant_create_export_txt_file' =>

		"<h3>Design Export Problem</h3><p><strong>Oops</strong> -- ProPhoto was not able to export your settings, most likely because of a server permissions problem. <br /><br />To fix it, use your FTP program to change the permissions of the <strong>%1</strong> folder to 777 and then try again. <br /><br />The server path to that folder is <tt>%2</tt>." . $filePerms . '</p>',




	'txt_success_zip_failure' =>

		"ProPhoto was able to write your design settings to an export file, which you can download by <strong>right-clicking</strong> (or control-clicking) on the link below and choosing 'Save Target As' or 'Save Link As': <br /><br /><a href='%1'>Export file</a><br /><br /><strong>However...</strong> For some reason, however, ProPhoto was <strong>unable to create a zip file containing this design's images</strong>, so should also use an FTP program to download all of the images in the <tt>%2</tt> directory. Or, you could try disabling all of your plugins and then attempting again." . $ftpTut,




	'cant_read_txt_export_file' =>

		'Sorry, but for some reason ProPhoto was not able to read the export file: <tt>%1</tt>. You might try setting the file permissions to 777 or checking to see that you uploaded it correctly.' . $filePerms,




	'blurb_menu_spacing' =>

		'In this area you can override the default spacing amounts in the %1 horizontal navigation menu area.  You can set the horizontal spacing between items, the vertical padding above and below menu items, and the spacing between the left and right edges of your site and your menu area.',




	'blurb_menu_custom_lines' =>

		'In this customization area you can choose to display custom decorative lines above and/or below your %1 horizontal navigation menu for visual effect. These can be any color or width, and can be styled as solid, dashed, dotted, or double-lined.',



	'blurb_menu_onoff' =>

		'This switch allows you to show or hide the entire %1 horizontal main navigation menu for your site.  This is most often left set to "show", but it can be handy to use the "hide" option if you want to experiment with a layout or design without a %1 horizontal menu, but still save the custom menu structure you\'ve created here.',



	'blurb_widget_menu_location' =>

		'This vertical menu can be placed into any area of your site that accepts widgets. To do so, go to the <a href="' . admin_url( 'widgets.php' ) . '">Widgets Page</a> and drag a new <em>ProPhoto Vertical Nav Menu</em> widget into the desired area, and select <em>Vertical Nav Menu #%1</em> from the widget-customization form.',




	'blurb_widget_menu_levels' =>

		'First level menu items are the main, left-most, outer menu items, not nested inside of other items. Second level menu items are items nested inside of other menu items. Third level menu items are items nested inside of second-level items.',



	'import_non_design_warning' =>

		'When importing layouts, you can also choose to import the non-design-related settings that were exported at the same time.  Non-design-related setting are stored options that don\'t affect the blog\'s appearance--things like Google Analytics tracking code, Feedburner URL, and translated phrases. Any non-design-related settings imported if you check this will <strong>immediately become your active settings for those options</strong>.',




	'untitled_design_desc' =>

		'This design was created automatically for you as your starting design when you activated ProPhoto4.  Any design changes you\'ve made or images you have uploaded already are already saved to this layout.  You can change the title or this description by clicking the "Edit" button below, or start a new design if you choose.',




	'recommended_img_max_upload_width' =>

		'Based on your current <strong>ProPhoto theme layout choices</strong>, you should upload images no wider than <strong>%1px</strong>.',




	'clone_name_required' =>

		'<p style="color:red">You must enter a clone name.</p><style type="text/css" media="screen">input#design_name {border:red solid 1px;}</style>',




	'no_mp3s_for_audio_player' =>

		'<span style="background:#fff;color:red;">You must have at least one MP3 for the audio player to work.</span>',




	'create_dir_error' =>

		'Unable to create directory %1. Is its parent directory writable by the server?',




	'nested_theme_folder' =>

		'Your <strong>prophoto4</strong> theme appears to be installed incorrectly.  Please see <a href="' . pp::tut()->nestedThemeFolder . '">this page</a> for info on how to fix this.',




	'misnamed_theme_folder' =>

		'Your <strong>prophoto4</strong> theme appears to be installed incorrectly.  Please see <a href="' . pp::tut()->misnamedThemeFolder . '">this page</a> for info on how to fix this.',




	'cant_create_folder' =>

		'ProPhoto is unable to create a folder it needs. See <a target="_blank" href="%1">this page</a> for info on how to fix this.',




	'tell_jared_problem' =>

		'<strong>How embarrassing.</strong> ProPhoto encountered an unexpected problem. Please contact us through our <a href="' . pp::tut()->contactUs . '">support page</a>, and copy this error code into the message body: <code>P4 error in function: %1()</code>',




	'static_file_write_error' =>

		'ProPhoto had a problem writing your custom files. See <a href="' . pp::tut()->staticFilesNotWritten . '">this page</a> for info on how to fix this.',




	'dont_edit_theme_files' =>

		'You should <strong>never need to edit ProPhoto theme files</strong> using this page. We <strong>do not support</strong> questions or problems that arise from hand-editing theme files.  More info on why you shouldn\'t edit these files and how to accomplish what you want without editing them <a href="' . pp::tut()->editThemeFiles . '">can be found here</a>.',




	'wpurl_change_warning_js' =>

		"<script type=\"text/javascript\" charset=\"utf-8\">jQuery(document).ready(function(){jQuery('input#siteurl, input#home').click(function(){jQuery('input#siteurl, input#home').css('border', 'solid red 1px');if (!jQuery('#siteurl-change-warn').length) jQuery('input#siteurl').parents('tr').before('<tr id=\"siteurl-change-warn\"><td colspan=\"2\"><p>ProPhoto Users: Changing these values <strong>without also making changes through FTP</strong> will cause your site to become <strong>inaccessible</strong>. Be sure to fully read our <a href=\"" . pp::tut()->changeBlogAddress . "\">tutorial here</a> before making any changes.</p></td></tr>');});});</script>",




	'design_upload_import_success' =>

		$removeImportingMsg . 'Successfully imported. The imported design is called <strong>%1</strong>, and will be visible in the <em>Inactive designs</em> section after <a' . $clickReloadParent . '>clicking here</a> to reload the page.',




	'invalid_design_zip' =>

		'The file <strong><code>%1</code></strong> does not appear to be a valid ProPhoto4 design export zip.  Only .zip files created by the ProPhoto theme will work. Please try again.',




	'p2_layouts_imported' =>

		$removeImportingMsg . '<p>ProPhoto version 2 import process successful. All imported layouts will be visible in the <em>Inactive designs</em> section and available to be activated after <a' . $clickReloadParent . '>clicking here</a> to reload the page.</p>',




	'wp_version_not_supported' =>

		'<div style="text-align:center;width:500px;margin:50px auto;"><h1>WP Version Not Supported</h1><p>The ProPhoto4 theme requires at least WordPress version <strong>2.9</strong> or newer. Your blog is running version <span style="color:red;font-family: Courier, monospace;">%1</span>.</p><p>Click, or copy this link and paste it into your browser: <br /><a href="' . pp::tut()->wpVersionFail . '">' . pp::tut()->wpVersionFail . '</a> <br />to get lots of good information on upgrading your version of WordPress, then activate another theme until you\'ve upgraded.</p><p>Remember to <a href="' . pp::tut()->backupBlog . '">backup your blog</a> <em>AND</em> database before you upgrade!</p></div>',




	'db_backup_plugin_missing' =>

		'<strong>ProPhoto</strong> has detected that you are not running our recommended<strong> database backup plugin</strong>.  You alone are responsible for <strong>always having a recent backup of your database</strong> in case something goes wrong.  The plugin we recommend makes this extremely simple and automated.  <a href="' . pp::tut()->backupNag . '">Click here</a> for instructions on how to install the plugin and remove this notice.',




	'flash_gal_needs_pathfixer' =>

		'<div style="color:red;background:white;padding:3px 6px;">ProPhoto has encoutered a problem trying to display your flash gallery.  Usually this problem occurs when you have changed your blog address without turning on the <strong>Blog path fixer</strong> and entering the old address of your blog.  You can find that option on the bottom of <a href="%1" style="color:blue;text-decoration:underline">this page</a>. Don\'t worry, <strong>only you can see this message</strong> because you are logged in as an administrator.</div>',




	'requires_feedburner_feed' =>

		'In order to allow your blog reader to subscribe to your posts by email, you must burn your blog\'s feed to a <strong>Feedburner Feed</strong>.  You can do this by first setting up a Feedburner feed (<a href="' . pp::tut()->feedburnerFeed . '">tutorial</a>), and then going <a href="%1">here</a> to enter in your Feedburner URL.  Then come back here, and you will be able to use the Feedburner Subscribe-by-email functionality.',




	'requires_email_subscription_enabled' =>

		'<strong>NOTE:</strong> in order for this to work, you have to have <strong>email subscriptions enabled</strong> in your Feedburner feed account. Tutorial <a href="' . pp::tut()->enableSubscribeByEmail . '" target="_blank">here</a>.',




	'deactivate_p4_before_upgrade' =>

		'<p style="font-size:18px; text-align:center;margin-bottom:100px;width:660px;margin:0 auto 100px auto;"><span style="color:red;font-weight:bold">IMPORTANT:</span> You must <strong>de-activate ProPhoto4 before upgrading WordPress</strong>.  To do so, go to <em>"Appearance" => "Themes"</em> in the left sidebar, and click one of the "Activate" buttons below an available theme.<br /><br /><span style="font-size:15px;">After successfully upgrading WordPress, you may re-activate ProPhoto4.</span></p>',




	'register_globals_on' =>

		'<strong>IMPORTANT:</strong> Your webhost has a very is configured in a <strong>very insecure way</strong>.  This configuration can also cause a problem where whenever you try to save changes in the "P4 Customize" page, everything <strong>goes blank</strong> instead of correctly saving. To find out how to fix this, see <a href="' . pp::tut()->registerGlobals . '">this tutorial</a>.',




	'safe_mode_subfolder_problem' =>

		'ProPhoto has encountered a problem trying to create some folders it needs due to a setting on your web server. Steps you can take to resolve this issue can be <a href="' . pp::tut()->safeMode . '">found here</a>.',




	'use_akismet' =>

		'We highly recommend activating the plugin "Akismet" to catch <strong>comment spam</strong>. It\'s super easy to do, our tutorial <a href=' . pp::tut()->commentSpam . '>is here</a>.',




	'old_wp_version_hack_warning' =>

		'Hello, this is a notice from your ProPhoto blog that the <strong>version of WordPress</strong> you are currently running is <strong>dangerously out-of-date</strong>.<br /><br />The reason this is important is because there are serious security problems with old versions of WordPress that <strong>hackers can use to insert malicious code into your blog</strong>. The only way to ensure that you won\'t get hacked is to always keep your WordPress blog up to date with the latest version of WordPress.<br /><br />Upgrading is <strong>free and easy</strong>.  For most people it takes less than 30 seconds and just a few clicks of a mouse.  We have an easy-to-follow tutorial here:<br /><br />' . pp::tut()->upgradeWp . '<br /><br />If you do not upgrade soon, you have a very high chance of having your blog hacked.  A hacked blog can get you <strong>banned from Google, can bring down your website, and can cause you to lose your posts and comments.</strong> Unhacking a blog is time-consuming, frustrating, and often expensive.<br /><br />We strongly recommend that you take a few moments right now to <strong>upgrade your WordPress version.</strong>',




	'ftp_info_advise_core' =>

		'<p id="ftp-advise">The information being requested here is your <strong>web-host account FTP login</strong> info.  If you do not know what it is, contact your web-host tech support.  <strong>Do not contact ProPhoto</strong>, as we do not know your FTP info. If you <strong>can not get this to work</strong>, you will have to upgrade WordPress manually.  Follow our <a href="' . pp::tut()->upgradeWp . '">tutorial here</a> to do so.</p>',




	'ftp_info_advise_plugin' =>

		'<p id="ftp-advise">The information being requested here is your <strong>web-host account FTP login</strong> info.  If you do not know what it is, contact your web-host tech support.  <strong>Do not contact ProPhoto</strong>, as we do not know your FTP info. If you <strong>can not get this to work</strong>, you will have to upgrade or install the plugin manually.  Follow our <a href="' . pp::tut()->manualPluginInstall . '">tutorial here</a> to do so.</p>',




	'no_theme_options_found' =>

		'ProPhoto is having a problem reading your stored options. First, <strong>try refreshing this page</strong>, the problem may resolve on it\'s own.<br /><br />If it does not, and <strong>you have not made any customizations yet</strong>, you may force-reset ProPhoto by <a href="' . admin_url( 'admin.php?page=pp-designs&show_reset=true' ) . '">clicking here</a> and then clicking the "Reset Everything" button that appears under "Upload Design Zip".%1',




	'restore_from_backups' =>

		'<br /><br />If you <strong>have made customization changes</strong>, you may <a href="' . admin_url( 'admin.php?page=pp-designs&restore_from_backups=1&restore_most_recent=1' ) . '">restore a backup of your most recent saved changes</a>, or <a href="' .  admin_url( 'admin.php?page=pp-designs&restore_from_backups=1' ) . '">view all available backups</a> and choose which to restore.',




	'no_theme_options_found_comment' =>

		"\n\n\n\n<!--\n\nP4 THEME ERROR !!!!!!! \nNO THEME OPTIONS FOUND \nSEE ppReadOptions()\n\n-->\n\n\n\n\n",




	'backup_restored' =>

		'<strong>Backup successfully restored</strong>.  <a target="_blank" href="' . pp::site()->url .'">View your blog</a> to see if things look correct.  Or, you can try restoring a different backup.',




	'wpmu_warning' =>

		'ProPhoto <strong>does not work in multi-user mode</strong>.  You must use it in standard, single-user mode.',




	'non_utf8_encoding' =>

		'Your blog\'s character encoding must be set to <code>UTF-8</code> in the <a href="%1">Reading Settings area</a>.',




	'not_registered_msg' =>

		'This copy of ProPhoto may not be used because it <b>has not been registered</b>. <a href="' . admin_url( 'themes.php?activated=true' ) . '">Click here</a> to register.',




	'not_registered_admin_notice' =>

		'You must register your copy of ProPhoto before it can be used. <a href="' . admin_url( 'themes.php?activated=true' ) . '">Click here</a> to register.',




	'dev_test_mode_enabled' =>

		'<b>Test mode is enabled.</b> Only logged-in administrators can view the site. You may disable it <a href="' . ppUtil::customizeURL( 'settings', 'misc' ) . '">here</a>.',




	'link_removal_txn_id_not_found' =>

		'The <strong>Transaction ID</strong> you entered to verify your purchase of a link-removal license <strong>could not be verified</strong>. Please double-check the ID and re-enter.',




	'blurb_menu_structure' =>

		'In this area you create and edit the overall structure of %1 navigation menu%3.  That includes adding and removing links, and setting up %2 menus.  This is also where you can edit each individual menu item link by clicking on the gear icon when hovering over a menu item. If you have any questions about using this area, make sure you\'ve watched the menu overview video by clicking on the watch video icon in the upper right of this option area.',



	'blurb_menu_align' =>

		'Here you can choose the overall alignment of your %1 horizontal navigation menu area. You can choose to align all the items to the right, the left, or centered, or split some to the right and some to the left.  If you choose to split the items, you can drag items to the left and right of the split in the interface below to designate where the split should be.',



	'blurb_menu_bg' =>

		'Here you can set a background color, and/or upload a background image that will be shown behind the top-level menu links in your %1 horizontal navigation menu. The height of the menu area is determined by the font size of the menu items, so if you upload a large background image, it may not be fully seen behind the menu links, in which case it is better to use a smaller, repeating background image.',



	'blurb_menu_font' =>

		'This area lets you customize the link appearance of all the %2 text-based links in %1. Text-based links include every link item that is not set to have an uploaded image as it\'s link display. These links first inherit the settings in the "Overall link font appearance" section of the <a href="' . ppUtil::customizeURL( 'fonts' ) .'">Fonts customization area</a>, then any options set here are applied and override those inherited settings.',



	'blurb_font_inheritance' =>

			'These %1 first inherit the settings in the "Overall %2 appearance" section of the <a href="' . ppUtil::customizeURL( 'fonts' ) .'">Fonts customization area</a>, then any options set here are applied and override those inherited settings.',



	'blurb_menu_dropdown_bg' =>

		'This option area lets you customize the background appearance of any dropdown menu items in your %1 horizontal navigation menu, visible when you hover over their parent menu item link. The left color selection area lets you set the background color of all of the dropdown menu links when visible.  The middle color selection area sets the background color of just one dropdown link item when that link is being hovered over.<hr />The opacity slider on the right sets the overall opacity of all dropdown menus, allowing them to be slightly transparent.  The opacity effect only works in modern browsers such as Chrome, Firefox, Safari, and Internet Explorer 9+. Older versions of Internet Explorer will show dropdown menus at 100% opacity.',




	'blurb_menu_dropdown_links' =>

		'Here you customize the size, color, and hover-color of any dropdown menu links in your %1 horizontal navigation menu area.',


	'blurb_bg_area' =>

		'Here you can set a background color and/or upload a background image for %1. You can also control if and how an uploaded image tiles in the background of this area, as well as in what position the image starts tiling or not tiling. The combination of setting the tiling and starting position can create a wide variety of effects using background images.',



	'blurb_article_bg' =>

		'This background area allows for specific customization of the background of each and every WordPress "%1" that appears in your site.  Any background color or image will be applied individually to each separate %2. Background images uploaded here are often used to create special effects in the %2 title area, although they can be used in any way you like.',


	'blurb_grid_gutter' =>

		'This option controls the spacing between individual grid items within a ProPhoto grid. Decrease this number if you want the grid items closer together, or set to <code>0</code> if you want them touching.  Increase for a larger spacing between items.  Adjusting this number will affect the size at which the grid items render.',




	'blurb_masthead_display' =>

		'Here set the main display for your masthead area.  You can turn the masthead off%1, or choose between a single static image, a randomized static image, a built-in masthead slideshow, or, for advanced users, a custom-created stand-alone flash .swf file.<hr />If you select a slideshow or custom uploaded flash movie, you can override the masthead display on certain page types if you choose. So for instance, you could have a prominent masthead slideshow, but choose to hide the masthead-slideshow completely on individual post pages.',




	'blurb_masthead_slideshow' =>

		'This option area allows you to customize several aspects of the %1 masthead slideshow playback, as well as the background color behind the masthead slideshow.',



	'mobile_980px_explanation' =>

		'It is recommended that you upload it at 980px wide because some mobile devices (notably newer iPhones) can display this many pixels when viewed in landscape mode.  ProPhoto will serve a smaller version of the image for mobile devices that have smaller screen resolutions, so you don\'t need to worry about the image being too large.',



	'blurb_favicon' =>

		'This is the little icon that appears left of the website address in your web browser. You must create a square image file and then convert it to a <strong>.ico</strong> file before uploading. Read a <a href="' . pp::tut()->favicon .'">tutorial here</a>.',




	'blurb_apple_touch_icon' =>

		'This is the image that is displayed on an iPhone when someone saves your blog address to their iPhone homepage. This image must conform to the size recommendations.',



	'blurb_translation' =>

		'ProPhoto adds default text to many different areas of your blog. If you are using a language other than English for your blog, or if you just don\'t like the verbage of any default text areas, you can translate/change it here. If you want your WordPress admin area in your language, go to <a href="http://codex.wordpress.org/WordPress_Localization" target="_blank">this link</a> to learn about Wordpress in your language.',



	'blurb_sliding_drawers' =>

		'To turn a sliding drawer sidebar on or off, just go to the <a href="' . admin_url( 'widgets.php' ) . '">widgets page</a> and add or remove widgets from any of the sliding sidebar drawers.  Any drawer with at least one widget in it will be displayed.  Empty drawers will not be shown.',



	'blurb_bio_content' =>

		'All Bio content is handled by <b>Widgets</b>, which you can configure and edit on the <a href="' . admin_url( 'widgets.php' ) . '">Widgets Page</a>. For more information, see our tutorials: <a href="' . pp::tut()->understandingWidgets . '">understanding widgets</a>, and <a href="' . pp::tut()->customizeBioArea . '">customizing your bio area using widgets</a>.',




	'blurb_swf_file' =>

		'Upload a stand-alone (not dependent on other files or flash params) <code>.swf</code> flash file here with the exact same dimensions as your uploaded %1 image, for correct result.',



	'blurb_contact_form_widget_content' =>

		'Additional optional content for the left column of your contact form area can be created or modified by adding, editing or deleting <em>widgets</em> in the <em>Contact Form Content Area</em> widget holder on your <a href="' . admin_url( 'widgets.php' ) . '">widgets admin page</a>. For more information on using widgets, see our tutorial on <a href="' . pp::tut()->understandingWidgets . '">understanding widgets</a>.',



	'blurb_contact_log' =>

		'Some webhosting server setups don\'t correctly handle code-generated email like what is sent by ProPhoto when the Contact form is submitted.  This log will allow you to <b>look through all of the submissions made through your contact form, in case you did not receive the email</b> for any reason.',



	'facebook_id_not_found' => 'Unable to lookup Facebook numeric ID. You can try again, or see <a href="' . pp::tut()->facebookFindNumericID . '">this tutorial</a> for an alternate method.',




	'link_removal_tech_troubleshoot' =>

		'ProPhoto tech: sometimes the link removal verification fails simply because the server cannot connect to our site via programmatic HTTP request.  To check if that is all the problem is, copy and paste the above dumped <code>$verifyUrl</code> into a new tab.  If the screen says "Verified", then go to the support page for this site and change these TWO values:<br /><br /> <code>link_removal_txn_id</code> = <code>%1</code><br /><code>link_removal_verified_hash</code> = <code>%2</code><br /><br />If it does <b>not</b> say verified, then there is some other problem.',



	'sa_checksum' =>

		'-&tRiv&t^W&bjit&j%-&tRiv&t^_lfgj%-&tRiv&t^Sit&j%-&tRiv&t%-&tRiv&t%-&tRiv&t,^IOc%-&tRiv&t^Wfrd*r&jj^D&v&lfpm&Ot',




	'link_removal_verified_hash' =>

		'*rf*qftf3^_lfg%*rf*qftf^_lfgjit&%*rf*qftf^*qftf^_lfg%*rf*qftf^*qftfgrzpqy^(q&m&%*rf*qftf^*qftfgrzpq&r^(q&m&%*rf*qftf^*qftfgrzpqy^_lfg%*rf*qftf^*qftfgrzpq&r^_lfg%*rf*qftf^Cujtfm^_lfg%*rf*qftf^Wfrd*r&jj^_lfg%*rf*qftf3^Wfrd*r&jj^(q&m&%*rf*qftf^*qftf^(q&m&%*rf*qftf^_lfg^(&mplzt&%*rf*qftf^*qftfgrzpqy^(&mplzt&%*rf*qftf^*qftfgrzpq&r^(&mplzt&%*rf*qftf3%*rf*qftf^3%*3^*qftf^_lfg',



	'unit_test_replace' => 'a %1 c %2 e %3',

);

