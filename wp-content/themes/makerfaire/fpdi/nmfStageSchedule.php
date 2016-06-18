
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
    global $root;
    $faire = (isset($_GET['faire']) && $_GET['faire'] != '' ? $_GET['faire'] . '-' : '');
    $image = $root . '/wp-content/themes/makerfaire/images/' . $faire . 'StageSchedule.png';
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
if (isset($_GET['stage']) && $_GET['stage'] != '') {
  $faire = (isset($_GET['faire']) && $_GET['faire'] != '' ? $_GET['faire'] : '');
  $stage = (isset($_GET['stage']) && $_GET['stage'] != '' ? $_GET['stage'] : '');
  $stagename = (isset($_GET['stagename']) && $_GET['stagename'] != '' ? $_GET['stagename'] : $stage);
  $stageday = (isset($_GET['stageday']) && $_GET['stageday'] != '' ? $_GET['stageday'] : '');
  createOutput($faire, $stage, $stageday, $stagename, $pdf);
  if (isset($_GET['type']) && $_GET['type'] == 'download') {
    if (ob_get_contents())
      ob_clean();
    $pdf->Output($stage . '.pdf', 'D');
  }elseif (isset($_GET['type']) && $_GET['type'] == 'save') {
    $filename = TEMPLATEPATH . '/signs/' . $faire . '/' . $stage .'-'.$stageday. '.pdf';
    $dirname = dirname($filename);
    if (!is_dir($dirname)) {
      mkdir($dirname, 0755, true);
    }
    $pdf->Output($filename, 'F');
    echo $entryid;
  } else {
    if (ob_get_contents())
      ob_clean();
    $pdf->Output($stage .'-'.$stageday. '.pdf', 'I');
  }

  //error_log('after writing pdf '.date('h:i:s'),0);
}else {
  echo 'No Stage submitted';
}

function createOutput($faire, $stage, $stageday, $stagename, $pdf) {
  //$entry = GFAPI::get_entry( $entry_id );
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $select_query = sprintf("SELECT TIME_FORMAT(`wp_mf_schedule`.`start_dt`,'%%l:%%i %%p') as stagetime
      ,(select group_concat( distinct CONCAT(`First Name`,' ',`Last Name`) separator ', ') as Makers 
                         from wp_mf_maker_to_entity maker_to_entity,
							wp_mf_maker
                         where wp_mf_entity.lead_id               = maker_to_entity.entity_id AND 
								wp_mf_maker.maker_id = maker_to_entity.maker_id AND
                               maker_to_entity.maker_type  != 'Contact' 
                         group by maker_to_entity.entity_id
                        ) as stagemakers,
                                         wp_mf_entity.presentation_title as stagetitle
                                FROM `wp_mf_schedule`, 
									  wp_mf_entity,
                                     `wp_mf_location`,                                                                            
                                      wp_mf_faire_area, 
                                      wp_mf_faire_subarea  
                                WHERE  `wp_mf_schedule`.faire = '$faire' and 
                                  wp_mf_faire_subarea.subarea = '$stage' and
									 DAYNAME(`wp_mf_schedule`.`start_dt`) = '$stageday' and
                                     wp_mf_schedule.entry_id = wp_mf_entity.lead_id AND 
                                     wp_mf_schedule.location_id = wp_mf_location.ID AND 
                                        wp_mf_faire_subarea.ID  = wp_mf_location.subarea_id AND
                                         wp_mf_faire_area.ID     = wp_mf_faire_subarea.area_id  
                                         order by `wp_mf_schedule`.`start_dt` ");
  $mysqli->query("SET NAMES 'utf8'");
  $result = $mysqli->query($select_query) or trigger_error($mysqli->error . "[$select_query]");
  $sign_body='';
 
  /* if (isset($entry['160.3']) && strlen($entry['160.3']) > 0) $makers[] = filterText($entry['160.3'] . ' ' .$entry['160.6']);
    if (isset($entry['158.3']) && strlen($entry['158.3']) > 0) $makers[] = filterText($entry['158.3'] . ' ' .$entry['158.6']);
    if (isset($entry['155.3']) && strlen($entry['155.3']) > 0) $makers[] = filterText($entry['155.3'] . ' ' .$entry['155.6']);
    if (isset($entry['156.3']) && strlen($entry['156.3']) > 0) $makers[] = filterText($entry['156.3'] . ' ' .$entry['156.6']);
    if (isset($entry['157.3']) && strlen($entry['157.3']) > 0) $makers[] = filterText($entry['157.3'] . ' ' .$entry['157.6']);
    if (isset($entry['159.3']) && strlen($entry['159.3']) > 0) $makers[] = filterText($entry['159.3'] . ' ' .$entry['159.6']);
    if (isset($entry['154.3']) && strlen($entry['154.3']) > 0) $makers[] = filterText($entry['154.3'] . ' ' .$entry['154.6']);

    //maker 1 bio
    $bio = (isset($entry['234']) ? filterText($entry['234']) : '');

    $groupname = (isset($entry['109']) ? filterText($entry['109']) : '');
    $groupbio = (isset($entry['110']) ? filterText($entry['110']) : '');

    $project_photo = (isset($entry['22']) ? $entry['22'] : '');
    $project_short = (isset($entry['16']) ? filterText($entry['16']) : '');
    $project_title = (isset($entry['151']) ? filterText((string)$entry['151']) : '');

    $project_title  = preg_replace('/\v+|\\\[rn]/','<br/>',$project_title);

    // Project ID
    $pdf->SetFont('Benton Sans','',12);
    $pdf->setTextColor(168,170,172);
    $pdf->SetXY(240, 20);
    $pdf->MultiCell(115, 10, $entry_id,0,'L');
   */
  // Project Title
  $pdf->setTextColor(0, 159, 219);
  $pdf->SetXY(38, 58);

  //auto adjust the font so the text will fit
  $x = 36;    // set the starting font size
  $pdf->SetFont('Benton Sans', 'B', 36);

  /* Cycle thru decreasing the font size until it's width is lower than the max width */
  while ($pdf->GetStringWidth(utf8_decode($stagename)) > 120) {
    $x--;   // Decrease the variable which holds the font size
    $pdf->SetFont('Benton Sans', 'B', $x);
    $pdf->SetXY(38, 60);
  }
  $lineHeight = $x * 0.2645833333333 * 1.3;


  /* Output the title at the required font size */
  $pdf->MultiCell(120, $lineHeight, $stagename, 0, 'C');

  //field 16 - short description
  //auto adjust the font so the text will fit
  $pdf->setTextColor(255, 255, 255);
  $pdf->SetXY(20, 100);


  //auto adjust the font so the text will fit
  $sx = 20;    // set the starting font size
  $pdf->SetFont('Benton Sans', '', $sx);
  
  
  // Cycle thru decreasing the font size until it's width is lower than the max width
 /* while ($pdf->GetStringWidth(utf8_decode($sign_body)) > 1300) {
    $sx--;   // Decrease the variable which holds the font size
    $pdf->SetFont('Benton Sans', '', $sx);
  }
*/
  $lineHeight = $sx * 0.2645833333333 * 1.3;
	while ( $row = $result->fetch_array(MYSQLI_ASSOC)  ) {
      $pdf->SetX(20);
      $x = $pdf->GetX();
      $y = $pdf->GetY();

     //$pdf->MultiCell(0, $lineHeight, filterText($row['stagerow']), 0, 'L');
      $pdf->SetFont('Benton Sans', 'B', 28);

     $pdf->MultiCell(50, $lineHeight, filterText($row['stagetime']), 0, 'L');
     $pdf->SetXY($x + 50, $y);
     $pdf->SetFont('Benton Sans', 'B', 20);

     $pdf->MultiCell(190, $lineHeight, filterText($row['stagemakers']), 0, 'L');
     $y=$pdf->GetY();
     $pdf->SetXY($x + 50, $y);
     $pdf->SetFont('Benton Sans', '', 18);

     $pdf->MultiCell(190, $lineHeight, filterText($row['stagetitle']), 0, 'L');
     $pdf->Ln();
 }
 

}

function filterText($text) {


  $string = iconv('UTF-8', 'windows-1252', $text);

  //now translate any unicode stuff...
  $conv = array(
      "&amp;" => '&',
      "&quot;" => "'",
      "\t" => "     ");
  return strtr($string, $conv);
}

?>