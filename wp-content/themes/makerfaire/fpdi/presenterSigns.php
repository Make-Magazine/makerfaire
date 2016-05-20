<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//set up database
$root = $_SERVER['DOCUMENT_ROOT'];
require_once( $root.'/wp-config.php' );
require_once( $root.'/wp-includes/wp-db.php' );
if (!is_user_logged_in())
    auth_redirect();
//error_log('start of makersigns.php '.date('h:i:s'),0);
// require tFPDF
require_once('fpdf/fpdf.php');

class PDF extends FPDF{
  // Page header
  function Header(){
    global $root;
    $image = $root.'/wp-content/themes/makerfaire/fpdi/pdf/speaker_info_sign_layout.png';
     // Logo
    $this->Image($image, 0, 0, $this->w, $this->h);
    // Arial bold 15
    $this->SetFont('Benton Sans','B',15);
  }
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AddFont('Benton Sans','B', 'bentonsans-bold-webfont.php');
$pdf->AddFont('Benton Sans','', 'bentonsans-regular-webfont.php');
$pdf->AddPage('L',array(279.4,431.8));
$pdf->SetFont('Benton Sans','',12);
$pdf->SetFillColor(255,255,255);

//get the entry-id, if one isn't set return an error
if(isset($_GET['eid']) && $_GET['eid']!=''){
  $faire = (isset($_GET['faire']) && $_GET['faire']!='' ? $_GET['faire']:'');
  $entryid = sanitize_text_field($_GET['eid']);
  createOutput($entryid, $pdf);
  if(isset($_GET['type']) && $_GET['type']=='download'){
    if (ob_get_contents()) ob_clean();
    $pdf->Output($entryid.'.pdf', 'D');
  }elseif(isset($_GET['type']) && $_GET['type'] == 'save'){
    $filename = TEMPLATEPATH.'/presenterSigns/'.$faire.'/'.$entryid.'.pdf';
    $dirname = dirname($filename);
    if (!is_dir($dirname)){
      mkdir($dirname, 0755, true);
    }
    $pdf->Output($filename, 'F');
    echo $entryid;
  }else{
    if (ob_get_contents()) ob_clean();
    $pdf->Output($entryid.'.pdf', 'I');
  }

  //error_log('after writing pdf '.date('h:i:s'),0);
}else{
    echo 'No Entry ID submitted';
}


function createOutput($entry_id,$pdf){
  global $wpdb;
  $sql = "select entity.presentation_title,
          (select  group_concat( distinct concat(maker.`FIRST NAME`,' ',maker.`LAST NAME`) separator ', ') as Makers
              from    wp_mf_maker maker,
                      wp_mf_maker_to_entity maker_to_entity
              where   entity.lead_id           = maker_to_entity.entity_id  AND
                      maker_to_entity.maker_id    = maker.maker_id AND
                      maker_to_entity.maker_type != 'Contact'
              group by maker_to_entity.entity_id
          )  as makers_list
          FROM    wp_mf_entity entity
          where entity.lead_id = ".$entry_id;
  foreach($wpdb->get_results($sql) as $row){
    $presenters = $row->makers_list;
    $presentation_title = $row->presentation_title;
  }
    // Project ID
    $pdf->SetFont('Benton Sans','B',55);
    $x = 65;    // set the starting font size
    /* Cycle thru decreasing the font size until it's width is lower than the max width */
    while( $pdf->GetStringWidth( utf8_decode( $presenters)) > 355.6 ){
        $x--;   // Decrease the variable which holds the font size
        $pdf->SetFont( 'Benton Sans','B',$x);
    }

    $lineHeight = $x*0.2645833333333*1.3;
    $pdf->setTextColor(160,0,0);
    $pdf->SetXY(38, 165);
    $pdf->MultiCell(355.6,$lineHeight, $presenters,0,'C');
$pdf->SetXY(38, 50);
$pdf->MultiCell(355.6,$lineHeight, $presenters,0,'C');


    // Presentation Title
    $pdf->setTextColor(0);
    $pdf->SetXY(38, 205);

    //auto adjust the font so the text will fit
    $x = 65;    // set the starting font size
    $pdf->SetFont( 'Benton Sans','B',80);

    /* Cycle thru decreasing the font size until it's width is lower than the max width */
    while( $pdf->GetStringWidth( utf8_decode( $presentation_title)) > 355.6 ){
        $x--;   // Decrease the variable which holds the font size
        $pdf->SetFont( 'Benton Sans','B',$x);
    }
    $lineHeight = $x*0.2645833333333*1.3;


    /* Output the title at the required font size */
    $pdf->MultiCell(355.6, $lineHeight, $presentation_title,0,'C');
    $pdf->SetXY(38, 95);
    $pdf->MultiCell(355.6, $lineHeight, $presentation_title,0,'C');

}

function filterText($text){
	$string = iconv('UTF-8', 'windows-1252',$text);

	//now translate any unicode stuff...
	$conv = array(
      "&amp;" => '&');
	return strtr($string, $conv);
}

?>