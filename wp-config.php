<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //

if (isset($_SERVER["DATABASE_URL"])) {
  $db = parse_url($_SERVER["DATABASE_URL"]);
  define("DB_NAME", trim($db["path"],"/"));
  define("DB_USER", $db["user"]);
  define("DB_PASSWORD", $db["pass"]);
  define("DB_HOST", $db["host"]);
}
else {
  die("Your heroku DATABASE_URL does not appear to be correctly specified.");
}

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'JE{V{[$]ZZ(*MU &>-(4>+]4aMi+3gZWyGG-|zhUw]|W `eocdD*I#qI.jdhm`AL');
define('SECURE_AUTH_KEY',  'X1`aAWpLFSDF:wZ hLIPINCm}Ru]#!wXud=2,cE)@Lv8SipGs8:W*-` 4-~3X(+t');
define('LOGGED_IN_KEY',    'RT-#):$?oYEEN0o`AhkmDSy%@ArsWO Ub/`8(,InAGHS|Jny%f}nd3:v=#2Cl(GQ');
define('NONCE_KEY',        '>@{PMo1UPS)E9C_#XS#BTF<OS&S-S3}9|tE?H#ve1 q|ig6J?@Vr~)9J21ho$@Kx');
define('AUTH_SALT',        '7Db2%0kvZC{[|U;qraa|eR_-9m-FPKJ9%uSvDJS+:47_?=A{b;l7,EK3C}>#jgbT');
define('SECURE_AUTH_SALT', '9J#V%g+)K`g;`l^OO0UDm]g_doH2R?8r@ 1b<>.ZFEDxS983JE+>R%4bpwoK:Hc>');
define('LOGGED_IN_SALT',   ';bVsv&G~$)j..-Kt+XG(fz:-t)r3WkaX-c-FpMTCR+}BZ:<w@|Z-sz6fv-BCjHe-');
define('NONCE_SALT',       '+QsB9%t+u<n::+i5l%wg^^4a[A{$~QowgtF*(4RKl=9].`h2{(0W. (bU})dBqsb');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
