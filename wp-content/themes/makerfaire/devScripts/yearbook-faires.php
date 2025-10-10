<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_connect.php';
$year    = (isset($_GET['year']) ? $_GET['year'] : '2025');

$sql =  'SELECT faire_name, event_dt, event_type, event_start_dt, event_end_dt, faire_url, venue_address_street, venue_address_city, venue_address_state, venue_address_country, venue_address_region, lat, lng, faire_image 
         FROM wp_mf_global_faire 
         WHERE faire_year="' . $year .'"';

$result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");

echo "<table border='1' cellspacing='0' cellpadding='6'>
        <thead>
            <tr>
                <th>Faire Name</th>
                <th>Event Date</th>
                <th>Event Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Faire URL</th>
                <th>Street</th>
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
        echo "<tr>
                <td>" . htmlspecialchars($row['faire_name']) . "</td>
                <td>" . htmlspecialchars($row['event_dt']) . "</td>
                <td>" . htmlspecialchars($row['event_type']) . "</td>
                <td>" . htmlspecialchars($row['event_start_dt']) . "</td>
                <td>" . htmlspecialchars($row['event_end_dt']) . "</td>
                <td>" . htmlspecialchars($row['faire_url']) . "</td>
                <td>" . htmlspecialchars($row['venue_address_street']) . "</td>
                <td>" . htmlspecialchars($row['venue_address_city']) . "</td>
                <td>" . htmlspecialchars($row['venue_address_state']) . "</td>
                <td>" . htmlspecialchars($row['venue_address_country']) . "</td>
                <td>" . htmlspecialchars($row['venue_address_region']) . "</td>
                <td>" . htmlspecialchars($row['lat']) . "</td>
                <td>" . htmlspecialchars($row['lng']) . "</td>
                <td>";
        
        // Display image if exists
        if (!empty($row['faire_image'])) {
            echo htmlspecialchars($row['faire_image']);
        }
        
        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan='14' style='text-align:center;'>No results found for $year</td></tr>";
}

echo "</tbody></table>";


$sqlCount = "SELECT event_type, COUNT(*) AS event_count 
        FROM wp_mf_global_faire 
        WHERE faire_year = '2025' 
        GROUP BY event_type";

$result = $mysqli->query($sqlCount) or trigger_error($mysqli->error . "[$sqlCount]");

$counts = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $counts[$row['event_type']] = $row['event_count'];
    }
}

print_r($counts);
?>
