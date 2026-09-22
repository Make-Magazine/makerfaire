<?php
/*
 * Generate Table Top Signs from an uploaded CSV. By Rio.
 *
 * Page: 215.9 x 139.7 mm landscape (8.5" x 5.5", half a letter sheet).
 * All coordinates are millimetres, FPDF's default unit. Font sizes are points.
 *
 * REQUIRED CSV COLUMNS
 *   Zone, Area, Booth Name, Project Name
 * OPTIONAL (default to 0 / empty when the column is absent or the cell is blank)
 *   Number of Tables, Number of Chairs, Elec_120V, Exhibitor Resources, Sponsor Order Payment
 * See tabletopsigns_upload_example.csv.
 */

include 'db_connect.php';

const DPI        = 96;
const MM_IN_INCH = 25.4;

/* =========================================================================
 * SIGN GEOMETRY — millimetres
 * ====================================================================== */
const TT_PAGE_W = 215.9;   // 8.5in
const TT_PAGE_H = 139.7;   // 5.5in
const TT_MARGIN = 3;

const TT_BOOTH_X     = 4;
const TT_BOOTH_Y     = 35;
const TT_BOOTH_SIZE  = 72;   // pt

const TT_TITLE_X        = 4;
const TT_TITLE_Y        = 62;
const TT_TITLE_SIZE     = 25;  // pt
const TT_TITLE_MIN      = 10;
const TT_TITLE_MAXLINES = 2;
const TT_TITLE_MAXY     = 79;  // must clear the location line at TT_LOC_Y

const TT_LOC_X    = 4;
const TT_LOC_Y    = 80;
const TT_LOC_SIZE = 24;
const TT_LOC_MIN  = 12;

const TT_RES_X    = 100;
const TT_RES_Y    = 97;
const TT_RES_SIZE = 14;

const TT_NOTE_X    = 15;
const TT_NOTE_Y    = 100;
const TT_NOTE_SIZE = 14;

/** FPDF's per-cell inset: $margin = 28.35/k, $cMargin = $margin/10 → 1.0mm in a mm document. */
const TT_CELL_MARGIN = 1.0;

/** Columns that must be present, or nothing useful can be drawn. */
const TT_REQUIRED_COLUMNS = ['Zone', 'Area', 'Booth Name', 'Project Name'];

/** Columns we use if present, defaulted if not. */
const TT_OPTIONAL_COLUMNS = [
   'Number of Tables',
   'Number of Chairs',
   'Elec_120V',
   'Exhibitor Resources',
   'Sponsor Order Payment',
];
?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
      <style>
         body { font-family: -apple-system, system-ui, sans-serif; margin: 2em; }
         table.report { border-collapse: collapse; margin-top: 1em; }
         table.report td, table.report th { border: 1px solid #ccc; padding: 4px 8px; font-size: 13px; }
         .ok   { color: #1a7f37; }
         .bad  { color: #b32d2e; }
         .box  { padding: 10px; border-radius: 10px; border: solid 1px #333; margin: 15px 0; background: #f1f1f1; }
      </style>
   </head>
   <body>

      <h2>Upload a CSV to generate Table Top Signs</h2>
      <form method="post" enctype="multipart/form-data">
         Select File to upload:
         <input type="file" name="fileToUpload" id="fileToUpload" accept=".csv,text/csv">
         <label for="faire">Faire Directory</label>
         <input type="text" name="faire" id="faire" required>
         <br />
         <input type="submit" value="Upload" name="submit">
      </form>

<?php
if (isset($_POST['submit'])) {

   require_once ('../generate_pdf/fpdf/fpdf.php');

   // ------------------------------------------------------------------
   // Validate the faire directory. This lands in a filesystem path, so it
   // must not be able to contain separators or "..".
   // ------------------------------------------------------------------
   $faire = isset($_POST['faire']) ? preg_replace('/[^A-Za-z0-9_\-]/', '', $_POST['faire']) : '';

   if ($faire === '') {
      echo '<p class="bad">Please enter a faire directory (letters, numbers, hyphen and underscore only).</p></body></html>';
      exit();
   }

   // ------------------------------------------------------------------
   // Take the upload
   // ------------------------------------------------------------------
   $savedFile = tt_store_upload();

   if ($savedFile === false) {
      echo '</body></html>';
      exit();
   }

   // ------------------------------------------------------------------
   // Parse
   // ------------------------------------------------------------------
   $parsed = tt_read_csv($savedFile);

   foreach ($parsed['errors'] as $message) {
      echo '<p class="bad">' . htmlspecialchars($message) . '</p>';
   }

   if (empty($parsed['rows'])) {
      echo '<p class="bad">No usable rows found — nothing generated.</p></body></html>';
      exit();
   }

   if (!empty($parsed['warnings'])) {
      echo '<div class="box">';
      foreach ($parsed['warnings'] as $message) {
         echo htmlspecialchars($message) . '<br>';
      }
      echo '</div>';
   }

   // ------------------------------------------------------------------
   // Generate
   // ------------------------------------------------------------------
   $results = tt_generate_signs($parsed['rows'], $faire);

   $ok   = 0;
   $fail = 0;
   echo '<table class="report"><tr><th>CSV row</th><th>Booth</th><th>Project</th><th>File</th><th>Result</th></tr>';

   foreach ($results as $r) {
      $r['ok'] ? $ok++ : $fail++;
      printf(
         '<tr><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td class="%s">%s</td></tr>',
         $r['line'],
         htmlspecialchars($r['booth']),
         htmlspecialchars($r['project']),
         htmlspecialchars($r['file']),
         $r['ok'] ? 'ok' : 'bad',
         htmlspecialchars($r['message'])
      );
   }

   echo '</table>';
   printf('<p><strong>%d generated, %d failed.</strong></p>', $ok, $fail);
   echo '</body></html>';
   exit();
}
?>
   </body>
</html>
<?php

/* =========================================================================
 * UPLOAD
 * ====================================================================== */

/**
 * Move the uploaded CSV somewhere we can read it. Returns the path or false.
 */
function tt_store_upload() {
   if (!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] === UPLOAD_ERR_NO_FILE) {
      echo '<p class="bad">No file selected.</p>';
      return false;
   }

   if ($_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
      echo '<p class="bad">Upload failed, error code ' . (int) $_FILES['fileToUpload']['error'] . '.</p>';
      return false;
   }

   $name = $_FILES['fileToUpload']['name'];
   $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

   if (!in_array($ext, array('csv', 'txt'), true)) {
      echo '<p class="bad">Please upload a .csv file (got .' . htmlspecialchars($ext) . ').</p>';
      return false;
   }

   $target_dir = 'uploads/';
   if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
      echo '<p class="bad">Could not create the uploads directory.</p>';
      return false;
   }

   // Unique name, extension kept last so the file is still recognisably a CSV.
   // The old version appended the date AFTER the extension and, if the name already
   // existed, skipped the move but then tried to read the file anyway.
   $base   = preg_replace('/[^A-Za-z0-9_\-]/', '-', pathinfo($name, PATHINFO_FILENAME));
   $target = $target_dir . $base . '-' . date('Ymd-His') . '-' . substr(md5(uniqid('', true)), 0, 6) . '.csv';

   if (!move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target)) {
      echo '<p class="bad">Could not store the uploaded file.</p>';
      return false;
   }

   printf(
      '<div class="box">Upload: %s<br>Size: %.1f Kb<br>Stored as: %s</div>',
      htmlspecialchars($name),
      $_FILES['fileToUpload']['size'] / 1024,
      htmlspecialchars($target)
   );

   return $target;
}

/* =========================================================================
 * CSV
 * ====================================================================== */

/**
 * Read the CSV with fgetcsv so quoted fields containing commas — or newlines — survive.
 *
 * Returns array(
 *   'rows'     => list of array('line' => int, 'data' => assoc row),
 *   'errors'   => fatal problems,
 *   'warnings' => per-row problems that were skipped over,
 * )
 */
function tt_read_csv($path) {
   $result = array('rows' => array(), 'errors' => array(), 'warnings' => array());

   $fh = @fopen($path, 'r');
   if (!$fh) {
      $result['errors'][] = 'Could not open the uploaded file.';
      return $result;
   }

   // Pass the escape parameter explicitly: PHP 8.4 deprecates relying on the default, and
   // "" gives standard RFC-4180 behaviour rather than PHP's backslash escaping.
   $header = fgetcsv($fh, 0, ',', '"', '');
   if ($header === false) {
      $result['errors'][] = 'The file appears to be empty.';
      fclose($fh);
      return $result;
   }

   // Excel writes a UTF-8 BOM ahead of the first header cell.
   $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
   $header    = array_map(function ($h) { return trim((string) $h); }, $header);

   $missing = array_diff(TT_REQUIRED_COLUMNS, $header);
   if ($missing) {
      $result['errors'][] = 'Missing required column(s): ' . implode(', ', $missing)
         . '. Found: ' . implode(', ', $header);
      fclose($fh);
      return $result;
   }

   $absent = array_diff(TT_OPTIONAL_COLUMNS, $header);
   if ($absent) {
      $result['warnings'][] = 'Optional column(s) not present, treated as blank: ' . implode(', ', $absent);
   }

   $line = 1;
   while (($row = fgetcsv($fh, 0, ',', '"', '')) !== false) {
      $line++;

      // fgetcsv hands back array(null) for a blank line
      if ($row === array(null) || (count($row) === 1 && trim((string) $row[0]) === '')) {
         continue;
      }

      if (count($row) !== count($header)) {
         $result['warnings'][] = sprintf(
            'Row %d skipped: %d fields, expected %d. (A stray comma, or a quote left open.)',
            $line,
            count($row),
            count($header)
         );
         continue;
      }

      $data = array_combine($header, $row);

      // Fill in any optional columns the file doesn't carry
      foreach (TT_OPTIONAL_COLUMNS as $col) {
         if (!array_key_exists($col, $data)) {
            $data[$col] = '';
         }
      }

      if (trim((string) $data['Project Name']) === '') {
         $result['warnings'][] = sprintf('Row %d skipped: Project Name is blank.', $line);
         continue;
      }

      $result['rows'][] = array('line' => $line, 'data' => $data);
   }

   fclose($fh);
   return $result;
}

/* =========================================================================
 * GENERATION
 * ====================================================================== */

/**
 * Build one PDF per row. Returns a list of per-row results for the report table.
 */
function tt_generate_signs(array $rows, $faire) {
   $results = array();
   $used    = array();

   $baseDir  = tt_template_directory() . '/signs/' . $faire . '/tabletags/';
   $errorDir = $baseDir . 'error/';

   foreach ($rows as $entry) {
      $rowData = $entry['data'];
      $line    = $entry['line'];

      $slug = tt_slug($rowData['Zone']) . '_' . tt_slug($rowData['Area']) . '_' . tt_slug($rowData['Project Name']);
      $slug = trim($slug, '_-');

      if ($slug === '') {
         $slug = 'row-' . $line;
      }

      // Two rows producing the same filename used to overwrite each other silently.
      $unique = $slug;
      $n      = 2;
      while (isset($used[$unique])) {
         $unique = $slug . '-' . $n;
         $n++;
      }
      $used[$unique] = true;

      $result = array(
         'line'    => $line,
         'booth'   => $rowData['Booth Name'],
         'project' => $rowData['Project Name'],
         'file'    => $unique . '.pdf',
         'ok'      => false,
         'message' => '',
      );

      if ($unique !== $slug) {
         $result['message'] = 'Duplicate name, numbered. ';
      }

      try {
         $pdf = new FPDF('L', 'mm', array(TT_PAGE_H, TT_PAGE_W));
         $pdf->AddFont('Benton Sans', 'B', 'bentonsans-bold-webfont.php');
         $pdf->AddFont('Benton Sans', '', 'bentonsans-regular-webfont.php');
         $pdf->AddPage('L', array(TT_PAGE_H, TT_PAGE_W));
         $pdf->SetFont('Benton Sans', '', 12);
         $pdf->SetMargins(TT_MARGIN, TT_MARGIN);
         $pdf->SetFillColor(255, 255, 255);
         // Without this an overlong block can push a blank second page into the PDF.
         $pdf->SetAutoPageBreak(false);

         $drew = createOutput($rowData, $pdf);

         $dir      = $drew ? $baseDir : $errorDir;
         $filename = $dir . $unique . '.pdf';
         $twin     = ($drew ? $errorDir : $baseDir) . $unique . '.pdf';

         if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new Exception('Could not create ' . $dir);
         }

         // A sign that has moved between good and error states shouldn't leave a stale twin
         if (file_exists($twin)) {
            unlink($twin);
         }

         if (ob_get_contents()) {
            ob_clean();
         }

         $pdf->Output($filename, 'F');

         $result['ok']       = (bool) $drew;
         $result['message'] .= $drew ? 'OK' : 'Written to error/ — check the row data';

      } catch (Exception $e) {
         $result['message'] .= 'Failed: ' . $e->getMessage();
         error_log('Unable to create table tag for row ' . $line . ': ' . $e->getMessage());
      }

      $results[] = $result;
   }

   return $results;
}

/**
 * Draw one table tag. Returns true when the row had enough data to make a usable sign.
 *
 * The old version returned a slug string used only for its truthiness, which meant a row
 * could land in error/ for reasons that had nothing to do with whether it drew correctly.
 */
function createOutput($rowData, $pdf) {
   $project_title = filterText(trim((string) $rowData['Project Name']));
   // A real newline is what MultiCell wraps on. The old code substituted the literal
   // string "<br/>", which FPDF prints as-is.
   $project_title = preg_replace('/\v+|\\\[rn]/', "\n", $project_title);

   $zone    = filterText(trim((string) $rowData['Zone']));
   $subarea = filterText(trim((string) $rowData['Area']));
   $booth   = filterText(trim((string) $rowData['Booth Name']));

   $chairs    = tt_number($rowData, 'Number of Chairs');
   $tables    = tt_number($rowData, 'Number of Tables');
   $elec_120V = tt_number($rowData, 'Elec_120V');

   // NOTE: these are truthiness checks, so a literal "0" in either column reads as empty.
   $other_resources = tt_filled($rowData, 'Exhibitor Resources') || tt_filled($rowData, 'Sponsor Order Payment');

   /* --- Booth name, large, centred ----------------------------------- */
   $boothW = TT_PAGE_W - TT_MARGIN - TT_BOOTH_X;
   $pdf->SetXY(TT_BOOTH_X, TT_BOOTH_Y);
   $size = tt_fit_font($pdf, 'Benton Sans', 'B', $booth, $boothW, TT_BOOTH_SIZE, 24, 1);
   $pdf->SetFont('Benton Sans', 'B', $size);
   $pdf->Cell(0, 10, $booth, 0, 0, 'C');

   /* --- Project title ------------------------------------------------ */
   $titleW = TT_PAGE_W - TT_MARGIN - TT_TITLE_X;
   $pdf->SetXY(TT_TITLE_X, TT_TITLE_Y);

   $size = tt_fit_font($pdf, 'Benton Sans', 'B', $project_title, $titleW, TT_TITLE_SIZE, TT_TITLE_MIN, TT_TITLE_MAXLINES);

   // Shrink further if the wrapped block would reach the location line below it
   while ($size > TT_TITLE_MIN) {
      $pdf->SetFont('Benton Sans', 'B', $size);
      $lines  = tt_count_lines($pdf, $project_title, $titleW);
      $bottom = TT_TITLE_Y + $lines * ($size * 0.2645833333333 * 1.4);
      if ($bottom <= TT_TITLE_MAXY) {
         break;
      }
      $size--;
   }

   $pdf->SetFont('Benton Sans', 'B', $size);
   $lineHeight = $size * 0.2645833333333 * 1.4;
   $pdf->MultiCell(0, $lineHeight, $project_title, 0, 'C');

   /* --- Zone / Area -------------------------------------------------- */
   $location = 'Zone: ' . $zone . ' - Area: ' . $subarea;
   $locW     = TT_PAGE_W - TT_MARGIN - TT_LOC_X;
   $pdf->SetXY(TT_LOC_X, TT_LOC_Y);
   $size = tt_fit_font($pdf, 'Benton Sans', 'B', $location, $locW, TT_LOC_SIZE, TT_LOC_MIN, 1);
   $pdf->SetFont('Benton Sans', 'B', $size);
   $pdf->Cell(0, 10, $location, 0, 0, 'C');

   /* --- Basic resources, right aligned ------------------------------- */
   $pdf->SetXY(TT_RES_X, TT_RES_Y);
   $pdf->SetFont('Benton Sans', '', TT_RES_SIZE);
   $lineHeight = 15 * 0.2645833333333 * 1.3;

   $resources  = 'Chairs - ' . $chairs . "\n";
   $resources .= 'Tables - ' . $tables . "\n";
   $resources .= 'Elec 120V - ' . $elec_120V . "\n";

   $pdf->MultiCell(0, $lineHeight, $resources, 0, 'R');

   /* --- Additional resources note ------------------------------------ */
   if ($other_resources) {
      $pdf->SetXY(TT_NOTE_X, TT_NOTE_Y);
      $pdf->SetFont('Benton Sans', 'B', TT_NOTE_SIZE);
      $pdf->Cell(0, 10, 'See Reports for additional resources needed', 0, 0, 'L');
   }

   return $project_title !== '' && $booth !== '';
}

/* =========================================================================
 * HELPERS
 * ====================================================================== */

/** Numeric resource cell, defaulting to 0 when absent or blank. */
function tt_number($rowData, $key) {
   if (!array_key_exists($key, $rowData)) {
      return 0;
   }
   $value = trim((string) $rowData[$key]);
   return ($value === '') ? 0 : $value;
}

/** True when a free-text cell has content. */
function tt_filled($rowData, $key) {
   return array_key_exists($key, $rowData) && trim((string) $rowData[$key]) !== '';
}

/**
 * Filesystem-safe slug. The old version only replaced spaces, so a "/" in a project name
 * wrote the PDF outside the target folder.
 */
function tt_slug($text) {
   $text = strtolower(trim((string) $text));
   $text = str_replace(array('/', '\\'), '-', $text);
   $text = preg_replace('/[^a-z0-9\-_]+/', '-', $text);
   $text = preg_replace('/-+/', '-', $text);
   return trim($text, '-');
}

/** get_template_directory() when WordPress is loaded, otherwise a sensible relative path. */
function tt_template_directory() {
   if (function_exists('get_template_directory')) {
      return get_template_directory();
   }
   return dirname(__DIR__);
}

/**
 * Largest font size (down to $minSize) at which $text fits $width in no more than $maxLines.
 */
function tt_fit_font($pdf, $family, $style, $text, $width, $startSize, $minSize, $maxLines) {
   $size      = $startSize;
   $effective = tt_effective_width($width);

   while ($size > $minSize) {
      $pdf->SetFont($family, $style, $size);

      /* Both conditions matter. MultiCell breaks a word that is wider than the column
       * CHARACTER BY CHARACTER — "COMMUNICATIONS" becomes "COMMUNICATIO / NS" — so the
       * longest word has to fit before the line count is even worth checking. */
      if (tt_longest_word_width($pdf, $text) <= $effective
         && tt_count_lines($pdf, $text, $width) <= $maxLines) {
         return $size;
      }

      $size--;
   }

   return $minSize;
}

/** Usable width inside a cell of $width, less FPDF's cell margin on each side. */
function tt_effective_width($width) {
   return max(1, $width - 2 * TT_CELL_MARGIN);
}

/** Width of the widest single word at the current font. */
function tt_longest_word_width($pdf, $text) {
   $max = 0;
   foreach (preg_split('/\s+/', trim($text)) as $word) {
      if ($word !== '') {
         $max = max($max, $pdf->GetStringWidth($word));
      }
   }
   return $max;
}

/** How many lines MultiCell() wraps $text into at the current font and $width. */
function tt_count_lines($pdf, $text, $width) {
   $width = tt_effective_width($width);
   $lines = 0;

   foreach (explode("\n", $text) as $paragraph) {
      $words   = preg_split('/\s+/', trim($paragraph));
      $current = '';
      $lines++;

      foreach ($words as $word) {
         if ($word === '') {
            continue;
         }

         // Model MultiCell's character-level break for an over-wide word
         if ($pdf->GetStringWidth($word) > $width) {
            if ($current !== '') {
               $lines++;
               $current = '';
            }
            $chunk = '';
            foreach (str_split($word) as $char) {
               if ($chunk !== '' && $pdf->GetStringWidth($chunk . $char) > $width) {
                  $lines++;
                  $chunk = $char;
               } else {
                  $chunk .= $char;
               }
            }
            $current = $chunk;
            continue;
         }

         $try = ($current === '') ? $word : $current . ' ' . $word;
         if ($pdf->GetStringWidth($try) > $width && $current !== '') {
            $lines++;
            $current = $word;
         } else {
            $current = $try;
         }
      }
   }

   return $lines;
}

/**
 * Convert to the windows-1252 encoding the Benton Sans font files use. Curly quotes, en
 * dashes and accented characters render as garbage without this.
 */
function filterText($text) {
   $text = (string) $text;

   $string = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text);
   if ($string === false) {
      ini_set('mbstring.substitute_character', 'none');
      $string = mb_convert_encoding($text, 'windows-1252', 'UTF-8');
   }

   // now translate any unicode stuff...
   $conv = array(
      '&amp;'  => '&',
      '&#039;' => "'",
      '&quot;' => '"',
   );

   return strtr((string) $string, $conv);
}

function pixelsToMM($val) {
   return $val * MM_IN_INCH / DPI;
}
