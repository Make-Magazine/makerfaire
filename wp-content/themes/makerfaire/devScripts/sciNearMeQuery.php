<?php // ?>
<!DOCTYPE html>
<html>
  <head>
    <style>
      table {font-size: 14px;margin:5px;}
      #headerRow {
        font-size: 1.2em;
        border: 1px solid #98bf21;
        padding: 5px;
        background-color: #A7C942;
        color: #fff;
        text-align: center;
      }
      .detailRow {
        font-size: 1.2em;
        border: 1px solid #98bf21;
      }
      #headerRow th,
      .detailRow td {
        border-right: 1px solid #98bf21;
        padding: 3px 7px;
        vertical-align: baseline;
        max-width: 200px;
        overflow-wrap: break-word;
      }
      .detailRow td:last-child {
        border-right: none;
      }
      .tcenter {
        text-align: center;
      }
    </style>
    <link rel='stylesheet' id='make-bootstrap-css'  href='https://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css' type='text/css' media='all' />
    <link rel='stylesheet' id='font-awesome-css'  href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=2.819999999999997' type='text/css' media='all' />
  <meta charset="UTF-8">
  </head>
  <body>
    <div class="tcenter">
      <h1>Science Near Me Opportunities for Make:</h1>
      <p>If you would like to see expired opportunities, please append ?expired=true to the URL</p>
    </div>
    <?php
    include '../../../../wp-load.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    //First do the authentication
    $url = "https://beta.sciencenearme.org/api/v1/partner/authorize";

    $post_data = '{
      "uid": "b75f265a-4107-5e6b-bd5d-1b03d51c51fa",
      "secret": "KDiwBOFaZlLkduvsmVFdAdNjOY4dFRcz"
    }';

    $authRes = curlCall($url, $post_data);

    //did we get a token?
    if(isset($authRes->token)){
      //echo 'Authenticated<br/>';
      $token = $authRes->token;
      //echo 'returned token is '.$token.'<br/><br/>';

      //query all available opportunities
      $url = "https://beta.sciencenearme.org/api/v1/opportunity/?partner=b75f265a-4107-5e6b-bd5d-1b03d51c51fa";
      $allOpps  = curlCall($url, '', $token);
      $opportunities = $allOpps->matches;

      $UidTable = array();
      $date = new DateTime();
      $now = $date->format('c');

      if(isset($_GET['expired']) && $_GET['expired']=='true'){
        $now = '';
      }
      foreach($opportunities as $opportunity){
        $uid =$opportunity->uid;
        //pull information for that uid
        $url = "https://beta.sciencenearme.org/api/v1/opportunity/".$uid;
        $oppData  = curlCall($url, '', $token);
        $end_datetime = $oppData->end_datetimes[0];

        $dataTable = array();
        if(isset($oppData->withdrawn) && $oppData->withdrawn == "1" ){
          continue;
        }

        //only return events in the future
        if($end_datetime >= $now){
          //set string fields
          $dataTable = array('uid'=>$oppData->uid,
            "title"             => '<a target="_none" href="https://sciencenearme.org/'.$oppData->slug.'">'.$oppData->title.'</a>',
            "start_datetimes"   => implode(",",$oppData->start_datetimes),
            "end_datetimes"     => implode(",",$oppData->end_datetimes),
            "image_url"         => $oppData->image_url,
            "image_credit"      => $oppData->image_credit,
            "partner_opp_url"   => $oppData->partner_opp_url,
            "partner_name"      => $oppData->partner_name,
            "partner_website"   => $oppData->partner_website,
            "partner_logo_url"  => $oppData->partner_logo_url,
            "entity_type"       => $oppData->entity_type,
            "opp_topics"        => implode(",",$oppData->opp_topics),
            "tags"              => implode(",",$oppData->tags),
            "ticket_required"   => $oppData->ticket_required,
            "has_end"           => $oppData->has_end,
            "cost"              => $oppData->cost,
            "is_online"         => $oppData->is_online,
            "contact_name"      => $oppData->contact_name,
            "contact_email"     => $oppData->contact_email,
            "withdrawn"         => $oppData->withdrawn,
          );
        }

        $UidTable[$uid] = $dataTable;
      }

      $fieldIncludeArr = array('uid','title',"start_datetimes", "end_datetimes",
      'image_url', 'image_credit',
      "partner_opp_url", "partner_name", "partner_website", "partner_logo_url", "entity_type",
      "opp_topics", "tags", "ticket_required", "has_end", "cost",
      "is_online", "contact_name", "contact_email", "withdrawn");

      //output table
      echo '<table>';
      //output headers
      echo '<tr id="headerRow">';
      foreach($fieldIncludeArr as $field){
        echo '<th>'.$field.'</th>';
      }
      echo '</tr>';

      //output data
      foreach($UidTable as $dkey=>$uidInfo){
        echo '<tr class="detailRow">';
        foreach($uidInfo as $uidData){
            echo '<td>'.$uidData.'</td>';
        }
        echo '</tr>';
      }
      echo '</table>';
    }
    ?>
  </body>
</html>

<?php
function curlCall($service_url,$curl_post_data,$token=''){
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $service_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //CURLOPT_VERBOSE => TRUE,
    //CURLOPT_STDERR => $verbose = fopen('php://temp', 'rw+'),
  ));

  if($curl_post_data !== ''){
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    if($token==''){
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json"));
    }else{
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer ".$token ,"content-type: application/json"));
    }

  }else{
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer ".$token ,"content-type: application/json"));
  }

  $curl_response = curl_exec($curl);
  //echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #:" . $err;
  }

  return json_decode($curl_response);
}
?>
