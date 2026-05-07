<?php

use Shopify\PrivateApp;
use rdx\graphqlquery\Query;

add_action('makershed_feed_cron', 'build_makershed_feed_table', 9999999);

function build_makershed_feed_table() {

    require_once __DIR__ . '/../vendor/autoload.php';

    if (UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX != "https://make.co/") {
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

    $collections = $wpdb->get_results($sqlquery, ARRAY_A);

    // make sure our default collection gets updated too
    $collections[] = ['makershed_collection' => MAKERSHED_DEFAULT_COLLECTION];

    // Move client instantiation outside the loop — no need to reconnect on every iteration
    $configs = include(dirname(__DIR__) . '/configs/shopify_graphQL_config.php');
    $api_params['version'] = $configs['version'];
    $client = new Shopify\PrivateApp(
        $configs['shop'],
        $configs['password'],
        $configs['access_token'],
        $api_params
    );

    $table = $wpdb->prefix . 'makershedfeed';

    foreach ($collections as $collection) {
        $collection = $collection['makershed_collection'];

        $query = Query::query("");
        $query->fields('collectionByHandle');
        $query->collectionByHandle->attribute('handle', $collection);
        $products = '
            products(first:25){
                edges{
                    node{
                        title
                        status
                        handle
                        publishedOnCurrentPublication
                        priceRangeV2{
                            minVariantPrice{
                                amount
                            }
                        }
                        variants(first:1){
                            edges{
                                node{
                                    availableForSale
                                }
                            }
                        }
                        images(first:1){
                            edges{
                                node{
                                    url
                                }
                            }
                        }
                    }
                }
            }';
        $query->collectionByHandle->fields(['id', 'title', $products]);
        $graphqlString = $query->build();
        $results = $client->callGraphql($graphqlString);

        if (empty($results['data']['collectionByHandle'])) {
            error_log("Makershed Feed: no collection found for {$collection}");
            continue; // skip to next collection rather than bailing entirely
        }

        $makershedProducts = $results['data']['collectionByHandle']['products']['edges'] ?? [];

        // Filter before touching the DB
        $validProducts = [];
        foreach ($makershedProducts as $product) {
            $node = $product['node'];

            $availableForSale = $node['variants']['edges'][0]['node']['availableForSale'] ?? false;
            $isActive         = $node['status'] === 'ACTIVE';
            $isPublished      = $node['publishedOnCurrentPublication'] ?? false;
            $hasImage         = isset($node['images']['edges'][0]);

            if (!$availableForSale || !$isActive || !$isPublished || !$hasImage) {
                continue;
            }

            $validProducts[] = $node;
        }

        if (empty($validProducts)) {
            error_log("Makershed Feed: no valid products for {$collection}, table left unchanged.");
            continue;
        }

        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$table} WHERE collection = %s",
            $collection
        ));

        if ($deleted === false) {
            error_log("Makershed Feed: DELETE failed for {$collection}: " . $wpdb->last_error);
            continue;
        }

        foreach ($validProducts as $node) {
            $link = 'https://www.makershed.com/products/' . $node['handle']
                . '?utm_source=makerfaire.com&utm_medium=cross-site'
                . '&utm_campaign=makershed_related';

            $result = $wpdb->insert($table, [
                'title'      => $node['title'],
                'link'       => $link,
                'image'      => $node['images']['edges'][0]['node']['url'],
                'price'      => $node['priceRangeV2']['minVariantPrice']['amount'] ?? '',
                'collection' => $collection,
            ]);

            if ($result === false) {
                error_log('Makershed insert failed for "' . $node['title'] . '" in collection "' . $collection . '": ' . $wpdb->last_error);
            }
        }

        error_log("Makershed Feed: inserted " . count($validProducts) . " products for {$collection}.");
    }
}