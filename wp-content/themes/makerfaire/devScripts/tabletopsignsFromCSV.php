<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. By Rio
 */
?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  </head>
  <body>

    <h2>Upload a CSV to generate Table Top Signs</h2>
    <form method="post" enctype="multipart/form-data">
        Select File to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <label for="faire">Faire Directory</label>
        <input type="text" name="faire" id="faire">
        <br />
        <input type="submit" value="Upload" name="submit">
    </form>
    
  </body>
</html>
<?php
include 'db_connect.php';

const DPI = 96;
const MM_IN_INCH = 25.4;

if (isset($_POST["submit"]) ) {

    // require FPDF
    require_once ('../generate_pdf/fpdf/fpdf.php');

    class PDF extends FPDF{
        // Page header
        function Header(){
            global $root;
            $this->SetFont('Benton Sans','B',15);
        }
    }

    $csv = [];
    if ( isset($_FILES["fileToUpload"])) {
        //if there was an error uploading the file
        if ($_FILES["fileToUpload"]["error"] > 0) {
            echo "Return Code: " . $_FILES["fileToUpload"]["error"] . "<br />";
        } else {
            //save the file
            $target_dir = "uploads/";
            if(!file_exists($target_dir)){
                mkdir("uploads/", 0777);
            }
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]).date('dmyhi');

            $name = $_FILES['fileToUpload']['name'];
            $nameArr = explode('.', $name);

            $ext = strtolower(end($nameArr));

            $type = $_FILES['fileToUpload']['type'];
            $tmpName = $_FILES['fileToUpload']['tmp_name'];

            //Print File Details
            echo "<div style='padding:10px;border-radius:10px;border:solid 1px #333;margin:15px;background:#f1f1f1;'>";
            echo "Upload: "    . $name . "<br />";
            echo "Type: "      . $type . "<br />";
            echo "Size: "      . ($_FILES["fileToUpload"]["size"] / 1024) . " Kb<br />";
            echo "Temp file: " . $tmpName . "<br />";
            //Save file to server
            //if file already exists
            $savedFile = "/dataUpload/upload/" . $name;
            $savedFile = $target_file;
            if (file_exists($savedFile)) {
                echo $name . " already exists. ";
            }else {
                if ($_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
                    //Store file in directory
                    if( move_uploaded_file($tmpName, $savedFile) ) {
                    echo "Stored in: " . $savedFile . "<br />";
                    } else {
                    echo "Not uploaded<br/>";
                    }
                }
            }
            echo "</div>";

            $rows   = array_map('str_getcsv', file($savedFile));
            $header = array_shift($rows);
            foreach($rows as $row) {
                $csv[] = array_combine($header, $row);
            }
        }
    } else {
        echo "No file selected <br />";
    }


  foreach ($csv as $rowData){
    echo '<pre>';
    print_r($rowData);
    echo '</pre>';
    // Instanciation of inherited class
    try {
        $pdf = new PDF();
        $pdf->AddFont('Benton Sans', 'B', 'bentonsans-bold-webfont.php');
        $pdf->AddFont('Benton Sans', '', 'bentonsans-regular-webfont.php');
        $pdf->AddPage('L',array(139.7,215.9));
        $pdf->SetFont('Benton Sans', '', 12);
        $pdf->SetMargins(3,3);
        $pdf->SetFillColor(255,255,255);
        
        // get the Project name to use as a slug, if one isn't set return an error
        $booth_slug = $rowData["Zone"] . "_" . str_replace(' ', '-', strtolower($rowData['Area'])) . "_" . str_replace(' ', '-', strtolower($rowData['Project Name']));

        if (isset($booth_slug) && $booth_slug != '') {
            $faire = $_POST['faire'];

            $file = createOutput($rowData, $pdf);

            // error_log("Resize Image: $resizeImage for $booth_slug");

            $validFile = get_template_directory() . '/signs/'.$faire.'/tabletags/' . $booth_slug . '.pdf';
            $errorFile = get_template_directory() . '/signs/'.$faire.'/tabletags/error/' . $booth_slug . '.pdf';

            if ($file) {
                $filename = $validFile;
                // If the file exists in the error log - delete it  
                if (file_exists($errorFile)) {
                    unlink(realpath($errorFile));
                }
            } else {
                $filename = $errorFile;
                // If the file exists in the regular path - delete it    
                if (file_exists($validFile)) {
                    unlink(realpath($validFile));
                }
            }

            $dirname = dirname($filename);
            
            if (!is_dir($dirname)) {
                error_log("directory");
                mkdir($dirname, 0755, true);
            }
            if (ob_get_contents())
                ob_clean();
            $pdf->Output($filename, 'F');

            //error_log('after writing pdf '.date('h:i:s'),0);
        } else {
            echo 'No Booth Slug provided';
        }
            
    } catch (Exception $e) {
        error_log("Unable to create PDF due to: " . $e);
    }
  }

  exit();
}



function createOutput($rowData, $pdf) {

    $project_title = $rowData["Project Name"];
    $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);
    $zone = $rowData["Zone"];
    $subarea = $rowData["Area"];
    $booth = $rowData["Booth Name"];
    $chairs = $rowData["Number of Chairs"] ? $rowData["Number of Chairs"] : 0;
    $tables = $rowData["Number of Tables"] ? $rowData["Number of Tables"] : 0;
    $elec_120V = $rowData["Elec_120V"] ? $rowData["Elec_120V"] : 0;
    $other_resources = $rowData["Exhibitor Resources"] ? true : ($rowData["Sponsor Order Payment"] ? true : false);

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

    $pdf->SetXY(4, 15);
    $pdf->SetFont( 'Benton Sans','B',72);
    $lineHeight = 14*0.2645833333333*1.3;
    $pdf->Cell( 0, 10, $booth, 0, 0, 'C' );

    $location = "Zone: " . $zone . " - Area: " . $subarea;
    $pdf->SetXY(4, 35);
    $pdf->SetFont( 'Benton Sans','B',24);
    $lineHeight = 14*0.2645833333333*1.3;
    $pdf->Cell( 0, 10, $location, 0, 0, 'C' );

    //Resource information
    $pdf->SetXY(100, 87);
    $pdf->SetFont('Benton Sans','',14);
    $lineHeight = 15*0.2645833333333*1.3;

    $resources = "Basic Resources:";
    $resources = "Chairs - " . $chairs . "\n";
    $resources .= "Tables - " . $tables . "\n";
    $resources .= "Elec 120V - " . $elec_120V . "\n";

    $pdf->MultiCell(0, $lineHeight, $resources,0,'R');

    if($other_resources == true) {
        $pdf->SetXY(100, 107);
        $pdf->SetFont( 'Benton Sans','B',14);
        //$pdf->SetTextColor(255, 0, 0);
        $lineHeight = 14*0.2645833333333*1.3;
        $pdf->Cell( 0, 10, "See Reports for additional resources needed", 0, 0, 'R' );
    }
    
    $file = $zone . "_" . str_replace(' ', '-', strtolower($subarea)) . "_" . str_replace(' ', '-', strtolower($project_title));
    return $file;
}

function filterText($text){
	$string = iconv('UTF-8', 'windows-1252',$text);

	//now translate any unicode stuff...
	$conv = array(
      "&amp;" => '&');
	return strtr($string, $conv);
}