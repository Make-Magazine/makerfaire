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
    $this->SetFont('Benton Sans','B',15);
  }
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AddFont('Benton Sans','B', 'bentonsans-bold-webfont.php');
$pdf->AddFont('Benton Sans','', 'bentonsans-regular-webfont.php');
$pdf->AddPage('L',array(139.7,215.9));
$pdf->SetFont('Benton Sans','',12);
$pdf->SetMargins(3,3);
$pdf->SetFillColor(255,255,255);

$pdf->SetAutoPageBreak(false);
//get the entry-id, if one isn't set return an error
if(isset($_GET['eid']) && $_GET['eid']!=''){
  $faire = (isset($_GET['faire']) && $_GET['faire']!='' ? $_GET['faire']:'');
  $entryid = sanitize_text_field($_GET['eid']);
  $fileName = createOutput($entryid, $pdf);
  if(isset($_GET['type']) && $_GET['type']=='download'){
    if (ob_get_contents()) ob_clean();
    $pdf->Output($entryid.'-tabletag.pdf', 'D');
  }elseif(isset($_GET['type']) && $_GET['type'] == 'save'){
    global $locName; global $area;
    //file name is zone-location-entryid
    $filename = TEMPLATEPATH.'/tabletags/'.$faire.'/'.$fileName;

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

    //entry ID
    $pdf->setTextColor(0);
    $pdf->SetXY(2, 27);
    $pdf->SetFont( 'Benton Sans','B',174);
    $lineHeight = 60*0.2645833333333*1.3;

    /* Output the entry ID at the required font size */
    $pdf->MultiCell(0, $lineHeight, $entry_id,0,'C');


    //Project Title
    $project_title = (isset($entry['151']) ? filterText((string)$entry['151']) : '');
    $project_title  = preg_replace('/\v+|\\\[rn]/','<br/>',$project_title);
    $pdf->SetXY(4, 62);
    $x = 25;    // set the starting font size
    $pdf->SetFont( 'Benton Sans','B',25);

    /* Cycle thru decreasing the font size until it's width is lower than the max width */
    while( $pdf->GetStringWidth( utf8_decode( $project_title)) > 400 ){
        $x--;   // Decrease the variable which holds the font size
        $pdf->SetFont( 'Benton Sans','B',$x);
    }
    $lineHeight = $x*0.2645833333333*1.4;

    /* Output the title at the required font size */
    $pdf->MultiCell(0, $lineHeight, $project_title,0,'C');


    //field 22 - QRCode
    $token=base64_encode($entry_id);
    $onsitecheckinurl='http://makerfaire.com/onsitecheckin/'.$token.'/';
    $project_photo = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.urlencode($onsitecheckinurl);
    $pdf->Image($project_photo,7,80,null,null,image_type_to_extension(IMAGETYPE_PNG,false));

    //qr text
    $pdf->SetXY(10, 120);
    $pdf->SetFont( 'Benton Sans','',12);
    $lineHeight = 13*0.2645833333333*1.3;
    $qrText = "Want to be found on the Maker Faire mobile app?\nScan this code at your booth location and follow the prompts.";
    $pdf->MultiCell(0, $lineHeight, $qrText,0,'L');

    // Location Info
    /* retSubAreaByEntry: function to return location information by entry id
     * returned as array keyed by location id
     * fields: area, subarea, nicename, location, start_dt and end_dt
     */
    $locTable = retSubAreaByEntry($entry_id);
    $disp = '';
    $fileName = $area = $locName = '';
    foreach($locTable as $location){
      $area    = $location['area'];
      $locName = ($location['location']!=''?$location['location']:($location['nicename']!=''?$location['nicename']:$location['subarea']));
      $disp   = $location['area'].': '.($location['nicename']!=''?$location['nicename']:$location['subarea']).': '.$location['location'];
    }

    if($area!='')     $fileName .= $area .'-';
    if($locName!='')  $fileName .= $locName .'-';
    $fileName .= $entry_id.'.pdf';

    $pdf->SetXY(100, 80);
    $pdf->SetFont( 'Benton Sans','B',14);
    $lineHeight = 14*0.2645833333333*1.3;
    $pdf->Cell( 0, 10, $disp, 0, 0, 'R' );

    //Resource information
    $pdf->SetXY(100, 87);
    $pdf->SetFont('Benton Sans','',14);
    $lineHeight = 15*0.2645833333333*1.3;
    /* Return array of resource information for lead*/
    $entResources = retResByEntry($entry_id);
    $eightFt = 0;
    $sixFt   = 0;
    $chairs  = 0;
    $elect   = 0;

    $disp = "";
    //loop thru and display chairs, table and electrical
    foreach($entResources as $resource){
      $disp .= $resource['item'] . ' - ' .$resource['type'].': '. $resource['qty']."\n";
    }
    $pdf->MultiCell(0, $lineHeight, $disp,0,'R');
    return $fileName;
}

function filterText($text){
	$string = iconv('UTF-8', 'windows-1252',$text);

	//now translate any unicode stuff...
	$conv = array(
      "&amp;" => '&');
	return strtr($string, $conv);
}

?>