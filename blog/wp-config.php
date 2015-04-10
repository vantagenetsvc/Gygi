<?php

/* Multisite Install Permissions */
define( 'WP_ALLOW_MULTISITE', true );


define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'www.gygi.com');
//define('PATH_CURRENT_SITE', '/blog/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/** Enable W3 Total Cache Edge Mode */
define('W3TC_EDGE_MODE', true); // Added by W3 Total Cache


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
/** The name of the database for WordPress */
define('DB_NAME', 'gygi_blog');

/** MySQL database username */
define('DB_USER', 'gygi_blog');

/** MySQL database password */
define('DB_PASSWORD', '91pyqalsDC&^');

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
define('AUTH_KEY',         'EUg)T@lflpF!gp7IFt*hk<bkXIB+Igs]>j{Joe%G.P`#th~FTT1>six$JE~ee!@K');
define('SECURE_AUTH_KEY',  'e-<.|$e/S]i~T5V7gS@/Lx}r7he.Qw#4DMv?1$}D<s`67R%}9d8-gZto)!,EE5k3');
define('LOGGED_IN_KEY',    '+h_WSIOyi^ )+Yl$RB))<MZ:jYw9{f=y>2c$wxCb!IJ~8OB(i<EaiT4R*l@5bQH4');
define('NONCE_KEY',        'c}:Rk,gqH>`brq(kHw)q@J<1?RrtzV@O~@luNK8N+f<RuY]`z3:#kq(JxbK!}N]w');
define('AUTH_SALT',        'N.u&5O91S79 YWjE8o *RP5rn)1cSVtE-]PK!G<-.mR4[]AV(kTBQ<h~EW0yhOfP');
define('SECURE_AUTH_SALT', '2q1tLb*?tKO6j6eBD_~X~BA)G1#?Q@Z_xC]Q%Z}Mry6[ShDtyTyF<]i9O>=Obyx<');
define('LOGGED_IN_SALT',   '7^=#Ha8Y/~po7tsZF*LOI]5~kDL,gW)OS2TF9ZZ+xXM>+!W9j,I~cuW#~`%jV=zJ');
define('NONCE_SALT',       'LS</8]#O3%jG+_G7t)PH*)[,mKtRQuL>F 07WGu[2m3A*yx9z*JP0= tPm]Mkbz@');

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
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

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
