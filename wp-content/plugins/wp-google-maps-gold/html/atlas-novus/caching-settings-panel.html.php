<div class="heading">
	<?php esc_html_e('Cache Settings', 'wp-google-maps'); ?>
</div>

<div class="tab-row">
	<div class="title">
		<?php esc_html_e('Enable Caching (beta)', 'wp-google-maps'); ?>
	</div>

	<div class='switch switch-inline'>
		<input name='enable_caching' 
				class='cmn-toggle cmn-toggle-round-flat' 
				type='checkbox' 
				id='enable_caching' 
				value='yes'/>
		
		<label for='enable_caching'></label>
		<label for='enable_caching'>
			<small>
				<?php esc_html_e('An experimental map caching system which will provide better loading times for large datasets. This is still in development, and may result in inconsistencies in some modules', 'wp-google-maps'); ?>
			</small>
		</label>
	</div>
</div>

<div class="tab-row caching-conditional-row">
	<div class="title">
		<?php esc_html_e('Cached Data', 'wp-google-maps'); ?>
	</div>

	<div class="tab-stretch-right">
		<div class='cache-info wpgmza-pad-10 wpgmza-margin-b-10'></div>
		<div class="wpgmza-flex-row">
			<a class='wpgmza-button wpgmza-button-primary wpgmza-margin-r-10' href="<?php echo wp_nonce_url(admin_url('admin.php?page=wp-google-maps-menu-settings&_wpmgza_cache_action=clear#tabs-caching'), 'wpgmza_caching', 'wpgmza_caching'); ?>">Clear Cache</a>
			<a class='wpgmza-button' href="<?php echo wp_nonce_url(admin_url('admin.php?page=wp-google-maps-menu-settings&_wpmgza_cache_action=preload#tabs-caching'), 'wpgmza_caching', 'wpgmza_caching'); ?>">Preload Cache</a>
		</div>
	</div>
</div>

<div class="tab-row caching-conditional-row">
	<div class="title"></div>

	<div class="tab-stretch-right">
		<small>
			<em>
				<strong><?php esc_html_e('Beta Note:', 'wp-google-maps'); ?></strong>
				<?php esc_html_e('We currently only cache marker data, specifically focused on first load times. Shape data and marker listings are not cached in this build, but we expect this will change in the future', 'wp-google-maps'); ?>
			</em>
		</small>
	</div>
</div>
