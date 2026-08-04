<?php

/**
 *  Creates a new custom yoast seo sitemap based on maker entry
 */
// only use this during development to disable sitemap caching :
// add_filter( 'wpseo_enable_xml_sitemap_transient_caching', '__return_false');
function faire_sitemap_form_types() {
    return array( 'Exhibit', 'Presentation', 'Performance',
                  'Startup Sponsor', 'Sponsor', 'Workshop', 'Master' );
}

function faire_sitemap_criteria() {
    return array(
        'status'        => 'active',
        'field_filters' => array(
            array( 'key' => '303', 'value' => 'Accepted' ),
        ),
    );
}
//define valid form types
$form_types = faire_sitemap_form_types();
//entry search criteria
$search_criteria = faire_sitemap_criteria();

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
    global $wpseo_sitemaps, $wp;

    $current_slug = add_query_arg( array(), $wp->request );
    $form_id      = (int) str_replace( array( 'form-', '-entries-sitemap.xml' ), '', $current_slug ?? '' );
    if ( ! $form_id ) {
        return;
    }

    $form            = GFAPI::get_form( $form_id );
    $search_criteria = faire_sitemap_criteria();

    $entries = array();
    $offset  = 0;
    do {
        $batch = GFAPI::get_entries( $form_id, $search_criteria, null,
            array( 'offset' => $offset, 'page_size' => 200 ) );
        if ( is_wp_error( $batch ) || empty( $batch ) ) {
            break;
        }
        $entries = array_merge( $entries, $batch );
        $offset += 200;
    } while ( count( $batch ) === 200 );

    if ( empty( $entries ) ) {
        return;
    }

    $urls = array();
    foreach ( $entries as $entry ) {
        if ( ! mf_entry_is_public( $entry, $form ) ) {
            continue;
        }

        $images = array();

        $project_photo = rgar( $entry, '22' );
        $photo         = json_decode( $project_photo, true );
        if ( is_array( $photo ) ) {
            $project_photo = isset( $photo[0] ) ? $photo[0] : '';
        }

        $gallery = json_decode( rgar( $entry, '878' ), true );
        $gallery = is_array( $gallery ) ? $gallery : array();

        if ( '' === $project_photo && ! empty( $gallery[0] ) ) {
            $project_photo = $gallery[0];
        }
        if ( '' !== $project_photo ) {
            $images[] = array( 'src' => $project_photo );
        }
        foreach ( $gallery as $image ) {
            if ( ! empty( $image ) && $image !== $project_photo ) {
                $images[] = array( 'src' => $image );
            }
        }

        $url = array(
            'loc' => home_url( mf_entry_path( rgar( $entry, (string) MF_TITLE_FIELD ), $entry['id'] ) ),
            'mod' => $entry['date_updated'],
        );
        if ( $images ) {
            $url['images'] = $images;
        }
        $urls[] = $wpseo_sitemaps->renderer->sitemap_url( $url );
    }

    if ( empty( $urls ) ) {
        return;
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
}     
   
