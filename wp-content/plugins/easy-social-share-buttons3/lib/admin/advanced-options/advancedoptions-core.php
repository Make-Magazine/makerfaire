<?php
$opts_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 'essb_options';
wp_nonce_field( 'essb_advancedoptions_setup', 'essb_advancedoptions_token' );
?>

<script type="text/javascript">
var essb_advancedopts_ajaxurl = "<?php echo esc_url(admin_url ('admin-ajax.php')); ?>",
	essb_advancedopts_reloadurl = "<?php echo esc_url(admin_url ('admin.php?page='.$opts_page)); ?>";
</script>

<div class="advancedoptions-modal"></div>
<div class="essb-helper-popup" id="essb-advancedoptions" data-width="1200" data-height="auto">
	<div class="essb-helper-popup-title">
		<span id="advancedOptions-title"></span>
		<div class="actions">
			<a href="#" class="advancedoptions-close" title="Close the window"><i class="ti-close"></i> <span>CLOSE</span></a>
			<a href="#" class="advancedoptions-save" title="Save"><i class="ti-check"></i> <span>SAVE</span></a>
		</div>
		
	</div>
	<div class="essb-helper-popup-content essb-options">
		<div class="essb-advanced-options-form" id="essb-advanced-options-form"></div>
	</div>
</div>


<div id="advancedoptions-preloader">
  <div id="advancedoptions-loader"></div>
</div>



<?php 
return;
?>

<div class="wrapper essb-settings-wrap" style="background:#fff; padding-left: 270px; padding-bottom: 40px;">

<div>
<a href="#" class="ao-options-btn"><span class="essb_icon fa fa-plus-square"></span><span>Manage display methods</span></a>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>This is portlet title</h3>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>This is portlet title</h3>
		</div>	
		<div class="advancedoptions-tile-head-tools">
			<span class="status notrunning">Inactive</span>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	Lorem Ipsum is simply dummy text of the printing and typesetting industry scrambled it to make text of the printing and typesetting industry scrambled a type specimen book text of the dummy text of the printing printing and typesetting industry scrambled dummy text of the printing.
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-config"><i class="fa fa-cog"></i>Configure</a>
		<a href="#" class="essb-btn tile-activate"><i class="fa fa-check"></i>Activate</a>
		<a href="#" class="essb-btn tile-deactivate"><i class="fa fa-close"></i>Deactivate</a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3><i class="ti-dashboard"></i>This is portlet title</h3>
		</div>	
		<div class="advancedoptions-tile-head-tools">
			<span class="status notrunning">Inactive</span>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	Lorem Ipsum is simply dummy text of the printing and typesetting industry scrambled it to make text of the printing and typesetting industry scrambled a type specimen book text of the dummy text of the printing printing and typesetting industry scrambled dummy text of the printing.
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-config"><i class="fa fa-cog"></i>Configure</a>
		<a href="#" class="essb-btn tile-activate"><i class="fa fa-check"></i>Activate</a>
		<a href="#" class="essb-btn tile-deactivate"><i class="fa fa-close"></i>Deactivate</a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>This is portlet title</h3>
		</div>	
		<div class="advancedoptions-tile-head-tools">
			<span class="status notrunning">Inactive</span>
		</div>	
	</div>
	<div class="advnacedoptions-tile-icon">
		<i class="ti-ruler-pencil"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3>This is portlet subtitle</h3>
	</div>
	<div class="advancedoptions-tile-body">
	Lorem Ipsum is simply dummy text of the printing and typesetting industry scrambled it to make text of the printing and typesetting industry scrambled a type specimen book text of the dummy text of the printing printing and typesetting industry scrambled dummy text of the printing.
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-config"><i class="fa fa-cog"></i>Configure</a>
		<a href="#" class="essb-btn tile-activate"><i class="fa fa-check"></i>Activate</a>
		<a href="#" class="essb-btn tile-deactivate"><i class="fa fa-close"></i>Deactivate</a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile running">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>This is portlet title</h3>
		</div>
		<div class="advancedoptions-tile-head-tools">
			<span class="status running">Running</span>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>This is portlet title</h3>
		</div>
		<div class="advancedoptions-tile-head-tools">
			<div class="onoffswitch">
			<input id="essb_field_conversions_lite1_run" type="checkbox" name="essb_options[conversions_lite_run]" class="onoffswitch-checkbox" value="true">
			<label class="onoffswitch-label" for="essb_field_conversions_lite1_run">
				<span class="onoffswitch-inner"></span>
				<span class="onoffswitch-switch"></span>
			</label>
			</div>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	Lorem Ipsum is simply dummy text of the printing and typesetting industry scrambled it to make text of the printing and typesetting industry scrambled a type specimen book text of the dummy text of the printing printing and typesetting industry scrambled dummy text of the printing.
	</div>
</div>

<div class="row">
<div class="advancedoptions-tile advancedoptions-panel">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>Generate image size tags</h3>
		</div>
		<div class="advancedoptions-tile-head-tools">
			<div class="onoffswitch">
			<input id="essb_field_conversions_lite1_run" type="checkbox" name="essb_options[conversions_lite_run]" class="onoffswitch-checkbox" value="true">
			<label class="onoffswitch-label" for="essb_field_conversions_lite1_run">
				<span class="onoffswitch-inner"></span>
				<span class="onoffswitch-switch"></span>
			</label>
			</div>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	Image size tags are not required - they are optional but in some cases without them Facebook may not recongnize the correct image. In case this happens to you try activating this option.
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-panel running">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>GIF images support</h3>
		</div>
		<div class="advancedoptions-tile-head-tools">
			<div class="onoffswitch">
			<input id="essb_field_conversions_lite1_run" type="checkbox" name="essb_options[conversions_lite_run]" class="onoffswitch-checkbox" value="true">
			<label class="onoffswitch-label" for="essb_field_conversions_lite1_run">
				<span class="onoffswitch-inner"></span>
				<span class="onoffswitch-switch"></span>
			</label>
			</div>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	Set this to Yes if your site uses GIF images as featured or optimized for sharing and you wish to see that appearing inside Facebook sharing
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-panel">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>Activate analytics and collect data for click over buttons</h3>
		</div>
		<div class="advancedoptions-tile-head-tools">
			<div class="onoffswitch">
			<input id="essb_field_conversions_lite1_run" type="checkbox" name="essb_options[conversions_lite_run]" class="onoffswitch-checkbox" value="true">
			<label class="onoffswitch-label" for="essb_field_conversions_lite1_run">
				<span class="onoffswitch-inner"></span>
				<span class="onoffswitch-switch"></span>
			</label>
			</div>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	Build-in analytics is exteremly powerful tool which will let you to track how your visitors interact with share buttons. Get reports by positions, device type, social networks, for periods or for content
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-panel">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>Generate image size tags</h3>
		</div>
		<div class="advancedoptions-tile-head-tools">
			
		</div>	
	</div>
	<div class="advancedoptions-tile-control">
	<select name="essb_options[esml_history]" class="input-element" id="essb_options_esml_history"><option value="1">1 day</option><option value="7">1 week</option><option value="14">2 weeks</option><option value="30">1 month</option></select>
	</div>
	<div class="advancedoptions-tile-body">
	Image size tags are not required - they are optional but in some cases without them Facebook may not recongnize the correct image. In case this happens to you try activating this option.
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-panel">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>Generate image size tags</h3>
		</div>
		<div class="advancedoptions-tile-head-tools">
			<div class="onoffswitch">
			<input id="essb_field_conversions_lite1_run" type="checkbox" name="essb_options[conversions_lite_run]" class="onoffswitch-checkbox" value="true">
			<label class="onoffswitch-label" for="essb_field_conversions_lite1_run">
				<span class="onoffswitch-inner"></span>
				<span class="onoffswitch-switch"></span>
			</label>
			</div>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	Image size tags are not required - they are optional but in some cases without them Facebook may not recongnize the correct image. In case this happens to you try activating this option.
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-panel">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>Generate image size tags</h3>
		</div>
		<div class="advancedoptions-tile-head-tools">
			<div class="onoffswitch">
			<input id="essb_field_conversions_lite1_run" type="checkbox" name="essb_options[conversions_lite_run]" class="onoffswitch-checkbox" value="true">
			<label class="onoffswitch-label" for="essb_field_conversions_lite1_run">
				<span class="onoffswitch-inner"></span>
				<span class="onoffswitch-switch"></span>
			</label>
			</div>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	Image size tags are not required - they are optional but in some cases without them Facebook may not recongnize the correct image. In case this happens to you try activating this option.
	</div>
</div>

</div>



</div>

<div class="wrap essb-settings-wrap">
<div class="advancedoptions-tile">
	<div class="advancedoptions-tile-head">
		<div class="advancedoptions-tile-head-title">
			<h3>This is portlet title</h3>
		</div>	
		<div class="advancedoptions-tile-head-tools">
			<span class="status notrunning">Inactive</span>
		</div>	
	</div>
	<div class="advancedoptions-tile-body">
	Lorem Ipsum is simply dummy text of the printing and typesetting industry scrambled it to make text of the printing and typesetting industry scrambled a type specimen book text of the dummy text of the printing printing and typesetting industry scrambled dummy text of the printing.
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-config"><i class="fa fa-cog"></i>Configure</a>
		<a href="#" class="essb-btn tile-activate"><i class="fa fa-check"></i>Activate</a>
		<a href="#" class="essb-btn tile-deactivate"><i class="fa fa-close"></i>Deactivate</a>
	</div>
</div>
</div>