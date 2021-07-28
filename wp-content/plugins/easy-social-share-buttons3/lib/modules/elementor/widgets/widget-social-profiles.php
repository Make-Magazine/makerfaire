<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ESSB_Elementor_Social_Profiles_Widget extends Widget_Base {
	public function get_name() {
		return 'profile-links';
	}
	
	public function get_title() {
		return esc_html__( 'Social Profiles', 'essb' );
	}
	
	public function get_icon() {
		return 'eicon-social-icons';
	}
	
	public function get_categories() {
		return [ 'essb' ];
	}
	
	protected function _register_controls() {
		
	    /**
	     * Loading shortcode options as a base
	     */
	    if (!function_exists('essb_get_shortcode_options_easy_profiles')) {
	        include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/shortcode-options/easy-profiles.php');
	    }
	    
		$this->start_controls_section(
			'section_my_custom',
			array(
				'label' => esc_html__( 'Options', 'essb' ),
			)
		);
		
		// setting just the option parameters from the shortode
		$general_options = array('template', 'animation', 'align', 'size', 'nospace', 'columns', 'cta', 'cta_vertical');
		$shortcode_settings = essb_get_shortcode_options_easy_profiles();
		
		foreach ($shortcode_settings as $field => $options) {
		    if (!in_array($field, $general_options)) { continue; }
		    $default = '';
		    $title = $options['title'];
		    $type = $options['type'];
		    
		    if ($type == 'select') {
		        $values = isset($options['options']) ? $options['options'] : array();		        
		       		        
		        $this->add_control(
		            $field,
		            [
		                'label' => $title,
		                'type' => Controls_Manager::SELECT,
		                'default' => $default,
		                'options' =>  $values
		            ]
		            );
		        
		    }
		    
		    if ($type == 'textbox') {
		        $this->add_control(
		            $field,
		            [
		                'label' => $title,
		                'type' => Controls_Manager::TEXT,
		                'default' => '',
		            ]
		            );
		    }
		    
		    if ($type == 'checkbox') {
		        $this->add_control(
		            $field,
		            [
		                'label' => $title,
		                'type' => Controls_Manager::SWITCHER,
		                'label_off' => esc_html__( 'No', 'essb' ),
		                'label_on' => esc_html__( 'Yes', 'essb' ),
		                'default' => 'no',
		            ]
		            );
		    }
		}
		
		
		$this->end_controls_section();
		
		$this->start_controls_section(
		    'section_my_networks',
		    array(
		        'label' => esc_html__( 'Networks', 'essb' ),
		    )
		    );
		
		$this->add_control(
		    'profiles_all_networks',
		    [
		        'label' => 'Custom network list',
		        'description' => 'Enable if you need to customize the configured networks in the settings',
		        'type' => Controls_Manager::SWITCHER,
		        'label_off' => esc_html__( 'No', 'essb' ),
		        'label_on' => esc_html__( 'Yes', 'essb' ),
		        'default' => 'no',
		    ]
		    );
		
		foreach (essb_available_social_profiles() as $key => $value) {
		    $this->add_control(
		        'profile_'.$key,
		        [
		            'label' => $value . ' URL',
		            'type' => Controls_Manager::TEXT,
		            'default' => '',
		            'condition' => [
		                'profiles_all_networks' => 'yes',
		            ],
		        ]
		        );
		    $this->add_control(
		        'profile_text_'.$key,
		        [
		            'label' => $value . ' follow text',
		            'type' => Controls_Manager::TEXT,
		            'default' => '',
		            'condition' => [
		                'profiles_all_networks' => 'yes',
		            ],
		            'separator' => 'after'
		        ]
		        );
		}
		
		$this->end_controls_section();
	}
	
	protected function render( $instance = array() ) {
		$settings = $this->get_settings_for_display();
		
		$custom_network_list = '';
		foreach (essb_available_social_profiles() as $key => $value) {
		    $has_url = isset($settings['profile_'.$key]) ? $settings['profile_'.$key] : '';
		    
		    if ($has_url != '') {
		        $custom_network_list .= ($custom_network_list != '' ? ',' : '') . $key;
		    }
		}
		
		if ($custom_network_list != '') {
		    $settings['networks'] = $custom_network_list;
		}
		
		essb_depend_load_class('\ESSBCoreExtenderShortcodeProfiles', 'lib/core/extenders/essb-core-extender-shortcode-profiles.php');
		echo \ESSBCoreExtenderShortcodeProfiles::parse_shortcode($settings, \ESSB_Plugin_Options::read_all());				
	}
	
	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
