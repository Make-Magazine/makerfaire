<?php namespace LazyLoadOptimizer;

use Premmerce\SDK\V2\FileManager\FileManager;
use LazyLoadOptimizer\Admin\Admin;
use LazyLoadOptimizer\Admin\Customizer;
use LazyLoadOptimizer\Frontend\Frontend;
use  LazyLoadOptimizer\Admin\Settings;

/**
 * Class LazyLoadOptimizerPlugin
 *
 * @package LazyLoadOptimizer
 */
class LazyLoadOptimizerPlugin
{

    const VERSION = '1.3.3';

    const DOMAIN = 'lazy-load-optimizer';

	/**
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * LazyLoadOptimizerPlugin constructor.
	 *
     * @param string $mainFile
	 */
    public function __construct($mainFile) {
        $this->fileManager = new FileManager($mainFile);

        add_action('plugins_loaded', [ $this, 'loadTextDomain' ]);

	}

	/**
	 * Run plugin part
	 */
	public function run() {
	    
		if ( is_admin() ) {
			new Admin( $this->fileManager );
		} else {
			new Frontend( $this->fileManager );
		}
		new Customizer($this->fileManager);

	}

    /**
     * Load plugin translations
     */
    public function loadTextDomain()
    {
        $name = $this->fileManager->getPluginName();
        load_plugin_textdomain('lazy-load-optimizer', false, $name . '/languages/');
    }

	/**
	 * Fired when the plugin is activated
	 */
	public function activate() {
		// TODO: Implement activate() method.

        $options = get_option( Settings::OPTIONS );
        $options['cssClasses'] = Settings::CSS_CLASSES;
        $options['excludeUrl'] = Settings::EXCLUDE_URL;
        $options['expand'] = Settings::EXPAND;
        $options['expFactor'] = Settings::EXP_FACTOR;
        $options['loadHidden'] = Settings::LOAD_HIDDEN;
        $options['init'] = Settings::INIT;
        $options['loadIframes'] = Settings::LOAD_IFRAMES;
        $options['loadFrontPage'] = 0;
        $options['loadPosts'] = 0;
        $options['loadPages'] = 0;
        $options['loadCategory'] = 0;
        $options['loadTag'] = 0;
        update_option(Settings::OPTIONS, $options);
	}

	/**
	 * Fired when the plugin is deactivated
	 */
	public function deactivate() {
		// TODO: Implement deactivate() method.

	}

	/**
	 * Fired during plugin uninstall
	 */
	public static function uninstall() {
		// TODO: Implement uninstall() method.
        delete_option(Settings::OPTIONS);
	}
}