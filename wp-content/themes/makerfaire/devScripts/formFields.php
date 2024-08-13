<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_connect.php';

$sql = 'select display_meta from wp_gf_form_meta where form_id!=1 and form_id!=24';
if(isset($_GET['formID'])) $sql.= ' and form_id='.$_GET['formID'];
$sort     = (isset( $_GET['sort'])    ? $_GET['sort'] : '');
$showAll  = (isset( $_GET['showAll']) ? TRUE : FALSE);
$showCalc = (isset( $_GET['showCalc']) ? TRUE : FALSE);

$condLog  = (isset( $_GET['condLog']) ? TRUE : FALSE);
$condLog  = TRUE; //for now let's always display the conditional logic

$mysqli->query("SET NAMES 'utf8'");
$result   = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");

$publicFields = array(109,11,110,105,151,22,16,27,32,151,160,234,217,158,258,224,155,259,223,156,260,222,157,261,220,159,262,221,154,263,219,878,906,320,321,287,209,821,168,111,112,828);
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
  <link rel='stylesheet' id='make-bootstrap-css'  href='https://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css' type='text/css' media='all' />
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
      <div>
        <div style="font-size: 12px;line-height: 12px;">          
          <ul>
            <li>to show a specific form -  add ?formID=xxx</li>
            <li>to sort by ID - add <a href="<?php echo $_SERVER['REQUEST_URI'];?>&sort=id">&sort=id</a></li>
            <li>to show all fields (including HTML, Section and Page) - add <a href="<?php echo $_SERVER['REQUEST_URI'];?>&showAll=true">&showAll=true</a></li>
            <li>to show calculations - add <a href="<?php echo $_SERVER['REQUEST_URI'];?>&showCalc=true">&showCalc=true</a></li>
            <!--<li>to show conditional logic for the field - add &condLog=true</li>-->
          </ul>
          
        </div>
      </div>

    <div style="clear:both"></div>
      <table style="margin: 10px 0;">
        <thead>
          <tr id="headerRow">
            <td style="width:  3%">ID</td>
            <td style="width: 30%">Label</td>
            <td style="width:  3%">Type</td>
            <td style="width: 30%">Options</td>
            <td style="width: 10%">CFM Field ID to Update</td>
            <td style="width:  3%">Admin Only / Hidden</td>
            <td style="width:  1%">Req</td>
            <!--<td style="width:  1%">Public</td>-->
            <?php if($condLog){ ?>
              <td style="width:  20%">Conditional Logic</td>
            <?php } ?>
            <?php if($showCalc){ ?>
              <td style="width:  20%">Calculation</td>
            <?php } ?>
          </tr>
        </thead>
      <?php

      $jsonArray = (array) $json->fields;
      foreach($jsonArray as &$array){
        $array->id = (float) $array->id;
        $array = (array) $array;
      }

      if($sort=='id'){
        usort($jsonArray, "cmp");
      }
            
      var_dump($jsonArray);
      foreach($jsonArray as $field){
        $condDisp = '';
        if(isset($field['conditionalLogic']) && $field['conditionalLogic']!='' && $condLog){
          $condLogic = $field['conditionalLogic'];          
          if(isset($condLogic->enabled)&&$condLogic->enabled){
            $condDisp = '<b>'.$condLogic->actionType .'</b> if <b>'.$condLogic->logicType .'</b>:<br/>';
            foreach($condLogic->rules as $rule){
              $condDisp .= 'Field '. $rule->fieldId .' '. $rule->operator.' '.$rule->value.'<br/>';
            }
          }          
        }
        if(
          ($field['type'] != 'html' && $field['type'] != 'section' && $field['type'] != 'page') ||
           ($showAll)
        ){
          //var_dump($field);
          $label   = (isset($field['adminLabel']) && trim($field['adminLabel']) != '' ? $field['adminLabel'] : $field['label']);
          $paramName=(isset($field['origFieldID'])?$field['origFieldID']:'');          
  
          if($label=='' && $field['type']=='checkbox') $label = $field['choices'][0]->text;
          $visibility = (isset($field['visibility']) && $field['visibility']!='visible'?$field['visibility']:'');
          ?>
          <tr class="detailRow">
            <td class="tcenter"><?php echo $field['id'];?></td>
            <td><?php echo $label;?></td>
            <td><?php echo $field['type'];?></td>
            <td><?php
              if($field['type']=='product') {
                echo 'Base Price = '.$field['basePrice'];
                
              }elseif($field['type']=='checkbox'||$field['type']=='radio'||$field['type']=='select' ||$field['type']=='address'||$field['type']=='name'){
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
            <td><?php echo $paramName;?></td>
            <td class="tcenter"><?php echo $visibility;?></td>
            
            <td class="tcenter"><?php echo ($field['isRequired']?'X':'');?></td>
            <!--<td class="tcenter"><?php echo (in_array($field['id'],$publicFields)?'X':'');?></td>-->
            <?php if($condLog){ ?>
              <td><?php echo $condDisp; ?></td>
            <?php } ?>  
            <?php if($showCalc){ ?>
              <td><?php echo (isset($field['calculationFormula'])?$field['calculationFormula']:'');?></td>
            <?php } ?>
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

