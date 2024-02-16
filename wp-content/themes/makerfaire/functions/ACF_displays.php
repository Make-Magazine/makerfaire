<?php

function do_image_grid($args){
    // echo '<pre style="display: none;">';
    // var_dump($args);
    // echo '</pre>';
    $return = '';
    $promote_url = get_field('promote_link');
    if($promote_url=='')
       $promote_url = 'https://makerfaire.com/bay-area'; 
    
    // Image Grid
    if (have_rows('image_grid')) {
        // loop through the rows of data
        while (have_rows('image_grid')) {
            the_row();
            $return .= '<div class="image_grid">';
            $return .= '<h2>' . get_sub_field('title') . '</h2>';
            //get list of images
            if (have_rows('image_section')) {
                // loop through the rows of data
                $return .= '<div class="row">';

                while (have_rows('image_section')) {
                    the_row();
                    $imageArr  = get_sub_field('grid_image');                    
                    $image_url = $imageArr['sizes']['thumbnail'];
                    $return .= '<div class="col-xs-4 col-sm-3 col-md-2 grid-padding">';                    
                    $return .= '    <a target="_blank" href="' . $image_url . '"><div class="grid-image" style="background-image:url(' . $image_url . ');"></div></a>';
                    $return .= '    <div class="img-size">' . $imageArr['width'] . ' x ' . $imageArr['height'] . '</div>';
                    $return .= '    <button class="btn universal-btn btn-info btn-copy-html" onclick="copyMe(\'img_' . $imageArr['id'] . '\')">COPY HTML</button>';
                    $return .= '    <div class="copyDiv" id="img_' . $imageArr['id'] . '">';
                    $return .=         '<a href="'.$promote_url.'">&lt;img src="' .$imageArr['url'] . '" alt="' . $imageArr['title'] . '" width="' . $imageArr['width'] . '" height="' . $imageArr['height'] . '" border="0" /&gt;</a>';
                    $return .= '    </div>';
                    
                    if (class_exists('Jetpack') && in_array('photon', Jetpack::get_active_modules())) {
                        $image_components = parse_url($image_url);
                        $image_path = $image_components['path'];
                        $return .= '<a target="_blank" class="download_btn" href="https:/' . $image_path . '" download="' . strtok(basename($image_url), '.') . '">Download</a>';
                    }

                    $return .= '</div>';
                }
                $return .= '</div>';
            }
            $return .= '</div>';
        }
    }

    echo $return;
}

function do_featured_presenter_grid($args) {
    // echo '<pre style="display: none;">';
    // var_dump($args);
    // echo '</pre>';
    $content = '';
    $content .= '<div class="featured-image-grid">';
    // $content .= '<div class="row">';
    // $content .= '<div class="col-xs-12 grid-inner">';
    foreach ($args as $key => $value) {
        $content .= '<div class="grid-item" style="background-image:url(' . $value['pres_image'] . ');">';

        $content .= '  <div class="grid-item-title-block">';
        $content .= '     <h3>' . $value['pres_name'] . '</h3>';
        $content .= '     <p>' . $value['pres_title'] . '</p>';
        $content .= '  </div>';

        $content .= '<div class="grid-item-desc">';
        if (!empty($value['event_title'])) {
            $content .= '     <h4>' . $value['event_title'] . '</h4>';
        }
        if (!empty($value['event_datetime'])) {
            $content .= '     <p class="dates">' . $value['event_datetime'] . '</p>';
        }

        $desc = $value['event_desc'];

        $content .= '        <p class="desc-body">' . $desc . '</p>';

        if (!empty($value['button_url']) && !empty($value['button_text'])) {
            $content .= '     <a href="' . $value['button_url'] . '" class="btn btn-blue read-more-link">' . $value['button_text'] . '</a>';
        }

        $content .= '  </div>';  // end desc

        $content .= '</div>'; // end grid item
    }
    // $content .= '</div>'; // end col
    // $content .= '</div>'; // end row
    $content .= '</div>'; // end container

    $content .= '<mask id="mask" maskContentUnits="objectBoundingBox">
					  <rect width="1" height="1" fill="url(#gradient)"/>
					  <linearGradient x2="0" y2="1" id="gradient">
						 <stop offset="25%" stop-color="white" />
						 <stop offset="50%" stop-color="black" />
					  </linearGradient>
					</mask>';

    $content .= '<script type="text/javascript">
						function fitTextToBox(){
							jQuery(".grid-item").each(function() {
							    var availableHeight = jQuery(this).innerHeight() - 30;
								 if(jQuery(this).find(".read-more-link").length > 0){
									 availableHeight = availableHeight - jQuery(this).find(".read-more-link").innerHeight() - 30;
								 }

								 jQuery(jQuery(this).find(".desc-body")).css("mask-image", "-webkit-linear-gradient(top, rgba(0,0,0,1) 80%, rgba(0,0,0,0) 100%)");

								 if( 561 > jQuery(window).width() ) {
								   jQuery(jQuery(this).find(".desc-body")).css("mask-image", "none");
									jQuery(jQuery(this).find(".desc-body")).css("height", "auto");
								 } else {
								 	jQuery(jQuery(this).find(".desc-body")).css("height", availableHeight);
								 }
							 });
						}
	                jQuery(document).ready(function(){
						    fitTextToBox();
						 });
						 jQuery(window).resize(function(){
						 	 fitTextToBox();
						 });
					 </script>';

    echo $content;
}

function get_acf_content() {
    $mappings = array(
        'image_grid' => 'do_image_grid',
        'featured_presenter_grid' => 'do_featured_presenter_grid'
    );
    $all_fields = get_fields();
    // echo '<pre style="display: none;">';
    // var_dump($all_fields);
    // echo '</pre>';
    if (is_array($all_fields)) {
        foreach ($all_fields as $key => $value) {
            if (is_array($value)) {
                //echo 'handle array ' . $key . ' ' . $mappings[$key] . '<br />';
                if (!empty($mappings[$key])) {
                    //echo 'handler ' . $mappings[$key] . '<br />';
                    $mappings[$key]($value);
                }
            } else {
                //echo $value . '<br />';
            }
        };
    }
}
