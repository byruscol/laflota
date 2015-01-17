<?php get_header();?>
        <section id="main">
            <section id="columna1">
                <?php while(have_posts()) : the_post();?>
                <div class="">
                    <h2><a href="<?php the_permalink();?>"><b><?php the_title();?></b></a></h2>
                    <p><?php the_content();?></p>
                </div>
                <?php endwhile; ?>
                <div id="comentarios">
                    <div id="titulo_fb">
                        <h3>Comentarios</h3>
                        <img src="<?php bloginfo('template_url')?>/images/login_icono.png" alt="comentarios">
                    </div>
                    <div class="comentarios"><?php comments_template();?></div>
		</div>
            </section>
            <?php get_sidebar();?>           
        </section>
<?php get_footer();?>