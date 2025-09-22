<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  </head>
  <body>

    <h2>Upload a CSV to generate Maker Signs</h2>
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
//image sizes
const MAX_WIDTH = 450;
const MAX_HEIGHT = 450;

if (isset($_POST["submit"]) ) {

    // require FPDF
    require_once ('../generate_pdf/fpdf/fpdf.php');
    // require clipping
    require ('../generate_pdf/fpdf/clipping.php');
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

            $rows = [];
            if (($handle = fopen($savedFile, "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
            $header = array_shift($rows);
            //error_log(print_r($rows, TRUE));
            //error_log(print_r($header, TRUE));
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
                $pdf = new PDF_Clipping();;
                $pdf->AddFont('Benton Sans', 'B', 'bentonsans-bold-webfont.php');
                $pdf->AddFont('Benton Sans', '', 'bentonsans-regular-webfont.php');
                $pdf->AddFont('FontAwesome1','','FontAwesome47-P1.php'); // https://drive.google.com/file/d/1Y3NlxBZtXPcFUIwiLeQWzdzdoSe9f6xo/view?pli=1
                $pdf->AddFont('FontAwesome2','','FontAwesome47-P2.php'); // https://drive.google.com/file/d/1XjjEyhkcD0mO6FTf0w9XHB4bwjMCL2ij/view
                $pdf->AddFont('FontAwesome3','','FontAwesome47-P3.php'); // https://drive.google.com/file/d/10WBuA63DMbNPRWjKSKJpVSk4I1OPwh2R/view
                $pdf->AddFont('FontAwesome4','','FontAwesome47-P4.php'); // https://drive.google.com/file/d/1lPeh5IGXY8Re6nNXEU7i0Wf63o97Svx0/view
                $pdf->AddPage('P', array(381, 381));
                $pdf->SetFont('Benton Sans', '', 12);
                $pdf->Image('../generate_pdf/pdf_layouts/signBackground2025.png', 0, 0, 381, 381); // background image
                
                $pdf->SetMargins(20,139,22); //left, top, right

                
                // get the Project name to use as a slug, if one isn't set return an error
                $booth_slug = str_replace(array('/', ' '), '-', strtolower($rowData['Project Name']));
                $project_area = str_replace(array('/', ' '), '-', strtolower($rowData["Area"]));
                $project_zone = str_replace(array('/', ' '), '-', strtolower($rowData["Zone"]));

                if (isset($booth_slug) && $booth_slug != '') {
                    $faire = $_POST['faire'];

                    $resizeImage = createOutput($rowData, $pdf);

                    // error_log("Resize Image: $resizeImage for $booth_slug");

                    $validFile = get_template_directory() . '/signs/' . $faire . '/maker/' . $project_zone . "_" . $project_area . "_" . $booth_slug . '.pdf';
                    $errorFile = get_template_directory() . '/signs/' . $faire . '/maker/error/' . $project_zone . "_" . $project_area . "_" .$booth_slug . '.pdf';

                    if ($resizeImage) {
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
                    echo 'No Booth Slug submitted';
                }
                    
            } catch (Exception $e) {
                error_log("Unable to create PDF due to: " . $e);
            }
        }
     
    }

    exit();




function createOutput($rowData, $pdf) {
    // Initialize the variable that the image was resized
    $resizeImage = 1;

    $project_image = $rowData["Primary Project Photo"];
    $project_title = $rowData["Project Name"];
    $project_short = $rowData["Exhibit Description (to appear publicly on the website)"];
    $project_title = preg_replace('/\v+|\\\[rn]/', '<br/>', $project_title);
    $project_category = $rowData["Pick the category that best fits your project."];
    $project_subarea = $rowData["Area"];
    $project_booth = $rowData["Booth Name"];
    $project_code = $rowData["BackstageExhibitorID"];
    $project_id = $rowData["Unified Form ID"];
    $name = !empty($rowData["Group Name*"]) ? $rowData["Group Name*"] : $rowData["Name"];

    /*$maker_photo = !empty($rowData["Your Photo"]) ? $rowData["Your Photo"] : $rowData["Group Photo"];
    if(!empty($maker_photo)) {
        $maker_photo = get_template_directory().'/images/default-makey-medium.png';
    }*/

   /***************************************************************************
    * Project Title
    * auto adjust the font so the text will fit
    ***************************************************************************/
   $pdf->setTextColor(245, 73, 39);
   $pdf->SetXY(20, 40);

   // auto adjust the font so the text will fit
   $x = 72; // set the starting font size
   $pdf->SetFont('Benton Sans', 'B', 72);

   /* Cycle thru decreasing the font size until it's width is lower than the max width */
   /*while ($pdf->GetStringWidth(utf8_decode($project_title)) > 410) {
      $x = $x-.1; // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', 'B', $x);
   } */
   $lineHeight = $x * 0.2645833333333 * 1.5;

   /* Output the title at the required font size */
   $pdf->MultiCell(340, $lineHeight, strtoupper($project_title), 0, 'C');

    /***************************************************************************
    * Maker / Group Name
    * auto adjust the font so the text will fit
    ***************************************************************************/
   $pdf->setTextColor(0, 0, 0);
   $pdf->SetXY(21, 140);

   // auto adjust the font so the text will fit
   $x = 52; // set the starting font size
   $pdf->SetFont('Benton Sans', '', $x);

   /* Cycle thru decreasing the font size until it's width is lower than the max width */
   /*while ($pdf->GetStringWidth(utf8_decode($project_title)) > 410) {
      $x = $x-.1; // Decrease the variable which holds the font size
      $pdf->SetFont('Benton Sans', 'B', $x);
   } */
   $lineHeight = $x * 0.2645833333333 * 1.5;

   /* Output the title at the required font size */
   //$name = str_replace(" ", "\n", strtoupper($name));
   //$pdf->MultiCell(340, $lineHeight, $name, 0, 'L');

    /***************************************************************************
    * short description
    * auto adjust the font so the text will fit
    ***************************************************************************/   
    /*$pdf->SetXY(16, 340);
    $pdf->setTextColor(51, 51, 51);

    // auto adjust the font so the text will fit
    $sx = 24; // set the starting font size
    $pdf->SetFont('Benton Sans', '', $sx);
 
    // Cycle thru decreasing the font size until it's width is lower than the max width
    /* while ($pdf->GetStringWidth(utf8_decode($project_short)) > 1500) {
       $sx = $sx - .1; // Decrease the variable which holds the font size
       $pdf->SetFont('Benton Sans', '', $sx);
    }*/
 
    //$lineHeight = $sx * 0.2645833333333 * 1.8;
 
    // the last parameter here will limit the amount of lines and end with an ellipsis
    //$pdf->MultiCell(250, $lineHeight, $project_short, 0, 'L', false, 6);

   /***************************************************************************
    * Location / Booth    
    ***************************************************************************/
    //$pdf->setTextColor(245, 20, 0);
    //$pdf->SetFont('FontAwesome4', '', 26);
    //$pdf->Text(21, 267, chr(0x003D));
    $pdf->SetXY(21, 237);
    $pdf->setTextColor(255, 255, 255);
    $pdf->SetFont('Benton Sans', '', 42);
    $lineHeight = 42 * 0.2645833333333 * 1.5;
    $pdf->MultiCell(340, $lineHeight, str_replace("_", "\n", strtoupper($project_subarea)), 100, "L");
    //$pdf->setTextColor(245, 20, 0);
    //$pdf->SetFont('Benton Sans', '', 42);
    //$pdf->Text(21, 267, $project_booth); // no longer showing booth

    /***************************************************************************
    * Type  
    **************************************************************************
    $pdf->setTextColor(245, 20, 0);
    $pdf->SetFont('FontAwesome1', '', 26);
    $pdf->Text(18, 310, chr(0x0031));
    $pdf->setTextColor(51, 51, 51);
    $pdf->SetFont('Benton Sans', '', 26);
    $pdf->Text(32, 310, $project_type);*/

    /***************************************************************************
    * Category  
    ***************************************************************************/
    /*$pdf->setTextColor(245, 20, 0);
    $pdf->SetFont('FontAwesome2', '', 26);
    $pdf->Text(18, 325, chr(0x0078));
    $pdf->setTextColor(51, 51, 51);
    $pdf->SetFont('Benton Sans', '', 26);
    $pdf->Text(32, 325, $project_category);*/
 
     
   /***************************************************************************
    * QR code    
    ***************************************************************************/
    $pdf->setTextColor(255, 255, 255);
    $pdf->SetFont('Benton Sans', '', 33);
    $pdf->Text(21, 297, "Learn More");
    $entryURL = 'https://bayarea.makerfaire.com/#/booth/'.$project_code.'/';
    $QR_Code = 'https://quickchart.io/qr?text=' . urlencode($entryURL) . '&dark=333333&margin=5&size=150';
    $pdf->Image($QR_Code,20,305,65,null,image_type_to_extension(IMAGETYPE_PNG,false));
   

   /***************************************************************************
    * Project Code
    ***************************************************************************/
    /*$pdf->SetFont('Benton Sans', '', 18);
    $pdf->setTextColor(91, 91, 91);
    $pdf->SetXY(203, 540);
    $pdf->MultiCell(115, 15, $project_code, 0, 'L');*/
    
          
    /***************************************************************************
    * project photo
    * image should never be larger than 450x450
    ***************************************************************************/
    if ($project_image != '') {      
        $photo_extension = pathinfo($project_image, PATHINFO_EXTENSION);
        if ($photo_extension) {
            //fit image onto pdf // Zoho Access Token needs to be refreshed every hour in Postman
            addZohoImageToPDF($pdf, $project_id, "Primary_Project_Photo", "1000.0fac1aab05d9772f81ca093cd444992c.718c9f78b3207b68ae041dafc5d3e773", 257, 220, 83, 'center');
        } else {
            error_log("Unable to find the image for entry $project_title for $project_image");
            $resizeImage = 0;
        }
    } else {
        error_log("Missing image for $project_title");
        $resizeImage = 0;
    }

    $pdf->Image('../generate_pdf/pdf_layouts/mareIslandMakey.png', 265, 230, 100, 132); // branding

   /***************************************************************************
    * Maker photo
    * image should never be larger than 450x450
    ***************************************************************************/
   /*
    if ($maker_photo != '') {    
      $photo_extension = pathinfo($maker_photo, PATHINFO_EXTENSION);
      if ($photo_extension) {
         //fit image onto pdf
         $maker_photo = stripslashes($maker_photo);

         $pdf->ClippingRoundedRect(15.5,439.5,116.5,117.5,13.5,true);
         if(!empty($rowData["Group Photo"])) {
            addZohoImageToPDF($pdf, $project_id, "Group_Photo", "1000.15890933f6edace510db5cb75f7c7faa.65e10b87d3bd59df63b46343d4ab1539", 15, 439, 118);
         } else {
            addZohoImageToPDF($pdf, $project_id, "Your_Photo", "1000.15890933f6edace510db5cb75f7c7faa.65e10b87d3bd59df63b46343d4ab1539" 15, 439, 118);
         }
         //$pdf->Image($maker_photo,15,439,118,null,$photo_extension);
         
      } else {
         error_log("Unable to find the Maker Photo for entry $project_title for $maker_photo");
         $resizeImage = 0;
      }
   } else {
      error_log("Missing image for $project_title");
      $resizeImage = 0;
   }*/
   
   /***************************************************************************
    * maker info, use a background of white to overlay any long images or text
    ***************************************************************************/
   /*$pdf->setTextColor(0, 0, 0);
   $pdf->SetFont('Benton Sans', 'B', 40);

   $pdf->SetXY(50, 145.5);
   if (!empty($groupbio)) {
      // auto adjust the font so the text will fit
      $sx = 40; // set the starting font size
      // Cycle thru decreasing the font size until it's width is lower than the max width
      while ($pdf->GetStringWidth(utf8_decode($groupname)) > 450) {
         $sx = $sx - .1; // Decrease the variable which holds the font size
         $pdf->SetFont('Benton Sans', 'B', $sx);
      }

      $lineHeight = $sx * 0.2645833333333 * 1.5;

      $pdf->MultiCell(0, $lineHeight, $groupname, 0, 'L', true);

      $pdf->setTextColor(0);
      $pdf->SetFont('Benton Sans', '', 24);

      // auto adjust the font so the text will fit
      $x = 24; // set the starting font size

      // Cycle thru decreasing the font size until it's width is lower than the max width 
      while ($pdf->GetStringWidth($groupbio) > 850) {
         $x = $x -.1; // Decrease the variable which holds the font size
         $pdf->SetFont('Benton Sans', '', $x);
      }
      $lineHeight = $x * 0.2645833333333 * 1.5;
      $pdf->MultiCell(0, $lineHeight, $groupbio, 0, 'L', true);
   } else { 
      $makerList = implode(', ', $makers);
      $pdf->SetFont('Benton Sans', 'B', 38);

      // auto adjust the font so the text will fit
      $x = 50; // set the starting font size

      // Cycle thru decreasing the font size until it's width is lower than the max width 
      while ($pdf->GetStringWidth(utf8_decode($makerList)) > 450) {
         $x = $x -.1; // Decrease the variable which holds the font size
         $pdf->SetFont('Benton Sans', '', $x);
      }
      $lineHeight = $x * 0.2645833333333 * 1.5;
      $pdf->MultiCell(120, $lineHeight, strtoupper($makerList), 0, 'L', false);
      // if size of makers is 1, then display maker bio
         if (sizeof($makers) == 1) {
         $pdf->setTextColor(0);
         $pdf->SetFont('Benton Sans', '', 24);

         // auto adjust the font so the text will fit
         $x = 24; // set the starting font size
         // Cycle thru decreasing the font size until it's width is lower than the max width 
         while ($pdf->GetStringWidth($bio) > 900) {
            $x = $x-.1; // Decrease the variable which holds the font size
            $pdf->SetFont('Benton Sans', '', $x);
         }

         $lineHeight = $x * 0.2645833333333 * 1.5;
         $pdf->MultiCell(0, $lineHeight, $bio, 0, 'L', true);
      }
   //}*/
   return $resizeImage;
}

function filterText($text) {
   try {
      $string = iconv('UTF-8', 'windows-1252//IGNORE', $text);
   } catch (Exception $e) {
      error_log("Unable to convert $text due to: " + $e);
      ini_set('mbstring.substitute_character', "none");
      $string = mb_convert_encoding($text, 'UTF-8', 'windows-1252');
   }

   // now translate any unicode stuff...
   $conv = array(
       "&amp;" => "&",
       "&#039;" => "'"
   );
   return strtr($string, $conv);
}

function pixelsToMM($val) {
   return $val * MM_IN_INCH / DPI;
}

function resizeToFit($imgFilename) {
   list($width, $height) = getimagesize($imgFilename);
   
   $widthScale = MAX_WIDTH / $width;
   $heightScale = MAX_HEIGHT / $height;
   $scale = min($widthScale, $heightScale);

   return array(
       round(pixelsToMM($scale * $width)),
       round(pixelsToMM($scale * $height))
   );
}

/**
 * Add a Zoho Creator image field to a PDF using API-signed URL
 *
 * @param object $pdf        PDF object (FPDF, TCPDF, etc.)
 * @param string $recordId   Zoho Creator record ID
 * @param string $fieldName  Image field API name
 * @param float  $x          X position on PDF
 * @param float  $y          Y position on PDF
 * @param float  $w          Width on PDF (0 to auto scale)
 * @param float  $h          Height on PDF (0 to auto scale)
 * @param string $oauth      Zoho OAuth token
 */

function addZohoImageToPDF($pdf, $recordId, $fieldName, $oauth, $circleX, $circleY, $circleR, $focus = 'center') {
    // Build API URL for the record
    $api_url = "https://creator.zoho.com/api/v2/gillian_make/make-co-registrations/report/Unified_Form_Maker_Sign_Source/$recordId";

    // Fetch record metadata
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Zoho-oauthtoken $oauth"]);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if (!$response || $info['http_code'] != 200) {
        error_log("Failed to fetch Zoho record: HTTP " . $info['http_code']);
        return false;
    }

    $data = json_decode($response, true);
    //error_log(print_r($data, TRUE));
    if (!isset($data['data'][$fieldName]) || empty($data['data'][$fieldName])) {
        error_log("Image field missing or empty: $fieldName");
        return false;
    }
    $image_url = "https://creator.zoho.com" . $data['data'][$fieldName];
    //error_log($image_url);

    // Download image binary via cURL with OAuth
    $ch = curl_init($image_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Zoho-oauthtoken $oauth"]);
    $image_data = curl_exec($ch);
    $image_info = curl_getinfo($ch);
    curl_close($ch);

    if (!$image_data) {
        error_log("No image data returned from $image_url");
        return false;
    }

    // Detect actual type using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->buffer($image_data);

    switch ($mime) {
        case 'image/jpeg': $ext = 'jpg'; break;
        case 'image/png':  $ext = 'png'; break;
        case 'image/gif':  $ext = 'gif'; break;
        case 'image/webp': $ext = 'webp'; break;
        case 'image/heic': $ext = 'heic'; break;
        default:
            error_log("Unsupported image type: $mime from $image_url");
            return false;
    }

    // Save temp file
    $tmpfile = tempnam(sys_get_temp_dir(), 'zoho_img_') . "." . $ext;
    file_put_contents($tmpfile, $image_data);

    if (!file_exists($tmpfile) || filesize($tmpfile) === 0) {
        error_log("Temp image file missing/empty: $tmpfile");
        return false;
    }

    // Convert unsupported formats to PNG
    if ($ext === 'webp') {
        $im = imagecreatefromwebp($tmpfile);
        if ($im) {
            $pngFile = preg_replace('/\.webp$/', '.png', $tmpfile);
            imagepng($im, $pngFile);
            imagedestroy($im);
            unlink($tmpfile); // remove original webp
            $tmpfile = $pngFile;
            $ext = 'png';
        }
    } elseif ($ext === 'heic') {
        try {
            $imagick = new Imagick($tmpfile);
            $pngFile = preg_replace('/\.heic$/', '.png', $tmpfile);
            $imagick->setImageFormat('png');
            $imagick->writeImage($pngFile);
            $imagick->clear();
            $imagick->destroy();
            unlink($tmpfile); // remove original heic
            $tmpfile = $pngFile;
            $ext = 'png';
        } catch (Exception $e) {
            error_log("HEIC conversion failed: " . $e->getMessage());
            return false;
        }
    }

    // Place into PDF with circle crop
    try {
        list($img_w, $img_h) = getimagesize($tmpfile);

        $circle_diameter = $circleR * 2;

        // Scale to cover circle
        $scale = max($circle_diameter / $img_w, $circle_diameter / $img_h);
        $new_w = $img_w * $scale;
        $new_h = $img_h * $scale;

        // Default center offsets
        $offsetX = $circleX - ($new_w / 2);
        $offsetY = $circleY - ($new_h / 2);

        // Adjust offsets based on $focus
        switch (strtolower($focus)) {
            case 'top':
                $offsetY = $circleY - $circleR; // align top of circle
                break;
            case 'bottom':
                $offsetY = $circleY - ($new_h - $circleR); // align bottom
                break;
            case 'left':
                $offsetX = $circleX - $circleR;
                break;
            case 'right':
                $offsetX = $circleX - ($new_w - $circleR);
                break;
            case 'topleft':
                $offsetX = $circleX - $circleR;
                $offsetY = $circleY - $circleR;
                break;
            case 'topright':
                $offsetX = $circleX - ($new_w - $circleR);
                $offsetY = $circleY - $circleR;
                break;
            case 'bottomleft':
                $offsetX = $circleX - $circleR;
                $offsetY = $circleY - ($new_h - $circleR);
                break;
            case 'bottomright':
                $offsetX = $circleX - ($new_w - $circleR);
                $offsetY = $circleY - ($new_h - $circleR);
                break;
            case 'center':
            default:
                // already centered
                break;
        }

        // Clip to circle and draw
        $pdf->ClippingCircle($circleX, $circleY, $circleR, false);
        $pdf->Image($tmpfile, $offsetX, $offsetY, $new_w, $new_h, strtoupper($ext));
        $pdf->UnsetClipping();

    } catch (Exception $e) {
        error_log("FPDF failed to add image: " . $e->getMessage());
        unlink($tmpfile);
        return false;
    }

    unlink($tmpfile);
    return true;
}


function cropImageToBox($sourceFile, $targetWidth, $targetHeight) {
    // Load image
    $info = getimagesize($sourceFile);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
        case 'image/jpg':
            $img = imagecreatefromjpeg($sourceFile);
            break;
        case 'image/png':
            $img = imagecreatefrompng($sourceFile);
            break;
        case 'image/gif':
            $img = imagecreatefromgif($sourceFile);
            break;
        case 'image/webp':
            $img = imagecreatefromwebp($sourceFile);
            break;
        default:
            return false;
    }

    $origWidth = imagesx($img);
    $origHeight = imagesy($img);

    // Compute aspect ratios
    $srcRatio = $origWidth / $origHeight;
    $targetRatio = $targetWidth / $targetHeight;

    if ($srcRatio > $targetRatio) {
        // Source is wider → crop width
        $newHeight = $origHeight;
        $newWidth = $origHeight * $targetRatio;
        $srcX = ($origWidth - $newWidth) / 2;
        $srcY = 0;
    } else {
        // Source is taller → crop height
        $newWidth = $origWidth;
        $newHeight = $origWidth / $targetRatio;
        $srcX = 0;
        $srcY = ($origHeight - $newHeight) / 2;
    }

    // Create new blank image
    $dst = imagecreatetruecolor($targetWidth, $targetHeight);

    // Preserve transparency for PNG/GIF/WebP
    if ($mime === 'image/png' || $mime === 'image/gif' || $mime === 'image/webp') {
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    // Copy cropped and resize
    imagecopyresampled($dst, $img, 0, 0, $srcX, $srcY, $targetWidth, $targetHeight, $newWidth, $newHeight);

    // Save to temp file
    $tmpfile = tempnam(sys_get_temp_dir(), 'crop_') . '.png'; // use PNG for generality
    imagepng($dst, $tmpfile);

    imagedestroy($img);
    imagedestroy($dst);

    return $tmpfile;
}