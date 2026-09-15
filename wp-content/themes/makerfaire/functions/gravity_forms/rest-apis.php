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

/* 
 * Create a rest api endpoint to set a gravity forms entry location
 */
add_action('rest_api_init', function() {
    register_rest_route('mf/v1', '/set-entry-location', [
        'methods'  => 'POST',
        'callback' => 'mf_set_entry_location',
        'permission_callback' => function($request) {
            $key = $request->get_header('X-MF-Key');
            return $key === GF_IMAGE_KEY;
        }
    ]);
});

function mf_set_entry_location(WP_REST_Request $request) {
    global $wpdb;

    $entry_id = intval($request->get_param('entry_id'));
    $zone     = sanitize_text_field($request->get_param('zone'));
    $booth    = sanitize_text_field($request->get_param('booth'));

    if (!$entry_id || !$zone) {
        return new WP_Error('bad_request', 'Missing entry_id or zone', ['status' => 400]);
    }

    // Validate GF entry exists
    $entry = GFAPI::get_entry($entry_id);
    if (is_wp_error($entry)) {
        return new WP_Error('entry_not_found', 'Gravity Forms entry not found', ['status' => 404]);
    }

    $faire_table      = $wpdb->prefix . 'mf_faire';
    $faire_area_table = $wpdb->prefix . 'mf_faire_area';
    $subarea_table    = $wpdb->prefix . 'mf_faire_subarea';
    $location_table   = $wpdb->prefix . 'mf_location';

    // Get the faire ID for this form
    $faire_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ID FROM {$faire_table} WHERE find_in_set(%d, form_ids) > 0 LIMIT 1",
            305
        )
    );

    if (!$faire_id) {
        return new WP_Error('not_found', 'No faire found for this form', ['status' => 404]);
    }

    // Get valid area IDs for this faire from wp_mf_faire_area
    $area_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT ID FROM {$faire_area_table} WHERE faire_id = %d",
            $faire_id
        )
    );

    if (empty($area_ids)) {
        return new WP_Error('not_found', 'No areas found for this faire', ['status' => 404]);
    }

    // Look up subarea and its parent area_id
    $placeholders = implode(',', array_fill(0, count($area_ids), '%d'));
    $subarea_row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT ID, area_id, exposure  FROM {$subarea_table} WHERE subarea = %s AND area_id IN ($placeholders) LIMIT 1",
            array_merge([$zone], $area_ids)
        )
    );

    if (!$subarea_row) {
        return new WP_Error('not_found', 'Subarea not found for zone: ' . $zone, ['status' => 404]);
    }

    $subarea_id = $subarea_row->ID;
    $area_id    = $subarea_row->area_id;

    // Use a transaction to make delete + insert atomic
    $wpdb->query('START TRANSACTION');

    try {
        $deleted = $wpdb->delete(
            $location_table,
            ['entry_id' => $entry_id],
            ['%d']
        );

        if ($deleted === false) {
            throw new Exception('Delete failed: ' . $wpdb->last_error);
        }

        $inserted = $wpdb->insert(
            $location_table,
            [
                'entry_id'            => $entry_id,
                'subarea_id'          => intval($subarea_id),
                'location'            => $booth,
                'location_element_id' => 3,
            ],
            ['%d', '%d', '%s', '%d']
        );

        if ($inserted === false) {
            throw new Exception($wpdb->last_error);
        }

        $wpdb->query('COMMIT');
        $location_id = $wpdb->insert_id;

    } catch (Throwable $e) {
        $wpdb->query('ROLLBACK');
        return new WP_Error('db_error', 'Transaction failed: ' . $e->getMessage(), ['status' => 500]);
    }

    return [
        'success'      => true,
        'entry_id'     => $entry_id,
        'subarea_id'   => intval($subarea_id),
        'area_id'      => intval($area_id),
        'exposure'     => $subarea_row->exposure,
        'location'     => $booth,
        'location_id'  => $location_id,
    ];
}