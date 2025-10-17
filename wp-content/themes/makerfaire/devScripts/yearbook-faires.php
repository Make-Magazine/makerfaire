<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$year    = (isset($_GET['year']) ? $_GET['year'] : '2025');

$sql =  'SELECT faire_name, faire_nicename, event_dt, event_type, event_start_dt, event_end_dt, faire_url, venue_address_city, venue_address_state, venue_address_country, venue_address_region, lat, lng, faire_image 
         FROM wp_mf_global_faire 
         WHERE faire_year="' . $year .'"';

$result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");


echo "<table border='1' cellspacing='0' cellpadding='6'>
        <thead>
            <tr>
                <th>Faire Name</th>
                <th>Faire Nice Name</th>
                <th>Event Date</th>
                <th>Event Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Faire URL</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>Region</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $title = trim( wp_strip_all_tags(htmlspecialchars($row['faire_name']) ));
        $nice_name = trim( wp_strip_all_tags(htmlspecialchars($row['faire_nicename']) ));
        $slug = str_replace(' ', '-', $nice_name) . "-" . $year;
        $image_url = htmlspecialchars($row['faire_image']);
        $start_dt = date_format(date_create($row['event_start_dt']),"Ymd");
        $end_dt = date_format(date_create($row['event_end_dt']),"Ymd");
        $event_type = htmlspecialchars($row['event_type']);
        $faire_url = htmlspecialchars($row['faire_url']);
        $city = htmlspecialchars($row['venue_address_city']);
        $state = htmlspecialchars($row['venue_address_state']);
        $country = htmlspecialchars($row['venue_address_country']);
        $region = htmlspecialchars($row['venue_address_region']);
        $lat = htmlspecialchars($row['lat']);
        $lng = htmlspecialchars($row['lng']);
        
        $post_type = 'yb_faires';
        // create a table
        echo "<tr>
                <td>" . $title . "</td>
                <td>" . $nice_name . "</td>
                <td>" . $start_dt . "</td>
                <td>" . $end_dt . "</td>
                <td>" . $start_dt . "</td>
                <td>" . $event_type . "</td>
                <td>" . $faire_url . "</td>
                <td>" . $city . "</td>
                <td>" . $state . "</td>
                <td>" . $country . "</td>
                <td>" . $region . "</td>
                <td>" . $lat . "</td>
                <td>" . $lng . "</td>
                <td>" . $image_url . "</td>
              </tr>";

        //  if a post with this title doesn't exist already, we must build one!
        if ( ! function_exists( 'post_exists' ) ) {
            // Load WordPress environment
            require_once( ABSPATH . 'wp-load.php' );
            require_once( ABSPATH . 'wp-admin/includes/post.php' );

            // Include necessary media-related admin files
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            add_filter('acf/validate_post', '__return_false');
        }

        // Query for posts with the same title
        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => 1,
            'title'          => $nice_name,
            'meta_query'     => [
                [
                    'key'     => 'start_date',
                    'value'   => [
                        "{$year}0101",
                        "{$year}1231"
                    ],
                    'compare' => 'BETWEEN',
                    'type'    => 'NUMERIC',
                ],
            ],
        ];

        $existing_posts = get_posts($args);

        if ( empty($existing_posts) && $event_type != "School" ) {
            $post_data = array (
                'comment_status'    => 'closed',
                'ping_status'       => 'closed',
                'post_author'       => 12416,
                'post_name'         => $slug,
                'post_title'        => $nice_name,
                'post_status'       => 'publish',
                'post_type'         => $post_type, 
            );

            $post_id = wp_insert_post( $post_data );

            $image = media_sideload_image( $image_url, $post_id, $title, 'id' );
            set_post_thumbnail( $post_id, $image );

            update_post_meta( $post_id, 'faire_city', $city, true);
            update_post_meta( $post_id, 'faire_state', $state, true);
            update_acf_term_field('country', 'countries', $country, $post_id);
            update_acf_term_field('region', 'regions', $region, $post_id);
            update_post_meta( $post_id, 'link_to_faire', $faire_url, true);
            update_field('producer_section_link_to_faire', $faire_url, $post_id);
            update_post_meta( $post_id, 'start_date', $start_dt, true);
            update_post_meta( $post_id, 'end_date', $end_dt, true);

            // Query for posts with the same title from the previous year so we can add last years graphics, if available
            $previous_year = $year - 1;

            $args = [
                'post_type'      => $post_type,
                'posts_per_page' => 1,
                'title'          => $nice_name,
                'meta_query'     => [
                    [
                        'key'     => 'start_date',
                        'value'   => [
                            "20230101",
                            "{$previous_year}1231"
                        ],
                        'compare' => 'BETWEEN',
                        'type'    => 'NUMERIC',
                    ],
                ],
            ];

            $existing_posts = get_posts($args);
            
            if ( !empty($existing_posts) ) {
                $pastyr_postid = $existing_posts[0]->ID;
                $logo = get_field('top_section_horizontal_faire_logo', $pastyr_postid);
                if ( !empty($logo) && isset($logo['ID']) ) {
                    $logo_id = $logo['ID'];
                    update_field('top_section_horizontal_faire_logo', $logo_id, $post_id );
                }
                $graphic = get_field('producer_section_faire_graphic', $pastyr_postid);
                if ( !empty($graphic) && isset($graphic['ID']) ) {
                    $graphic_id = $graphic['ID'];
                    update_field('producer_section_faire_graphic', $graphic_id, $post_id );
                }
                $badge = get_field('producer_section_circular_faire_logo', $pastyr_postid);
                if ( !empty($badge) && isset($badge['ID']) ) {
                    $badge_id = $badge['ID'];
                    update_field('producer_section_circular_faire_logo', $badge_id, $post_id );
                }
            }
        }
    }
} else {
    echo "<tr><td colspan='14' style='text-align:center;'>No results found for $year</td></tr>";
}

echo "</tbody></table>";


$sqlCount = "SELECT event_type, COUNT(*) AS event_count 
        FROM wp_mf_global_faire 
        WHERE faire_year = $year 
        GROUP BY event_type";

$result = $mysqli->query($sqlCount) or trigger_error($mysqli->error . "[$sqlCount]");

$counts = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $counts[$row['event_type']] = $row['event_count'];
    }
}

print_r($counts);

function update_acf_term_field($field_name, $taxonomy_slug, $value, $post_id) {
    if (empty($value)) return;

    $term_name = trim($value);
    $term = get_term_by('name', $term_name, $taxonomy_slug);

    if (!$term) {
        $term_result = wp_insert_term($term_name, $taxonomy_slug);
        if (is_wp_error($term_result)) {
            error_log("Failed to create term for {$taxonomy_slug}: {$term_name} - " . $term_result->get_error_message());
            return;
        }
        $term_id = $term_result['term_id'];
    } else {
        $term_id = $term->term_id;
    }

    // Update the ACF field — use array for single or multiple select
    update_field($field_name, $term_id, $post_id);

    // Also assign the term to the post in WP core (if "Save Terms" is off)
    wp_set_post_terms($post_id, [$term_id], $taxonomy_slug, false);
}

remove_filter('acf/validate_post', '__return_false');
?>
