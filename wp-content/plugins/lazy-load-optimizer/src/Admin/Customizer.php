<?php
namespace LazyLoadOptimizer\Admin;

use Premmerce\SDK\V2\FileManager\FileManager;
use  LazyLoadOptimizer\Admin\Customizer\CustomizeRange;
use  LazyLoadOptimizer\Admin\Customizer\CustomizeDonate;
use  LazyLoadOptimizer\Admin\Customizer\CustomizeLabel;
/**
 * Class Customizer
 *
 * @package ResponsiveTable\Admin
 */



class Customizer
{

    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
        $this->registerHooks();
    }

    public function registerHooks(){
        add_action('customize_register', array($this, 'addSection'));
        add_action('customize_register', array($this, 'addSettings'));
    }

    public function addSection($wp_customize){


        $wp_customize->add_section( 'lazyload_settings' , array(
            'title'      => __('Lazy load Settings', 'lazy-load-optimizer'),
            'priority'   => 200,
        ) );

    }

    public function addSettings($wp_customize){


        $checkboxOptions = array(
            'label' => __('Add lazy load styles', 'lazy-load-optimizer'),
            'default' => 1
        );
        $this->addSetting('lazyload_styles', 'checkbox', $wp_customize, $checkboxOptions);


        $wp_customize->add_setting('loading_label', array());
        $wp_customize->add_control(new CustomizeLabel($wp_customize, 'loading_label', array(
            'label' => __('Loading effects', 'lazy-load-optimizer'),
            'section' => 'lazyload_settings'
        )));

        $checkboxOptions = array(
            'label' => __('Spinner', 'lazy-load-optimizer'),
            'default' => 0
        );
        $this->addSetting('spinner', 'checkbox', $wp_customize, $checkboxOptions);

        $imageOptions = array(
            'label' => __('Spinner image', 'lazy-load-optimizer'),
            'default' => $this->fileManager->locateAsset('frontend/img/50x50-loader.gif'),
            'active_callback' => array( $this, 'isSpinnerSettingActive' )
        );
        $this->addSetting('spinner_image', 'image', $wp_customize,  $imageOptions);

        $rangeOptions = array(
            'label' => __('Spinner size (px)', 'lazy-load-optimizer'),
            'min' => 20,
            'max' => 100,
            'step' => 1,
            'default' => 30,
            'active_callback' => array( $this, 'isSpinnerSettingActive' )
        );
        $this->addSetting('spinner_size', 'range', $wp_customize, $rangeOptions);

        $checkboxOptions = array(
            'label' => __('Fade in', 'lazy-load-optimizer'),
            'default' => 0
        );
        $this->addSetting('fade_in', 'checkbox', $wp_customize, $checkboxOptions);

        $checkboxOptions = array(
            'label' => __('Fade in', 'lazy-load-optimizer'),
            'default' => 0
        );
        $this->addSetting('fade_in', 'checkbox', $wp_customize, $checkboxOptions);


        $checkboxOptions = array(
            'label' => __('Animation up', 'lazy-load-optimizer'),
            'default' => 0
        );
        $this->addSetting('animation', 'checkbox', $wp_customize, $checkboxOptions);

        $rangeOptions = array(
            'label' => __('Animation time (ms)', 'lazy-load-optimizer'),
            'min' => 0,
            'max' => 5000,
            'step' => 1,
            'default' => 300,
            'active_callback' => array( $this, 'isAnimationSettingActive' )

        );
        $this->addSetting('time_animation', 'range', $wp_customize, $rangeOptions);

        $colorOptions = array(
            'label' => __('Background color', 'lazy-load-optimizer'),
            'default' => '#ffffff',
            'active_callback' => array( $this, 'isTransparentBackgroundActive' )
        );
        $this->addSetting('lla_background_color', 'color', $wp_customize, $colorOptions);

        $checkboxOptions = array(
            'label' => __('Transparent background', 'lazy-load-optimizer'),
            'default' => 0
        );
        $this->addSetting('transparent_background', 'checkbox', $wp_customize, $checkboxOptions);


        $wp_customize->add_setting('lla_donate', array());
        $wp_customize->add_control(new CustomizeDonate($wp_customize, 'lla_donate', array(
            'label' => __('Donate to this plugin', 'lazy-load-optimizer'),
            'section' => 'lazyload_settings',
            'settings' => 'lla_donate',
        )));


    }


    private function addSetting($name, $type, $wp_customize, $args = array())
    {

        $default = array(
            'default' => '',
            'description' => '',
            'active_callback' => '',
            'label' => ''
        );
        $args = array_merge($default, $args);

        switch ($type) {
            case 'text':
            case 'checkbox':
                $wp_customize->add_setting($name, array(
                    'default' => $args['default']
                ));
                $wp_customize->selective_refresh->add_partial($name, array(
                    'selector' => '.' . $name
                ));
                $wp_customize->add_control($name, array(
                        'label' => $args['label'],
                        'section' => 'lazyload_settings',
                        'type' => $type
                    )
                );
                break;

            case 'select':
                $wp_customize->add_setting($name, array(
                    'capability' => 'edit_theme_options',
                    'default' => $args['default'],
                ));
                $wp_customize->selective_refresh->add_partial($name, array(
                    'selector' => '.' . $name
                ));

                $wp_customize->add_control($name, array(
                    'type' => 'select',
                    'section' => 'lazyload_settings',
                    'label' => $args['label'],
                    'description' => __($args['description'], 'lazy-load-optimizer'),
                    'choices' => $args['choices'],
                ));
                break;

            case 'radio':
                $wp_customize->add_setting($name, array(
                    'capability' => 'edit_theme_options',
                    'default' => $args['default'],
                ));
                $wp_customize->selective_refresh->add_partial($name, array(
                    'selector' => '.'.$name
                ));

                $wp_customize->add_control($name, array(
                    'type' => 'radio',
                    'section' => 'lazyload_settings',
                    'label' => $args['label'],
                    'description' => __($args['description'], 'lazy-load-optimizer'),
                    'choices' => $args['choices'],
                ));
                break;

            case 'range':
                $wp_customize->add_setting($name, array(
                    'default' => $args['default']
                ));
                $wp_customize->selective_refresh->add_partial($name, array(
                    'selector' => '.'.$name
                ));

                $wp_customize->add_control(new CustomizeRange($wp_customize, $name, array(
                    'label' => $args['label'],
                    'min' => $args['min'],
                    'max' => $args['max'],
                    'step' => $args['step'],
                    'section' => 'lazyload_settings',
                    'active_callback'	=> $args['active_callback']
                )));

                break;

            case 'color':
                $wp_customize->add_setting($name, array(
                    'default' => $args['default'],
                ));
                $wp_customize->selective_refresh->add_partial($name, array(
                    'selector' => '.'.$name
                ));

                $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, $name, array(
                    'label' => $args['label'],
                    'section' => 'lazyload_settings',
                    'settings' => $name,
                    'active_callback'	=> $args['active_callback'],
                )));


                break;

            case 'image':
                $wp_customize->add_setting($name, array(
                    'capability' => 'edit_theme_options',
                    'default' => $args['default'],
                ));


                $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, $name, array(
                    'label' => $args['label'],
                    'section' => 'lazyload_settings',
                    'settings' => $name,
                    'type' => 'image',
                    'active_callback'	=> $args['active_callback'],
                )));

                break;
        }



    }

     public function isSpinnerSettingActive($control){
         $spinner = $control->manager->get_setting( 'spinner' )->value();

        if($spinner) {
            return true;
        } else {
            return false;
        }
    }


    public function isAnimationSettingActive($control){
        $animation = $control->manager->get_setting( 'animation' )->value();
        $fadein = $control->manager->get_setting( 'fade_in' )->value();

        if($animation || $fadein) {
            return true;
        } else {
            return false;
        }
    }

    public function isTransparentBackgroundActive($control){
        $isTransparent = $control->manager->get_setting( 'transparent_background' )->value();

        if($isTransparent) {
            return false;
        } else {
            return true;
        }
    }

}