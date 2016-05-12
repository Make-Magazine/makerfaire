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
    $faire = (isset($_GET['faire']) && $_GET['faire']!='' ? $_GET['faire'].'-':'');
   // $image = $root.'/wp-content/themes/makerfaire/images/'.$faire.'maker_sign.png';
    // Logo
   // $this->Image($image, 0, 0, $this->w, $this->h);
    // Arial bold 15
    $this->SetFont('Benton Sans','B',15);
  }
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AddFont('Benton Sans','B', 'bentonsans-bold-webfont.php');
$pdf->AddFont('Benton Sans','', 'bentonsans-regular-webfont.php');
$pdf->AddPage('L',array(139.7,215.9));
$pdf->SetFont('Benton Sans','',12);
$pdf->SetFillColor(255,255,255);

//get the entry-id, if one isn't set return an error
if(isset($_GET['eid']) && $_GET['eid']!=''){
  $faire = (isset($_GET['faire']) && $_GET['faire']!='' ? $_GET['faire']:'');
  $entryid = sanitize_text_field($_GET['eid']);
  createOutput($entryid, $pdf);
  if(isset($_GET['type']) && $_GET['type']=='download'){
    if (ob_get_contents()) ob_clean();
    $pdf->Output($entryid.'-tabletag.pdf', 'D');
  }elseif(isset($_GET['type']) && $_GET['type'] == 'save'){
    $filename = TEMPLATEPATH.'/tabletags/'.$faire.'/'.$entryid.'-tabletag.pdf';
    $dirname = dirname($filename);
    if (!is_dir($dirname)){
      mkdir($dirname, 0755, true);
    }
    $pdf->Output($filename, 'F');
    echo $entryid;
  }else{
    if (ob_get_contents()) ob_clean();
    $pdf->Output($entryid.'-tabletag.pdf', 'I');
  }

  //error_log('after writing pdf '.date('h:i:s'),0);
}else{
    echo 'No Entry ID submitted';
}


function createOutput($entry_id,$pdf){
    $entry = GFAPI::get_entry( $entry_id );
    $project_title = (isset($entry['151']) ? filterText((string)$entry['151']) : '');

    $project_title  = preg_replace('/\v+|\\\[rn]/','<br/>',$project_title);

    // Project ID
    $pdf->SetFont('Benton Sans','',12);
    $pdf->setTextColor(168,170,172);
    $pdf->SetXY(240, 20);
    $pdf->MultiCell(115, 10, $entry_id,0,'L');

    // Project Title
    $pdf->setTextColor(0);
    $pdf->SetXY(50, 25);

    //auto adjust the font so the text will fit
    $pdf->SetFont( 'Benton Sans','B',105);
    $lineHeight = 60*0.2645833333333*1.3;

    /* Output the title at the required font size */
    $pdf->MultiCell(0, $lineHeight, $entry_id,0,'L');

    //field 22 - QRCode
    $project_photo = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.urlencode('http://www.makerfaire.com/maker/entry/'.$entry_id.'/');
    $pdf->Image($project_photo,155,70,null,null,image_type_to_extension(IMAGETYPE_PNG,false));

    //Resource data?
    $pdf->SetXY(160, 107);
    $pdf->SetFont( 'Benton Sans','',12);
    $lineHeight = 14*0.2645833333333*1.3;
    $pdf->MultiCell(0, $lineHeight, "What goes here? \nResource data?",0,'L');

    //Project Title
    $pdf->SetXY(90, 50);
    $x = 15;    // set the starting font size
    $pdf->SetFont( 'Benton Sans','B',15);

    /* Cycle thru decreasing the font size until it's width is lower than the max width */
    while( $pdf->GetStringWidth( utf8_decode( $project_title)) > 400 ){
        $x--;   // Decrease the variable which holds the font size
        $pdf->SetFont( 'Benton Sans','B',$x);
    }
    $lineHeight = $x*0.2645833333333*1.3;

    /* Output the title at the required font size */
    $pdf->MultiCell(0, $lineHeight, $project_title,0,'L');


     

}

function filterText($text)
{


	$string = iconv('UTF-8', 'windows-1252',$text);

	//now translate any unicode stuff...
	$conv = array(
      "&amp;" => '&');
	return strtr($string, $conv);


}

?>