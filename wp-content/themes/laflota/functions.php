<?php
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
?>
