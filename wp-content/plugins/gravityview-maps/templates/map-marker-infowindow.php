<?php
/**
 * @package GravityView_Maps\templates
 * @since 1.4
 * @see GravityView_Maps_InfoWindow::prepare_template
 * @global array $infobox_content array with keys: 'img', 'title', 'img_src', 'container_class', 'link_atts', 'content'
 */
?>
<div class="gv-infowindow-container [[container_class]]">
	[[img]]
	<div class="gv-infowindow-content">
		<h4>[[link_open]][[title]][[link_close]]</h4>
		[[content]]
	</div>
</div>
