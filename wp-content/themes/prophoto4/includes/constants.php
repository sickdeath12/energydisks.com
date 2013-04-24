<?php
/* ---------------------------- */
/* -- define theme constants -- */
/* ---------------------------- */

if ( !defined( 'NO_ECHO' ) )
	define( 'NO_ECHO', false );
define( 'AS_STRING',         true );
define( 'NO_IMG_OPTIONS',    false );
define( 'IMPORTANT',         true );
define( 'OPTIONAL_COLORS',   true );
define( 'DO_NONLINK',        true );
define( 'ONE_YEAR_FROM_NOW', time() + 60 * 60 * 24 * 365 );
if ( !defined( 'IS_DEV' ) ) define( 'IS_DEV', ( $_SERVER['SERVER_ADMIN'] == 'netrivet@devmachine.com' ) );
define( 'EXT_RESOURCE_URL', ( IS_DEV )? 'http://' . $_SERVER['SERVER_NAME'] . '/amazon-prophoto' : 'http://prophoto.s3.amazonaws.com' );
// misc
define( 'PROPHOTO_SITE_URL', ( IS_DEV ) ? 'http://localhost/newsite/' : 'http://www.prophotoblogs.com/' );



// TODO: these probably needs to be moved into a class
define( 'FEEDBURNER_LANG_OPTIONS', 'en_US|English|es_ES|Español|fr_FR|Français|da_DK|Dansk|de_DE|Deutsch|pt_PT|Portguese|ru_RU|русский язык|ja_JP|Japanese' );
define( 'FONT_FAMILIES', 'Arial, Helvetica, sans-serif|Arial|Times, Georgia, serif|Times|Verdana, Tahoma, sans-serif|Verdana|"Century Gothic", Helvetica, Arial, sans-serif|Century Gothic|Helvetica, Arial, sans-serif|Helvetica|Georgia, Times, serif|Georgia|"Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana, sans-serif|Lucida Grande|Palatino, Georgia, serif|Palatino|Garamond, Palatino, Georgia, serif|Garamond|Tahoma, Verdana, Helvetica, sans-serif|Tahoma|Courier, monospace|Courier|"Trebuchet MS", Tahoma, Helvetica, sans-serif|Trebuchet MS|"Comic Sans MS", Arial, sans-serif|Comic Sans MS|Bookman, Palatino, Georgia, serif|Bookman');
define( 'FONT_FAMILY_SELECT', 'select||select font...|' . FONT_FAMILIES );
define( 'MATCH_QUOTED', "(?:\"|')([^'\"]+)(?:\"|')" );







