<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class DCE_Widget_RemoteContent extends \DynamicContentForElementor\Widgets\DCE_Widget_Prototype
{
    public function show_in_panel()
    {
        if (!current_user_can('administrator')) {
            return \false;
        }
        return \true;
    }
    protected function _register_controls()
    {
        if (current_user_can('administrator') || !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->register_controls_content();
        } elseif (!current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->register_controls_non_admin_notice();
        }
    }
    protected function register_controls_content()
    {
        $this->start_controls_section('section_remotecontent', ['label' => __('Remote Content', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('url', ['label' => __('Page URL', 'dynamic-content-for-elementor'), 'description' => __('The full URL of the page to include', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_control('incorporate', ['label' => __('Incorporate in the page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'description' => __('Insert remote content in page html or simply add as iframe.', 'dynamic-content-for-elementor'), 'condition' => ['url!' => '']]);
        $this->add_control('require_authorization', ['label' => __('Require Authorization', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['url!' => '', 'incorporate!' => '']]);
        $this->add_control('authorization_header', ['label' => __('Authorization Header', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'placeholder' => 'Authorization: Bearer <token>', 'rows' => '1', 'condition' => ['require_authorization' => 'yes', 'url!' => '']]);
        $this->add_control('authorization_user', ['label' => __('Basic HTTP User', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['require_authorization' => 'yes', 'url!' => '']]);
        $this->add_control('authorization_pass', ['label' => __('Basic HTTP Password', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['require_authorization' => 'yes', 'url!' => ''], 'separator' => 'after']);
        $this->add_control('connect_timeout', ['label' => __('Connection Timeout', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 5, 'description' => __('Max time in seconds your server can wait for a response from target server.', 'dynamic-content-for-elementor'), 'condition' => ['incorporate!' => '', 'url!' => '']]);
        $this->add_control('iframe_doc', ['label' => __('Use Google Document preview', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['incorporate' => '', 'url!' => '']]);
        $this->add_responsive_control('iframe_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '80', 'unit' => 'vh'], 'range' => ['px' => ['min' => 0, 'max' => 1920, 'step' => 1], '%' => ['min' => 5, 'max' => 100, 'step' => 1], 'vh' => ['min' => 5, 'max' => 100, 'step' => 1]], 'size_units' => ['%', 'px', 'vh'], 'selectors' => ['{{WRAPPER}} iframe' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['incorporate' => '', 'url!' => '']]);
        $this->add_control('data_json', ['label' => __('Data is JSON formatted', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['incorporate!' => '', 'url!' => '']]);
        $this->add_control('tag_id', ['label' => __('Tag, ID or Class', 'dynamic-content-for-elementor'), 'description' => __('To include only subcontent of remote page. Use like jQuery selector (footer, #element, h2.big, etc).', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'body', 'default' => 'body', 'condition' => ['incorporate!' => '', 'url!' => '', 'data_json' => '']]);
        $this->add_control('limit_tags', ['label' => __('Limit elements', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'description' => __('Set -1 for unlimited', 'dynamic-content-for-elementor'), 'default' => -1, 'condition' => ['incorporate!' => '', 'url!' => '', 'data_json' => '']]);
        $this->add_control('data_template', ['label' => __('Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => '<div class="dce-remote-content"><h3 class="dce-remote-content-title">[DATA:title:rendered]</h3><div class="dce-remote-content-body">[DATA:excerpt:rendered]</div><a class="btn btn-primary" href="[DATA:link]">Read more</a></div>', 'description' => __('Add a specific format to data elements. Use Tokens to represent JSON fields.', 'dynamic-content-for-elementor'), 'condition' => ['incorporate!' => '', 'url!' => '', 'data_json' => 'yes']]);
        $this->add_control('single_or_archive', ['label' => __('Single or Archive', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Archive', 'dynamic-content-for-elementor'), 'label_on' => __('Single', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['incorporate!' => '', 'url!' => '', 'data_json' => 'yes']]);
        $this->add_control('archive_path', ['label' => __('Archive Array path', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'description' => __('Leave empty if JSON result is a direct array (like in WP API). For web services usually might use "results". You can browse sub arrays separate them by comma like "data.people"', 'dynamic-content-for-elementor'), 'condition' => ['incorporate!' => '', 'url!' => '', 'data_json' => 'yes', 'single_or_archive' => '']]);
        $this->add_control('limit_contents', ['label' => __('Limit elements', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'description' => __('Set -1 for unlimited', 'dynamic-content-for-elementor'), 'default' => -1, 'condition' => ['incorporate!' => '', 'url!' => '', 'single_or_archive' => '']]);
        $this->add_control('offset_contents', ['label' => __('Start from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'description' => __('0 or empty to start from the first', 'dynamic-content-for-elementor'), 'default' => -1, 'condition' => ['incorporate!' => '', 'url!' => '', 'single_or_archive' => '']]);
        $this->add_control('data_cache', ['label' => __('Cache', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('If linked sites are slow or not reachable is better to enable cache. To force the refresh, disable, save and re-enable it.', 'dynamic-content-for-elementor'), 'condition' => ['incorporate!' => '', 'url!' => '']]);
        $this->add_control('data_cache_maxage', ['label' => __('Cache Max-age', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 86400, 'description' => __('How long cache is valid? Set it in seconds. (86400 seconds is a day)', 'dynamic-content-for-elementor'), 'condition' => ['data_cache!' => '', 'url!' => '', 'incorporate!' => '']]);
        $this->add_control('data_cache_refresh', ['label' => __('Last time cache was rebuilt', 'dynamic-content-for-elementor'), 'default' => '', 'type' => Controls_Manager::TEXT, 'description' => '<style>.elementor-control-data_cache_refresh{display:none !important;}</style>', 'condition' => ['data_cache!' => '', 'url!' => '', 'incorporate!' => '']]);
        $this->add_control('data_cache_content', ['label' => __('Cache content', 'dynamic-content-for-elementor'), 'description' => '<style>.elementor-control-data_cache_content{display:none !important;}</style>', 'type' => Controls_Manager::TEXTAREA, 'default' => '', 'condition' => ['data_cache!' => '', 'url!' => '', 'incorporate!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_html_manipulation', ['label' => __('HTML Manipulation', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['incorporate!' => '', 'url!' => '']]);
        $this->add_control('fix_links', ['label' => __('Fix Relative links', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Enable if remote page contains relative links', 'dynamic-content-for-elementor')]);
        $this->add_control('blank_links', ['label' => __('Target Blank links', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Open links in a new page.', 'dynamic-content-for-elementor')]);
        $this->add_control('lazy_images', ['label' => __('Fix Lazy Images src', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Show lazy images without using specific JS', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode() && empty($settings['url'])) {
            _e('Add remote url to begin', 'dynamic-content-for-elementor');
        } elseif (!current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->render_non_admin_notice();
        } elseif (current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode() && !empty($settings['url']) || !is_admin() && !empty($settings['url'])) {
            $url = $settings['url'];
            if (\filter_var($url, \FILTER_VALIDATE_URL)) {
                if ($settings['incorporate']) {
                    $pageHtml = $this->getCache();
                    $context = null;
                    if ($settings['require_authorization']) {
                        if ($settings['authorization_header']) {
                            $context = \stream_context_create(array('http' => array('header' => $settings['authorization_header'])));
                        }
                    }
                    if (!$pageHtml) {
                        $pageHtml = self::file_get_contents($url, \false, $context, $settings);
                    }
                    if ($pageHtml !== \false && !is_wp_error($pageHtml)) {
                        $pageBody = $pageHtml;
                        if ($settings['data_json']) {
                            $jsonData = \json_decode($pageBody, \true);
                            $pageBody = array();
                            if ($settings['single_or_archive']) {
                                $pageBody[] = $this->replaceTemplateTokens(Helper::get_dynamic_value($settings['data_template']), $jsonData);
                            } else {
                                $jsonDataArchive = $jsonData;
                                if (isset($settings['archive_path']) && $settings['archive_path']) {
                                    $settings['archive_path'] = \str_replace('.', ':', $settings['archive_path']);
                                    $pezzi = \explode(':', $settings['archive_path']);
                                    $tmp_val = \DynamicContentForElementor\Helper::get_array_value_by_keys($jsonData, $pezzi);
                                    if ($tmp_val) {
                                        $jsonDataArchive = $tmp_val;
                                    }
                                }
                                if (!empty($jsonDataArchive)) {
                                    foreach ($jsonDataArchive as $aJsonData) {
                                        $pageBody[] = $this->replaceTemplateTokens(Helper::get_dynamic_value($settings['data_template']), $aJsonData);
                                    }
                                }
                            }
                        } elseif ($settings['tag_id']) {
                            $crawler = new \DynamicOOOS\Symfony\Component\DomCrawler\Crawler($pageHtml);
                            $pageBody = array();
                            $pageBody = $crawler->filter($settings['tag_id'])->each(function (\DynamicOOOS\Symfony\Component\DomCrawler\Crawler $node, $i) {
                                return $node->html();
                            });
                            if (isset($settings['limit_tags']) && $settings['limit_tags'] > 0 && \count($pageBody) > $settings['limit_tags']) {
                                $pageBody = \array_slice($pageBody, 0, $settings['limit_tags']);
                            }
                        } else {
                            $pageBody = array($pageBody);
                        }
                        $host = '';
                        if (isset($settings['fix_links']) && $settings['fix_links']) {
                            $pezzi = \explode('/', $settings['url'], 4);
                            \array_pop($pezzi);
                            $host = \implode('/', $pezzi);
                        }
                        echo '<div class="dynamic-remote-content">';
                        $showed = 0;
                        foreach ($pageBody as $key => $aElem) {
                            if ($settings['limit_contents'] <= 0 || $showed <= $settings['limit_contents']) {
                                if ($key >= $settings['offset_contents']) {
                                    echo '<div class="dynamic-remote-content-element">';
                                    if (isset($settings['fix_links']) && $settings['fix_links']) {
                                        $aElem = \str_replace('href="/', 'href="' . $host . '/', $aElem);
                                    }
                                    if (isset($settings['lazy_images']) && $settings['lazy_images']) {
                                        $imgs = \explode('<img ', $aElem);
                                        foreach ($imgs as $ikey => $aimg) {
                                            if (\strpos($aimg, 'data-lazy-src') !== \false) {
                                                $imgs[$ikey] = \str_replace(' src="', 'data-placeholder-src="', $imgs[$ikey]);
                                                $imgs[$ikey] = \str_replace('data-lazy-src="', 'src="', $imgs[$ikey]);
                                                $imgs[$ikey] = \str_replace('data-lazy-srcset="', 'srcset="', $imgs[$ikey]);
                                                $imgs[$ikey] = \str_replace('data-lazy-sizes="', 'sizes="', $imgs[$ikey]);
                                            }
                                        }
                                        $aElem = \implode('<img ', $imgs);
                                    }
                                    if (isset($settings['blank_links']) && $settings['blank_links']) {
                                        $anchors = \explode('<a ', $aElem);
                                        foreach ($anchors as $akey => $anchor) {
                                            if (\strpos($anchor, ' target="_') !== \false) {
                                                $anchors[$akey] = 'target="_blank" ' . $anchors[$akey];
                                            }
                                        }
                                        $aElem = \implode('<a ', $anchors);
                                    }
                                    echo $aElem;
                                    echo '</div>';
                                }
                                $showed++;
                            }
                        }
                        echo '</div>';
                    } else {
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                            _e('Can\'t fetch remote content. Please check url', 'dynamic-content-for-elementor');
                        }
                    }
                } else {
                    // view as simple iframe
                    if ($settings['iframe_doc']) {
                        $url = 'https://docs.google.com/viewer?embedded=true&url=' . \urlencode($url);
                    }
                    echo '<iframe src="' . $url . '" frameborder="0" width="100%" height="' . $settings['iframe_height']['size'] . '"></iframe>';
                }
            } else {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    _e('The url is not valid', 'dynamic-content-for-elementor');
                }
            }
        }
    }
    public function replaceTemplateTokens($text, $content)
    {
        $text = \DynamicContentForElementor\Tokens::replace_var_tokens($text, 'DATA', $content);
        $pezzi = \explode('[', $text);
        if (\count($pezzi) > 1) {
            foreach ($pezzi as $key => $avalue) {
                if ($key) {
                    $pezzo = \explode(']', $avalue);
                    $metaParams = \reset($pezzo);
                    $optionParams = \explode('.', $metaParams);
                    $fieldName = $optionParams[0];
                    $optionValue = isset($content[$fieldName]) ? $content[$fieldName] : '';
                    $replaceValue = $this->checkArrayValue($optionValue, $optionParams);
                    $text = \str_replace('[' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    private function checkArrayValue($optionValue = array(), $optionParams = array())
    {
        if (\is_array($optionValue)) {
            if (\count($optionValue) == 1) {
                $tmpValue = \reset($optionValue);
                if (!\is_array($tmpValue)) {
                    return $tmpValue;
                }
            }
            if (\is_array($optionParams)) {
                $val = $optionValue;
                foreach ($optionParams as $key => $value) {
                    if (isset($val[$value])) {
                        $val = $val[$value];
                    }
                }
                if (\is_array($val)) {
                    $val = \var_export($val, \true);
                }
                return $val;
            }
            if ($optionParams) {
                return $optionValue[$optionParams];
            }
            return \var_export($optionValue, \true);
        }
        return $optionValue;
    }
    private static function file_get_contents_file($url)
    {
        $content = \false;
        // using file() function to get content
        if ($lines_array = @\file($url)) {
            // turn array into one variable
            $content = \implode('', $lines_array);
            //output, you can also save it locally on the server
        }
        return $content;
    }
    private static function file_get_contents_curl($url, $curl_timeout, $opts, $settings = array())
    {
        $content = \false;
        if (\function_exists('curl_init')) {
            $curl = \curl_init();
            \curl_setopt($curl, \CURLOPT_RETURNTRANSFER, 1);
            \curl_setopt($curl, \CURLOPT_URL, $url);
            \curl_setopt($curl, \CURLOPT_CONNECTTIMEOUT, 5);
            \curl_setopt($curl, \CURLOPT_TIMEOUT, $curl_timeout);
            \curl_setopt($curl, \CURLOPT_SSL_VERIFYPEER, \false);
            \curl_setopt($curl, \CURLOPT_SSL_VERIFYHOST, \false);
            \curl_setopt($curl, \CURLOPT_FOLLOWLOCATION, \true);
            \curl_setopt($curl, \CURLOPT_MAXREDIRS, 5);
            if (!empty($settings['require_authorization'])) {
                if (!empty($settings['authorization_user']) && !empty($settings['authorization_pass'])) {
                    \curl_setopt($curl, \CURLOPT_USERPWD, $settings['authorization_user'] . ':' . $settings['authorization_pass']);
                    \curl_setopt($curl, \CURLOPT_HTTPAUTH, \CURLAUTH_BASIC);
                }
            }
            if ($opts != null) {
                if (isset($opts['http']['method']) && \strtolower($opts['http']['method']) == 'post') {
                    \curl_setopt($curl, \CURLOPT_POST, \true);
                    if (isset($opts['http']['content'])) {
                        \parse_str($opts['http']['content'], $post_data);
                        \curl_setopt($curl, \CURLOPT_POSTFIELDS, $post_data);
                    }
                }
            }
            $content = \curl_exec($curl);
            \curl_close($curl);
        }
        return $content;
    }
    private static function file_get_contents_fopen($url)
    {
        $content = \false;
        //fopen opens webpage in Binary
        if ($handle = @\fopen($url, 'rb')) {
            // initialize
            $content = '';
            // read content line by line
            do {
                $data = \fread($handle, 1024);
                if (\strlen($data) == 0) {
                    break;
                }
                $content .= $data;
            } while (\true);
            //close handle to release resources
            \fclose($handle);
            //output, you can also save it locally on the server
            echo $content;
        }
        return $content;
    }
    /**
     * This method allows to get the content from either a URL or a local file
     * @param string $url the url to get the content from
     * @param bool $use_include_path second parameter of http://php.net/manual/en/function.file-get-contents.php
     * @param resource $stream_context third parameter of http://php.net/manual/en/function.file-get-contents.php
     * @param int $curl_timeout
     * @param bool $fallback whether or not to use the fallback if the main solution fails
     * @return bool|string false or the string content
     */
    public static function file_get_contents($url, $use_include_path = \false, $stream_context = null, $settings = array(), $fallback = \false)
    {
        $curl_timeout = !empty($settings['connect_timeout']) ? $settings['connect_timeout'] : 5;
        $is_local_file = !\preg_match('/^https?:\\/\\//', $url);
        $require_fopen = \false;
        $opts = null;
        if ($stream_context) {
            $opts = \stream_context_get_options($stream_context);
            if (isset($opts['http'])) {
                $require_fopen = \true;
                $opts_layer = \array_diff_key($opts, array('http' => null));
                $http_layer = \array_diff_key($opts['http'], array('method' => null, 'content' => null));
                if (empty($opts_layer) && empty($http_layer)) {
                    $require_fopen = \false;
                }
            }
        } elseif (!$is_local_file) {
            $stream_context = @\stream_context_create(array('http' => array('timeout' => $curl_timeout), 'ssl' => array('verify_peer' => \false, 'verify_peer_name' => \false)));
        }
        $content = \false;
        if (\in_array(\ini_get('allow_url_fopen'), array('On', 'on', '1'))) {
            $content = @\file_get_contents($url, $use_include_path, $stream_context);
        }
        if (!$content) {
            $content = self::file_get_contents_file($url);
        }
        if (!$content) {
            $content = self::file_get_contents_fopen($url);
        }
        if (!$content) {
            $content = self::file_get_contents_curl($url, $curl_timeout, $opts, $settings);
        }
        return $content;
    }
    public function getCache()
    {
        $wCache = \false;
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $settings = $this->get_settings_for_display();
            if ($settings['data_cache']) {
                $lastRenew = (int) $settings['data_cache_refresh'];
                if ($lastRenew + $settings['data_cache_maxage'] < \time() || !$settings['data_cache_content']) {
                    // cache refresh
                    $wCache = self::file_get_contents($settings['url'], \false, null, $settings['connect_timeout']);
                    if ($wCache) {
                        $wCachePrepared = \base64_encode($wCache);
                        $this->update_settings('data_cache_content', $wCachePrepared);
                        $this->update_settings('data_cache_refresh', \time());
                    }
                } else {
                    $wCache = \stripslashes($settings['data_cache_content']);
                    $wCache = \base64_decode($wCache);
                }
            }
        }
        return $wCache;
    }
}
