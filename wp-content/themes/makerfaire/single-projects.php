<?php get_header(); ?>

<article>
    <?php
    $faireData = get_field("faire_information");
    $exhibit_photo = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' )[0];
    $exhibit_video = get_field("exhibit_video_link");
    $exhibit_inspiration = get_field("exhibit_inspiration");
    $exhibit_additional_images = get_field("additional_exhibit_images");
    $exhibit_social = get_field("exhibit_social");
    $exhibit_website = get_field("exhibit_website");
    $faire_name = (isset($faireData["faire_post"]->post_title)?$faireData["faire_post"]->post_title:'');
    $faire_id = $faireData["faire_post"]->ID;
    $hero_bg = isset(get_field("top_section", $faire_id)['hero_image']['url']) ? get_field("top_section", $faire_id)['hero_image']['url'] : get_template_directory() . "/images/eoy/YB_MF_23_1400_Default-1.png"; ;
    
    $contact_location = get_field('project_location');
    $contact_state = $contact_location['state'];
    $contact_country = $contact_location['country'];

    $faire_year = (isset($faireData["faire_year"]->name)?$faireData["faire_year"]->name:'');
    
    ?>
    <header id="project-hero" style="background-image:url('<?php echo $hero_bg; ?>');">
        <div class="hero-overlay"></div>
        <div class="breadcrumbs">
            <a href="/yearbook/<?php echo $faire_year; ?>/faires">Home</a> / <a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/yearbook/' . $faire_year; ?>/projects">Projects</a>
        </div>
    </header>
    
    <section id="project-info-section" class="container">
        <div class="project-info">
            <h3 class="faire-details"><?php echo $faire_name ." ".$faire_year;?></h3>
            <h1 class="project-title"><?php echo get_field("title");?></h1>
            <h4><?php
                 if(!empty($contact_state)) {
                    echo $contact_state . ", ";
                 }
                 echo $contact_country; 
                 ?>
            </h4>
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
    
    <?php if(!empty($exhibit_video) || !empty($exhibit_inspiration)){ ?>
        <section id="project-highlights-section">
            <span class="striped-background"></span>
            <?php if($exhibit_video && is_valid_video($exhibit_video)) { ?>
                <div class="project-video">            
                    <?php
                        global $wp_embed;
                        echo $wp_embed->run_shortcode('[embed]' . $exhibit_video . '[/embed]');
                    ?>
                </div>
            <?php } ?>
            <div class="project-inspiration">
                <?php
                if($exhibit_inspiration!=''){
                    echo '<h3>What Inspired You to Make This?</h3>';
                    echo '<p>'.$exhibit_inspiration.'</p>';
                }
                ?>
                <div class="blue-spacer"></div>
            </div>
        </section>
    <?php } ?>
    
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
                    <div class="img-wrap">
                        <img src="<?php echo $maker["maker_photo"]["url"];?>" />
                    </div>
                    <h4><?php echo $maker["maker_or_group_name"];?></h4>
                    <p class="maker-bio"><?php echo $maker["maker_bio"]?></p>
                    <div class="social-links reversed">
                        <?php 
                        if(!empty($maker['maker_social'])) {
                            foreach($maker['maker_social'] as $link) {
                                if($link) {
                                    echo('<a class="link" href="' . $link['maker_social_link'] . '"></a>');
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