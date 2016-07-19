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
define('DB_NAME', 'proficientairllc');

/** MySQL database username */
define('DB_USER', 'proficientairllc');

/** MySQL database password */
define('DB_PASSWORD', 'proficientairllc');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'AAEhZ8awU&oBJVjeg&v~g:fHOfq*?:5V34dmZ]IUoNvpuZh;L?9[Y.O&|[7GI!(T');
define('SECURE_AUTH_KEY',  'N[Vk3 vwh=?#_Dxn!YI0[e,gbV$LI_U6z|$_eu!W)m>A4X@k*B8ccYdI/hHf,5B]');
define('LOGGED_IN_KEY',    'G+:+=K2#OyDklb(^6HK?Co[DjKax%gE,=.7bl|Ulau2+p./>1~>dfsPd$!2k M< ');
define('NONCE_KEY',        '(zDUJI,m%MJcV?%BHthnW^lBvg8:*haWmdDAZ:+akXe;V;cpi#Qx* ;la?~?:?J~');
define('AUTH_SALT',        'z*pT@iQ8~@#Yv0h, h=X8a0Unr#I{$*`Uf/M.S}zi0.{GbZ[kpX^*V[1i4sha~@%');
define('SECURE_AUTH_SALT', 'sEuRz@q^8|q1P-7s?qt.+x&a^96h9Pv^EvRAZc#o<65!Ap:Xo2Sd`UB,Osr%x<`R');
define('LOGGED_IN_SALT',   '@I7[^$`3OK~UV`_}.GNp-s^~R&3PM;&U&}Ieh;fC$W3~L-Y)KP?-rcGk&h*aNApA');
define('NONCE_SALT',       'rfbU1T_D4^@/m<eu$=Q cP_sK2vy=0NW*{yKzR@<Fz/,+R+td5S7<wK_#g([%6ni');

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
