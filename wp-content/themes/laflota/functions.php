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


function custom_login_logo() {
    echo '<style type="text/css">
            body{
                background-color: #e4e4e4;
                font-family: Arial,Helvetica,sans-serif;
                font-size: 14px;
            }
            .login h1 a {width: auto !important; background-image:url('. get_bloginfo( 'template_directory' ) .'/images/favicon.png) !important; background-size: 80px auto !important;}
            
            #loginform, #lostpasswordform {
                background:  url("'. get_bloginfo( 'template_directory' ) .'/images/ingresar_fondo.png") repeat-x scroll 0 0 rgba(0, 0, 0, 0);
                color: #333;
                height: 174px;
                width: 330px;
                padding: 46px 25px 36px 25px;
                background-color: #C7C7C7;
            }
            
            #lostpasswordform {
                height: 104px;
            }

            #loginform p label, #lostpasswordform  p label{
                color: #000;
                margin-bottom: 2px;
            }
            
            .input{
                border: 1px solid #aeaeae;
                font-size: 14px;
                height: 25px;
                margin-bottom: 10px;
                padding: 2px 10px;
                width: 93%;
            }
            #wp-submit{
                background: url("'. get_bloginfo( 'template_directory' ) .'/images/login_btn.png") repeat scroll 0 0 rgba(0, 0, 0, 0);
                color: #fff;
                cursor: pointer;
                font-size: 14px;
                height: 30px !important;
                line-height: 32px;
                text-align: center;
                width: 100%;
            }
            
            .login .message {
                border-left: 4px solid #E98020;
            }
            
            #loginform img, #lostpasswordform img {
                float: right;
                margin: -46px -25px 0;
            }
            #loginform h3, #lostpasswordform h3{
                color: #FFF;
                font-family: Arial, sans-serif !important;
                font-weight: 300 !important;
                float: left;
                margin: -40px 0 0 -15px;
            }        

    </style>';
}
function ad_login_footer() {
    $ref = wp_get_referer();
    if ($ref) :
?>
<script type="text/javascript">
    var img = document.createElement("img");
    img.src = "<?php echo get_bloginfo( 'template_directory' ); ?>/images/login_icono.png";
    var h3 = document.createElement("h3");
    h3.innerHTML = "Ingresar";
    
    var loginForm;
    
    if(document.getElementById("loginform"))
        loginForm = document.getElementById("loginform");
    else
        loginForm = document.getElementById("lostpasswordform");
    
    loginForm.insertBefore(h3, loginForm.firstChild);
    loginForm.insertBefore(img, loginForm.firstChild);
    
</script>
<?php
    endif;
}


add_action('login_head', 'custom_login_logo');
add_action('login_footer', 'ad_login_footer');
?>
