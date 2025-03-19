<?php

/**
 *  Creates a new custom yoast seo sitemap based on maker entry
 */
// only use this during development to disable sitemap caching :
// add_filter( 'wpseo_enable_xml_sitemap_transient_caching', '__return_false');

//define valid form types
$form_types = array('Exhibit', 'Presentation', 'Performance', 'Startup Sponsor', 'Sponsor', 'Workshop', 'Master');
//entry search criteria
$search_criteria = array();
$search_criteria['status'] = 'active';
$search_criteria['field_filters'][] = array('key' => '303', 'value' => 'Accepted');

/**
 * When the sitemap_index.xml page is accessed, add links to form sitemaps
 */
function faire_entries_sitemap_index($sitemap_index) {
   global $form_types;
   global $search_criteria;

   //generate a sitemap for each exhibit form
   $forms = GFAPI::get_forms(NULL, false);

   foreach ($forms as $form) {
      if (isset($form['form_type']) && in_array($form['form_type'], $form_types)) {
         //maybe check for accepted entries here                 
         $formId = $form['id'];

         //see if there are any entries that match search criteria
         $entries = GFAPI::get_entries((int)$formId, $search_criteria, null, array('offset' => 0, 'page_size' => 1));
         //if there are entries, go ahead and add the sitemap 
         if (!empty($entries)) {
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
   }
   return $sitemap_index;
}

add_filter("wpseo_sitemap_index", "faire_entries_sitemap_index", 99);


add_action('init', 'register_entries_sitemap', 99);

/**
 * On init, run the function that will register sitemaps for all gravity forms that match the defined form types
 */
function register_entries_sitemap() {
   global $wpseo_sitemaps;
   global $form_types;
   global $wpdb;
   if ($wpseo_sitemaps && is_array($form_types)) {
      $formResults = $wpdb->get_results('select display_meta, form_id from wp_gf_form_meta', ARRAY_A);
      foreach ($formResults as $formrow) {
         $form_id = $formrow['form_id'];

         $json = json_decode($formrow['display_meta']);
         $form_type = (isset($json->form_type) ? $json->form_type : '');
         
         if (in_array($form_type, $form_types)) {
            $wpseo_sitemaps->register_sitemap('form-' . $form_id . '-entries', 'faire_entries_sitemap_generate');          
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

   //get form id from current sitemap name ie) form43-entries-sitemap.xml
   $form_id = str_replace('-entries-sitemap.xml', '', $current_slug ?? '');
   $form_id = str_replace('form-', '', $current_slug ?? '');

   //retrieve list of entries   
   $entries = GFAPI::get_entries((int)$form_id, $search_criteria, null, array('offset' => 0, 'page_size' => 999));
   $urls = array();
   if (!empty($entries)) {
      foreach ($entries as $entry) {         
         $url['loc'] = site_url() . '/maker/entry/' . $entry['id'] . '/';
         $url['mod'] = $entry['date_updated'];
         $url['images'] = array();

         //project photos
         $project_photo = (isset($entry['22']) ? $entry['22'] : '');
         //for BA24, the single photo was changed to a multi image which messed things up a bit
         $photo = json_decode($project_photo);
         if (is_array($photo)) {
            $project_photo = $photo[0];
         }
         // this returns an array of image urls from the additional images field
         $project_gallery = (isset($entry['878']) ? json_decode($entry['878']) : '');

         //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery
         if ($project_photo == '' && is_array($project_gallery)) {
            $project_photo = $project_gallery[0];
         }

         $url['images'][] = array("src" => $project_photo);

         //additional photos
         if (isset($project_gallery) && !empty($project_gallery)) {
            foreach ($project_gallery as $key => $image) {
               if ($image != '') {
                  $url['images'][] = array("src" => $image);
               }
            }
         }

         $urls[] .= $wpseo_sitemaps->renderer->sitemap_url($url);
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
