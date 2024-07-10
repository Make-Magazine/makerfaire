<?php 

namespace WPGMZA;

class ImportSETTINGS extends Import{

	public function import() {
		
		if(is_object($this->file_data)){
			$keyMap = array(
				'global' => 'importObjects',
				'styling' => 'importObjects',
				'keys' => 'importValues'
			);

			foreach($this->file_data as $key => $data){
				if(!empty($keyMap[$key])){
					$method = $keyMap[$key];
					if(method_exists($this, $method)){
						$this->{$method}($data);
					}
				}
			}
		}

		$this->onImportComplete();
	}

	public function importObjects($data){
		if(!empty($data) && is_object($data)){
			foreach($data as $key => $value){
				try{
					$json = json_encode($value);

					update_option($key, $json);
				} catch (\Exception $ex){

				} catch (\Error $err){

				}
			}
		}
	}

	public function importValues($data){
		if(!empty($data) && is_object($data)){
			foreach($data as $key => $value){
				update_option($key, $value);
			}
		}
	}

	protected function check_options() {
		if ( ! is_array( $this->options ) ) {
			if(empty($this->options)){
				$this->options = array();
			} else {
				throw new \Exception( __( 'Error: Malformed options.', 'wp-google-maps' ) );
			}
		}

		$this->options['delete']   = isset( $this->options['delete'] ) ? true : false;
	}

	protected function parse_file() {
		$this->log("Attempting to parse WPGMZA Settings");

		if(!empty($this->file_data)){
			$this->file_data = json_decode($this->file_data);
			
			if($this->file_data === null){
				$this->log("Failed to parse WPGMZA Settings");
				$this->log(json_last_error_msg());
			
				throw new \Exception( __('Error parsing WPGMZA Settings: ', 'wp-google-maps') . json_last_error_msg() );
			}

			if(empty($this->file_data->type) || $this->file_data->type !== "configuration"){
				throw new \Exception( __('WPGMZA Settings do not appear to be valid', 'wp-google-maps') );
			}
		} else {
			$this->log("The file is empty");
			throw new \Exception( __( 'Error: Empty file data.', 'wp-google-maps' ) );
		}
	}

	public function admin_options(){
		ob_start();
		?>
			<h2><?php esc_html_e( 'Import Settings', 'wp-google-maps' ); ?></h2>
			<h4><?php echo ! empty($this->file) ? esc_html(basename( $this->file)) : (!empty( $this->file_url) ? esc_html($this->file_url) : ''); ?></h4>

			<br>
			<div class="delete-after-import">
				<div class="switch">
					<input id="delete_import" class="map_data_import cmn-toggle cmn-toggle-round-flat" type="checkbox" <?php echo $doing_edit ? 'disabled' : ''; ?>>
					<label for="delete_import"></label>
				</div>
				<?php esc_html_e( 'Delete import file after import', 'wp-google-maps' ); ?>
			</div>
		
			<br><br>
		
			<p>
				<button id="import-settings" class="wpgmza_general_btn"><?php esc_html_e( 'Import', 'wp-google-maps' ); ?></button>
			</p>
			
			<script>
				(function($){
					function settings_get_import_options(){
						var import_options = {};
					
						if ($('#delete_import').prop('checked')){
							import_options['delete'] = true;
						}
						
						return import_options;
					}
				
					$('#import-settings').click(function(){
						var import_options = settings_get_import_options();

						if($("#import_loader").hasClass('wpgmza-import-loader')){
							/* New loader */
							$('.wpgmza-import-loader .wpgmza-loader-message').text('<?php echo wp_slash( __( 'Importing, this may take a moment...', 'wp-google-maps' )); ?>');
							$('.wpgmza-import-loader .wpgmza-progress-bar').addClass('wpgmza-hidden');
						} else {
							/* Old approach */
							$('#import_loader_text').html('<br><?php echo wp_slash( __( 'Importing, this may take a moment...', 'wp-google-maps' ) ); ?>');
						}

						$('#import_loader').show();
						$('#import_options').hide();
					
						wp.ajax.send({
							data: {
								action: 'wpgmza_import',
								<?php echo isset( $_POST['import_id'] ) ? 'import_id: ' . absint( $_POST['import_id'] ) . ',' : ( isset( $_POST['import_url'] ) ? "import_url: '" . $_POST['import_url'] . "'," : '' ); ?>

								options: import_options,
								wpgmaps_security: WPGMZA.import_security_nonce
							},
							success: function (data) {
								$('#import_loader').hide();
								
								if (typeof data !== 'undefined' && data.hasOwnProperty('id')) {
									wpgmaps_import_add_notice('<p><?php echo wp_slash( __( 'Import completed.', 'wp-google-maps' ) ); ?></p>');
									if (data.hasOwnProperty('del') && 1 === data.del){
										$('#import_options').html('');
										$('#import-list-item-' + data.id).remove();
										$('#import_files').show();
										return;
									}
								}
								
								$('#import_options').show();
							},
							error: function (data) {
								if (typeof data !== 'undefined') {
									wpgmaps_import_add_notice(data, 'error');
								}
								
								$('#import_loader').hide();
								$('#import_options').show();
							}
						});
					});
			})(jQuery);
		</script>
		<?php

		return ob_get_clean();

	}
}