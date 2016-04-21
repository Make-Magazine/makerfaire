<?php
$eventID    = 21038172741; //ba16
$oAuthToken = 'DM4CN4OUFHNVZRBYGHGI';
// Load the Eventbrite API class.
require_once('eventbrite.class.inc');

// Instantiate a new object with your OAuth token.
$eventbrite = new eventbrite($oAuthToken);
if(isset($_GET['accessCode'])&& $_GET['accessCode']!=''){
  $args = array(
    'id'   => $eventID,
    'data' => 'access_codes',
    'create' => array(
      'access_code.code' => $_GET['accessCode'],
      'access_code.ticket_ids'=>'44091559'
    )
  );
  $access_codes = $eventbrite->events($args);
  var_dump($access_codes);
}
  // Get an event by its ID.
  $args = array('id' => $eventID);
  $events = $eventbrite->events($args);
  //var_dump($events);
  echo 'Event - '.$events->name->text.'<br/><br/>';

  //get information for one ticket
  $args = array('id' => $eventID, 'data' => 'ticket_classes', 'ticket_id' =>44091563);
  $ticket_types = $eventbrite->events($args);
  
  echo 'Single ticket information:<br/>';
  echo '<table>';
      echo '<tr>';
      echo '<td>' . $ticket_types->id   . '</td>';
      echo '<td>' . $ticket_types->name . '</td>';
      echo '<td>' . $ticket_types->description.'</td></tr>';
  echo '</table>';

  // Get list of ticket types of an event.
  $args = array('id' => $eventID, 'data' => 'ticket_classes');
  $ticket_types = $eventbrite->events($args);
  echo 'Ticket Types:<br/>';
  echo '<table>';
  foreach($ticket_types->ticket_classes as $type){
    if($type->hidden==true){
      echo '<tr>';
      echo '<td>' . $type->id   . '</td>';
      echo '<td>' . $type->name . '</td>';
      echo '<td>' . $type->description.'</td></tr>';
    }
  }
  echo '</table>';
echo '<br/>';

//get list of access codes
/* Access Codes:
  Fields -
    code (str):                 The access code itself
    ticket_ids (array):         List of ticket IDs to apply discount to, or null if it applies to all tickets.
    quantity_available (int):   The maximum number of uses (optional)
    start_date (timestamp):     The time when the discount code is valid from (optional)
    end_date (timestamp):       The time when the discount code is valid to (optional)
 *    */
// Get orders for events associated with a specific user.
$args = array('id' => 21038172741, 'data' => 'access_codes');
$access_codes = $eventbrite->events($args);
echo 'Available Access Codes:<br/>';
echo '<table>';
echo '<tr><th>Code</th><th>Qty</th><th>ticket ID\'s</th></tr>';
foreach($access_codes->access_codes as $ak){
  echo '<tr>';
  echo '<td>' . $ak->code.'</td>';
  echo '<td>'.$ak->quantity_available.'</td>';
  echo '<td>';
  if($ak->ticket_ids == NULL ){
    echo 'All Tickets';
  }else{
    foreach($ak->ticket_ids as $ticket_id){
      echo $ticket_id . '<br/>';
    }
  }
  echo '</td></tr>';
}
echo '</table>';
echo '<br/><br/>';

?>
Create a new Access Code:
<form>
  Access Code:<br>
  <input type="text" name="accessCode"><br>
  <input type="submit" />
</form>

<html>
<head>
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script>
    $(document).ready(function() {

        var token = 'VIB7IGVIP33EUXJXAMA6';
        var $events = $("#events");

        $.get('https://www.eventbriteapi.com/v3/events/21038172741/ticket_classes?token='+token, function(res) {
            if(res.events.length) {
                var s = "<ul class='eventList'>";
                for(var i=0;i<res.events.length;i++) {
                    var event = res.events[i];
                    console.dir(event);
                    s += "<li><a href='" + event.url + "'>" + event.name.text + "</a> - " + event.description.text + "</li>";
                }
                s += "</ul>";
                $events.html(s);
            } else {
                $events.html("<p>Sorry, there are no upcoming events.</p>");
            }
        });


    });
    </script>
</head>
<body>

<h2>Upcoming Events!</h2>
<div id="events"></div>
</body>
</html>