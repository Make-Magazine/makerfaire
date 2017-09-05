<?php
function create_calendar($formIDs){
  $schedules = getSchedule($formIDs);

  $ics = array();
  $str = '';

  foreach($schedules['schedule'] as $schedule){
    foreach($schedule as $day) {
      $dt = new DateTime($day['time_start']);
      $start = $dt->format('Ymd\THis');
      $dt = new DateTime($day['time_end']);
      $end = $dt->format('Ymd\THis');

      $ics[] = array( 'location'    => 'New York Hall of Science 47-01 111th St, Corona, NY 11368'.' - '.$day['nicename'],
                      'summary'     => $day['name'],
                      'dtstart'     => $start,
                      'dtend'       => $end,
                      'description' => $day['desc'],
          'latitude'=>$day['latitude'],
          'longitude'=>$day['longitude'],
                      'url'         => "http://makerfaire.com/maker/entry/".$day['id']);
    }
  }

  $event = new ICS(array( 'location'    => 'MakerFaire'));
  $output = $event->buildCal($ics);

  $event->save($output);
}