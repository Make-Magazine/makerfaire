<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class DCE_Extension_Form_PDF extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    public $has_action = \true;
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_pdf';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return '<span class="color-dce icon icon-dyn-logo-dce pull-right ml-1"></span> ' . __('PDF Generator', 'dynamic-content-for-elementor');
    }
    public function get_script_depends()
    {
        return [];
    }
    public function get_style_depends()
    {
        return [];
    }
    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_dce_form_pdf', ['label' => $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() && !current_user_can('administrator')) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="elementor-panel-alert elementor-panel-alert-warning">' . __('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor') . '</div>']);
            $widget->end_controls_section();
            return;
        }
        $widget->add_control('dce_form_pdf_converter', ['label' => __('Converter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'description' => __('Choose the converter that will generate the PDF.', 'dynamic-content-for-elementor'), 'options' => ['dompdf' => ['title' => __('DomPDF', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-paint-brush'], 'svg' => ['title' => __('SVG', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-fire']], 'toggle' => \false, 'default' => 'svg']);
        if (!\extension_loaded('imagick')) {
            $msg_txt = __(' The PHP extension <strong>imagick</strong> is missing but is highly recommended when creating the PDF from an SVG template. As a fallback only a limited subset of SVG is supported and the recommend editor is ', 'dynamic-content-for-elementor');
            $imagick_warning = <<<EOF
{$msg_txt}<a href="https://svgeditor.dynamic.ooo">Dynamic SVG Editor</a>
EOF;
            $widget->add_control('dce_form_pdf_missing_imagick', ['type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => $imagick_warning, 'separator' => 'before', 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['dce_form_pdf_converter' => 'svg']]);
        } else {
            $msg_txt = __('The SVG converter tries to use imagick for better results, but on some old system it might not work correctly, resulting in blank PDFs. If you have problems you can try to disable it. Please notice that if imagick is disabled you will have to use a simple SVG editor like this: ', 'dynamic-content-for-elementor');
            $imagick_warning = "{$msg_txt}<a href='https://svgeditor.dynamic.ooo'>Dynamic SVG Editor</a>";
            $widget->add_control('dce_form_pdf_disable_imagick', ['label' => __('Disable Imagick', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'description' => $imagick_warning, 'return_value' => 'disable', 'default' => 'enable', 'separator' => 'before', 'condition' => ['dce_form_pdf_converter' => 'svg']]);
        }
        $widget->add_control('dce_form_pdf_name', ['label' => __('Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[date|U]', 'description' => __('The PDF file name, the .pdf extension will automatically added', 'dynamic-content-for-elementor'), 'label_block' => \true, 'separator' => 'before']);
        $widget->add_control('dce_form_pdf_folder', ['label' => __('Folder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'elementor/pdf/[date|Y]/[date|m]', 'description' => __('The directory inside /wp-content/uploads/ where save the PDF file', 'dynamic-content-for-elementor'), 'label_block' => \true]);
        $svg_repeater = new \Elementor\Repeater();
        $svg_repeater->add_control('text', ['label' => __('SVG Page code', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'language' => 'svg', 'dynamic' => ['active' => \false]]);
        $widget->add_control('dce_form_pdf_svg_code_repeater', ['label' => __('PDF Pages', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'title_field' => 'Page', 'fields' => $svg_repeater->get_controls(), 'description' => __('The SVG template code that will be converted to PDF. One SVG per page. You can insert Tokens inside it.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_pdf_converter' => 'svg']]);
        $widget->add_control('dce_form_pdf_template', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => __('Use an Elementor Template as body for this PDF', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $paper_sizes = \array_keys(\DynamicOOOS\Dompdf\Adapter\CPDF::$PAPER_SIZES);
        $tmp = array();
        foreach ($paper_sizes as $asize) {
            $tmp[$asize] = \strtoupper($asize);
        }
        $paper_sizes = $tmp;
        $widget->add_control('dce_form_pdf_size', ['label' => __('Page Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'a4', 'options' => $paper_sizes, 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_orientation', ['label' => __('Page Orientation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['portrait' => ['title' => __('Portrait', 'dynamic-content-for-elementor'), 'icon' => 'fas fa-portrait'], 'landscape' => ['title' => __('Landscape', 'dynamic-content-for-elementor'), 'icon' => 'fas fa-tv']], 'toggle' => \false, 'default' => 'portrait', 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_margin', ['label' => __('Page Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_button_dpi', ['label' => __('DPI', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '96', 'options' => ['72' => '72', '96' => '96', '150' => '150', '200' => '200', '240' => '240', '300' => '300'], 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_section_page', ['label' => __('Sections Page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Force every Template Section in a new page', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_save', ['label' => __('Save PDF file as Media', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Form data by', 'dynamic-content-for-elementor') . ' [field id="name"] in [date|Y-m-d H:i:s]', 'description' => __('The PDF file Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['dce_form_pdf_save!' => '', 'dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_content', ['label' => __('Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[field id="message"]', 'description' => __('The PDF file Description', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['dce_form_pdf_save!' => '', 'dce_form_pdf_converter' => 'dompdf']]);
        //
        // $widget->add_control(
        // 	'dce_form_pdf_help',
        // 	[
        // 		'type' => \Elementor\Controls_Manager::RAW_HTML,
        // 		'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . __( 'Need Help', 'dynamic-content-for-elementor' ) . ' <i class="eicon-help-o"></i></a></div>',
        // 		'separator' => 'before',
        // 	]
        // );
        $widget->end_controls_section();
    }
    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run($record, $ajax_handler)
    {
        $fields = Helper::get_form_data($record);
        $settings = $record->get('form_settings');
        if ($settings['dce_form_pdf_converter'] === 'svg') {
            try {
                $this->svg_converter($settings, $record, $fields, $ajax_handler);
            } catch (\Throwable $e) {
                $ajax_handler->add_admin_error_message($e->getMessage());
            }
        } else {
            $settings = Helper::get_dynamic_value($settings, $fields);
            $this->dompdf_converter($settings, $fields, $ajax_handler);
        }
    }
    /**
     * On Export
     *
     * Clears form settings on export
     * @access Public
     * @param array $element
     */
    public function on_export($element)
    {
        $tmp = array();
        if (!empty($element)) {
            foreach ($element['settings'] as $key => $value) {
                if (\substr($key, 0, 4) == 'dce_') {
                    unset($element['settings'][$key]);
                }
            }
        }
    }
    /** Make sure the given dir is created and has protection files. */
    private static function ensure_dir($path)
    {
        if (\file_exists($path . '/index.php')) {
            return $path;
        }
        wp_mkdir_p($path);
        $files = [['file' => 'index.php', 'content' => ['<?php', '// Silence is golden.']], ['file' => '.htaccess', 'content' => ['Options -Indexes', '<ifModule mod_headers.c>', '	<Files *.*>', '       Header set Content-Disposition attachment', '	</Files>', '</IfModule>']]];
        foreach ($files as $file) {
            if (!\file_exists(trailingslashit($path) . $file['file'])) {
                $content = \implode(\PHP_EOL, $file['content']);
                @\file_put_contents(trailingslashit($path) . $file['file'], $content);
            }
        }
    }
    /** Get width, height and unit from svg root node. */
    private static function svg_get_dimensions(string $svg)
    {
        $svg = @\simplexml_load_string($svg);
        // try to get dimensions from width and height attrs:
        if (isset($svg['width'])) {
            \preg_match('/^([\\d\\.,]+)(\\S*)$/', $svg['width'], $matches);
            $width = $matches[1];
            $unit = $matches[2] ?: 'px';
            \preg_match('/^([\\d\\.,]+)(\\S*)$/', $svg['height'], $matches);
            $height = $matches[1];
        } elseif (isset($svg['viewBox'])) {
            // no luck, try with the viewBox attr:
            \preg_match('/^\\s*[\\d\\.,]+\\s+[\\d\\.,]+\\s+([\\d\\.,]+)\\s+([\\d\\.,]+)$/', $svg['viewBox'], $matches);
            $width = $matches[1];
            $height = $matches[2];
            $unit = 'px';
        } else {
            // fallback values (a4 paper):
            $width = 210;
            $unit = 'mm';
            $height = 297;
        }
        // This is to match what Illustrator considers as pixel, which is
        // not the same as tcpdf.
        if ('px' === $unit) {
            $width = $width / 2.834762;
            $height = $height / 2.834762;
            $unit = 'mm';
        }
        return ['unit' => $unit, 'width' => $width, 'height' => $height];
    }
    /** Set svg root width and height to the given values */
    private static function svg_set_width_height(string $svg, $dim)
    {
        $svg = @\simplexml_load_string($svg);
        if ($svg === \false) {
            return \false;
        }
        $svg['width'] = $dim['width'] . $dim['unit'];
        $svg['height'] = $dim['height'] . $dim['unit'];
        return $svg->asXML();
    }
    /**
     * Method ac editor seems to output non standard null values, remove
     * them.
     */
    private static function svg_fix_for_methodac(string $svg)
    {
        return \preg_replace('/[\\w-]+="null"/', '', $svg);
    }
    /**
     * Gets an array of SVG strings. Returns them converted to one pdf as a
     * string.
     */
    private static function svg_converter_convert(array $svgs, $disable_imagick)
    {
        $use_imagick = !$disable_imagick && \extension_loaded('imagick');
        // The unit will be the same for all pages. Get it from the first one.
        $dim = self::svg_get_dimensions($svgs[0]);
        $unit = $dim['unit'];
        $pdf = new \DynamicOOOS\TCPDF('P', $unit, 'A4', \true, 'UTF-8', \false);
        $pdf->setPrintHeader(\false);
        $pdf->setPrintFooter(\false);
        // Prevent TCPDF from automatically adding other pages:
        $pdf->SetAutoPageBreak(\false, PDF_MARGIN_BOTTOM);
        if ($use_imagick) {
            $pdf->setRasterizeVectorImages(\true);
        }
        foreach ($svgs as $svg) {
            $dim = self::svg_get_dimensions($svg);
            if ($use_imagick) {
                // The following is necessary in case of missing width and
                // height there could be a mismatch between imagick and tcpdf
                // in terms of resolution.
                $svg = self::svg_set_width_height($svg, $dim);
                if ($svg === \false) {
                    return \false;
                }
            } else {
                $svg = self::svg_fix_for_methodac($svg);
            }
            $width = $dim['width'];
            $height = $dim['height'];
            $orientation = $width >= $height ? 'L' : 'P';
            $pdf->AddPage($orientation, [$width, $height]);
            $pdf->ImageSVG('@' . \trim($svg), 0, 0, $width, $height);
        }
        return $pdf->Output('', 'S');
    }
    /**
     * Look for elements (should be rectangles) that have an SVG id like
     * "form:name". Check if name it corresponds to a form field with a
     * dataURL image inside (a signature).  If so replace the element with
     * the acutual image, it should be in the same place and with the same
     * size.
     */
    private static function replace_template_images($svg, $fields)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($svg);
        $xpath = new \DOMXpath($dom);
        $els = $xpath->query('//*');
        foreach ($els as $el) {
            $el_id = $el->getAttribute('id');
            if (\preg_match('/^form:(\\S+)/', $el_id, $matches)) {
                if (isset($fields[$matches[1]])) {
                    $dataURL = $fields[$matches[1]]['raw_value'];
                    // Replace the rect element with a new image element in the
                    // correct position, by reading position information and
                    // copying it.
                    $x = $el->getAttribute('x');
                    $y = $el->getAttribute('y');
                    $width = $el->getAttribute('width');
                    $height = $el->getAttribute('height');
                    $img = $dom->createElement('image');
                    $img->setAttribute('xlink:href', $dataURL);
                    $img->setAttribute('x', $x);
                    $img->setAttribute('y', $y);
                    $img->setAttribute('width', $width);
                    $img->setAttribute('height', $height);
                    $img->setAttribute('preserveAspectRation', 'none');
                    $el->parentNode->replaceChild($img, $el);
                }
            }
        }
        return $dom->saveXML();
    }
    private function svg_converter($settings, $record, $fields, $ajax_handler)
    {
        global $dce_form;
        $dir_rel_path = Helper::get_dynamic_value($settings['dce_form_pdf_folder'], $fields);
        $upload_dir = wp_upload_dir();
        $dir_abs_path = trailingslashit($upload_dir['basedir']) . $dir_rel_path;
        self::ensure_dir($dir_abs_path);
        $dir_url = trailingslashit($upload_dir['baseurl']) . $dir_rel_path;
        $file_name = Helper::get_dynamic_value($settings['dce_form_pdf_name'], $fields);
        $file_name = $file_name . '.pdf';
        $file_path = trailingslashit($dir_abs_path) . $file_name;
        $dce_form['pdf']['path'] = $file_path;
        $dce_form['pdf']['url'] = trailingslashit($dir_url) . $file_name;
        $svgs = $settings['dce_form_pdf_svg_code_repeater'];
        if (empty($svgs)) {
            $msg = __('PDF not generated, no SVG pages found.', 'dynamic-content-for-elementor');
            $ajax_handler->add_error_message($msg);
            return;
        }
        $tfun = function ($svg) use($record, $fields) {
            $svg = self::replace_template_images($svg['text'], $record->get('fields'));
            return Helper::get_dynamic_value($svg, $fields);
        };
        $svgs = \array_map($tfun, $svgs);
        $raw_pdf = self::svg_converter_convert($svgs, ($settings['dce_form_pdf_disable_imagick'] ?? '') === 'disable');
        if ($raw_pdf === \false) {
            $ajax_handler->add_admin_error_message('PDF: invalid SVG code.');
        }
        \file_put_contents($file_path, $raw_pdf);
    }
    public function dompdf_converter($settings, $fields, $ajax_handler = null)
    {
        global $dce_form, $post;
        if (empty($settings['dce_form_pdf_template'])) {
            $ajax_handler->add_error_message(__('Error: PDF Template not found or not set', 'dynamic-content-for-elementor'));
            return;
        }
        // verify Template
        $template = get_post($settings['dce_form_pdf_template']);
        if (!$template || $template->post_type != 'elementor_library') {
            $ajax_handler->add_error_message(__('Error: PDF Template not set correctly', 'dynamic-content-for-elementor'));
            return;
        }
        $post = get_post($fields['submitted_on_id']);
        // to retrieve dynamic data from post where the form was submitted
        $pdf_folder = '/' . $settings['dce_form_pdf_folder'] . '/';
        $upload = wp_upload_dir();
        $pdf_dir = $upload['basedir'] . $pdf_folder;
        $pdf_url = $upload['baseurl'] . $pdf_folder;
        $pdf_name = $settings['dce_form_pdf_name'] . '.pdf';
        $dce_form['pdf']['path'] = $pdf_dir . $pdf_name;
        $dce_form['pdf']['url'] = $pdf_url . $pdf_name;
        $pdf_html = do_shortcode('[dce-elementor-template id="' . $settings['dce_form_pdf_template'] . '"]');
        $pdf_html = Helper::get_dynamic_value($pdf_html, $fields);
        // add CSS
        $css = Helper::get_post_css($settings['dce_form_pdf_template']);
        // from flex to table
        $css .= '.elementor-section .elementor-container { display: table !important; width: 100% !important; }';
        $css .= '.elementor-row { display: table-row !important; }';
        $css .= '.elementor-column { display: table-cell !important; }';
        $css .= '.elementor-column-wrap, .elementor-widget-wrap { display: block !important; }';
        $css = \str_replace(':not(.elementor-motion-effects-element-type-background) > .elementor-element-populated', ':not(.elementor-motion-effects-element-type-background)', $css);
        $css .= '.elementor-column .elementor-widget-image img { max-width: none !important; }';
        $cssToInlineStyles = new \DynamicOOOS\TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
        $pdf_html = $cssToInlineStyles->convert($pdf_html, $css);
        $pdf_html = Helper::template_unwrap($pdf_html);
        // link image from url to path
        $site_url = site_url();
        if (is_rtl()) {
            // fix for arabic and hebrew
            $pdf_html .= '<style>* { font-family: DejaVu Sans, sans-serif; }</style>';
        }
        if (!empty($settings['dce_form_section_page'])) {
            $pdf_html .= '<style>.elementor-top-section { page-break-before: always; }.elementor-top-section:first-child { page-break-before: no; }</style>';
        }
        if (!empty($settings['dce_form_pdf_background'])) {
            $bg_path = get_attached_file($settings['dce_form_pdf_background']);
            $pdf_html .= '<style>body { background-image: url("' . $bg_path . '"); }</style>';
            $pdf_html .= '<style>body { background-repeat: no-repeat; background-position: center; background-size: cover; }</style>';
        }
        $pdf_html .= '<style>@page { margin: ' . $settings['dce_form_pdf_margin']['top'] . $settings['dce_form_pdf_margin']['unit'] . ' ' . $settings['dce_form_pdf_margin']['right'] . $settings['dce_form_pdf_margin']['unit'] . ' ' . $settings['dce_form_pdf_margin']['bottom'] . $settings['dce_form_pdf_margin']['unit'] . ' ' . $settings['dce_form_pdf_margin']['left'] . $settings['dce_form_pdf_margin']['unit'] . '; }</style>';
        if (!\is_dir($pdf_dir)) {
            \mkdir($pdf_dir, 0755, \true);
        }
        // Add to the directory an empty index.php
        if (!\is_file($pdf_dir . 'index.php')) {
            $phpempty = "<?php\n//Silence is golden.\n";
            \file_put_contents($pdf_dir . 'index.php', $phpempty);
        }
        // https://github.com/dompdf/dompdf
        $context = \stream_context_create(array('ssl' => array('verify_peer' => \false, 'verify_peer_name' => \false)));
        $options = new \DynamicOOOS\Dompdf\Options();
        $options->set('isRemoteEnabled', \true);
        $options->setIsRemoteEnabled(\true);
        // instantiate and use the dompdf class
        $dompdf = new \DynamicOOOS\Dompdf\Dompdf($options);
        $dompdf->setHttpContext($context);
        $dompdf->loadHtml($pdf_html);
        $dompdf->set_option('isRemoteEnabled', \true);
        $dompdf->set_option('isHtml5ParserEnabled', \true);
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper($settings['dce_form_pdf_size'], $settings['dce_form_pdf_orientation']);
        // DPI
        $dompdf->set_option('dpi', $settings['dce_form_pdf_button_dpi']);
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $output = $dompdf->output();
        if (!\file_put_contents($pdf_dir . $pdf_name, $output)) {
            $ajax_handler->add_error_message(__('Error generating PDF', 'dynamic-content-for-elementor'));
        }
        if ($settings['dce_form_pdf_save']) {
            // Insert the post into the database
            // https://codex.wordpress.org/Function_Reference/wp_insert_attachment
            // $filename should be the path to a file in the upload directory.
            $filename = $dce_form['pdf']['path'];
            // The ID of the post this attachment is for.
            $parent_post_id = $fields['submitted_on_id'];
            // Check the type of file. We'll use this as the 'post_mime_type'.
            $filetype = wp_check_filetype(\basename($filename), null);
            // Get the path to the upload directory.
            $wp_upload_dir = wp_upload_dir();
            // Prepare an array of post data for the attachment.
            $attachment = array('guid' => $wp_upload_dir['url'] . '/' . \basename($filename), 'post_mime_type' => $filetype['type'], 'post_status' => 'inherit', 'post_title' => $settings['dce_form_pdf_title'], 'post_content' => $settings['dce_form_pdf_content']);
            // Insert the attachment.
            $attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);
            // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
            require_once ABSPATH . 'wp-admin/includes/image.php';
            // Generate the metadata for the attachment, and update the database record.
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);
            if ($attach_id) {
                $dce_form['pdf']['id'] = $attach_id;
                $dce_form['pdf']['title'] = $settings['dce_form_pdf_title'];
                $dce_form['pdf']['description'] = $settings['dce_form_pdf_content'];
                if (!empty($fields) && \is_array($fields)) {
                    foreach ($fields as $akey => $adata) {
                        update_post_meta($attach_id, $akey, $adata);
                    }
                }
            } else {
                $ajax_handler->add_error_message(__('Error saving PDF as Media', 'dynamic-content-for-elementor'));
            }
        }
    }
}
