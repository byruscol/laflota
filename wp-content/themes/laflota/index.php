<?php get_header();?>
        <section id="main">
            <div id="no-slide">
                <?php include TEMPLATEPATH .'/slideshow.php';?>
            </div>
            <section id="columna1">
                
                <?php if(have_posts()): while(have_posts()) : the_post();?>
                    <?php if (in_category('Home') ) : ?>
                        <div class="home_txt">
                            <h2><a href="<?php the_permalink();?>"><b><?php the_title();?></b></a></h2>
                            <p><?php the_excerpt();?></p>
                        </div>
                    <?php endif;?>
                <?php endwhile; else:?>
                <div class="home_txt">
                    <h2>No hay artículos</h2>
                    <p>No hay artículos</p>
                </div>
                <?php endif;?>
		
                <div id="fb_box">
			<div id="titulo_fb">
                            <h3>Haga parte de nuestra comunidad</h3>
			</div>
                    <div class="fb-like-box" data-href="https://www.facebook.com/comunidadlaflota" data-width="100%" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="false" data-show-border="false"></div>
		</div>
            </section>
            <?php get_sidebar();?>           
        </section>
<?php get_footer();?>    

