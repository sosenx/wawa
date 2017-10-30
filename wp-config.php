<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', '23341640_0000002');

/** MySQL database username */
define('DB_USER', '23341640_0000002');

/** MySQL database password */
define('DB_PASSWORD', '6GN0l8TcUDN08vEfHadesWCivDPUq0Xx');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'LvxlKKy&RLkoA4azzmYNkzPPx%3NUnSvmHqRJqInlHDiBGox%a)Qgq5l@&@hYnoi');
define('SECURE_AUTH_KEY',  'wy(*Jmv5MBI^jH&^8W8zE)a!VWQ%x70Y#*sSH6RuWpMq&pVy1wRCZII!QTDo3U^Y');
define('LOGGED_IN_KEY',    'jaVPuH%wMdjp6v%WOAQNuhols#ZGfhTX3))#nkczOk)EeFCUwqJzuF3vKmhfvV)2');
define('NONCE_KEY',        'SGRF6eu0ln5(X%MevLkTC*J!pDeP2iz9ygfRJXxDPRasR^d0Uyxnblxdyra5fHq1');
define('AUTH_SALT',        '5Dud6*C8V6sztkiUpcpWuA(JISH#rb%&J4#xxsuY1f2PD%FFsGjF9ut5BO7vW#3I');
define('SECURE_AUTH_SALT', '8TyOuCDGdtS%1GC9vq45D(kJTKe5g3vs*DgHl4)27suPbg6!b@a4SUM(wh8D8pEu');
define('LOGGED_IN_SALT',   'r8)9GuyYCa&D%xLamY^LVcPcEk!%p(V0okPrrXxZR^l4n638v@mQYRzk@ibHoYX#');
define('NONCE_SALT',       'wgEyJp0K7NXrdCafXx3ch^YKYAb)Cw)SL)A1I*aHMjZHvUo8akv^#BXXz@q9mzuj');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define( 'WP_ALLOW_MULTISITE', true );

define ('FS_METHOD', 'direct');
?>