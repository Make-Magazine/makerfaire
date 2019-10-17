<?php
/**
 * @package GravityView_Maps\templates
 * @since TODO
 * @see GravityView_Maps_InfoWindow::prepare_template
 * @global array $infobox_content array with keys: 'img', 'title', 'img_src', 'container_class', 'link_atts', 'content'
 */
?>
<div class="gv-infowindow-container [[container_class]]">
	[[img]]
	<div class="gv-infowindow-content">
		<h4><a href="[[entry_url]]" [[link_atts]]>[[title]]</a></h4>
		[[content]]
	</div>
</div>