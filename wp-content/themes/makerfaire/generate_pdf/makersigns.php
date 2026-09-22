<?php
/*
 * Create Maker sign of a submitted Gravity Forms entry.
 *
 * US LETTER PORTRAIT, 8.5" x 11". The 2025 square artwork is scaled to the page width and
 * pinned to the BOTTOM edge; the title sits in the white band above it. Ported from the
 * CSV-driven prototype, but every value is read from the Gravity Forms entry instead of a
 * spreadsheet column.
 *
 * All coordinates are in MILLIMETRES — FPDF's default unit. Font sizes are in points, as
 * FPDF always expects. Elements belonging to the artwork keep their original 381mm design
 * coordinates and are mapped onto the page by mf_ax() / mf_ay() / mf_as(), so the layout
 * still matches the background art and the whole block moves together.
 *
 * WHAT IS DRAWN (matching the prototype):
 *   - SIGN_BACKGROUND artwork, full width, bottom aligned
 *   - project title, orange, centred, in the white band above the art
 *   - area / subarea, white, left column of the blue panel
 *   - "Learn More" + QR code, bottom left
 *   - project photo, circle-cropped into the photo well at (257, 220) r83
 *   - MFBA26_Patch_Final.png branding patch, bottom aligned to the QR box
 *
 * WHAT IS COMPUTED BUT NOT DRAWN:
 *   Maker/group name, short description, category, exhibit type, booth and maker photo are
 *   all resolved into variables, with their drawing blocks left commented out exactly as in
 *   the prototype. Uncomment a block and the value is already there and correct.
 *
 * NOTES ON THE PORT:
 *   - The output filename stays "<entry_id>.pdf". createSignZip() and the generator's
 *     post-write verification both look for that exact name — don't change it to a slug.
 *   - The category (field 320) is a DROPDOWN storing text, not term IDs. The old
 *     get_term($entry['320']) call therefore always returned false and printed nothing.
 *     mf_project_category() below resolves it properly.
 *   - The Zoho Creator image API from the prototype is gone: Gravity Forms gives us real
 *     image URLs, so mf_add_circular_image() takes a URL or path directly.
 */

// set up database
$root = $_SERVER['DOCUMENT_ROOT'];
require_once ($root . '/wp-config.php');
require_once ($root . '/wp-includes/class-wpdb.php');

const DPI = 96;
const MM_IN_INCH = 25.4;
// image sizes
const MAX_WIDTH = 450;
const MAX_HEIGHT = 450;

/* =========================================================================
 * PAGE — US Letter portrait, 8.5" x 11"
 * ====================================================================== */
const PAGE_W = 215.9;   // 8.5in in mm
const PAGE_H = 279.4;   // 11in  in mm

/* -------------------------------------------------------------------------
 * ART BLOCK
 *
 * The background is square (1080x1080). It is scaled to the full page width and pinned to
 * the BOTTOM edge of the page, leaving a plain white band above it for the title.
 *
 *     y = 0            ┌──────────────┐
 *                      │  title band  │   PAGE_H - ART_W  = 63.5mm
 *     y = ART_TOP      ├──────────────┤
 *                      │              │
 *                      │  square art  │   ART_W = PAGE_W = 215.9mm
 *                      │              │
 *     y = PAGE_H       └──────────────┘
 *
 * Every element that belongs to the artwork is positioned in the art's own 381mm design
 * space — the same numbers as the square sign, so they still line up with the background —
 * and mapped onto the page by mf_ax() / mf_ay() / mf_as(). Change ART_W or ART_TOP and the
 * whole composition moves together.
 * ---------------------------------------------------------------------- */
/** Background artwork, in pdf_layouts/. Must be square and match the well to PHOTO_CY. */
const SIGN_BACKGROUND = 'signBackground2026-raisedwell.png';

const ART_DESIGN_SIZE = 381;                       // the design space the art was drawn in
const ART_W           = PAGE_W;                    // art is full page width
const ART_TOP         = PAGE_H - ART_W;            // bottom aligned
const ART_SCALE       = ART_W / ART_DESIGN_SIZE;   // ≈ 0.5667

/* -------------------------------------------------------------------------
 * TITLE — lives in the page band ABOVE the art, so these are PAGE coordinates
 * ---------------------------------------------------------------------- */
const TITLE_X        = 15;
const TITLE_Y        = 20;
const TITLE_W        = PAGE_W - 30;
const TITLE_SIZE     = 60;    // pt
const TITLE_MAXLINES = 4;   // the band fits 4 lines at 60pt (20 + 4*23.8 = 115mm, clear of 133)
/* Bottom limit: the top of the photo circle in page space is
 * ART_TOP + (PHOTO_CY - PHOTO_R) * ART_SCALE = 63.5 + 112*0.5667 ≈ 127mm. Stop short of it.
 * 4 lines at 60pt reach 115.2mm, so full-size titles are unaffected by the tighter floor. */
const TITLE_MAXY     = 119;

/* Set false to render titles at a fixed TITLE_SIZE. Left on because long titles otherwise
 * run down into the photo circle. Shrinking only kicks in when text would actually overflow. */
const TITLE_AUTOFIT = true;

/* -------------------------------------------------------------------------
 * ART-SPACE ELEMENTS — 381mm design space, mapped onto the page at draw time
 * ---------------------------------------------------------------------- */
const NAME_X      = 21;    // maker / group name — computed, block commented out
const NAME_Y      = 140;
const NAME_W      = 340;
const NAME_SIZE   = 52;

/* -------------------------------------------------------------------------
 * LEFT COLUMN
 *
 * The area text, "Learn More" and the QR code's white box all share one left edge. Three
 * separate things pushed the text right of the QR box before:
 *   1. the text was at art x=21 while the QR sat at x=20
 *   2. FPDF insets MultiCell content by cMargin (1mm in a millimetre document)
 *   3. the first glyph's left side bearing — the gap between the text origin and where the
 *      ink actually starts
 * COL_X fixes (1), FPDF_CELL_MARGIN cancels (2), COL_INK_NUDGE compensates (3).
 * ---------------------------------------------------------------------- */
const COL_X = 20;          // shared left edge, art space

/** FPDF's per-cell inset. $margin = 28.35/k; $cMargin = $margin/10 → 1.0mm for a mm document. */
const FPDF_CELL_MARGIN = 1.0;

/* Left side bearing compensation, in PAGE millimetres, so ink lines up with the hard edge of
 * the QR box rather than the invisible text origin. Measured off the rendered PDF. Set to 0
 * for strict origin alignment (text will sit ~1mm right of the box). */
const COL_INK_NUDGE = 0.95;

const AREA_X      = COL_X;
const AREA_Y      = 237;
const AREA_W      = 150;   // left column only — the photo circle occupies x>174
const AREA_SIZE   = 42;
/* Two lines is what fits between AREA_Y and "Learn More" at LEARN_Y (237 + 2*16.7 = 270,
 * clear of 297). A third line at 42pt lands on top of it, so long area names shrink. */
const AREA_MAXLINES = 2;

const LEARN_X     = COL_X;
const LEARN_Y     = 297;
const LEARN_SIZE  = 33;

const QR_X        = COL_X;
const QR_Y        = 305;
const QR_W        = 65;

const PHOTO_CX    = 257;   // photo well centre
const PHOTO_CY    = 195;   // raised 25 units from the artwork's well to close the gap under the title
const PHOTO_R     = 83;

/* Branding lockup. The old Makey was 100 wide x 108.3 tall (10,831 sq units); this lockup is
 * wide and short (aspect 0.392), so 166.3 wide gives it the same area on the page. Its right
 * edge stays at art x=365 where the Makey's was, and its BOTTOM is aligned to the QR box at
 * draw time. Drop to 130.3 for a smaller wordmark with more blue around it. */
const LOGO_RIGHT  = 365;
const LOGO_W      = 166.3;   // max width
const LOGO_MAX_H  = 110;     // max height — the patch tucks into the photo well's lower-left
const LOGO_FILE   = 'MFBA26_Patch_Final.png';
const LOGO_Y      = 260;   // fallback only, used if the artwork can't be measured


/* -------------------------------------------------------------------------
 * IMAGE SIZE GUARD
 *
 * FPDF inflates a PNG to raw scanlines in memory, then makes a second copy of the
 * colour data and a third of the alpha while splitting the channels — roughly
 * width * height * 8 bytes at peak. A 77-megapixel file therefore wants ~585MB and
 * takes the request down with an OOM fatal before anything can be caught.
 *
 * getimagesize() reads only the header, so this costs nothing and turns a fatal into
 * one logged, skipped sign. 12 MP peaks around 96MB, comfortably inside a 512MB limit.
 * ---------------------------------------------------------------------- */
const MF_MAX_IMAGE_PIXELS = 12000000;

/** Bumped whenever this file changes, so a run can prove which copy is live. */
const MF_SIGNS_VERSION = '2026-09-22-guard3';

/**
 * php.ini's memory_limit in bytes. 0 or -1 means unlimited.
 */
function mf_memory_limit_bytes() {
   $raw = trim((string) ini_get('memory_limit'));
   if ($raw === '' || $raw === '-1') {
      return 0;
   }
   $unit = strtolower(substr($raw, -1));
   $val  = (float) $raw;
   if ($unit === 'g') { $val *= 1024 * 1024 * 1024; }
   elseif ($unit === 'm') { $val *= 1024 * 1024; }
   elseif ($unit === 'k') { $val *= 1024; }
   return (int) $val;
}

/**
 * Report the entry and the image being placed when a fatal kills the request.
 *
 * An OOM inside FPDF cannot be caught - it is not an Exception - so the whole request dies
 * with a 500 and nothing in the log says which entry or which image did it. This turns that
 * into one actionable line.
 */
$GLOBALS['mf_oom_reserve'] = str_repeat('x', 262144);   // freed below so the handler can run
register_shutdown_function(function () {
   // PHP does not hand memory back before shutdown functions run, so release a reserve
   // first - otherwise the report about running out of memory runs out of memory.
   unset($GLOBALS['mf_oom_reserve']);

   $e = error_get_last();
   if (!$e || !in_array($e['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR), true)) {
      return;
   }
   error_log(sprintf(
      'makersigns FATAL [%s] entry=%s last image=%s (peak %.0fMB of a %.0fMB limit) :: %s',
      MF_SIGNS_VERSION,
      isset($GLOBALS['mf_current_entry']) ? $GLOBALS['mf_current_entry'] : '?',
      isset($GLOBALS['mf_last_image']) ? $GLOBALS['mf_last_image'] : '?',
      memory_get_peak_usage(true) / 1048576,
      mf_memory_limit_bytes() / 1048576,
      $e['message']
   ));
});

/**
 * True when $path is missing, unreadable, or too large for FPDF to decode safely.
 * $why is filled with a human-readable reason for the log.
 */
function mf_image_too_big($path, &$why = null) {
   // Recorded for the shutdown handler: if FPDF dies, this is what it died on.
   $GLOBALS['mf_last_image'] = $path;

   $size = @getimagesize($path);

   if (!$size || empty($size[0]) || empty($size[1])) {
      $why = 'unreadable or not an image';
      return true;
   }

   $pixels = $size[0] * $size[1];
   $need   = $pixels * 8;   // FPDF's rough peak: inflated scanlines + colour copy + alpha copy

   if ($pixels > MF_MAX_IMAGE_PIXELS) {
      $why = sprintf(
         '%dx%d = %.1f megapixels, over the %.0f MP limit (FPDF would need ~%.0fMB)',
         $size[0], $size[1], $pixels / 1e6, MF_MAX_IMAGE_PIXELS / 1e6, $need / 1048576
      );
      return true;
   }

   // A fixed megapixel ceiling is not enough on its own: what matters is whether this
   // particular image fits in what is left of the limit right now. Keep 20% back for the
   // rest of the page.
   $limit = mf_memory_limit_bytes();
   if ($limit > 0) {
      $free = $limit - memory_get_usage(true);
      if ($need > $free * 0.8) {
         $why = sprintf(
            '%dx%d needs ~%.0fMB but only %.0fMB of the %.0fMB limit is free',
            $size[0], $size[1], $need / 1048576, $free / 1048576, $limit / 1048576
         );
         return true;
      }
   }

   return false;
}

/** Map an art-space X onto the page. */
function mf_ax($x) { return $x * ART_SCALE; }

/** Map an art-space Y onto the page. */
function mf_ay($y) { return ART_TOP + $y * ART_SCALE; }

/** Scale an art-space length or font size onto the page. */
function mf_as($v) { return $v * ART_SCALE; }

// require FPDF
require_once ('fpdf/fpdf.php');
// require clipping
require ('fpdf/clipping.php');

class PDF extends FPDF {

   // Page header
   function Header() {
      // Header required when using restful structures for Chrome, otherwise generating
      // signs curl will get a 403
      header('HTTP/1.0 200 OK');
      header('Cache-Control: public, must-revalidate, max-age=0');
      header('Pragma: no-cache');
      header('Accept-Ranges: bytes');
      header("Content-Transfer-Encoding: binary");
      header("Content-type: application/pdf");
      $this->SetFont('Benton Sans', 'B', 15);
   }

}

// Instanciation of inherited class
try {
   $pdf = new PDF_Clipping();
   $pdf->AddFont('Benton Sans', 'B', 'bentonsans-bold-webfont.php');
   $pdf->AddFont('Benton Sans', '', 'bentonsans-regular-webfont.php');
   $pdf->AddFont('FontAwesome1','','FontAwesome47-P1.php'); // https://drive.google.com/file/d/1Y3NlxBZtXPcFUIwiLeQWzdzdoSe9f6xo/view?pli=1
   $pdf->AddFont('FontAwesome2','','FontAwesome47-P2.php'); // https://drive.google.com/file/d/1XjjEyhkcD0mO6FTf0w9XHB4bwjMCL2ij/view
   $pdf->AddFont('FontAwesome3','','FontAwesome47-P3.php'); // https://drive.google.com/file/d/10WBuA63DMbNPRWjKSKJpVSk4I1OPwh2R/view
   $pdf->AddFont('FontAwesome4','','FontAwesome47-P4.php'); // https://drive.google.com/file/d/1lPeh5IGXY8Re6nNXEU7i0Wf63o97Svx0/view

   $pdf->AddPage('P', array(PAGE_W, PAGE_H));
   $pdf->SetFont('Benton Sans', '', 12);
   $pdf->SetAutoPageBreak(false);

   // Background — the square art, scaled to page width and pinned to the bottom edge.
   // The band above it is left as plain white page for the title.
   //
   // NOTE: this is signBackground2025.png with the photo well raised 25 art units to match
   // PHOTO_CY, rebuilt programmatically (flat white above y=625px, flat blue below, the
   // dotted arcs and red dot composited back on top). If the photo position changes again,
   // the well has to move with it or a crescent of the well shows from under the photo.
   // Ideally your designer supplies updated art and this points back at a supplied file.
   $background = get_template_directory() . '/generate_pdf/pdf_layouts/' . SIGN_BACKGROUND;
   if (!file_exists($background)) {
      error_log("makersigns: background missing at $background");
   } elseif (mf_image_too_big($background, $why)) {
      error_log("makersigns: background not usable — $why: $background");
   } else {
      $pdf->Image($background, 0, ART_TOP, ART_W, ART_W);
   }

   $pdf->SetMargins(TITLE_X, TITLE_Y, TITLE_X); //left, top, right

   // get the entry-id, if one isn't set return an error
   $eid = '';
   if (isset($wp_query->query_vars['eid'])) {
      $eid = $wp_query->query_vars['eid'];
   } else if (isset($_GET['eid']) && $_GET['eid'] != '') {
      $eid = $_GET['eid'];
   }

   if (isset($eid) && $eid != '') {
      $faire = '';
      if (isset($wp_query->query_vars['faire'])) {
         $faire = $wp_query->query_vars['faire'];
      } else if (isset($_GET['faire']) && $_GET['faire'] != '') {
         $faire = $_GET['faire'];
      }
      $entryid = sanitize_text_field($eid);
      $resizeImage = createOutput($entryid, $pdf);

      if (isset($_GET['type']) && $_GET['type'] == 'download') {
         if (ob_get_contents())
            ob_clean();
         $pdf->Output($entryid . '.pdf', 'D');
      } elseif (isset($_GET['type']) && $_GET['type'] == 'save') {
         // Filename must stay <entry_id>.pdf — createSignZip() and the post-write
         // verification in fairesigns.php both look for exactly this.
         $validFile = get_template_directory() . '/signs/' . $faire . '/maker/' . $entryid . '.pdf';
         $errorFile = get_template_directory() . '/signs/' . $faire . '/maker/error/' . $entryid . '.pdf';

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
            mkdir($dirname, 0755, true);
         }
         if (ob_get_contents())
            ob_clean();
         $pdf->Output($filename, 'F');

         exit();
      } else {
         if (ob_get_contents())
            ob_clean();
         $pdf->Output($entryid . '.pdf', 'I');
      }
   } else {
      echo 'No Entry ID submitted';
   }
} catch (Exception $e) {
   error_log("Unable to create PDF due to: " . $e);
}


function createOutput($entry_id, $pdf) {
   $GLOBALS['mf_current_entry'] = $entry_id;

   // Initialize the variable that the image was resized
   $resizeImage = 1;
   $entry = GFAPI::get_entry($entry_id);

   if (is_wp_error($entry)) {
      error_log("makersigns: GFAPI::get_entry failed for $entry_id: " . $entry->get_error_message());
      return 0;
   }

   /* ======================================================================
    * GATHER EVERY VALUE
    * Blocks further down decide which of these actually get drawn.
    * =================================================================== */

   // Field 22 — primary project photo. Field 878 — additional images gallery.
   $project_photo = (isset($entry['22']) ? $entry['22'] : '');
   $photo = json_decode($project_photo);
   if (is_array($photo) && !empty($photo)) {
      $project_photo = $photo[0];
   }

   $project_gallery = (isset($entry['878']) ? json_decode($entry['878']) : '');

   // if the main project photo isn't set but the photo gallery is, use the first gallery image
   if ($project_photo == '' && is_array($project_gallery) && !empty($project_gallery)) {
      $project_photo = $project_gallery[0];
   }

   // Field 217 — maker photo, field 111 — group photo. Computed; block commented out below.
   $group_photo = (isset($entry['111']) ? $entry['111'] : '');
   $maker_photo = ((isset($entry['217']) && !empty($entry['217']) && $entry['217'] != "[]") ? $entry['217'] : $group_photo);
   $photo = json_decode($maker_photo);
   if (is_array($photo) && !empty($photo)) {
      $maker_photo = $photo[0];
   } else {
      $maker_photo = get_template_directory() . '/images/default-makey-medium.png';
   }

   // NOTE: the old code called getimagesize() on the remote URL here to validate it, then
   // downloaded the same file again to draw it — two full fetches per sign. Validation now
   // happens on the single local copy inside mf_add_circular_image().

   // --- text fields -----------------------------------------------------
   $project_short       = (isset($entry['16']) ? filterText($entry['16']) : '');
   $project_affiliation = (isset($entry['168']) ? filterText((string) $entry['168']) : '');
   $project_title       = (isset($entry['151']) ? filterText((string) $entry['151']) : '');

   // A real newline is what MultiCell wraps on. The old code substituted the literal string
   // "<br/>", which FPDF prints verbatim — any multi-line title showed "<br/>" on the sign.
   $project_title = preg_replace('/\v+|\\\[rn]/', "\n", $project_title);

   // Maker / group name. 109 = group name, 96.3 / 96.6 = maker first / last.
   $group_name  = trim((string) (isset($entry['109']) ? $entry['109'] : ''));
   $maker_first = trim((string) (isset($entry['96.3']) ? $entry['96.3'] : ''));
   $maker_last  = trim((string) (isset($entry['96.6']) ? $entry['96.6'] : ''));
   $maker_name  = trim($maker_first . ' ' . $maker_last);
   $name        = filterText($group_name !== '' ? $group_name : $maker_name);

   // Exhibit type lives in the 339.x sub-keys
   $project_type = '';
   foreach ($entry as $key => $value) {
      if (strpos($key ?? '', '339.') === 0) {
         if ($value != '') {
            if (stripos($value, 'sponsor') !== false) {
               $project_type = 'Exhibit';
            } else {
               $project_type = $value;
            }
         }
      }
   }

   // Category — field 320 is a DROPDOWN holding text, not a term id.
   $project_category = filterText(mf_project_category($entry, '320'));

   // --- location --------------------------------------------------------
   global $wpdb;
   $location_results = $wpdb->get_results(
      $wpdb->prepare(
         "SELECT subarea.nicename, subarea.subarea, area.area, location.location
            FROM wp_mf_location location
            LEFT OUTER JOIN wp_mf_faire_subarea subarea ON location.subarea_id = subarea.ID
            LEFT OUTER JOIN wp_mf_faire_area    area    ON subarea.area_id     = area.id
           WHERE location.entry_id = %d
             AND location.location <> ''",
         $entry_id
      )
   );

   $project_subarea = isset($location_results[0]->nicename) ? filterText((string) $location_results[0]->nicename) : '';
   $project_area    = isset($location_results[0]->area) ? filterText((string) $location_results[0]->area) : '';
   $project_booth   = isset($location_results[0]->location) ? filterText((string) $location_results[0]->location) : '';

   // Fall back to the raw subarea name if no friendly nicename is set
   if ($project_subarea === '' && isset($location_results[0]->subarea)) {
      $project_subarea = filterText((string) $location_results[0]->subarea);
   }

   /* ======================================================================
    * DRAW
    * =================================================================== */

   /***************************************************************************
    * Project Title — page space, in the white band above the artwork
    * auto adjust the font so the text will fit
    ***************************************************************************/
   $pdf->setTextColor(237, 28, 36);
   $pdf->SetXY(TITLE_X, TITLE_Y);

   $x = TITLE_SIZE; // set the starting font size
   $pdf->SetFont('Benton Sans', 'B', $x);

   $titleText = strtoupper($project_title);

   if (TITLE_AUTOFIT) {
      // Shrink until the title fits TITLE_MAXLINES, then again if those lines would still
      // reach past TITLE_MAXY into the photo circle.
      $x = mf_fit_font($pdf, 'Benton Sans', 'B', $titleText, TITLE_W, TITLE_SIZE, 20, TITLE_MAXLINES);

      while ($x > 20) {
         $pdf->SetFont('Benton Sans', 'B', $x);
         $lines  = mf_count_lines($pdf, $titleText, TITLE_W);
         $bottom = TITLE_Y + $lines * ($x * 0.2645833333333 * 1.5);
         if ($bottom <= TITLE_MAXY) {
            break;
         }
         $x -= 1;
      }

      $pdf->SetFont('Benton Sans', 'B', $x);
   }

   $lineHeight = $x * 0.2645833333333 * 1.5;

   /* Output the title at the required font size */
   $pdf->MultiCell(TITLE_W, $lineHeight, $titleText, 0, 'C');

   /***************************************************************************
    * Maker / Group Name
    * auto adjust the font so the text will fit
    ***************************************************************************/
   /*
   $pdf->setTextColor(0, 0, 0);
   $pdf->SetXY(mf_ax(NAME_X), mf_ay(NAME_Y));
   $x = mf_as(NAME_SIZE);
   $pdf->SetFont('Benton Sans', '', $x);
   $lineHeight = $x * 0.2645833333333 * 1.5;
   $pdf->MultiCell(mf_as(NAME_W), $lineHeight, strtoupper($name), 0, 'L');
   */

   /***************************************************************************
    * short description
    ***************************************************************************/
   /*
   $pdf->SetXY(mf_ax(16), mf_ay(340));
   $pdf->setTextColor(51, 51, 51);
   $sx = mf_as(24);
   $pdf->SetFont('Benton Sans', '', $sx);
   $lineHeight = $sx * 0.2645833333333 * 1.8;
   // the last parameter here will limit the amount of lines and end with an ellipsis
   $pdf->MultiCell(mf_as(250), $lineHeight, $project_short, 0, 'L', false, 6);
   */

   /***************************************************************************
    * Location / Area
    ***************************************************************************/
   // Back off the cell margin and the side bearing so the INK lands on COL_X.
   $pdf->SetXY(mf_ax(AREA_X) - FPDF_CELL_MARGIN - COL_INK_NUDGE, mf_ay(AREA_Y));
   $pdf->setTextColor(255, 255, 255);

   $areaText = str_replace("_", "\n", strtoupper($project_subarea));

   // Keep the area inside the left column so it never runs under the photo circle.
   $areaSize = mf_fit_font($pdf, 'Benton Sans', '', $areaText, mf_as(AREA_W), mf_as(AREA_SIZE), mf_as(18), AREA_MAXLINES);
   $pdf->SetFont('Benton Sans', '', $areaSize);

   $lineHeight = $areaSize * 0.2645833333333 * 1.5;
   $pdf->MultiCell(mf_as(AREA_W), $lineHeight, $areaText, 0, 'L');

   /***************************************************************************
    * Booth
    ***************************************************************************/
   /*
   $pdf->setTextColor(255, 255, 255);
   $pdf->SetFont('Benton Sans', '', mf_as(30));
   $pdf->Text(mf_ax(AREA_X), mf_ay(AREA_Y + 40), $project_booth);
   */

   /***************************************************************************
    * Type
    ***************************************************************************/
   /*
   $pdf->setTextColor(245, 20, 0);
   $pdf->SetFont('FontAwesome1', '', mf_as(26));
   $pdf->Text(mf_ax(18), mf_ay(310), chr(0x0031));
   $pdf->setTextColor(51, 51, 51);
   $pdf->SetFont('Benton Sans', '', mf_as(26));
   $pdf->Text(mf_ax(32), mf_ay(310), $project_type);
   */

   /***************************************************************************
    * Category
    ***************************************************************************/
   /*
   $pdf->setTextColor(245, 20, 0);
   $pdf->SetFont('FontAwesome2', '', mf_as(26));
   $pdf->Text(mf_ax(18), mf_ay(325), chr(0x0078));
   $pdf->setTextColor(51, 51, 51);
   $pdf->SetFont('Benton Sans', '', mf_as(26));
   $pdf->Text(mf_ax(32), mf_ay(325), $project_category);
   */

   /***************************************************************************
    * QR code
    ***************************************************************************/
   $pdf->setTextColor(255, 255, 255);
   $pdf->SetFont('Benton Sans', '', mf_as(LEARN_SIZE));
   $pdf->Text(mf_ax(LEARN_X) - COL_INK_NUDGE, mf_ay(LEARN_Y), "Learn More");

   $entryURL = 'https://makerfaire.com/maker/entry/' . $entry_id . '/';
   $QR_Code  = 'https://quickchart.io/qr?text=' . urlencode($entryURL) . '&dark=333333&margin=5&size=300';

   $qrX = mf_ax(QR_X);
   $qrY = mf_ay(QR_Y);
   $qrW = mf_as(QR_W);
   $qrH = $qrW;   // QR codes are square; corrected below from the real file if we can read it

   // Fetch it ourselves rather than letting FPDF open the URL: gives us a timeout, a visible
   // error when the QR service is unreachable, and the exact pixel dimensions needed to sit
   // the Makey flush against the bottom of the white box.
   $qrLocal = mf_localize_image($QR_Code);

   if ($qrLocal) {
      list($qrPath, $qrExt, $qrIsTemp) = $qrLocal;
      if (mf_image_too_big($qrPath, $whyQr)) {
         error_log("makersigns: QR image not usable — $whyQr");
         $qrSize = false;
      } else {
         $qrSize = @getimagesize($qrPath);
      }
      if ($qrSize && !empty($qrSize[0])) {
         $qrH = $qrW * ($qrSize[1] / $qrSize[0]);
      }
      $pdf->Image($qrPath, $qrX, $qrY, $qrW, $qrH, strtoupper($qrExt));
      if ($qrIsTemp) {
         @unlink($qrPath);
      }
   } else {
      error_log("makersigns: could not fetch the QR code for entry $entry_id from $QR_Code");
   }

   // The white box's bottom edge — the Makey is aligned to this.
   $qrBottom = $qrY + $qrH;

   /***************************************************************************
    * Project Code
    ***************************************************************************/
   /*
   $pdf->SetFont('Benton Sans', '', mf_as(18));
   $pdf->setTextColor(91, 91, 91);
   $pdf->SetXY(mf_ax(203), mf_ay(540));
   $pdf->MultiCell(mf_as(115), mf_as(15), $entry_id, 0, 'L');
   */

   /***************************************************************************
    * project photo — circle cropped into the photo well
    ***************************************************************************/
   if ($project_photo != '') {
      $source = stripslashes($project_photo);

      // Use the theme's resizer when available — smaller download, same framing.
      if (function_exists('legacy_get_fit_remote_image_url')) {
         $fitted = legacy_get_fit_remote_image_url($source, 1000, 1000, 1);
         if (!empty($fitted)) {
            $source = $fitted;
         }
      }

      if (!mf_add_circular_image($pdf, $source, mf_ax(PHOTO_CX), mf_ay(PHOTO_CY), mf_as(PHOTO_R), 'center')) {
         // Same behaviour as before: fall back to the default image and still treat the
         // sign as valid. Flip $resizeImage to 0 here instead if you'd rather these land
         // in /maker/error/ so they show up under "Include signs in error?".
         error_log("Unable to place the project image for entry $entry_id from $source — using the default");
         $fallback = get_template_directory() . '/images/default-featured-image.jpg';

         if (!mf_add_circular_image($pdf, $fallback, mf_ax(PHOTO_CX), mf_ay(PHOTO_CY), mf_as(PHOTO_R), 'center')) {
            error_log("Default project image also failed for entry $entry_id");
            $resizeImage = 0;
         }
      }
   } else {
      error_log("Missing image for $entry_id");
      $resizeImage = 0;
   }

   /***************************************************************************
    * branding
    ***************************************************************************/
   $logo = get_template_directory() . '/generate_pdf/pdf_layouts/' . LOGO_FILE;
   if (file_exists($logo) && mf_image_too_big($logo, $whyLogo)) {
      // Resize the artwork rather than raising memory_limit: the logo prints 94mm wide,
      // so anything past ~1500px across is wasted bytes in every single PDF.
      error_log("makersigns: branding logo not usable — $whyLogo: $logo");
   } elseif (file_exists($logo)) {
      $logoW = mf_as(LOGO_W);
      $logoX = mf_ax(LOGO_RIGHT) - $logoW;   // right aligned where the old Makey ended
      $logoSize = @getimagesize($logo);

      // Sit the logo's bottom edge on the QR box's bottom edge. Derived from the artwork's
      // real aspect ratio rather than a fixed Y, so the two stay flush even if either image
      // is swapped for one with different proportions.
      if ($logoSize && !empty($logoSize[0])) {
         $logoH = $logoW * ($logoSize[1] / $logoSize[0]);
         if ($logoH > mf_as(LOGO_MAX_H)) {
            $logoH = mf_as(LOGO_MAX_H);
            $logoW = $logoH * ($logoSize[0] / $logoSize[1]);
            $logoX = mf_ax(LOGO_RIGHT) - $logoW;
         }
         $pdf->Image($logo, $logoX, $qrBottom - $logoH, $logoW, $logoH);
      } else {
         error_log("makersigns: could not read $logo — falling back to the fixed LOGO_Y");
         $pdf->Image($logo, $logoX, mf_ay(LOGO_Y), $logoW);
      }
   } else {
      error_log("makersigns: branding logo missing at $logo");
   }

   /***************************************************************************
    * Maker photo
    ***************************************************************************/
   /*
   if ($maker_photo != '') {
      mf_add_circular_image($pdf, stripslashes($maker_photo), mf_ax(75), mf_ay(220), mf_as(55), 'center');
   }
   */

   return $resizeImage;
}


/* =========================================================================
 * HELPERS
 * ====================================================================== */

/**
 * Resolve the project category.
 *
 * Field 320 is a dropdown whose stored values are TEXT, not term IDs. The previous code did
 * get_term($entry['320']) — get_term() hands off to WP_Term::get_instance(), which casts its
 * argument to int, so a text value became 0 and it returned false. isset($term->name) was
 * then false and the sign printed an empty string with no error anywhere. This resolves a
 * term when the value happens to match one, and otherwise uses the stored text as-is.
 */
function mf_project_category($entry, $field_id = '320') {
   $value = isset($entry[$field_id]) ? trim((string) $entry[$field_id]) : '';

   // Defensive: if the field is ever switched back to a checkbox, values move to sub-keys.
   if ($value === '') {
      foreach ($entry as $k => $v) {
         if (strpos((string) $k, $field_id . '.') === 0 && trim((string) $v) !== '') {
            $value = trim((string) $v);
            break;
         }
      }
   }

   if ($value === '') {
      return '';
   }

   // Numeric values are term IDs
   if (is_numeric($value)) {
      $term = get_term((int) $value);
      if ($term && !is_wp_error($term) && !empty($term->name)) {
         return html_entity_decode($term->name, ENT_QUOTES, 'UTF-8');
      }
   }

   // Otherwise try to match the text against a term, by slug then name
   foreach (get_taxonomies(array(), 'names') as $taxonomy) {
      foreach (array('slug', 'name') as $by) {
         $term = get_term_by($by, $value, $taxonomy);
         if ($term && !is_wp_error($term) && !empty($term->name)) {
            return html_entity_decode($term->name, ENT_QUOTES, 'UTF-8');
         }
      }
   }

   // The dropdown text is already human readable — use it.
   return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Largest font size (down to $minSize) at which $text wraps to no more than $maxLines
 * within $width. Deliberately avoids utf8_decode(), which the old auto-shrink loops used
 * and which is deprecated as of PHP 8.2 — filterText() has already converted to
 * windows-1252 by this point, so widths measure correctly without it.
 */
function mf_fit_font($pdf, $family, $style, $text, $width, $startSize, $minSize, $maxLines) {
   $size      = $startSize;
   $effective = mf_effective_width($width);

   while ($size > $minSize) {
      $pdf->SetFont($family, $style, $size);

      /* Two conditions, both required:
       *
       * 1. No single word may be wider than the column. FPDF's MultiCell breaks an
       *    over-wide word CHARACTER BY CHARACTER — that is what turned
       *    "COMMUNICATIONS" into "COMMUNICATIO / NS". Shrinking until the longest word
       *    fits is what stops words being cut in half.
       * 2. The whole string must still wrap to no more than $maxLines.
       */
      if (mf_longest_word_width($pdf, $text) <= $effective
         && mf_count_lines($pdf, $text, $width) <= $maxLines) {
         return $size;
      }

      $size -= 1;
   }

   return $minSize;
}

/** Usable width inside a MultiCell of $width, i.e. less FPDF's cell margin on each side. */
function mf_effective_width($width) {
   return max(1, $width - 2 * FPDF_CELL_MARGIN);
}

/** Width of the widest single word at the current font. */
function mf_longest_word_width($pdf, $text) {
   $max = 0;

   foreach (preg_split('/\s+/', trim($text)) as $word) {
      if ($word === '') {
         continue;
      }
      $max = max($max, $pdf->GetStringWidth($word));
   }

   return $max;
}

/**
 * How many lines MultiCell() will wrap $text into at the current font and $width.
 *
 * FPDF reserves a cell margin on each side, so the usable width inside a MultiCell of $w is
 * $w - 2*cMargin. With the document in millimetres that margin is 1mm a side. Without this
 * subtraction a string measuring between 148 and 150mm counts as fitting here but wraps in
 * FPDF, and the fitted size comes out one line too large.
 */
function mf_count_lines($pdf, $text, $width) {
   $width = mf_effective_width($width);
   $lines = 0;

   foreach (explode("\n", $text) as $paragraph) {
      $words   = preg_split('/\s+/', trim($paragraph));
      $current = '';
      $lines++;

      foreach ($words as $word) {
         if ($word === '') {
            continue;
         }

         // A word wider than the column gets broken character by character by MultiCell.
         // Model that here, otherwise the count is short and the fitted size too large.
         // filterText() has already reduced the string to single-byte windows-1252, so
         // str_split() is safe.
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
 * Draw an image cropped to a circle, scaled to cover.
 *
 * Accepts a local path or a remote URL. Remote files are downloaded to a temp file because
 * the cover maths needs real pixel dimensions, and because FPDF only understands JPEG, PNG
 * and GIF — WebP and HEIC are converted first.
 *
 * $focus: center | top | bottom | left | right | topleft | topright | bottomleft | bottomright
 */
function mf_add_circular_image($pdf, $src, $circleX, $circleY, $circleR, $focus = 'center') {
   $local = mf_localize_image($src);
   if (!$local) {
      return false;
   }
   list($path, $ext, $isTemp) = $local;

   if (mf_image_too_big($path, $why)) {
      error_log("mf_add_circular_image: $why: $src");
      if ($isTemp) {
         @unlink($path);
      }
      return false;
   }

   $size = @getimagesize($path);

   list($img_w, $img_h) = $size;
   if (!$img_w || !$img_h) {
      if ($isTemp) {
         @unlink($path);
      }
      return false;
   }

   // FPDF only reads JPEG, PNG and GIF, and its PNG parser cannot handle 16-bit channels.
   // Checked here, on the local copy, rather than by re-fetching the remote URL.
   if (!in_array($size['mime'], array('image/jpeg', 'image/png', 'image/gif'), true)) {
      error_log("mf_add_circular_image: unsupported mime {$size['mime']} for $src");
      if ($isTemp) {
         @unlink($path);
      }
      return false;
   }
   if (isset($size['bits']) && $size['bits'] == 16) {
      error_log("mf_add_circular_image: 16-bit image not supported by FPDF: $src");
      if ($isTemp) {
         @unlink($path);
      }
      return false;
   }

   $diameter = $circleR * 2;

   // Scale to cover the circle
   $scale = max($diameter / $img_w, $diameter / $img_h);
   $new_w = $img_w * $scale;
   $new_h = $img_h * $scale;

   // Default: centred
   $offsetX = $circleX - ($new_w / 2);
   $offsetY = $circleY - ($new_h / 2);

   switch (strtolower($focus)) {
      case 'top':
         $offsetY = $circleY - $circleR;
         break;
      case 'bottom':
         $offsetY = $circleY - ($new_h - $circleR);
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
         break;
   }

   $placed = true;
   $pdf->ClippingCircle($circleX, $circleY, $circleR, false);
   try {
      $pdf->Image($path, $offsetX, $offsetY, $new_w, $new_h, strtoupper($ext));
   } catch (Exception $e) {
      // FPDF::Error() throws. Without the finally below, an exception here would leave the
      // clip path applied and everything drawn afterwards (the Makey, any later text) would
      // be clipped to this circle.
      error_log("mf_add_circular_image: FPDF failed to add image: " . $e->getMessage());
      $placed = false;
   } finally {
      $pdf->UnsetClipping();
   }

   if ($isTemp) {
      @unlink($path);
   }

   return $placed;
}

/**
 * Get a local, FPDF-readable copy of an image.
 * Returns array(path, extension, isTempFile) or false.
 */
function mf_localize_image($src) {
   if ($src === '') {
      return false;
   }

   // Already local?
   if (!preg_match('#^https?://#i', $src)) {
      if (!file_exists($src)) {
         error_log("mf_localize_image: local file missing: $src");
         return false;
      }
      $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
      return array($src, mf_normalize_ext($ext), false);
   }

   $response = wp_remote_get($src, array('timeout' => 30));
   if (is_wp_error($response)) {
      error_log("mf_localize_image: download failed for $src — " . $response->get_error_message());
      return false;
   }
   if ((int) wp_remote_retrieve_response_code($response) !== 200) {
      error_log("mf_localize_image: HTTP " . wp_remote_retrieve_response_code($response) . " for $src");
      return false;
   }

   $data = wp_remote_retrieve_body($response);
   if ($data === '') {
      error_log("mf_localize_image: empty body for $src");
      return false;
   }

   $finfo = new finfo(FILEINFO_MIME_TYPE);
   $mime  = $finfo->buffer($data);

   switch ($mime) {
      case 'image/jpeg': $ext = 'jpg';  break;
      case 'image/png':  $ext = 'png';  break;
      case 'image/gif':  $ext = 'gif';  break;
      case 'image/webp': $ext = 'webp'; break;
      case 'image/heic': $ext = 'heic'; break;
      default:
         error_log("mf_localize_image: unsupported image type $mime from $src");
         return false;
   }

   $tmpfile = tempnam(sys_get_temp_dir(), 'mf_sign_') . '.' . $ext;
   file_put_contents($tmpfile, $data);

   if (!file_exists($tmpfile) || filesize($tmpfile) === 0) {
      error_log("mf_localize_image: temp file missing/empty for $src");
      return false;
   }

   // GD and Imagick decode to a full w*h*4 bitmap, so an oversized source would blow up here
   // - before FPDF is ever reached. Check the converted-from file too, not just the output.
   if (($ext === 'webp' || $ext === 'heic') && mf_image_too_big($tmpfile, $whyConv)) {
      error_log("mf_localize_image: $ext source too large to convert - $whyConv: $src");
      @unlink($tmpfile);
      return false;
   }

   // FPDF understands JPEG, PNG and GIF only
   if ($ext === 'webp') {
      $im = @imagecreatefromwebp($tmpfile);
      if ($im) {
         $png = preg_replace('/\.webp$/', '.png', $tmpfile);
         imagepng($im, $png);
         imagedestroy($im);
         @unlink($tmpfile);
         $tmpfile = $png;
         $ext = 'png';
      } else {
         @unlink($tmpfile);
         error_log("mf_localize_image: webp conversion failed for $src");
         return false;
      }
   } elseif ($ext === 'heic') {
      try {
         $imagick = new Imagick($tmpfile);
         $png = preg_replace('/\.heic$/', '.png', $tmpfile);
         $imagick->setImageFormat('png');
         $imagick->writeImage($png);
         $imagick->clear();
         $imagick->destroy();
         @unlink($tmpfile);
         $tmpfile = $png;
         $ext = 'png';
      } catch (Exception $e) {
         @unlink($tmpfile);
         error_log("mf_localize_image: HEIC conversion failed for $src — " . $e->getMessage());
         return false;
      }
   }

   return array($tmpfile, $ext, true);
}

function mf_normalize_ext($ext) {
   $ext = strtolower($ext);
   return ($ext === 'jpeg') ? 'jpg' : $ext;
}

function filterText($text) {
   try {
      $string = iconv('UTF-8', 'windows-1252//IGNORE', $text);
   } catch (Exception $e) {
      error_log("Unable to convert $text due to: " . $e);
      ini_set('mbstring.substitute_character', "none");
      $string = mb_convert_encoding($text, 'UTF-8', 'windows-1252');
   }

   // now translate any unicode stuff...
   $conv = array(
       "&amp;"  => "&",
       "&#039;" => "'"
   );
   return strtr((string) $string, $conv);
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
