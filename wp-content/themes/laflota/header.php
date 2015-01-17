<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link rel="icon" href="<?php bloginfo('template_url')?>/images/favicon.ico" sizes="64x64">
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url')?>/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url')?>">
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url')?>/css/reset.css">
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url')?>/css/responsive.css">
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url')?>/css/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url')?>/css/circular_content_carousel.css">
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url')?>/css/jquery.jscrollpane.css">
        <link href='http://fonts.googleapis.com/css?family=Oswald:400,300' rel='stylesheet' type='text/css'>
        <?php if(substr_count($_SERVER["REQUEST_URI"],"respaldo") > 0):?>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript">
        <?php endif;?>
        <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="<?php bloginfo('template_url')?>/js/jquery-ui.min.js"></script>
        <script src="<?php bloginfo('template_url')?>/js/bootstrap.min.js"></script>
        <script src="<?php bloginfo('template_url')?>/js/jquery.slides.min.js"></script>
        <script src="<?php bloginfo('template_url')?>/js/jquery.mousewheel.js"></script>
        <script src="<?php bloginfo('template_url')?>/js/jquery.easing.1.3.js"></script>
        <script src="<?php bloginfo('template_url')?>/js/jquery.contentcarousel.js"></script>
        <script>
            $(function(){
                if($("#slideshow")){
                    $("#slideshow").slidesjs({
                      height: 351,
                      navigation: false,
                      play: {
                          active: false,
                          effect: "fade",
                          interval: 3000,
                          auto: true,
                          swap: true,
                          pauseOnHover: false,
                          restartDelay: 2500
                        }
                    });
                }
            });
            
            $(function(){
              $("#capacitacionesSlide").slidesjs({
                height: 350,
                navigation: false,
                play: {
                    active: false,
                    effect: "slide",
                    interval: 5000,
                    auto: true,
                    swap: true,
                    pauseOnHover: false,
                    restartDelay: 2500
                  }
              });
            });
            
            $(function() {
                    $( "#accordion" ).accordion({
                            collapsible: true,
                            heightStyle: "content",
                            icons: false,
                            active: "none",
                    });
                    $( "#accordion2" ).accordion({
                            collapsible: false,
                            heightStyle: "content",
                            icons: false,
                            active: 0,
                    });
            });
            $(function() {
                $('#ca-container').contentcarousel();
            });
        </script>
        
        <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script>
        var marker, map;
        
        function changeMarkerPosition(val){
            var latLng = val.split(",");
            var myLatlng = new google.maps.LatLng(parseFloat(latLng[0]), parseFloat(latLng[1]));
            marker.setPosition(myLatlng);
            map.setCenter(myLatlng);
            changeContactInfo(latLng[2])
            changeServicesInfo(latLng[3])
        }
        
        function changeContactInfo(info){
            $('#direcciones_info > div').css('display', 'none');
            $('#'+info).css('display', 'block');
        }
        
        function changeServicesInfo(buttons){
            var button = buttons.split("-");
            var buttonArea = button.length*161;
            
            $('#servicesButons').css('width', buttonArea);
            $('#servicesButons > div').css('display', 'none');
            
            for(i in button)
                $('#'+button[i]).css('display', 'block');
        }
        
        function initialize() {
            if($('#map-canvas')){
                var myLatlng = new google.maps.LatLng(4.650853, -74.144255);
                var mapOptions = {
                  zoom: 17,
                  center: myLatlng
                }
                map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                marker = new google.maps.Marker({
                      animation: google.maps.Animation.DROP,
                  position: myLatlng,
                  map: map,
                  title: 'Organización Equitel'
                });
            }
        }
        $(function() {
            if($('#map-canvas').length){
                google.maps.event.addDomListener(window, 'load', initialize);
                $('#fila1').css('display', 'block');
                $('#direcciones').css('display', 'block');
                $("#lista_ciudades").val($("#lista_ciudades option:first").val());
                
            }
           if($('#contact_form').length){
               $('#celudesvare').css('display', 'block');
           }
           if($('#slideshow').length){
               $('#capacitaciones_section').css('display', 'block');
           }
           
        });
        </script>
        
        <script>
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.0";
                fjs.parentNode.insertBefore(js, fjs);
              }(document, 'script', 'facebook-jssdk'));
        </script>
        <title>La Flota • Ahorro en cada kilómetro </title>
    </head>
    <body>
        <div id="top">
            <div id="top0"></div>
            <div id="top1"></div>
            <div id="top2"></div>
        </div>
        <header id="header">
            <div class="navbar-header">
                <a id="nav-header-home" href="<?php bloginfo('url')?>"></a>
                <button class="navbar-toggle collapsed" aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <nav id="menu" role="navigation">
                <div id="navbar" class="navbar-collapse collapse">
                    <?php 
                        wp_nav_menu(
                                array(
                                    'container' => false,
                                    'items_wrap' => '<ul>%3$s</ul>',
                                    'theme_location' => 'top-menu'
                                )
                            );
                    ?>
                </div>
            </nav>
        </header>
        <div id="socialNetworks">
                <a id="youtube" href="https://www.youtube.com/user/EquitelTV" target="_blank" id="youtube"></a>
                <a href="https://www.facebook.com/comunidadlaflota" target="_blank" id="fb"></a>
            </div>