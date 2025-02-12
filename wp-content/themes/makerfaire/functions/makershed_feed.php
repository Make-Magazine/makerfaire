<?php

// output the makershed related products
function makershedOutput($collection = MAKERSHED_DEFAULT_COLLECTION, $amount = "4") {
    global $wpdb;
    $wpdb->show_errors();
    $default_collection = MAKERSHED_DEFAULT_COLLECTION;
    // substract one from the amount because the default Make: Mag ad will take it's place
    --$amount;
    // Pull products by collection, limited to the amount set, and randomize
    $sql = "SELECT * FROM wp_makershedfeed 
            WHERE ( (select count(ID) from wp_makershedfeed where collection='$collection') > 0 AND collection='$collection' ) OR 
            ( (select count(ID) from wp_makershedfeed where collection='$collection') <= 0 AND collection='$default_collection' ) 
            ORDER BY RAND() LIMIT ".$amount;
    $makershedProducts = $wpdb->get_results($sql); //or die($wpdb->last_error);
    
    if(!empty($makershedProducts)) {
        // We need to get the term for the user friendly format of the collection name

        // Start building the html output
        $makershedOutput = '<div class="related-makershed-wrapper amount-'.($amount+1).' ' .$collection. '">';
        $makershedOutput .= '<h3>Learn, Create, and Share with <i>Make:</i></h3>';
            $makershedOutput .= '<div class="related-makershed-items card-deck">';
            foreach($makershedProducts as $product) {
                $makershedOutput .= '<div class="card">';
                    $makershedOutput .= '<a href="'.$product->link.'" class="related-makershed-item" target="_blank">';
                        $makershedOutput .= '<div class="card-header"><img src="'.$product->image.'" alt="'.$product->title.'" /></div>';
                        $makershedOutput .= '<div class="card-body"><div class="card-text">';
                            $makershedOutput .= '<h4>'.$product->title.'</h4>';
                            $makershedOutput .= '<div class="price">$'.$product->price.'</div>';
                        $makershedOutput .= '</div></div>';
                    $makershedOutput .= '</a>';
                $makershedOutput .= '</div>';
            }
            $makershedOutput .= '<div class="card">
                                    <a href="https://subscribe.makezine.com/loading.do?omedasite=Make_subscribe&PK=M3GCT015&utm_source=makerfaire.com&utm_medium=cross-site&utm_campaign=makershed_related&utm_content=yearbook_ms_widget_subscribe_link" class="related-makershed-item" target="_blank">
                                        <div class="card-header"><img src="https://make.co/wp-content/universal-assets/v2/images/make-magazine-cover-large.jpg" alt="Subscribe to Make: Magazine Today" /></div>
                                        <div class="card-body"><div class="card-text">
                                            <h4>Get <i>Make:</i> Magazine</h4>
                                            <div class="price">$19.99</div>
                                        </div></div>
                                    </a>
                                </div>';
            $makershedOutput .= '</div>';
        $makershedOutput .= "</div>";
        
        return $makershedOutput;
    } else {
        return;
    }
}

//add the makershed collection ACF field to the admin taxonomy page for mf-projects
/**
 * Add ACF thumbnail columns to Linen Category custom taxonomy
 */
function add_makershed_collection_column($columns) {
    $columns['makershed_collection'] = __('Maker Shed Collection');    
    return $columns;
}
add_filter('manage_edit-mf-project-cat_columns', 'add_makershed_collection_column');

/**
 * Output ACF thumbnail content in Linen Category custom taxonomy columns
 */
function collection_columns_content($content, $column_name, $term_id) {
    if ('makershed_collection' == $column_name) {        
        $primary_collection = get_field("makershed_collection", "mf-project-cat_" . $term_id);                
        $content = $primary_collection;

        }
    return $content;
}
add_filter('manage_mf-project-cat_custom_column' , 'collection_columns_content' , 10 , 3);