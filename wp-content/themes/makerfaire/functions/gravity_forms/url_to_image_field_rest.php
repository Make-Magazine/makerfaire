<?php
/* 
 * Create a rest api endpoint to recieve an image url and add as an image to a gravity form image field
 */

add_action( 'rest_api_init', 'entry_images_api');
function entry_images_api() {
    register_rest_route('mf/v1', '/set-entry-images', [
        'methods'  => 'POST',
        'callback' => 'mf_set_entry_images',
        'permission_callback' => function($request) {
            $key = $request->get_header('X-MF-Key');
            return $key === GF_IMAGE_KEY;
        }
    ]);
}

function mf_set_entry_images(WP_REST_Request $request) {
    $entry_id  = intval($request->get_param('entry_id'));
    $fields    = $request->get_param('fields'); // array of { field_id, url }

    if (!$entry_id || !$fields) {
        return new WP_Error('bad_request', 'Missing entry_id or fields', ['status' => 400]);
    }

    $results = [];
    foreach ($fields as $item) {
        $field_id = sanitize_text_field($item['field_id']);
        $url      = esc_url_raw($item['url']);

        // For single-image fields (22, 217, 111)
        GFAPI::update_entry_field($entry_id, $field_id, $url);
        $results[] = ['field' => $field_id, 'set' => $url];
    }

    return ['success' => true, 'updated' => $results];
}