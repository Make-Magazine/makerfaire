<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// require tFPDF
require_once('fpdf/fpdf.php');
require_once('fpdi/fpdi.php');

// The class, that overwrites the Header() method:
class PDF extends FPDI
{
    protected $_tplIdx;

    public function Header()
    {
        $docTitle   = 'Fire Safety Plan';
        $faireName  = 'Maker Faire Bay Area 2016';
        $badge      = 'http://makerfaire.com/wp-content/themes/makerfaire/images/MF16BA_Badge_255.jpg';
        $dates      = 'May 20-22, 2016';

        $this->SetTextColor(0);
        $this->SetFont('Helvetica','B',20);
        $this->SetXY(15,22);

        //Doc Title
        $this->Cell(0,0,$docTitle,0);
        $this->SetFont('Helvetica','B',16);
        $this->SetXY(15,30);

        //faire name
        $this->Cell(0,0,$faireName,0);

        //faire dates
        $this->SetFont('Helvetica','B',12);
        $this->SetXY(15,36);
        $this->Cell(0,0,$dates,0);

        //faire logo
        $this->Image($badge,153,11,45,0,'jpg');

        //set font size for PDF answers
        $this->SetFont('Helvetica','',10);
        $this->SetXY(15,52);
    }
}

// initiate FPDI
$pdf = new PDF();
$pdf->SetMargins(15,15,15);

$form_id = 35;
$form = GFAPI::get_form( $form_id );
$fieldData = array();
//put fieldData in a usable array
foreach($form['fields'] as $field){
    $fieldData[$field['id']] = $field;
}
$entries = GFAPI::get_entries( $form_id );

foreach($entries as $entry){
    output_data($pdf,$entry,$form,$fieldData);
}

// Output the new PDF
ob_clean();
$pdf->Output('FSP.pdf', 'D');        //output download
//$pdf->Output('doc.pdf', 'I');        //output in browser

function output_data($pdf,$lead=array(),$form=array(),$fieldData=array()){
    $pdf->AddPage();
        $dataArray = array(
                        array('Project ID #',3,'text'),
                        array('Project Name', 38,'text'),
                        array('Name of person responsible for fire safety at your exhibit', 21,'text'),
                        array('Their Email',23,'text'),
                        array('Their Phone', 24,'text'),
                        array('Description', 37,'textarea'),
                        array('Describe your fire safety concerns', 19,'textarea'),
                        array('Describe how you plan to keep your exhibit safe', 27,'textarea'),
                        array('Who will be assisting at your exhibit to keep it safe', 20,'text'),
                        array('Placement Requirements', 7,'textarea'),
                        array('What is burning',10,'text'),
                        array('What is the fuel source',11,'text'),
                        array('how much is fuel is burning and in what time period',12,'textarea'),
                        array('how much fuel will you have at the event, including tank sizes',13,'textarea'),
                        array('where and how is the fuel stored',14,'text'),
                        array('Does the valve have an electronic propane sniffer',15,'text'),
                        array('Other suppression devices',16,'textarea'),
                        array('Do you have insurance?',18,'text'),
                        array('Additional comments',28,'textarea'),
                        array('Are you 18 years or older?',30,'text'),
                        array('Signed',32,'text'),
                        array('I am the Parent and/or Legal Guardian of',33,'text'),
                        array('Date',34,'text' )
                );
        $pdf->SetFillColor(190, 210, 247);
        $lineheight = 6;
        foreach($dataArray as $data){
            $fieldID = $data[1];
            if(isset($fieldData[$fieldID])){
                $field = $fieldData[$fieldID];
                $value = RGFormsModel::get_lead_field_value( $lead, $field );
                if(RGFormsModel::get_input_type($field)!='email'){
                    $display_value = GFCommon::get_lead_field_display( $field, $value);
                    $display_value = apply_filters( 'gform_entry_field_value', $display_value, $field, $lead, $form );
                }else{
                    $display_value =  $value;
                }
            }else{
                $display_value = '';
            }
            $display_value = str_replace('<br />', "\n", $display_value);
            $display_value = htmlspecialchars_decode($display_value,ENT_QUOTES);

            $pdf->MultiCell(0, $lineheight, $data[0].': ');
            $pdf->MultiCell(0, $lineheight, $display_value,0,'L',true);
            $pdf->Ln();
        }

}

