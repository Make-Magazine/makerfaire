<?php

namespace NinjaTablesPro\App\Modules\DataProviders;

use NinjaTablesPro\App\Traits\WPPostDataSourceTrait;
use NinjaTables\Framework\Support\Arr;

class WPPostsProvider
{
    use WPPostDataSourceTrait;

    public function boot()
    {
        add_filter('ninja_tables_get_table_wp-posts', array($this, 'getTableSettings'));
        add_filter('ninja_tables_get_table_data_wp-posts', array($this, 'getTableData'), 10, 4);
        add_filter('ninja_tables_fetching_table_rows_wp-posts', array($this, 'data'), 10, 5);
        add_action('wp_ajax_ninja_table_wp-posts_create_table', array($this, 'createTable'));
        add_action('wp_ajax_ninja_table_wp-posts_get_custom_field_options', array($this, 'getTableDataOptions'));

        add_filter('ninja_table_activated_features', function ($features) {
            $features['wp_posts_table'] = true;

            return $features;
        });
    }

    public function createTable()
    {
        if (!current_user_can(ninja_table_admin_role())) {
            return;
        }
        ninjaTablesValidateNonce();
        $messages = array();

        $tableId = intval(Arr::get($_REQUEST, 'tableId'));

        if (!$tableId) {
            if (empty(sanitize_text_field(Arr::get($_REQUEST, 'post_title', '')))) {
                $messages['title'] = __('The title field is required.', 'ninja-tables-pro');
            }
        }

        $fields = ninja_tables_sanitize_array(Arr::get($_REQUEST, 'data.columns', []));
        $fields = array_filter($fields);

        if (!$fields) {
            $messages['columns'] = __('No columns were selected.', 'ninja-tables-pro');
        }

        if (array_filter($messages)) {
            wp_send_json_error(array('message' => $messages), 422);
            wp_die();
        }

        if ($tableId) {
            $oldColumns = get_post_meta($tableId, '_ninja_table_columns', true);

            $oldColumnOriginalNames = array_filter(
                array_map(function ($col) {
                    return $col['original_name'];
                }, $oldColumns)
            );

            $oldColumns = array_filter($oldColumns, function ($col) use ($fields) {
                return in_array($col['original_name'], $fields) || array_key_exists('wp_post_custom_data_type', $col);
            });

            $fields = array_diff($fields, $oldColumnOriginalNames);
        }

        $headers = ninja_table_format_header($fields);

        $columns = array();
        foreach ($headers as $key => $column) {
            $dataType   = $this->getType($column);
            $sourceType = $this->getSourceType($column);
            $columnData = array(
                'name'                => $this->getHumanName($column),
                'key'                 => $key,
                'breakpoints'         => '',
                'data_type'           => $dataType,
                'dateFormat'          => ($dataType == 'date') ? 'YYYY-MM-DD' : null,
                'header_html_content' => null,
                'enable_html_content' => false,
                'contentAlign'        => null,
                'textAlign'           => null,
                'source_type'         => $sourceType,
                'original_name'       => $column
            );
            if ($sourceType == 'post_data') {
                $columnData['permalinked'] = in_array($column, ['post_title', 'ID', 'post_author']) ? 'yes' : 'no';
                if ($column === 'post_author') {
                    $columnData['filter_permalinked'] = 'yes';
                }
            } elseif ($sourceType == 'tax_data') {
                $columnData['permalinked']        = 'yes';
                $columnData['filter_permalinked'] = 'yes';
                $columnData['taxonomy_separator'] = ', ';
            }

            $columns[] = $columnData;
        }

        if ($tableId) {
            $columns = array_merge($oldColumns, $columns);
            $message = 'Table updated successfully.';
        } else {
            $tableId = $this->saveTable();
            update_post_meta($tableId, '_ninja_wp_posts_query_extra', $this->getQueryExtra($tableId));
            $message = 'Table created successfully.';
        }

        update_post_meta(
            $tableId,
            '_ninja_table_wpposts_ds_post_types',
            ninja_tables_sanitize_array(Arr::get($_REQUEST, 'data.post_types'))
        );
        update_post_meta(
            $tableId,
            '_ninja_table_wpposts_ds_where',
            ninja_tables_sanitize_array(Arr::get($_REQUEST, 'data.where', []))
        );

        update_post_meta($tableId, '_ninja_table_wpposts_ds_meta_query', ninja_tables_sanitize_array(Arr::get($_REQUEST, 'data.metas', [])));
        update_post_meta($tableId, '_ninja_table_columns', $columns);
        update_post_meta($tableId, '_ninja_tables_data_provider', 'wp-posts');

        if (isset($_REQUEST['data']['current_user_posts'])) {
            if (!is_array($_REQUEST['data']['query_extra'])) {
                $_REQUEST['data']['query_extra'] = array();
            }
            $_REQUEST['data']['query_extra']['current_user_posts'] = sanitize_text_field(
                Arr::get($_REQUEST, 'data.current_user_posts')
            );
        }

        if (isset($_REQUEST['data']['query_extra'])) {
            update_post_meta(
                $tableId,
                '_ninja_wp_posts_query_extra',
                ninja_tables_sanitize_array(Arr::get($_REQUEST, 'data.query_extra'))
            );
        }

        wp_send_json_success(array('table_id' => $tableId, 'message' => $message), 200);
    }

    public function getTableSettings($table)
    {
        $table->isEditable        = false;
        $table->dataSourceType    = 'wp-posts';
        $table->whereConditions   = get_post_meta($table->ID, '_ninja_table_wpposts_ds_where', true);
        $table->metaQuery         = get_post_meta($table->ID, '_ninja_table_wpposts_ds_meta_query', true);
        $table->post_types        = get_post_meta($table->ID, '_ninja_table_wpposts_ds_post_types', true);
        $table->isEditableMessage = 'You may edit your table settings here.';

        $table->isExportable      = true;
        $table->isImportable      = false;
        $table->isSortable        = false;
        $table->isCreatedSortable = false;
        $table->hasCacheFeature   = false;
        $table->query_extra       = $this->getQueryExtra($table->ID);

        return $table;
    }

    public function getTableData($data, $tableId, $perPage = -1, $offset = 0)
    {
        if ($perPage === -1) {
            $queryExtra = $this->getQueryExtra($tableId);
            if (!empty($queryExtra['query_limit'])) {
                $perPage = intval($queryExtra['query_limit']);
            }
        }

        $newData       = array();
        $posts         = $this->getPosts($tableId);
        $total         = count($posts);
        $responsePosts = array_slice($posts, $offset, $perPage);
        foreach ($responsePosts as $key => $post) {
            $newData[] = array(
                'id'       => $key + 1,
                'values'   => $post,
                'position' => $key + 1,
            );
        }

        return array($newData, $total);
    }

    public function data($data, $tableId, $defaultSorting, $limitEntries = false, $skip = false)
    {
        global $ninja_table_current_rendering_table;

        $perPage    = -1;
        $queryExtra = $this->getQueryExtra($tableId);
        if ($limitEntries) {
            $perPage = $limitEntries;
        } elseif (!empty(Arr::get($queryExtra, 'query_limit'))) {
            $perPage = intval($queryExtra['query_limit']);
        }

        return $this->getPosts($tableId, $perPage, $skip);
    }

    public function getPosts($tableId, $per_page = -1, $offset = 0)
    {
        $columns           = get_post_meta($tableId, '_ninja_table_columns', true);
        $formatted_columns = array();
        foreach ($columns as $column) {
            $type         = $this->get($column, 'source_type');
            $originalName = $this->get($column, 'original_name');
            $columnKey    = $this->get($column, 'key');
            $dataType     = $this->get($column, 'wp_post_custom_data_type');
            $dataValue    = $this->get($column, 'wp_post_custom_data_value');

            $formatted_columns[$columnKey] = array(
                'type'                      => ($originalName == 'post_author') ? 'author_data' : $type,
                'original_name'             => $originalName,
                'key'                       => $columnKey,
                'permalinked'               => $this->get($column, 'permalinked'),
                'permalink_target'          => $this->get($column, 'permalink_target'),
                'filter_permalinked'        => $this->get($column, 'filter_permalinked'),
                'taxonomy_separator'        => $this->get($column, 'taxonomy_separator'),
                'wp_post_custom_data_type'  => $dataType,
                'wp_post_custom_data_value' => $dataValue,
                'column_settings'           => $column
            );
        }

        $where = get_post_meta($tableId, '_ninja_table_wpposts_ds_where', true);

        $metas = get_post_meta($tableId, '_ninja_table_wpposts_ds_meta_query', true);

        $post_types = get_post_meta($tableId, '_ninja_table_wpposts_ds_post_types', true);

        return $this->buildWPQuery(
            compact('tableId', 'formatted_columns', 'where', 'post_types', 'offset', 'per_page', 'metas')
        );
    }

    protected function saveTable($postId = null)
    {
        $attributes = array(
            'post_title'   => sanitize_text_field(Arr::get($_REQUEST, 'post_title')),
            'post_content' => wp_kses_post(Arr::get($_REQUEST, 'post_content')),
            'post_type'    => 'ninja-table',
            'post_status'  => 'publish',
        );

        if (!$postId) {
            $postId = wp_insert_post($attributes);
        } else {
            $attributes['ID'] = $postId;
            wp_update_post($attributes);
        }

        return $postId;
    }

    public function getTableDataOptions()
    {
        ninjaTablesValidateNonce();
        wp_send_json_success([
            'custom_fields' => $this->getPostDynamicColumnAtrributes()
        ]);
    }
}
