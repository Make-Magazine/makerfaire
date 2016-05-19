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
      title: '<h2>Welcome to MakerFaire! First Question...</h2>',
      message: '<h1>Are you <?= $entryID ?>, <?= $project_title ?>?</h1>',
      type: BootstrapDialog.TYPE_INFO, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
      closable: false, // <-- Default value is false
      draggable: false, // <-- Default value is false
      btnCancelLabel: '<h1>No</h1>', // <-- Default value is 'Cancel',
      btnOKLabel: '<h1>Yes</h1>', // <-- Default value is 'OK',
      btnOKClass: 'btn-info', // <-- If you didn't specify it, dialog type will be used,
      callback: function (result) {
        // result will be true if button was click, while it will be false if users close the dialog directly.
        if (result) {
          BootstrapDialog.confirm({
            title: '<h2>Next Question...</h2>',
            message: '<h1>Are you standing in your location at Maker Faire Bay Area 2016?</h1>',
            type: BootstrapDialog.TYPE_INFO, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
            closable: false, // <-- Default value is false
            draggable: false, // <-- Default value is false
            btnCancelLabel: '<h1>No</h1>', // <-- Default value is 'Cancel',
            btnOKLabel: '<h1>Yes</h1>', // <-- Default value is 'OK',
            btnOKClass: 'btn-info', // <-- If you didn't specify it, dialog type will be used,
            callback: function (result) {
              // result will be true if button was click, while it will be false if users close the dialog directly.
              if (result) {
                getLocation();
                jQuery("#entrygeolocate").show();
              } else {
                jQuery("#error").show();
              }
            }
          });
        } else {
          jQuery("#optout").show();

        }
      }
    });
  });
</script>
<div class="clear"></div>


<div class="container">

  <div class="row">

    <div id="entrygeolocate" style="display:none" class="content col-md-8">
      <div class="jumbotron">
        <h1>Welcome, <?= $entryID ?>!</h1>
        <h1>Your pin will show on the MakerFaire App in 15-30 minutes.</h1>
        <h2>Thank you for pinning your location.</h2>

          <input id="entryID"   name="entryID"   type="hidden" value ="<?php echo $entryID; ?>" />
            <input id="latitude" type="hidden" class="form-control" placeholder="latitude" aria-describedby="latitude-addon1">
              <input  id="longitude" name="longitude"  type="hidden" class="form-control" placeholder="latitude" aria-describedby="longitude-addon1">
            <div class="pull-left flagship-icon-link">
            <a class="flagship-icon-link" href="/app/">
              <img src="/wp-content/uploads/2016/01/icon-mobile.png" width="40px" scale="0">
              If you don't have it...download the MakerFaire App
            </a>
            </div>  
      </div>
      
    </div><!--content-->
    <div id="error" style="display:none" class="content col-md-8">
      <div class="jumbotron">
        <h1>Sorry, <?= $entryID ?>.  There was a problem and support has been notified.</h1>
      </div>
      </div>
       <div id="optout" style="display:none" class="content col-md-8">
      <div class="jumbotron">
        <h1>Rescan your code later, after your situation changes.</h1>
      </div>
      </div>
  </div><!--row-->
</div><!--container-->

<?php wp_footer(); ?>
