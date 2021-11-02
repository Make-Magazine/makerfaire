<h3><?php _e( 'Configure', $text_domain ); ?></h3>

<?php
if ( isset( $_POST['sbi_connect_username'] ) ) {
	$new_user_name = sanitize_text_field( $_POST['sbi_connect_username'] );
	$new_account_details = json_decode( stripslashes( $_POST['sbi_account_json'] ), true );
	array_map( 'sanitize_text_field', $new_account_details );

	$updated_options = sbi_connect_basic_account( $new_account_details );
	$connected_accounts = $updated_options['connected_accounts'];
	$user_feed_ids = $updated_options['sb_instagram_user_id'];
}
if( isset($_GET['cff_access_token']) && isset($_GET['cff_final_response']) ) {
	$page_id                = 'cff_page_id';
	$page_id_val = get_option( $page_id );
	//Which extensions are active?
	//Is individual extension active || is Smash License extensions bundle active?
	$cff_ext_options = get_option('cff_extensions_status');
	$cff_connected_accounts = 'cff_connected_accounts';

	$cff_connected_accounts_val = get_option( $cff_connected_accounts );

	//Set extensions in extensions plugin all to false by default
	$cff_extensions_multifeed_active = false;
	$cff_extensions_date_range_active = false;
	$cff_extensions_featured_post_active = false;
	$cff_extensions_album_active = false;
	$cff_extensions_lightbox_active = false;
	$cff_extensions_masonry_columns_active = false;
	$cff_extensions_carousel_active = false;
	$cff_extensions_reviews_active = false;


	if (WPW_SL_ITEM_NAME == 'Custom Facebook Feed WordPress Plugin Smash'){
		//Set page variables
		if( isset($cff_ext_options[ 'cff_extensions_multifeed_active' ]) ) $cff_extensions_multifeed_active = $cff_ext_options[ 'cff_extensions_multifeed_active' ];
		if( isset($cff_ext_options[ 'cff_extensions_date_range_active' ]) ) $cff_extensions_date_range_active = $cff_ext_options[ 'cff_extensions_date_range_active' ];
		if( isset($cff_ext_options[ 'cff_extensions_featured_post_active' ]) ) $cff_extensions_featured_post_active = $cff_ext_options[ 'cff_extensions_featured_post_active' ];
		if( isset($cff_ext_options[ 'cff_extensions_album_active' ]) ) $cff_extensions_album_active = $cff_ext_options[ 'cff_extensions_album_active' ];
		if( isset($cff_ext_options[ 'cff_extensions_lightbox_active' ]) ) $cff_extensions_lightbox_active = $cff_ext_options[ 'cff_extensions_lightbox_active' ];
		if( isset($cff_ext_options[ 'cff_extensions_masonry_columns_active' ]) ) $cff_extensions_masonry_columns_active = $cff_ext_options[ 'cff_extensions_masonry_columns_active' ];
		if( isset($cff_ext_options[ 'cff_extensions_carousel_active' ]) ) $cff_extensions_carousel_active = $cff_ext_options[ 'cff_extensions_carousel_active' ];
		if( isset($cff_ext_options[ 'cff_extensions_reviews_active' ]) ) $cff_extensions_reviews_active = $cff_ext_options[ 'cff_extensions_reviews_active' ];
	}
	( is_plugin_active( 'cff-reviews/cff-reviews.php' ) || $cff_extensions_reviews_active ) ? $cff_reviews_active = true : $cff_reviews_active = false;
?>
<div id="cff-admin">
    <input name="cff_page_id" id="cff_page_id" type="hidden" value="<?php echo esc_attr( $page_id_val ); ?>" data-page-id="<?php echo esc_attr( $page_id_val ); ?>" />
    <input name="cff_access_token" id="cff_access_token" type="hidden" data-accesstoken="" >
    <input name="cff_connected_accounts" id="cff_connected_accounts" type="hidden" value="<?php echo stripslashes( esc_attr( $cff_connected_accounts_val ) ); ?>" />

    <?php
	cff_after_access_token_retrieved( $page_id_val, $cff_reviews_active );
?>
</div>
	<?php
}
if ( function_exists('sby_attempt_connection' ) && isset( $_GET['sby_access_token'] ) ) {
	sby_attempt_connection();
}
if ( function_exists('cff_admin_modal' ) ) {
	$admin_url_state = admin_url( 'admin.php?page=sbsw' );

	?>
    <div id="cff-admin">
		<?php
		cff_admin_modal( $admin_url_state );
		?>
    </div>
	<?php
}
if ( isset( $_GET['oauth_token'] ) ) {
	$oauth_token = sanitize_text_field( $_GET['oauth_token'] );
	$oauth_token_secret = sanitize_text_field( $_GET['oauth_token_secret'] );
	$screen_name = sanitize_text_field( $_GET['screen_name'] );

	$options = get_option( 'ctf_options', array() );

	$options['access_token'] = $oauth_token;
	$options['access_token_secret'] = $oauth_token_secret;
	$options['usertimeline_text'] = $screen_name;

	update_option( 'ctf_options', $options );
}

$installed = sbsw_get_active_plugins();

$if_compatible = sbsw_instagram_feed_is_minimum_version() && sbsw_social_wall_is_minimum_version_for_instagram_feed();
$if_active = in_array( 'instagram', $installed, true );

$fb_compatible = sbsw_facebook_feed_is_minimum_version() && sbsw_social_wall_is_minimum_version_for_facebook_feed();
$fb_active = in_array( 'facebook', $installed, true );

$tw_compatible = sbsw_twitter_feed_is_minimum_version() && sbsw_social_wall_is_minimum_version_for_twitter_feed();
$tw_active = in_array( 'twitter', $installed, true );

$yt_compatible = sbsw_youtube_feed_is_minimum_version() && sbsw_social_wall_is_minimum_version_for_youtube_feed();
$yt_active = in_array( 'youtube', $installed, true );

$json_array = array();
?>

<p><?php echo __( 'Using Social Wall, you can create a wall of multiple different social media feeds. Just use the options below to choose which feeds to add to your wall.', 'social-wall' ); ?></p><p><?php echo __( 'Once you\'re done, simply copy and paste the following shortcode block into a page or widget on your site to display your Social Wall. To customize your feed, just click on the "Customize" tab above.', 'social-wall' ); ?></p>

<div id="sbsw-shortcode-generator">
    <div class="sbsw-sc-generator-wrap">
<textarea readonly="readonly" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).">
[social-wall]
<?php if ( $if_active && $if_compatible ) : ?>
    [instagram-feed]
<?php endif; ?>
<?php if ( $fb_active && $fb_compatible ) : ?>
    [custom-facebook-feed]
<?php endif; ?>
<?php if ( $tw_active && $tw_compatible ) : ?>
    [custom-twitter-feed]
<?php endif; ?>
<?php if ( $yt_active && $yt_compatible ) : ?>
    [youtube-feed]
<?php endif; ?>
[/social-wall]
</textarea>
    </div>
    <div class="sbsw-active-accounts">
	    <?php if ( $if_active && $if_compatible ) :

            $account_and_feed_info = sbi_get_account_and_feed_info();
            $default_type_and_terms = $account_and_feed_info['type_and_terms'];
		    $connected_accounts = $account_and_feed_info['connected_accounts'];
		    $if_connected_accounts = $connected_accounts;

		    $available_types = $account_and_feed_info['available_types'];

		    $empty_connected_accounts = empty( $connected_accounts );

		    $json_array['instagram'] = array(
			    'current' => array(
				    'type' => $default_type_and_terms['type'],
				    'term' => implode(',',$default_type_and_terms['terms'])
			    ),
			    'available_types' => $available_types,
			    'settings' => $account_and_feed_info['settings']
		    );
            ?>
        <div class="sbsw-default-feed-wrap sbsw-instagram" data-plugin="instagram">

                <?php if ( !$empty_connected_accounts ){ ?>
                    <div class="sbsw-platform-label">
                        <span class="sbsw-added">
                            <?php echo SW_Display_Elements::get_icon( 'yes' ); ?>
                            <?php echo __( 'Added to Wall', 'social-wall' ); ?>
                        </span>
                        <span class="sbsw-removed">
                            <?php echo SW_Display_Elements::get_icon( 'no' ); ?>
                            <?php echo __( 'Not in Wall', 'social-wall' ); ?>
                        </span>
                        <button class="sbsw-add-remove-plugin button"><?php echo __( 'Remove', 'social-wall' ); ?></button>
                    </div>
                <?php } ?>

                <h4 class="sbsw-platform-name"><?php echo SW_Display_Elements::get_icon( 'instagram' ); ?>Instagram</h4>

                <?php if ( $empty_connected_accounts ) :
    	            $json_array['instagram']['exclude'] = true; ?>
                    <div id="sbi_admin" style="margin-top: 15px;">
                        <div id="sbi_config">
                        <?php sbi_get_connect_account_button( 'admin.php?page=sbsw' ); ?>
                        </div>
                    </div>
                <?php else: ?>
                    
                <?php endif; ?>
                
            <?php if ( ! $empty_connected_accounts ) : ?>

            <div class="sbsw-feed-settings">
                <div class="sbsw-feed-type">
                    <h4><?php echo __( 'Feed Type:', 'social-wall' ); ?></h4>
                    <select class="sbsw-type-select" name="sbsw_instagram_types">
                        <?php foreach ( $available_types as $available_type ) :
        	                $selected = $available_type['shortcode'] === $default_type_and_terms['type'] ? ' selected' : '';
                            ?>
                        <option value="<?php echo esc_attr( $available_type['shortcode'] ); ?>"<?php echo $selected; ?>><?php echo esc_html( $available_type['label'] ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sbsw-connected-accounts-wrap">
                    <div class="sbsw-connected-accounts-inner">
                        <h4><?php echo __( 'Select User(s) for Wall:', 'social-wall' ); ?></h4>
                        <?php
                        foreach ( $connected_accounts as $connected_account ) :
                            $is_default = false;
        	                $wrap_class = '';
        	                $button_class = '';
                            $button_text = __( 'Add', 'social-wall' );
                            if ( $default_type_and_terms['type'] === 'user'
                                || $default_type_and_terms['type'] === 'tagged' ) {
                                if ( in_array( $connected_account['username'], $default_type_and_terms['terms'], true )
                                    || in_array( $connected_account['user_id'], $default_type_and_terms['terms'], true ) ) {
                                    $wrap_class = ' sbsw-selected';
                                    $button_class = ' sbsw-is-default';
                                    $button_text = __( 'Remove', 'social-wall' );
                                }
                            }

                        ?>

                        <div class="sbsw-connected-account<?php echo esc_attr( $wrap_class ); ?>" data-id="<?php echo esc_attr( $connected_account['user_id'] ); ?>" data-user="<?php echo esc_attr( $connected_account['username'] ); ?>">
                            <button class="sbsw-add-remove-account button<?php echo esc_attr( $button_class ); ?>"><?php echo esc_html( $button_text ); ?></button>
                            <p><?php echo esc_html( $connected_account['username'] ); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="sbi_admin" class="sbsw-connect-account-btn-small">
                        <div id="sbi_config">
                            <?php sbi_get_connect_account_button( 'admin.php?page=sbsw' ); ?>
                        </div>
                    </div>
                </div>

                <div class="sbsw-text-input-wrap">
                    <h4><?php echo __( 'Select Hashtag(s) for Wall:', 'social-wall' ); ?></h4>
                    <input type="text" name="sbsw-instagram-text" value="">
                    <span class="sbsw-text-input-instructions"></span>
                </div>
            </div>
            <?php endif; ?>

        </div>
        <?php endif; ?>
	    <?php if ( $fb_active && $fb_compatible ) :
		    $account_and_feed_info = cff_get_account_and_feed_info();
		    $default_type_and_terms = $account_and_feed_info['type_and_terms'];
		    $connected_accounts = $account_and_feed_info['connected_accounts'];
		    $if_connected_accounts = $connected_accounts;
		    $available_types = $account_and_feed_info['available_types'];
		    $empty_connected_accounts = empty( $connected_accounts );

            // var_dump( get_object_vars($connected_accounts) );
            // var_dump( $connected_accounts );

		    $json_array['facebook'] = array(
			    'current' => array(
				    'type' => $default_type_and_terms['type'],
				    'term' => implode(',',$default_type_and_terms['terms'])
			    ),
			    'available_types' => $available_types,
			    'settings' => $account_and_feed_info['settings']
		    );

		    $selected_types = explode( ',', $default_type_and_terms['type'] );
		    ?>
        <div class="sbsw-default-feed-wrap" data-plugin="facebook">

            <?php if ( !$empty_connected_accounts ){ ?>
                <div class="sbsw-platform-label">
                    <span class="sbsw-added">
                        <?php echo SW_Display_Elements::get_icon( 'yes' ); ?>
                        <?php echo __( 'Added to Wall', 'social-wall' ); ?>  
                    </span>
                    <span class="sbsw-removed">
                        <?php echo SW_Display_Elements::get_icon( 'no' ); ?>
                        <?php echo __( 'Not in Wall', 'social-wall' ); ?>
                    </span>
                    <button class="sbsw-add-remove-plugin button">
                        <?php echo __( 'Remove', 'social-wall' ); ?>
                            
                        </button>
                </div>
            <?php } ?>

            <h4 class="sbsw-platform-name"><?php echo SW_Display_Elements::get_icon( 'facebook' ); ?>Facebook</h4>

            <?php if ( $empty_connected_accounts ) :
                $json_array['facebook']['exclude'] = true;?>
                <div id="cff-admin" style="margin-top: 15px;">
                    <a href="JavaScript:void(0);" class="cff_admin_btn" id="cff_fb_login"><i class="fa fa-facebook-square"></i> <?php _e( 'Connect a Facebook account', 'custom-facebook-feed' ); ?></a>
                </div>
            <?php endif; ?>

            <?php if ( ! $empty_connected_accounts ) : ?>
            <div class="sbsw-feed-settings">

                <div class="sbsw-feed-type">
                    <h4><?php echo __( 'Feed Type:', 'social-wall' ); ?></h4>
                    <div class="sbsw-types-checkbox-wrap">
                        <?php foreach ( $available_types as $available_type ) :
                            $checked = in_array( $available_type['shortcode'], $selected_types, true ) ? ' checked' : '';
                            ?>
                            <input id="sbsw_fb_types_<?php echo $available_type['shortcode']; ?>" type="radio" value="<?php echo $available_type['shortcode']; ?>" name="sbsw_fb_types"<?php echo $checked; ?>><label for="sbsw_fb_types_<?php echo $available_type['shortcode']; ?>"><?php echo esc_html( $available_type['label'] ); ?></label><br>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="sbsw-connected-accounts-wrap">
                    <div class="sbsw-connected-accounts-inner">
                        <h4><?php echo __( 'Select Page/Group for Wall:', 'social-wall' ); ?></h4>
                        <?php
                        foreach ( $connected_accounts as $connected_account ) :
                            $is_default = false;
                            $wrap_class = '';
                            $button_class = '';
                            $button_text = __( 'Add', 'social-wall' );
                            if ( $default_type_and_terms['type'] === 'user'
                                 || $default_type_and_terms['type'] === 'tagged' ) {
                                if ( in_array( $connected_account->id, $default_type_and_terms['terms'], true ) ) {
                                    $wrap_class = ' sbsw-selected';
                                    $button_class = ' sbsw-is-default';
                                    $button_text = __( 'Remove', 'social-wall' );
                                }
                            }
                            ?>

                            <div class="sbsw-connected-account<?php echo esc_attr( $wrap_class ); ?>" data-id="<?php echo esc_attr( $connected_account->id ); ?>" data-user="<?php echo esc_attr( $connected_account->id ); ?>">
                                <button class="sbsw-add-remove-account button<?php echo esc_attr( $button_class ); ?>"><?php echo esc_html( $button_text ); ?></button>
                                <p>
                                    <?php echo esc_html( urldecode( $connected_account->name ) ); ?>

	                                <?php if ( $connected_account->name !== $connected_account->id ) : ?>
                                    <span>(<?php echo esc_html( $connected_account->id ); ?>)</span>
	                                <?php endif; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="cff-admin" class="sbsw-connect-account-btn-small">
                        <a href="JavaScript:void(0);" class="cff_admin_btn" id="cff_fb_login"><i class="fa fa-facebook-square"></i> <?php _e( 'Connect a Facebook account', 'custom-facebook-feed' ); ?></a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
	    <?php endif; ?>

	    <?php if ( $tw_active && $tw_compatible ) :
		    $account_and_feed_info = ctf_get_account_and_feed_info();
            $default_type_and_terms = $account_and_feed_info['type_and_terms'];
            $connected_accounts = $account_and_feed_info['connected_accounts'];
            $if_connected_accounts = $connected_accounts;

            $available_types = $account_and_feed_info['available_types'];

            $empty_connected_accounts = empty( $connected_accounts );

            $json_array['twitter'] = array(
            'current' => array(
            'type' => $default_type_and_terms['type'],
            'term' => implode(',',$default_type_and_terms['terms'])
            ),
            'available_types' => $available_types,
            'settings' => $account_and_feed_info['settings']
            );
            ?>
            <div class="sbsw-default-feed-wrap" data-plugin="twitter">

                <?php if ( !$empty_connected_accounts ){ ?>
                    <div class="sbsw-platform-label">
                        <span class="sbsw-added">
                            <?php echo SW_Display_Elements::get_icon( 'yes' ); ?>
                            <?php echo __( 'Added to Wall', 'social-wall' ); ?>  
                        </span>
                        <span class="sbsw-removed">
                            <?php echo SW_Display_Elements::get_icon( 'no' ); ?>
                            <?php echo __( 'Not in Wall', 'social-wall' ); ?>
                        </span>
                        <button class="sbsw-add-remove-plugin button"><?php echo __( 'Remove', 'social-wall' ); ?></button>
                    </div>
                <?php } ?>
            
                <h4 class="sbsw-platform-name"><?php echo SW_Display_Elements::get_icon( 'twitter' ); ?>Twitter</h4>

			    <?php if ( empty( $connected_accounts ) ) :
				    $json_array['twitter']['exclude'] = true; ?>
                <div id="ctf-admin">
                <?php CtfAdmin::the_admin_access_token_configure_html( $_GET, 'admin.php?page=sbsw', false ); ?>
                </div>
                <?php endif; ?>

	            <?php if ( ! empty( $connected_accounts ) ) : ?>
                <div class="sbsw-feed-settings">
                    <h4><?php echo __( 'Feed Type:', 'social-wall' ); ?></h4>

                    <select class="sbsw-type-select" name="sbsw_twitter_types">
					    <?php foreach ( $available_types as $available_type ) :
						    $selected = $available_type['shortcode'] === $default_type_and_terms['type'] ? ' selected' : '';
						    ?>
                            <option value="<?php echo esc_attr( $available_type['shortcode'] ); ?>"<?php echo $selected; ?>><?php echo esc_html( $available_type['label'] ); ?></option>
					    <?php endforeach; ?>
                    </select>

                    <div class="sbsw-text-input-wrap">
                        <input type="text" name="sbsw-twitter-text" value="<?php echo esc_attr( $json_array['twitter']['current']['term'] ); ?>">
                        <span class="sbsw-text-input-instructions"></span>
                    </div>

                    <div class="sbsw-message-wrap">
                        <p>This type of feed will use the account that is attached to your access token</p>
                    </div>
                </div>
			    <?php endif; ?>

            </div>
	    <?php endif; ?>

	    <?php if ( $yt_active && $yt_compatible ) :
		    $account_and_feed_info = sby_get_account_and_feed_info();
		    $default_type_and_terms = $account_and_feed_info['type_and_terms'];
		    $connected_accounts = $account_and_feed_info['connected_accounts'];
		    $channel_ids_names = $account_and_feed_info['channel_ids_names'];

		    $yt_connected_accounts = $connected_accounts;

		    $available_types = $account_and_feed_info['available_types'];

		    $empty_connected_accounts = empty( $connected_accounts ) || isset( $connected_accounts['rss_only'] );

		    $json_array['youtube'] = array(
			    'current' => array(
				    'type' => $default_type_and_terms['type'],
				    'term' => implode(',',$default_type_and_terms['terms'])
			    ),
			    'available_types' => $available_types,

                'settings' => $account_and_feed_info['settings'],
			    'channel_ids_names' => $account_and_feed_info['channel_ids_names']

		    );
		    ?>
            <div class="sbsw-default-feed-wrap" data-plugin="youtube">

                <?php if ( !$empty_connected_accounts ){ ?>
                    <div class="sbsw-platform-label">
                        <span class="sbsw-added">
                            <?php echo SW_Display_Elements::get_icon( 'yes' ); ?>
                            <?php echo __( 'Added to Wall', 'social-wall' ); ?>  
                        </span>
                        <span class="sbsw-removed">
                            <?php echo SW_Display_Elements::get_icon( 'no' ); ?>
                            <?php echo __( 'Not in Wall', 'social-wall' ); ?>
                        </span>
                        <button class="sbsw-add-remove-plugin button">
                            <?php echo __( 'Remove', 'social-wall' ); ?>
                                
                            </button>
                    </div>
                <?php } ?>
                
                <h4 class="sbsw-platform-name"><?php echo SW_Display_Elements::get_icon( 'youtube' ); ?>YouTube</h4>

			    <?php if ( $empty_connected_accounts ) :
				    $json_array['youtube']['exclude'] = true;
				    $oauth_processor_url = '';
				    if ( defined( 'SBY_OAUTH_PROCESSOR_URL' ) ) {
					    $oauth_processor_url = SBY_OAUTH_PROCESSOR_URL;
				    }
				    ?>
                    <div id="sby_admin">
                        <a href="<?php echo $oauth_processor_url . admin_url( 'admin.php?page=sbsw' ); ?>" id="sbspf_get_token"><?php echo sprintf( __( 'Connect to %s to Create a Feed', 'youtube-feed' ), 'YouTube' ); ?></a>
                    </div>
                <?php endif; ?>

	            <?php if ( ! $empty_connected_accounts ) : ?>
                <div class="sbsw-feed-settings">

                    <h4><?php echo __( 'Feed Type:', 'social-wall' ); ?></h4>

                    <select class="sbsw-type-select" name="sbsw_youtube_types">
					    <?php foreach ( $available_types as $available_type ) :
						    $selected = $available_type['shortcode'] === $default_type_and_terms['type'] ? ' selected' : '';
						    ?>
                            <option value="<?php echo esc_attr( $available_type['shortcode'] ); ?>"<?php echo $selected; ?>><?php echo esc_html( $available_type['label'] ); ?></option>
					    <?php endforeach; ?>
                    </select>

                    <div class="sbsw-text-input-wrap">
                        <input type="text" name="sbsw-youtube-text" value="<?php echo esc_attr( $json_array['youtube']['current']['term'] ); ?>">
                        <span class="sbsw-text-input-identity"></span>
                        <span class="sbsw-text-input-instructions"></span>
                    </div>
                </div>
			    <?php endif; ?>

            </div>
	    <?php endif; ?>

        <?php if ( !$if_active || !$if_compatible ) : ?>
            <div class="sbsw-default-feed-wrap sbsw-plugin-missing">
                <h4 class="sbsw-platform-name"><?php echo SW_Display_Elements::get_icon( 'instagram' ); ?>Instagram</h4>
	            <?php if ( $if_active && !$if_compatible ) : ?>
                    <div class="sbsw-content">
                        <p><?php echo __( 'The version of the Instagram Feed plugin you are using is not compatible with the Social Wall plugin and needs to be updated.', 'social-wall' ); ?></p>
                        <a class="button-primary" href="plugins.php"><?php echo __( 'Update Instagram Plugin', 'social-wall' ); ?></a>
                    </div>
	            <?php else : ?>
                    <div class="sbsw-content">
                        <p><?php echo __( 'Add Instagram posts to your Social Wall.', 'social-wall' ); ?></p>
                        <a class="button-primary" href="plugin-install.php"><?php echo __( 'Install Instagram Plugin', 'social-wall' ); ?></a>
                        <p><?php echo __( "Don't have this plugin?", "social-wall" ); ?> <a href="https://smashballoon.com/instagram-feed/" target="_blank"><?php echo __( "Get it now", "social-wall" ); ?></a></p>
                    </div>
	            <?php endif; ?>

            </div>
        <?php endif; ?>

        <?php if ( !$fb_active || !$fb_compatible ) : ?>
            <div class="sbsw-default-feed-wrap sbsw-plugin-missing">
                <h4 class="sbsw-platform-name"><?php echo SW_Display_Elements::get_icon( 'facebook' ); ?>Facebook</h4>
	            <?php if ( $fb_active && !$fb_compatible ) : ?>
                    <div class="sbsw-content">
                        <p><?php echo __( 'The version of the Custom Facebook Feed plugin you are using is not compatible with the Social Wall plugin and needs to be updated.', 'social-wall' ); ?></p>
                        <a class="button-primary" href="plugins.php"><?php echo __( 'Update Facebook Plugin', 'social-wall' ); ?></a>
                    </div>
	            <?php else : ?>
                    <div class="sbsw-content">
                        <p><?php echo __( 'Add Facebook content to your Social Wall.', 'social-wall' ); ?></p>
                        <a class="button-primary" href="plugin-install.php"><?php echo __( 'Install Facebook Plugin', 'social-wall' ); ?></a>
                        <p><?php echo __( "Don't have this plugin?", "social-wall" ); ?> <a href="https://smashballoon.com/custom-facebook-feed/" target="_blank"><?php echo __( "Get it now", "social-wall" ); ?></a></p>
                    </div>
	            <?php endif; ?>

            </div>
        <?php endif; ?>

        <?php if ( !$tw_active || !$tw_compatible ) : ?>
            <div class="sbsw-default-feed-wrap sbsw-plugin-missing">
                <h4 class="sbsw-platform-name"><?php echo SW_Display_Elements::get_icon( 'twitter' ); ?>Twitter</h4>
	            <?php if ( $tw_active && !$tw_compatible ) : ?>
                    <div class="sbsw-content">
                        <p><?php echo __( 'The version of the Custom Twitter Feeds plugin you are using is not compatible with the Social Wall plugin and needs to be updated.', 'social-wall' ); ?></p>
                        <a class="button-primary" href="plugins.php"><?php echo __( 'Update Twitter Plugin', 'social-wall' ); ?></a>
                    </div>
	            <?php else : ?>
                    <div class="sbsw-content">
                        <p><?php echo __( 'Add Tweets to your Social Wall.', 'social-wall' ); ?></p>
                        <a class="button-primary" href="plugin-install.php"><?php echo __( 'Install Twitter Plugin', 'social-wall' ); ?></a>
                        <p><?php echo __( "Don't have this plugin?", "social-wall" ); ?> <a href="https://smashballoon.com/custom-twitter-feeds/" target="_blank"><?php echo __( "Get it now", "social-wall" ); ?></a></p>
                    </div>
	            <?php endif; ?>

            </div>
        <?php endif; ?>

        <?php if ( !$yt_active || !$yt_compatible) : ?>
            <div class="sbsw-default-feed-wrap sbsw-plugin-missing">
                <h4 class="sbsw-platform-name"><?php echo SW_Display_Elements::get_icon( 'youtube' ); ?>YouTube</h4>
	            <?php if ( $yt_active && !$yt_compatible ) : ?>
                    <div class="sbsw-content">
                        <p><?php echo __( 'The version of the Feeds for YouTube plugin you are using is not compatible with the Social Wall plugin and needs to be updated.', 'social-wall' ); ?></p>
                        <a class="button-primary" href="plugins.php"><?php echo __( 'Update YouTube Plugin', 'social-wall' ); ?></a>
                    </div>
	            <?php else : ?>
                    <div class="sbsw-content">
                        <p><?php echo __( 'Add YouTube videos to your Social Wall.', 'social-wall' ); ?></p>
                        <a class="button-primary" href="plugin-install.php"><?php echo __( 'Install YouTube Plugin', 'social-wall' ); ?></a>
                        <p><?php echo __( "Don't have this plugin?", "social-wall" ); ?> <a href="https://smashballoon.com/youtube-feed/" target="_blank"><?php echo __( "Get it now", "social-wall" ); ?></a></p>
                    </div>
	            <?php endif; ?>

            </div>
        <?php endif; ?>


    </div>
    <span id="sbsw-account-json" data-json="<?php echo esc_attr( wp_json_encode( $json_array ) ); ?>"></span>
</div>
<form method="post" action="">
	<?php $this->hidden_fields_for_tab( 'configure' ); ?>

	<?php foreach ( $this->get_sections( 'configure' ) as $section ) : ?>

		<?php do_settings_sections( $section['id'] ); // matches the section name ?>
		<?php if ( $section['save_after'] ) : ?>
            <p class="submit"><input class="button-primary" type="submit" name="save" value="<?php esc_attr_e( 'Save Changes' ); ?>" /></p>
		<?php endif; ?>
        <hr>
	<?php endforeach; ?>
</form>

<?php if( isset($_GET['access_token']) && isset($_GET['graph_api']) && empty($_POST) ) { ?>
<div id="sbi_admin" class="wrap sbsw-reload" data-reload="<?php echo esc_attr( admin_url('admin.php?page=sbsw') ); ?>">
    <?php sbi_get_business_account_connection_modal( '' ); ?>
</div>
<?php } elseif ( isset( $_GET['access_token'] ) && isset( $_GET['account_type'] ) && empty( $_POST ) ) { ?>
<div id="sbi_admin" class="wrap sbsw-reload" data-reload="<?php echo esc_attr( admin_url('admin.php?page=sbsw') ); ?>">
    <?php sbi_get_personal_connection_modal( $if_connected_accounts, 'admin.php?page=sbsw' ); ?>
</div>
<?php } elseif ( isset( $_GET['final_response'] ) ) { ?>
    <input type="hidden" id="cff-reloader" class="wrap sbsw-reload" data-reload="<?php echo esc_attr( admin_url('admin.php?page=sbsw') ); ?>">
<?php }