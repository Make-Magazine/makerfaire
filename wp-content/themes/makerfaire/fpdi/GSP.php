<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//set up database
$root = $_SERVER['DOCUMENT_ROOT'];
require_once( $root . '/wp-config.php' );
require_once( $root . '/wp-includes/wp-db.php' );
if (!is_user_logged_in())
   auth_redirect();

// require tFPDF
require_once('fpdf/fpdf.php');
require_once('fpdi/fpdi.php');

// The class, that overwrites the Header() method:
class PDF extends FPDI {

   protected $_tplIdx;

   public function Header() {
      $docTitle = 'General Safety Plan';
      $faire = (isset($_GET['faire']) ? $faire = $_GET['faire'] : '');
      global $wpdb;
      $faire = (isset($_GET['faire']) && $_GET['faire'] != '' ? $_GET['faire'] : 'BA16');
      $faireData = $wpdb->get_row('select faire_name, start_dt, end_dt from wp_mf_faire where faire="' . $faire . '"');

      $faireName = $faireData->faire_name;
      $start_dt = date('F j', strtotime($faireData->start_dt));
      $end_dt = date('j, Y', strtotime($faireData->end_dt));
      $dates = $start_dt . ' - ' . $end_dt;

      $this->SetTextColor(0);
      $this->SetFont('Helvetica', 'B', 20);
      $this->SetXY(15, 22);

      //faire name
      $this->Cell(0, 0, $docTitle, 0);

      $this->SetFont('Helvetica', 'B', 16);
      $this->SetXY(15, 30);
      //faire name
      $this->Cell(0, 0, $faireName, 0);
      //faire dates
      $this->SetFont('Helvetica', 'B', 12);
      $this->SetXY(15, 36);
      $this->Cell(0, 0, $dates, 0);

      //set font size for PDF answers
      $this->SetFont('Helvetica', '', 10);
      $this->SetXY(15, 52);
   }

}

// initiate FPDI
$pdf = new PDF();
$pdf->SetMargins(15, 15, 15);

$form_id = (isset($_GET['form']) && $_GET['form'] != '' ? $_GET['form'] : '102');
$faire = (isset($_GET['faire']) ? $faire = $_GET['faire'] : '');

$faireFormIDs = $wpdb->get_var('select form_ids from wp_mf_faire where faire="' . $faire . '"');

//remove spaces
$faireFormIDs = str_replace(' ', '', $faireFormIDs);
$faireFormIDs = explode(',', $faireFormIDs);

$form = GFAPI::get_form($form_id);
$fieldData = array();

//put fieldData in a usable array
foreach ($form['fields'] as $field) {
   $fieldData[$field['id']] = $field;
}
$search_criteria = array('status' => 'active');
$paging = array('offset' => 0, 'page_size' => 9999);
$entries = GFAPI::get_entries($form_id, $search_criteria, null, $paging);


foreach ($entries as $entry) {
   //get original entry info 
   $origEntry = GFAPI::get_entry($entry['3']);

   //check if original entry is in the faire we are creating a report for
   if (in_array($origEntry['form_id'], $faireFormIDs)) {
      output_data($pdf, $entry, $form, $fieldData);
   }
}
if (ob_get_contents())
   ob_clean();
$pdf->Output('GSP.pdf', 'D');        //output download
//$pdf->Output('doc.pdf', 'I');        //output in browser

function output_data($pdf, $lead = array(), $form = array(), $fieldData = array()) {
   $pdf->AddPage();
   $dataArray = array(
       array('Project ID #', 3),
       array('Project Name', 28),
       array('Name of person responsible for fire safety at your exhibit', 5),
       array('Their Email', 15),
       array('Their phone', 16),
       array('Description', 19),
       array('Describe your safety concerns', 12),
       array('Describe how you plan to keep your exhibit safe', 20),
       array('Who will be assisting at your exhibit to keep it safe', 11),
       array('Placement Requirements', 7),
       array('Do you have Insurance', 9),
       array('Additional Comments', 13),
       array('Are you 18 years or older?', 23),
       array('Signed', 25),
       array('I am the Parent and/or Legal Guardian of', 26),
       array('Date', 27)
   );
   $pdf->SetFillColor(190, 210, 247);
   $lineheight = 6;
   foreach ($dataArray as $data) {
      $fieldID = $data[1];
      if (isset($fieldData[$fieldID])) {
         $field = $fieldData[$fieldID];
         $value = RGFormsModel::get_lead_field_value($lead, $field);
         if (RGFormsModel::get_input_type($field) != 'email') {
            $display_value = GFCommon::get_lead_field_display($field, $value);
            $display_value = apply_filters('gform_entry_field_value', $display_value, $field, $lead, $form);
         } else {
            $display_value = $value;
         }
      } else {
         $display_value = '';
      }
      $display_value = str_replace('<br />', "\n", $display_value);
      $display_value = htmlspecialchars_decode($display_value, ENT_QUOTES);

      $pdf->MultiCell(0, $lineheight, $data[0] . ': ');
      $pdf->MultiCell(0, $lineheight, $display_value, 0, 'L', true);
      $pdf->Ln();
   }
}
