<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_connect.php';

$sql = 'select display_meta from wp_rg_form_meta where form_id!=1 and form_id!=24';
if(isset($_GET['formID'])) $sql.= ' and form_id='.$_GET['formID'];
//echo $sql;
$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
?>
<!doctype html>

<html lang="en">
<head>

<style>
  #headerRow {
    font-size: 1.2em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;
    background-color: #A7C942;
    color: #fff;
  }

  .detailRow {
    font-size: 1.2em;
    border: 1px solid #98bf21;
  }
  .detailRow div {
    border-right: 1px solid #98bf21;
    padding: 3px 7px;
  }
  .detailRow div:last-child {
    border-right: none;
  }
  .row-eq-height {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
  }
</style>
<link rel='stylesheet' id='make-bootstrap-css'  href='http://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css' type='text/css' media='all' />
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>

<body>
  <div style="text-align: center">
    <h2> MakerFaire Form Layout </h2>
    <i>To Display a specific form, add the following to the end of your URL:
      ?formID=77(where 77 is the # of the form you want to display)<br/>
      ie: makerfaire.com/wp-content/themes/makerfaire/devScripts/formFields.php?formID=77
     </i>
  </div>
  <div class="clear"></div>
  <div class="container" style="width:95%">
    <?php
    // Loop through the posts
    while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
      $json = json_decode($row['display_meta']);
      echo '<h2>Form '.$json->id.' - '.$json->title.'</h2>';
      ?>
      <div id="headerRow" class="row">
        <div class="col-sm-1">
          ID
        </div>
        <div class="col-sm-4">
          Label
        </div>
        <div class="col-sm-2">
          Type
        </div>
        <div class="col-sm-1">
          Admin Only?
        </div>
        <div class="col-sm-1">
          Required?
        </div>
        <div class="col-sm-3">
          Options
        </div>
      </div>
      <?php

      $jsonArray = (array) $json->fields;
      foreach($jsonArray as &$array){
        $array->id = (float) $array->id;
        $array = (array) $array;
      }

      usort($jsonArray, "cmp");
      //   var_dump($jsonArray);
      foreach($jsonArray as $field){
        if($field['type'] != 'html' && $field['type'] != 'section' && $field['type'] != 'page'){
          //var_dump($field);
          $label = (isset($field['adminLabel']) && trim($field['adminLabel']) != '' ? $field['adminLabel'] : $field['label']);
          if($label=='' && $field['type']=='checkbox') $label = $field['choices'][0]->text;

          ?>
          <div class="row detailRow row-eq-height">
            <div class="col-sm-1">
              <?php echo $field['id'];?>
            </div>
            <div class="col-sm-4">
              <?php echo $label;?>
            </div>
            <div class="col-sm-2">
              <?php echo $field['type']; ?>
            </div>
            <div class="col-sm-1">
              <?php echo (isset($field['adminOnly']) && $field['adminOnly']?'Yes':'');?>
            </div>
            <div class="col-sm-1">
              <?php echo ($field['isRequired']?'Yes':'');?>
            </div>
            <div class="col-sm-3">
              <?php
              if($field['type']=='product') {

                echo '<table width="100%">';
                echo '<tr><th>Label</th><th>Price</th></tr>';
                foreach($field['choices'] as $choice){
                  echo '<tr><td>'.($choice->value!=$choice->text?$choice->value.'-'.$choice->text:$choice->text).'</td><td>'.$choice->price.'</td></tr>';
                }
                echo '</table>';
              }elseif($field['type']=='checkbox'||$field['type']=='radio'||$field['type']=='select' ||$field['type']=='address'){
                echo '<ul>';
                if(isset($field['inputs']) && !empty($field['inputs'])){
                  foreach($field['inputs'] as $choice){
                    echo '<li>'.$choice->id.' : '.$choice->label.'</li>';
                  }
                }else{
                  foreach($field['choices'] as $choice){
                    echo '<li>'.($choice->value!=$choice->text?$choice->value.'-'.$choice->text:$choice->text).'</li>';
                  }
                }
                echo '</ul>';
              }
              ?>
            </div>
          </div>
          <?php
        }
      }
      echo '<br/><br/>';
    }
    ?>
  </div>
</body>
</html>
<?php
function cmp($a, $b) {
    return $a["id"] - $b["id"];
}

