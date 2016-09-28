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
//error_log('start of makersigns.php '.date('h:i:s'),0);
// require tFPDF


require_once('fpdf/fpdf.php');

class PDF extends FPDF {

  // Page header
  function Header() {
    // Header required when using restful structures for Chrome
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
    } else if (isset($_GET['faire']) && $_GET['faire']!='') {
      $faire = $_GET['faire'];
    }

    $image = $root . '/wp-content/themes/makerfaire/images/' . $faire . '-maker_sign.jpg';
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
$pdf->AddPage('P', array(279.4, 431.8));
$pdf->SetFont('Benton Sans', '', 12);
$pdf->SetFillColor(255, 255, 255);

//get the entry-id, if one isn't set return an error
$eid = '';
if (isset($wp_query->query_vars['eid'])) {
  $eid = $wp_query->query_vars['eid'];
} else if (isset($_GET['eid']) && $_GET['eid']!='') {
  $eid = $_GET['eid'];
}

if (isset($eid) && $eid != '') {
  $faire = '';
  if (isset($wp_query->query_vars['faire'])) {
    $faire = $wp_query->query_vars['faire'];
  } else if (isset($_GET['faire']) && $_GET['faire']!='') {
    $faire = $_GET['faire'];
  }
  $entryid = sanitize_text_field($eid);
  createOutput($entryid, $pdf);
  if (isset($_GET['type']) && $_GET['type'] == 'download') {
    if (ob_get_contents())
      ob_clean();
    $pdf->Output($entryid . '.pdf', 'D');
  }elseif (isset($_GET['type']) && $_GET['type'] == 'save') {
    $filename = TEMPLATEPATH . '/signs/' . $faire . '/maker/' . $entryid . '.pdf';
    $dirname = dirname($filename);
    if (!is_dir($dirname)) {
      mkdir($dirname, 0755, true);
    }
    if (ob_get_contents())
      ob_clean();
    $pdf->Output($filename, 'F');
    //needed for faire signs mass creation
    echo $entryid;

    exit;
  }else {
    if (ob_get_contents())
      ob_clean();
    $pdf->Output($entryid . '.pdf', 'I');
  }

  //error_log('after writing pdf '.date('h:i:s'),0);
}else {
  echo 'No Entry ID submitted';
}

function createOutput($entry_id, $pdf) {
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

  //maker 1 bio
  $bio = (isset($entry['234']) ? filterText($entry['234']) : '');

  $groupname = (isset($entry['109']) ? filterText($entry['109']) : '');
  $groupbio = (isset($entry['110']) ? filterText($entry['110']) : '');

  $project_photo = (isset($entry['22']) ? $entry['22'] : '');
  $project_short = (isset($entry['16']) ? filterText($entry['16']) : '');
  $project_title = (isset($entry['151']) ? filterText((string) $entry['151']) : '');

  $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);

  // Project ID
  $pdf->SetFont('Benton Sans', '', 12);
  $pdf->setTextColor(168, 170, 172);
  $pdf->SetXY(240, 20);
  $pdf->MultiCell(115, 10, $entry_id, 0, 'L');

  // Project Title
  $pdf->setTextColor(0);
  $pdf->SetXY(12, 75);

  //auto adjust the font so the text will fit
  $x = 65;    // set the starting font size
  $pdf->SetFont('Benton Sans', 'B', 65);

  /* Cycle thru decreasing the font size until it's width is lower than the max width */
  while ($pdf->GetStringWidth(utf8_decode($project_title)) > 400) {
    $x--;   // Decrease the variable which holds the font size
    $pdf->SetFont('Benton Sans', 'B', $x);
  }
  $lineHeight = $x * 0.2645833333333 * 1.3;


  /* Output the title at the required font size */
  $pdf->MultiCell(0, $lineHeight, $project_title, 0, 'L');

  //field 16 - short description
  //auto adjust the font so the text will fit
  $pdf->SetXY(145, 135);


  //auto adjust the font so the text will fit
  $sx = 28;    // set the starting font size
  $pdf->SetFont('Benton Sans', '', $sx);

  // Cycle thru decreasing the font size until it's width is lower than the max width
  while ($pdf->GetStringWidth(utf8_decode($project_short)) > 1300) {
    $sx--;   // Decrease the variable which holds the font size
    $pdf->SetFont('Benton Sans', '', $sx);
  }

  $lineHeight = $sx * 0.2645833333333 * 1.3;

  $pdf->MultiCell(125, $lineHeight, $project_short, 0, 'L');

  //field 22 - project photo
  if ($project_photo != '') {
    $photo_extension = exif_imagetype($project_photo);
    if ($photo_extension) {
      //DEBUG:
      $project_photo = legacy_get_fit_remote_image_url($project_photo, 450, 450, 0);
      $pdf->Image($project_photo, 12, 135, null, null, image_type_to_extension($photo_extension, false));
    }
  }
  //print white box to overlay long descriptions or photos
  /* $pdf->SetXY(10, 255);
    $pdf->Cell(300,80,'',0,2,'L',true); */


  //maker info, use a background of white to overlay any long images or text
  $pdf->setTextColor(0, 174, 239);
  $pdf->SetFont('Benton Sans', 'B', 48);

  $pdf->SetXY(10, 270);
  if (!empty($groupbio)) {
    //auto adjust the font so the text will fit
    $sx = 48;    // set the starting font size
    // Cycle thru decreasing the font size until it's width is lower than the max width
    while ($pdf->GetStringWidth(utf8_decode($groupname)) > 240) {
      $sx--;   // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', 'B', $sx);
    }

    $lineHeight = $sx * 0.2645833333333 * 1.3;

    $pdf->MultiCell(0, $lineHeight, $groupname, 0, 'L', true);

    $pdf->setTextColor(0);
    $pdf->SetFont('Benton Sans', '', 24);

    //auto adjust the font so the text will fit
    $x = 24;    // set the starting font size

    /* Cycle thru decreasing the font size until it's width is lower than the max width */
    while ($pdf->GetStringWidth($groupbio) > 1200) {
      $x--;   // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', '', $x);
    }
    $lineHeight = $x * 0.2645833333333 * 1.3;
    $pdf->MultiCell(0, $lineHeight, $groupbio, 0, 'L', true);
  } else {
    $makerList = implode(', ', $makers);
    $pdf->SetFont('Benton Sans', 'B', 48);

    //auto adjust the font so the text will fit
    $x = 48;    // set the starting font size

    /* Cycle thru decreasing the font size until it's width is lower than the max width */
    while ($pdf->GetStringWidth(utf8_decode($makerList)) > 900) {
      $x--;   // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', '', $x);
    }
    $lineHeight = $x * 0.2645833333333 * 1.3;
    $pdf->MultiCell(0, $lineHeight, $makerList, 0, 'L', true);
    //if size of makers is 1, then display maker bio
    if (sizeof($makers) == 1) {
      $pdf->setTextColor(0);
      $pdf->SetFont('Benton Sans', '', 24);

      //auto adjust the font so the text will fit
      $x = 24;    // set the starting font size
      /* Cycle thru decreasing the font size until it's width is lower than the max width */
      while ($pdf->GetStringWidth($bio) > 900) {
        $x--;   // Decrease the variable which holds the font size
        $pdf->SetFont('Benton Sans', '', $x);
      }

      $lineHeight = $x * 0.2645833333333 * 1.37;
      $pdf->MultiCell(0, $lineHeight, $bio, 0, 'L', true);
    }
  }
}

function filterText($text) {


  $string = iconv('UTF-8', 'windows-1252', $text);

  //now translate any unicode stuff...
  $conv = array(
      "&amp;" => '&');
  return strtr($string, $conv);
}

?>