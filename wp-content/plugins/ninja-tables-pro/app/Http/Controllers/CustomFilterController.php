<?php

namespace NinjaTablesPro\App\Http\Controllers;

use NinjaTables\Framework\Support\Arr;
use NinjaTablesPro\app\Traits\CustomFilter;

class CustomFilterController extends Controller
{
    use CustomFilter;

    public function index()
    {
        $tableId          = intval(Arr::get($_REQUEST, 'table_id'));
        $table_filters    = $this->getCustomFilters($tableId, array());
        $formattedFilters = array();

        foreach ($table_filters as $key => $table_filter) {
            $table_filter['name'] = $key;
            $formattedFilters[]   = $table_filter;
        }

        $filterStyling = get_post_meta($tableId, '_ninja_custom_filter_styling', true);

        if (!$filterStyling) {
            $filterStyling = array();
        }

        $defaultStyling = array(
            'filter_display_type' => 'inline',
            'filter_columns'      => 'columns_2',
            'filter_column_label' => 'new_line',
            'progressive'         => 'no'
        );

        $filterStyling = wp_parse_args($filterStyling, $defaultStyling);

        wp_send_json_success(array(
            'table_filters'  => $formattedFilters,
            'filter_styling' => $filterStyling
        ));
    }

    public function store()
    {
        $tableId = intval(Arr::get($_REQUEST, 'table_id'));
        $filters = wp_unslash($this->sanitizeCustomFilterData(Arr::get($_REQUEST, 'ninja_filters', [])));
        $this->updateFilters($tableId, $filters);

        if (isset($_REQUEST['filter_styling'])) {
            $filterAppearance = wp_unslash(ninja_tables_sanitize_array(Arr::get($_REQUEST, 'filter_styling')));
            update_post_meta($tableId, '_ninja_custom_filter_styling', $filterAppearance);
        }

        if (isset($_REQUEST['table_buttons'])) {
            $tableButtons = wp_unslash(ninja_tables_sanitize_array(Arr::get($_REQUEST, 'table_buttons')));
            update_post_meta($tableId, '_ninja_custom_table_buttons', $tableButtons);
        }

        wp_send_json_success(array(
            'message' => __('Filters successfully updated', 'ninja_table_pro')
        ));
    }

    private function sanitizeCustomFilterData($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->sanitizeCustomFilterData($value);
            } else {
                $array[$key] = sanitize_text_field($value);
            }
        }

        return $array;
    }
}
