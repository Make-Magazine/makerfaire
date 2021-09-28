<?php
/**
 * Override normal single post behavior for elementor templates
 */
  
get_header(); ?>
  
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
  
        <?php the_content(); ?>
  
        </main><!-- .site-main -->
    </div><!-- .content-area -->
  
<?php get_footer(); ?>