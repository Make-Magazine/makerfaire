<?php

function create_calendar($formIDs) {
   $schedules = getSchedule($formIDs);

   $ics = array();
   $str = '';

   foreach ($schedules['schedule'] as $schedule) {      
      if (isset($schedule['time_start'])){            
         $dt = new DateTime($schedule['time_start']);
         $start = $dt->format('Ymd\THis');
         $dt = new DateTime($schedule['time_end']);
         $end = $dt->format('Ymd\THis');

         $ics[] = array('location' => 'San Mateo Event Center 1346 Saratoga Dr, San Mateo, CA 94403' . ' - ' . $schedule['nicename'],
             'summary' => $schedule['name'],
             'dtstart' => $start,
             'dtend' => $end,
             'description' => $schedule['desc'],
             'latitude' => $schedule['latitude'],
             'longitude' => $schedule['longitude'],
             'url' => "http://makerfaire.com/maker/entry/" . $schedule['id']);
      }      
   }
   
   $event = new ICS(array('location' => 'MakerFaire'));
   $output = $event->buildCal($ics);

   $event->save($output);
}
