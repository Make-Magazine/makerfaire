<?php /** Template Name: Onsite Checkin */ ?>
<?php
wp_head();
//Determine the token
$token = (isset($wp_query->query_vars['token']) ? $wp_query->query_vars['token'] : '');
$decodedtoken = base64_decode($token);
$entryID = $decodedtoken;
$entry = GFAPI::get_entry($entryID);
$project_title = (isset($entry['151']) ? (string) $entry['151'] : '');
?>

<script type="text/javascript">
  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(processPosition);
      
    } else {
      x.innerHTML = "Geolocation is not supported by this browser.";
    }
  }

  function processPosition(position) {
    jQuery('#latitude').val(position.coords.latitude);
    jQuery('#longitude').val(position.coords.longitude);
    
    
    jQuery.ajax({
        type: "POST",
        url: "/processonsitecheckin/",
        data: "entryID=" + jQuery('#entryID').val() + "&latitude=" + position.coords.latitude + "&longitude=" + position.coords.longitude,
        success : function(text){
            /*if (text == "success"){
                formSuccess();
            }*/
            console.log(text);
        }
    });

    jQuery('#geotext').val();
    var output = '';
    output = 'Returned Data: ' + "\n " +
            'Latitude: ' + position.coords.latitude + "\n " +
            'Longitude: ' + position.coords.longitude + "\n " +
            'Accuracy: ' + position.coords.accuracy + '\n ' +
            'Altitude: ' + position.coords.altitude + '\n ' +
            'Altitude Accuracy: ' + position.coords.altitudeAccuracy + '\n ' +
            'Heading as degrees clockwise from North: ' + position.coords.heading + '\n ' +
            'Speed (meters): ' + position.coords.speed + '\n ' +
            'Timestamp: ' + position.timestamp;
    jQuery('#geotext').val(output);
    //var newtext = document.myform.geotext.value;
    //document.myform.geotext.value += output;
    
    
 
    
  }

  jQuery(window).load(function () {
    BootstrapDialog.confirm({
      title: 'Welcome to MakerFaire! First Question...',
      message: 'Are you <?= $entryID ?>, <?= $project_title ?>?',
      type: BootstrapDialog.TYPE_INFO, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
      closable: false, // <-- Default value is false
      draggable: false, // <-- Default value is false
      btnCancelLabel: 'No', // <-- Default value is 'Cancel',
      btnOKLabel: 'Yes', // <-- Default value is 'OK',
      btnOKClass: 'btn-info', // <-- If you didn't specify it, dialog type will be used,
      callback: function (result) {
        // result will be true if button was click, while it will be false if users close the dialog directly.
        if (result) {
          BootstrapDialog.confirm({
            title: 'Next Question...',
            message: 'Are you standing in your location at Maker Faire Bay Area 2016?',
            type: BootstrapDialog.TYPE_INFO, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
            closable: false, // <-- Default value is false
            draggable: false, // <-- Default value is false
            btnCancelLabel: 'No', // <-- Default value is 'Cancel',
            btnOKLabel: 'Yes', // <-- Default value is 'OK',
            btnOKClass: 'btn-info', // <-- If you didn't specify it, dialog type will be used,
            callback: function (result) {
              // result will be true if button was click, while it will be false if users close the dialog directly.
              if (result) {
                getLocation();
              } else {
                jQuery("#entrygeolocate").hide();
              }
            }
          });
        } else {
          jQuery("#entrygeolocate").hide();

        }
      }
    });
  });
</script>
<div class="clear"></div>


<div class="container">

  <div class="row">

    <div id="entrygeolocate" class="content col-md-8">
      <div class="jumbotron">
        <h1>Welcome, <?= $entryID ?>!</h1>

          <input id="entryID"   name="entryID"   type="hidden" value ="<?php echo $entryID; ?>" />
            <div class="input-group">
              <span class="input-group-addon" id="latitude-addon1">latitude</span>
              <input id="latitude" type="text" class="form-control" placeholder="latitude" aria-describedby="latitude-addon1">
            </div>
            <br />
            <div class="input-group">
              <span class="input-group-addon" id="longitude-addon1">longitude</span>
              <input  id="longitude" name="longitude"  type="text" class="form-control" placeholder="latitude" aria-describedby="longitude-addon1">
            </div>
            <br />
            <div class="pull-left flagship-icon-link">
            <a class="flagship-icon-link" href="/app/">
              <img src="/wp-content/uploads/2016/01/icon-mobile.png" width="40px" scale="0">
              Download the Maker Faire App
            </a>
            </div>  
            <input type="submit" class="btn btn-primary btn-lg" role="button" Submit Location />
      
      </div>
    </div><!--content-->
  </div><!--row-->
</div><!--container-->

<?php wp_footer(); ?>
