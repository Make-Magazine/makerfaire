<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_connect.php';

$sql = 'select display_meta from wp_rg_form_meta where form_id!=1 and form_id!=24';
if(isset($_GET['formID'])) $sql.= ' and form_id='.$_GET['formID'];

$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");

$msFieldArr = array(151,22,217,320,321,16,32,287,
      '161', '160', '234', '185', '201', '217', '209', '200', '310', '369', '443',  //maker 1
      '158', '258', '162', '192', '208', '224', '216', '199', '311', '370', '444',  //maker 2
      '155', '259', '167', '190', '207', '223', '215', '193', '312', '371', '445',  //maker 3
      '156', '260', '166', '191', '206', '222', '214', '198', '313', '372', '446',  //maker 4
      '157', '261', '165', '189', '205', '220', '213', '195', '314', '373', '447',  //maker 5
      '159', '262', '164', '188', '204', '221', '211', '197', '315', '374', '448',  //maker 6
      '154', '263', '163', '187', '203', '219', '212', '196', '316', '375', '449'); //maker 7
$publicFields = array(109,11,110,105,151,22,16,27,32,151,160,234,217,158,258,224,155,259,223,156,260,222,157,261,220,159,262,221,154,263,219);
?>
<!doctype html>

<html lang="en">
<head>

<style>
  h1, .h1, h2, .h2, h3, .h3 {
    margin-top: 10px !important;
    margin-bottom: 10px !important;
  }
  ul, ol {
    margin-top: 0 !important;
    margin-bottom: 0px !important;
    padding-top: 0px !important;
    padding-bottom: 0px !important;
  }
  table {font-size: 14px;}
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
  #headerRow td, .detailRow td {
    border-right: 1px solid #98bf21;
    padding: 3px 7px;
    vertical-align: baseline;
  }
  .detailRow td:last-child {
    border-right: none;
  }
  .row-eq-height {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
  }
  .tcenter {
    text-align: center;
  }
</style>
  <link rel='stylesheet' id='make-bootstrap-css'  href='http://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css' type='text/css' media='all' />
  <link rel='stylesheet' id='font-awesome-css'  href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=2.819999999999997' type='text/css' media='all' />
  <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>

<body>
  <div class="container" style="width:100%; line-height: 1.3em">
    <?php
    // Loop through the posts
    while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
      $json = json_decode($row['display_meta']);
      $form = GFAPI::get_form($json->id);
      $form_type = (isset($form['form_type'])  ? $form['form_type'] : '');
      echo '<h3 style="float:left">Form '.$json->id.' - '.$json->title.'</h3>';
      echo '<span style="float:right; margin-top: 15px;"><i>Form Type = '.$form_type.'</i></span>';?>
      <div style="clear:both"></div>
      <div style="text-align: center">
        <div style="font-size: 12px;line-height: 12px;">
          <i>add ?formID=xxx to the end of the URL to specify a specific form - ie: makerfaire.com/wp-content/themes/makerfaire/devScripts/formFields.php?formID=77</i>
        </div>
      </div>

    <div style="clear:both"></div>
      <table style="margin: 10px 0;">
        <thead>
          <tr id="headerRow">
            <td style="width:  3%">ID</td>
            <td style="width: 40%">Label</td>
            <td style="width:  3%">Type</td>
            <td style="width: 40%">Options</td>
            <td style="width:  3%">Admin Only</td>
            <td style="width:  3%">Req</td>
            <td style="width:  3%">Public</td>
            <td style="width:  3%">MS</td>
          </tr>
        </thead>
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
          <tr class="detailRow">
            <td class="tcenter"><?php echo $field['id'];?></td>
            <td><?php echo $label;?></td>
            <td><?php echo $field['type'];?></td>
            <td><?php
              if($field['type']=='product') {
                echo '<table width="100%">';
                echo '<tr><th>Label</th><th>Price</th></tr>';
                foreach($field['choices'] as $choice){
                  echo '<tr><td>'.($choice->value!=$choice->text?$choice->value.'-'.$choice->text:$choice->text).'</td><td>'.$choice->price.'</td></tr>';
                }
                echo '</table>';
              }elseif($field['type']=='checkbox'||$field['type']=='radio'||$field['type']=='select' ||$field['type']=='address'){
                echo '<ul style="padding-left: 20px;">';
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
            </td>
            <td class="tcenter"><?php echo (isset($field['visibility']) && $field['visibility']=='administrative'?'<i class="fa fa-check" aria-hidden="true"></i>':'');?></td>
            <td class="tcenter"><?php echo ($field['isRequired']?'<i class="fa fa-check" aria-hidden="true"></i>':'');?></td>
            <td class="tcenter"><?php echo (in_array($field['id'],$publicFields)?'<i class="fa fa-check" aria-hidden="true"></i>':'');?></td>
            <td class="tcenter"><?php echo (in_array($field['id'],$msFieldArr)?'<i class="fa fa-check" aria-hidden="true"></i>':'');?></td>

          </tr>
          <?php
        }
      }
    }
    ?>
  </table>
</body>
</html>
<?php
function cmp($a, $b) {
    return $a["id"] - $b["id"];
}

