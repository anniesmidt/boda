<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'boda_db');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         'Vd@*nvS[FF&Uj;+}yn~mH6qH6J^M6Pq3$/Ct3>.%Xvhnm%~J}@1%3bkt]L4;jnZg');
define('SECURE_AUTH_KEY',  'qf``w5s]V+!|5KLZ=lbQ~^#WQ>mVaBTi/Ss2fWyU~PC$Rr)>I!e-AJH}o^]? |T%');
define('LOGGED_IN_KEY',    '%W9ij2:|+hZ+}.N?9mQPZ#|W(~^QmF@p8[6Z.&N[ddC<guK6|}FLo;,2(_5pQs+|');
define('NONCE_KEY',        '$Hk|k}x(ndR!2]Bx+HWtR@usx&O<KK!1SG`N%T.o?%#%h4s]MQ|>mUE}|P~yui_Y');
define('AUTH_SALT',        ')9@$ kI8_z^C>FfY&tG}mysP3{,ngk*vaygJt.vjfSS$b9-o}N3glkQ>Fgy1dC+l');
define('SECURE_AUTH_SALT', 'iFsk2Os*S uC8[kMfY :9g)pW*DM;;1*wE~L3VHL|q-NZ;ihVdt{gTLwDFwDst;R');
define('LOGGED_IN_SALT',   '~xE8R}qFx.}m=i*Mb#{XY$sVkaQT|<1<#EyAo-U`Plj7Jk{yq|%-2l^-R{+QSt#A');
define('NONCE_SALT',       'U/Qd8,8e2&FykxRz,x@rsJ(-+K%m*Nk+2zz#^tC,%;3}:QxDTp5QV4woxAvH;:.8');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
