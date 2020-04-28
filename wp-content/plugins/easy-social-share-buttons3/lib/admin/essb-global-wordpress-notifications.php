<?php
/**
 * Global WordPress notifications manager. Allows to register and show admin notifications
 *
 * @version 1.0
 * @since 5.7
 * @package EasySocialShareButtons
 * @author appscreo
 */

class ESSBWordPressNotifications {

	private $notifications = array();

	private $interface_notifications = array();

	private static $instance = null;

	private $footer_script_added = false;

	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	} // end get_instance;

	/**
	 * Cloning disabled
	 */
	public function __clone() {
	}

	/**
	 * Serialization disabled
	 */
	public function __sleep() {
	}

	/**
	 * De-serialization disabled
	 */
	public function __wakeup() {
	}

	public function __construct() {

		// reset stored notifications in case there is an old data left
		$this->notifications = array();

		add_action('admin_notices', array($this, 'generate_active_notices'));

		add_action( 'wp_ajax_essb_notice_dismiss', array( $this, 'notice_dismiss' ) );
	}

	/**
	 * Register a new notification inside the pool
	 *
	 * @param unknown_type $key
	 * @param unknown_type $text
	 * @param unknown_type $buttons
	 * @param unknown_type $start_date
	 * @param unknown_type $end_date
	 */
	public function add_notification($key, $text, $buttons = array(), $type = '', $start_date = '', $end_date = '') {
		if (empty($key)) {
			return;
		}

		// notice was dismissed by the user
		if ($this->is_dismissed($key)){
			return;
		}

		// notice has a timeframe and it is not active right now
		if (!$this->is_active($key, $start_date, $end_date)) {
			return;
		}

		$buttons = $this->default_button($buttons);

		$this->notifications[$key] = array('text' => $text, 'buttons' => $buttons, 'type' => $type, 'start' => $start_date, 'end' => $end_date);
	}

	public function add_interface_notification($key, $text, $buttons = array(), $type = '', $start_date = '', $end_date = '') {
		if (empty($key)) {
			return;
		}

		// notice was dismissed by the user
		if ($this->is_dismissed($key)){
			return;
		}

		// notice has a timeframe and it is not active right now
		if (!$this->is_active($key, $start_date, $end_date)) {
			return;
		}

		$buttons = $this->default_button($buttons);

		$this->interface_notifications[$key] = array('text' => $text, 'buttons' => $buttons, 'type' => $type, 'start' => $start_date, 'end' => $end_date);
	}

	/**
	 * Notification is dismissed by user
	 *
	 * @param unknown_type $key
	 */
	public function is_dismissed($key) {
		$dismissed_notices = get_option( 'essb_dismissed_notices', false );

		if ( false === $dismissed_notices ) {
			$dismissed_notices = array();
		}

		if (!is_array($dismissed_notices)) {
			$dismissed_notices = array();
		}

		return isset($dismissed_notices[$key]) ? true : false;
	}

	/**
	 * Notification has a period of appearance. Check if it is active
	 *
	 * @param unknown_type $key
	 * @param unknown_type $fromdate
	 * @param unknown_type $todate
	 */
	public function is_active($key, $fromdate = '', $todate = '') {
		$is_active = true;

		if ($fromdate != '' || $todate != '') {
			$today = date ( "Ymd" );

			$fromdate = str_replace ( '-', '', $fromdate );
			$todate = str_replace ( '-', '', $todate );

			if (intval ( $today ) < intval ( $fromdate ) && intval ( $today ) > intval ( $todate )) {
				$is_active = false;
			}
		}

		return $is_active;
	}

	public function generate_active_notices() {
		$has_one = false;

		foreach ($this->notifications as $key => $data) {
			$type = isset($data['type']) ? $data['type'] : 'info';
			if ($type == '') {
				$type = 'info';
			}


			//type = warning, error, success, info

			echo '<div class="notice-'.$type.' notice essb-notice essb-notice-'.$key.'" data-key="'.$key.'">';
			echo '<p><strong>Easy Social Share Buttons for WordPress:</strong> '.$data['text'].'</p>';
			echo '<div class="actions">';
			foreach ($data['buttons'] as $button) {
				$is_dismiss = isset($button['dismiss']) ? $button['dismiss'] : '';

				if (!isset($button['url'])) {
					$button['url'] = '';
				}

				if (!isset($button['class'])) {
					$button['class'] = '';
				}

				echo '<a href="'.$button['url'].'" class="'.$button['class'].($is_dismiss == '1' ? ' essb-notice-dismiss' : '').'" data-notice="'.$key.'">'.$button['text'].'</a>';
			}

			echo '</div>';
			echo '</div>';

			$has_one = true;
		}

		if ($has_one) {
			$this->footer_script_added = true;
			add_action('admin_footer', array($this, 'admin_footer_script'));
		}
	}

	public function generate_interface_notifications() {
		$has_one = false;

		foreach ($this->interface_notifications as $key => $data) {
			$type = isset($data['type']) ? $data['type'] : 'info';
			if ($type == '') {
				$type = 'info';
			}


			//type = warning, error, success, info
			echo '<div class="essb-header-status">';
			echo '<div class="if-notice-'.$type.' if-notice essb-options-hint essb-options-hint-status  essb-notice essb-notice-'.$key.'" data-key="'.$key.'">';
			echo '<p>'.$data['text'].'</p>';
			echo '<div class="actions">';
			foreach ($data['buttons'] as $button) {
				$is_dismiss = isset($button['dismiss']) ? $button['dismiss'] : '';

				if (!isset($button['url'])) {
					$button['url'] = '';
				}

				if (!isset($button['class'])) {
					$button['class'] = '';
				}

				if (!isset($button['target'])) {
					$button['target'] = '';
				}

				echo '<a href="'.$button['url'].'" class="'.$button['class'].($is_dismiss == '1' ? ' essb-notice-dismiss' : '').'" '.($button['target'] != '' ? 'target="'.$button['target'].'"' : '' ).' data-notice="'.$key.'">'.$button['text'].'</a>';
			}

			echo '</div>';
			echo '</div>';
			echo '</div>';

			$has_one = true;
		}

		if ($has_one) {
			$this->footer_script_added = true;
			add_action('admin_footer', array($this, 'admin_footer_script'));
		}
	}

	public function notice_dismiss() {
		$key = isset($_POST['key']) ? $_POST['key'] : '';

		if ($key == '') {
			return;
		}

		$dismissed_notices = get_option( 'essb_dismissed_notices', false );

		if ( false === $dismissed_notices ) {
			$dismissed_notices = array();
		}

		if (!is_array($dismissed_notices)) {
			$dismissed_notices = array();
		}

		$dismissed_notices[$key] = date ( "Ymd" );

		update_option('essb_dismissed_notices', $dismissed_notices, 'no');
	}

	public function default_button($buttons = array(), $dismiss_text = '') {
		if (empty($buttons)) {
			$buttons = array();

			if ($dismiss_text == '') {
				$dismiss_text = __('Thank You. I understand.', 'essb');
			}

			$buttons[] = array('text' => $dismiss_text, 'url' => '', 'target' => '', 'class' => '', 'dismiss' => '1');
		}

		return $buttons;
	}

	public function admin_footer_script() {
		?>
		<script type="text/javascript">
		var essb_dismiss_ajax_url = "<?php echo esc_url(admin_url ('admin-ajax.php')); ?>";

		jQuery(document).ready(function($){
			$('.essb-notice-dismiss').each(function() {
				$(this).click(function(e) {
					e.preventDefault();

					var notice = $(this).attr('data-notice') || '';
					if ($('.essb-notice-' + notice).length) {
						$('.essb-notice-' + notice).fadeOut();
					}

                	$.ajax({
    		            type: "POST",
    		            url: essb_dismiss_ajax_url,
    		            data: { 'action': 'essb_notice_dismiss', 'key': notice },
    		            success: function (data) {

    		            }
                	});

				});
			});
		});
		</script>
		<?php
	}
}

global $essb_dash_notifications;
$essb_dash_notifications = ESSBWordPressNotifications::get_instance();

function essb_dashboard_notification($key, $text, $buttons = array(), $type = '', $start = '', $end = '') {
	global $essb_dash_notifications;

	$essb_dash_notifications->add_notification($key, $text, $buttons, $type, $start, $end);
}

function essb_interface_notification($key, $text, $buttons = array(), $type = '', $start = '', $end = '') {
	global $essb_dash_notifications;

	$essb_dash_notifications->add_interface_notification($key, $text, $buttons, $type, $start, $end);
}

function essb_dashboard_notification_dismiss_button($dismiss_text = '') {
	global $essb_dash_notifications;

	return $essb_dash_notifications->default_button(array(), $dismiss_text);
}

function essb_show_interface_notifications() {
	global $essb_dash_notifications;

	$essb_dash_notifications->generate_interface_notifications();
}
