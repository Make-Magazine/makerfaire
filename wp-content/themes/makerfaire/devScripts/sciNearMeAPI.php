<?php // ?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  </head>
  <body>
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
      echo 'Authenticated<br/>';
      $token = $authRes->token;
      echo 'returned token is '.$token.'<br/><br/>';

      //lets send some data
      echo 'Sending - Create Your Own Roller Coaster or Marble Run<br/>';
      $post_data = array(
          "title"             =>  "Sewing for Cosplay 101 Lesson 4 – Shaping Fabric",
          "start_datetimes"   => array("2022-06-11T01:00:00-07:00"),
          "end_datetimes"     => array("2022-06-11T12:00:00-07:00"),
          "short_desc"        =>	"Join <a href=\"https://make.co/people/cori-leyden-sussler/\">Cori Leyden-Sussler</a> where we'll explore how to shape fabric through darts, gathers, pleats, and tucks.
          <a href=\"https://make.co/makercampus/sewing-for-cosplay-101-lesson-4-shaping-fabric/\">Register Today</a>",
          "description"       =>	"<h4>What You'll Do:</h4>
          In this lesson, we’ll explore how to sew our garments to better fit the human form. The curves, the flares, darts and gathers. This class will lay the ground work to take a basic bag shape into something fitted.

          This is lesson 4 of an 8 part series.
<h4>Skill Level Needed</h4>
This is part 4 of an eight part series. If you have not taken the first three courses, you will need to know basic sewing machine use, how to hand sew, and how to sew a seam.
<h4>Skills you will learn:</h4>
How to sew and work with darts, gathers, pleats, interface (both fusible and stitched) tucks.
<h4>What You'll need</h4>
This is a lecture/presentation style course. You may work along with us, or listen and then practice. A sewing machine (Just a basic machine will do) Sewing Thread Scissors Scrap Fabric Sewing Pins Machine Sewing Needles A popsicle stick or turning device.
<h4>What is Included:</h4>
<ul>
<li>A free membership to <a href=\"https://make.co\">Make: Community</a></li>
<li>Access to discussion forums and community groups</li>
<li>Post event access to session recordings</li>
</ul>

<h4>About your Facilitator:</h4>
I'm a Maryland-based cosplayer and costumer who has a passion for teaching. I'm an accomplished maker who creates my own costumes from scratch. I began cosplaying in 2003, and currently compete and win at the master craftsmanship level. I hold a B.F.A. in Technical Theater and Costume Design as well as a M.A. in Puppetry (with a high focus on Fabrication). I've taught for over a decade for both higher education and for K-12 students. I work frequently as a panelist, teacher, and cosplay judge at several conventions on the east coast.",
                "image_url"         =>	"https://i0.wp.com/make.co/wp-content/uploads/2022/03/FHIhhXGXIAY_bAu.jpg",
                "image_credit"      =>	"Cori Leyden-Sussler",
                "partner_opp_url"   =>  "https://make.co/makercampus/sewing-for-cosplay-101-lesson-4-shaping-fabric/",
                "partner_name"      =>  "Maker Campus",
                "partner_website"   =>  "https://makercampus.com",
                "partner_logo_url"  =>  "https://make.co/wp-content/universal-assets/v1/images/MakerCampus_Logo_Boxless.png",
                "entity_type"	      =>  "opportunity",
                "opp_topics"        =>  array("art", "engineering", "design", "education"),
                "tags"              =>  array("Craft & Design", "Makeup & Costumes", "Wearables"),
                "ticket_required"   =>  TRUE,
                "has_end"           =>  TRUE,
                "cost"              =>  "cost",
                "is_online"         =>  TRUE,
                "contact_name"      =>  "Maker Campus",
                "contact_email"     =>  "makercampus@make.co");
        $dataToSend = json_encode($post_data);
        $url = "https://beta.sciencenearme.org/api/v1/opportunity/";
        $authRes  = curlCall($url, $dataToSend, $token);
        if(isset($authRes->accepted) && $authRes->accepted){
          echo 'The UID for this is '.$authRes->uid.'<br/>';
          echo 'The slug for this is '.$authRes->slug;
        }else{
          var_dump($authRes);
        }

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
