<?php

/**
 *  Creates a new custom yoast seo sitemap based on maker entry
 */
// only use this during development to disable sitemap caching :
// add_filter( 'wpseo_enable_xml_sitemap_transient_caching', '__return_false');

add_filter('wpseo_sitemap_index', 'add_entries_sitemap_to_index', 99);
//add_action('init', 'add_entries_sitemap_to_wpseo');
$form_types = array('Exhibit','Presentation','Performance','Startup Sponsor','Sponsor','Workshop');
$search_criteria['status'] = 'active';
$search_criteria['field_filters'][] = array('key' => '303', 'value' => 'Accepted');


// Add custom index
function add_entries_sitemap_to_index($smp) {
   global $form_types; global $search_criteria;
   
   //generate a sitemap for each exhibit form
   $forms = GFAPI::get_forms(true, false);
   
   foreach ($forms as $form) {
   	if (isset($form['form_type']) && in_array($form['form_type'],$form_types)) {         
         
         $formId = $form['fields'][0]['formId'];     
         //see if there are any entries that match search criteria
         $entries = GFAPI::get_entries((int)$formId, $search_criteria, null, array('offset' => 0, 'page_size' => 1));
         if(!empty($entries)){
            $smp .= '<sitemap>' . PHP_EOL;
            $smp .= '<loc>' . site_url() . "/form-$formId-entries-sitemap.xml</loc>" . PHP_EOL;   
            $smp .= '</sitemap>' . PHP_EOL;
         }
      }
   }
   
   

   return $smp;
}

function add_entries_sitemap_to_wpseo() {   
   add_action("wpseo_do_sitemap_BA19-entries", 'generate_entries_sitemap');
}

function generate_entries_sitemap($args) {
   global $wp;
   $current_slug = add_query_arg( array(), $wp->request );
   //get form id from current sitemap name ie) form-43-entries-sitemap.xml
   $form_id = str_replace('-entries-sitemap.xml','',$current_slug);
   $form_id = str_replace('form-','',$current_slug);
   
   global $wpseo_sitemaps;   
   global $search_criteria;
   
   //retrieve list of entries   
   $entries = GFAPI::get_entries((int)$form_id, $search_criteria, null, array('offset' => 0, 'page_size' => 999));

   $output = '';

   $chf = 'weekly';
   $pri = 1.0;

   foreach ($entries as $entry) {
      $url = array();
      $url['mod'] = $entry['date_updated'];
      $url['loc'] = site_url() . '/maker/entry/' . $entry['id'].'/';
      $url['chf'] = $chf;
      $url['pri'] = $pri;
      /*$image = [];
      if ($entry[22]) {
         $image["src"] = [22];
         $image["title"] = $entry[151];
      }
      $url['images'] = [$image];*/
      $output .= $wpseo_sitemaps->renderer->sitemap_url($url);
   }


   //Build the full sitemap
   $sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
   $sitemap .= $output . '</urlset>';

   $wpseo_sitemaps->set_sitemap($sitemap);
}

/* * *******************************************************
 *  OR we can use $wpseo_sitemaps->register_sitemap( 'entry', 'METHOD' );
 * ****************************************************** */

add_action('init', 'register_entries_sitemap', 99);

/**
 * On init, run the function that will register our new sitemap as well
 * as the function that will be used to generate the XML. This creates an
 * action that we can hook into built around the new
 * sitemap name - 'wp_seo_do_sitemap_*'
 */
function register_entries_sitemap() {
   global $wpseo_sitemaps; global $form_types;
   if ($wpseo_sitemaps) {
      //generate a sitemap for each exhibit form
      $forms = GFAPI::get_forms(true, false);
      
      foreach ($forms as $form) {
         if (isset($form['form_type']) && in_array($form['form_type'],$form_types)) {
            $formId = $form['fields'][0]['formId'];
            $wpseo_sitemaps->register_sitemap("form-$formId-entries", 'generate_entries_sitemap');
         }
      }      
   }
}
/*
add_action('init', 'init_do_sitemap_actions');

function init_do_sitemap_actions() {
   add_action('wp_seo_do_sitemap_entries', 'generate_entries_sitemap');
}
 * 
 */
