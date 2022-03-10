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
          "title"             =>  "Create Your Own Roller Coaster or Marble Run",
          "short_desc"        =>	"Join <a href=\"https://make.co/people/godwyn-morris/\">Godwyn Morris</a> for a hands-on workshop on how to create your own roller coaster or marble run.

In this topsy turvy action packed workshop, learn how to build your own amazing roller coaster or marble run from household supplies.     Great for educators,  families and anyone who want to create a twisting, turning game that lets you test the properties of physics while having fun. Whether you are a teacher looking to expand your students’ hands on science activities or a family looking for a exciting project, you will come away with techniques, ideas tips and tricks.  Together we will explore the science, a variety of materials, how to work with them and then get you started building at least one  roller coaster.  A version of this project was featured in an article written by Godwyn Morris and Paula Frisch, published November 2020 in the New York Times.

Price includes the downloadable Roller Coaster instruction packet from Engineering with Paper, a $12 value.

<a href=\"https://make.co/makercampus/create-your-own-roller-coaster-or-marble-run/\">Register Today</a>",
          "description"       =>	"<h4>What You'll Do:</h4>In this topsy turvy action packed workshop, learn how to build your own amazing roller coaster or marble run from household supplies.  Great for educators, families and anyone who want to create a twisting, turning game that lets you test the properties of physics while having fun. Whether you are a teacher looking to expand your students’ hands on science activities or a family looking for a exciting project, you will come away with techniques, ideas tips and tricks. Together we will explore the science, a variety of materials, how to work with them and then get you started building at least one roller coaster. A version of this project was featured in an article written by Godwyn Morris and Paula Frisch, published November 2020 in the New York Times.

Price includes the downloadable Roller Coaster instruction packet from Engineering with Paper, a $12 value.

<h4>What You'll Need:</h4>
You will definitely need:
<ul>
<li><strong>Paper</strong> manila folder or cover stock paper is best but ANY paper will work, including copy paper, construction paper or old catalogs and magazines.&nbsp; You will need at least 10 sheets of paper.</li>
<li><strong>Tape&nbsp;</strong>masking tape is best but you could use washi tape, scotch tape or painters tape. Don't use packing tape or duct tape those will not work.</li>
<li><strong>Scissors</strong>adult or kid scissors</li>
</ul>

OPTIONAL:
<ul>
<li>empty toilet paper and paper towel tubes</li>
<li>cereal box cardboard</li>
<li>markers</li>
</ul>

<h4>What is Included:</h4>
<ul>
<li>A free membership to Make: Community</li>
<li>Access to discussion forums and community groups</li>
<li>Post event access to session recordings</li>
</ul>

<h4>About your Facilitator:</h4>
Godwyn Morris and Paula Frisch, co-creators of Engineering with Paper, and Director and Assistant Director of Dazzling Discoveries,  have had several projects published in the New York Times.  Their article,  \"Put Physics to the Test with a D.I.Y. Roller Coaster\", is the inspiration for this workshop.  They are both long time educators and experts at making amazing projects with simple supplies.",
                "image_url"         =>	"https://i2.wp.com/make.co/wp-content/uploads/2021/12/IMG_20190828_142117-scaled.jpg",
                "image_credit"      =>	"Godwyn Morris",
                "partner_opp_url"   =>  "https://make.co/makercampus/create-your-own-roller-coaster-or-marble-run/",
                "partner_name"      =>  "Maker Campus",
                "partner_website"   =>  "https://makercampus.com",
                "partner_logo_url"  =>  "https://make.co/wp-content/universal-assets/v1/images/MakerCampus_Logo_Boxless.png",
                "entity_type"	      =>  "opportunity",
                "opp_topics"        => array("art", "engineering", "design", "education"),
                "tags"              => array("Craft & Design", "Education", "Fun & Games", "Kids", "Paper Crafts"),
                "ticket_required"   =>  TRUE,
                "start_datetimes"   => array("2022-04-09T16:00:00-08:00"),
                "end_datetimes"     => array("2022-04-09T17:30:00-08:00"),
                "has_end"           => TRUE,
                "cost"              => "cost",
                "is_online"         => TRUE,
                "contact_name"      => "Maker Campus",
                "contact_email"     => "makercampus@make.co");
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
