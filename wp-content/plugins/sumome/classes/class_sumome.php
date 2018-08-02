<?php
class WP_Plugin_SumoMe {
  public function __construct() {
    add_action('wp_ajax_sumome_main', array($this, 'ajax_sumome_main'));
    add_action('wp_ajax_sumome_dashboard_welcome', array($this, 'ajax_sumome_dashboard_welcome'));
    add_action('wp_ajax_sumome_hide_dashboard_overlay', array($this, 'ajax_sumome_hide_dashboard_overlay'));
    add_action('wp_footer', array($this, 'append_script_code'));
    add_action('admin_footer', array($this, 'append_admin_script_code'));
    add_action('admin_menu', array($this, 'admin_menu'));
    add_action('admin_init', array($this, 'admin_init'));
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    add_action('wp_dashboard_setup', array($this, 'dashboard_setup'));

    //add to front end footer
    add_action('wp_footer', array($this, 'woocommerce_cart_scripts'));
    //add to backend footer
    add_action('admin_footer', array($this, 'woocommerce_cart_scripts'));

    // WooCommerce helpers
    add_action('wp_ajax_nopriv_sumo_get_woocommerce_cart_subtotal', array($this, 'ajax_sumo_get_woocommerce_cart_subtotal'));
    add_action('wp_ajax_nopriv_sumo_add_woocommerce_coupon', array($this, 'ajax_sumo_add_woocommerce_coupon'));
    add_action('wp_ajax_nopriv_sumo_remove_woocommerce_coupon', array($this, 'ajax_sumo_remove_woocommerce_coupon'));

    add_action('wp_ajax_sumo_get_woocommerce_cart_subtotal', array($this, 'ajax_sumo_get_woocommerce_cart_subtotal'));
    add_action('wp_ajax_sumo_add_woocommerce_coupon', array($this, 'ajax_sumo_add_woocommerce_coupon'));
    add_action('wp_ajax_sumo_remove_woocommerce_coupon', array($this, 'ajax_sumo_remove_woocommerce_coupon'));

    $this->dataSumoPlatform="wordpress";
    if (get_option('endurance_user')==1)  $this->dataSumoPlatform="wordpress-endurance";
  }

  public function activate_SumoMe_plugin() {
    WP_Plugin_SumoMe::upgrade_manual_sumome_installation();
    WP_Plugin_SumoMe::ajax_sumome_show_dashboard_overlay();
  }

  public function deactivate_SumoMe_plugin() {
    WP_Plugin_SumoMe::ajax_sumome_show_dashboard_overlay();
  }

  public function admin_init() {
    register_setting('sumome', 'sumome_site_id', array($this, 'sanitize_site_id'));

    $this->check_generate_site_id();

    add_settings_section(
      'sumome-settings',
      'Settings',
      null,
      'sumome'
    );

    add_settings_field(
      'sumome-site_id',
      '',
      array(&$this, 'settings_field_site_id'),
      'sumome',
      'sumome-settings',
      array('field' => 'sumome_site_id', 'label_for' => 'sumome_site_id')
    );
  }

  public function admin_menu() {
    add_menu_page('Sumo', 'Sumo', 'manage_options', 'sumo', array($this, 'sumome_render_dashboard_page'), plugins_url('images/icon.png', SUMOME__PLUGIN_FILE));

    if (isset($_COOKIE['__smUser'])) {
      add_submenu_page('sumo', 'Dashboard', 'Dashboard', 'manage_options', 'sumo', array($this, 'sumome_render_dashboard_page') );
      add_submenu_page('sumo', 'Statistics', 'Statistics', 'manage_options', 'sumo-statistics', array($this, 'sumome_render_statistics_page'));
      add_submenu_page('sumo', 'About', 'About', 'manage_options', 'sumo-about', array($this, 'sumome_render_welcome_page'));
    }

    add_submenu_page(null, 'SiteID', 'SiteID', 'manage_options', 'sumo-siteID', array($this, 'sumome_render_siteID_page'));
  }

  public function sanitize_site_id($value) {
    $value = preg_replace('/[^0-9a-f]/', '', strtolower($value));

    return $value;
  }

  public function settings_field_site_id($args) {
    $field = $args['field'];
    $value = get_option($field);

    if (!$value) {

    }

    ?>
    <script type="text/javascript">
    function sumome_generate_site_id() {
      function _sumome_r() {
        return (Math.random().toString(16)+"000000000").substr(2,8);
      }

      var new_sumome_site_id = _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r();

      jQuery('.sumome-site-id').val(new_sumome_site_id);

    }
    </script>
    <?php
    echo sprintf('<textarea type="text" name="%s" id="%s" class="sumome-site-id" />%s</textarea><button onclick="sumome_generate_site_id(); return false;" class="button">Get New Site ID</button>', $field, $field, esc_attr($value));
  }

  public function check_generate_site_id() {
    $site_id = get_option('sumome_site_id');

    if (!$site_id || WP_Plugin_SumoMe::blacklisted_site_id($site_id)) {
      list($usec, $sec) = explode(' ', microtime());
      $sumoSeed=(float) $sec + ((float) $usec * 100000);

      mt_srand($sumoSeed);
      $site_id='';
      for ($i=0;$i<8;$i++) {
        $site_id.=substr(dechex(mt_rand()).'000000000',2,8);
      }

      update_option('sumome_site_id', $site_id);
    }
  }

  private function blacklisted_site_id($site_id) {
    $blacklist=array("8ce3f35797bf87c1644e567db13d9b3c2d9422027c10a7874b3446c9283c9aad");
    if ($site_id && in_array($site_id, $blacklist)) return true;
  }

  public function upgrade_manual_sumome_installation() {
    $wpThemeFiles=array("header","footer");
    foreach ($wpThemeFiles as $wpThemeFile) {   
      $themeFile=get_template_directory()."/".$wpThemeFile.".php";

      if (file_exists($themeFile) && is_writable($themeFile)) {
        $themeFile_contents = file_get_contents($themeFile);

        libxml_use_internal_errors(true);
        $pageDom = new \DOMDocument('1.0', 'utf-8');
        $pageDom->loadHTML($themeFile_contents);
        $scripts = $pageDom->getElementsByTagName('script');

        for ($i = 0; $i < $scripts->length; $i++) {

          $scriptDoc = new \DOMDocument('1.0', 'utf-8');
          $scriptDoc->appendChild($scriptDoc->importNode($scripts->item($i), true));
          $scriptLine=$scriptDoc->saveHTML();

          if ($scriptLine) {
            $dom = new \DOMDocument('1.0', 'utf-8');
            $dom->loadHTML($scriptLine);
            $nodes = $dom->getElementsByTagName('script');

            if (substr_count($scriptLine, 'data-sumo-site-id')>0) {
              $manuallyInsertedScriptTagSiteID=$nodes->item(0)->getAttribute('data-sumo-site-id');
            } else {
              $scriptBreakdown=explode(';', $scriptLine);
              
              if ($scriptBreakdown) foreach ($scriptBreakdown as $scriptBreakdownSpec) {
                if (substr_count($scriptBreakdownSpec, 'j.dataset.sumoSiteId'))  $siteIDLine=$scriptBreakdownSpec;
              }
              $findSiteID[]="'";
              $findSiteID[]="j.dataset.sumoSiteId=";
              $manuallyInsertedScriptTagSiteID=str_replace($findSiteID,"",$siteIDLine);
            }        
          }
        }

        if (trim($manuallyInsertedScriptTagSiteID)!="") {

          //save users site ID from the manually inserted tag
          update_option('sumome_site_id', $manuallyInsertedScriptTagSiteID);

          $sumomeScriptTag[]='<script src="//load.sumome.com/" data-sumo-site-id="' . esc_attr($manuallyInsertedScriptTagSiteID) . '" async="async"></script>';
          $sumomeScriptTag[]='<script src="//load.sumo.com/" data-sumo-site-id="' . esc_attr($manuallyInsertedScriptTagSiteID) . '" async="async"></script>';
          $sumomeScriptTag[]="<script async>(function(s,u,m,o,j,v){j=u.createElement(m);v=u.getElementsByTagName(m)[0];j.async=1;j.src=o;j.dataset.sumoSiteId='".esc_attr($manuallyInsertedScriptTagSiteID)."';v.parentNode.insertBefore(j,v)})(window,document,'script','//load.sumo.com/');</script>";
          $modified_themeFile = str_replace($sumomeScriptTag,"",$themeFile_contents);

          //make backup of theme file just in case
          copy($themeFile,get_template_directory()."/".$wpThemeFile.".bak[".date('Y-m-d_H.i.s')."].php");

          //remove manually inserted SumoMe tag
          if (trim($modified_themeFile)) file_put_contents($themeFile,$modified_themeFile);
        }
      }
    }
  }


  public function append_script_code() {
    $this->check_generate_site_id();

    $site_id = get_option('sumome_site_id');

    if ($site_id) {
      echo("<script async>(function(s,u,m,o,j,v){j=u.createElement(m);v=u.getElementsByTagName(m)[0];j.async=1;j.src=o;j.dataset.sumoSiteId='".esc_attr($site_id)."';j.dataset.sumoPlatform='".$this->dataSumoPlatform."';v.parentNode.insertBefore(j,v)})(window,document,'script','//load.sumo.com/');</script>");
    }
  }

  public function append_admin_script_code() {
    if (defined('XMLRPC_REQUEST') || defined('DOING_AJAX') || defined('IFRAME_REQUEST'))
      return false;

    $this->check_generate_site_id();

    $site_id = get_option('sumome_site_id');

    if ($site_id) {
      include(SUMOME__PLUGIN_DIR.'/js/preload.php');
      echo("<script async>(function(s,u,m,o,j,v){j=u.createElement(m);v=u.getElementsByTagName(m)[0];j.async=1;j.src=o;j.dataset.sumoSiteId='".esc_attr($site_id)."';j.dataset.sumoPlatform='".$this->dataSumoPlatform."';j.dataset.sumoMode='admin';v.parentNode.insertBefore(j,v)})(window,document,'script','//load.sumo.com/');</script>");
    }
  }

  public function admin_enqueue_scripts() {
    wp_enqueue_style('sumome-admin-styles', plugins_url('styles/styles.css', SUMOME__PLUGIN_FILE));
    wp_enqueue_style('sumome-admin-media', plugins_url('styles/media.css', SUMOME__PLUGIN_FILE));
  }

  public function sumome_render_welcome_page() {
    $noClose=true;
    print '<div class="sumome-plugin-container"><div class="sumome-plugin-main">';
    include(SUMOME__PLUGIN_DIR.'/views/wordpress-dashboard-welcome-page.php');
    print '</div></div>';
    $this->sumome_plugin_only();
  }

  public function sumome_render_dashboard_page() {
    include(SUMOME__PLUGIN_DIR.'/js/general.php');
    include(SUMOME__PLUGIN_DIR.'/views/landing.php');
    $this->sumome_plugin_only();
  }

  public function sumome_render_statistics_page() {
    print '<link rel="stylesheet" type="text/css" href="'.plugins_url('styles/statistics.css', dirname(__FILE__)).'">';
    include(SUMOME__PLUGIN_DIR.'/views/statistics.php');
    $this->sumome_plugin_only();
  }

  public function sumome_render_siteID_page() {
    include(SUMOME__PLUGIN_DIR.'/views/siteID.php');
    $this->sumome_plugin_only();
  }

  public function sumome_plugin_only() {
      ?>
      <script>
      function sumo_logout_redirect(){
        setTimeout(function(){
          document.location.href='<?php print admin_url('admin.php?page=sumo')?>';
        }, 500);
      }
      </script>
      <?php
  }

  public function sumome_plugin_footer() {
    include(SUMOME__PLUGIN_DIR.'/views/footer.php');
  }

  public function ajax_sumome_main() {
    include(SUMOME__PLUGIN_DIR.'/views/main.php');
    $this->sumome_plugin_footer();
    exit;
  }

  public function ajax_sumome_dashboard_welcome() {
    include(SUMOME__PLUGIN_DIR.'/views/wordpress-dashboard-welcome-page.php');
    exit;
  }

  public function ajax_sumome_hide_dashboard_overlay() {
    update_option('sumome_hide_dashboard_overlay', 1);
  }

  public function ajax_sumome_show_dashboard_overlay() {
    update_option('sumome_hide_dashboard_overlay', 0);
  }

  public function dashboard_setup() {
    add_meta_box( 'my_dashboard_widget', 'Sumo', array($this , 'dashboard_widget'), 'dashboard', 'normal', 'high');
  }

  public function dashboard_widget() {
    $dashboardWidgetClass = '';

    $enduranceUser=get_option('endurance_user');
    $sumoMeInitialDashboardLoadTime=get_option('sumome_initial_wpdashboard_load_time');

    $date = new DateTime($sumoMeInitialDashboardLoadTime);
    $date->modify('+1 hour');
    $sumoMeInitialDashboardLoadTimePlusHour=$date->format('YmdHis');

    if (isset($_COOKIE['__smUser']) ||
      $enduranceUser!=1 ||
      get_option('sumome_hide_dashboard_overlay')==1 ||
      !$sumoMeInitialDashboardLoadTime ||
      ($enduranceUser==1 && date('YmdHis')<$sumoMeInitialDashboardLoadTimePlusHour)
      ) {
      $dashboardWidgetClass = 'minimized';
    }

    if (!$sumoMeInitialDashboardLoadTime) {
      update_option('sumome_initial_wpdashboard_load_time',date('YmdHis'));
    }

    include_once(SUMOME__PLUGIN_DIR.'/js/general.php');
    echo '<div class="sumome-plugin-dashboard-widget '.$dashboardWidgetClass.'"></div>';
    ?>
    <script>
      jQuery.post(ajaxurl, { action: 'sumome_dashboard_welcome' },
      function(data) {
        jQuery('.sumome-plugin-dashboard-widget').html(data);
      });

      function sumo_logout_redirect(){
        jQuery('.sumome-wp-dash-logged-in').addClass('status-logged-out');
        jQuery('.sumome-wp-dash-logged-in').removeClass('status-logged-in');

        jQuery('.sumome-wp-dash-logged-out').addClass('status-logged-out');
        jQuery('.sumome-wp-dash-logged-out').removeClass('status-logged-in');
      }
      </script>
    <?php
  }

  public function ajax_sumo_add_woocommerce_coupon() {
    $code = $_POST['code'];
    WC()->cart->apply_coupon($code);
    wp_die();
  }

  public function ajax_sumo_remove_woocommerce_coupon() {
    $code = $_POST['code'];
    WC()->cart->remove_coupon($code);
    wp_die();
  }

  public function ajax_sumo_get_woocommerce_cart_subtotal() {
    echo WC()->cart->subtotal;
    wp_die();
  }

  public function woocommerce_cart_scripts() {
    ?>
    <script type="application/javascript">
      var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

      function sumo_add_woocommerce_coupon(code) {
        jQuery.post(ajaxurl, {
          action: 'sumo_add_woocommerce_coupon',
          code: code,
        });
      }

      function sumo_remove_woocommerce_coupon(code) {
        jQuery.post(ajaxurl, {
          action: 'sumo_remove_woocommerce_coupon',
          code: code,
        });
      }

      function sumo_get_woocommerce_cart_subtotal(callback) {
        jQuery.ajax({
          method: 'POST',
          url: ajaxurl,
          dataType: 'html',
          data: {
            action: 'sumo_get_woocommerce_cart_subtotal',
          },
          success: function(subtotal) {
            return callback(null, subtotal);
          },
          error: function(err) {
            return callback(err, 0);
          }
        });
      }
    </script>
    <?php
  }

}
