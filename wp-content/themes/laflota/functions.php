<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
register_nav_menus(
            array(
                'top-menu' => 'Menu superior'
            )
        );

add_theme_support('post-thumbnails');
add_image_size("slider_thumbs", 960, 351, true);
add_image_size("list_articles_thumbs", 350, 250, true);

function digwp_bloginfo_shortcode( $atts ) {
   extract(shortcode_atts(array(
       'key' => '',
   ), $atts));
   return get_bloginfo($key);
}

add_shortcode('bloginfo', 'digwp_bloginfo_shortcode');

add_action( 'wp_login_failed', 'pu_login_failed' ); // hook failed login

function pu_login_failed( $user ) {
  	// check what page the login attempt is coming from
  	$referrer = $_SERVER['HTTP_REFERER'];

  	// check that were not on the default login page
	if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') && $user!=null ) {
		// make sure we don't already have a failed login attempt
		if ( !strstr($referrer, '?login=failed' )) {
			// Redirect to the login page and append a querystring of login failed
	    	wp_redirect( $referrer . '?login=failed');
	    } else {
	      	wp_redirect( $referrer );
	    }

	    exit;
	}
}
?>
