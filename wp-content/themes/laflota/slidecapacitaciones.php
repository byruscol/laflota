<div id="capacitacionesSlide">
    <?php 
    $query = new WP_Query( 'category_name=Capacitaciones' );
    if($query->have_posts()):while($query->have_posts()): $query->the_post();?>
        <div class="slide">
            <?php the_excerpt();?>
        </div>
    <?php endwhile; else:?>
    <div>
        <h2>No hay capacitaciones</h2>
    </div>
    <?php endif;?>
</div>

