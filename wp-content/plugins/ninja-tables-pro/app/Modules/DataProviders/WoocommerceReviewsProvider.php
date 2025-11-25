<?php

namespace NinjaTablesPro\App\Modules\DataProviders;

use NinjaTables\Framework\Support\Arr;
use NinjaTables\Framework\Foundation\App;
use NinjaTablesPro\App\Traits\WooDataSourceTrait;

class WoocommerceReviewsProvider
{
    use WooDataSourceTrait;

    public function boot()
    {
        if ( ! defined('WC_PLUGIN_FILE')) {
            return;
        }

        add_filter('ninja_table_activated_features', function ($features) {
            $features['woocommerce_table'] = true;

            return $features;
        });

        add_filter('ninja_tables_get_table_wp_woo_reviews', array($this, 'getReviewsTableSettings'));
        add_filter('ninja_tables_get_table_data_wp_woo_reviews', array($this, 'getReviewsTableData'), 10, 4);
        add_filter('ninja_tables_fetching_table_rows_wp_woo_reviews', array($this, 'getReviewsData'), 10, 5);

        add_action('wp_ajax_ninja_table_woocommerece_create_reviews_table', array($this, 'createReviewsTable'));
        add_action('wp_ajax_ninja_table_woocommerece_get_reviews_options', array($this, 'getWooReviewsSettings'));
        add_action('wp_ajax_ninja_table_save_query_settings_woo_reviews_table', array($this, 'saveQuerySettings'));
    }

    public function createReviewsTable()
    {
        if ( ! current_user_can(ninja_table_admin_role())) {
            return;
        }
        ninjaTablesValidateNonce();

        $messages = [];

        $request = App::getInstance('request');

        $post_title = sanitize_text_field(Arr::get($request->all(), 'post_title', ''));
        if ($post_title === '') {
            $messages['title'] = __('The title field is required.', 'ninja-tables-pro');
        }
        if (array_filter($messages)) {
            wp_send_json_error(array('message' => $messages), 422);
            wp_die();
        }

        $initialHeaders = [
            [
                'name'                            => 'Image',
                'key'                             => 'product_image',
                'breakpoints'                     => '',
                'data_type'                       => 'html',
                'width'                           => '100',
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'permalinked'                     => "yes",
                'source_type'                     => "review_data",
                'wp_post_custom_data_source_type' => 'product_image',
                'wp_post_custom_data_key'         => 'shop_thumbnail'
            ],
            [
                'name'                            => 'Title',
                'key'                             => 'product_title',
                'breakpoints'                     => '',
                'data_type'                       => 'text',
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'permalinked'                     => "yes",
                'source_type'                     => "review_data",
                'wp_post_custom_data_source_type' => 'product_title',
                'wp_post_custom_data_key'         => 'product_title'
            ],
            [
                'name'                            => 'Rating',
                'key'                             => 'rating',
                'breakpoints'                     => '',
                'data_type'                       => 'html',
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'permalinked'                     => "no",
                'source_type'                     => "review_data",
                'wp_post_custom_data_source_type' => 'rating',
                'wp_post_custom_data_key'         => 'rating'
            ],
            [
                'name'                            => 'Review',
                'key'                             => 'review_content',
                'breakpoints'                     => '',
                'data_type'                       => 'text',
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'permalinked'                     => "no",
                'source_type'                     => "review_data",
                'wp_post_custom_data_source_type' => 'review_content',
                'wp_post_custom_data_key'         => 'comment_content'
            ],
            [
                'name'                            => 'Reviewer',
                'key'                             => 'reviewer',
                'breakpoints'                     => '',
                'data_type'                       => 'text',
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'permalinked'                     => "no",
                'source_type'                     => "review_data",
                'wp_post_custom_data_source_type' => 'reviewer',
                'wp_post_custom_data_key'         => 'comment_author'
            ]
        ];

        $tableId = $this->saveTable();

        $query_selections = ninja_tables_sanitize_array(Arr::get($request->all(), 'query_selections', []));
        $query_conditions = ninja_tables_sanitize_array(Arr::get($request->all(), 'query_conditions', []));

        update_post_meta($tableId, '_ninja_table_woo_reviews_query_selections', $query_selections);
        update_post_meta($tableId, '_ninja_table_woo_reviews_query_conditions', $query_conditions);

        $appearance_settings = [
            'filled_star_color' => '#f2c94c',
            'empty_star_color'  => '#e6e6e6'
        ];
        update_post_meta($tableId, '_ninja_table_woo_reviews_appearance_settings', $appearance_settings);

        update_post_meta($tableId, '_ninja_table_columns', $initialHeaders);
        update_post_meta($tableId, '_ninja_tables_data_provider', 'wp_woo_reviews');

        $message = 'Reviews table created successfully.';

        wp_send_json_success(array('table_id' => $tableId, 'message' => $message), 200);
    }


    protected function saveTable($postId = null)
    {
        $attributes = array(
            'post_title'  => sanitize_text_field(Arr::get($_REQUEST, 'post_title')),
            'post_type'   => 'ninja-table',
            'post_status' => 'publish'
        );

        if ( ! $postId) {
            $postId = wp_insert_post($attributes);
        } else {
            $attributes['ID'] = absint($postId);
            wp_update_post($attributes);
        }

        return $postId;
    }

    public function getReviewsTableSettings($table)
    {
        $table->dataSourceType    = 'wp_woo_reviews';
        $table->isEditableMessage = 'You may edit your table settings here.';

        $table->isEditable        = false;
        $table->isExportable      = true;
        $table->isImportable      = false;
        $table->isSortable        = false;
        $table->isCreatedSortable = false;
        $table->hasCacheFeature   = false;

        $querySelections = get_post_meta($table->ID, '_ninja_table_woo_reviews_query_selections', true);

        if ( ! $querySelections) {
            $querySelections = (object)[];
        }

        $queryConditions = get_post_meta($table->ID, '_ninja_table_woo_reviews_query_conditions', true);
        if ( ! $queryConditions) {
            $queryConditions = (object)[];
        }

        $appearanceSettings = get_post_meta($table->ID, '_ninja_table_woo_reviews_appearance_settings', true);
        if ( ! $appearanceSettings) {
            $appearanceSettings = (object)[];
        }

        $table->query_selections    = $querySelections;
        $table->query_conditions    = $queryConditions;
        $table->appearance_settings = $appearanceSettings;

        return $table;
    }

    public function getWooReviewsSettings()
    {
        if ( ! current_user_can(ninja_table_admin_role())) {
            return;
        }

        ninjaTablesValidateNonce();

        $quertTerms = $this->getWooReviewsQueryTerms();
        wp_send_json_success([
            'query_terms' => $quertTerms
        ], 200);
    }

    public function getWooReviewsQueryTerms()
    {
        $settings = [
            'product_cat'  => [
                'title'       => 'Select Products Reviews By Category',
                'description' => 'Select the categories from where you want to show the products reviews. Leave empty if you want to show from all categories',
                'terms'       => get_terms([
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                ])
            ],
            'product_tag'  => [
                'title'       => 'Select Products Reviews By Product Tags',
                'description' => 'Select the product tags from where you want to show the products reviews. Leave empty if you want to show from all tags',
                'terms'       => get_terms([
                    'taxonomy'   => 'product_tag',
                    'hide_empty' => false,
                ])
            ],
            'product_type' => [
                'title'       => 'Select Products Reviews By Product Type',
                'description' => 'Select the product types from where you want to show the products reviews. Leave empty if you want to show from all types',
                'terms'       => get_terms([
                    'taxonomy'   => 'product_type',
                    'hide_empty' => false,
                ])
            ]
        ];

        return apply_filters('ninja_table_woo_reviews_table_query_terms', $settings);
    }

    public function saveQuerySettings()
    {
        if ( ! current_user_can(ninja_table_admin_role())) {
            return;
        }

        ninjaTablesValidateNonce();

        $request = App::getInstance('request');

        $tableId          = intval(Arr::get($request->all(), 'table_id', 0));
        $query_selections = ninja_tables_sanitize_array(Arr::get($request->all(), 'query_selections', []));
        $query_conditions = ninja_tables_sanitize_array(Arr::get($request->all(), 'query_conditions', []));

        update_post_meta($tableId, '_ninja_table_woo_reviews_query_selections', $query_selections);
        update_post_meta($tableId, '_ninja_table_woo_reviews_query_conditions', $query_conditions);

        $appearance_settings = Arr::get($request->all(), 'appearance_settings', []);
        if ($appearance_settings) {
            update_post_meta($tableId, '_ninja_table_woo_reviews_appearance_settings', $appearance_settings);
        }

        wp_send_json_success([
            'message' => 'Settings successfully updated'
        ], 200);
    }

    public function getReviewsTableData($data, $tableId, $perPage = -1, $offset = 0)
    {
        if ($perPage == -1) {
            $queryExtra = $this->getQueryExtra($tableId);
            if (isset($queryExtra['query_limit']) && $queryExtra['query_limit']) {
                $perPage = intval($queryExtra['query_limit']);
            }
        }

       $reviews = $this->getReviews($tableId, $perPage, $offset);

        $formatted_reviews = array();
        if ($reviews && is_array($reviews)) {
            foreach ($reviews as $key => $review) {
                $formatted_reviews[] = [
                    'id'       => $key + 1,
                    'values'   => $review,
                    'position' => $key + 1,
                ];
            }
        }

        return array(
            $formatted_reviews,
            count($formatted_reviews)
        );
    }

    public function getReviewsData($data, $tableId, $defaultSorting, $limitEntries = false, $skip = false)
    {
        $perPage    = -1;
        $queryExtra = $this->getQueryExtra($tableId);
        if ($limitEntries) {
            $perPage = $limitEntries;
        }

        return $this->getReviews($tableId, $perPage, $skip);
    }

    public function getReviews($tableId, $per_page = -1, $offset = 0)
    {
        $columns = get_post_meta($tableId, '_ninja_table_columns', true);

        $formatted_columns = array();
        foreach ($columns as $column) {
            $type      = Arr::get($column, 'source_type');
            $columnKey = Arr::get($column, 'key');
            $dataType  = Arr::get($column, 'wp_post_custom_data_source_type');
            $dataValue = Arr::get($column, 'wp_post_custom_data_key');

            $formatted_columns[$columnKey] = array(
                'type'                            => $type,
                'key'                             => $columnKey,
                'permalinked'                     => Arr::get($column, 'permalinked'),
                'permalink_target'                => Arr::get($column, 'permalink_target'),
                'filter_permalinked'              => Arr::get($column, 'filter_permalinked'),
                'taxonomy_separator'              => Arr::get($column, 'taxonomy_separator'),
                'wp_post_custom_data_source_type' => $dataType,
                'wp_post_custom_data_key'         => $dataValue,
                'column_settings'                 => $column
            );
        }

        $query_selections = get_post_meta($tableId, '_ninja_table_woo_reviews_query_selections', true);
        $query_conditions = get_post_meta($tableId, '_ninja_table_woo_reviews_query_conditions', true);

        $product_ids = $this->getProductIdsFromSelections($query_selections);

        if ( ! is_array($product_ids)) {
            $product_ids = array();
        }

        $where = [];

        if (!empty($product_ids)) {
            $where[] = array(
                'field'    => 'comment.post_id',
                'operator' => 'IN',
                'value'    => $product_ids
            );
        }

        if (!empty($query_conditions['status'])) {
            if ($query_conditions['status'] === 'approved') {
                $where[] = array(
                    'field'    => 'comment.status',
                    'value'    => 'approve',
                    'operator' => '='
                );
            } elseif ($query_conditions['status'] === 'unapproved') {
                $where[] = array(
                    'field'    => 'comment.status',
                    'value'    => 'hold',
                    'operator' => '='
                );
            } else {
                $where[] = array(
                    'field'    => 'comment.status',
                    'value'    => array('approve', 'hold'),
                    'operator' => 'IN'
                );
            }
        }

        $order_query = [
            'orderby' => 'comment_date',
            'order'   => 'DESC'
        ];

        return $this->buildCommentsQuery(
            compact('tableId', 'formatted_columns', 'order_query', 'where', 'offset', 'per_page')
        );
    }

    private function buildCommentsQuery($args)
    {
        $tableId           = intval(Arr::get($args, 'tableId', 0));
        $formatted_columns = Arr::get($args, 'formatted_columns', []);
        $order_query       = Arr::get($args, 'order_query', []);
        $where             = Arr::get($args, 'where', []);
        $offset            = Arr::get($args, 'offset', 0);
        $per_page          = Arr::get($args, 'per_page', -1);

        $comment_args = [
            'type'       => 'review',
            'status'     => 'approve',
            'orderby'    => 'comment_date',
            'order'      => 'DESC',
            'meta_query' => []
        ];

       // where
        if (!empty($where)) {
            foreach ($where as $condition) {
                $field    = $condition['field'];
                $operator = $condition['operator'];
                $value    = $condition['value'];

                if ($field === 'comment.post_id') {
                    if ($operator === 'IN' && is_array($value)) {
                        $comment_args['post__in'] = $value;
                    }
                } elseif ($field === 'comment.status') {
                    if ($operator === '=') {
                        $comment_args['status'] = $value;
                    } elseif ($operator === 'IN' && is_array($value)) {
                        $comment_args['status'] = $value;
                    }
                }
            }
        }

        if (!empty($order_query['orderby'])) {
            $comment_args['orderby'] = $order_query['orderby'];
        }
        if (!empty($order_query['order'])) {
            $comment_args['order'] = $order_query['order'];
        }

        if ($per_page > 0) {
            $comment_args['number'] = $per_page;
            $comment_args['offset'] = $offset;
        }

        $comments_query = new \WP_Comment_Query($comment_args);
        $comments = $comments_query->comments;

        $formatted_reviews = [];

        if ($comments && is_array($comments)) {
            foreach ($comments as $comment) {
                $formatted_reviews[] = $this->formatReviewDataFromComment($comment, $formatted_columns, $tableId);
            }
        }

        return $formatted_reviews;
    }

    private function formatReviewDataFromComment($comment, $formatted_columns = [], $tableId = null)
    {
        $product = wc_get_product($comment->comment_post_ID);
        $rating = get_comment_meta($comment->comment_ID, 'rating', true);

        $data = [];

        if (empty($formatted_columns)) {
            return [];
        }

        // Dynamic column formatting
        foreach ($formatted_columns as $column_key => $column) {
            $type = Arr::get($column, 'type');

            if ($type == 'review_data') {
                $data[$column_key] = $this->getReviewData($comment, $product, $rating, $column, $tableId);
            } elseif ($type == 'post_data') {
                $post = $product ? get_post($product->get_id()) : null;
                $data[$column_key] = $this->getPostData($post, $column);
            } elseif ($type == 'tax_data') {
                $post = $product ? get_post($product->get_id()) : null;
                $data[$column_key] = $this->getTaxData($post, $column);
            } elseif ($type == 'custom') {
                $data[$column_key] = $this->getCustomReviewData($comment, $product, $column);
            } elseif ($type == 'author_data') {
                $post = $product ? get_post($product->get_id()) : null;
                $data[$column_key] = $this->getAuthorData($post, $column);
            } elseif ($type == 'product_data') {
                $data[$column_key] = $this->getProductData($product, $column);
            } elseif ($type == 'shortcode') {
                $value = Arr::get($column, 'column_settings.wp_post_custom_data_value', '');
                $post = $product ? get_post($product->get_id()) : null;
                $codes = $this->getShortCodes($value, $post);
                if ($codes) {
                    $value = str_replace(array_keys($codes), array_values($codes), $value);
                    $value = do_shortcode($value);
                } else {
                    $value = do_shortcode($value);
                }
                $data[$column_key] = $value;
            } else {
                $data[$column_key] = '';
            }
        }

        return $data;
    }

    private function getReviewData($comment, $product, $rating, $column, $tableId = 0)
    {
        $dataType = Arr::get($column, 'wp_post_custom_data_source_type');

        if ($dataType == 'product_image') {
            return $this->getProductImageHtml($product);
        } elseif ($dataType == 'product_title') {
            return $this->getProductTitleHtml($product, $product ? $product->get_name() : '');
        } elseif ($dataType == 'rating') {
            return $this->getRatingHtml($rating, $tableId);
        } elseif ($dataType == 'review_content') {
            return wp_kses_post($comment->comment_content);
        } elseif ($dataType == 'reviewer') {
            return sanitize_text_field($comment->comment_author);
        } else {
            return '';
        }
    }

    private function getCustomReviewData($comment, $product, $column)
    {
        $type  = Arr::get($column, 'wp_post_custom_data_source_type');
        $value = Arr::get($column, 'wp_post_custom_data_key');

        if (!$value) {
            return '';
        }

        if ($type == 'comment_meta') {
            return get_comment_meta($comment->comment_ID, $value, true);
        } elseif ($type == 'post_meta' && $product) {
            return get_post_meta($product->get_id(), $value, true);
        } elseif ($type == 'acf_field' && function_exists('get_field')) {
            return get_field($value, $product->get_id());
        } elseif ($type == 'product_acf_field' && function_exists('get_field') && $product) {
            return get_field($value, $product->get_id());
        } elseif ($type == 'featured_image' && $product) {
            return $this->getFeaturedImage(get_post($product->get_id()), $column);
        }

        return $value;
    }

    private function getProductIdsFromSelections($query_selections)
    {
        $product_ids = [];

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids'
        ];


        if (empty($query_selections)) {
            $product_query = new \WP_Query($args);
            $product_ids = $product_query->posts;
        } else {
            $tax_queries = [];

            if ( ! empty(Arr::get($query_selections, 'product_cat', []))) {
                $tax_queries[] = [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => Arr::get($query_selections, 'product_cat', [])
                ];
            }

            if ( ! empty(Arr::get($query_selections, 'product_tag'))) {
                $tax_queries[] = [
                    'taxonomy' => 'product_tag',
                    'field'    => 'slug',
                    'terms'    => Arr::get($query_selections, 'product_tag', [])
                ];
            }

            if ( ! empty($tax_queries)) {
                if (count($tax_queries) > 1) {
                    $args['tax_query'] = [
                        'relation' => 'AND'
                    ];
                    $args['tax_query'] = array_merge($args['tax_query'], $tax_queries);
                } else {
                    $args['tax_query'] = $tax_queries;
                }
            }

            if ( ! empty($query_selections['product_type'])) {
                $args['meta_query'] = [
                    [
                        'key'     => '_product_type',
                        'value'   => $query_selections['product_type'],
                        'compare' => 'IN'
                    ]
                ];
            }

            $product_query = new \WP_Query($args);
            $product_ids   = $product_query->posts;

            if ( ! is_array($product_ids)) {
                $product_ids = [];
            }
        }

        return $product_ids;
    }

    private function getProductImageHtml($product)
    {
        if ( ! $product) {
            return '';
        }

        $image_id = $product->get_image_id();
        if ( ! $image_id) {
            return '';
        }

        $image_url = wp_get_attachment_image_url($image_id, 'shop_thumbnail');
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

        return sprintf(
            '<a href="%s"><img src="%s" alt="%s" class="nt_image_type_thumb" /></a>',
            esc_url($product->get_permalink()),
            esc_url($image_url),
            esc_attr($image_alt)
        );
    }

    private function getProductTitleHtml($product, $product_title)
    {
        if ( ! $product) {
            return sanitize_text_field($product_title);
        }

        return sprintf(
            '<a href="%s">%s</a>',
            esc_url($product->get_permalink()),
            esc_html($product->get_name())
        );
    }

    private function getRatingHtml($rating, $tableId = null)
    {
        $rating = floatval($rating);
        if ($rating <= 0) {
            return '';
        }

        $appearance_settings = get_post_meta($tableId, '_ninja_table_woo_reviews_appearance_settings', true);
        $filled_color        = Arr::get($appearance_settings, 'filled_star_color', '#f2c94c');
        $empty_color         = Arr::get($appearance_settings, 'empty_star_color', '#e6e6e6');

        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= sprintf(
                    '<span class="star filled" style="color: %s;">★</span>',
                    esc_attr($filled_color)
                );
            } else {
                $stars .= sprintf(
                    '<span class="star" style="color: %s;">☆</span>',
                    esc_attr($empty_color)
                );
            }
        }

        return sprintf(
            '<div class="woo-rating">%s</div>',
            $stars
        );
    }
}
