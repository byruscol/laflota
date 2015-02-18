<aside>
    <?php if ( is_user_logged_in() ):?>
    <a href="<?php echo get_permalink( get_page( 97 ) );?>" id="backMiFlota"></a>
    <?php endif; ?>
    <section id="login_setion">
    <?php if ( is_user_logged_in() ):
        $current_user = wp_get_current_user();
    ?>
        <h3>¡Bienvenido!</h3>
        <img src="<?php bloginfo('template_url')?>/images/login_icono_inner.png">
        <form id="loginform" name="loginform">
            <div id="fot_div">
            <img id="foto_cliente" src="<?php bloginfo('template_url')?>/images/foto_cliente.png">
            </div>
            <h4><?php echo $current_user->display_name?></h4>
            <h4><?php echo $current_user->user_login?></h4>

            <a id="logout_btn" href="<?php echo wp_logout_url(home_url()); ?>" title="Logout">Cerrar sesión</a>
        </form>	
              
    <?php else:?>
        <h3>Ingresar</h3>
        <img src="<?php bloginfo('template_url')?>/images/login_icono.png">
        <?php $args = array(
        'echo' => true,
        'redirect' => get_permalink( get_page( 97 ) ), 
        'form_id' => 'loginform',
        'label_username' => "Usuario",
        'label_password' => "Contraseña",
        'label_log_in' => "Ingresar",
        'id_username' => 'user_login',
        'id_password' => 'user_pass',
        'id_remember' => 'rememberme',
        'id_submit' => 'wp-submit',
        'remember' => false,
        'value_username' => NULL,
        'value_remember' => false );
        
        wp_login_form( $args );
        ?>
        <a id="lostPwd" href="<?php echo wp_lostpassword_url(); ?>" title="Olvidó sucontraseña">¿Olvidó sucontraseña?</a>
    <?php endif;?>
    </section>
    <section id="direcciones">
        <div id="direcciones1">
            <div class="bgArrowDir"></div>
        </div>
        <div id="direcciones2">
                <img src="<?php bloginfo('template_url')?>/images/direcciones_info.png" alt="direcciones equitel">
                    <ul id="direcciones_info">
                        <div id="bogota">
                            <li>Av. Ciudad de Cali No. 11 - 22 <br/>Bogotá, Colombia</li><br /><br />
                            <li>(57 1) 294 84 44 Ext. 4500 <br/>312 4647667</li><br />
                            <li>Andrés Orjuela<br><a href="mailto:aorjuela@equitel.com.co"><b>aorjuela@equitel.com.co</b></a></li>
                        </div>
                        <div id="barranquilla">
                            <li>Km. 3 vía oriental, diagonal al Parque Industrial PIMSA <br/>Barranquilla, Colombia</li><br />
                            <li>(57 5) 385 23 61 Ext. 4505 <br/>313 404 1389</li><br />
                            <li>Cindi Grisales<br><a href="mailto:cgrisales@equitel.com.co"><b>cgrisales@equitel.com.co</b></a></li>
                        </div>
                        <div id="medellin">
                            <li>Cra. 52 No. 10-184 <br/>Medellín, Colombia</li><br /><br />
                            <li>(57 4) 255 42 00 <br/>312 776 2208</li><br />
                            <li>Diego Alvarez<br><a href="mailto:dalvarez@equitel.com.co"><b>dalvarez@equitel.com.co</b></a></li>
                        </div>
                        <div id="ibague">
                            <li>Glorieta Mirolindo Vía Bogotá  <br/>Ibagué, Colombia</li><br /><br />
                            <li>(57 8) 269 05 73 <br/>313 386 2393</li><br />
                            <li>Sandra Oliveros<br><a href="mailto:soliveros@equitel.com.co"><b>soliveros@equitel.com.co</b></a></li>
                        </div>
                        <div id="villavicencio">
                            <li>Vía Puerto López Km. 1 <br />Vereda Ocoa  <br/>Villavicencio, Colombia</li><br />
                            <li>(57 8) 684 98 44 <br/>310 817 1953</li><br />
                            <li>Patricia Bernal<br><a href="mailto:pbernal@equitel.com.co"><b>pbernal@equitel.com.co</b></a></li>
                        </div>
                    </ul>
        </div>
    </section>
    <section id="celudesvare">
        <h3>Celudesvare</h3>
        <img src="<?php bloginfo('template_url')?>/images/celudesvare_icono.png">
        <span>
        <h4>Servicio exclusivo para clientes<br /> Cummins de los Andes</h4>
        <ul>
        <li><b>Bogotá</b><br/>314 411 69 46</li><br />
        <li><b>Medellín</b><br/>314 791 10 47</li><br />
        <li><b>Ibagué</b><br/>314 470 90 58</li><br />
        <li><b>Villavicencio</b><br/>310 476 77 07</li>
        </ul>
        <h4>Asistencia técnica y desvare<br>las 24 horas</h4>
        </span>
    </section>
    <section id="capacitaciones_section">
        <h3>Capacitaciones</h3>
        <img src="<?php bloginfo('template_url')?>/images/capac_icono.png">
        <div id="capacitaciones_cont">
            <?php include TEMPLATEPATH .'/slidecapacitaciones.php';?>	
        </div>
    </section>
</aside>