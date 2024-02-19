<?php
function mpfy_upsell_get_promo_code() {
	return 'RETURNFOR10';
}

add_action( 'admin_enqueue_scripts', 'mpfy_upsell_enqueue_assets' );
function mpfy_upsell_enqueue_assets() {
	wp_enqueue_style( 'mpfy-lite-upsell', plugins_url('modules/upsell/style.css', MAPIFY_PLUGIN_FILE) );
}

add_action( 'admin_notices', 'mpfy_upsell_render_notices' );
function mpfy_upsell_render_notices() {
	global $pagenow;

	$notice = 'short';
	if ( $pagenow == 'post.php' && isset( $_GET['post'] ) ) {
		$post = get_post( intval( $_GET['post'] ) );
		if ( in_array( $post->post_type, array( 'map', 'map-location' ) ) ) {
			$notice = 'long';
		}
	} elseif ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] === 'crbn-mapifylite-settings.php' ) {
		$notice = 'long';
	}
	$func = 'mpfy_upsell_render_notice_' . $notice;
	$func();
}

function mpfy_upsell_render_notice_short() {
	if ( isset( $_COOKIE['mpfy_lite_dismissed_notice_short'] ) ) {
		return;
	}
	?>
	<div class="notice is-dismissible mpfy-upsell-popup-primary">
		<h6><?php printf( __( '%1$sYou are using the free version of Mapify%2$s - Upgrade to MapifyPro for DOZENS of additional features.', 'mpfy' ), '<strong>', '</strong>' ); ?></h6>

		<p><?php printf( __( '%1$sMapifyPro Features Include:%2$s Bulk Location Uploader, Unlimited Maps &amp; Unique Markers, Share Locations on Social Media, Advanced Search &amp; Filters, Location Directory, Add Blog Posts to your Maps, Search by Radius, Provide Directions to Any Location, Premium Support, And So Much More.', 'mpfy' ), '<strong>', '</strong>' ); ?></p>

		<blockquote>
			<p><?php _e( 'Use Promo Code:', 'mpfy' ); ?> <span style="color: #1fa4ca;"><?php echo esc_html( mpfy_upsell_get_promo_code() ); ?></span></p>

			<p>
				<a href="https://mapifypro.com/product/mapifypro/" class="mpfy-upsell-btn" target="_blank"><?php _e( 'Upgrade Now and get 10% off', 'mpfy' ); ?></a>

				<a href="#" class="mpfy-upsell-btn mpfy-notice-dismiss"><?php _e( 'Dismiss This Notice', 'mpfy' ); ?></a>
			</p>
		</blockquote>
	</div>
	<script type="text/javascript">
	(function($, $window, $document){

	$document.ready(function(){
		// @w3schools
		function setCookie(cname, cvalue, exdays) {
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+ d.toUTCString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		}

		$( '.mpfy-upsell-popup-primary' ).on('click', '.notice-dismiss, .mpfy-notice-dismiss', function ( e ) {
			if ( $(e.target).is('.mpfy-notice-dismiss') ) {
				e.preventDefault();
				$(this).closest('.mpfy-upsell-popup-primary').find('.notice-dismiss:first').click();
				return;
			}

			setCookie('mpfy_lite_dismissed_notice_short', 1, 365);
		});
	});

	})(jQuery, jQuery(window), jQuery(document));
	</script>
	<?php
}

function mpfy_upsell_render_notice_long() {
	?>
	<div class="notice mpfy-upsell-popup-secondary">
		<ul class="mpfy-upsell-popup-cols">
			<li>
				<img src="<?php echo plugins_url('modules/upsell/images/mpf-upsell-logo.png', MAPIFY_PLUGIN_FILE); ?>" alt="" />

				<p><?php printf( __( '%1$sWelcome to Mapify,%2$s the perfect way to add a powerful map to your site. Here are a couple of useful links to get you going:', 'mpfy' ), '<strong>', '</strong>' ); ?></p>

				<ol>
					<li>
						<a href="https://mapifypro.com/mapifylite-tutorials/" target="_blank"><?php _e( 'MapifyLite Tutorials', 'mpfy' ); ?></a>
					</li>

					<li>
						<a href="https://mapifypro.com/product/mapifylite/" target="_blank"><?php _e( 'Leave a Review', 'mpfy' ); ?></a>
					</li>

					<li>
						<?php _e( 'Install MapifyPro with', 'mpfy' ); ?> <a href="<?php echo admin_url( 'admin.php?page=mapifypro-installer' ); ?>"><?php _e( 'MapifyPro Installer', 'mpfy' ); ?></a>
					</li>
				</ol>
			</li>

			<li>
				<h4><?php _e( 'Special Offer: Get 10% off', 'mpfy' ); ?></h4>

				<p>
					<strong><?php _e( 'Upgrade to MapifyPro for DOZENS of extra features:', 'mpfy' ); ?></strong>
				</p>

				<ul>
					<li><?php _e( 'Unlimited Maps &amp; Unique Markers/Icons', 'mpfy' ); ?></li>
					<li><?php _e( 'Bulk Upload all Locations From a Spreadsheet', 'mpfy' ); ?></li>
					<li><?php _e( 'Share Locations on Social Media', 'mpfy' ); ?></li>
					<li><?php _e( 'Advanced Search &amp; Filters', 'mpfy' ); ?></li>
					<li><?php _e( 'Interactive Location Directory', 'mpfy' ); ?></li>
					<li><?php _e( 'Add Blog Posts to your Maps', 'mpfy' ); ?></li>
					<li><?php _e( 'Included CrowdMaps and PrettyRoutes addons', 'mpfy' ); ?></li>
					<li><?php printf( __( '%1$sSo. Much More.%2$s %3$sUse Promo %5$s%4$s', 'mpfy' ), '<a href="https://mapifypro.com/full-feature-list/" target="_blank">', '</a>', '<strong>', '</strong>', mpfy_upsell_get_promo_code() ); ?></li>
				</ul>

				<p>
					<a href="https://mapifypro.com/product/mapifypro/" target="_blank" class="mpfy-upsell-btn mpfy-upsell-btn-blue"><?php _e( 'UPGRADE TO MAPIFYPRO', 'mpfy' ); ?></a>
				</p>
			</li>
		</ul>
	</div>
	<?php
}