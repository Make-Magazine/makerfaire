<?php
/**
 * Template Name: Home page
 */
get_header();
?>
<main id="main" class="quora front-page" role="main">
	<!-- Homepage carousel-->
    <div class="carousel-holder">
        <div class="social-popup popup-active hidden-xs">
            <a class="open" href="#"><i class="icon-share"></i></a>
            <div class="popup">
                <a class="close" href="#"><i class="icon-close"></i></a>
                <ul class="social-list">
                    <li class="facebook"><a href="http://www.facebook.com/makerfaire" target="_blank"><i class="icon-facebook"></i></a></li>
                    <li class="twitter"><a href="http://twitter.com/makerfaire"><i class="icon-twitter" target="_blank"></i></a></li>
                    <li class="instagram"><a href="//instagram.com/makerfaire" target="_blank"><i class="icon-instagram"></i></a></li>
                    <li class="googleplus"><a href="http://plus.google.com/communities/105823492396218903971" target="_blank"><i class="icon-googleplus"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="carousel-inner">
            <div class="mask">
                <div class="slideset">
                    <?php
                    $sorting = array( 'key' => 5, 'direction' => 'ASC' );
                    $search_criteria['status'] = 'active';
                    $entries = GFAPI::get_entries(24, $search_criteria, $sorting, array('offset' => 0, 'page_size' => 10));

                    ?>

                    <?php foreach ($entries as $entry): ?>
                    <div class="slide" data-url="<?php echo $entry['4'] ?>">
                        <div class="bg-stretch">
                            <a href="<?php echo $entry['4'] ?>"><img src="<?php echo legacy_get_resized_remote_image_url($entry['1'],1274,370); ?>" alt="Maker Faire slide show image"></a>
                        </div>
                        <div class="text-box">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12">
                                    <a href="<?php echo $entry['4'] ?>" style="color:#FFF;">
                                        <h1><?php echo $entry['2'] ?></h1>
                                        <p><?php echo $entry['3'] ?> <span class="icon-arrow-right"></span></p>
                                    </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="btn-box">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <a class="btn-prev" href="#"><span class="icon-arrow-left"></span></a>
                            <a class="btn-next" href="#"><span class="icon-arrow-right"></span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pagination">
            </div>
        </div>
    </div>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php the_content(); ?>
    <!-- The last holder-->
	<?php endwhile; ?>
<?php endif; ?>

</main>
<?php get_footer(); ?>