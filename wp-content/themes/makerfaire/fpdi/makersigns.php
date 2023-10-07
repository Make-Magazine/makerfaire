<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
      }
      // Arial bold 15
      $this->SetFont('Benton Sans', 'B', 15);
   }

}

// Instanciation of inherited class
try {
   $pdf = new PDF_Clipping();;
   $pdf->AddFont('Benton Sans', 'B', 'bentonsans-bold-webfont.php');
   $pdf->AddFont('Benton Sans', '', 'bentonsans-regular-webfont.php');
   $pdf->AddPage('P', array(381, 381));
   $pdf->SetFont('Benton Sans', '', 12);
   $pdf->Image('signBackground2023.png', 0, 0, 381, 381); // background image
   //$pdf->SetFillColor(255, 255, 255); white background
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
         $validFile = TEMPLATEPATH . '/signs/' . $faire . '/maker/' . $entryid . '.pdf';
         $errorFile = TEMPLATEPATH . '/signs/' . $faire . '/maker/error/' . $entryid . '.pdf';

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
   $makers = array();
   if (isset($entry['160.3']) && strlen($entry['160.3']) > 0)
      $makers[] = filterText($entry['160.3'] . ' ' . $entry['160.6']);
   if (isset($entry['158.3']) && strlen($entry['158.3']) > 0)
      $makers[] = filterText($entry['158.3'] . ' ' . $entry['158.6']);
   if (isset($entry['155.3']) && strlen($entry['155.3']) > 0)
      $makers[] = filterText($entry['155.3'] . ' ' . $entry['155.6']);
   if (isset($entry['156.3']) && strlen($entry['156.3']) > 0)
      $makers[] = filterText($entry['156.3'] . ' ' . $entry['156.6']);
   if (isset($entry['157.3']) && strlen($entry['157.3']) > 0)
      $makers[] = filterText($entry['157.3'] . ' ' . $entry['157.6']);
   if (isset($entry['159.3']) && strlen($entry['159.3']) > 0)
      $makers[] = filterText($entry['159.3'] . ' ' . $entry['159.6']);
   if (isset($entry['154.3']) && strlen($entry['154.3']) > 0)
      $makers[] = filterText($entry['154.3'] . ' ' . $entry['154.6']);

   // maker 1 bio
   //$bio = (isset($entry['234']) ? filterText($entry['234']) : '');

   $groupname = (isset($entry['109']) ? filterText($entry['109']) : '');
   //$groupbio = (isset($entry['110']) ? filterText($entry['110']) : '');

   // Field from Gravity form which is the image
   $project_photo = (isset($entry['22']) ? $entry['22'] : '');
   //Check for image override
   $overrideImg = findOverride($entry_id, 'signs');
   if ($overrideImg != '')
      $project_photo = $overrideImg;

   // project gallery was introduced with BA23 - this returns an array of image urls from the additional images field
   $project_gallery = (isset($entry['878']) ? explode(",", str_replace(array( '[', ']', '"' ), '', $entry['878'])) : '');

   //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery
   if($project_photo=='' && is_array($project_gallery)){
       $project_photo = $project_gallery[0];
   }
   
   $project_short = (isset($entry['16']) ? filterText($entry['16']) : '');
   $project_affiliation = (isset($entry['168']) ? filterText((string) $entry['168']) : '');
   $project_title = (isset($entry['151']) ? filterText((string) $entry['151']) : '');
   //$project_title = "this is my long long title it goes for two lines, maybe more";
   $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);

   global $wpdb;
   $location_sql = "select  subarea.nicename, location.location
   from  wp_mf_location location
   left outer join  wp_mf_faire_subarea subarea
                   ON  location.subarea_id = subarea.ID
    where location.entry_id=$entry_id" . ' AND location.location <> ""';
   $location_results = $wpdb->get_results($location_sql);
   
   $project_subarea = isset($location_results[0]->nicename) ? $location_results[0]->nicename : '';
   $project_booth = isset($location_results[0]->location) ? $location_results[0]->location : '';

   /***************************************************************************
    * Project ID
    ***************************************************************************/
   /*$pdf->SetFont('Benton Sans', '', 12);
   $pdf->setTextColor(168, 170, 172);
   $pdf->SetXY(240, 20);
   $pdf->MultiCell(115, 15, $entry_id, 0, 'L');*/
   
   /***************************************************************************
    * Project Title
    * auto adjust the font so the text will fit
    ***************************************************************************/
   $pdf->setTextColor(255, 255, 255);
   $pdf->SetXY(20, 40);

   // auto adjust the font so the text will fit
   $x = 72; // set the starting font size
   $pdf->SetFont('Benton Sans', 'B', 72);

   /* Cycle thru decreasing the font size until it's width is lower than the max width */
   /*while ($pdf->GetStringWidth(utf8_decode($project_title)) > 410) {
      $x = $x-.1; // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', 'B', $x);
   }*/
   $lineHeight = $x * 0.2645833333333 * 1.5;

   /* Output the title at the required font size */
   $pdf->MultiCell(340, $lineHeight, $project_title, 0, 'C');

   /***************************************************************************
    * Affiliation
    * auto adjust the font so the text will fit
    ***************************************************************************/
    if ($project_affiliation != '') {
      $pdf->setTextColor(255, 255, 255);
      $pdf->SetXY(20, 20);
   
      // auto adjust the font so the text will fit
      $x = 32; // set the starting font size
      $pdf->SetFont('Benton Sans', '', 42);
   
      /* Cycle thru decreasing the font size until it's width is lower than the max width */
      /*while ($pdf->GetStringWidth(utf8_decode($project_title)) > 410) {
         $x = $x-.1; // Decrease the variable which holds the font size
         $pdf->SetFont('Benton Sans', 'B', $x);
      }*/
      $lineHeight = $x * 0.2645833333333 * 1.5;
   
      /* Output the title at the required font size */
      $pdf->MultiCell(340, $lineHeight, strtoupper($project_affiliation), 0, 'C');
   } 

   /***************************************************************************
    * Location / Booth    
    ***************************************************************************/
    $pdf->setTextColor(245, 20, 0);
    $pdf->SetFont('Benton Sans', '', 42);
    $pdf->Text(21, 247, $project_subarea);
    $pdf->setTextColor(245, 20, 0);
    $pdf->SetFont('Benton Sans', '', 42);
    $pdf->Text(21, 267, $project_booth);
 
     
   /***************************************************************************
    * QR code    
    ***************************************************************************/
   $pdf->setTextColor(245, 20, 0);
   $pdf->SetFont('Benton Sans', '', 33);
   $pdf->Text(21, 297, "Learn More");

   $entryURL = ($entry['27']!==''?$entry['27']:'https://makerfaire.com/maker/entry/'.$entry_id.'/');
   $QR_Code = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.urlencode($entryURL);
   $pdf->Image($QR_Code,20,305,65,null,image_type_to_extension(IMAGETYPE_PNG,false));
    
          
   /***************************************************************************
    * field 22 - project photo
    * image should never be larger than 450x450
    ***************************************************************************/
   if ($project_photo != '') {
      $photo_extension = pathinfo($project_photo, PATHINFO_EXTENSION);
      if ($photo_extension) {
         //fit image onto pdf
         
         $project_photo = legacy_get_fit_remote_image_url( stripslashes($project_photo), 750, 750, 0);
         // figure out whether x or y is bigger on the image and make it fit for that
         list($x1, $y1) = getimagesize($project_photo);
         $x2 = 180;
         $y2 = 180;
         if(($x1 / $x2) < ($y1 / $y2)) {
            $y2 = 0;
         } else {
            $x2 = 0;
         }
         $pdf->ClippingCircle(257,220,83,false);
         $pdf->Image($project_photo, 168, 133, $x2, $y2, $photo_extension);
         $pdf->UnsetClipping();

         //list($width, $height) = resizeToFit($project_photo);
                           
         //$pdf->Image($project_photo, 22, 110, $width, $height, $photo_extension);
      } else {
         error_log("Unable to find the image for entry $entry_id for $project_photo");
         $resizeImage = 0;
      }
   } else {
      error_log("Missing image for $entry_id");
      $resizeImage = 0;
   }

   /***************************************************************************
    * field 16 - short description
    * auto adjust the font so the text will fit
    ***************************************************************************/   
   /*$pdf->SetXY(145, 110);

   // auto adjust the font so the text will fit
   $sx = 28; // set the starting font size
   $pdf->SetFont('Benton Sans', '', $sx);

   // Cycle thru decreasing the font size until it's width is lower than the max width
   while ($pdf->GetStringWidth(utf8_decode($project_short)) > 1500) {
      $sx = $sx - .1; // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', '', $sx);
   }

   $lineHeight = $sx * 0.2645833333333 * 1.5;

   $pdf->MultiCell(125, $lineHeight, $project_short, 0, 'L');*/
   
   /***************************************************************************
    * maker info, use a background of white to overlay any long images or text
    ***************************************************************************/
   $pdf->setTextColor(255, 255, 255);
   $pdf->SetFont('Benton Sans', 'B', 40);

   $pdf->SetXY(50, 145.5);
   /*if (!empty($groupbio)) {
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
   } else { */
      $makerList = implode(', ', $makers);
      $pdf->SetFont('Benton Sans', 'B', 40);

      // auto adjust the font so the text will fit
      $x = 50; // set the starting font size

      /* Cycle thru decreasing the font size until it's width is lower than the max width */
      while ($pdf->GetStringWidth(utf8_decode($makerList)) > 450) {
         $x = $x -.1; // Decrease the variable which holds the font size
         $pdf->SetFont('Benton Sans', '', $x);
      }
      $lineHeight = $x * 0.2645833333333 * 1.5;
      $pdf->MultiCell(100, $lineHeight, strtoupper($makerList), 0, 'L', false);
      // if size of makers is 1, then display maker bio
      /* if (sizeof($makers) == 1) {
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
      }*/
   //}
   return $resizeImage;
}

function filterText($text) {
   try {
      $string = iconv('UTF-8', 'windows-1252', $text);
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

?>