<?php
/*
 * Create PDF of all Fire Safety plans filled out
 */
//set up database
$root = $_SERVER['DOCUMENT_ROOT'];
require_once( $root . '/wp-config.php' );
require_once( $root . '/wp-includes/class-wpdb.php' );
if (!is_user_logged_in())
   auth_redirect();


use \setasign\Fpdi\Fpdi;

require_once('fpdf/fpdf.php');
require_once('fpdi/src/autoload.php');


// The class, that overwrites the Header() method:
class PDF extends Fpdi {

   protected $_tplIdx;

   public function Header() {
      $docTitle = 'Fire Safety Plan';
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

      //Doc Title
      $this->Cell(0, 0, $docTitle, 0);
      $this->SetFont('Helvetica', 'B', 16);
      $this->SetXY(15, 30);

      //faire name
      $this->Cell(0, 0, $faireName, 0);

      //faire dates
      $this->SetFont('Helvetica', 'B', 12);
      $this->SetXY(15, 36);
      $this->Cell(0, 0, $dates, 0);

      //faire logo
      //$this->Image($badge,153,11,45,0,'jpg');
      //set font size for PDF answers
      $this->SetFont('Helvetica', '', 10);
      $this->SetXY(15, 42);
   }

   //function for making text bold
   public function WriteText($text = ''){
      $intPosIni = 0;
      $intPosFim = 0;
      if (strpos($text,'<')!==false && strpos($text,'[')!==false){
         if (strpos($text,'<')<strpos($text,'[')){
            $this->Write(5,substr($text,0,strpos($text,'<')));
            $intPosIni = strpos($text,'<');
            $intPosFim = strpos($text,'>');
            $this->SetFont('','B');
            $this->Write(5,substr($text,$intPosIni+1,$intPosFim-$intPosIni-1));
            $this->SetFont('','');
            $this->WriteText(substr($text,$intPosFim+1,strlen($text)));
         }else{
            $this->Write(5,substr($text,0,strpos($text,'[')));
            $intPosIni = strpos($text,'[');
            $intPosFim = strpos($text,']');
            $w=$this->GetStringWidth('a')*($intPosFim-$intPosIni-1);
            $this->Cell($w,$this->FontSize+0.75,substr($text,$intPosIni+1,$intPosFim-$intPosIni-1),1,0,'');
            $this->WriteText(substr($text,$intPosFim+1,strlen($text)));
         }
      }else{
         if (strpos($text,'<')!==false){
            $this->Write(5,substr($text,0,strpos($text,'<')));
            $intPosIni = strpos($text,'<');
            $intPosFim = strpos($text,'>');
            $this->SetFont('','B');
            $this->WriteText(substr($text,$intPosIni+1,$intPosFim-$intPosIni-1));
            $this->SetFont('','');
            $this->WriteText(substr($text,$intPosFim+1,strlen($text)));
         }elseif (strpos($text,'[')!==false){
            $this->Write(5,substr($text,0,strpos($text,'[')));
            $intPosIni = strpos($text,'[');
            $intPosFim = strpos($text,']');
            $w=$this->GetStringWidth('a')*($intPosFim-$intPosIni-1);
            $this->Cell($w,$this->FontSize+0.75,substr($text,$intPosIni+1,$intPosFim-$intPosIni-1),1,0,'');
            $this->WriteText(substr($text,$intPosFim+1,strlen($text)));
         }else{
            $this->Write(5,$text);
         }

      }
   }

}

// initiate FPDI
$pdf = new PDF();
$pdf->SetMargins(15, 15, 15);

$form_id = (isset($_GET['form']) && $_GET['form'] != '' ? $_GET['form'] : '101');
$faire   = (isset($_GET['faire']) ? $faire = $_GET['faire'] : '');

$faireFormIDs = $wpdb->get_var('select form_ids from wp_mf_faire where faire="' . $faire . '"');

//remove spaces
$faireFormIDs = str_replace(' ', '', $faireFormIDs ?? '');
$faireFormIDs = explode(',',$faireFormIDs);

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
   if(in_array($origEntry['form_id'], $faireFormIDs)){
      output_data($pdf, $entry, $form, $fieldData);
   }
}

// Output the new PDF
if (ob_get_contents())
   ob_clean();
$pdf->Output('FSP.pdf', 'D');        //output download
//$pdf->Output('doc.pdf', 'I');        //output in browser




function output_data($pdf, $lead = array(), $form = array(), $fieldData = array()) {
   $pdf->AddPage();
   $dataArray = array(
       array('Project ID #', 3, 'text'),
       array('Project Name', 38, 'text'),
       array('Name of person responsible for fire safety at your exhibit', 21, 'text'),
       array('Their Email', 23, 'text'),
       array('Their Phone', 24, 'text'),
       array('Description', 37, 'textarea'),
       array('Describe your fire safety concerns', 19, 'textarea'),
       array('Describe how you plan to keep your exhibit safe', 27, 'textarea'),
       array('Who will be assisting at your exhibit to keep it safe', 20, 'text'),
       array('Placement Requirements', 7, 'textarea'),
       array('What is burning', 10, 'text'),
       array('What is the fuel source', 11, 'text'),
       array('how much is fuel is burning and in what time period', 12, 'textarea'),
       array('how much fuel will you have at the event, including tank sizes', 13, 'textarea'),
       array('where and how is the fuel stored', 14, 'text'),
       array('Does the valve have an electronic propane sniffer', 15, 'text'),
       array('Other suppression devices', 16, 'textarea'),
       array('Do you have insurance?', 18, 'text'),
       array('Additional comments', 28, 'textarea'),
       array('Are you 18 years or older?', 30, 'text'),
       array('Signed', 32, 'text'),
       array('I am the Parent and/or Legal Guardian of', 33, 'text'),
       array('Date', 34, 'text')
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
      $display_value = str_replace('<br />', "\n", $display_value ?? '');
      $display_value = htmlspecialchars_decode( (string) $display_value, ENT_QUOTES);
      if(in_array($fieldID, array(37,19,27,20,7), true )) {
         $pdf->MultiCell(0, $lineheight, $pdf->WriteText("<" .$data[0] . '>: '));
         $pdf->MultiCell(0, $lineheight, $display_value, 0);
      } else {
         $pdf->MultiCell(0, $lineheight, $pdf->WriteText("<" .$data[0] . '>: ' . $display_value));
      }
      
      //$pdf->Ln(); // don't print this line between
   }
}
