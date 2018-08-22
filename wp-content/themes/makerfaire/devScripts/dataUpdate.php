<?php
include '../wp-load.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
   </head>
   <body>
      To Update the Hidden/Not Hidden indicator for Entry passes
      &type=entry_passes
      &go_live=yes (if, = no this will just display what needs updated
      
      select eb_entry_access_code.*, wp_rg_lead.form_id, eb_eventToTicket.ticket_type, eb_ticket_type.*
FROM eb_entry_access_code
LEFT OUTER JOIN wp_rg_lead ON wp_rg_lead.id = eb_entry_access_code.entry_id
LEFT OUTER JOIN eb_eventToTicket ON eb_eventToTicket.ticketID = eb_entry_access_code.EBticket_id
LEFT OUTER JOIN eb_ticket_type on form_type = 1 and eb_ticket_type.ticket_type = eb_eventToTicket.ticket_type
WHERE form_id = 208 AND eb_ticket_type.event_id in(11,12)
ORDER BY eb_entry_access_code.id DESC
      <?php
      //retrieve all BA2016 entries
      $search_criteria = array();
      $search_criteria['status'] = 'active';
      $offset = $_GET['offset'];
      $sorting = array('key' => 'id', 'direction' => 'DESC', 'is_numeric' => true);
      $paging = array('offset' => $offset, 'page_size' => 200);
      $total_count = 0;
      $entries = GFAPI::get_entries(array(46, 45, 49, 51, 47, 48), $search_criteria, $sorting, $paging, $total_count);
      echo 'Total # of Records = ' . $total_count;
      ?>

      <table border="1">
         <tr>
            <th>Entry ID</th>
            <th>Form</th>
            <th>Field 83</th>
            <th>Field 84</th>
            <th>Field 293</th>
            <th>Field 60</th>
            <th>Field 73</th>
            <th>Field 75</th>
            <th>Field 64</th>
            <th>Status</th>
            <th>Assign</th>
         </tr>
         <?php
         foreach ($entries as $entry) {
            echo '<tr>';
            echo '<td>' . $entry['id'] . '</td>';
            echo '<td>' . $entry['form_id'] . '</td>';
            echo '<td>' . $entry['83'] . '</td>';
            echo '<td>' . $entry['84'] . '</td>';
            echo '<td>' . $entry['293'] . '</td>';
            echo '<td>' . $entry['60'] . '</td>';
            echo '<td>' . $entry['73'] . '</td>';
            echo '<td>' . $entry['75'] . '</td>';
            echo '<td>' . $entry['64'] . '</td>';

            $status = gform_get_meta($entry['id'], 'res_status');
            $assignTo = gform_get_meta($entry['id'], 'res_assign');
            echo '<td>' . $status . '</td><td>' . $assignTo . '</td>';
            echo '</tr>';
            if ($status == '' || $assignTo == '') {
               $assignTo = 'na'; //not assigned to anyone
               $status = 'ready'; //ready
               //field ID 83
               if ($entry['83'] == 'Yes' ||
                       $entry['84'] == 'Yes' ||
                       $entry['293'] == 'Yes' ||
                       $entry['60'] == "Other") {
                  $status = 'review';
                  $assignTo = 'jay'; //Jay
               } elseif ($entry['73'] == 'Yes' &&
                       $entry['75'] == 'Other. Power request specified in the Special Power Requirements box') {
                  $status = 'review';
                  $assignTo = 'kerry'; //Kerry
               } elseif ($entry['64'] != '') {
                  $status = 'review';
                  $assignTo = 'kerry'; //Kerry
               }
               $resUpdate[] = array($entry['id'], $status, $assignTo);
            }
         }
         ?>
      </table>
      <?php
      foreach ($resUpdate as $update) {
         $entryID = $update[0];
         $status = $update[1];
         $assignTo = $update[2];

         echo 'gform_update_meta( ' . $entryID . ', ' . $status . ',   \'res_status\' )<br/>';
         echo 'gform_update_meta( ' . $entryID . ', ' . $assignTo . ', \'res_assign\' )<br/><br/>';
         // update custom meta field
         gform_update_meta($entryID, 'res_status', $status);
         gform_update_meta($entryID, 'res_assign', $assignTo);
      }
      ?>
   </body>
</html>