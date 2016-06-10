<?php //Used to copy fields from master form to a new form ?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  </head>
  <body>
    <form method="post" enctype="multipart/form-data">
      What fields would you like to copy (comma separated list)<br/>
      <input type="text" name="fieldList" />
      <br/><br/>
      What form do you want to copy into (comma separated list)<br/>
      <input type="text" name="copyForm" />
      <input type="submit" value="Copy" name="submit">
    </form>
  </body>
</html>

<?php
include '../../../../wp-load.php';
if(isset($_POST['copyForm'])&& isset($_POST['fieldList'])){
  $fieldList = $_POST['fieldList'];
  $copyForms  = $_POST['copyForm'];

  //get master form
  $form    = GFAPI::get_form( 9 );

  //remove any extra spaces in the field list
  $fieldList = str_replace(' ', '', $fieldList);

  //place them into an array
  $fields = explode(",", $fieldList);

  //find the requested fields
  $fieldsUpd = array();
  foreach($form['fields'] as $formField){
    //echo $formField->id.'<br/>';
    if(in_array($formField->id,$fields)){
      $fieldsUpd[] = $formField;
    }
  }
  if(empty($fieldsUpd)){
    echo 'Requested Fields not found';
  }else{
    //remove any extra spaces in the form list
    $copyForms = str_replace(' ', '', $copyForms);

    //place them into an array
    $copyForms = explode(",", $copyForms);
    foreach($copyForms as $copyForm){
      echo 'Copying fields '.$_POST['fieldList'] . ' to form '.$copyForm.'<br/>';
      $updForm = GFAPI::get_form( $copyForm );

      //find out if the field is already in the form
      foreach($updForm['fields'] as $formField){
        $errMsg  = '';
        if(in_array($formField->id,$fields)){
          $errMsg = 'Error: Field ' . $formField->id. ' already in form ' .$copyForm;
          break;
        }
      }
      if($errMsg==''){
        $updForm['fields'] = array_merge($updForm['fields'],$fieldsUpd);
        $result = GFAPI::update_form( $updForm );
        if(!$result){
          var_dump($result);
        }else{
          echo 'Form - '.$copyForm.' Updated<br/>';
        }
        echo '<br/><br/>';
      }else{
        echo $errMsg.'<br/>Process Failed on form '.$copyForm;
      }
    }
  }
}


