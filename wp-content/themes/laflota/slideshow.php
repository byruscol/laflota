<section id="slideshow">
    <?php $query = new WP_Query( 'category_name=SlideShow' );
    if($query->have_posts()):while($query->have_posts()): $query->the_post();?>
        <div class="slide">
            <?php if(has_post_thumbnail()){the_post_thumbnail('slider_thumbs');}?>
        </div>
    <?php endwhile; else:?>
    <div class="home_txt">
        <h2>No hay imagenes</h2>
    </div>
    <?php endif;?>
</section>