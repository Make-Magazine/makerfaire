<?php namespace LazyLoadOptimizer\Admin;

use Premmerce\SDK\V2\FileManager\FileManager;

class Settings
{

    const OPTIONS = 'lazy_load';

    const SETTINGS_PAGE = 'lazy_load_page';

    private $fileManager;

    private $options;

//plugin default options

    const CSS_CLASSES = '';
    const EXCLUDE_URL = '';
    const EXPAND = '';
    const EXP_FACTOR = '';
    const INIT = true;
    const LOAD_HIDDEN = 1;
    const LOAD_IFRAMES = 1;


    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
        $this->options = get_option(self::OPTIONS);
        if(!isset($this->options['init'])){
            $this->options['init'] = self::INIT;
            update_option(self::OPTIONS, $this->options);
        }
    }


    public function registerSettings()
    {
        register_setting(self::OPTIONS, self::OPTIONS, array(
            'sanitize_callback' => array($this, 'updateSettings'),
        ));

        add_settings_section('main_settings', __('', 'lazy-load-optimizer'), array(
            $this,
            'mainSection',
        ), self::SETTINGS_PAGE);

        add_settings_section('exceptions_settings', __('', 'lazy-load-optimizer'), array(
            $this,
            'exceptionSection',
        ), 'exceptions');

    }

    public function exceptionSection()
    {
        $this->fileManager->includeTemplate('admin/section/exception-settings.php', array(
            'cssClasses' => $this->getOption('cssClasses'),
            'excludeUrl' => $this->getOption('excludeUrl'),
            'loadFrontPage' => $this->getOption('loadFrontPage'),
            'loadPosts' => $this->getOption('loadPosts'),
            'loadPages' => $this->getOption('loadPages'),
            'loadCategory' => $this->getOption('loadCategory'),
            'loadTag' => $this->getOption('loadTag')
        ));
    }


    public function showSettingsExceptions()
    {
        print('<form action="' . admin_url('options.php') . '" method="post">');

        //settings_errors();

        settings_fields(self::OPTIONS);

        do_settings_sections('exceptions');

        submit_button();
        print('</form>');
    }

    public function mainSection()
    {
        $this->fileManager->includeTemplate('admin/section/main-settings.php', array(
            'cssClasses' => $this->getOption('cssClasses'),
            'loadIframes' => $this->getOption('loadIframes'),
            'loadHidden' => $this->getOption('loadHidden'),
            'init' => $this->getOption('init'),
            'expand' => $this->getOption('expand'),
            'expFactor' => $this->getOption('expFactor')
        ));
    }

    public function showSettings()
    {
        print('<form action="' . admin_url('options.php') . '" method="post">');

        //settings_errors();

        settings_fields(self::OPTIONS);

        do_settings_sections(self::SETTINGS_PAGE);

        submit_button();
        print('</form>');
    }


    public function updateSettings($settings)
    {


         if($_POST['section'] == 'main'){
             if(!isset($settings['init']))
                 $settings['init'] = 0;

             if(!isset($settings['loadHidden']))
                 $settings['loadHidden'] = 0;

             if(!isset($settings['loadIframes']))
                 $settings['loadIframes'] = 0;
         }

        if($_POST['section'] == 'exception'){
            if(!isset($settings['loadFrontPage']))
                $settings['loadFrontPage'] = 0;

            if(!isset($settings['loadPosts']))
                $settings['loadPosts'] = 0;

            if(!isset($settings['loadPages']))
                $settings['loadPages'] = 0;

            if(!isset($settings['loadCategory']))
                $settings['loadCategory'] = 0;

            if(!isset($settings['loadTag']))
                $settings['loadTag'] = 0;
        }

        $options_old = get_option(self::OPTIONS);
        $settings = array_merge($options_old, $settings);

        return $settings;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getOption($key, $default = null)
    {
        return isset($this->options[ $key ])? $this->options[ $key ] : $default;
    }
}
