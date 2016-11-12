<?php

define('WP_HOME','https://www.eclink.ca');
define('WP_SITEURL','https://www.eclink.ca');

define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/var/www/html/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('FS_METHOD', 'direct');

define ('WPLANG', 'zh_CN');
// ** MySQL settings ** //
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp223');

/** MySQL database username */
define('DB_USER', 'wp-db-user122');

/** MySQL database password */
define('DB_PASSWORD', '6geu.V3Hm4HK2vkdNN7EVEtO7');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('AUTH_KEY',         '6-2b{y>+Yv 3i/L.u-*g.hO<ZFq:Ku4m=jxr2$SKp:C0faK9]c*(G5N,$r22=Lo8');
define('SECURE_AUTH_KEY',  'M%#&>Z-c}.{G1Na1jW4Rv{$e`Ji-o4nu$-QR+.:SoLY9-w|M5W30r.d|0dmuc<+#');
define('LOGGED_IN_KEY',    '++}SwnAq COl}yJ@y8[=z,O8Dd+HA}|.!;_X%I</Tj5M6ASs;-[`:pi~ K{)#,1a');
define('NONCE_KEY',        '^FZv+*ea-5IIL4mq)E9A$G$>-yBK;(^I.u6S2._5*NY~%HQ~.+=]NSM v}&WW(0K');
define('AUTH_SALT',        'N.YL0;bNrO9LmYSdqr1oi^V++QO0U/xZuUZIn)B(54iE~r[*gddX5~AEL}RURz)l');
define('SECURE_AUTH_SALT', 'c-W&UFG8GO}+@)=},%-cOviMXT7:3-V2HF`i8`@WSY|DlEx!0~czs0mdiNSWyk+&');
define('LOGGED_IN_SALT',   'I$DOd&$?sGdg_vYld8i,w|]<_[4f+Bicwc&AIUwDaDwWT8<`Ybc3B>PO<m1M/O$x');
define('NONCE_SALT',       'l;ErZ8.| l^M0Q9nSy%3-t+#e{(@KM<vghMkt@k.-ERD4^^hx|;9r=m9+o*:7||l');


$table_prefix = 'XQiviLiz';





/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

