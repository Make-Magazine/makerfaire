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
      /*
      //Southwest Michigan
      echo 'Sending Southwest Michigan<br/>';
      $post_data = array(
        "title"       => "Southwest Michigan Mini Maker Faire",
        "description" => "What is Maker Faire? ... From engineers to artists to scientists to crafters, Maker Faire is an event for these \"makers\" to show hobbies, experiments, projects. We call it the Greatest Show (& Tell) on Earth - a family-friendly showcase of invention, creativity, and resourcefulness. Glimpse the future and get inspired!",
        "short_desc"  => "What is Maker Faire? ... From engineers to artists to scientists to crafters, Maker Faire is an event for these \"makers\" to show hobbies, experiments, projects. We call it the Greatest Show (& Tell) on Earth - a family-friendly showcase of invention, creativity, and resourcefulness. Glimpse the future and get inspired!",
        "image_url"   => "https://makerfaire.com/wp-content/uploads/2021/11/MG_1565-scaled.jpg",
        "image_credit"=> "Maker Faire",
        "partner_opp_url" => "https://swm.makerfaire.com/",
        "partner_name"    => "Maker Faire",
        "partner_website" => "https://makerfaire.com",
        "partner_logo_url"=> "https://makerfaire.com/wp-content/themes/makerfaire/img/Maker_Faire_Logo.svg",
        "entity_type"     => "opportunity",
        "start_datetimes" => array("2022-06-04T10:00:00-05:00"),
        "end_datetimes"   => array("2022-06-04T16:00:00-05:00"),
        "location_type"   => "at",
        "location_name"   => "",
        "address_street"  => "200 Broad St",
        "address_city"    => "St Joseph",
        "address_state"   => "MI",
        "address_country" => "USA",
        "address_zip"     => "49085",
        "opp_hashtags"    => array("#makerfaire")
      );
      $dataToSend = json_encode($post_data);
      $url = "https://beta.sciencenearme.org/api/v1/opportunity/";
      $authRes  = curlCall($url, $dataToSend, $token);
      if($authRes->accepted){
        echo 'The UID for this is '$authRes->uid;
      }else{
        var_dump($authRes);
      }

      echo '<br/><br/><br/>';*/
/*
      echo 'Sending Bloomsburg<br/>';
      //Bloomsburg
      $post_data = array(
        "title"           => "Bloomsburg Mini Maker Faire",
        "description"     => "Mini Maker Faires are one‐day, family‐friendly events that celebrate arts, crafts, engineering, food, music, science and technology projects, and the Do‐It‐Yourself (DIY) mindset. It’s for resourceful, creative people who like to tinker and love to make things. Craftsmen, artisans, performers, homesteaders, crafters, inventors, thinkers, and doers are welcome to apply to be Makers. The Faire will showcase cutting-edge technology such as 3D printing and robotics but also have more traditional trades such as woodworking, fine arts, and sewing crafts.<br/><br/>Makers from hobbyists to large corporations are encouraged to apply because the Faire is about creativity and innovation everywhere. The purpose is to expose attendees to skills they have never seen or tried before, whether they’re cutting edge, centuries-old, or anywhere in between.<br/><br/>The Children’s Museum is the host and producer of this event. The Museum is a leader in our community for youth education and promoting a passion for lifelong learning. In keeping with this tradition, the Bloomsburg Mini Maker Faire theme is \"Makers of All Ages.\" The Museum is actively recruiting Makers of every age. Makers under 18 will be featured as \"Young Makers\". ",
        "short_desc"      => "What is Maker Faire? ... From engineers to artists to scientists to crafters, Maker Faire is an event for these \"makers\" to show hobbies, experiments, projects. We call it the Greatest Show (& Tell) on Earth - a family-friendly showcase of invention, creativity, and resourcefulness. Glimpse the future and get inspired!",
        "image_url"       => "https://makerfaire.com/wp-content/uploads/2021/11/DIY-Drown-scaled.jpg",
        "image_credit"    => "Maker Faire",
        "partner_opp_url" => "https://bloomsburg.makerfaire.com/",
        "partner_name"    => "Maker Faire",
        "partner_website" => "https://makerfaire.com",
        "partner_logo_url"=> "https://makerfaire.com/wp-content/themes/makerfaire/img/Maker_Faire_Logo.svg",
        "entity_type"     => "opportunity",
        "start_datetimes" => array("2022-04-23T10:00:00-05:00"),
        "end_datetimes"   => array("2022-04-23T16:00:00-05:00"),
        "location_type"   => "at",
        "location_name"   => "Bloomsburg Children's Museum",
        "address_street"  => "2 W 7th St",
        "address_city"    => "Bloomsburg",
        "address_state"   => "PA",
        "address_country" => "USA",
        "address_zip"     => "17815",
        "opp_hashtags"    => array("#makerfaire")
      );
      $dataToSend = json_encode($post_data);
      $url = "https://beta.sciencenearme.org/api/v1/opportunity/";
      $authRes  = curlCall($url, $dataToSend, $token);

      if($authRes->accepted){
        echo 'The UID for this is '.$authRes->uid;
      }else{
        var_dump($authRes);
      }
      echo '<br/><br/><br/>';
*/
/*
      echo 'Sending Lake County<br/>';
      //Maker Faire Lake County
      $post_data = array(
        "title"           => "Maker Faire Lake County",
        "description"     => "The Maker Faire is a celebration of the innovative spirit within Lake County. The goal is to bring as many Lake County makers together as possible, in a virtual format, to celebrate the multifaceted fields of making and celebrate the unique makers in our region as well as highlight CLC’s home for makers, the Baxter Innovation Lab.<br/>
<br/>
Become a part of the Maker Movement! The College of Lake County will welcome makers – tech enthusiasts, inventors, crafters, educators, students, tinkerers and hobbyists of all ages – to exhibit their work and share their knowledge and skills with other makers and with the community.<br/>
<br/>
Follow the development of Maker Faire Lake County on Twitter @clcillinois, Instagram @clcengineering, as well as on its Facebook fan page.<br/>
 <br/>
Maker Faire originated in 2006 in the San Francisco Bay Area as a project of the editors of Make: magazine.  It has since grown into a significant worldwide network of both flagship and independently-produced events.",
        "short_desc"      => "What is Maker Faire? ... From engineers to artists to scientists to crafters, Maker Faire is an event for these \"makers\" to show hobbies, experiments, projects. We call it the Greatest Show (& Tell) on Earth - a family-friendly showcase of invention, creativity, and resourcefulness. Glimpse the future and get inspired!",
        "image_url"       => "https://makerfaire.com/wp-content/uploads/2021/11/BlondieWoodworking-scaled.jpg",
        "image_credit"    => "Maker Faire",
        "partner_opp_url" => "https://lakecounty.makerfaire.com/",
        "partner_name"    => "Maker Faire",
        "partner_website" => "https://makerfaire.com",
        "partner_logo_url"=> "https://makerfaire.com/wp-content/themes/makerfaire/img/Maker_Faire_Logo.svg",
        "entity_type"     => "opportunity",
        "start_datetimes" => array("2022-04-09T10:00:00-06:00"),
        "end_datetimes"   => array("2022-04-09T23:59:00-06:00"),
        "is_online"       => TRUE,
        "opp_hashtags"    => array("#makerfaire")
      );
      $dataToSend = json_encode($post_data);
      $url = "https://beta.sciencenearme.org/api/v1/opportunity/";
      $authRes  = curlCall($url, $dataToSend, $token);
      if(isset($authRes->accepted) && $authRes->accepted){
        echo 'The UID for this is '.$authRes->uid;
      }else{
        var_dump($authRes);
      }

      echo '<br/><br/><br/>';
      */
/*
      echo 'Sending Syracuse<br/>';
      //Maker Faire Syracuse
      $post_data = array(
        "title"           => "Maker Faire Syracuse",
        "description"     => "Featuring local \"makers\", Maker Faire Syracuse is a family-friendly celebration featuring all kinds of making and creating from traditional arts & crafts to high tech creations. Maker Faire Syracuse is hosting our inaugural faire on April 2, 2022.",
        "short_desc"      => "What is Maker Faire? ... From engineers to artists to scientists to crafters, Maker Faire is an event for these \"makers\" to show hobbies, experiments, projects. We call it the Greatest Show (& Tell) on Earth - a family-friendly showcase of invention, creativity, and resourcefulness. Glimpse the future and get inspired!",
        "image_url"       => "https://makerfaire.com/wp-content/uploads/2021/11/9866416976_bcfd03e1c0_o-scaled.jpg",
        "image_credit"    => "Maker Faire",
        "partner_opp_url" => "https://syracuse.makerfaire.com/",
        "partner_name"    => "Maker Faire",
        "partner_website" => "https://makerfaire.com",
        "partner_logo_url"=> "https://makerfaire.com/wp-content/themes/makerfaire/img/Maker_Faire_Logo.svg",
        "entity_type"     => "opportunity",
        "start_datetimes" => array("2022-04-02T10:00:00-05:00"),
        "end_datetimes"   => array("2022-04-02T23:59:00-05:00"),
        "location_type"   => "at",
        "location_name"   => "SRC Arena & Events Center",
        "address_street"  => "4585 W Seneca Turnpike",
        "address_city"    => "Syracuse",
        "address_state"   => "NY",
        "address_country" => "USA",
        "address_zip"     => "13215",
        "opp_hashtags"    => array("#makerfaire")
      );
      $dataToSend = json_encode($post_data);
      $url = "https://beta.sciencenearme.org/api/v1/opportunity/";
      $authRes  = curlCall($url, $dataToSend, $token);
      if(isset($authRes->accepted) && $authRes->accepted){
        echo 'The UID for this is '.$authRes->uid;
      }else{
        var_dump($authRes);
      }
      echo '<br/><br/><br/>';
      */
/*
      echo 'Sending Tulsa<br/>';
      //Tulsa
      $post_data = array(
          "title"             => "Maker Faire Tulsa",
          "short_desc"        => "What is Maker Faire? ... From engineers to artists to scientists to crafters, Maker Faire is an event for these \"makers\" to show hobbies, experiments, projects. We call it the Greatest Show (& Tell) on Earth - a family-friendly showcase of invention, creativity, and resourcefulness. Glimpse the future and get inspired!",
          "partner_opp_url"   => "http://tulsa.makerfaire.com/",
          "partner_name"      => "Maker Faire",
          "description"       => "Maker Faire Tulsa is an award-winning, family-friendly event celebrating technology, education, science, arts, crafts, engineering, food, sustainability and making of all kinds. In 2021, Fab Lab Tulsa celebrates our ninth maker faire anniversary.<br/>Whether as hobbyists or professionals, makers are creative, resourceful and curious, developing projects that demonstrate how they can interact with the world around them. Maker Faire Tulsa highlights Tulsa’s own Do-It-Yourself (DIY) mindset.<br/>Maker Faire Tulsa is an outreach program of Fab Lab Tulsa, a non-profit makerspace located in the Kendall-Whittier neighborhood of Tulsa. Fab Lab Tulsa provides education, community, workforce and business programming that teaches innovation, design-thinking, problem-solving and change-making, together with open and equitable access to 21st Century digital fabrication tools, equipment and technology.",
          "image_url"         => "https://tulsa.makerfaire.com/wp-content/uploads/sites/99/2018/07/Tulsa_MakerFaire_LogoBadge.png?w=723&ssl=1",
          "image_credit"      => "Maker Faire",
          "partner_website"   => "https://makerfaire.com",
          "partner_logo_url"  => "https://makerfaire.com/wp-content/themes/makerfaire/img/Maker_Faire_Logo.svg",
          "entity_type"       => "opportunity",
          "location_type"     => "at",
          "location_name"     => "Exchange Center at Expo Square",
          "address_street"    => "4145 E 21st Street",
          "address_city"      => "Tulsa",
          "address_state"     => "OK",
          "address_country"   => "USA",
          "address_zip"       => "74112",
          "start_datetimes"   => array("2022-08-27T10:00:00-06:00"),
          "end_datetimes"     => array("2022-08-27T17:00:00-06:00"),
          "opp_hashtags"      => array("#makerfaire")
        );*/
/*
        //Let's be Bakeneers
        echo 'Sending Let\'s be Bakeneers<br/>';

        $post_data = array(
          "title"           => "Let’s be Bakeneers (Baking + Engineering)",
          "description"     => "In this workshop we will create a tasty holiday present with a surprise that can’t wait to be unwrapped!  We will make a gingerbread present with a robot \"Makey\" cookie that will pop up from inside!<br/><br/>By the end of the workshop you will learn:<br/>
            <ul>
              <li>A \"Construction-grade\" gingerbread recipe</li>
              <li>How to use isomalt instead of frosting as your \"superglue\"</li>
              <li>How to construct a \"Makey\" the robot inspired cookie cutter</li>
              <li>Use electronics such as Servos, Motors, LEDs and the Adafruit Circuit Playground to bring our creation to life.</li>
              <li>Templates and instructions will be provided so you too can apply what you learn!</li>
            </ul>
            <br/>
            The workshop will be broken out into the following topics:<br/>
            <ul>
              <li>Introduction</li>
              <li>Project and Materials</li>
              <li>Setting up the Playground Circuit Express</li>
              <li>Design and Build project</li>
              <li>Tips and tricks</li>
              <li>Decorate and present</li>
              <li>Q&A</li>
            </ul>",
          "short_desc"      => "Virtual Workshop led by [Mitchell Malpartida](\"https://make.co/people/mitchell-malpartida/\").<br/><br/>Give your \"sweets\" some new moves with Bakeneering!

          Learn how to go from concept to confection!",
          "image_url"       => "https://i1.wp.com/make.co/wp-content/uploads/2021/11/IMG_4961-scaled.jpg?resize=300%2C300&ssl=1",
          "image_credit"    =>	"Mitchell Malpartida",
          "partner_opp_url" =>  "https://make.co/makercampus/let-s-be-bakeneers-baking-engineering/",
          "partner_name"    =>	"Maker Campus",
          "partner_website" =>  "https://makercampus.com",
          "partner_logo_url"=>	"https://make.co/wp-content/universal-assets/v1/images/MakerCampus_Logo_Boxless.png",
          "opp_topics"      =>	array("education", "food", "robotics", "art"),
          "entity_type"     =>	"opportunity",
          "tags"            => array("Baking", "Decorating", "Electronics", "Programming"),
          "ticket_required" => TRUE,
          "start_datetimes" => array("2021-12-19T15:00:00-08:00"),
          "end_datetimes"   => array("2021-12-19T17:00:00-08:00"),
          "has_end"         => TRUE,
          "cost"            => "cost",
          "is_online"       => TRUE,
          "contact_name"    =>  "Maker Campus",
          "contact_email"   =>  "makercampus@make.co"
        );*/
/*
        //Beginning 3D Printing
        echo 'Sending - Beginning 3D Printing<br/>';
        $post_data = array(
            "title"           => "Beginning 3D Printing",
            "description"     => "Have you (or your school) just purchased a 3D printer? Are you wondering how to get started, and where you can find good educational materials? We will assume that you have minimal exposure to 3D printing and talk you through:
      - What a 3D printer can do
      - How to create a 3D-printable model with free programs, or where to find models online
      - The steps and software involved for common 3D printers
      - Different materials you can print with and why you might want to use each one

      We’ll also introduce you to 3D printable math and science models we have developed.",
            "short_desc"        => "Virtual Workshop led by [Joan Horvath and Rich Cameron](https://make.co/people/joan-horvath/).
      Do you have a new 3D printer? Let us help you get started!",
            "image_url"         => "https://i2.wp.com/make.co/wp-content/uploads/2021/11/3d-printer.jpeg",
            "image_credit"      => "Joan Horvath and Rich Cameron",
            "partner_opp_url"   => "https://make.co/makercampus/beginning-3d-printing/",
            "partner_name"      => "Maker Campus",
            "partner_website"   => "https://makercampus.com",
            "partner_logo_url"  => "https://make.co/wp-content/universal-assets/v1/images/MakerCampus_Logo_Boxless.png",
            "opp_topics"        => array("technology", "computers_and_technology"),
            "entity_type"	      => "opportunity",
            "tags"              => array("3D Printing", "3D Printing and Imaging", "CAD", "Digital Fabrication"),
            "ticket_required"   => TRUE,
            "start_datetimes" => array("2022-01-13T18:00:00-08:00"),
            "end_datetimes"   => array("2022-01-13T20:00:00-08:00"),
            "has_end"           => TRUE,
            "cost"              => "cost",
            "is_online"         => TRUE,
            "contact_name"      => "Maker Campus",
            "contact_email"     => "makercampus@make.co"
        );
*/
        //Japanese Chisels: How Samurai Tech Beats Modern Tools
        echo 'Sending - Japanese Chisels: How Samurai Tech Beats Modern Tools<br/>';
        $post_data = array(
            "title"             =>  "Japanese Chisels: How Samurai Tech Beats Modern Tools",
            "description"       =>	"Japanese chisels have a well-deserved reputation for being incredibly sharp, while holding their edge for a very long time. In this workshop, you’ll learn how to set up and use Japanese chisels in your workshop, and how and why centuries-old blacksmithing techniques can produce a tool that outshines modern-day offerings. \n\n
Other topics will include the wide variety of Japanese chisels, how to select and buy Japanese chisels (and other Japanese woodworking tools), demonstrations of techniques to get the most out of your chisels, and time for Q&A.",
            "short_desc"        =>	"Virtual Workshop led by Wilbur Pan. \n
How to use Japanese chisels in your workshop, and why they may be the best option for a chisel today",
            "image_url"         =>	"https://i2.wp.com/make.co/wp-content/uploads/2021/10/DSCF4713-scaled.jpg?resize=300%2C300&ssl=1",
            "image_credit"      =>	"Wilbur Pan",
            "partner_opp_url"   =>  "https://make.co/makercampus/japanese-chisels-how-samurai-tech-beats-modern-tools/",
            "partner_name"      =>  "Maker Campus",
            "partner_website"   =>  "https://makercampus.com",
            "partner_logo_url"  =>  "https://make.co/wp-content/universal-assets/v1/images/MakerCampus_Logo_Boxless.png",
            "entity_type"	      =>  "opportunity",
            "opp_topics"        => array("archaeology_and_cultural", "art", "engineering"),
            "tags"              =>  array("Craft & Design", "Metalworking", "Woodworking", "Chisel"),
            "ticket_required"   =>  TRUE,
            "start_datetimes" => array("2022-01-15T11:00:00-08:00"),
            "end_datetimes"   => array("2022-01-15T13:30:00-08:00"),
            "has_end"           => TRUE,
            "cost"              => "cost",
            "is_online"         => TRUE,
            "contact_name"      => "Maker Campus",
            "contact_email"     => "makercampus@make.co");

        $dataToSend = json_encode($post_data);
        $url = "https://beta.sciencenearme.org/api/v1/opportunity/";
        $authRes  = curlCall($url, $dataToSend, $token);
        if(isset($authRes->accepted) && $authRes->accepted){
          echo 'The UID for this is '.$authRes->uid;
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
