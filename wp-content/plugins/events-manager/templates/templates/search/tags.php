<?php
	/* @var $args array */
	$include = !empty($args['tags_include']) ? $args['tags_include']:array();
	$exclude = !empty($args['tags_exclude']) ? $args['tags_exclude']:array();
?>
<!-- START Tag Search -->
<div class="em-search-tag em-search-field">
	<label for="em-search-tag-<?php echo absint($args['id']) ?>" class="screen-reader-text"><?php echo esc_html($args['tag_label']); ?></label>

	<select name="tag[]" class="em-search-tag em-selectize always-open checkboxes" id="em-search-tag-<?php echo absint($args['id']) ?>" multiple size="10" placeholder="<?php echo esc_html($args['tags_placeholder']); ?>">
		<?php
		$args_em = apply_filters('em_advanced_search_tags_args', array('orderby'=>'name','hide_empty'=>0, 'include' => $include, 'exclude' => $exclude ));
		$tags = EM_Tags::get($args_em);
		$selected = array();
		if( !empty($args['tag']) ){
			if( !is_array($args['tag']) ){
				$selected = explode(',', $args['tag']);
			} else {
				$selected = $args['tag'];
			}
		}
		$walker = new EM_Walker_CategoryMultiselect();
		$args_em = apply_filters('em_advanced_search_tags_walker_args', array(
		    'hide_empty' => 0,
		    'orderby' =>'name',
		    'name' => 'tag',
		    'hierarchical' => true,
		    'taxonomy' => EM_TAXONOMY_TAG,
		    'selected' => $selected,
		    'show_option_none' => $args['tags_label'],
		    'option_none_value'=> 0,
			'walker'=> $walker
		));
		echo walk_category_dropdown_tree($tags, 0, $args_em);
		?>
	</select>
</div>
<!-- END Tag Search -->