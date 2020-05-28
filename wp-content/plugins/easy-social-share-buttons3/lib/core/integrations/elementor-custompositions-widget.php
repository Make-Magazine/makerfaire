<?php
namespace Elementor;

if (! defined('ABSPATH'))
    exit();
 // Exit if accessed directly
class ESSB_Elementor_Custom_Positions_Widget extends Widget_Base {

    public function get_name () {
        return 'social-share-display';
    }

    public function get_title () {
        return esc_html__('Social Share Buttons Display', 'essb');
    }

    public function get_icon () {
        return 'eicon-share';
    }

    public function get_categories () {
        return [ 
            'basic', 'essb' 
        ];
    }

    protected function _register_controls () {
        $this->start_controls_section('section_my_custom', array ( 
            'label' => esc_html__('Display Setup', 'essb') 
        ));
        
        $this->add_control('display', [ 
            'label' => esc_html__('Custom Design', 'essb'), 
            'type' => Controls_Manager::SELECT, 'default' => '', 'options' => essb5_get_custom_positions() 
        ]);
        
        $this->add_control('always_show_desc', [ 
            'type' => Controls_Manager::RAW_HTML, 
            'raw' => esc_html__('The custom display/position selected above won\'t generate share buttons if you do not enable it inside the Positions menu. You can bypass this activation by enabling the option to Always show share buttons. If you are using the display on an Archive template do not forget to enable the Used on an archive template option.', 'essb'), 'content_classes' => 'elementor-descriptor' 
        ]);
        
        $this->add_control('force', [ 
            'label' => esc_html__('Always Show', 'essb'), 
            'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('No', 'essb'), 'label_on' => esc_html__('Yes', 'essb'), 
            'default' => 'no' 
        ]);
        
        $this->add_control('archive', [ 
            'label' => esc_html__('Used on an archive template', 'essb'), 
            'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('No', 'essb'), 'label_on' => esc_html__('Yes', 'essb'), 
            'default' => 'no' 
        ]);
        
        $this->add_control('colors_warning', [ 
            'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('Note: If you need to add additional displays you can do this from Where to Display -> Custom Position/Displays', 'essb'), 'content_classes' => 'elementor-descriptor' 
        ]);
        
        $this->end_controls_section();
    }

    protected function render ($instance = array()) {
        $settings = $this->get_settings_for_display();
        
        // get our input from the widget settings.
        $force = ! empty($settings['force']) ? $settings['force'] : '';
        $archive = ! empty($settings['archive']) ? $settings['archive'] : '';
        $display = ! empty($settings['display']) ? $settings['display'] : '';
        
        $force = ($force == 'yes') ? true : false;
        $archive = ($archive == 'yes') ? true : false;
        
        essb_custom_position_draw($display, $force, $archive);
    }

    protected function content_template () {}

    public function render_plain_content ($instance = []) {}
}
