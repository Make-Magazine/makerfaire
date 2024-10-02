<?php
/*
 * Used to gnerate a load in pass with Area/Subarea shown
 */

//set up database
$root = $_SERVER['DOCUMENT_ROOT'];
require_once( $root . '/wp-config.php' );

// require FPDF
require_once('fpdf/fpdf.php');

class PDF extends FPDF {

   // Page header
   function Header() {
      global $root;
      $image = $root . '/wp-content/themes/makerfaire/generate_pdf/pdf_layouts/MF_TempLoadingPass.png';
      // Logo
      $this->Image($image, 0, 0, $this->w, $this->h);
      // Arial bold 15
      $this->SetFont('Benton Sans', 'B', 15);
   }

}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AddFont('Benton Sans', 'B', 'bentonsans-bold-webfont.php');
$pdf->AddFont('Benton Sans', '', 'bentonsans-regular-webfont.php');
$pdf->AddPage('P', 'Letter');
$pdf->SetFont('Benton Sans', '', 12);
$pdf->SetFillColor(255, 255, 255);

//get the entry-id, if one isn't set return an error
if (isset($_GET['eid']) && $_GET['eid'] != '') {
   $faire = (isset($_GET['faire']) && $_GET['faire'] != '' ? $_GET['faire'] : '');
   $entryid = sanitize_text_field($_GET['eid']);
   createOutput($entryid, $pdf);
   if (isset($_GET['type']) && $_GET['type'] == 'download') {
      if (ob_get_contents())
         ob_clean();
      $pdf->Output($entryid . '-LoadIn.pdf', 'D');
   } elseif (isset($_GET['type']) && $_GET['type'] == 'save') {
      $filename = get_template_directory() . '/signs/' . $faire . '/tempLoadIn/' . $entryid . '.pdf';
      $dirname = dirname($filename);
      if (!is_dir($dirname)) {
         mkdir($dirname, 0755, true);
      }
      $pdf->Output($filename, 'F');
      echo $entryid;
   } else {
      if (ob_get_contents())
         ob_clean();
      $pdf->Output('I');
   }

   //error_log('after writing pdf '.date('h:i:s'),0);
} else {
   echo 'No Entry ID submitted';
}

function createOutput($entry_id, $pdf) {    
   global $wpdb;
    //Populate the entry ID
    $pdf->setTextColor(0);
    $pdf->SetFont('Courier', 'B', 25);
    $pdf->SetXY(60, 75);   
    $pdf->Cell(20, 30, $entry_id, 0, 2, 'C');

   //Find assigned subarea
   $sql = "SELECT subarea.nicename FROM wp_mf_location location 
    left outer join wp_mf_faire_subarea subarea on subarea.id = location.subarea_id 
    left outer join wp_mf_schedule on wp_mf_schedule.entry_id=location.entry_id and wp_mf_schedule.location_id=location.id 
    where location.entry_id = $entry_id and start_dt is null";
   
   $subarea = $wpdb->get_var($sql);
   if($subarea != ''){
    $pdf->setTextColor(0);
    $pdf->SetFont('Benton Sans', 'B', 60);
    $pdf->SetXY(12, 160);   
    $pdf->MultiCell(190, 25, 'Loading in at '.$subarea, 0, 'C');    
   }
}

function filterText($text) {   
   $text = html_entity_decode(utf8_encode($text));
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

?>