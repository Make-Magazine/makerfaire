<div class="essb-shortcode-list">
	<div class="heading">
		<h2><?php esc_html_e('List of All Available Shortcodes', 'essb'); ?></h2>
		<div class="essb-btn essb-open-shortcodes"><?php esc_html_e('Open Shortcode Generator', 'essb'); ?></div>
	</div>
	
<?php 

/**
 * Shortcode list with all options in plugin
 */

if (class_exists('ESSBControlCenterShortcodes')) {
	
	foreach (ESSBControlCenterShortcodes::$shortcodes as $code => $options) {
		echo '<div class="shortcode-block">';
		
		echo '<h3>'.$options['title'].'</h3>';
		
		echo '<code>['.$code.']</code>';
		
		echo '<div class="shortcode-state"><i class="fa fa-chevron-right"></i></div>';
		
		echo '<div class="shortcode-args">';
		echo '<h4>'.esc_html__('Args', 'essb').'</h4>';
		
		echo '<table>';
		echo '<thead>';
		echo '<tr>';
		echo '<th>'.esc_html__('Argument', 'essb').'</th>';
		echo '<th>'.esc_html__('Description', 'essb').'</th>';
		echo '</tr>';
		echo '</thead>';
		
		echo '<tbody>';
		
		$default_options = ESSBControlCenterShortcodes::$shortcode_options[$code];
			
		foreach ($default_options as $key => $setup) {
			$type = $setup['type'];
			$title = isset($setup['title']) ? $setup['title'] : '';
			$description = isset($setup['description']) ? $setup['description'] : '';
			$options = isset($setup['options']) ? $setup['options'] : array();
			
			if ($type == 'section-open' || $type == 'section-close' || $type == 'separator' || $type == 'separator-small') {
				continue;
			}
			
			echo '<tr>';
			echo '<td>'.$key.'</td>';
			echo '<td>';
			echo '<span class="title">'.$title.'</span>';
			if ($description != '') {
				echo '<span class="description">'.$description.'</span>';
			}
			
			if ($type == 'checkbox') {
				echo '<span class="values">';
				echo 'Possible values';
				echo '<ul>';
				echo '<li><code>no</code></li>';
				echo '<li><code>yes</code></li>';
				echo '</ul>';
				echo '</span>';
			}
			
			if ($type == 'checkbox-true') {
				echo '<span class="values">';
				echo 'Possible values';
				echo '<ul>';
				echo '<li><code>false</code></li>';
				echo '<li><code>true</code></li>';
				echo '</ul>';
				echo '</span>';
			}
			
			if ($type == 'select') {
				echo '<span class="values">';
				echo 'Possible values';
				echo '<ul>';

				foreach ($options as $opt_key => $opt_value) {
					echo '<li><code>'.$opt_key.'</code>'.' - '.$opt_value.'</li>';
				}
				
				echo '</ul>';
				echo '</span>';
			}
			
			echo '</td>';
			echo '</tr>';
		}
		
		echo '</tbody>';
		
		echo '</table>';
		
		
		echo '</div>';
		
		echo '</div>';
	}
}

?>

</div>