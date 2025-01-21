<?php
require(__DIR__ . '/../vendor/autoload.php');
use Shopify\PrivateApp;
use rdx\graphqlquery\Query;

// Nightly Cron job code uses argument from the cron job to run a graphQL query pulling from each makershed collection and writing to a mz database
add_action( 'makershed_feed_cron', 'build_makershed_feed_table', 9999999 );
function build_makershed_feed_table() {
    global $wpdb;
    $wpdb->show_errors();
    $query = "SELECT DISTINCT 
               t.term_id
              FROM
               {$wpdb->terms} t 
              INNER JOIN 
               {$wpdb->term_taxonomy} tax 
              ON 
               tax.term_id = t.term_id
              WHERE 
               ( tax.taxonomy = 'mf-project-cat')";                     
    $result = $wpdb->get_results( $query , ARRAY_A );

    //error_log(print_r($result, TRUE));
    
    $collections = array("maker-faire-wear");
    foreach($result as $yb_category) {
        $collections[] = get_field("makershed_collection", "mf-project-cat_" . $yb_category['term_id']);
    }

    // remove empty values, dedupe and reorder array
    $collections = array_values(array_unique(array_filter($collections)));

    foreach($collections as $collection) {

        // if the cron didn't specify an argument, let's get out of here. nothing to be done
        if($collection == "not-set") {
            error_log("Makershed Feed cronjob was run without collection set");
            die;
        } 
        //pull parameters for shopify graphQL sdk query from our config file
        $configs = include(dirname(__DIR__).'/configs/shopify_graphQL_config.php');
        $api_params['version'] = $configs['version'];
        $client = new Shopify\PrivateApp($configs['shop'], $configs['password'], $configs['access_token'], $api_params);
        
        $query = Query::query("");
        $query->fields('collectionByHandle');
        $query->collectionByHandle->attribute('handle', $collection);
        // ID will allow us to sort new to old
        $products = 'products(first:20,sortKey:ID,reverse:true){edges{node{title,status,handle,variants(first: 1){edges{node{price,availableForSale}}},images(first:1){edges{node{url}}}}}}';
        $query->collectionByHandle->fields(['id','title',$products]);
        $graphqlString = $query->build();
        
        $results = $client->callGraphql($graphqlString);
        
        // if we get results, delete what we had previously and write the new results to the wp_makershedfeed table
        if(!empty($results['data']['collectionByHandle'])) {
            // delete the olde
            $wpdb->query($wpdb->prepare("DELETE FROM wp_makershedfeed WHERE collection = %s", $collection));
            
            // get all the products resturned in the results
            $makershedProducts = (isset($results['data']['collectionByHandle']['products']['edges']) ? $results['data']['collectionByHandle']['products']['edges'] : array());
            $current = 0;
            foreach($makershedProducts as $product) {
                // remove all the items that have nothing available for sale and write what is left to the database
                if($product['node']['variants']['edges'][0]['node']['availableForSale'] != 1 || $product['node']['status'] != "ACTIVE") {
                    unset($makershedProducts[$current]);
                    ++$current;
                } else {
                    // built the list with utm parameters for tracking
                    $link = 'https://www.makershed.com/products/'. $product['node']['handle'] . '?utm_source=makezine&utm_medium=related&utm_campaign=makershed&utm_content=launch';
                    // insert our refreshed products into the table if it has an image
                    if(isset($product['node']['images']['edges'][0])) {
                        $wpdb->insert('wp_makershedfeed', array('title' => $product['node']['title'], 'link' => $link, 'image' => $product['node']['images']['edges'][0]['node']['url'], 'price' => $product['node']['variants']['edges'][0]['node']['price'], 'collection' => $collection));
                        //error_log($wpdb->last_query);
                    }
                }
            }
        } else {
            error_log("Makershed Feed cronjob was run for " . $collection . ", but no collection exists");
        }
    }
}


// output the makershed related products
function makershedOutput($collection = "maker-faire-wear", $amount = "4") {
    global $wpdb;
    $wpdb->show_errors();
    // Pull products by collection, limited to the amount set, and randomize
    $sql = "SELECT * FROM wp_makershedfeed WHERE collection LIKE '$collection' ORDER BY RAND() LIMIT ".$amount."";
    $makershedProducts = $wpdb->get_results($sql); //or die($wpdb->last_error);
    
    if(!empty($makershedProducts)) {
        // We need to get the term for the user friendly format of the collection name

        // Start building the html output
        $makershedOutput = '<div class="related-makershed-wrapper amount-'.$amount.'">';
        $makershedOutput .= '<h3>From the Shed:</h3>';
            $makershedOutput .= '<div class="related-makershed-items">';
            foreach($makershedProducts as $product) {
                $makershedOutput .= '<a href="'.$product->link.'" class="related-makershed-item" target="_blank">';
                $makershedOutput .= '<div class="image-square"><img src="'.$product->image.'" alt="'.$product->title.'" /></div>';
                $makershedOutput .= '<h4>'.$product->title.'</h4>';
                $makershedOutput .= '<div class="item-price">$'.$product->price.'</div>';
                $makershedOutput .= '</a>';
            }
            $makershedOutput .= '</div>';
        $makershedOutput .= "</div>";
        
        return $makershedOutput;
    } else {
        return;
    }
}



