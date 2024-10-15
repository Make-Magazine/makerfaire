<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
      $image = $root . '/wp-content/themes/makerfaire/generate_pdf/pdf_layouts/speaker_sign_layout.jpg';
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
$pdf->AddPage('L', array(279.4, 431.8));
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
      $pdf->Output($entryid . '.pdf', 'D');
   } elseif (isset($_GET['type']) && $_GET['type'] == 'save') {
      $filename = get_template_directory() . '/signs/' . $faire . '/presenter/' . $entryid . '.pdf';
      $dirname = dirname($filename);
      if (!is_dir($dirname)) {
         mkdir($dirname, 0755, true);
      }
      $pdf->Output($filename, 'F');
      echo $entryid;
   } else {
      if (ob_get_contents())
         ob_clean();
      $pdf->Output($entryid . '.pdf', 'I');
   }

   //error_log('after writing pdf '.date('h:i:s'),0);
} else {
   echo 'No Entry ID submitted';
}

function createOutput($entry_id, $pdf) {
   global $wpdb;
   $presenters = $presentation_title = '';
   $sql = "select entity.presentation_title,
    (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = entity.lead_id and meta_key='96.3') as maker_fname,
    (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = entity.lead_id and meta_key='96.6') as maker_lname,
    (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = entity.lead_id and meta_key='109') as group_name,
    (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = entity.lead_id and meta_key='916') as presenter_list
    
          FROM    wp_mf_entity entity
          where entity.lead_id = " . $entry_id;
   $row = $wpdb->get_row($sql);
   
   $presentation_title = filterText($row->presentation_title);

   //determine presenter names
   $maker_name = $row->maker_fname . ($row->maker_lname!=''?' '.$row->maker_lname:'');
   $group_name = $row->group_name; 
   $presenter_list = unserialize($row->presenter_list);
 
   //if presenter list is set, use this instead of the maker name
   if(is_array($presenter_list)){         
       $maker_name = implode(", ", $presenter_list);
   }        
    
   $presenters = $maker_name . ($group_name!=''?' - '.$group_name:'');
   
   // Presenter names
   $pdf->SetFont('Benton Sans', 'B', 49);
   $x = 49;    // set the starting font size
   /* Cycle thru decreasing the font size until it's width is lower than the max width */
   while ($pdf->GetStringWidth($presenters) > 700) {
      $x = $x=.1;   // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', 'B', $x);
   }

   $lineHeight = $x * 0.2645833333333 * 1.5;
   
   //$presenters = "Judy Castro, Terry & Belinda Kilby, Jillian & Jefferey Northrup, Kyrsten Mate & Jon Sarriugarte";
   $presenterHeight = $pdf->GetStringWidth($presenters);
//$presenters .= '-'.$presenterHeight;
   if ($presenterHeight > 700) {
      $y1 = 30;
      $y2 = 145;
   } elseif ($presenterHeight > 350) {
      $y1 = 45;
      $y2 = 160;
   } else {
      $y1 = 55;
      $y2 = 170;
   }

   $x = 38;




   // Presentation Title
   

   //auto adjust the font so the text will fit
   $x = 49;    // set the starting font size
   $pdf->SetFont('Benton Sans', 'B', 49);

   /* Cycle thru decreasing the font size until it's width is lower than the max width */
   while ($pdf->GetStringWidth($presentation_title) > 600) {
      $x = $x-.1;   // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', 'B', $x);
   }
   $lineHeight = $x * 0.2645833333333 * 1.5;

   //Write Presentation title
   $pdf->setTextColor(0);
   
   //top section
   $pdf->SetXY($x, $y1);
   $pdf->MultiCell(355.6, $lineHeight, $presentation_title, 0, 'C');
   
   //bottom section
   $pdf->SetXY($x, $y2);
   $pdf->MultiCell(355.6, $lineHeight, $presentation_title, 0, 'C');

   //Write presenter name
   $pdf->setTextColor(160, 0, 0);
   
   //top section 
   $pdf->SetXY(38, 195); /* Output the title at the required font size */   
   $pdf->MultiCell(355.6, $lineHeight, $presenters, 0, 'C');

   //bottom section
   $pdf->SetXY(38, 80);
   $pdf->MultiCell(355.6, $lineHeight, $presenters, 0, 'C');
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