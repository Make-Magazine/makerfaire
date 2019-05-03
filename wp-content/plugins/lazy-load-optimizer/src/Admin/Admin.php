<?php namespace LazyLoadOptimizer\Admin;

use Premmerce\SDK\V2\FileManager\FileManager;
use LazyLoadOptimizer\LazyLoadOptimizerPlugin;

/**
 * Class Admin
 *
 * @package LazyLoadOptimizer\Admin
 */
class Admin {

	/**
	 * @var FileManager
	 */
	private $fileManager;

    /**
     * @var string
     */
    private $settingsPage;

    /**
     * @var Settings
     */
    private $settings;

	/**
	 * Admin constructor.
	 *
	 * Register menu items and handlers
	 *
	 * @param FileManager $fileManager
	 */
	public function __construct( FileManager $fileManager ) {
		$this->fileManager = $fileManager;
        $this->settings = new Settings($fileManager);
        $this->settingsPage = LazyLoadOptimizerPlugin::DOMAIN . '-admin';
        $this->registerActions();
    }

    public function registerActions(){
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_filter('plugin_action_links_lazy-load-optimizer/lazy-load-optimizer.php', array($this, 'PluginActionLinks'));
        add_action('admin_init', array($this->settings, 'registerSettings'));
        add_action('admin_menu', array($this, 'addMenuPage'));

    }

    public function enqueueScripts()
    {

        wp_enqueue_style(
            'lla-admin-styles',
            $this->fileManager->locateAsset('admin/css/lla-admin.css'),
            array(),
            LazyLoadOptimizerPlugin::VERSION
        );

    }

    public function PluginActionLinks($links)
    {
        $action_links = array(
            'settings' => '<a href="' . admin_url('admin.php?page=lazy-load-optimizer-admin') .
    '" aria-label="' . esc_attr__('Lazy Load Optimizer', 'lazy-load-optimizer') .
    '">' . esc_html__('Settings', 'lazy-load-optimizer') .
    '</a>');

        return array_merge($action_links, $links);
    }


    public function addMenuPage()
    {

        add_options_page(
            __('Lazy Load Optimizer', 'lazy-load-optimizer'),
            __('Lazy Load Optimizer', 'lazy-load-optimizer'),
            'edit_theme_options',
            $this->settingsPage,
            array($this, 'optionsPage')
        );
    }

    /**
     * Options page
     */
    public function optionsPage()
    {

        $current = isset($_GET['tab']) ? $_GET['tab'] : 'settings';

        $tabs['settings'] = __('General', 'lazy-load-optimizer');
        $tabs['exceptions'] = __('Exceptions settings', 'lazy-load-optimizer');


        $this->fileManager->includeTemplate('admin/main.php', array(
            'settings' => $this->settings,
            'tabs' => $tabs,
            'current' => $current,
        ));
    }

}