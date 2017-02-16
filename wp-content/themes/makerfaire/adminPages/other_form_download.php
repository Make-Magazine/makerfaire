<?php
/* this provides a javascript button that allows the users to print out
 * all maker pdf's
 */
global $wpdb;
$selfaire  = '';
$type   = '';

$sql = "SELECT faire,faire_name FROM wp_mf_faire order by start_dt DESC";
$results = $wpdb->get_results($sql);

?>

<!-- New Collapsible menus and more user friendly interface -->
<div id="faire-signs">
  <h2 style="text-align:center">FSP/GSP Download</h2>

  <div class="row">
    <div class="col-md-4">
      <h4>Download FSP</h4>
      <div class="form-group">
        <label for="faire">Faire</label>
        <select class="form-control" id="faire">
          <?php foreach($results as $row){ ?>
          <option value="<?php echo $row->faire;?>"><?php echo $row->faire;?></option>
          <?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label for="form_id">FSP Form ID</label>
        <input class="form-control" type="text" name="form_id" id="form_id" />
      </div>
      <button id="fsp_download">Download</button>
    </div>
    <div class="col-md-4">
      <h4>Download GSP</h4>
      <div class="form-group">
        <label for="GSPfaire">Faire</label>
        <select class="form-control" id="GSPfaire">
          <?php foreach($results as $row){ ?>
          <option value="<?php echo $row->faire;?>"><?php echo $row->faire;?></option>
          <?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label for="GSPform_id">GSP Form ID</label>
        <input class="form-control" type="text" name="GSPform_id" id="GSPform_id" />
      </div>
      <button id="gsp_download">Download</button>
    </div>
  </div>
</div>

<script>
  jQuery( "#fsp_download" ).click(function() {
    var faire   = jQuery('#faire').val();
    var form_id = jQuery('#form_id').val();
    var url = '<?php echo get_template_directory_uri();?>/fpdi/FSP.php?faire='+faire+'&form='+form_id;
    
    var link = document.createElement('a');
    link.href = url;
    document.body.appendChild(link);
    link.click();
  });

  jQuery( "#gsp_download" ).click(function() {
    var faire   = jQuery('#GSPfaire').val();
    var form_id = jQuery('#GSPform_id').val();
    var url = '<?php echo get_template_directory_uri();?>/fpdi/GSP.php?faire='+faire+'&form='+form_id;

    var link = document.createElement('a');
    link.href = url;
    document.body.appendChild(link);
    link.click();
  });

</script>