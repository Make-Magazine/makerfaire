<?php
$itemArray['My Apps']['data-name']="sumome-control-apps";
$itemArray['My Apps']['class']="sumo-apps";
$itemArray['My Apps']['columns']=2;
$itemArray['My Apps']['data-type']="sumome-app";

$itemArray['Store']['data-name']="sumome-control-store";
$itemArray['Store']['class']="sumo-store";
$itemArray['Store']['columns']=2;
$itemArray['Store']['data-type']="sumome-app";

$itemArray['Notifications']['data-name']="sumome-control-notifications";
$itemArray['Notifications']['class']="sumo-notifications";
$itemArray['Notifications']['data-type']="sumome-app";
$itemArray['Notifications']['columns']=1;

$itemArray['Statistics']['data-name']="sumome-control-statistics";
$itemArray['Statistics']['class']="sumome-popup-no-dim sumo-statistics";
$itemArray['Statistics']['columns']=1;

$itemArray['I Need Help']['data-name']="sumome-control-help";
$itemArray['I Need Help']['class']="sumome-control-help sumome-popup-no-dim";
$itemArray['I Need Help']['columns']=1;

$itemArray['About']['data-name']="sumome-control-about";
$itemArray['About']['class']="sumome-tile-about sumome-popup-no-dim";
$itemArray['About']['columns']=1;

$itemArray['Sumo Settings']['data-name']="sumome-control-settings";
$itemArray['Sumo Settings']['class']="sumo-settings";
$itemArray['Sumo Settings']['data-type']="sumome-app";
$itemArray['Sumo Settings']['columns']=1;
?>
<div class="sumome-plugin-main-wrapper">
  <div class="sumome-logged-in-container">
    <!-- Header -->

      <div class="header-banner"></div>

      <div class="items">
        <?php
        foreach ($itemArray as $title => $parameters) {
          print '<div ';
          foreach ($parameters as $parameterName=>$parameterValue) {
              if ($parameterName=="class") $parameterValue.=' item-tile';
              print $parameterName.'="'.$parameterValue.'" ';
          }
          print ' data-title="'.$title.'"';
          print '>';
          if ($parameters['columns']>1) print'<div class="item-tile-background"></div>';

          print '<div class="item-tile-title">'.$title.'</div></div>';
        }
        ?>
      </div>

      <div class="tabbed-content-container">
        <div class="back-logged-in">Back</div>
        <div class="content"></div>
      </div>
  </div>


  <div class="sumome-plugin-main main-bottom">
      <!-- Review -->
      <div class="row row3">
        <div class="large-12 columns">
          <div class="list-bullet">
            <h4 class="list-number-title">Leave a Review!</h4>
          </div>
          <div class="sumome-instructions">We will love you forever if you leave an <a href="https://wordpress.org/support/view/plugin-reviews/sumome" target="_blank">honest review here</a> of the Sumo plugin.</div>
        </div>
      </div>

      <!-- Help -->
      <div class="row">
        <div class="large-12 columns footer">
          <h4 class="list-number-title">Need Help?</h4>
          <div class="sumome-help">
            <span>Take a look at our <a target="_blank" href="https://help.sumome.com/">help page</a> to see our frequently answered</span>
            <span>questions or <a target="_blank" href="https://help.sumome.com/hc/en-us/requests/new">send us a message</a> and we will get back to you asap.</span>
          </div>
        </div>
      </div>



  </div>
</div>

<div class="sumome-logged-in-container-overlay"></div>
<?php
  include_once "popup.php";
?>


