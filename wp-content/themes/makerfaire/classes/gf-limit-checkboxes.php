<?php

/**
* Limit How Many Checkboxes Can Be Checked
* http://gravitywiz.com/2012/06/11/limiting-how-many-checkboxes-can-be-checked/
*/

class GFLimitCheckboxes {

    private $form_id;
    private $field_limits;
    private $output_script;

    function __construct($form_id, $field_limits) {

        $this->form_id = $form_id;
        $this->field_limits = $this->set_field_limits($field_limits);

        add_filter("gform_pre_render", array(&$this, 'pre_render'));
        add_filter("gform_validation", array(&$this, 'validate'));

    }

    function pre_render($form) {

        $script = '';
        $output_script = false;

        foreach($form['fields'] as $field) {

            $field_id = $field['id'];
            $field_limits = $this->get_field_limits($field['id']);

            if( !$field_limits                                          // if field limits not provided for this field
                || RGFormsModel::get_input_type($field) != 'checkbox'   // or if this field is not a checkbox
                || !isset($field_limits['max'])        // or if 'max' is not set for this field
                )
                continue;

            $output_script = true;
            $max = $field_limits['max'];
            $selectors = array();

            foreach($field_limits['field'] as $checkbox_field) {
                $selectors[] = "#field_{$form['id']}_{$checkbox_field} .gfield_checkbox input:checkbox";
            }

            $script .= "jQuery(\"" . implode(', ', $selectors) . "\").checkboxLimit({$max});";

        }

        if($output_script):
          GFFormDisplay::add_init_script($form['id'], 'limit_checkboxes', GFFormDisplay::ON_PAGE_RENDER, $script);

            ?>

            <script type="text/javascript">
            jQuery(document).ready(function($) {
                $.fn.checkboxLimit = function(n) {

                    var checkboxes = this;

                    this.toggleDisable = function() {

                        // if we have reached or exceeded the limit, disable all other checkboxes
                        if(this.filter(':checked').length >= n) {
                            var unchecked = this.not(':checked');
                            unchecked.prop('disabled', true);
                        }
                        // if we are below the limit, make sure all checkboxes are available
                        else {
                            this.prop('disabled', false);
                        }

                    }

                    // when form is rendered, toggle disable
                    checkboxes.bind('gform_post_render', checkboxes.toggleDisable());

                    // when checkbox is clicked, toggle disable
                    checkboxes.click(function(event) {

                        checkboxes.toggleDisable();

                        // if we are equal to or below the limit, the field should be checked
                        return checkboxes.filter(':checked').length <= n;
                    });

                }
            });
            </script>

            <?php
        endif;

        return $form;
    }

    function validate($validation_result) {

        $form = $validation_result['form'];
        $checkbox_counts = array();

        // loop through and get counts on all checkbox fields (just to keep things simple)
        foreach($form['fields'] as $field) {

            if( RGFormsModel::get_input_type($field) != 'checkbox' )
                continue;

            $field_id = $field['id'];
            $count = 0;

            foreach($_POST as $key => $value) {
                if(strpos($key, "input_{$field['id']}_") !== false)
                    $count++;
            }

            $checkbox_counts[$field_id] = $count;

        }

        // loop through again and actually validate
        foreach($form['fields'] as &$field) {

            if(!$this->should_field_be_validated($form, $field))
                continue;

            $field_id = $field['id'];
            $field_limits = $this->get_field_limits($field_id);

            $min = isset($field_limits['min']) ? $field_limits['min'] : false;
            $max = isset($field_limits['max']) ? $field_limits['max'] : false;

            $count = 0;
            foreach($field_limits['field'] as $checkbox_field) {
                $count += rgar($checkbox_counts, $checkbox_field);
            }

            if($count < $min) {
                $field['failed_validation'] = true;
                $field['validation_message'] = sprintf( _n('You must select at least %s item.', 'You must select at least %s items.', $min), $min );
                $validation_result['is_valid'] = false;
            }
            else if($count > $max) {
                $field['failed_validation'] = true;
                $field['validation_message'] = sprintf( _n('You may only select %s item.', 'You may only select %s items.', $max), $max );
                $validation_result['is_valid'] = false;
            }

        }

        $validation_result['form'] = $form;

        return $validation_result;
    }

    function should_field_be_validated($form, $field) {

        if( $field['pageNumber'] != GFFormDisplay::get_source_page( $form['id'] ) )
    		return false;

        // if no limits provided for this field
        if( !$this->get_field_limits($field['id']) )
            return false;

        // or if this field is not a checkbox
        if( RGFormsModel::get_input_type($field) != 'checkbox' )
            return false;

        // or if this field is hidden
        if( RGFormsModel::is_field_hidden($form, $field, array()) )
            return false;

        return true;
    }

    function get_field_limits($field_id) {

        foreach($this->field_limits as $key => $options) {
            if(in_array($field_id, $options['field']))
                return $options;
        }

        return false;
    }

    function set_field_limits($field_limits) {

        foreach($field_limits as $key => &$options) {

            if(isset($options['field'])) {
                $ids = is_array($options['field']) ? $options['field'] : array($options['field']);
            } else {
                $ids = array($key);
            }

            $options['field'] = $ids;

        }

        return $field_limits;
    }

}

/*
 * array to limit checkboxes.
 *
 * to add a new limit, add a new array to limitArray array(fieldID,max#, array of formID's)
 *
 */
$limitArray = array(
                array(321,4,array(9, 43, 25, 26, 27, 28, 29, 30, 31)), //limit additional categories field to 4
                array(147,5,array(9, 10, 20, 12, 13, 14, 15, 16, 17, 18))
              );

foreach($limitArray as $limit){
  $field = $limit[0];
  $max   = $limit[1];
  //limits are for all forms
  new GFLimitCheckboxes(9, array(
      $field => array('max' => $max)
  ));
  /*foreach($limit[2] as $form){
    new GFLimitCheckboxes($form, array(
      $field => array('max' => $max)
    ));
  }*/
}


