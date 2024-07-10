<div class="wpgmza-directions-box wpgmza-panel-view">
	<div class="wpgmza-panel-actions">
		<svg class='wpgmza-close' width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M1.17157 27.1716C-0.390528 28.7337 -0.390528 31.2663 1.17157 32.8284L26.6274 58.2843C28.1895 59.8464 30.7222 59.8464 32.2843 58.2843C33.8464 56.7222 33.8464 54.1895 32.2843 52.6274L9.65685 30L32.2843 7.37258C33.8464 5.81049 33.8464 3.27783 32.2843 1.71573C30.7222 0.153632 28.1895 0.153632 26.6274 1.71573L1.17157 27.1716ZM64 26L4 26V34L64 34V26Z" />
			<title><?php _e("Close", "wp-google-maps"); ?></title>
		</svg>
	</div>

	<div class="wpgmza-directions-box-title"><?php esc_html_e("Get Directions", "wp-google-maps"); ?></div>
	
	<div class="wpgmza-directions-box-inner">
		<div class="wpgmza-directions-actions">
			<div class="wpgmza-directions__travel-mode wpgmza-flex-row">
				<div class="wpgmza-travel-mode-option wpgmza-travel-option__selected" data-mode="driving">
					<img src="<?php esc_attr_e(WPGMZA_PRO_DIR_URL . 'images/icons/directions_car.png'); ?>">
				</div>
				<div class="wpgmza-travel-mode-option" data-mode="walking">
					<img src="<?php esc_attr_e(WPGMZA_PRO_DIR_URL . 'images/icons/directions_walking.png'); ?>">
				</div>
				<div class="wpgmza-travel-mode-option" data-mode="transit">
					<img src="<?php esc_attr_e(WPGMZA_PRO_DIR_URL . 'images/icons/directions_transit.png'); ?>">
				</div>
				<div class="wpgmza-travel-mode-option" data-mode="bicycling">
					<img src="<?php esc_attr_e(WPGMZA_PRO_DIR_URL . 'images/icons/directions_bike.png'); ?>">
				</div>
			</div>

			<div class="wpgmza-directions-locations">
				<div class="wpgmza-directions-from wpgmza-directions-input-row">
					<svg width="40" height="40" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="wpgmza-circle">';
						<circle cx="30" cy="30" r="20.5" stroke-width="11"/>';
					</svg>

					<label class="wpgmza-form-field__label"><?php esc_html_e('From', 'wp-google-maps'); ?></label>
					<input class="wpgmza-address wpgmza-directions-from wpgmza-form-field__input" type="text" placeholder="<?php esc_html_e('From', 'wp-google-maps'); ?>"/>
				</div>

				<div class="wpgmza-directions-to wpgmza-directions-input-row">
					<svg width="40" height="40" viewBox="0 0 43 60" fill="none" xmlns="http://www.w3.org/2000/svg" class='wpgmza-mark'>
						<path d="M21.25 60L2.84696 31.875L39.653 31.875L21.25 60Z"/>
						<circle cx="21.25" cy="21.25" r="15.25" stroke-width="12"/>
					</svg>

					<label><?php esc_html_e('To', 'wp-google-maps'); ?></label>
					<input class="wpgmza-address wpgmza-form-field__input wpgmza-directions-to" type="text" placeholder="<?php esc_html_e('To', 'wp-google-maps'); ?>"/>
				</div>
				
				<div class='wpgmza-waypoint-via wpgmza-directions-input-row'>
					
					<button href="javascript:;" class="wpgmza_remove_via" title="<?php esc_html_e('Remove waypoint', 'wp-google-maps'); ?>">
						<svg width="40" height="40" viewBox="0 0 62 62" fill="none" xmlns="http://www.w3.org/2000/svg" class="wpgmza-multiply">
							<line x1="4.94975" y1="5" x2="56.5685" y2="56.6188" stroke-width="10" stroke-linecap="round"/>
							<line x1="5" y1="56.6188" x2="56.6188" y2="5" stroke-width="10" stroke-linecap="round"/>
						</svg>
					</button>
										
					<input class="wpgmza-waypoint-via" type="text" placeholder="<?php esc_html_e('Via', 'wp-google-maps'); ?>"/>
				</div>
				
				<div class='wpgmza-add-waypoint'>
					<a href='javascript:;' class='wpgmaps_add_waypoint'>
						<?php esc_html_e('Add Waypoint', 'wp-google-maps'); ?>
					</a>
				</div>
			</div>

			<div class="wpgmza-hidden">
			<label class="wpgmza-travel-mode wpgmza-form-field__label">
				<?php
				esc_html_e('For', 'wp-google-maps');
				?>
			</label>
			<select class="wpgmza-travel-mode wpgmza-form-field__input">
				<option value="driving">
					<?php
					esc_html_e("Driving", "wp-google-maps");
					?>
				</option>
				<option value="walking">
					<?php
					esc_html_e("Walking", "wp-google-maps");
					?>
				</option>
				<option value="transit">
					<?php
					esc_html_e("Transit", "wp-google-maps");
					?>
				</option>
				<option value="bicycling">
					<?php
					esc_html_e("Bicycling", "wp-google-maps");
					?>
				</option>
			</select>
			</div>
		</div>
	</div>

	<div class="wpgmza-directions-options-bar">

		<div class="wpgmza-directions-options__section">
			<a href="javascript:;" class="wpgmza-show-directions-options">
				<?php esc_html_e("Options","wp-google-maps"); ?>
			</a>
			
			<a href="javascript:;" class="wpgmza-hide-directions-options">
				<?php esc_html_e("hide options","wp-google-maps"); ?>
			</a>
			
			<div class="wpgmza-directions-options">
				<label>
					<input type="checkbox" class="wpgmza-avoid-tolls" value="tolls"/>
					<?php
					esc_html_e('Avoid Tolls', 'wp-google-maps');
					?>
				</label>
				<label>
					<input type="checkbox" class="wpgmza-avoid-highways" value="highways"/>
					<?php
					esc_html_e('Avoid Highways', 'wp-google-maps');
					?>
				</label>
				<label>
					<input type="checkbox" class="wpgmza-avoid-ferries" value="ferries"/>
					<?php
					esc_html_e('Avoid Ferries', 'wp-google-maps');
					?>
				</label>
			</div>
		</div>
		
		<div class="wpgmza-directions-buttons">
			<input class="wpgmza-get-directions" onclick="javascript:;" type="button" value="<?php esc_html_e('Go', 'wp-google-maps') ?>"/>
		</div>
	</div>

	<span class="wpgmza-directions-result__buttons wpgmza-directions-result-bar wpgmza-hidden">
		<a class="wpgmza-print-directions" onclick="javascript:;">
			<?php esc_html_e('Print', 'wp-google-maps'); ?>
		</a>
		
		<a class="wpgmza-reset-directions" onclick="javascript:;">
			<?php esc_html_e('Reset', 'wp-google-maps'); ?>
		</a>
	</span>

	<div class="wpgmza-directions-notifications wpgmza-hidden"><?php _e("Fetching directions...","wp-google-maps"); ?></div>
	<div class="wpgmza-directions-output-panel"></div>

	
</div>