<?php
require_once $pluginPath . "/helpers/resources.php";

if(empty($_GET["page"])){
    $viewDir = "basic";
    $viewName = "basic";
}else{
    $viewDir = $_GET["page"]."View";
    switch ($_GET["task"]){
        case "Details": $viewName = $_GET["page"]."DetailsView";break;
        default: $viewName = $_GET["page"]."View";break;
    }  
}

$viewFile = $pluginPath . "/views/" . $viewDir . "/" . $viewName . ".php";
$resource = new resources();

if(!file_exists($viewFile)){
	$viewFile = $pluginPath. "/views/basicView/basicView.php";
}

function clientes() {
    global $pluginPath;
    global $viewFile;
    global $resource;
    require_once($viewFile);
}

function vehiculos() {
    global $pluginPath;
    global $viewFile;
    global $resource;
    require_once($viewFile);
}

function extensiones() {
    global $pluginPath;
    global $viewFile;
    global $resource;
    require_once($viewFile);
}

function muestras() {
    global $pluginPath;
    global $viewFile;
    global $resource;
    require_once($viewFile);
}


class laFlota_Shortcode {
        static $add_script;

        static function init() {
                add_shortcode('laFlota_Shortcode', array(__CLASS__, 'handle_shortcode'));
                if(!is_admin()){
                    add_action('init', array(__CLASS__, 'register_script'));
                    add_action('wp_footer', array(__CLASS__, 'print_script'));
                }
        }

        static function handle_shortcode($atts) {
                
                global $resource;
                self::$add_script = true;
                if ( !is_user_logged_in() )
                    return $resource->getWord("necesitaAutenticarse");
                
                $template =  file_get_contents(__DIR__."/miFlotaUserView/miFlotaUserView.php");
                $template = str_replace("{placa}", $resource->getWord("placa"), $template);
                $template = str_replace("{buscarPlaca}", $resource->getWord("buscarPlaca"), $template);
                $template = str_replace("{placasCriticas}", $resource->getWord("placasCriticas"), $template);
                $template = str_replace("{placasExtendidas}", $resource->getWord("placasExtendidas"), $template);
                
                return $template;
        }

        static function register_script() {

                if ( is_user_logged_in() ){
                    wp_register_style( 'bootstrapResponsiveCss', plugins_url('../css/bootstrap-responsive.min.css', __FILE__));
                    wp_enqueue_style( 'bootstrapResponsiveCss' );
                    wp_register_style( 'bootstrapThemeCss', plugins_url('../css/bootstrap-theme.min.css', __FILE__));
                    wp_enqueue_style( 'bootstrapThemeCss' );
                    
                    wp_register_style( 'flexslider', plugins_url('../css/flexslider.css', __FILE__));
                    wp_enqueue_style( 'flexslider' );
                    
                    wp_register_style( 'uiCss', plugins_url('../css/jqGrid/themes/ui-lightness/jquery-ui.min.css', __FILE__));
                    wp_enqueue_style( 'uiCss' );

                    wp_register_style( 'gridCss', plugins_url('../css/jqGrid/ui.jqgrid.css', __FILE__));
                    wp_enqueue_style( 'gridCss' );

                    wp_register_style( 'pluginCss', plugins_url('../css/plugincss.css', __FILE__) );
                    wp_enqueue_style( 'pluginCss' );


                    wp_register_script('jqGridLocale_es', plugins_url('../js/jqGrid/grid.locale-es.js', __FILE__), array('jquery'), '1.0', true);
                    wp_enqueue_script('jqGridLocale_es');
                
                    wp_register_script('jqGrid', plugins_url('../js/jqGrid/jquery.jqGrid.src.js', __FILE__), array('jquery'), '1.0', true);
                    wp_enqueue_script('jqGrid');

                    wp_register_script('flexslider', plugins_url('../js/jquery.flexslider-min.js', __FILE__), array('jquery'), '1.0', true);
                    wp_enqueue_script( 'flexslider' );
        
                    wp_register_script('pluginjs', plugins_url('../js/pluginjs.js', __FILE__), array('jquery'), '1.0', true);
                    wp_enqueue_script('pluginjs');

                    wp_register_script('myFleet', plugins_url('miFlotaUserView/JSScripts/myFleet.php', __FILE__), array('jquery'), '1.0', true);
                    wp_enqueue_script('myFleet');
                
                }

        }

        static function print_script() {
                if ( ! self::$add_script )
                        return;
                if ( is_user_logged_in() ){
                    wp_print_scripts('myFleet');
                    wp_print_scripts('jqGridLocale_es'); 
                    wp_print_scripts('jqGrid');
                    wp_print_scripts('pluginjs');
                    wp_print_scripts('jquery-u');
                }
        }
}
laFlota_Shortcode::init();

?>
