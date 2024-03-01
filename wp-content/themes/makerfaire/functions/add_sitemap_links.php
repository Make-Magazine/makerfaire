<?php
/**
 *  Creates a new custom yoast seo sitemap based on maker entry
 */
// only use this during development to disable sitemap caching :
// add_filter( 'wpseo_enable_xml_sitemap_transient_caching', '__return_false');

//define valid form types
$form_types = array('Exhibit', 'Presentation', 'Performance', 'Startup Sponsor', 'Sponsor', 'Workshop', 'Master');

//for testing ONLY
add_filter("wpseo_enable_xml_sitemap_transient_caching", "__return_false");

/**
 * When the sitemap_index.xml page is accessed, add links to form sitemaps
 */
function faire_entries_sitemap_index($sitemap_index) {
   global $form_types;

   //generate a sitemap for each exhibit form
   $forms = GFAPI::get_forms(true, false);
   
   foreach ($forms as $form) {
   	if (isset($form['form_type']) && in_array($form['form_type'],$form_types)) {                  
         $formId = $form['id'];     
         $sitemap_url = home_url("form-$formId-entries-sitemap.xml");
         $sitemap_date = date(DATE_W3C);  # Current date and time in sitemap format.
         
         $faire_entries = <<<SITEMAP_INDEX_ENTRY
         <sitemap>
            <loc>%s</loc>
            <lastmod>%s</lastmod>
         </sitemap>
         SITEMAP_INDEX_ENTRY;
            $sitemap_index .= sprintf($faire_entries, $sitemap_url, $sitemap_date);
      }
   }
   return $sitemap_index;
}

add_filter("wpseo_sitemap_index", "faire_entries_sitemap_index", 99);


add_action('init', 'register_entries_sitemap', 99);

/**
 * On init, run the function that will register sitemaps for all gravity forms that match the defined form types
 */
function register_entries_sitemap() {
   global $wpseo_sitemaps; global $form_types;
   if ($wpseo_sitemaps && is_array($form_types)) {
      //generate a sitemap for each exhibit form
      $forms = GFAPI::get_forms(true, false);
      
      foreach ($forms as $form) {
         if (isset($form['form_type']) && in_array($form['form_type'],$form_types)) {
            $formId = $form['fields'][0]['formId'];
            $wpseo_sitemaps->register_sitemap("form-$formId-entries", 'faire_entries_sitemap_generate');
         }
      }      
   }
}

/**
 * Generate faire_entries sitemap XML body
 * This is triggered when the specific form sitemap is accessed
 */
function faire_entries_sitemap_generate() {
   global $wpseo_sitemaps;   
   global $search_criteria;
   
   //determine what form this sitemap this is for
   global $wp;
   $current_slug = add_query_arg(array(), $wp->request);

   //get form id from current sitemap name ie) form-43-entries-sitemap.xml
   $form_id = str_replace('-entries-sitemap.xml', '', $current_slug);
   $form_id = str_replace('form-', '', $current_slug);

   //entry search criteria
   $search_criteria['status'] = 'active';
   $search_criteria['field_filters'][] = array('key' => '303', 'value' => 'Accepted');

   //retrieve list of entries   
   $entries = GFAPI::get_entries((int)$form_id, $search_criteria, null, array('offset' => 0, 'page_size' => 999));

   if (!empty($entries)) {
      foreach ($entries as $entry) {
         //error_log('adding entry '.$entry['id']);
         $urls[] = $wpseo_sitemaps->renderer->sitemap_url(array(
            "mod" => $entry['date_updated'],  # <lastmod></lastmod>
            "loc" => site_url() . '/maker/entry/' . $entry['id'] . '/',  # <loc></loc>
            "images" => array(
               array(  # <image:image></image:image>
                  "src" => (isset($entry['22']) ? $entry['22'] : ''),  # <image:loc></image:loc>
                  "title" => (isset($entry['151']) ? $entry['151'] : ''),  # <image:title></image:title>
                  "alt" => (isset($entry['151']) ? $entry['151'] : ''),  # <image:caption></image:caption>
               ),
            ),
         ));
      }
      $sitemap_body = <<<SITEMAP_BODY
            <urlset
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
                xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd"
                xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            %s
            </urlset>
            SITEMAP_BODY;
      $sitemap = sprintf($sitemap_body, implode("\n", $urls));
      $wpseo_sitemaps->set_sitemap($sitemap);
   } //end check if entries      
}