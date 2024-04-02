<?php
if(get_post_type() == "gravityview") {
    get_header();
    /* Start the Loop */
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="gravity-view-<?php the_ID(); ?>" <?php post_class(); ?>>

            <header class="entry-header alignwide">
                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

            </header><!-- .entry-header -->

            <div class="entry-content">
                <?php the_content(); ?>
            </div><!-- .entry-content -->

        </article><!-- #post-<?php the_ID(); 

    endwhile; // End of the loop.

    get_footer();
} else {
    header( 'Location: ' . get_bloginfo('url') );
}

