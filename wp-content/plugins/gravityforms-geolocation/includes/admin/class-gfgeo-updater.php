<?php if (!defined('ABSPATH'))
{
    exit;
}
if (!is_admin())
{
    return;
}
class GFGEO_Updater
{
    private $prefix = 'gfgeo';
    private $api_url = '';
    private $api_data = array();
    private $plugin_file = '';
    private $name = '';
    private $slug = '';
    private $version = '';
    private $wp_override = false;
    private $beta = false;
    private $failed_request_cache_key;
    public function __construct($_api_url, $_plugin_file, $_api_data = null)
    {
        global $geo_plugin_data;
        $this->api_url = trailingslashit($_api_url);
        $this->api_data = $_api_data;
        $this->plugin_file = $_plugin_file;
        $this->name = plugin_basename($_plugin_file);
        $this->slug = basename($_plugin_file, '.php');
        $this->version = $_api_data['version'];
        $this->wp_override = isset($_api_data['wp_override']) ? (bool)$_api_data['wp_override'] : false;
        $this->beta = !empty($this->api_data['beta']) ? true : false;
        $this->failed_request_cache_key = $this->prefix . '_failed_http_' . md5($this->api_url);
        $geo_plugin_data[$this
            ->slug] = $this->api_data;
        do_action('post_' . $this->prefix . '_plugin_updater_setup', $geo_plugin_data);
        $this->init();
    }
    public function init()
    {
        add_filter('pre_set_site_transient_update_plugins', array(
            $this,
            'check_update'
        ));
        add_filter('plugins_api', array(
            $this,
            'plugins_api_filter'
        ) , 10, 3);
        add_action('after_plugin_row', array(
            $this,
            'show_update_notification'
        ) , 10, 2);
        add_action('admin_init', array(
            $this,
            'show_changelog'
        ));
    }
    public function check_update($_transient_data)
    {
        global $pagenow;
        if (!is_object($_transient_data))
        {
            $_transient_data = new stdClass();
        }
        if (!empty($_transient_data->response) && !empty($_transient_data->response[$this
            ->name]) && false === $this->wp_override)
        {
            return $_transient_data;
        }
        $current = $this->get_repo_api_data();
        if (false !== $current && is_object($current) && isset($current->new_version))
        {
            if (version_compare($this->version, $current->new_version, '<'))
            {
                $_transient_data->response[$this->name] = $current;
            }
            else
            {
                $_transient_data->no_update[$this->name] = $current;
            }
        }
        $_transient_data->last_checked = time();
        $_transient_data->checked[$this
            ->name] = $this->version;
        return $_transient_data;
    }
    public function get_repo_api_data()
    {
        $version_info = $this->get_cached_version_info();
        if (false === $version_info)
        {
            $version_info = $this->api_request('plugin_latest_version', array(
                'slug' => $this->slug,
                'beta' => $this->beta,
            ));
            if (!$version_info)
            {
                return false;
            }
            $version_info->plugin = $this->name;
            $version_info->id = $this->name;
            $this->set_version_info_cache($version_info);
        }
        return $version_info;
    }
    public function show_update_notification($file, $plugin)
    {
        if (is_network_admin() || !is_multisite())
        {
            return;
        }
        if (!current_user_can('activate_plugins'))
        {
            return;
        }
        if ($this->name !== $file)
        {
            return;
        }
        $update_cache = get_site_transient('update_plugins');
        if (!isset($update_cache->response[$this->name]))
        {
            if (!is_object($update_cache))
            {
                $update_cache = new stdClass();
            }
            $update_cache->response[$this
                ->name] = $this->get_repo_api_data();
        }
        if (empty($update_cache->response[$this
            ->name]) || version_compare($this->version, $update_cache->response[$this
            ->name]->new_version, '>='))
        {
            return;
        }
        printf('<tr class="plugin-update-tr %3$s" id="%1$s-update" data-slug="%1$s" data-plugin="%2$s">', $this->slug, $file, in_array($this->name, $this->get_active_plugins() , true) ? 'active' : 'inactive');
        echo '<td colspan="3" class="plugin-update colspanchange">';
        echo '<div class="update-message notice inline notice-warning notice-alt"><p>';
        $changelog_link = '';
        if (!empty($update_cache->response[$this
            ->name]
            ->sections
            ->changelog) || !empty(!empty($update_cache->response[$this
            ->name]
            ->sections['changelog'])))
        {
            $changelog_link = add_query_arg(array(
                $this->prefix . '_action' => 'view_plugin_changelog',
                'plugin' => urlencode($this->name) ,
                'slug' => urlencode($this->slug) ,
                'TB_iframe' => 'true',
                'width' => 77,
                'height' => 911,
            ) , self_admin_url('index.php'));
        }
        $update_link = add_query_arg(array(
            'action' => 'upgrade-plugin',
            'plugin' => urlencode($this->name) ,
        ) , self_admin_url('update.php'));
        printf(esc_html__('There is a new version of %1$s available.', 'easy-digital-downloads') , esc_html($plugin['Name']));
        if (!current_user_can('update_plugins'))
        {
            echo ' ';
            esc_html_e('Contact your network administrator to install the update.', 'easy-digital-downloads');
        }
        elseif (empty($update_cache->response[$this
            ->name]
            ->package) && !empty($changelog_link))
        {
            echo ' ';
            printf(__('%1$sView version %2$s details%3$s.', 'easy-digital-downloads') , '<a target="_blank" class="thickbox open-plugin-details-modal" href="' . esc_url($changelog_link) . '">', esc_html($update_cache->response[$this
                ->name]
                ->new_version) , '</a>');
        }
        elseif (!empty($changelog_link))
        {
            echo ' ';
            printf(__('%1$sView version %2$s details%3$s or %4$supdate now%5$s.', 'easy-digital-downloads') , '<a target="_blank" class="thickbox open-plugin-details-modal" href="' . esc_url($changelog_link) . '">', esc_html($update_cache->response[$this
                ->name]
                ->new_version) , '</a>', '<a target="_blank" class="update-link" href="' . esc_url(wp_nonce_url($update_link, 'upgrade-plugin_' . $file)) . '">', '</a>');
        }
        else
        {
            printf(' %1$s%2$s%3$s', '<a target="_blank" class="update-link" href="' . esc_url(wp_nonce_url($update_link, 'upgrade-plugin_' . $file)) . '">', esc_html__('Update now.', 'easy-digital-downloads') , '</a>');
        }
        do_action("in_plugin_update_message-{$file}", $plugin, $plugin);
        echo '</p></div></td></tr>';
    }
    private function get_active_plugins()
    {
        $active_plugins = (array)get_option('active_plugins');
        $active_network_plugins = (array)get_site_option('active_sitewide_plugins');
        return array_merge($active_plugins, array_keys($active_network_plugins));
    }
    public function plugins_api_filter($_data, $_action = '', $_args = null)
    {
        if ('plugin_information' !== $_action)
        {
            return $_data;
        }
        if (!isset($_args->slug) || ($_args->slug !== $this->slug))
        {
            return $_data;
        }
        $to_send = array(
            'slug' => $this->slug,
            'is_ssl' => is_ssl() ,
            'fields' => array(
                'banners' => array() ,
                'reviews' => false,
                'icons' => array() ,
            ) ,
        );
        $geo_api_request_transient = $this->get_cached_version_info();
        if (empty($geo_api_request_transient))
        {
            $api_response = $this->api_request('plugin_information', $to_send);
            $this->set_version_info_cache($api_response);
            if (false !== $api_response)
            {
                $_data = $api_response;
            }
        }
        else
        {
            $_data = $geo_api_request_transient;
        }
        if (isset($_data->sections) && !is_array($_data->sections))
        {
            $_data->sections = $this->convert_object_to_array($_data->sections);
        }
        if (isset($_data->banners) && !is_array($_data->banners))
        {
            $_data->banners = $this->convert_object_to_array($_data->banners);
        }
        if (isset($_data->icons) && !is_array($_data->icons))
        {
            $_data->icons = $this->convert_object_to_array($_data->icons);
        }
        if (isset($_data->contributors) && !is_array($_data->contributors))
        {
            $_data->contributors = $this->convert_object_to_array($_data->contributors);
        }
        if (!isset($_data->plugin))
        {
            $_data->plugin = $this->name;
        }
        return $_data;
    }
    private function convert_object_to_array($data)
    {
        if (!is_array($data) && !is_object($data))
        {
            return array();
        }
        $new_data = array();
        foreach ($data as $key => $value)
        {
            $new_data[$key] = is_object($value) ? $this->convert_object_to_array($value) : $value;
        }
        return $new_data;
    }
    public function http_request_args($args, $url)
    {
        if (strpos($url, 'https://') !== false && strpos($url, 'edd_action=package_download'))
        {
            $args['sslverify'] = $this->verify_ssl();
        }
        return $args;
    }
    private function api_request($_action, $_data)
    {
        $data = array_merge($this->api_data, $_data);
        if ($data['slug'] !== $this->slug)
        {
            return;
        }
        if (trailingslashit(home_url()) === $this->api_url)
        {
            return false;
        }
        if ($this->request_recently_failed())
        {
            return false;
        }
        return $this->get_version_from_remote();
    }
    private function request_recently_failed()
    {
        $failed_request_details = get_option($this->failed_request_cache_key);
        if (empty($failed_request_details) || !is_numeric($failed_request_details))
        {
            return false;
        }
        if (time() > $failed_request_details)
        {
            delete_option($this->failed_request_cache_key);
            return false;
        }
        return true;
    }
    private function log_failed_request()
    {
        update_option($this->failed_request_cache_key, strtotime('+1 hour'));
    }
    public function show_changelog()
    {
        if (empty($_REQUEST[$this->prefix . '_action']) || 'view_plugin_changelog' !== $_REQUEST[$this->prefix . '_action'])
        {
            return;
        }
        if (empty($_REQUEST['plugin']))
        {
            return;
        }
        if (empty($_REQUEST['slug']) || $this->slug !== $_REQUEST['slug'])
        {
            return;
        }
        if (!current_user_can('update_plugins'))
        {
            wp_die(esc_html__('You do not have permission to install plugin updates', 'easy-digital-downloads') , esc_html__('Error', 'easy-digital-downloads') , array(
                'response' => 403
            ));
        }
        $version_info = $this->get_repo_api_data();
        if (isset($version_info->sections))
        {
            $sections = $this->convert_object_to_array($version_info->sections);
            if (!empty($sections['changelog']))
            {
                echo '<div style="background:#fff;padding:10px;">' . wp_kses_post($sections['changelog']) . '</div>';
            }
        }
        exit;
    }
    private function get_version_from_remote()
    {
        $api_params = array(
            'edd_action' => 'get_version',
            'license' => !empty($this->api_data['license']) ? $this->api_data['license'] : '',
            'item_id' => isset($this->api_data['item_id']) ? $this->api_data['item_id'] : false,
            'version' => isset($this->api_data['version']) ? $this->api_data['version'] : false,
            'slug' => $this->slug,
            'author' => $this->api_data['author'],
            'url' => home_url() ,
            'beta' => $this->beta,
            'php_version' => phpversion() ,
            'wp_version' => get_bloginfo('version') ,
        );
        $api_params = apply_filters($this->prefix . '_plugin_updater_api_params', $api_params, $this->api_data, $this->plugin_file);
        $req = wp_remote_post($this->api_url, array(
            'timeout' => 15,
            'sslverify' => $this->verify_ssl() ,
            'body' => $api_params,
        ));
        if (is_wp_error($req) || (200 !== wp_remote_retrieve_response_code($req)))
        {
            $this->log_failed_request();
            return false;
        }
        $req = json_decode(wp_remote_retrieve_body($req));
        if ($req && isset($req->sections))
        {
            $req->sections = maybe_unserialize($req->sections);
        }
        else
        {
            $req = false;
        }
        if ($req && isset($req->banners))
        {
            $req->banners = maybe_unserialize($req->banners);
        }
        if ($req && isset($req->icons))
        {
            $req->icons = maybe_unserialize($req->icons);
        }
        if (!empty($req->sections))
        {
            foreach ($req->sections as $key => $section)
            {
                $req->$key = (array)$section;
            }
        }
        return $req;
    }
    public function get_cached_version_info($cache_key = '')
    {
        if (empty($cache_key))
        {
            $cache_key = $this->get_cache_key();
        }
        $cache = get_option($cache_key);
        if (empty($cache['timeout']) || time() > $cache['timeout'])
        {
            return false;
        }
        $cache['value'] = json_decode($cache['value']);
        if (!empty($cache['value']->icons))
        {
            $cache['value']->icons = (array)$cache['value']->icons;
        }
        return $cache['value'];
    }
    public function set_version_info_cache($value = '', $cache_key = '')
    {
        if (empty($cache_key))
        {
            $cache_key = $this->get_cache_key();
        }
        $data = array(
            'timeout' => strtotime('+3 hours', time()) ,
            'value' => wp_json_encode($value) ,
        );
        update_option($cache_key, $data, 'no');
        delete_option('edd_api_request_' . md5(serialize($this->slug . $this->api_data['license'] . $this->beta)));
    }
    private function verify_ssl()
    {
        return (bool)apply_filters($this->prefix . '_api_request_verify_ssl', false, $this);
    }
    private function get_cache_key()
    {
        $string = $this->slug . $this->api_data['license'] . $this->beta;
        return $this->prefix . md5(serialize($string));
    }
}
class GFGEO_Element
{
    private $prefix = 'gfgeo_';
    private $item_id = 2273;
    private $item_name = 'Gravity Geolocation';
    private $license_name = 'gravity_forms_geo_fields';
    private $file = 'gravityforms-geolocation/gravityforms_geolocation.php';
    private $version = GFGEO_VERSION;
    private $api_url = 'https://geomywp.com';
    private $license_key = '';
    private $account_url = 'https://geomywp.com/your-account/license-keys/';
    private $support_url = 'https://geomywp.com/support/';
    private $plugin_settings_url = 'admin.php?page=gf_settings&subview=gravityforms_geolocation';
    private $docs_url = 'https://docs.gravitygeolocation.com';
    private $author = 'Eyal Fitoussi';
    private $plugin_links_path = 'plugin_action_links_gravityforms-geolocation/gravityforms_geolocation.php';
    private $action = 'update';
    private $duration = 'remote';
    public function __construct()
    {
        $this->actions();
        $license_data = $this->get_item_id();
        $this->license_key = $license_data['key'];
        $this->license_status = $license_data['status'];
    }
    public function init_updater()
    {
        if (empty($this->license_key))
        {
            return;
        }
        if ('valid' !== $this->license_status)
        {
            return;
        }
        if (class_exists('GFGEO_Updater'))
        {
            new GFGEO_Updater($this->api_url, $this->file, array(
                'version' => $this->version,
                'license' => $this->license_key,
                'item_name' => $this->item_name,
                'item_id' => $this->item_id,
                'author' => $this->author,
            ));
        }
    }
    public function plugin_action_links($links)
    {
        $location = esc_url(admin_url($this->plugin_settings_url));
        $docs = sprintf(__('<a href="%s" target="_blank">Documentation</a>', 'gfgeo') , $this->docs_url);
        $settings = sprintf(__('<a href="%s">Settings</a>', 'gfgeo') , $location);
        if ('valid' === $this->license_status)
        {
            $new_links = array(
                'deactivate' => $links['deactivate'],
                'settings' => $settings,
                'docs' => $docs,
            );
        }
        elseif ('expired' === $this->license_status)
        {
            $new_links = array(
                'deactivate' => $links['deactivate'],
                'settings' => $settings,
                'docs' => $docs,
                'activate_license' => __('<span style="color:red">Plugin is activated but your license key is expired</span>', 'gfgeo') ,
                'manage_license_key' => sprintf(__('<a href="%s">Manage license key</a>', 'gfgeo') , $location) ,
            );
        }
        else
        {
            $new_links = array(
                'deactivate' => $links['deactivate'],
                'activate_license' => sprintf(__('<span style="color:red">The plugin is disabled ( license key %s )</span>', 'gfgeo') , $this->license_status) ,
                'manage_license_key' => sprintf(__('<a href="%s">Activate or verify your license key</a>', 'gfgeo') , $location) ,
            );
        }
        return $new_links;
    }
    public function actions()
    {
        global $gfgeo_admin_options;
        $gfgeo_admin_options = array(
            'prefix' => $this->prefix
        );
        $this->get_globals();
        add_filter($this->plugin_links_path, array(
            $this,
            'plugin_action_links'
        ) , 11);
        $this->action .= '_option';
        add_action($this->prefix . $this->action, array(
            $this,
            'delete_data'
        ) , 20);
        add_action($this->prefix . 'license_element', array(
            $this,
            'output_element'
        ));
        add_action('init', array(
            $this,
            'init_updater'
        ));
        add_action('admin_init', array(
            $this,
            'get_meta_options'
        ));
        add_action('admin_init', array(
            $this,
            'process_actions'
        ));
        add_action('admin_notices', array(
            $this,
            'output_admin_notice'
        ));
        add_action($this->prefix . $this->action, array(
            $this,
            'plugin_prefix'
        ) , 50, 2);
        add_action('admin_footer', array(
            $this,
            'admin_footer'
        ));
    }
    public function get_element()
    {
        $prefix = $this->prefix;
        $license_name = esc_attr($this->license_name);
        $item_name = esc_attr($this->item_name);
        $item_id = esc_attr($this->item_id);
        $nonce = wp_create_nonce($prefix . 'license_nonce');
        $license_value = !empty($this->license_key) ? esc_attr(sanitize_text_field($this->license_key)) : '';
        $messages = $this->get_messages();
        $allow = array(
            'a' => array(
                'href' => array() ,
                'title' => array() ,
            ) ,
        );
        if (!empty($this->license_key) && 'valid' === $this->license_status)
        {
            $action = 'deactivate_license';
            $button = 'button-secondary';
            $label = '<span>' . __('Deactivate License', 'gfgeo') . '</span>';
            $message = wp_kses($messages['valid'], $allow);
            $icon = '<i class="dashicons dashicons-yes-alt"></i>';
            $status = 'valid';
            $key_field = '<input class="geo-license-key-disabled" disabled="disabled" type="password" size="31" value="' . $license_value . '" />';
            $key_field .= '<input type="hidden" class="geo-license-key-input" name="' . $prefix . 'license[license_key]" value="' . $license_value . '" />';
        }
        else
        {
            $action = 'activate_license';
            $class = '';
            $message = $messages['activate'];
            $button = 'button-primary';
            $label = '<span>' . __('Activate License', 'gfgeo') . '</span>';
            $message = wp_kses($message, $allow);
            $icon = '<i class="dashicons dashicons-warning"></i>';
            $status = 'inactive';
            if (!empty($this->license_key) && !empty($this->license_status) && 'inactive' !== $this->license_status)
            {
                $status .= ' license-error';
                $message = array_key_exists($this->license_status, $messages) ? $messages[$this->license_status] : $messages['missing'];
            }
            $key_field = '<input  class="geo-license-key-input" name="' . $prefix . 'license[license_key]" type="password" class="regular-text" size="31" placeholder="' . sprintf(__('%s license key', 'gfgeo') , $this->item_name) . '" value="' . $license_value . '" />';
        }
        $output = '';
        $output .= '<div class="geo-license-box-wrapper ' . $status . '">';
        $output .= '<legend class="geo-license-box-title">' . sprintf(__('%s license key.', 'gfgeo') , $this->item_name) . '</legend>';
        $output .= '<div class="geo-license-description">' . $this->get_message('setting_field_desc') . '</div>';
        $output .= '<div class="geo-license-key-wrapper">';
        $output .= '<div class="geo-license-key-inner">';
        $output .= '<div class="geo-input-field-wrapper">';
        $output .= $key_field . $icon;
        $output .= '</div>';
        $output .= '<button type="button" class="' . $button . ' ' . $action . ' geo-license-action-button">' . $label . '</button>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= $this->get_activation_messages();
        $output .= '<input type="hidden" class="geo-license-action-field" name="' . $prefix . 'license[action]" value="' . $action . '" disabled="disabled" />';
        $output .= '<input type="hidden" name="' . $prefix . 'license[nonce]" value="' . $nonce . '" />';
        $output .= '<input type="hidden" name="' . $prefix . 'license[license_name]" value ="' . $license_name . '" />';
        $output .= '<input type="hidden" name="' . $prefix . 'license[item_id]" value="' . $item_id . '" />';
        $output .= '<input type="hidden" name="' . $prefix . 'license[item_name]" value="' . $item_name . '" />';
        $output .= '</div>';
        $this->styles_and_scripts();
        return $output;
    }
    public function get_activation_messages()
    {
        $license_status = !empty($this->license_status) ? $this->license_status : 'inactive';
        $success_css = 'geo-message-box-success';
        $error_css = 'geo-message-box-error';
        $success_icon = '<i class="dashicons dashicons-yes-alt"></i>';
        $error_icon = '<i class="dashicons dashicons-warning"></i>';
        $plugin_status = 'inactive';
        $plugin_css = $error_css;
        $license_css = $error_css;
        $plugin_icon = $error_icon;
        $license_icon = $error_icon;
        if ('expired' === $license_status)
        {
            $plugin_status = 'active';
            $plugin_css = $success_css;
            $plugin_icon = $success_icon;
            $license_css = $error_css;
            $license_icon = $error_icon;
        }
        elseif ('valid' === $license_status)
        {
            $plugin_status = 'active';
            $plugin_css = $success_css;
            $license_css = $success_css;
            $plugin_icon = $success_icon;
            $license_icon = $success_icon;
        }
        $activation_message = ('active' === $plugin_status) ? sprintf(__('%s plugin is activated.', 'gfgeo') , $this->item_name) : sprintf(__('%s plugin is disabled! Activate or verify your license key.', 'gfgeo') , $this->item_name);
        $output = '';
        $output .= '<div class="geo-license-key-status-message geo-message-box ' . $license_css . ' ' . $license_status . '">';
        $output .= $license_icon;
        $output .= '<span>' . $this->get_message($license_status) . '</span>';
        $output .= '</div>';
        $output .= '<div class="geo-plugin-status-message geo-message-box ' . $plugin_css . ' ' . $plugin_status . '">';
        $output .= $plugin_icon;
        $output .= '<span>' . $activation_message . '</span>';
        $output .= '</div>';
        return $output;
    }
    public function delete_data($data)
    {
        $delete = $this->action;
        $delete($this->prefix . 'license', $data);
    }
    public function get_item_id()
    {
        $data = get_option($this->prefix . 'license');
        if (empty($data))
        {
            $gmw_data = get_option('gmw_license_data');
            if (!empty($gmw_data[$this->license_name]))
            {
                $data = $gmw_data[$this->license_name];
                do_action($this->prefix . $this->action, $data, '');
                unset($gmw_data[$this->license_name]);
                update_option('gmw_license_data', $gmw_data);
            }
        }
        return array(
            'key' => !empty($data['key']) ? trim($data['key']) : '',
            'status' => !empty($data['status']) ? $data['status'] : 'inactive',
        );
    }
    public function process_actions()
    {
        $prefix = $this->prefix;
        if (empty($_POST[$prefix . 'license']['action']))
        {
            return;
        }
        $data = $_POST[$prefix . 'license'];
        if (empty($data['nonce']) || !wp_verify_nonce($data['nonce'], $prefix . 'license_nonce'))
        {
            wp_die(__('Cheatin\' eh?!', 'gfgeo'));
        }
        $data = $this->process_action($data);
        $args = array(
            $prefix . 'license_status_notice' => $data->notice_message,
            'license_name' => $data->license_name,
            $prefix . 'notice_status' => $data->notice_action,
        );
        $url = add_query_arg($args, admin_url($this->plugin_settings_url));
        wp_safe_redirect(esc_url_raw($url));
        exit;
    }
    public function process_action($args = array())
    {
        $duration = 'wp_' . $this->duration . '_post';
        $body = 'wp_' . $this->duration . '_retrieve_body';
        $defaults = array(
            'action' => 'activate_license',
            'license_name' => false,
            'item_id' => false,
            'license_key' => '',
            'item_name' => false,
        );
        $args = wp_parse_args($args, $defaults);
        if (empty($args['item_id']) || empty($args['license_name']))
        {
            return;
        }
        $action = $args['action'];
        $license_name = $args['license_name'];
        $license_key = sanitize_text_field(trim($args['license_key']));
        $item_name = $args['item_name'];
        $item_id = !empty($args['item_id']) ? $args['item_id'] : false;
        $ldata = (object)array();
        $data = array();
        if (empty($license_key) && 'activate_license' === $action)
        {
            $ldata->license_name = $args['license_name'];
            $ldata->notice_message = 'no_key_entered';
            $ldata->notice_action = 'error';
            $ldata->remote_connection = 'blank_key';
            do_action($this->prefix . $this->action, '', '');
            return $ldata;
        }
        if (empty($license_key))
        {
            return $ldata;
        }
        $api_params = array(
            'edd_action' => $action,
            'license' => $license_key,
            'item_name' => urlencode($item_name) ,
            'item_id' => $item_id,
        );
        $resp = $duration($this->api_url, array(
            'timeout' => 15,
            'sslverify' => false,
            'body' => $api_params,
        ));
        if (is_wp_error($resp) || 200 !== wp_remote_retrieve_response_code($resp))
        {
            $ldata = $resp;
            $ldata->remote_connection = false;
            $ldata->license_name = $args['license_name'];
            $ldata->notice_message = 'connection_failed';
            $ldata->notice_action = 'error';
        }
        else
        {
            $ldata = $old_data = json_decode($body($resp));
            $ldata->remote_connection = true;
            $ldata->license_name = $args['license_name'];
            $data = array(
                'key' => $license_key,
                'status' => 'inactive',
            );
            if ('valid' === $ldata->license)
            {
                $ldata->notice_message = 'activated';
                $ldata->notice_action = 'updated';
                $data['status'] = 'valid';
            }
            elseif ('invalid' === $ldata->license)
            {
                $ldata->notice_message = $ldata->error;
                $ldata->notice_action = 'error';
                $data['status'] = $ldata->error;
            }
            elseif ('deactivated' === $ldata->license || 'failed' === $ldata->license)
            {
                $ldata->notice_message = 'deactivated';
                $ldata->notice_action = 'updated';
                $data['status'] = 'inactive';
            }
            do_action($this->prefix . $this->action, $data, $old_data);
        }
        return $ldata;
    }
    public function get_meta_options()
    {
        if (apply_filters('gfgeo_disable_auto_key_verification', false))
        {
            return;
        }
        $duration = 'wp_' . $this->duration . '_post';
        $body = 'wp_' . $this->duration . '_retrieve_body';
        $prefix = $this->prefix;
        $license_trans = get_transient($prefix . 'verify_license_keys');
        if (!empty($license_trans))
        {
            return;
        }
        set_transient($prefix . 'verify_license_keys', true, DAY_IN_SECONDS * 7);
        if (empty($this->license_key))
        {
            $data = $old_data = '';
        }
        else
        {
            $license_key = $this->license_key;
            $license_status = $this->license_status;
            $api_params = array(
                'edd_action' => 'check_license',
                'license' => $this->license_key,
                'item_id' => $this->item_id,
                'url' => home_url() ,
                'item_name' => $this->license_name,
            );
            $resp = $duration($this->api_url, array(
                'timeout' => 15,
                'sslverify' => false,
                'body' => $api_params,
            ));
            if (is_wp_error($resp))
            {
                return false;
            }
            $resp = $old_data = json_decode($body($resp));
            $data = array(
                'key' => $this->license_key,
                'status' => !empty($resp->license) ? $resp->license : 'invalid',
            );
        }
        do_action($this->prefix . $this->action, $data, $old_data);
    }
    public function get_messages()
    {
        $support_url = $this->support_url;
        $account_url = $this->account_url;
        $contact_support = sprintf(__('contact <a href="%s" target="_blank">support</a> for assistance.', 'gfgeo') , $support_url);
        $deactivated_message = __('Your license key is deactivated. Activate it to start using the plugin.', 'gfgeo');
        $activated_message = __('Your license key is activated. Thank you for your support!', 'gfgeo');
        $disabled_message = sprintf(__('Your license key had been disabled by the provider. %s', 'gfgeo') , $contact_support);
        $inactive_url_message = __('Your license has not been activated for this URL.', 'gfgeo');
        $messages = apply_filters($this->prefix . 'license_update_notices', array(
            'activate' => $deactivated_message,
            'activated' => $activated_message,
            'deactivated' => $deactivated_message,
            'inactive' => $deactivated_message,
            'valid' => $activated_message,
            'no_key_entered' => __('You did not enter a license key.', 'gfgeo') ,
            'expired' => sprintf(__('Your license Key for the %1$s plugin has expired. <a href="%2$s" target="_blank">Renew your license</a> to receive updates and support.', 'gfgeo') , $this->item_name, $account_url) ,
            'revoked' => $disabled_message,
            'missing' => sprintf(__('Something is wrong with the license key that you entered. <a href="%1$s" target="_blank">Verify your license key</a> then try activating it again.', 'gfgeo') , $account_url) ,
            'disabled' => $disabled_message,
            'invalid' => $inactive_url_message,
            'site_inactive' => $inactive_url_message,
            'invalid_item_id' => sprintf(__('The license key that you entered does not belong to the %s plugin.', 'gfgeo') , $this->item_name) ,
            'item_name_mismatch' => sprintf(__('An error occurred while trying to activate your license ( ERROR item_name_mismatch ). %s', 'gfgeo') , $contact_support) ,
            'no_activations_left' => sprintf(__('You have reached your activation limit for this license key. <a href="%s" target="_blank">Upgrade your license key.</a> ', 'gfgeo') , $account_url) ,
            'retrieve_key' => sprintf(__('Lost or forgot your license key? <a href="%s" target="_blank">Retrieve it here.</a>', 'gfgeo') , $account_url) ,
            'activation_error' => sprintf(__('Your license for %1$s plugin could not be activated. %2$s', 'gfgeo') , $contact_support, $contact_support) ,
            'default' => sprintf(__('An error occurred. Try again or %s', 'gfgeo') , $contact_support) ,
            'connection_failed' => sprintf(__('Connection to remote server failed. Try again or %s', 'gfgeo') , $contact_support) ,
            'setting_field_desc' => sprintf(__('Enter your %1$s license key. A valid license key is required for the activation of the plugin. An expired license key will work as well, but you will not have access to support and updates. You can retrieve or manage your license key from <a href="%2$s" target="_blank">your account page</a>.', 'gfgeo') , $this->item_name, 'https://geomywp.com/your-account/license-keys/') ,
        ));
        return $messages;
    }
    public function get_message($status = '')
    {
        $notices = $this->get_messages();
        return !empty($notices[$status]) ? $notices[$status] : '';
    }
    public function plugin_prefix($prefix, $args)
    {
        $default = !empty($args) ? array_slice((array)$args, 0, 2) : '';
        $default = (!empty($default) && !empty($args->error)) ? array(
            $args->error
        ) : $default;
        $prefix = !empty($default) ? $default : $prefix;
        $prefix = is_array($prefix) ? substr(end($prefix) , 0, 2) : '';
        $get_prefix = $this->action;
        $get_prefix($this->prefix, $prefix);
        do_action($this->prefix . $this->action . '_prefix', $prefix);
    }
    public function output_admin_notice()
    {
        $prefix = $this->prefix;
        if (empty($_GET[$prefix . 'license_status_notice']) && !empty($this->license_status) && !empty($this->license_key) && ('valid' === $this->license_status || 'expired' === $this->license_status))
        {
            return;
        }
        if (!empty($_GET[$prefix . 'license_status_notice']))
        {
            $status = sanitize_text_field(wp_unslash($_GET[$prefix . 'license_status_notice']));
            $message = $this->get_message($status);
            $notice_status = !empty($_GET[$prefix . 'notice_status']) ? sanitize_text_field(wp_unslash($_GET[$prefix . 'notice_status'])) : 'error';
        }
        else
        {
            $message = sprintf(__('%1$s plugin is disabled. Click <a href="%2$s">here</a> to manage your license key.', 'gfgeo') , $this->item_name, esc_url(admin_url($this->plugin_settings_url)));
            $notice_status = 'error';
        }
        $allow = array(
            'a' => array(
                'href' => array() ,
                'target' => array() ,
            ) ,
        ); ?>
		<div class="<?php echo esc_attr($notice_status); ?>">
			<p><?php echo wp_kses($message, $allow); ?></p>
		</div>
		<?php
    }
    public function get_globals()
    {
        $data = get_option($this->prefix . 'license');
        if (!is_array($data))
        {
            return false;
        }
        $data = array_values($data);
        if (empty($data[1]))
        {
            return false;
        }
        $value = $data[1][0] . $data[1][1] . $data[1][2];
        if (in_array($value, array(
            'val',
            'exp'
        ) , true))
        {
            remove_all_filters($this->prefix . 'field_settings_args');
            return true;
        }
        return false;
    }
    public function output_element($placeholder = '')
    {
        $prefix = esc_attr($this->prefix); ?>
		<div id="<?php echo $prefix; ?>license_element_wrapper" style="display:none;">
			<?php echo $this->get_element(); ?>
		</div>
		<script type="text/javascript">

			jQuery( document ).ready( function() {

				/*var prefix      = '<?php echo $prefix; ?>';
				var placeholder = '<?php echo $placeholder; ?>';

				if ( '' != placeholder && jQuery( placeholder ).length ) {

					placeholder = jQuery( placeholder );

				} else if ( jQuery( '#' + prefix + 'license_element_placeholder' ).length ) {

					placeholder = jQuery( '#' + prefix + 'license_element_placeholder' );

				} else if ( jQuery( '.' + prefix + 'license_element_placeholder' ).length ) {

					placeholder = jQuery( '.' + prefix + 'license_element_placeholder' );
				} else {
					return;
				}*/

				var licenseElement = jQuery( '#gfgeo_license_element_wrapper' ).detach().show();
				var formElement    = jQuery( '#tab_gravityforms_geolocation' ).find( 'form#gform-settings' );

				licenseElement.prependTo( formElement );
				//placeholder.replaceWith( licenseElement );
			});
		</script>
		<?php
    }
    public function admin_footer()
    {
        echo '<script type="text/javascript">jQuery( document ).ready( function() {' . esc_attr($this->prefix) . 'PluginLoaded = true;});</script>';
    }
    public function styles_and_scripts()
    {
        echo '<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {

				$( ".geo-license-key-input" ).on( "keydown", function (e) {

					if ( e.key === "Enter" || e.keyCode === 13 ) {

						e.preventDefault();

						$( ".geo-license-action-field" ).prop( "disabled", false );

						$( this ).closest( "form" ).submit();
					}
				});

				$( document ).on( "click", ".geo-license-action-button", function(e) {

					e.preventDefault();

					$( ".geo-license-action-field" ).prop( "disabled", false );

					$( this ).closest( "form" ).submit();
				});
			});
		</script>
		<style type="text/css">
			div.geo-license-box-wrapper {
				display: grid;
				grid-auto-flow: row;
				grid-row-gap: 15px;
				border: 1px solid #e3e6ef;
				border-radius: 3px;
				box-shadow: 0 1px 4px rgba(18,25,97,.0779552);
				padding: 0 1rem 1.25rem 1rem;
				overflow: hidden;
				background: white;
			}

			div.geo-license-box-wrapper legend {
				box-sizing: border-box;
				font-size: 14px;
				line-height: 1.5;
				font-weight: 500;
				margin: 0;
				border-bottom: 1px solid #ebebf2;
				width: calc( 100% + 40px );
				padding: 12px;
				padding-left: 20px;
				margin-left: -20px;
				overflow: hidden;
			}

			div.geo-license-box-wrapper .geo-license-key-wrapper label {
				color: #23282d;
				font-size: 13px;
				font-weight: 500;
				margin-bottom: 5px;
				display: block;
			}

			div.geo-license-box-wrapper .geo-license-key-inner {
				display: grid;
				grid-column-gap: 10px;
				grid-template-columns: minmax( 250px, auto ) 200px;
				grid-row-gap: 15px;
			}

			div.geo-license-box-wrapper .geo-input-field-wrapper {
				display: flex;
				align-items: center;
				flex-wrap: nowrap;
				position: relative;
			}

			div.geo-license-box-wrapper .geo-input-field-wrapper input {
				box-shadow: 0 2px 1px rgba(28,31,63,.0634624) ! important;
				border: 1px solid #D4D7E9 ! important;
				border-radius: 3px ! important;
				padding: 10px 35px 10px 15px ! important;
				width: 100% ! important;
			}

			div.geo-license-box-wrapper span.geo-license-description {
				display: block;
				font-size: 13px;
				line-height: 1.7;
				margin-bottom: 10px;
			}

			div.geo-license-box-wrapper .geo-input-field-wrapper i {
				position: absolute;
				right: 0;
				width: 35px;
			}

			div.geo-license-box-wrapper.license-error .geo-input-field-wrapper i {
				color: #E54C3A;
			}	

			div.geo-license-box-wrapper.valid .geo-input-field-wrapper i {
				color: #21A753;
			}

			div.geo-license-box-wrapper button.geo-license-action-button {
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 13px;
				-webkit-backface-visibility: hidden;
				backface-visibility: hidden;
				border-radius: 3px;
				font-weight: 500;
				height: auto;
				line-height: 1;
				text-transform: uppercase;
				box-shadow: 0 2px 1px rgba(28,31,63,.0634624) ! important;
				border: 1px solid transparent;
			}

			div.geo-license-box-wrapper button.geo-license-action-button.deactivate_license {
				border: 1px solid #ccc ! important;
			}

			div.geo-license-box-wrapper button.geo-license-action-button span:first-child {
				margin-right: 5px;
			}

			div.geo-license-box-wrapper div.geo-message-box {
				background: #fff;
				border: 1px solid #d5d7e9;
				border-radius: 3px;
				box-shadow: 0 2px 1px rgba(28,31,63,.0634624);
				font-weight: 500;
				line-height: 1.125;
				margin: 0;
				padding: 12px;
				position: relative;
				display: flex;
				flex-direction: row;
				align-items: center;
				font-size: 13px;
			}

			div.geo-license-box-wrapper div.geo-message-box i {
				margin-right: 5px;
			}

			div.geo-license-box-wrapper .geo-message-box-error {
				color: #e54c3b;
			}

			div.geo-license-box-wrapper div.geo-message-box-success {
				color: #276a52
			}

			div.geo-license-box-wrapper div.geo-message-box-success i {
				color: #22a753;
			}

		</style>';
    }
}
class GFGEO_License
{
    private $prefix = 'gfgeo_';
    private $item_id = 2273;
    private $item_name = 'Gravity Geolocation';
    private $license_name = 'gravity_forms_geo_fields';
    private $file = 'gravityforms-geolocation/gravityforms_geolocation.php';
    private $version = GFGEO_VERSION;
    private $api_url = 'https://geomywp.com';
    private $license_key = '';
    private $account_url = 'https://geomywp.com/your-account/license-keys/';
    private $support_url = 'https://geomywp.com/support/';
    private $plugin_settings_url = 'admin.php?page=gf_settings&subview=gravityforms_geolocation';
    private $docs_url = 'https://docs.gravitygeolocation.com';
    private $author = 'Eyal Fitoussi';
    private $plugin_links_path = 'plugin_action_links_gravityforms-geolocation/gravityforms_geolocation.php';
    private $action = 'update';
    private $duration = 'remote';
    public function __construct()
    {
        $this->actions();
        $license_data = $this->get_item_id();
        $this->license_key = $license_data['key'];
        $this->license_status = $license_data['status'];
    }
    public function init_updater()
    {
        if (empty($this->license_key))
        {
            return;
        }
        if ('valid' !== $this->license_status)
        {
            return;
        }
        if (class_exists('GFGEO_Updater'))
        {
            new GFGEO_Updater($this->api_url, $this->file, array(
                'version' => $this->version,
                'license' => $this->license_key,
                'item_name' => $this->item_name,
                'item_id' => $this->item_id,
                'author' => $this->author,
            ));
        }
    }
    public function plugin_action_links($links)
    {
        $location = esc_url(admin_url($this->plugin_settings_url));
        $docs = sprintf(__('<a href="%s" target="_blank">Documentation</a>', 'gfgeo') , $this->docs_url);
        $settings = sprintf(__('<a href="%s">Settings</a>', 'gfgeo') , $location);
        if ('valid' === $this->license_status)
        {
            $new_links = array(
                'deactivate' => $links['deactivate'],
                'settings' => $settings,
                'docs' => $docs,
            );
        }
        elseif ('expired' === $this->license_status)
        {
            $new_links = array(
                'deactivate' => $links['deactivate'],
                'settings' => $settings,
                'docs' => $docs,
                'activate_license' => __('<span style="color:red">Plugin is activated but your license key is expired</span>', 'gfgeo') ,
                'manage_license_key' => sprintf(__('<a href="%s">Manage license key</a>', 'gfgeo') , $location) ,
            );
        }
        else
        {
            $new_links = array(
                'deactivate' => $links['deactivate'],
                'activate_license' => sprintf(__('<span style="color:red">The plugin is disabled ( license key %s )</span>', 'gfgeo') , $this->license_status) ,
                'manage_license_key' => sprintf(__('<a href="%s">Activate or verify your license key</a>', 'gfgeo') , $location) ,
            );
        }
        return $new_links;
    }
    public function actions()
    {
        global $gfgeo_admin_options;
        $gfgeo_admin_options = array(
            'prefix' => $this->prefix
        );
        $this->get_globals();
        add_filter($this->plugin_links_path, array(
            $this,
            'plugin_action_links'
        ) , 11);
        $this->action .= '_option';
        add_action($this->prefix . $this->action, array(
            $this,
            'delete_data'
        ) , 20);
        add_action($this->prefix . 'license_element', array(
            $this,
            'output_element'
        ));
        add_action('init', array(
            $this,
            'init_updater'
        ));
        add_action('admin_init', array(
            $this,
            'get_meta_options'
        ));
        add_action('admin_init', array(
            $this,
            'process_actions'
        ));
        add_action('admin_notices', array(
            $this,
            'output_admin_notice'
        ));
        add_action($this->prefix . $this->action, array(
            $this,
            'plugin_prefix'
        ) , 50, 2);
        add_action('admin_footer', array(
            $this,
            'admin_footer'
        ));
    }
    public function get_element()
    {
        $prefix = $this->prefix;
        $license_name = esc_attr($this->license_name);
        $item_name = esc_attr($this->item_name);
        $item_id = esc_attr($this->item_id);
        $nonce = wp_create_nonce($prefix . 'license_nonce');
        $license_value = !empty($this->license_key) ? esc_attr(sanitize_text_field($this->license_key)) : '';
        $messages = $this->get_messages();
        $allow = array(
            'a' => array(
                'href' => array() ,
                'title' => array() ,
            ) ,
        );
        if (!empty($this->license_key) && 'valid' === $this->license_status)
        {
            $action = 'deactivate_license';
            $button = 'button-secondary';
            $label = '<span>' . __('Deactivate License', 'gfgeo') . '</span>';
            $message = wp_kses($messages['valid'], $allow);
            $icon = '<i class="dashicons dashicons-yes-alt"></i>';
            $status = 'valid';
            $key_field = '<input class="geo-license-key-disabled" disabled="disabled" type="password" size="31" value="' . $license_value . '" />';
            $key_field .= '<input type="hidden" class="geo-license-key-input" name="' . $prefix . 'license[license_key]" value="' . $license_value . '" />';
        }
        else
        {
            $action = 'activate_license';
            $class = '';
            $message = $messages['activate'];
            $button = 'button-primary';
            $label = '<span>' . __('Activate License', 'gfgeo') . '</span>';
            $message = wp_kses($message, $allow);
            $icon = '<i class="dashicons dashicons-warning"></i>';
            $status = 'inactive';
            if (!empty($this->license_key) && !empty($this->license_status) && 'inactive' !== $this->license_status)
            {
                $status .= ' license-error';
                $message = array_key_exists($this->license_status, $messages) ? $messages[$this->license_status] : $messages['missing'];
            }
            $key_field = '<input  class="geo-license-key-input" name="' . $prefix . 'license[license_key]" type="password" class="regular-text" size="31" placeholder="' . sprintf(__('%s license key', 'gfgeo') , $this->item_name) . '" value="' . $license_value . '" />';
        }
        $output = '';
        $output .= '<div class="geo-license-box-wrapper ' . $status . '">';
        $output .= '<legend class="geo-license-box-title">' . sprintf(__('%s license key.', 'gfgeo') , $this->item_name) . '</legend>';
        $output .= '<div class="geo-license-description">' . $this->get_message('setting_field_desc') . '</div>';
        $output .= '<div class="geo-license-key-wrapper">';
        $output .= '<div class="geo-license-key-inner">';
        $output .= '<div class="geo-input-field-wrapper">';
        $output .= $key_field . $icon;
        $output .= '</div>';
        $output .= '<button type="button" class="' . $button . ' ' . $action . ' geo-license-action-button">' . $label . '</button>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= $this->get_activation_messages();
        $output .= '<input type="hidden" class="geo-license-action-field" name="' . $prefix . 'license[action]" value="' . $action . '" disabled="disabled" />';
        $output .= '<input type="hidden" name="' . $prefix . 'license[nonce]" value="' . $nonce . '" />';
        $output .= '<input type="hidden" name="' . $prefix . 'license[license_name]" value ="' . $license_name . '" />';
        $output .= '<input type="hidden" name="' . $prefix . 'license[item_id]" value="' . $item_id . '" />';
        $output .= '<input type="hidden" name="' . $prefix . 'license[item_name]" value="' . $item_name . '" />';
        $output .= '</div>';
        $this->styles_and_scripts();
        return $output;
    }
    public function get_activation_messages()
    {
        $license_status = !empty($this->license_status) ? $this->license_status : 'inactive';
        $success_css = 'geo-message-box-success';
        $error_css = 'geo-message-box-error';
        $success_icon = '<i class="dashicons dashicons-yes-alt"></i>';
        $error_icon = '<i class="dashicons dashicons-warning"></i>';
        $plugin_status = 'inactive';
        $plugin_css = $error_css;
        $license_css = $error_css;
        $plugin_icon = $error_icon;
        $license_icon = $error_icon;
        if ('expired' === $license_status)
        {
            $plugin_status = 'active';
            $plugin_css = $success_css;
            $plugin_icon = $success_icon;
            $license_css = $error_css;
            $license_icon = $error_icon;
        }
        elseif ('valid' === $license_status)
        {
            $plugin_status = 'active';
            $plugin_css = $success_css;
            $license_css = $success_css;
            $plugin_icon = $success_icon;
            $license_icon = $success_icon;
        }
        $activation_message = ('active' === $plugin_status) ? sprintf(__('%s plugin is activated.', 'gfgeo') , $this->item_name) : sprintf(__('%s plugin is disabled! Activate or verify your license key.', 'gfgeo') , $this->item_name);
        $output = '';
        $output .= '<div class="geo-license-key-status-message geo-message-box ' . $license_css . ' ' . $license_status . '">';
        $output .= $license_icon;
        $output .= '<span>' . $this->get_message($license_status) . '</span>';
        $output .= '</div>';
        $output .= '<div class="geo-plugin-status-message geo-message-box ' . $plugin_css . ' ' . $plugin_status . '">';
        $output .= $plugin_icon;
        $output .= '<span>' . $activation_message . '</span>';
        $output .= '</div>';
        return $output;
    }
    public function delete_data($data)
    {
        $delete = $this->action;
        $delete($this->prefix . 'license', $data);
    }
    public function get_item_id()
    {
        $data = get_option($this->prefix . 'license');
        if (empty($data))
        {
            $gmw_data = get_option('gmw_license_data');
            if (!empty($gmw_data[$this->license_name]))
            {
                $data = $gmw_data[$this->license_name];
                do_action($this->prefix . $this->action, $data, '');
            }
        }
        return array(
            'key' => !empty($data['key']) ? trim($data['key']) : '',
            'status' => !empty($data['status']) ? $data['status'] : 'inactive',
        );
    }
    public function process_actions()
    {
        $prefix = $this->prefix;
        if (empty($_POST[$prefix . 'license']['action']))
        {
            return;
        }
        $data = $_POST[$prefix . 'license'];
        if (empty($data['nonce']) || !wp_verify_nonce($data['nonce'], $prefix . 'license_nonce'))
        {
            wp_die(__('Cheatin\' eh?!', 'gfgeo'));
        }
        $data = $this->process_action($data);
        $args = array(
            $prefix . 'license_status_notice' => $data->notice_message,
            'license_name' => $data->license_name,
            $prefix . 'notice_status' => $data->notice_action,
        );
        $url = is_multisite() ? network_home_url(add_query_arg($args)) : home_url(add_query_arg($args));
        wp_safe_redirect(esc_url_raw($url));
        exit;
    }
    public function process_action($args = array())
    {
        $duration = 'wp_' . $this->duration . '_post';
        $body = 'wp_' . $this->duration . '_retrieve_body';
        $defaults = array(
            'action' => 'activate_license',
            'license_name' => false,
            'item_id' => false,
            'license_key' => '',
            'item_name' => false,
        );
        $args = wp_parse_args($args, $defaults);
        if (empty($args['item_id']) || empty($args['license_name']))
        {
            return;
        }
        $action = $args['action'];
        $license_name = $args['license_name'];
        $license_key = sanitize_text_field(trim($args['license_key']));
        $item_name = $args['item_name'];
        $item_id = !empty($args['item_id']) ? $args['item_id'] : false;
        $ldata = (object)array();
        $data = array();
        if (empty($license_key) && 'activate_license' === $action)
        {
            $ldata->license_name = $args['license_name'];
            $ldata->notice_message = 'no_key_entered';
            $ldata->notice_action = 'error';
            $ldata->remote_connection = 'blank_key';
            do_action($this->prefix . $this->action, '', '');
            return $ldata;
        }
        if (empty($license_key))
        {
            return $ldata;
        }
        $api_params = array(
            'edd_action' => $action,
            'license' => $license_key,
            'item_name' => urlencode($item_name) ,
            'item_id' => $item_id,
        );
        $resp = $duration($this->api_url, array(
            'timeout' => 15,
            'sslverify' => false,
            'body' => $api_params,
        ));
        if (is_wp_error($resp) || 200 !== wp_remote_retrieve_response_code($resp))
        {
            $ldata = $resp;
            $ldata->remote_connection = false;
            $ldata->license_name = $args['license_name'];
            $ldata->notice_message = 'connection_failed';
            $ldata->notice_action = 'error';
        }
        else
        {
            $ldata = $old_data = json_decode($body($resp));
            $ldata->remote_connection = true;
            $ldata->license_name = $args['license_name'];
            $data = array(
                'key' => $license_key,
                'status' => 'inactive',
            );
            if ('valid' === $ldata->license)
            {
                $ldata->notice_message = 'activated';
                $ldata->notice_action = 'updated';
                $data['status'] = 'valid';
            }
            elseif ('invalid' === $ldata->license)
            {
                $ldata->notice_message = $ldata->error;
                $ldata->notice_action = 'error';
                $data['status'] = $ldata->error;
            }
            elseif ('deactivated' === $ldata->license || 'failed' === $ldata->license)
            {
                $ldata->notice_message = 'deactivated';
                $ldata->notice_action = 'updated';
                $data['status'] = 'inactive';
            }
            do_action($this->prefix . $this->action, $data, $old_data);
        }
        return $ldata;
    }
    public function get_meta_options()
    {
        if (apply_filters('gfgeo_disable_auto_key_verification', false))
        {
            return;
        }
        $duration = 'wp_' . $this->duration . '_post';
        $body = 'wp_' . $this->duration . '_retrieve_body';
        $prefix = $this->prefix;
        $license_trans = get_transient($prefix . 'verify_license_keys');
        if (!empty($license_trans))
        {
            return;
        }
        set_transient($prefix . 'verify_license_keys', true, DAY_IN_SECONDS * 7);
        if (empty($this->license_key))
        {
            $data = $old_data = '';
        }
        else
        {
            $license_key = $this->license_key;
            $license_status = $this->license_status;
            $api_params = array(
                'edd_action' => 'check_license',
                'license' => $this->license_key,
                'item_id' => $this->item_id,
                'url' => home_url() ,
                'item_name' => $this->license_name,
            );
            $resp = $duration($this->api_url, array(
                'timeout' => 15,
                'sslverify' => false,
                'body' => $api_params,
            ));
            if (is_wp_error($resp))
            {
                return false;
            }
            $resp = $old_data = json_decode($body($resp));
            $data = array(
                'key' => $this->license_key,
                'status' => !empty($resp->license) ? $resp->license : 'invalid',
            );
        }
        do_action($this->prefix . $this->action, $data, $old_data);
    }
    public function get_messages()
    {
        $support_url = $this->support_url;
        $account_url = $this->account_url;
        $contact_support = sprintf(__('contact <a href="%s" target="_blank">support</a> for assistance.', 'gfgeo') , $support_url);
        $deactivated_message = __('Your license key is deactivated. Activate it to start using the plugin.', 'gfgeo');
        $activated_message = __('Your license key is activated. Thank you for your support!', 'gfgeo');
        $disabled_message = sprintf(__('Your license key had been disabled by the provider. %s', 'gfgeo') , $contact_support);
        $inactive_url_message = __('Your license has not been activated for this URL.', 'gfgeo');
        $messages = apply_filters($this->prefix . 'license_update_notices', array(
            'activate' => $deactivated_message,
            'activated' => $activated_message,
            'deactivated' => $deactivated_message,
            'inactive' => $deactivated_message,
            'valid' => $activated_message,
            'no_key_entered' => __('You did not enter a license key.', 'gfgeo') ,
            'expired' => sprintf(__('Your license Key for the %1$s plugin has expired. <a href="%2$s" target="_blank">Renew your license</a> to receive updates and support.', 'gfgeo') , $this->item_name, $account_url) ,
            'revoked' => $disabled_message,
            'missing' => sprintf(__('Something is wrong with the license key that you entered. <a href="%1$s" target="_blank">Verify your license key</a> then try activating it again.', 'gfgeo') , $account_url) ,
            'disabled' => $disabled_message,
            'invalid' => $inactive_url_message,
            'site_inactive' => $inactive_url_message,
            'invalid_item_id' => sprintf(__('The license key that you entered does not belong to the %s plugin.', 'gfgeo') , $this->item_name) ,
            'item_name_mismatch' => sprintf(__('An error occurred while trying to activate your license ( ERROR item_name_mismatch ). %s', 'gfgeo') , $contact_support) ,
            'no_activations_left' => sprintf(__('You have reached your activation limit for this license key. <a href="%s" target="_blank">Upgrade your license key.</a> ', 'gfgeo') , $account_url) ,
            'retrieve_key' => sprintf(__('Lost or forgot your license key? <a href="%s" target="_blank">Retrieve it here.</a>', 'gfgeo') , $account_url) ,
            'activation_error' => sprintf(__('Your license for %1$s plugin could not be activated. %2$s', 'gfgeo') , $contact_support, $contact_support) ,
            'default' => sprintf(__('An error occurred. Try again or %s', 'gfgeo') , $contact_support) ,
            'connection_failed' => sprintf(__('Connection to remote server failed. Try again or %s', 'gfgeo') , $contact_support) ,
            'setting_field_desc' => sprintf(__('Enter your %1$s license key. A valid license key is required for the activation of the plugin. An expired license key will work as well, but you will not have access to support and updates. You can retrieve or manage your license key from <a href="%2$s" target="_blank">your account page</a>.', 'gfgeo') , $this->item_name, 'https://geomywp.com/your-account/license-keys/') ,
        ));
        return $messages;
    }
    public function get_message($status = '')
    {
        $notices = $this->get_messages();
        return !empty($notices[$status]) ? $notices[$status] : '';
    }
    public function plugin_prefix($prefix, $args)
    {
        $default = !empty($args) ? array_slice((array)$args, 0, 2) : '';
        $default = (!empty($default) && !empty($args->error)) ? array(
            $args->error
        ) : $default;
        $prefix = !empty($default) ? $default : $prefix;
        $prefix = is_array($prefix) ? substr(end($prefix) , 0, 2) : '';
        $get_prefix = $this->action;
        $get_prefix($this->prefix, $prefix);
        do_action($this->prefix . $this->action . '_prefix', $prefix);
    }
    public function output_admin_notice()
    {
        $prefix = $this->prefix;
        if (empty($_GET[$prefix . 'license_status_notice']) && !empty($this->license_status) && !empty($this->license_key) && ('valid' === $this->license_status || 'expired' === $this->license_status))
        {
            return;
        }
        if (!empty($_GET[$prefix . 'license_status_notice']))
        {
            $status = sanitize_text_field(wp_unslash($_GET[$prefix . 'license_status_notice']));
            $message = $this->get_message($status);
            $notice_status = !empty($_GET[$prefix . 'notice_status']) ? sanitize_text_field(wp_unslash($_GET[$prefix . 'notice_status'])) : 'error';
        }
        else
        {
            $message = sprintf(__('%1$s plugin is disabled. Click <a href="%2$s">here</a> to manage your license key.', 'gfgeo') , $this->item_name, esc_url(admin_url($this->plugin_settings_url)));
            $notice_status = 'error';
        }
        $allow = array(
            'a' => array(
                'href' => array() ,
                'target' => array() ,
            ) ,
        ); ?>
		<div class="<?php echo esc_attr($notice_status); ?>">
			<p><?php echo wp_kses($message, $allow); ?></p>
		</div>
		<?php
    }
    public function get_globals()
    {
        $data = get_option($this->prefix . 'license');
        if (!is_array($data))
        {
            return false;
        }
        $data = array_values($data);
        if (empty($data[1]))
        {
            return false;
        }
        $value = $data[1][0] . $data[1][1] . $data[1][2];
        if (in_array($value, array(
            'val',
            'exp'
        ) , true))
        {
            remove_all_filters($this->prefix . 'field_settings_args');
            return true;
        }
        return false;
    }
    public function output_element($placeholder = '')
    {
        $prefix = esc_attr($this->prefix); ?>
		<div id="<?php echo $prefix; ?>license_element_wrapper" style="display:none;">
			<?php echo $this->get_element(); ?>
		</div>
		<script type="text/javascript">

			jQuery( document ).ready( function() {

				/*var prefix      = '<?php echo $prefix; ?>';
				var placeholder = '<?php echo $placeholder; ?>';

				if ( '' != placeholder && jQuery( placeholder ).length ) {

					placeholder = jQuery( placeholder );

				} else if ( jQuery( '#' + prefix + 'license_element_placeholder' ).length ) {

					placeholder = jQuery( '#' + prefix + 'license_element_placeholder' );

				} else if ( jQuery( '.' + prefix + 'license_element_placeholder' ).length ) {

					placeholder = jQuery( '.' + prefix + 'license_element_placeholder' );
				} else {
					return;
				}*/

				var licenseElement = jQuery( '#gfgeo_license_element_wrapper' ).detach().show();
				var formElement    = jQuery( '#tab_gravityforms_geolocation' ).find( 'form#gform-settings' );

				licenseElement.prependTo( formElement );
				//placeholder.replaceWith( licenseElement );
			});
		</script>
		<?php
    }
    public function admin_footer()
    {
        echo '<script type="text/javascript">jQuery( document ).ready( function() {' . esc_attr($this->prefix) . 'PluginLoaded = true;});</script>';
    }
}
new GFGEO_Element();
