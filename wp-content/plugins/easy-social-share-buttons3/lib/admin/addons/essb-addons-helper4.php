<?php
//
class ESSBAddonsHelper {

	private $cache_options_slug = "essb3_addons";
	private $announced_addons_slug = "essb3_addons_announce";
	private $update_addons_server = "http://extensions.appscreo.com/4/"; //"http://addons.appscreo.com";

	private $base_addons = '';
	private $base_addons_data;
	private $version5_exclude = array('essb-optin-flyout', 'essb-optin-booster', 'essb-optin-content', 'essb-amp-support');

	private static $instance = null;
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	} // end get_instance;

	function __construct() {
		$remote_result = $this->default_addons();
		
		$remote_result = htmlspecialchars_decode ( $remote_result );
		$remote_result = stripslashes ( $remote_result );
		$info = json_decode($remote_result, true);
		$this->base_addons_data = $info;
	}


	public function call_remove_addon_list_update() {		
		$url = $this->update_addons_server;
		$result = wp_remote_get($url);
		$success_connection = true;

		if(is_wp_error($result) or (wp_remote_retrieve_response_code($result) != 200)){
			$success_connection = false;
		}
			
		/* Check for incorrect data */
		if ($success_connection) {
			$remote_result = wp_remote_retrieve_body($result);
			$remote_result = base64_decode ( $remote_result );
		
			$remote_result = htmlspecialchars_decode ( $remote_result );
			$remote_result = stripslashes ( $remote_result );						
			
			$info = json_decode($remote_result, true);
			if (is_array($info)) {
				update_option($this->cache_options_slug, $info);
			}
		}
	}

	public function get_addons() {
		$addons = $this->base_addons_data;
		
		if (!is_array($addons)) {
			$addons = array();
		}
		
		$r = array();
		
		foreach ($addons as $key => $data) {
			if (!in_array($key, $this->version5_exclude)) {
				$r[$key] = $data;
			}
		}
		
		return $r;
	}

	public function get_new_addons() {
		$addons = $this->get_addons();

		if (!is_array($addons)) {
			$addons = array();
		}

		$current_announced = get_option($this->announced_addons_slug);
		if (!is_array($current_announced)) {
			$current_announced = array();
		}

		$list_of_new = array();
		foreach ($addons as $addon_key => $addon_data) {
			if ($addon_key == "filters") continue;
			
			if (in_array($addon_key, $this->version5_exclude)) continue;
			if (!isset($current_announced[$addon_key])) {
				$list_of_new[$addon_key] = array("title" => $addon_data['name'], "url" => $addon_data['page']);
			}
		}


		return $list_of_new;
	}

	public function get_new_addons_count() {

		$addons = $this->get_addons();

		if (!is_array($addons)) {
			$addons = array();
		}

		$current_announced = get_option($this->announced_addons_slug);
		if (!is_array($current_announced)) {
			$current_announced = array();
		}

		$list_of_new = 0;
		foreach ($addons as $addon_key => $addon_data) {
			if ($addon_key == "filters") continue;
			if (in_array($addon_key, $this->version5_exclude)) continue;
			if (!isset($current_announced[$addon_key])) {
				$list_of_new++;
			}
		}


		return $list_of_new;
	}

	public function dismiss_addon_notice($addon) {
		$current_announced = get_option($this->announced_addons_slug);
		if (!is_array($current_announced)) {
			$current_announced = array();
		}

		if (strpos($addon, ',') == false) {
			$current_announced[$addon] = "yes";
		}
		else {
			$addon_list = explode(',', $addon);
			foreach ($addon_list as $one) {
				$current_announced[$one] = "yes";
			}
		}

		update_option($this->announced_addons_slug, $current_announced, 'no');
	}
	
	public function default_addons() {
		return '{"essb-templates-rainbow":{"slug":"essb-templates-rainbow","name":"Rainbow Templates Pack","image":"http:\/\/addons.appscreo.com\/i\/rainbow-templates.png","description":"60 awesome looking gradient templates for Easy Social Share Buttons for WordPress","price":"$10","page":"https:\/\/codecanyon.net\/item\/rainbow-templates-pack-for-easy-social-share-buttons\/22753541","demo_url":"https:\/\/socialsharingplugin.com\/rainbow-templates-pack\/","check":"","check_function":"essb_rainbow_initialze","tags":"unique","category":"templates","requires":"5.0","filters":"templates,paid,new"},"essb-custom-template-builder":{"slug":"essb-custom-template-builder","name":"Custom Template Builder for Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/extensions-01.png","description":"Easy build with visual options a custom template for social sharing buttons integrated into plugin and used along with the default","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-custom-template-builder","demo_url":"","check":"ESSB_SBTB_VERSION","tags":"unique","category":"templates","requires":"5.1.4","filters":"templates,free,new"},"essb-multistep-sharerecovery":{"slug":"essb-multistep-sharerecovery","name":"Multi-step Share Counter Recovery","image":"http:\/\/addons.appscreo.com\/i\/extensions-05.png","description":"Include additional recovery rules that can be used if you have made additional changes in the past - up to 3 additional recovery rules in help of the primary","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-multistep-sharerecovery","demo_url":"","check":"ESSB_MSSR_VERSION","tags":"unique","category":"function","requires":"5.1.3","filters":"function,free,new"},"essb-bimber-extension":{"slug":"essb-bimber-extension","name":"Display Method: Bimber Theme Share Buttons Replace","image":"http:\/\/addons.appscreo.com\/i\/extensions-06.png","description":"Include replacement of default theme share buttons with Easy Social Share Buttons (theme specific functions)","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-bimber-extension","demo_url":"","check":"ESSB_BIMBER_REPLACE","tags":"new","category":"integration","requires":"4.1.8","filters":"display,new,free,integration"},"essb-display-woocommercethankyou":{"slug":"essb-display-woocommercethankyou","name":"Display Method: WooCommerce Thank You Page Share Products","image":"http:\/\/addons.appscreo.com\/i\/extensions-03.png","description":"Add list of purchased products with share buttons on your WooCommerce thank you after purchase page","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-display-woocommercethankyou","demo_url":"https:\/\/socialsharingplugin.com\/50-social-networks-special-buttons\/","check":"ESSB_DM_WTB_PLUGIN_ROOT","tags":"new","category":"woocommerce","requires":"4.1.8","filters":"display,new,free,woocommerce"},"essb-extended-buttons-pack":{"slug":"essb-extended-buttons-pack","name":"Social Networks: Extended Social Networks Pack","image":"http:\/\/addons.appscreo.com\/i\/extensions-02.png","description":"Networks: Hatena, Douban, Tencent QQ, Naver, Renren","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-extended-buttons-pack","demo_url":"https:\/\/socialsharingplugin.com\/50-social-networks-special-buttons\/","check":"ESSB_EP_ROOT","tags":"new","category":"social networks","requires":"4.1.8","filters":"networks,free"},"essb-functional-buttons-pack":{"slug":"essb-functional-buttons-pack","name":"Social Networks: Functional Share Buttons Pack","image":"http:\/\/addons.appscreo.com\/i\/extensions-02.png","description":"Include usage of functional buttons set: Previous Post, Next Post, Copy Link, Bookmark, QR Code Generator","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-funcitonal-buttons-pack","demo_url":"https:\/\/socialsharingplugin.com\/50-social-networks-special-buttons\/","check":"ESSB_FP_ROOT","tags":"new","category":"social networks","requires":"4.1.8","filters":"networks,free"},"essb-optin-flyout":{"slug":"essb-optin-flyout","name":"Opt-in Flyout Add-on for Easy Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/extensions-04.png","description":"Display flyout opt-in forms based on various conditions: time delay, scroll or exit intent and increase your subscribers.","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-optin-flyout","demo_url":"","check":"ESSB3_OFOF_VERSION","tags":"new","category":"subscribe extension","requires":"4.0.3","filters":"subscribe,free"},"essb-beaverbuilder-theme-integration":{"slug":"essb-beaverbuilder-theme-integration","name":"Display Method: Beaver Builder Theme Integration","image":"http:\/\/addons.appscreo.com\/i\/extensions-06.png","description":"Custom display positions for Beaver Builder Theme: Before\/After content","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-beaverbuilder-theme-integration","demo_url":"","check":"ESSB_BBT_CUSTOM_BOILERPLATE","tags":"new","category":"integration","requires":"4.0.2","filters":"display,integration,free"},"essb-video-share-events":{"slug":"essb-video-share-events","name":"Video Sharing Add-on for Easy Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/addon_images4-11.png","description":"A must have tool for each video marketing campaign. Add beautiful call to actions on specific events to increase your social shares, social following, mailing list, your marketing message at the right time or just share buttons.","price":"$14","page":"https:\/\/codecanyon.net\/item\/video-sharing-addon-for-easy-social-share-buttons\/8434467","demo_url":"http:\/\/preview.codecanyon.net\/item\/video-sharing-addon-for-easy-social-share-buttons\/full_screen_preview\/8434467","check":"ESSB3_VSE_VERSION","tags":"","category":"video sharing","requires":"4.0","filters":"video,function,paid"},"essb-optin-booster":{"slug":"essb-optin-booster","name":"Opt-in Booster Add-on for Easy Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/extensions-04.png","description":"Display opt-in forms based on various conditions: time delay, scroll or exit intent and increase your subscribers.","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-optin-booster","demo_url":"","check":"ESSB3_OFOB_VERSION","tags":"","category":"subscribe extension","requires":"4.0.3","filters":"subscribe,free"},"essb-template-christmas":{"slug":"essb-template-christmas","name":"Templates: Christmas pack","image":"http:\/\/addons.appscreo.com\/i\/extensions-01.png","description":"Prepare your site for upcoming Christmas and New Year with two special Christmas templates","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-template-christmas","demo_url":"","check":"ESSB_TEMPLATEPACK_CHRISTMAS","tags":"","category":"templates","requires":"4.0.2","filters":"template,free"},"essb-display-woocommercebar":{"slug":"essb-display-woocommercebar","name":"Display Method: WooCommerce Bar","image":"http:\/\/addons.appscreo.com\/i\/extensions-03.png","description":"Special designed share bar for WooCommerce stores with shaer buttons, product title\/price and buy now button","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-display-woocommercebar","demo_url":"https:\/\/socialsharingplugin.com\/product\/flying-ninja\/?position_demo=woobar","check":"ESSB_DM_VP_PLUGIN_URL","tags":"","category":"woocommerce","requires":"4.0.2","filters":"display,free,woocommerce"},"essb-display-viralpoint":{"slug":"essb-display-viralpoint","name":"Display Method: Viral Point","image":"http:\/\/addons.appscreo.com\/i\/extensions-03.png","description":"Super cool share point design with automatic trigger on hover, eye catching design and animations","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-display-viralpoint","demo_url":"https:\/\/socialsharingplugin.com\/27-display-positions\/?position_demo=viralpoint","check":"ESSB_DM_VP_PLUGIN_URL","tags":"","category":"display method","requires":"4.0.2","filters":"display,free"},"essb-display-superpostfloat":{"slug":"essb-display-superpostfloat","name":"Display Method: Super Post Float","image":"http:\/\/addons.appscreo.com\/i\/extensions-03.png","description":"Extended version of post vertical float with call to action message and display of total\/comments count","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-display-superpostfloat","demo_url":"https:\/\/socialsharingplugin.com\/27-display-positions\/?position_demo=superpostfloat","check":"ESSB_DM_SPF_PLUGIN_URL","tags":"","category":"display method","requires":"4.0.2","filters":"display,free"},"essb-display-superpostbar":{"slug":"essb-display-superpostbar","name":"Display Method: Super Post Bar","image":"http:\/\/addons.appscreo.com\/i\/extensions-03.png","description":"Extend your bottom display method with super post bar. Super post bar allows display of previous\/next post too.","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-display-superpostbar","demo_url":"https:\/\/socialsharingplugin.com\/27-display-positions\/?position_demo=superpostbar","check":"ESSB_DM_SPB_PLUGIN_URL","tags":"","category":"display method","requires":"4.0.2","filters":"display,free"},"essb-display-mobile-sharebarcta":{"slug":"essb-display-mobile-sharebarcta","name":"Display Method: Mobile Share Bar with Call to Action Button","image":"http:\/\/addons.appscreo.com\/i\/extensions-03.png","description":"Include mobile share bar with custom call to action button next to share button.","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-display-mobile-sharebarcta","demo_url":"https:\/\/socialsharingplugin.com\/27-display-positions\/?position_demo=mobile7","check":"ESSB_DM_MSBCTA_PLUGIN_URL","tags":"","category":"display method","requires":"4.0.2","filters":"display,free"},"essb-subscribe-connector-jetpack":{"slug":"essb-subscribe-connector-jetpack","name":"Opt-in Connector: JetPack Subscriptions","image":"http:\/\/addons.appscreo.com\/i\/extensions-04.png","description":"Activate usage of JetPack Subscriptions in Opt-in Module","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-subscribe-connector-jetpack","demo_url":"","check":"ESSB_SUBSCRIBE_CONNECTOR_JETPACK","tags":"","category":"subscribe forms","requires":"4.0.2","filters":"subscribe,free"},"essb-social-ab":{"slug":"essb-social-ab","name":"Social A\/B Visual Tests Add-on for Easy Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/extensions-05.png","description":"It was never been so easy to find what works best for your site. Setup up to 3 different configurations of buttons on your site and run the tests. You will see real time stats of what performs best.","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-social-ab","demo_url":"http:\/\/socialsharingplugin.com\/social-ab\/","check":"ESSB3_AB_VERSION","tags":"unique","category":"function","requires":"4.0","filters":"function,free"},"essb-post-views":{"slug":"essb-post-views","name":"Post Views Add-on for Easy Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/extensions-05.png","description":"Track and display post views\/reads with your share buttons and also display most popular posts with widget or shortcode.","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-post-views","demo_url":"http:\/\/socialsharingplugin.com\/viewsreads-counter\/","tags":"popular","check":"ESSB3_PV_VERSION","category":"function","requires":"3.0","filters":"fuction,free"},"essb-facebook-comments":{"slug":"essb-facebook-comments","name":"Facebook Comments Add-on for Easy Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/extensions-05.png","description":"Automatically include Facebook comments to your blog with moderation option below posts","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-facebook-comments","demo_url":"http:\/\/socialsharingplugin.com\/facebook-comments\/?fbcomments_demo=true","check":"ESSB3_FC_VERSION","tags":"popular","category":"function","requires":"3.0","filters":"function,free"},"essb-amp-support":{"slug":"essb-amp-support","name":"AMP Share Buttons Add-on for Easy Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/extensions-05.png","description":"Include share buttons on your AMP pages if you use official plugin WordPress AMP","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-amp-support","demo_url":"","check":"ESSB3_AMP_PLUGIN_ROOT","tags":"updated","category":"mobile","requires":"4.0","filters":"function,free"},"essb-optin-content":{"slug":"essb-optin-content","name":"Opt-in Forms Below Content Add-on for Easy Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/extensions-04.png","description":"Automatically include opt-in forms from Easy Optin below post content. Easy get more new subscribers to your mailing list.","price":"FREE","page":"http:\/\/get.socialsharingplugin.com\/?download=essb-optin-content","demo_url":"http:\/\/socialsharingplugin.com\/easy-optin\/","check":"ESSB3_OF_VERSION","tags":"","category":"feature","requires":"4.0","filters":"subscribe,free"},"hello-followers":{"slug":"hello-followers","name":"Hello Followers - Social Counter Plugin","image":"http:\/\/addons.appscreo.com\/i\/addon_images4-05.png","description":"Beatiful and unique extension of your current social followers with cover boxes, layout builder, advanced customizer, profile analytics. Try the live demo to test.","price":"$24","page":"http:\/\/codecanyon.net\/item\/hello-followers-social-counter-plugin-for-wordpress\/15801729","demo_url":"http:\/\/codecanyon.net\/item\/hello-followers-social-counter-plugin-for-wordpress\/full_screen_preview\/15801729","check":"HF_VERSION","category":"paid","requires":"1.0","filters":"paid"},"essb-self-short-url":{"slug":"essb-self-short-url","name":"Self-Hosted Short URLs add-on for Easy Social Share Buttons","image":"http:\/\/addons.appscreo.com\/i\/addon_images4-01.png","description":"Generate self hosted short URLs directly from your WordPress without external services like http:\/\/domain.com\/axWsa or custom based http:\/\/domain.com\/essb.","price":"$14","page":"http:\/\/codecanyon.net\/item\/self-hosted-short-urls-addon-for-easy-social-share-buttons\/15066447","demo_url":"http:\/\/codecanyon.net\/item\/self-hosted-short-urls-addon-for-easy-social-share-buttons\/full_screen_preview\/15066447","check":"ESSB3_SSU_VERSION","category":"paid","requires":"3.1.2","filters":"function,paid"},"filters":{"all":"All","new":"New","free":"Free","networks":"Networks","display":"Display Methods","template":"Templates","subscribe":"Subscribe Forms","video":"Video Sharing","function":"Functions","paid":"Paid","integration":"Integration","woocommerce":"WooCommerce"}}';
	}
}

?>