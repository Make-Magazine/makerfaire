<?php get_header(); ?>

<article>
    <?php
    $faireData = get_field("faire_information");
    $exhibit_photo = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' )[0];
    $exhibit_video = get_field("exhibit_video_link");
    $exhibit_additional_images = get_field("additional_exhibit_images");
    $exhibit_social = get_field("exhibit_social");
    $exhibit_website = get_field("exhibit_website");
    
    $faire_name = (isset($faireData["faire_post"]->post_title)?$faireData["faire_post"]->post_title:'');
    $faire_id = $faireData["faire_post"]->ID;
    $event_id = get_post_meta($faire_id, '_event_id');
    $EM_Event = new EM_Event( $event_id[0] );
    $EM_Location = $EM_Event->get_location();
    $faire_location = $EM_Location->location_town;
    $faire_state = $EM_Location->location_state;
    $faire_countries = em_get_countries();
    $faire_country = $EM_Location->location_country;
    if(!empty($faire_state) && $faire_country == "US") {
        $faire_location .= ", " . $faire_state;
    }
    $faire_location .= ", " . $faire_countries[$faire_country];
    $faire_year = (isset($faireData["faire_year"]->name)?$faireData["faire_year"]->name:'');
    
    ?>
    <header id="project-hero" style="background-image:url('');">
        <div class="hero-overlay"></div>
        <div class="breadcrumbs">
            <a href="/yearbook/<?php echo $faire_year; ?>/faires">Home</a> / <a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $faire_year; ?>/projects">Projects</a>
        </div>
    </header>
    
    <section id="project-info-section" class="container">
        <div class="project-info">
            <h3 class="faire-details"><?php echo $faire_name ." ".$faire_year;?></h3>
            <h1 class="project-title"><?php echo get_field("title");?></h1>
            <h4><?php echo $faire_location; ?></h4>
            <div class="blue-spacer"></div>
            <p><?php echo get_field("exhibit_description");?></p>
            <div class="social-links reversed">
				<?php foreach ($exhibit_social[0] as $link) {
					if($link) {
						echo('<a class="link" href="' . $link . '"></a>');
					}
				}
                if(!empty($exhibit_website)) { ?>
				    <a class="link fa fa-link" href="<?php echo $exhibit_website; ?>" target="_blank"></a>
                <?php } ?>
			</div>          
        </div>
        <div class="project-picture">
            <img class="featured-image" src="<?php echo $exhibit_photo;?>" />
        </div>
    </section>

    <section id="project-highlights-section">
        <div class="project-video">            
			<?php
			if($exhibit_video && is_valid_video($exhibit_video)) {
				global $wp_embed;
				echo $wp_embed->run_shortcode('[embed]' . $exhibit_video . '[/embed]');
			} else { ?>
				<img src="<?php echo $exhibit_additional_images[0]['url']; ?>" alt="<?php the_title(); ?> Featured Image" />
			<?php }
			?>
        </div>
        <div class="project-inspiration">
            <div class="striped-background"></div>
            <?php
            $exhibit_inspiration = get_field("exhibit_inspiration");
            if($exhibit_inspiration!=''){
                echo '<h3>What Inspired You to Make This?</h3>';
                echo '<p>'.$exhibit_inspiration.'</p>';
            }
            ?>
            <div class="blue-spacer"></div>
        </div>
    </section>
    
    <section id="project-makers" class="container">
    <!-- Maker Data -->
    <?php
    $maker_data = get_field("maker_data");
    if(!empty($maker_data)) {
        if(count($maker_data) == 1) { ?>
            <div class="single-maker-info">
                <img src="<?php echo $maker_data[0]["maker_photo"]["url"]; ?>" alt="<?php echo $maker_data[0]["maker_or_group_name"]; ?> Maker Photo">
                <p><b>Name:</b> <?php echo $maker_data[0]["maker_or_group_name"]; ?></p>
                <div class="social-links reversed">
                    <?php 
                    if(!empty($maker_data[0]['maker_social'])) {
                        foreach($maker_data[0]['maker_social'] as $link) {
                            if($link) {
                                echo('<a class="link" href="' . $link . '"></a>');
                            }
                        } 
                    }
                    if(!empty($maker_data[0]["maker_website"])) { ?>
                        <a class="link fa fa-link" href="<?php echo $maker_data[0]["maker_website"]; ?>" target="_blank"></a>
                    <?php } ?>
                </div>
            </div>
            <div class="single-maker-bio">
                <p><?php echo $maker_data[0]["maker_bio"]?></p>
            </div>
        <?php } else { // we got a different layout for multiple makers ?>
            <h2>Makers</h2>
            <div class="multiple-maker-wrapper">
        <?php foreach($maker_data as $maker){ ?>
                <div class="maker-wrapper">
                    <img style="max-height:360px" src="<?php echo $maker["maker_photo"]["url"];?>" />
                    <h4><?php echo $maker["maker_or_group_name"];?></h4>
                    <p><?php echo $maker["maker_bio"]?></p>
                    <div class="social-links reversed">
                        <?php 
                        if(!empty($maker['maker_social'])) {
                            foreach($maker['maker_social'] as $link) {
                                if($link) {
                                    echo('<a class="link" href="' . $link . '"></a>');
                                }
                            } 
                        }
                        if(!empty($maker["maker_website"])) { ?>
                            <a class="link fa fa-link" href="<?php echo $maker["maker_website"]; ?>" target="_blank"></a>
                        <?php } ?>
                    </div>
                </div>
                <?php
            } ?>
            </div>
            <?php
        }
    }
    
    
    
    //echo '<div>'.$exhibit_social
    
        $faire = ( isset( $_GET['faire'] ) && ! empty( $_GET['faire'] ) ) ? sanitize_title( $_GET['faire'] ) : '';				
    ?>
    </section>


</article><!--Container-->

<?php get_footer(); ?>