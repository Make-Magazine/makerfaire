<div>
	<h3>
		<?php esc_html_e('Cache Settings', 'wp-google-maps'); ?>
	</h3>
	
	<fieldset>
		<legend>
            <?php esc_html_e('Enable Caching (beta)', 'wp-google-maps'); ?>
		</legend>

		<div class="switch switch-inline">
			<input 
				id="enable_caching" 
				name="enable_caching" 
				class="cmn-toggle cmn-toggle-yes-no"
				type="checkbox"/> 
				
			<label
				data-on="<?php esc_html_e('Yes', 'wp-google-maps'); ?>" 
				data-off="<?php esc_html_e('No', 'wp-google-maps'); ?>"
				for="enable_caching"></label>

			<label for="enable_caching">
                <small>
                    <?php esc_html_e('An experimental map caching system which will provide better loading times for large datasets. This is still in development, and may result in inconsistencies in some modules', 'wp-google-maps'); ?>
                </small>
			</label>
		</div>
	</fieldset>

    <fieldset class="caching-conditional-row">
		<legend>
            <?php esc_html_e('Cached Data', 'wp-google-maps'); ?>
		</legend>

        <div class='cache-info'></div>
        <br>
        <div class="wpgmza-flex-row">
			<a class='wpgmza_general_btn button button-secondary' href="<?php echo wp_nonce_url(admin_url('admin.php?page=wp-google-maps-menu-settings&_wpmgza_cache_action=clear#tabs-caching'), 'wpgmza_caching', 'wpgmza_caching'); ?>">Clear Cache</a>
			<a class='wpgmza_general_btn button button-secondary' href="<?php echo wp_nonce_url(admin_url('admin.php?page=wp-google-maps-menu-settings&_wpmgza_cache_action=preload#tabs-caching'), 'wpgmza_caching', 'wpgmza_caching'); ?>">Preload Cache</a>
		</div>
    </fieldset>

	<div class="caching-conditional-row"> 
		<small>
			<em>
				<strong><?php esc_html_e('Beta Note:', 'wp-google-maps'); ?></strong>
				<?php esc_html_e('We currently only cache marker data, specifically focused on first load times. Shape data and marker listings are not cached in this build, but we expect this will change in the future', 'wp-google-maps'); ?>
			</em>
		</small>
	</div>
</div>