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
        $docTitle   = 'General Safety Plan';
        $faireName  = 'Maker Faire Bay Area 2016';
        $badge      = 'http://makerfaire.com/wp-content/themes/makerfaire/images/MF16BA_Badge_255.jpg';
        $dates      = 'May 20-22, 2016';

        //$this->useTemplate($this->_tplIdx);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica','B',20);
        $this->SetXY(15,22);
        //faire name
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

$form_id = 36;
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

ob_clean();
$pdf->Output('GSP.pdf', 'D');        //output download

//$pdf->Output('doc.pdf', 'I');        //output in browser

function output_data($pdf,$lead=array(),$form=array(),$fieldData=array()){
    $pdf->AddPage();
        $dataArray = array(
                        array('Project ID #',3),
                        array('Project Name',28),
                        array('Name of person responsible for fire safety at your exhibit', 5),
                        array('Their Email', 15),
                        array('Their phone',16),
                        array('Description',19),
                        array('Describe your safety concerns',12),
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

