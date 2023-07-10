<?php
//
class ESSBAddonsHelper {

	public $base_addons_data;
	
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

	public function get_addons() {
		$addons = $this->base_addons_data;
		
		if (!is_array($addons)) {
			$addons = array();
		}
		
		return $addons;
	}
	
	public function default_addons() {
		return '{
  "essb-fomo": {
    "slug": "essb-social-proof-notifications",
    "name": "Social Proof Notifications Pro",
    "description": "Instantly increase conversions on your Website with Social Proof. Add attention-grabbing social notifications to grow shares, followers, subscribers.",
    "check": {
      "param": "essb_social_proof_run",
      "type": "function"
    },
    "icon": "icon-fomo",
    "price": "$25",
    "page": "https://codecanyon.net/item/social-proof-notifications-addon-for-easy-social-share-buttons/24197749",
    "requires": "6.0",
    "version7": "true"
  },
  "essb-networks-navigator-share": {
    "slug": "essb-networks-navigator-share",
    "name": "Share API Button",
    "description": "Add a Web Share API button that invokes the native sharing mechanism of the device to share data such as text, and URLs. Works on desktop and mobile devices.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-networks-navigator-share",
    "requires": "4.1.8",
    "check": {
      "param": "ESSB_BTN_NAVSHARE_ROOT",
      "type": "param"
    },
    "icon": "icon-addon-networkpack",
    "version7": "true",
    "actual_version": "1.0"
  },
  "essb-subscribe-connector-sendfox": {
    "slug": "essb-subscribe-connector-sendfox",
    "name": "SendFox Subscribe Connector",
    "description": "Activate subscribe to SendFox in subscribe forms module.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-subscribe-connector-sendfox",
    "requires": "7.0",
    "check": {
      "param": "ESSB_SUBSCRIBE_CONNECTOR_SENDFOX",
      "type": "param"
    },
    "icon": "icon-addon-sendfox",
    "version7": "true",
    "actual_version": "1.0"
  },
  "essb-display-woocommercebar": {
    "slug": "essb-display-woocommercebar",
    "name": "WooCommerce Sharing Bar",
    "description": "Include floating bar on the products page with details, share buttons and buy button",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-display-woocommercebar",
    "requires": "7.0",
    "check": {
      "param": "ESSB_DM_WSB_PLUGIN_ROOT",
      "type": "param"
    },
    "icon": "icon-addon-woo",
    "actual_version": "3.0",
    "version7": "true"
  },
  "essb-display-woocommercethankyou": {
    "slug": "essb-display-woocommercethankyou",
    "name": "WooCommerce Thank You Page Share Products",
    "description": "Add list of purchased products with share buttons on your WooCommerce thank you after purchase page",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-display-woocommercethankyou",
    "check": {
      "param": "ESSB_DM_WTB_PLUGIN_ROOT",
      "type": "param"
    },
    "icon": "icon-addon-woo",
    "requires": "4.1.8",
    "version7": "true"
  },
  "essb-display-woocommerceindex": {
    "slug": "essb-display-woocommerceindex",
    "name": "WooCommerce Share Products From The Product List",
    "description": "Include share buttons in the list of WooCommerce products. This makes it possible visitor share products without opening the internal page where full information is available.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-display-woocommerceindex",
    "requires": "7.0",
    "check": {
      "param": "ESSB_DM_WSC_PLUGIN_ROOT",
      "type": "param"
    },
    "icon": "icon-addon-woo",
    "actual_version": "3.0",
    "version7": "true"
  },
  "essb-social-contact-lite": {
    "slug": "essb-social-contact-lite",
    "name": "Social Contact Lite",
    "description": "Display contact us via various social messengers or apps. Use it with an automated display, shortcode or Elementor widget.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-social-contact-lite",
    "requires": "7.2",
    "check": {
      "param": "ESSB_SCL_VERSION",
      "type": "param"
    },
    "icon": "icon-contact",
    "actual_version": "1.0",
    "version7": "true"
  },
  "essb-facebook-comments": {
    "slug": "essb-facebook-comments",
    "name": "Facebook Comments",
    "description": "Automatically include Facebook comments to your blog with moderation option below posts, pages, products",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-facebook-comments",
    "requires": "3.0",
    "check": {
      "param": "ESSB3_FC_VERSION",
      "type": "param"
    },
    "icon": "icon-addon-fbcomments",
    "version7": "true",
    "actual_version": "2.0"
  },
  "essb-post-views": {
    "slug": "essb-post-views",
    "name": "Post Views Counter",
    "description": "Track and display post views/reads with your share buttons. Cache plugin compatible update and view mode. Show views also with shortcode or function. Most viewed posts shortcode and widget also present.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-post-views",
    "requires": "3.0",
    "check": {
      "param": "ESSB3_PV_VERSION",
      "type": "param"
    },
    "icon": "icon-addon-postviews",
    "actual_version": "3.0",
    "version7": "true"
  },
  "essb-subscribe-connector-convertkit": {
    "slug": "essb-subscribe-connector-convertkit",
    "name": "ConvertKit Connector",
    "description": "Integrate plugin subscribe to mailing list forms with ConvertKit.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-subscribe-connector-convertkit",
    "requires": "7.0",
    "check": {
      "param": "ESSB_SUBSCRIBE_CONNECTOR_CONVERTKIT",
      "type": "param"
    },
    "icon": "icon-subscribe",
    "actual_version": "1.0",
    "version7": "true"
  },
  "essb-subscribe-connector-jetpack": {
    "slug": "essb-subscribe-connector-jetpack",
    "name": "JetPack Subscription Connector",
    "description": "Integrate plugin subscribe to mailing list forms with JetPack Subscriptions.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-subscribe-connector-jetpack",
    "requires": "4.0.2",
    "check": {
      "param": "ESSB_SUBSCRIBE_CONNECTOR_JETPACK",
      "type": "param"
    },
    "icon": "icon-subscribe",
    "version7": "true"
  },
  "essb-networks-parler": {
    "slug": "essb-networks-parler",
    "name": "Share to Parler",
    "description": "Add support for Parler sharing.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-networks-parler",
    "requires": "7.0",
    "check": {
      "param": "ESSB_PARLER_URL",
      "type": "param"
    },
    "icon": "icon-parler",
    "actual_version": "1.0",
    "version7": "true"
  },
  "essb-networks-msteams": {
    "slug": "essb-networks-msteams",
    "name": "Share to Microsoft Teams",
    "description": "Add support for Microsoft Teams sharing.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-networks-msteams",
    "requires": "7.0",
    "check": {
      "param": "ESSB_MSTEAMS_URL",
      "type": "param"
    },
    "icon": "icon-msteams",
    "actual_version": "1.0",
    "version7": "true"
  },
  "essb-networks-snapchat": {
    "slug": "essb-networks-snapchat",
    "name": "Share to Snapchat",
    "description": "Add support for Snapchat Creative Kit for Web. With Creative Kit for Web, publishers and brands can add a Share to Snapchat button to their website so Snapchatters can share content from a mobile or desktop website into Snapchat.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-networks-snapchat",
    "requires": "7.2",
    "check": {
      "param": "ESSB_SNAPCHAT_ROOT",
      "type": "param"
    },
    "icon": "icon-snapchat",
    "actual_version": "1.0",
    "version7": "true"
  },
  "essb-networks-wykop": {
    "slug": "essb-networks-wykop",
    "name": "Share to Wykop.pl",
    "description": "Add support for Wykop.pl sharing.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-networks-wykop",
    "requires": "7.0",
    "check": {
      "param": "ESSB_WYKOP_URL",
      "type": "param"
    },
    "icon": "icon-wykop",
    "actual_version": "1.0",
    "version7": "true"
  },
  "essb-bimber-extension": {
    "slug": "essb-bimber-extension",
    "name": "Bimber Theme Share Buttons Replace",
    "description": "Include replacement of default theme share buttons with Easy Social Share Buttons (theme specific functions)",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-bimber-extension",
    "requires": "4.1.8",
    "check": {
      "param": "ESSB_BIMBER_REPLACE",
      "type": "param"
    },
    "icon": "icon-addon-theme",
    "version7": "true",
    "actual_version": "2.0"
  },
  "essb-beaverbuilder-theme-integration": {
    "slug": "essb-beaverbuilder-theme-integration",
    "name": "Beaver Builder Theme Integration",
    "description": "Custom display positions for Beaver Builder Theme: Before/After content",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-beaverbuilder-theme-integration",
    "requires": "7.0",
    "check": {
      "param": "ESSB_BBT_CUSTOM_BOILERPLATE",
      "type": "param"
    },
    "icon": "icon-addon-theme",
    "version7": "true",
    "actual_version": "2.0"
  },
  "essb-display-viralpoint": {
    "slug": "essb-display-viralpoint",
    "name": "Display Method Viral Point",
    "description": "Super cool share point design with automatic trigger on hover, eye catching design and animations",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-display-viralpoint",
    "requires": "4.0.2",
    "check": {
      "param": "ESSB_DM_VP_PLUGIN_URL",
      "type": "param"
    },
    "icon": "icon-addon-displaymethod",
    "version7": "true"
  },
  "essb-display-superpostfloat": {
    "slug": "essb-display-superpostfloat",
    "name": "Display Method Super Post Float",
    "description": "Extended version of post vertical float with call to action message and display of total/comments count",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-display-superpostfloat",
    "requires": "4.0.2",
    "check": {
      "param": "ESSB_DM_SPF_PLUGIN_URL",
      "type": "param"
    },
    "icon": "icon-addon-displaymethod",
    "version7": "true"
  },
  "essb-display-superpostbar": {
    "slug": "essb-display-superpostbar",
    "name": "Display Method Super Post Bar",
    "description": "Extend your bottom display method with super post bar. Super post bar allows display of previous/next post too.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-display-superpostbar",
    "requires": "4.0.2",
    "check": {
      "param": "ESSB_DM_SPB_PLUGIN_URL",
      "type": "param"
    },
    "icon": "icon-addon-displaymethod",
    "version7": "true"
  },
  "essb-display-mobile-sharebarcta": {
    "slug": "essb-display-mobile-sharebarcta",
    "name": "Display Method Mobile Share Bar with Call to Action Button",
    "description": "Include mobile share bar with custom call to action button next to share button.",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-display-mobile-sharebarcta",
    "requires": "4.0.2",
    "check": {
      "param": "ESSB_DM_MSBCTA_PLUGIN_URL",
      "type": "param"
    },
    "icon": "icon-addon-displaymethod",
    "version7": "true"
  },
  "essb-extended-buttons-pack": {
    "slug": "essb-extended-buttons-pack",
    "name": "Hatena, Douban, Tencent QQ, Naver, Renren",
    "description": "Additional network extension pack including Hatena, Douban, Tencent QQ, Naver, Renren",
    "price": "FREE",
    "page": "https://get.socialsharingplugin.com/?activationKey={activationKey}&domain={domain}&download=essb-extended-buttons-pack",
    "requires": "4.1.8",
    "check": {
      "param": "ESSB_EP_ROOT",
      "type": "param"
    },
    "icon": "icon-addon-networkpack",
    "version7": "true",
    "actual_version": "2.0"
  },
  "essb-templates-rainbow": {
    "slug": "essb-templates-rainbow",
    "name": "Rainbow Templates Pack",
    "description": "60 awesome looking gradient templates for Easy Social Share Buttons for WordPress",
    "check": {
      "param": "essb_rainbow_initialze",
      "type": "function"
    },
    "icon": "icon-template",
    "price": "$10",
    "page": "https://codecanyon.net/item/rainbow-templates-pack-for-easy-social-share-buttons/22753541",
    "requires": "5.0",
    "version7": "true"
  },
  "essb-video-share-events": {
    "slug": "essb-video-share-events",
    "name": "Video Sharing",
    "description": "A must have tool for each video marketing campaign. Add beautiful call to actions on specific events to increase your social shares, social following, mailing list, your marketing message at the right time or just share buttons.",
    "price": "$29",
    "icon": "icon-video",
    "page": "https://codecanyon.net/item/video-sharing-addon-for-easy-social-share-buttons/8434467",
    "demo_url": "https://preview.codecanyon.net/item/video-sharing-addon-for-easy-social-share-buttons/full_screen_preview/8434467",
    "check": {
      "param": "ESSB3_VSE_VERSION",
      "type": "param"
    },
    "requires": "4.0",
    "version7": "true"
  },
  "essb-self-short-url": {
    "slug": "essb-self-short-url",
    "name": "Self-Hosted Short URLs",
    "icon": "icon-url",
    "description": "Generate self hosted short URLs directly from your WordPress without external services like http://domain.com/axWsa or custom based http://domain.com/essb.",
    "price": "$19",
    "page": "https://codecanyon.net/item/self-hosted-short-urls-addon-for-easy-social-share-buttons/15066447",
    "check": {
      "param": "ESSB3_SSU_VERSION",
      "type": "param"
    },
    "requires": "3.1.2",
    "version7": "true"
  }
}';
	}
}

function essb_map_addons_to_tmga() {
    $essb_addons = ESSBAddonsHelper::get_instance ();
    $current_list = $essb_addons->get_addons ();
    
    $plugins = array();
    
    foreach ($current_list as $key => $data) {
        $slug = $data['slug'];
        
        $price = $data['price'];
        
        if ($price == 'free' || $price == 'Free' || $price == 'FREE') {      
                    
            /**
             * Adding additional required keys to to download URL
             * @var Ambiguous $source
             */
            $source = $data['page'];
            $source = str_replace('{activationKey}', ESSBActivationManager::getActivationCode(), $source);
            $source = str_replace('{domain}', esc_url(ESSBActivationManager::getSiteURL()), $source);
            
            $plugins[] = array(
                'name' => $data['name'],
                'slug' => $slug,
                'source' => $source
            );
        }        
    }
    
    /*
     * Array of configuration settings. Amend each line as needed.
     *
     * TGMPA will start providing localized text strings soon. If you already have translations of our standard
     * strings available, please help us make TGMPA even better by giving us access to these translations or by
     * sending in a pull-request with .po file(s) with the translations.
     *
     * Only uncomment the strings in the config array if you want to customize the strings.
     */
    $config = array(
        'id'           => 'essb',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'essb_redirect_addons', // Menu slug.
        'parent_slug'  => 'essb_options',            // Parent menu slug.
        'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => false,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings' => array(
            'page_title' => 'Add-Ons',
            'menu_title' => 'Add-Ons'
        )
        
    );    
    
    essb_tgmpa( $plugins, $config );
    
}

/**
 * Generate an array of all site plugins
 * @return unknown[][]|NULL[][]
 */
function essb_get_site_plugins() {
    $plugins = get_plugins();
    
    $r = array();
    
    foreach ($plugins as $key => $data) {
        $key_path = explode('/', $key);
        $slug = $key_path[0];
        
        $r[$slug] = array(
            'name' => $data['Name'],
            'version' => $data['Version'],
            'description' => $data['Description'],
            'pluginURL' => $data['PluginURI'],
            'author' => $data['AuthorName'],
            'path' => $key,
            'active' => is_plugin_active( $key )
        );
    }
    
    return $r;
}