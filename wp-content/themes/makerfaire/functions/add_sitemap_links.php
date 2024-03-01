<?php
//new methods for generating faire exhibit sitemap
/**
 * Add faire-entries-sitemap.xml to Yoast sitemap index
 */
function faire_entries_sitemap_index($sitemap_index) {
   global $wpseo_sitemaps;
   $sitemap_url = home_url("faire_entries-sitemap.xml");
   $sitemap_date = date(DATE_W3C);  # Current date and time in sitemap format.
   $faire_entries = <<<SITEMAP_INDEX_ENTRY
<sitemap>
   <loc>%s</loc>
   <lastmod>%s</lastmod>
</sitemap>
SITEMAP_INDEX_ENTRY;
   $sitemap_index .= sprintf($faire_entries, $sitemap_url, $sitemap_date);
   return $sitemap_index;
}
add_filter("wpseo_sitemap_index", "faire_entries_sitemap_index");

/**
 * Register faire_entries sitemap with Yoast
 */
function faire_entries_sitemap_register() {
   global $wpseo_sitemaps;
   if (isset($wpseo_sitemaps) && !empty($wpseo_sitemaps)) {
      $wpseo_sitemaps->register_sitemap("faire_entries", "faire_entries_sitemap_generate");
   }
}
add_action("init", "faire_entries_sitemap_register");

/**
 * Generate faire_entries sitemap XML body
 */
function faire_entries_sitemap_generate() {
   global $wpseo_sitemaps;
   global $form_types;
   global $search_criteria;

   $form_types = array('Exhibit', 'Presentation', 'Performance', 'Startup Sponsor', 'Sponsor', 'Workshop', 'Master');
   $search_criteria['status'] = 'active';
   $search_criteria['field_filters'][] = array('key' => '303', 'value' => 'Accepted');
   
   //generate a sitemap for each exhibit form
   $forms = GFAPI::get_forms(true, false);

   $urls = array();
   foreach ($forms as $form) {
      if (isset($form['form_type']) && in_array($form['form_type'], $form_types)) {
         $formId = $form['fields'][0]['formId'];
         //error_log('adding form id '.$formId);
         //see if there are any entries that match search criteria
         $entries = GFAPI::get_entries((int)$formId, $search_criteria, null, array('offset' => 0, 'page_size' => 999));
         if (!empty($entries)) {
            foreach ($entries as $entry) {
               //error_log('adding entry '.$entry['id']);
               $urls[] = $wpseo_sitemaps->renderer->sitemap_url(array(
                  "mod" => $entry['date_updated'],  # <lastmod></lastmod>
                  "loc" => site_url() . '/maker/entry/' . $entry['id'] . '/',  # <loc></loc>
                  "images" => array(
                     array(  # <image:image></image:image>
                        "src" => (isset($entry['22'])?$entry['22']:''),  # <image:loc></image:loc>
                        "title" => (isset($entry['151'])?$entry['151']:''),  # <image:title></image:title>
                        "alt" => (isset($entry['151'])?$entry['151']:''),  # <image:caption></image:caption>
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
      } //end check form type
   } //end foreach forms
}
