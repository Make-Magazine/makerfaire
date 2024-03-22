<select name="<?php echo esc_attr($html_name); ?>">
    <option value=""><?php _e('Select', 'wp_all_import_plugin'); ?></option>
    <?php foreach ($field['choices'] as $choice) : ?>
        <option value="<?php echo $choice['value']; ?>" <?php echo $field_value && $choice['value'] == $field_value ? 'selected="selected"' : ''; ?>>
            <?php echo $choice['label']; ?>
        </option>
    <?php endforeach; ?>
</select>