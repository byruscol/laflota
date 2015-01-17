<?php get_header();?>
<section id="main">
    <section id="columna1">
        <?php while(have_posts()) : the_post();?>
            <?php the_content();?>
        <?php endwhile; ?>
    </section>
    <?php get_sidebar();?> 
    <section id="fila1">
	<h3>Servicios</h3>
        <div class="servicios_min" id="servicesButons">
            <div class="servicios_btn" id="filtracion">
            <img src="<?php bloginfo('template_url')?>/images/servicios_filtracion.png" alt="filtracion lubricacion" />
                    Filtración<br/>y lubricación
            </div>
            <div class="servicios_btn" id="llantas">
            <img src="<?php bloginfo('template_url')?>/images/servicios_llantas.png" alt="llantas" />
                    Llantas
            </div>
            <div class="servicios_btn" id="frenos">
            <img src="<?php bloginfo('template_url')?>/images/servicios_frenos.png" alt="frenos suspensión" />
                    Frenos <br />y suspensión
            </div>
            <div class="servicios_btn" id="baterias">
            <img src="<?php bloginfo('template_url')?>/images/servicios_baterias.png" alt="baterias sistema electrico" />
                    Suministro de baterías <br /> y sistema eléctrico
            </div>
            <div class="servicios_btn" id="lavado">
            <img src="<?php bloginfo('template_url')?>/images/servicios_lavado.png" alt="lavado tanques" />
                    Lavado <br /> de tanques
            </div>
            <div class="servicios_btn" id="arranque">
            <img src="<?php bloginfo('template_url')?>/images/servicios_arranque.png" alt="qsrt" />
                    Reparación y suministro  <br />de arranques <br /> y alternadores
            </div>
        </div>
    </section>
</section>

<?php get_footer();?>    




