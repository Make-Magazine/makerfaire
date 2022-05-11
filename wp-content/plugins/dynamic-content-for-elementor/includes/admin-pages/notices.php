<?php

namespace DynamicContentForElementor\AdminPages;

class Notices
{
    const DB_PREFIX = 'dce_dismissed_';
    private $admin_notices = [];
    const TYPES = ['error', 'warning', 'info', 'success'];
    public function __construct()
    {
        add_action('admin_notices', array(&$this, 'action_admin_notices'));
        add_action('admin_init', array(&$this, 'action_admin_init'));
    }
    public function error($message, $dismiss_key = \false)
    {
        $this->notice('error', $message, $dismiss_key);
    }
    public function warning($message, $dismiss_key = \false)
    {
        $this->notice('warning', $message, $dismiss_key);
    }
    public function success($message, $dismiss_key = \false)
    {
        $this->notice('success', $message, $dismiss_key);
    }
    public function info($message, $dismiss_key = \false)
    {
        $this->notice('info', $message, $dismiss_key);
    }
    private function notice($type, $message, $dismiss_key)
    {
        $notice = ['message' => $message, 'dismiss_key' => $dismiss_key, 'type' => $type];
        $this->admin_notices[] = $notice;
    }
    public function action_admin_init()
    {
        $dismiss_key = \filter_input(\INPUT_GET, 'dce_dismiss', \FILTER_SANITIZE_STRING);
        if (\is_string($dismiss_key)) {
            update_user_meta(get_current_user_id(), self::DB_PREFIX . $dismiss_key, \true);
            wp_die('Notice dismissed', 'Notice dismissed', ['response' => 200]);
        }
    }
    /**
     * @param string $message
     * @param string $type
     * @param string|false $dismiss_key
     * @return void
     */
    public static function render_notice($message, $type, $dismiss_key = \false)
    {
        $classes = "notice dce-generic-notice notice-{$type}";
        $dismiss_attr = '';
        if ($dismiss_key) {
            $classes .= ' dce-dismissible-notice is-dismissible';
            $dismiss_url = add_query_arg(array('dce_dismiss' => $dismiss_key), admin_url());
            $dismiss_attr .= ' data-dismiss-url="' . esc_url($dismiss_url) . '"';
        }
        $icon_url = DCE_URL . '/assets/media/dce.png';
        $html = <<<EOD
<div class="{$classes}" {$dismiss_attr}>
<div class="img-responsive pull-left">
\t<img class='dce-logo' src="{$icon_url}" title="<?php echo DCE_PRODUCT_NAME_LONG; ?>">
</div>
<p><strong>Dynamic.ooo - Dynamic Content for Elementor</strong><br />
{$message}
</div>
EOD;
        echo $html;
    }
    /**
     * @return void
     */
    public function action_admin_notices()
    {
        foreach ($this->admin_notices as $admin_notice) {
            $dismiss_key = $admin_notice['dismiss_key'];
            $user = get_current_user_id();
            // User dismissed, no need to display:
            if ($dismiss_key && get_user_meta($user, self::DB_PREFIX . $dismiss_key, \true)) {
                continue;
            }
            $message = $admin_notice['message'];
            $type = $admin_notice['type'];
            self::render_notice($message, $type, $dismiss_key);
        }
    }
}
