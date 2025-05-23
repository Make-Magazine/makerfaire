<?php
/*
 * Create Maker sign of submitted entry
 */

// set up database
$root = $_SERVER['DOCUMENT_ROOT'];
require_once ($root . '/wp-config.php');
require_once ($root . '/wp-includes/class-wpdb.php');

/*if (!is_user_logged_in())
   auth_redirect();*/
const DPI = 96;
const MM_IN_INCH = 25.4;
//image sizes
const MAX_WIDTH = 450;
const MAX_HEIGHT = 450;

// require FPDF
require_once ('fpdf/fpdf.php');
// require clipping
require ('fpdf/clipping.php');

class PDF extends FPDF {

   // Page header
   function Header() {
      // Header required when using restful structures for Chrome, otherwise generating signs curl will get a 403
      header('HTTP/1.0 200 OK');
      header('Cache-Control: public, must-revalidate, max-age=0');
      header('Pragma: no-cache');
      header('Accept-Ranges: bytes');
      header("Content-Transfer-Encoding: binary");
      header("Content-type: application/pdf");
      // Faire sign setup
      /*
      global $root;
      global $wp_query;
      $faire = '';
      if (isset($wp_query->query_vars['faire'])) {
         $faire = $wp_query->query_vars['faire'];
      } else if (isset($_GET['faire']) && $_GET['faire'] != '') {
         $faire = $_GET['faire'];
      }

      $image = $root . '/wp-content/themes/makerfaire/images/' . $faire . '-maker_sign.jpg';
      // Logo
      if (file_exists($image)) {
         $this->Image($image, 0, 0, $this->w, $this->h);
      }*/
      // Arial bold 15
      $this->SetFont('Benton Sans', 'B', 15);
   }

}

// Instanciation of inherited class
try {
   $pdf = new PDF_Clipping();;
   $pdf->AddFont('Benton Sans', 'B', 'bentonsans-bold-webfont.php');
   $pdf->AddFont('Benton Sans', '', 'bentonsans-regular-webfont.php');
   $pdf->AddFont('FontAwesome1','','FontAwesome47-P1.php'); // https://drive.google.com/file/d/1Y3NlxBZtXPcFUIwiLeQWzdzdoSe9f6xo/view?pli=1
   $pdf->AddFont('FontAwesome2','','FontAwesome47-P2.php'); // https://drive.google.com/file/d/1XjjEyhkcD0mO6FTf0w9XHB4bwjMCL2ij/view
   $pdf->AddFont('FontAwesome3','','FontAwesome47-P3.php'); // https://drive.google.com/file/d/10WBuA63DMbNPRWjKSKJpVSk4I1OPwh2R/view
   $pdf->AddFont('FontAwesome4','','FontAwesome47-P4.php'); // https://drive.google.com/file/d/1lPeh5IGXY8Re6nNXEU7i0Wf63o97Svx0/view
   $pdf->AddPage('P', array(288, 576));
   $pdf->SetFont('Benton Sans', '', 12);
   $pdf->Image(get_template_directory().'/generate_pdf/pdf_layouts/signBackground2024.png', 0, 0, 288, 576); // background image
   
   $pdf->SetMargins(20,139,22); //left, top, right

   
   
   // get the entry-id, if one isn't set return an error
   $eid = '';
   if (isset($wp_query->query_vars['eid'])) {
      $eid = $wp_query->query_vars['eid'];
      //error_log("EID Query Vars: " . $wp_query->query_vars['eid']);
   } else if (isset($_GET['eid']) && $_GET['eid'] != '') {
      $eid = $_GET['eid'];
      //error_log("EID: ".$_GET['eid']);
   }

   if (isset($eid) && $eid != '') {
      $faire = '';
      if (isset($wp_query->query_vars['faire'])) {
         $faire = $wp_query->query_vars['faire'];
      } else if (isset($_GET['faire']) && $_GET['faire'] != '') {
         $faire = $_GET['faire'];
      }
      $entryid = sanitize_text_field($eid);
      $resizeImage = createOutput($entryid, $pdf);

      // error_log("Resize Image: $resizeImage for Entry Id: $entryid");
      if (isset($_GET['type']) && $_GET['type'] == 'download') {
         if (ob_get_contents())
            ob_clean();
         $pdf->Output($entryid . '.pdf', 'D');
      } elseif (isset($_GET['type']) && $_GET['type'] == 'save') {
         $validFile = get_template_directory() . '/signs/' . $faire . '/maker/' . $entryid . '.pdf';
         $errorFile = get_template_directory() . '/signs/' . $faire . '/maker/error/' . $entryid . '.pdf';

         if ($resizeImage) {
            $filename = $validFile;
            // If the file exists in the error log - delete it  
            if (file_exists($errorFile)) {
               unlink(realpath($errorFile));
            }
         } else {
            $filename = $errorFile;
            // If the file exists in the regular path - delete it    
            if (file_exists($validFile)) {
               unlink(realpath($validFile));
            }
         }

         $dirname = dirname($filename);
         
         if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
         }
         if (ob_get_contents())
            ob_clean();
         $pdf->Output($filename, 'F');

         exit();
      } else {
         if (ob_get_contents())
            ob_clean();
         $pdf->Output($entryid . '.pdf', 'I');
      }

      //error_log('after writing pdf '.date('h:i:s'),0);
   } else {
      echo 'No Entry ID submitted';
   }
} catch (Exception $e) {
   error_log("Unable to create PDF due to: " . $e);
}

function createOutput($entry_id, $pdf) {
   // Initialize the variable that the image was resized
   $resizeImage = 1;
   $entry = GFAPI::get_entry($entry_id);

   // Field from Gravity form which is the image
   $project_photo = (isset($entry['22']) ? $entry['22'] : '');
   $photo = json_decode($project_photo);
   if (is_array($photo)) {
      $project_photo = $photo[0];
   }
   
   // this returns an array of image urls from the additional images field
   $project_gallery = (isset($entry['878']) ? json_decode($entry['878']) : '');

   //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery
   if($project_photo=='' && is_array($project_gallery)){
       $project_photo = $project_gallery[0];
   }

   // Field from Gravity form which is maker image or group image
   $group_photo = ($entry['111'] ? $entry['111'] : '');
   $maker_photo = (($entry['217'] && !empty($entry['217']) && $entry['217'] != "[]" && $entry['217'] != '[]') ? $entry['217'] : $group_photo);

   $photo = json_decode($maker_photo);

   if (is_array($photo) && !empty($photo)) {
      $maker_photo = $photo[0];
   } else { // it's the final default image if no maker or group photo is found
      $maker_photo = get_template_directory().'/images/default-makey-medium.png';
   }

   
   if($project_photo !=''){
      //$project_photo= stripslashes($project_photo);      
      
      $imgSize = getimagesize($project_photo);
      // NOTE: we need a new default image
      $error_photo = get_template_directory().'/images/default-featured-image.jpg';
      
      if(!$imgSize){
         error_log('error in getimagesize for '.$project_photo.' for '.$entry_id);         
         $project_photo  = $error_photo;//fpdf does not support 16 bit png images      
      }elseif($imgSize['mime']!= 'image/jpeg' && $imgSize['mime']!= 'image/png'){
         error_log('mime type is '.$imgSize['mime'].' for '.$project_photo.' for '.$entry_id);         
         $project_photo  = $error_photo;//fpdf does not support 16 bit png images      
         
      }elseif(isset($imgSize["bits"]) && $imgSize["bits"]==16){
         error_log("16bit depth image for $entry_id");
         $project_photo  = $error_photo;//fpdf does not support 16 bit png images      
      }
   }   

   $project_short = (isset($entry['16']) ? filterText($entry['16']) : '');
   $project_affiliation = (isset($entry['168']) ? filterText((string) $entry['168']) : '');
   $project_title = (isset($entry['151']) ? filterText((string) $entry['151']) : '');
   //$project_title = "this is my long long title it goes for two lines, maybe more";
   $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);

   foreach ($entry as $key => $value) {
      if (strpos($key ?? '', '339.') === 0) {
         if ($value != '') {
            if (stripos($value, 'sponsor') !== false) {
               $project_type = 'Exhibit';
            } else {
               $project_type = $value;
            }
         }
      }
   }

   $mainCategory = get_term($entry['320']);
   $project_category = (isset($mainCategory->name) ? html_entity_decode($mainCategory->name) : '');
  
   global $wpdb;
   $location_sql = "select  subarea.nicename, location.location
   from  wp_mf_location location
   left outer join  wp_mf_faire_subarea subarea
                   ON  location.subarea_id = subarea.ID
    where location.entry_id=$entry_id" . ' AND location.location <> ""';
   $location_results = $wpdb->get_results($location_sql);
   
   $project_subarea = isset($location_results[0]->nicename) ? $location_results[0]->nicename : '';
   //$project_booth = isset($location_results[0]->location) ? $location_results[0]->location : '';

   
   /***************************************************************************
    * Project Title
    * auto adjust the font so the text will fit
    ***************************************************************************/
   $pdf->setTextColor(43, 143, 192);
   $pdf->SetXY(16, 258);

   // auto adjust the font so the text will fit
   //$x = 72; // set the starting font size
   $pdf->SetFont('Benton Sans', 'B', 32);

   /* Cycle thru decreasing the font size until it's width is lower than the max width */
   /*while ($pdf->GetStringWidth(utf8_decode($project_title)) > 410) {
      $x = $x-.1; // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', 'B', $x);
   }
   $lineHeight = $x * 0.2645833333333 * 1.5;*/

   /* Output the title at the required font size */
   $pdf->MultiCell(250, 18, $project_title, 0, 'L', false, 2);

    /***************************************************************************
    * field 16 - short description
    * auto adjust the font so the text will fit
    ***************************************************************************/   
    $pdf->SetXY(16, 340);
    $pdf->setTextColor(51, 51, 51);

    // auto adjust the font so the text will fit
    $sx = 24; // set the starting font size
    $pdf->SetFont('Benton Sans', '', $sx);
 
    // Cycle thru decreasing the font size until it's width is lower than the max width
    /* while ($pdf->GetStringWidth(utf8_decode($project_short)) > 1500) {
       $sx = $sx - .1; // Decrease the variable which holds the font size
       $pdf->SetFont('Benton Sans', '', $sx);
    }*/
 
    $lineHeight = $sx * 0.2645833333333 * 1.8;
 
    // the last parameter here will limit the amount of lines and end with an ellipsis
    $pdf->MultiCell(250, $lineHeight, $project_short, 0, 'L', false, 6);

   /***************************************************************************
    * Location / Booth    
    ***************************************************************************/
    $pdf->setTextColor(245, 20, 0);
    $pdf->SetFont('FontAwesome4', '', 26);
    $pdf->Text(18, 312, chr(0x003D));
    $pdf->setTextColor(51, 51, 51);
    $pdf->SetFont('Benton Sans', '', 26);
    $pdf->Text(32, 312, $project_subarea);
    //$pdf->setTextColor(245, 20, 0);
    //$pdf->SetFont('Benton Sans', '', 42);
    //$pdf->Text(21, 267, $project_booth); // no longer showing booth

    /***************************************************************************
    * Type  
    **************************************************************************
    $pdf->setTextColor(245, 20, 0);
    $pdf->SetFont('FontAwesome1', '', 26);
    $pdf->Text(18, 310, chr(0x0031));
    $pdf->setTextColor(51, 51, 51);
    $pdf->SetFont('Benton Sans', '', 26);
    $pdf->Text(32, 310, $project_type);*/

    /***************************************************************************
    * Category  
    ***************************************************************************/
    $pdf->setTextColor(245, 20, 0);
    $pdf->SetFont('FontAwesome2', '', 26);
    $pdf->Text(18, 325, chr(0x0078));
    $pdf->setTextColor(51, 51, 51);
    $pdf->SetFont('Benton Sans', '', 26);
    $pdf->Text(32, 325, $project_category);
 
     
   /***************************************************************************
    * QR code    
    ***************************************************************************/

   $entryURL = 'https://makerfaire.com/maker/entry/'.$entry_id.'/';
   $QR_Code = 'https://quickchart.io/qr?text=' . urlencode($entryURL) . '&dark=d82a2e&margin=5&size=150';
   
   $pdf->Image($QR_Code,163,445,105,null,image_type_to_extension(IMAGETYPE_PNG,false));

   /***************************************************************************
    * Project ID
    ***************************************************************************/
    $pdf->SetFont('Benton Sans', '', 18);
    $pdf->setTextColor(91, 91, 91);
    $pdf->SetXY(203, 540);
    $pdf->MultiCell(115, 15, $entry_id, 0, 'L');
    
          
   /***************************************************************************
    * field 22 - project photo
    * image should never be larger than 450x450
    ***************************************************************************/
   if ($project_photo != '') {      
      $photo_extension = pathinfo($project_photo, PATHINFO_EXTENSION);
      if ($photo_extension) {
         //fit image onto pdf
         
         $project_photo = legacy_get_fit_remote_image_url( stripslashes($project_photo), 1200, 800, 0);

         $pdf->Image($project_photo, 0, 44.23, 288, 192, $photo_extension);


      } else {
         error_log("Unable to find the image for entry $entry_id for $project_photo");
         $resizeImage = 0;
      }
   } else {
      error_log("Missing image for $entry_id");
      $resizeImage = 0;
   }


   /***************************************************************************
    * field 217 - Maker photo
    * image should never be larger than 450x450
    ***************************************************************************/
    if ($maker_photo != '') {      
      $photo_extension = pathinfo($maker_photo, PATHINFO_EXTENSION);
      if ($photo_extension) {
         //fit image onto pdf
         
         $maker_photo = stripslashes($maker_photo);

         $pdf->ClippingRoundedRect(15.5,439.5,116.5,117.5,13.5,true);
         $pdf->Image($maker_photo,15,439,118,null,$photo_extension);
         
         //list($width, $height) = resizeToFit($maker_photo);
                           
         //$pdf->Image($maker_photo, 15, 439, $width, $height, $photo_extension);
      } else {
         error_log("Unable to find the Maker Photo for entry $entry_id for $maker_photo");
         $resizeImage = 0;
      }
   } else {
      error_log("Missing image for $entry_id");
      $resizeImage = 0;
   }
   
   /***************************************************************************
    * maker info, use a background of white to overlay any long images or text
    ***************************************************************************/
   /*$pdf->setTextColor(0, 0, 0);
   $pdf->SetFont('Benton Sans', 'B', 40);

   $pdf->SetXY(50, 145.5);
   if (!empty($groupbio)) {
      // auto adjust the font so the text will fit
      $sx = 40; // set the starting font size
      // Cycle thru decreasing the font size until it's width is lower than the max width
      while ($pdf->GetStringWidth(utf8_decode($groupname)) > 450) {
         $sx = $sx - .1; // Decrease the variable which holds the font size
         $pdf->SetFont('Benton Sans', 'B', $sx);
      }

      $lineHeight = $sx * 0.2645833333333 * 1.5;

      $pdf->MultiCell(0, $lineHeight, $groupname, 0, 'L', true);

      $pdf->setTextColor(0);
      $pdf->SetFont('Benton Sans', '', 24);

      // auto adjust the font so the text will fit
      $x = 24; // set the starting font size

      // Cycle thru decreasing the font size until it's width is lower than the max width 
      while ($pdf->GetStringWidth($groupbio) > 850) {
         $x = $x -.1; // Decrease the variable which holds the font size
         $pdf->SetFont('Benton Sans', '', $x);
      }
      $lineHeight = $x * 0.2645833333333 * 1.5;
      $pdf->MultiCell(0, $lineHeight, $groupbio, 0, 'L', true);
   } else { 
      $makerList = implode(', ', $makers);
      $pdf->SetFont('Benton Sans', 'B', 38);

      // auto adjust the font so the text will fit
      $x = 50; // set the starting font size

      // Cycle thru decreasing the font size until it's width is lower than the max width 
      while ($pdf->GetStringWidth(utf8_decode($makerList)) > 450) {
         $x = $x -.1; // Decrease the variable which holds the font size
         $pdf->SetFont('Benton Sans', '', $x);
      }
      $lineHeight = $x * 0.2645833333333 * 1.5;
      $pdf->MultiCell(120, $lineHeight, strtoupper($makerList), 0, 'L', false);
      // if size of makers is 1, then display maker bio
         if (sizeof($makers) == 1) {
         $pdf->setTextColor(0);
         $pdf->SetFont('Benton Sans', '', 24);

         // auto adjust the font so the text will fit
         $x = 24; // set the starting font size
         // Cycle thru decreasing the font size until it's width is lower than the max width 
         while ($pdf->GetStringWidth($bio) > 900) {
            $x = $x-.1; // Decrease the variable which holds the font size
            $pdf->SetFont('Benton Sans', '', $x);
         }

         $lineHeight = $x * 0.2645833333333 * 1.5;
         $pdf->MultiCell(0, $lineHeight, $bio, 0, 'L', true);
      }
   //}*/
   return $resizeImage;
}

function filterText($text) {
   try {
      $string = iconv('UTF-8', 'windows-1252//IGNORE', $text);
   } catch (Exception $e) {
      error_log("Unable to convert $text due to: " + $e);
      ini_set('mbstring.substitute_character', "none");
      $string = mb_convert_encoding($text, 'UTF-8', 'windows-1252');
   }

   // now translate any unicode stuff...
   $conv = array(
       "&amp;" => "&",
       "&#039;" => "'"
   );
   return strtr($string, $conv);
}

function pixelsToMM($val) {
   return $val * MM_IN_INCH / DPI;
}

function resizeToFit($imgFilename) {
   list($width, $height) = getimagesize($imgFilename);
   
   $widthScale = MAX_WIDTH / $width;
   $heightScale = MAX_HEIGHT / $height;
   $scale = min($widthScale, $heightScale);

   return array(
       round(pixelsToMM($scale * $width)),
       round(pixelsToMM($scale * $height))
   );
}