<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Maker Shed purchases Widget
 *
 * Elementor widget that shows a list of recent purchases for a user from makershed
 *
 * @since 1.0.0
 */
class Elementor_mShedPurch_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve mShedPurch widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'mshedpurch';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve mShedPurch widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Maker Shed Recent Purchases', 'elementor-make-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve mShedPurch widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-custom';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the mShedPurch widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'make-category' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the mShedPurch widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'make', 'shed', 'purchases' ];
	}

	/**
	 * Register mShedPurch widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-make-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
    			'title',
    			[
    				'label' => esc_html__( 'Title (optional)', 'elementor-make-widget' ),
    				'type' => \Elementor\Controls_Manager::TEXT,
    				'placeholder' => esc_html__( 'Enter your title', 'elementor-make-widget' ),
    			]
    		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'elementor-make-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label' => esc_html__( 'Icon Alignment', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'after' => esc_html__( 'After', 'elementor-make-widget' ),
					'before' => esc_html__( 'Before', 'elementor-make-widget' ),
				],
				'default' => 'after',
				'prefix_class' => 'expandobox-align-',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render mshedpurch widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$current_user = wp_get_current_user();
		$user_email = $current_user->user_email;

    //if we are in the elementor admin, use this email as an example
    if ( \Elementor\Plugin::$instance->editor->is_edit_mode() && current_user_can('administrator')) {
    	$user_email = "ken@nmhu.edu";
		}

    $api_url      = 'https://4e27971e92304f98d3e97056a02045f1:32e156e38d7df1cd6d73298fb647be72@makershed.myshopify.com';
    $customer_api = $api_url . '/admin/customers/search.json?query=email:' . $user_email  . '&fields=id';
		$customer_content = MakeBasicCurl($customer_api);

    // Decode the JSON in the file
    $customer = ((isset($customer_content) && !empty($customer_content)) ? json_decode($customer_content, true) : array());
		$output = "";
		$output = '<div class="dashboard-box make-elementor-expando-box">';
    $output .= 	'<h4 class="closed">'. ($settings['title']!=''?$settings['title']:'Makershed Orders').'</h4>';
    $output .= 	'<ul class="closed">';

		if (isset($customer['customers']) && !empty($customer['customers']) ) {
        $customerID = $customer['customers'][0]['id'];
        $orders_api = $api_url . '/admin/orders.json?customer_id=' . $customerID;
        $orders_content = MakeBasicCurl($orders_api);
        $orderJson = json_decode($orders_content, true);
  			if ( empty($orderJson["orders"]) ) {
        	$output .= 	'<li>
                        <p>Looks like you haven\'t placed any orders yet...</p><br />
                        <a href="https://makershed.com" target="_blank" class="btn universal-btn">Here\'s your chance!</a>
                    </li>';

        } else {
          foreach ($orderJson['orders'] as $order) {
          	$output .= 	'<li><p><b><a href="'. $order['order_status_url'].'">Order #'. $order['id'].'</a></b></p>';
            foreach ($order['line_items'] as $line_item) {
							$output .= 	'<p>'. $line_item['name'] .' - $'. $line_item['price'].'</p>';
            }
            $output .= 	'</li>';
					}
        }
			} else {
				$output .=
				'<li>
					<p> Looks like you haven\'t placed any orders yet...</p><br />
					<a href="https://makershed.com" target="_blank" class="btn universal-btn">Here\'s your chance!</a>
				</li>';
			}
			$output .=
      '</ul>
		</div>';
    echo $output;
	}

}
