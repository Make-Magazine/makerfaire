<?php
if(isset($_COOKIE['__smUser'])) {
  $sumomeStatus="status-logged-in";
} else{
  $sumomeStatus="status-logged-out";
}
?>

<div class="sumome-plugin-container">
  <!-- Logged in -->
  <div class="sumome-plugin-main logged-in <?php print $sumomeStatus?>">
    <div class="loading"><img src="<?php echo plugins_url('images/sumome-loading.gif', dirname(__FILE__)) ?>"></div>
  </div>

  <!-- Logged out -->
  <div class="sumome-plugin-main logged-out <?php print $sumomeStatus?>">
    <?php
    $noClose=true;
    include "wordpress-dashboard-welcome-page.php";
    ?>
  </div>

  <?php
    include_once "popup.php";
  ?>


</div>
