<?php
/* this provides a javascript button that allows the users to print out
 * all maker pdf's
 */
global $wpdb;
$selfaire = '';
$type     = '';
?>

<script>
	var mfSigns = {
		nonce: <?php echo wp_json_encode( wp_create_nonce( 'mf_faire_signs' ) ); ?>,
		themeUri: <?php echo wp_json_encode( get_template_directory_uri() ); ?>
	};
</script>

<div id="faire-signs">
	<h2 style="text-align:center">Faire Signs</h2>
	<div class="panel-group" id="accordion">
		<?php
		$results = $wpdb->get_results( "SELECT faire, faire_name, form_ids FROM wp_mf_faire ORDER BY start_dt DESC" );
		$first   = true;

		foreach ( $results as $row ) :
			$faire = $row->faire;
			?>

			<h3 class="panel-title"><?php echo esc_html( $row->faire_name ); ?></h3>
			<div>
				<div class="panel-body" id="tabs<?php echo esc_attr( $faire ); ?>">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a aria-controls="#maker<?php echo esc_attr( $faire ); ?>" role="tab" data-toggle="#maker<?php echo esc_attr( $faire ); ?>" href="#maker<?php echo esc_attr( $faire ); ?>">Maker Signs</a></li>
						<li role="presentation"><a aria-controls="#presenter<?php echo esc_attr( $faire ); ?>" role="tab" data-toggle="#presenter<?php echo esc_attr( $faire ); ?>" href="#presenter<?php echo esc_attr( $faire ); ?>">Presenter Signs</a></li>
						<li role="presentation"><a aria-controls="#table<?php echo esc_attr( $faire ); ?>" role="tab" data-toggle="#table<?php echo esc_attr( $faire ); ?>" href="#table<?php echo esc_attr( $faire ); ?>">Table Tags</a></li>
					</ul>

					<div class="tab-content">

						<?php
						/* One block per sign type. $key is what createPDF() sends and what the
						 * pdfEntList span is classed with; $zipKey is what createZip() sends and
						 * what the updateMsg span is classed with; $dir is the output folder. */
						$panes = array(
							array(
								'id'      => 'maker' . $faire,
								'classes' => 'tab-pane fade in active',
								'label'   => 'maker signs',
								'key'     => 'signs',
								'zipKey'  => 'maker',
								'dir'     => 'maker',
								'forms'   => true,
							),
							array(
								'id'      => 'presenter' . $faire,
								'classes' => 'tab-pane fade',
								'label'   => 'presenter signs',
								'key'     => 'presenter',
								'zipKey'  => 'presenter',
								'dir'     => 'presenter',
								'forms'   => false,
							),
							array(
								'id'      => 'table' . $faire,
								'classes' => 'tab-pane fade',
								'label'   => 'table tags',
								'key'     => 'tabletags',
								'zipKey'  => 'tabletags',
								'dir'     => 'tabletags',
								'forms'   => false,
							),
						);

						foreach ( $panes as $pane ) :
							$lastrunFile = get_template_directory() . '/signs/' . $faire . '/' . $pane['dir'] . '/lastrun.txt';
							$lastCreated = file_exists( $lastrunFile ) ? file_get_contents( $lastrunFile ) : '';
							?>

							<div id="<?php echo esc_attr( $pane['id'] ); ?>" class="<?php echo esc_attr( $pane['classes'] ); ?>">
								<div class="is-flex">
									<div style="margin-top: 22px;">
										<i>Click the button to generate all <?php echo esc_html( $pane['label'] ); ?> for this faire</i><br>
										<input type="button" style="text-align:center;width: 400px;" name="zipCreate"
											value="Generate all signs" class="button button-large button-primary"
											onClick="createPDF('<?php echo esc_js( $faire ); ?>', '<?php echo esc_js( $pane['key'] ); ?>')" />
										<br/>
										<span class="<?php echo esc_attr( $pane['key'] ); ?> pdfEntList"></span>
									</div>
									<div style="margin-left: 20px;"><p><br><br>Last created on: <?php echo esc_html( $lastCreated ); ?></p></div>
								</div>

								<div class="is-flex">
									<div class="right-border">
										<h4>Create a zip file of the <?php echo esc_html( $pane['label'] ); ?>:</h4>

										<div class="is-flex justify-space-between">
											<div>
												How should we group the zip files?<br/>
												<input type="radio" name="<?php echo esc_attr( $faire . $pane['zipKey'] ); ?>seltype" value="area" checked> By Area<br>
												<input type="radio" name="<?php echo esc_attr( $faire . $pane['zipKey'] ); ?>seltype" value="subarea"> By Subarea<br>
												<input type="radio" name="<?php echo esc_attr( $faire . $pane['zipKey'] ); ?>seltype" value="faire"> By Faire<br>
											</div>
											<div>
												What entry status(es) should we include?<br/>
												<input type="radio" name="<?php echo esc_attr( $faire . $pane['zipKey'] ); ?>selstatus" value="accepted" checked> Accepted Only<br>
												<input type="radio" name="<?php echo esc_attr( $faire . $pane['zipKey'] ); ?>selstatus" value="accAndProp"> Accepted and Proposed<br>
												<input type="radio" name="<?php echo esc_attr( $faire . $pane['zipKey'] ); ?>selstatus" value="all"> All Status
											</div>
										</div>

										<?php if ( $pane['forms'] ) : ?>
											<br>
											<div>Filter by specific form (optional):</div>
											<select name="<?php echo esc_attr( $faire . $pane['zipKey'] ); ?>filterform" multiple>
												<?php
												$forms = preg_split( '/,/', $row->form_ids );
												foreach ( $forms as $formId ) {
													$formId = absint( trim( $formId ) );
													if ( ! $formId ) {
														continue;
													}
													$formResult = $wpdb->get_results(
														$wpdb->prepare(
															"SELECT title FROM wp_gf_form WHERE id = %d AND is_active = 1 AND is_trash = 0 ORDER BY title DESC",
															$formId
														)
													);
													foreach ( $formResult as $formRow ) {
														printf(
															'<option value="%d">%s</option>',
															$formId,
															esc_html( $formRow->title )
														);
													}
												}
												?>
											</select>
											<br>
											<p><input type="checkbox" name="<?php echo esc_attr( $faire . $pane['zipKey'] ); ?>filtererror" value="error"> Include signs in error?</p>
										<?php endif; ?>

										<br/>
										<input type="button" style="text-align:center" name="zipCreate" value="Re-Create Zip Files"
											class="button button-large button-primary"
											onClick="createZip('<?php echo esc_js( $faire ); ?>', '<?php echo esc_js( $pane['zipKey'] ); ?>')" /><br/>
										<span class="<?php echo esc_attr( $pane['zipKey'] ); ?> updateMsg"></span>
									</div>

									<div>
										<h4>Download generated zip files</h4>
										<?php
										$signDir = get_template_directory() . '/signs/' . $faire . '/' . $pane['dir'] . '/zip/';
										$files   = glob( $signDir . '*.zip' );

										if ( is_array( $files ) && ! empty( $files ) ) {
											foreach ( $files as $zipFile ) {
												$url = get_template_directory_uri() . '/signs/' . $faire . '/' . $pane['dir'] . '/zip/' . basename( $zipFile );
												?>
												<div class="is-flex">
													<div><a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_html( basename( $zipFile ) ); ?></a></div>
													<div><?php echo esc_html( date( 'm/d/Y H:i', filemtime( $zipFile ) ) ); ?></div>
												</div>
												<?php
											}
										} else {
											echo '<i>No Zip files found.<br>Please use the tools to the left to generate.</i>';
										}
										?>
									</div>
								</div>
							</div>

						<?php endforeach; ?>

					</div>
				</div>
			</div>
			<?php
			$first = false;
		endforeach;
		?>
	</div>
</div>

<script>
	jQuery( function () {
		jQuery( '#accordion' ).accordion( {
			collapsible: true,
			autoHeight: false,
			heightStyle: 'content'
		} );
		jQuery( "[id^='tabs']" ).tabs();
	} );
</script>
