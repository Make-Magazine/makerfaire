<?php
/* 
 * Create a rest api endpoint to recieve an image url and add as an image to a gravity form image field
 */

add_action('rest_api_init', function() {
    register_rest_route('mf/v1', '/set-entry-images', [
        'methods'  => 'POST',
        'callback' => 'mf_set_entry_images',
        'permission_callback' => function($request) {
            $key = $request->get_header('X-MF-Key');
            return $key === GF_IMAGE_KEY;
        }
    ]);
});

function mf_set_entry_images(WP_REST_Request $request) {
    $entry_id = intval($request->get_param('entry_id'));
    $fields   = $request->get_param('fields');

    if (!$entry_id || !$fields) {
        return new WP_Error('bad_request', 'Missing entry_id or fields', ['status' => 400]);
    }

    $results = [];
    foreach ($fields as $item) {
        $field_id = sanitize_text_field($item['field_id']);
        $urls     = $item['urls']; // now always an array

        if (!is_array($urls)) {
            $urls = [$urls];
        }

        // Sanitize each URL
        $clean_urls = array_map('esc_url_raw', array_filter($urls));

        if (empty($clean_urls)) continue;

        $value = json_encode(array_values($clean_urls)); // always JSON array

        GFAPI::update_entry_field($entry_id, $field_id, $value);
        $results[] = ['field' => $field_id, 'set' => $clean_urls];
    }

    return ['success' => true, 'updated' => $results];
}