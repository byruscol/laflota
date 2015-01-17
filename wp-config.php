<?php
/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'laflota');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'root');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', 'clinicol');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'teS? g=pA4+S@nc-:}_1(.z89*}5 5;Bh33JYUJ2yq]|POJMMz>4#?^$UO8*E9F1'); // Cambia esto por tu frase aleatoria.
define('SECURE_AUTH_KEY', '-IV#9W;r%%LvGO+WG4X(5<A|j@27K<,-eQ$%Wa$i!-HwAS>m~9=[a@C(cQbsr#HD'); // Cambia esto por tu frase aleatoria.
define('LOGGED_IN_KEY', 'vuJ)wV;`-Bp/=%#OzuyE2>^/=$%O&mzcFwDWC1sRq-^$l<&nQ@`|N]* H%:y++3q'); // Cambia esto por tu frase aleatoria.
define('NONCE_KEY', 'Ji_7Bb|?I<`ma-Z;_VfkpP/mO`i]n,)84K<)Y/Y^j:|w:!vEsl[k`J4v)^d$lfH|'); // Cambia esto por tu frase aleatoria.
define('AUTH_SALT', '=$g0@9gu334n BI+ZXp]j1rQ9ev&0Tq1TA}.)gR)1)|nyYq}6-YFqvCs{%5E78;t'); // Cambia esto por tu frase aleatoria.
define('SECURE_AUTH_SALT', 'verK4O<(//+JE9BcGag,=t0s&>Ss@9_ Lpu}H4CB:[.PS07iy:!fB7!]I7+iW]IH'); // Cambia esto por tu frase aleatoria.
define('LOGGED_IN_SALT', '>nlH?>WR:|-zCv`WvDjT+:A02D87V:evQyPT`7uH#BL~0%|xe[C.S(y(|I..Y:~ '); // Cambia esto por tu frase aleatoria.
define('NONCE_SALT', 'f*Y-+ +Vu{nUM=s-XmMCy*x0BJ2-q&,<3xH-5tfHv1};C[8|V-<|#)(bkG;Jy+t+'); // Cambia esto por tu frase aleatoria.

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


