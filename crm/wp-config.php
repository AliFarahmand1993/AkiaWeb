<?php

/**

 * The base configuration for WordPress

 *

 * The wp-config.php creation script uses this file during the installation.

 * You don't have to use the web site, you can copy this file to "wp-config.php"

 * and fill in the values.

 *

 * This file contains the following configurations:

 *

 * * MySQL settings

 * * Secret keys

 * * Database table prefix

 * * ABSPATH

 *

 * @link https://wordpress.org/support/article/editing-wp-config-php/

 *

 * @package WordPress

 */


// ** MySQL settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define( 'DB_NAME', "austechd_crmradwin" );


/** MySQL database username */

define( 'DB_USER', "austechd_crmrad" );


/** MySQL database password */

define( 'DB_PASSWORD', "ilA22843532@ilA" );


/** MySQL hostname */

define( 'DB_HOST', "localhost" );


/** Database charset to use in creating database tables. */

define( 'DB_CHARSET', 'utf8mb4' );


/** The database collate type. Don't change this if in doubt. */

define( 'DB_COLLATE', '' );


/**#@+

 * Authentication unique keys and salts.

 *

 * Change these to different unique phrases! You can generate these using

 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.

 *

 * You can change these at any point in time to invalidate all existing cookies.

 * This will force all users to have to log in again.

 *

 * @since 2.6.0

 */

define( 'AUTH_KEY',         'i&~w`mvL ,)x6EM>V-_aBCf^2pXAWnUFDlA)v9_W^s0g E;nlq0Mia;Fn+3sD!q?' );

define( 'SECURE_AUTH_KEY',  'jllw>R?|G<?}wT8N.Ak&=v1ol)[=tY0ma}-R&@${#&qFsiGy}5vd2E[UjJ586xPu' );

define( 'LOGGED_IN_KEY',    'Q|G~;C;<5/$>i--r i}6%1`iTE8bHm@)/r;~iRa_Mp`g1aB2?_uS=:K(MsWE-;99' );

define( 'NONCE_KEY',        '0)~a8)=&fWDR0 K})[BgIK259s+jp?u#1(&jYU{w@F8c[L$]d|+c<#Db6E)j(x9y' );

define( 'AUTH_SALT',        'E4]%4Gj]dC?^.i5gHu$6/^gO@F]bC,)~p(mPulx#m3G7grS0Vb#Y;Xa`oj+@:4iA' );

define( 'SECURE_AUTH_SALT', 't#BT^05b1}fz3HGA,nKk`]Wv}N(#|8[#&%OpOiq{;-%vO}GZ@g/*N1N`TlA6Ym,H' );

define( 'LOGGED_IN_SALT',   '1U`,2x5+E62mik!A)gQ[qXw=;Qo(27{qQ1K1u&GQ;{BY@!gN1g:,+),2}:f}q<qb' );

define( 'NONCE_SALT',       '[V I%82IOe[6a5OL=>jE&Cu|e3X?=VR4XOF4~QorA+c?sM$J% Mt5$Eq:A;MGgVk' );


/**#@-*/


/**

 * WordPress database table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix = 'wpr_';


/**

 * For developers: WordPress debugging mode.

 *

 * Change this to true to enable the display of notices during development.

 * It is strongly recommended that plugin and theme developers use WP_DEBUG

 * in their development environments.

 *

 * For information on other constants that can be used for debugging,

 * visit the documentation.

 *

 * @link https://wordpress.org/support/article/debugging-in-wordpress/

 */

define( 'WP_DEBUG', false );


/* Add any custom values between this line and the "stop editing" line. */




/* That's all, stop editing! Happy publishing. */


/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}


/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';

