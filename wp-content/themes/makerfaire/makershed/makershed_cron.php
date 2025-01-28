<?php

require(__DIR__ . '/../vendor/autoload.php');
use Shopify\PrivateApp;
use rdx\graphqlquery\Query;

// Nightly Cron job code uses argument from the cron job to run a graphQL query pulling from each makershed collection and writing to a mz database
add_action( 'makershed_feed_cron', 'build_makershed_feed_table', 9999999 );
function build_makershed_feed_table() {
    error_log("is this thing on");
    // check that we are on prod, otherwise don't run any queries or graphql calls
    if(UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX != "https://make.co/") {
        error_log("Makershed Table Build not run, not a production environment.");
        return;
    }
    global $wpdb;

    $sqlquery = "SELECT distinct(wp_termmeta.meta_value) 
                as makershed_collection 
                FROM wp_term_taxonomy tax 
                inner join wp_termmeta 
                on wp_termmeta.term_id = tax.term_id 
                WHERE tax.taxonomy = 'mf-project-cat' 
                and wp_termmeta.meta_key = 'makershed_collection'";    
                                 
    $collections = $wpdb->get_results( $sqlquery , ARRAY_A );

    // make sure our default collection gets updated too
    $collections[] = array("makershed_collection" => MAKERSHED_DEFAULT_COLLECTION);

    foreach($collections as $collection) {
        // get just the collection name/slug
        $collection = $collection['makershed_collection'];

        //pull parameters for shopify graphQL sdk query from our config file
        $configs = include(dirname(__DIR__).'/configs/shopify_graphQL_config.php');
        $api_params['version'] = $configs['version'];
        $client = new Shopify\PrivateApp($configs['shop'], $configs['password'], $configs['access_token'], $api_params);
        
        $query = Query::query("");
        $query->fields('collectionByHandle');
        $query->collectionByHandle->attribute('handle', $collection);
        // ID will allow us to sort new to old
        $products = 'products(first:15){edges{node{title,status,handle,variants(first: 1){edges{node{price,availableForSale}}},images(first:1){edges{node{url}}}}}}';
        $query->collectionByHandle->fields(['id','title',$products]);
        $graphqlString = $query->build();
        
        $results = $client->callGraphql($graphqlString);
        
        // if we get results, delete what we had previously and write the new results to the wp_makershedfeed table
        if(!empty($results['data']['collectionByHandle'])) {
            // delete the olde
            $wpdb->query($wpdb->prepare("DELETE FROM wp_makershedfeed WHERE collection = %s", $collection));
            
            // get all the products resturned in the results
            $makershedProducts = (isset($results['data']['collectionByHandle']['products']['edges']) ? $results['data']['collectionByHandle']['products']['edges'] : array());
            foreach($makershedProducts as $product) {
                // remove all the items that have nothing available for sale and write what is left to the database
                if($product['node']['variants']['edges'][0]['node']['availableForSale'] != 1 || $product['node']['status'] != "ACTIVE") {
                    // this item is not for sale, so we don't want to show it
                    continue;
                } else {
                    // built the list with utm parameters for tracking
                    $link = 'https://www.makershed.com/products/'. $product['node']['handle'] . '?utm_source=makerfaire&utm_medium=yearbook&utm_campaign=makershed&utm_content=launch';
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
