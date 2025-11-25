<?php

namespace NinjaTablesPro\App\Traits;

use NinjaTables\Framework\Support\Arr;
use NinjaTablesPro\App\Application;

trait WooDataSourceTrait
{
    private $__queryable_postColumns__ = [];

    private $acf_installed = false;

    public function buildWPQuery($data)
    {
        $columns      = Arr::get($data, 'formatted_columns', []);
        $tableId      = Arr::get($data, 'tableId');
        $whereClauses = Arr::get($data, 'where', []);
        $postTypes    = Arr::get($data, 'post_types', []);
        $perPage      = (int)Arr::get($data, 'per_page', -1);
        $offset       = (int)Arr::get($data, 'offset', 0);

        $args = [
            'post_type'      => $postTypes,
            'posts_per_page' => $perPage,
            'offset'         => $offset,
            'post_status'    => 'publish',
        ];

        $orderQuery = Arr::get($data, 'order_query', []);
        if ($orderQuery) {
            $args = wp_parse_args($args, $orderQuery);
        }

        $args = $this->buildQueryArgsForPostFields($args, $whereClauses);

        $args = $this->buildQueryArgsForTaxonomies($args, $whereClauses);

        $args = apply_filters('ninja_table_post_table_args', $args, $data);
        $args = apply_filters('ninja_table_post_table_args_' . $tableId, $args, $data);

        $query = (new \WP_Query($args));
        $posts = $query->posts;

        $formattedPosts = [];

        $cachedProducts = [];


        foreach ($posts as $post_index => $post) {
            $data = [];
            foreach ($columns as $column_key => $column) {
                if (Arr::get($column, 'type') == 'post_data') {
                    $data[$column_key] = $this->getPostData($post, $column);
                } elseif (Arr::get($column, 'type') == 'tax_data') {
                    $data[$column_key] = $this->getTaxData($post, $column);
                } elseif (Arr::get($column, 'type') == 'custom') {
                    $data[$column_key] = $this->getCustomData($post, $column);
                } elseif (Arr::get($column, 'type') == 'author_data') {
                    $data[$column_key] = $this->getAuthorData($post, $column);
                } elseif (Arr::get($column, 'type') == 'product_data') {
                    if (!isset($cachedProducts[$post->ID])) {
                        $cachedProducts[$post->ID] = wc_get_product($post->ID);
                    }
                    $product           = $cachedProducts[$post->ID];
                    $data[$column_key] = $this->getProductData($product, $column);
                } elseif (Arr::get($column, 'type') == 'shortcode') {
                    $value = Arr::get($column, 'column_settings.wp_post_custom_data_value');
                    $codes = $this->getShortCodes($value, $post);
                    if ($codes) {
                        $value = str_replace(array_keys($codes), array_values($codes), $value);
                        $value = do_shortcode($value);
                    } else {
                        $value = do_shortcode($value);
                    }
                    $data[$column_key] = $value;
                }
            }
            $formattedPosts[] = $data;
        }

        return $formattedPosts;
    }

    private function getTaxData($post, $column)
    {
        $tax       = Arr::get($column, 'column_settings.wp_post_custom_data_key');
        $separator = Arr::get($column, 'column_settings.taxonomy_separator', ', ');

        $atts = '';
        if (Arr::get($column, 'permalinked') == 'yes') {
            if (Arr::get($column, 'filter_permalinked') == 'yes') {
                $atts = ' data-target_column=' . Arr::get(
                        $column,
                        'key'
                    ) . ' class="ninja_table_permalink ninja_table_do_column_filter" ';
            } elseif (Arr::get($column, 'permalink_target') == '_blank') {
                $atts = ' class="ninja_table_tax_permalink" target="_blank" ';
            } else {
                $atts = ' class="ninja_table_tax_permalink" ';
            }
        }

        $terms = array_map(function ($term) use ($atts) {
            if ($atts) {
                $link = get_term_link($term);

                return "<a " . $atts . " href='" . esc_url($link) . "'>" . esc_html($term->name) . "</a>";
            }

            return $term->name;
        }, wp_get_post_terms($post->ID, $tax));
        if ($terms) {
            return implode($separator, $terms);
        }

        return '';
    }

    private function getPostData($post, $column)
    {
        if (isset($post->post_author)) {
            $user = get_user_by('id', $post->post_author);
            if ($user) {
                $post->post_author = $user->display_name;
            }
        }

        $original_name = Arr::get($column, 'column_settings.wp_post_custom_data_key');
        $value         = '';
        if (property_exists($post, $original_name)) {
            $value = $post->{$original_name};
        }
        if (!$value) {
            return '';
        }

        if (
            Arr::get($column, 'column_settings.data_type') == 'date' &&
            $format = Arr::get($column, 'column_settings.dateFormat')
        ) {
            $value = date($this->convertMomentFormatToPhp($format), strtotime($value));
        }

        // Check if linkable
        if (Arr::get($column, 'permalinked') == 'yes') {
            $atts = '';
            if (Arr::get($column, 'permalink_target') == '_blank') {
                $atts = 'target="_blank"';
            }

            return '<a ' . esc_attr($atts) . ' title="' . esc_attr($post->post_title) . '" class="ninja_table_permalink" 
                    href="' . esc_url(get_the_permalink($post)) . '">' . esc_html($value) . '</a>';
        }

        return esc_html($value);
    }

    private function getProductData($product, $column)
    {
        $type = Arr::get($column, 'column_settings.wp_post_custom_data_key');
        if ($type == 'product_price') {
            return $product->get_price_html();
        } elseif ($type == 'product_average_rating') {
            $average_rating = (float)$product->get_average_rating();

            if ($average_rating <= 0) {
                return esc_html(Arr::get($column, 'column_settings.empty_review_text', 'No rating'));
            }

            $max_rating      = 5;
            $filled_stars    = floor($average_rating);
            $filledStarColor = Arr::get($column, 'column_settings.filled_star_color') ?: '#ffd700';;
            $emptyStarColor = Arr::get($column, 'column_settings.empty_star_color') ?: '#e0e0e0';
            $showText       = Arr::get($column, 'column_settings.show_rating_text', 'yes');

            $html = '<div style="display:inline-flex;align-items:center;gap:2px">';

            $html .= str_repeat(
                '<span style="color:' . esc_attr($filledStarColor) . '">★</span>',
                $filled_stars
            );

            $html .= str_repeat(
                '<span style="color:' . esc_attr($emptyStarColor) . '">★</span>',
                $max_rating - $filled_stars
            );

            if (strtolower($showText) === 'yes') {
                $html .= '<span style="margin-left:8px;color:#666">'
                         . esc_html(number_format($average_rating, 1)) . '/' . esc_html($max_rating)
                         . '</span>';
            }

            $html .= '</div>';

            return $html;
        } elseif ($type == 'buy_now_button') {
            if (!$product->is_in_stock() && apply_filters('ninjatable_hide_out_stock_cart_btn', true, $product)) {
                return 'Out of stock';
            }
            $productType = $product->get_type();
            $html        = '';
            $value       = Arr::get($column, 'column_settings.buy_now_button_text');
            if (!$value) {
                $value = $product->add_to_cart_text();
            }
            if ($productType == 'simple') {
                $html = sprintf(
                    '<a href="%s" data-product_id="%d" data-quantity="%s" class="%s">%s</a>',
                    esc_url($product->add_to_cart_url()),
                    $product->get_id(),
                    '1',
                    'nt_add_to_cart_' . $product->get_id(
                    ) . ' nt_button nt_button_woo single_add_to_cart_button button alt wc_product_' . $productType,
                    $value
                );
            } elseif ($productType == 'external') {
                $html = sprintf(
                    '<a target="_blank" rel="noopener" href="%s" class="%s">%s</a>',
                    esc_url($product->add_to_cart_url()),
                    'nt_button nt_button_woo nt_button_woo wc_product_' . $productType,
                    esc_html($product->add_to_cart_text())
                );
            } elseif ($productType == 'variable') {
                $html = $this->variableProduct($product, $value);
            } else {
                $html = sprintf(
                    '<a target="_blank" rel="noopener" href="%s" class="%s">%s</a>',
                    esc_url($product->add_to_cart_url()),
                    'nt_button nt_button_woo nt_button_woo wc_product_' . $productType,
                    esc_html($product->add_to_cart_text())
                );
            }

            $contentTextAlign = Arr::get($column, 'column_settings.contentAlign', '');

            return '<div class="nt_add_cart_wrapper ' . esc_attr($contentTextAlign) . '">' . $html . '</div>';
        } elseif ($type == 'product_quantity') {
            ob_start();
            $this->getQunatityInput($product);

            return ob_get_clean();
        } elseif ($type == 'product_stock_status') {
            if ($product->get_stock_status() === 'outofstock') {
                return 'Out of Stock';
            } elseif ($product->get_stock_status() === 'onbackorder') {
                return 'On Backorder';
            } elseif ($product->get_stock_status() === 'instock') {
                return 'In Stock';
            }

            return ucwords($product->get_stock_status());
        } elseif ($type == 'product_sku') {
            return $product->get_sku();
        }

        return $type;
    }

    public function hasDefaultTaxonomy($default_variation, $key, $values)
    {
        if (get_taxonomy($key)) {
            $terms  = get_terms([
                'taxonomy'   => $key,
                'hide_empty' => false,
                'slug'       => $values,
            ]);
            $values = array_column($terms, 'slug');

            if (empty($values) || empty($default_variation)) {
                return [];
            }

            $keys   = array_values(array_intersect($values, $default_variation));
            $terms  = get_terms([
                'taxonomy'   => $key,
                'hide_empty' => false,
                'slug'       => $keys,
            ]);
            $values = array_column($terms, 'name');
            $values = array_combine($keys, $values);
        } else {
            if (empty($values) || empty($default_variation)) {
                return [];
            }

            $values = array_values(array_intersect($default_variation, $values));
            $values = array_combine($values, $values);
        }

        return $values;
    }

    public function getAllEnabledVariations($product)
    {
        $variations         = $product->get_available_variations();
        $default_variations = $product->get_variation_attributes();

        $variations_attributes = [];
        foreach ($variations as $variation) {
            $variation_attributes = Arr::get($variation, 'attributes');
            foreach ($variation_attributes as $key => $variation_attribute) {
                $variations_attributes[$key][] = $variation_attribute;
            }
        }

        $index = 0;
        foreach ($default_variations as $key => $default_variation) {
            $values = array_values($variations_attributes)[$index];

            if (in_array('', (array)$values)) {
                $values = $default_variation;
            }

            $values                   = $this->hasDefaultTaxonomy($default_variation, $key, $values);
            $default_variations[$key] = $values;
            $index++;
        }

        return $default_variations;
    }

    public function makeKey($key)
    {
        $key      = strtolower($key);
        $key      = preg_replace("/[^a-z0-9_]+/", "-", $key);
        $last_key = substr($key, -1);

        if ($last_key == '-') {
            $key = substr($key, 0, -1);
        }

        return $key;
    }

    public function variableProduct($prod, $value)
    {
        global $product;
        $product = $prod;

        $get_variations       = count($product->get_children()) <= apply_filters(
                'woocommerce_ajax_variation_threshold',
                30,
                $product
            );
        $available_variations = $get_variations ? $product->get_available_variations() : false;
        $attributes           = $product->get_variation_attributes();
        $selected_attributes  = $product->get_default_attributes();

        ob_start();
        include NINJAPROPLUGIN_PATH . 'app/Views/woocommerce/variation.php';

        return ob_get_clean();
    }

    public function buildQueryArgsForPostFields($args, $whereClauses)
    {
        $this->__queryable_postColumns__ = array_filter($whereClauses, function ($item) {
            return strpos(Arr::get($item, 'field'), '.') === false;
        });

        foreach ($this->__queryable_postColumns__ as $postColumn) {
            $postColumnField = Arr::get($postColumn, 'field');
            if ($postColumnField == 'post_status') {
                if (Arr::get($postColumn, 'operator') == 'NOT IN') {
                    $postStatuses        = array_map(function ($status) {
                        return Arr::get($status, 'key');
                    }, ninjaTablesGetPostStatuses());
                    $postColumn['value'] = array_diff(
                        $postStatuses,
                        Arr::get($postColumn, 'value')
                    );
                }
                $args['post_status'] = Arr::get($postColumn, 'value');
            } elseif ($postColumnField == 'post_author') {
                $operator = Arr::get($postColumn, 'operator');
                if ($operator == 'IN') {
                    $operator = 'author__in';
                } elseif ($operator == 'NOT IN') {
                    $operator = 'author__not_in';
                }
                $args[$operator] = Arr::get($postColumn, 'value');
            } elseif ($postColumnField == 'ID') {
                add_filter('posts_where', [$this, 'WPNinjaTablesPostWhereIDFilter']);
            } elseif ($postColumnField == 'post_date') {
                add_filter('posts_where', [$this, 'WPNinjaTablesPostWherePostDateFilter']);
            } elseif ($postColumnField == 'post_modified') {
                add_filter('posts_where', [$this, 'WPNinjaTablesPostWherePostModifiedFilter']);
            } elseif ($postColumnField == 'comment_count') {
                add_filter('posts_where', [$this, 'WPNinjaTablesPostWhereCommentCountFilter']);
            }
        }

        return $args;
    }

    public function WPNinjaTablesPostWhereIDFilter($where)
    {
        global $wpdb;

        remove_filter(current_filter(), [$this, __FUNCTION__]);

        foreach ($this->__queryable_postColumns__ as $column) {
            if (Arr::get($column, 'field') == 'ID') {
                $operator = Arr::get($column, 'operator');
                $value    = Arr::get($column, 'value');
                $where    .= " AND {$wpdb->posts}.ID {$operator} {$value}";
            }
        }

        return $where;
    }

    public function WPNinjaTablesPostWherePostDateFilter($where)
    {
        global $wpdb;

        remove_filter(current_filter(), [$this, __FUNCTION__]);

        foreach ($this->__queryable_postColumns__ as $column) {
            if (Arr::get($column, 'field') == 'post_date') {
                $operator = Arr::get($column, 'operator');
                $value    = Arr::get($column, 'value');
                $where    .= " AND {$wpdb->posts}.post_date {$operator} '{$value}'";
            }
        }

        return $where;
    }

    public function WPNinjaTablesPostWherePostModifiedFilter($where)
    {
        global $wpdb;

        remove_filter(current_filter(), [$this, __FUNCTION__]);

        foreach ($this->__queryable_postColumns__ as $column) {
            if (Arr::get($column, 'field') == 'post_modified') {
                $operator = Arr::get($column, 'operator');
                $value    = Arr::get($column, 'value');
                $where    .= " AND {$wpdb->posts}.post_modified {$operator} '{$value}'";
            }
        }

        return $where;
    }

    public function WPNinjaTablesPostWhereCommentCountFilter($where)
    {
        global $wpdb;

        remove_filter(current_filter(), [$this, __FUNCTION__]);

        foreach ($this->__queryable_postColumns__ as $column) {
            if (Arr::get($column, 'field') == 'comment_count') {
                $operator = Arr::get($column, 'operator');
                $value    = Arr::get($column, 'value');
                $where    .= " AND {$wpdb->posts}.comment_count {$operator} {$value}";
            }
        }

        return $where;
    }

    public function buildQueryArgsForTaxonomies($args, $whereClauses)
    {
        $taxonomies = array_filter($whereClauses, function ($item) {
            return strpos(Arr::get($item, 'field'), '.') !== false;
        });

        global $ninja_table_current_rendering_table;
        $manualTaxonomies = false;
        if ($ninja_table_current_rendering_table && $ninja_table_current_rendering_table['shortCodeData']['post_tax']) {
            $postTaxes = $ninja_table_current_rendering_table['shortCodeData']['post_tax'];
            $postTaxes = explode('|', $postTaxes);
            foreach ($postTaxes as $postTax) {
                $postTax = explode('=', $postTax);
                if (count($postTax) == 2) {
                    $manualTaxonomies[$postTax[0]] = explode(',', $postTax[1]);
                }
            }
        }

        if (!$taxonomies && !$manualTaxonomies) {
            return $args;
        }

        $taxQueryItems = [];
        foreach ($taxonomies as $taxQuery) {
            $field    = Arr::get($taxQuery, 'field');
            $taxonomy = substr($field, strpos($field, '.') + 1);

            if ($manualTaxonomies && $manualTaxonomies[$taxonomy]) {
                continue;
            }

            $taxQueryItems[] = [
                'field'    => 'slug',
                'taxonomy' => $taxonomy,
                'terms'    => Arr::get($taxQuery, 'value'),
                'operator' => Arr::get($taxQuery, 'operator')
            ];
        }

        if ($manualTaxonomies) {
            $manualTaxField = 'slug';
            if (
                isset($ninja_table_current_rendering_table['shortCodeData']['post_tax_field']) &&
                $ninja_table_current_rendering_table['shortCodeData']['post_tax_field']
            ) {
                $manualTaxField = $ninja_table_current_rendering_table['shortCodeData']['post_tax_field'];
            }

            foreach ($manualTaxonomies as $manualTax => $taxValue) {
                $taxQueryItems[] = [
                    'field'    => $manualTaxField,
                    'taxonomy' => $manualTax,
                    'terms'    => $taxValue,
                    'operator' => 'IN'
                ];
            }
        }

        $taxQueryItems['relation'] = 'AND';
        $args['tax_query']         = $taxQueryItems;

        return $args;
    }

    public function getType($column)
    {
        $numericColumnsMap = [
            'ID',
            'comment_count',
            'menu_order',
            'post_parent'
        ];
        $dateColumnsMap    = [
            'post_date',
            'post_date_gmt',
            'post_modified',
            'post_modified_gmt'
        ];

        if (in_array($column, $numericColumnsMap)) {
            return 'number';
        }

        if (in_array($column, $dateColumnsMap)) {
            return 'date';
        }

        return 'text';
    }

    public function getHumanName($column)
    {
        $trans = [
            'post_author'    => __('Author', 'ninja-tables-pro'),
            'post_date'      => __('Create Date', 'ninja-tables-pro'),
            'post_content'   => __('Content', 'ninja-tables-pro'),
            'post_title'     => __('Title', 'ninja-tables-pro'),
            'post_excerpt'   => __('Excerpt', 'ninja-tables-pro'),
            'post_status'    => __('Status', 'ninja-tables-pro'),
            'comment_status' => __('Comment Status', 'ninja-tables-pro'),
            'post_type'      => __('Post Type', 'ninja-tables-pro'),
            'comment_count'  => __('Total Comments', 'ninja-tables-pro')
        ];

        if (isset($trans[$column])) {
            return $trans[$column];
        } elseif (($pos = strpos($column, '.')) !== false) {
            return ucfirst(substr($column, $pos + 1));
        }

        return $column;
    }

    public function getSourceType($column)
    {
        if (strpos($column, '.')) {
            return 'tax_data';
        }

        return 'post_data';
    }

    private function getCustomData($post, $column)
    {
        $type = Arr::get($column, 'wp_post_custom_data_source_type');

        $value = Arr::get($column, 'wp_post_custom_data_key');
        if (!$value) {
            return '';
        }
        if ($type == 'acf_field') {
            if ($this->acf_installed || function_exists('the_field')) {
                $this->acf_installed = true;
                ob_start();
                the_field($value, $post->ID);

                return ob_get_clean();
            }
        } elseif ($type == 'post_meta') {
            return get_post_meta($post->ID, $value, true);
        } elseif ($type == 'shortcode') {
            // check for data types
            $codes = $this->getShortCodes($value, $post);
            if ($codes) {
                $value = str_replace(array_keys($codes), array_values($codes), $value);

                return do_shortcode($value);
            } else {
                return do_shortcode($value);
            }
        } elseif ($type == 'featured_image') {
            $value = $this->getFeaturedImage($post, $column);
        } else {
        }

        return $value;
    }

    private function getShortCodes($string, $post)
    {
        $matches = [];
        $regex   = "/\{([^\}]*)\}/";
        preg_match_all($regex, $string, $matches);
        if (count($matches) != 2) {
            return false;
        }
        $formats = [];

        $acceptedPrefixes = [
            'acf',
            'post',
            'post_mata'
        ];

        foreach ($matches[1] as $match) {
            $group      = substr($match, 0, strpos($match, '.'));
            $fieldName  = str_replace($group . '.', '', $match);
            $parseValue = '';
            if ($group && $fieldName) {
                if ($group == 'post') {
                    if (property_exists($post, $fieldName)) {
                        $parseValue = $post->{$fieldName};
                    } elseif ($fieldName == 'permalink') {
                        $parseValue = get_the_permalink($post);
                    } elseif ($fieldName == 'featured_image_url') {
                        $parseValue = get_the_post_thumbnail_url($post);
                    }
                } elseif ($group == 'postmeta') {
                    $parseValue = get_post_meta($post->ID, $fieldName, true);
                } elseif ($group == 'acf' && function_exists('get_field')) {
                    $parseValue = get_field($fieldName, $post->ID);
                }
            }
            $formats['{' . $match . '}'] = $parseValue;
        }

        return $formats;
    }

    private function getAuthorData($post, $column)
    {
        $atts = '';
        if (Arr::get($column, 'permalinked') == 'yes') {
            if (Arr::get($column, 'filter_permalinked') == 'yes') {
                $atts = ' class="ninja_table_author_permalink ninja_table_do_column_filter" ';
            } elseif (Arr::get($column, 'permalink_target') == '_blank') {
                $atts .= ' class="ninja_table_author_permalink" target="_blank" ';
            } else {
                $atts .= ' class="ninja_table_author_permalink" ';
            }
        }

        $authorName = get_the_author_meta('display_name', $post->post_author);

        if ($atts && $authorName) {
            $authlink = get_author_posts_url($post->post_author);

            return '<a data-target_column=' . Arr::get(
                    $column,
                    'key'
                ) . ' href="' . $authlink . '" ' . $atts . '>' . $authorName . '</a>';
        }

        return $authorName;
    }

    private function getFeaturedImage($post, $column)
    {
        $featuredImageUrl = get_the_post_thumbnail_url(
            $post,
            Arr::get($column, 'wp_post_custom_data_key', 'thumbnail')
        );
        if (!$featuredImageUrl) {
            return '';
        }

        $linkType = Arr::get($column, 'column_settings.image_permalink_type');

        $postTitle = $post->post_title;
        $value     = '<img alt="' . $postTitle . '" src="' . $featuredImageUrl . '" />';
        // Product Linked
        if ($linkType == 'linked') {
            $permalink = get_the_permalink($post);
            $atts      = '';
            if (Arr::get($column, 'permalink_target') == '_blank') {
                $atts = 'target="_blank"';
            }

            return '<a ' . $atts . ' rel="noopener" title="' . $postTitle . '" class="ninja_table_permalink" href="' . $permalink . '">' . $value . '</a>';
        } elseif ($linkType == 'lightbox') {
            $permalink = get_the_post_thumbnail_url($post, 'full');

            return '<a title="' . $postTitle . '" class="nt_lightbox" href="' . $permalink . '">' . $value . '</a>';
        }

        return $value;
    }

    protected function getQueryExtra($tableId)
    {
        $queryExtra = get_post_meta($tableId, '_ninja_wp_posts_query_extra', true);
        if (!$queryExtra || $queryExtra == 'false') {
            $queryExtra = [
                'query_limit'     => 6000,
                'order_by_column' => 'ID',
                'order_by'        => 'DESC'
            ];
        }

        if (empty(Arr::get($queryExtra, 'query_limit'))) {
            $queryExtra['query_limit'] = 7000;
        }

        return apply_filters('ninja_table_wp_posts_query_extra', $queryExtra, $tableId);
    }

    protected function convertMomentFormatToPhp($format)
    {
        $replacements = [
            'DD'   => 'd',
            'ddd'  => 'D',
            'D'    => 'j',
            'dddd' => 'l',
            'E'    => 'N',
            'o'    => 'S',
            'e'    => 'w',
            'DDD'  => 'z',
            'W'    => 'W',
            'MMMM' => 'F',
            'MM'   => 'm',
            'MMM'  => 'M',
            'M'    => 'n',
            'YYYY' => 'Y',
            'YY'   => 'y',
            'a'    => 'a',
            'A'    => 'A',
            'h'    => 'g',
            'H'    => 'G',
            'hh'   => 'h',
            'HH'   => 'H',
            'mm'   => 'i',
            'ss'   => 's',
            'SSS'  => 'u',
            'zz'   => 'e',
            'X'    => 'U',
        ];

        $phpFormat = strtr($format, $replacements);

        return $phpFormat;
    }

    public function getWooProductAtrributes()
    {
        $attributes = [
            'product_price'          => 'Product Price',
            'product_stock_status'   => 'Product Stock Status',
            'product_quantity'       => 'Product Quantity input field',
            'buy_now_button'         => 'Buy Now Button',
            'product_sku'            => 'Product SKU',
            'product_average_rating' => 'Product Average Rating',
        ];

        return [
            "key"         => 'product_data',
            'source_type' => 'product_data',
            "label"       => 'Product Data',
            "instruction" => 'Show Product Data Attributes',
            "value_type"  => 'options',
            "placeholder" => 'Select Data Attribute',
            "options"     => $attributes
        ];

        return $attributes;
    }

    public function getPostDynamicColumnAtrributes()
    {
        $fields = [];
        if (function_exists('get_field')) {
            $fields[] = [
                "key"             => 'acf_field',
                'source_type'     => 'custom',
                "label"           => 'Advanced Custom Fields (ACF)',
                "instruction"     => 'You can populate any ACF fields. Please provide the selector name of the ACF field then your table column values will be populated',
                "learn_more_url"  => 'https://wpmanageninja.com/docs/ninja-tables/wp-posts-table/acf-field/',
                "learn_more_text" => 'Learn more about ACF Field integration',
                "value_type"      => 'text',
                "placeholder"     => 'Type ACF field selector'
            ];
        }

        $fields[] = [
            "key"             => 'post_meta',
            'source_type'     => 'custom',
            "label"           => 'Post Meta',
            "placeholder"     => 'Type Post Meta key',
            "instruction"     => 'You can populate any Post Meta. Please provide the name of the meta key then your table column values will be populated for corresponding row',
            "learn_more_url"  => 'https://wpmanageninja.com/docs/ninja-tables/wp-posts-table/custom-column-on-wp-posts-table/',
            "learn_more_text" => 'Learn more about Post Meta integration',
            "value_type"      => 'text'
        ];
        $fields[] = [
            "key"             => 'shortcode',
            'source_type'     => 'shortcode',
            "label"           => 'Shortcode / Computed Value or HTML',
            "placeholder"     => 'Provide any valid HTML / Computed fields, Please check instruction / documentation for advance usage',
            "instruction"     => 'You can add any type of HTML or customized dynamic field / shortcode as the column value. You add dynamic post/post meta/acf field like as below: <ul><li>For Post Field: {post.ID} / {post.post_title} / {post.permalink}</li><li>For Post Meta: {postmeta.POSTMETA_KEY_NAME}</li><li>For ACF Field: {acf.acf_field_name}</li><li>For Dynamic Shortcode: [yourshortcode YourParam="{post.ID}"]</li></ul>',
            "learn_more_url"  => 'https://wpmanageninja.com/docs/ninja-tables/wp-posts-table/shortcode-computed-value-or-html-in-wp-posts-table/',
            "learn_more_text" => 'Please read the documentation for more details and advanced usage',
            "value_type"      => 'textarea'
        ];

        $imageSizes          = get_intermediate_image_sizes();
        $formattedImageSizes = [];
        foreach ($imageSizes as $imageSize) {
            $formattedImageSizes[$imageSize] = $imageSize;
        }

        $fields[] = [
            "key"         => 'featured_image',
            'source_type' => 'custom',
            "label"       => 'Featured Image',
            "instruction" => 'Show Featured image with post link / without link',
            "value_type"  => 'options',
            "placeholder" => 'Select Image Size',
            "options"     => $formattedImageSizes
        ];

        return $fields;
    }

    public function getPostColumnAttributes()
    {
        $attributes = [
            'post_author'   => 'Post Author',
            'post_date'     => 'Post Date',
            'post_title'    => 'Product Title',
            'post_excerpt'  => 'Product Short Description',
            'post_content'  => 'Product Long Description',
            'post_status'   => 'Post Status',
            'comment_count' => 'Post Comment Count'
        ];

        return [
            "key"         => 'post_data',
            'source_type' => 'post_data',
            "label"       => 'Post Data',
            "instruction" => 'Show Post Data Attributes',
            "value_type"  => 'options',
            "placeholder" => 'Select Data Attribute',
            "options"     => $attributes
        ];
    }

    public function getPostTaxAttributes($postType)
    {
        $taxonomies = get_object_taxonomies($postType);

        $attributes = [];
        foreach ($taxonomies as $taxonomy) {
            $attributes[$taxonomy] = ucfirst(str_replace('_', ' ', $taxonomy));
        }

        return [
            "key"         => 'tax_data',
            'source_type' => 'tax_data',
            "label"       => 'Product Taxonomy',
            "instruction" => 'Show Product Categories/Tags/Type/Other Terms',
            "value_type"  => 'options',
            "placeholder" => 'Select Data Attribute',
            "options"     => $attributes
        ];
    }

    protected function get($array, $key, $default = false)
    {
        if (!is_array($array)) {
            return $default;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }

        return $default;
    }

    private function getQunatityInput($product, $display_type = 'input')
    {
        if ($product->get_type() === 'external' || $product->get_type() === 'grouped') {
            return;
        }
        if (!$product->is_in_stock() && apply_filters('ninjatable_hide_out_stock_cart_btn', true, $product)) {
            return '';
        }

        $args = apply_filters('woocommerce_quantity_input_args', [
            'input_id'     => uniqid('quantity_'),
            'input_name'   => 'quantity',
            'input_value'  => '1',
            'max_value'    => apply_filters(
                'woocommerce_quantity_input_max',
                $product->get_max_purchase_quantity(),
                $product
            ),
            'min_value'    => apply_filters(
                'woocommerce_quantity_input_min',
                $product->get_min_purchase_quantity(),
                $product
            ),
            'step'         => apply_filters('woocommerce_quantity_input_step', 1, $product),
            'pattern'      => apply_filters(
                'woocommerce_quantity_input_pattern',
                has_filter('woocommerce_stock_amount', 'intval') ? '[0-9]*' : ''
            ),
            'inputmode'    => apply_filters(
                'woocommerce_quantity_input_inputmode',
                has_filter('woocommerce_stock_amount', 'intval') ? 'numeric' : ''
            ),
            'product_name' => $product ? $product->get_title() : '',
        ], $product);

        // Apply sanity to min/max args - min cannot be lower than 0.
        $args['min_value'] = max($args['min_value'], 0);
        $args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

        // Max cannot be lower than min if defined.
        if ('' !== $args['max_value'] && $args['max_value'] < $args['min_value']) {
            $args['max_value'] = $args['min_value'];
        }

        extract($args);
        $controls_html_classes = '';
        if (empty($max_qty)) {
            $max_qty = 10;
        }
        ?>
        <div
                class="quantity nt-quantity-wrapper nt-noselect nt-display-type-<?php
                echo $display_type ?>">
            <?php
            if ($display_type === 'input'): ?>
                <span class="nt-minus nt-qty-controller nt-noselect"></span
                ><input
                        type="number"
                        data-product_id="<?php
                        echo esc_attr($product->get_id()); ?>"
                        id="nt_product_qty_<?php
                        echo esc_attr($product->get_id()); ?>"
                        class="input-text qty text nt_woo_quantity"
                    <?php
                    if ($product->get_sold_individually()) {
                        echo 'disabled';
                    } ?>
                        step="<?php
                        echo esc_attr($step); ?>"
                        min="<?php
                        echo esc_attr($min_value); ?>"
                        max="<?php
                        echo esc_attr(0 < $max_value ? $max_value : ''); ?>"
                        name="<?php
                        echo esc_attr($input_name); ?>"
                        value="<?php
                        echo esc_attr($input_value); ?>"
                        title="<?php
                        echo esc_attr_x('Quantity', 'Product quantity input tooltip', 'ninja-tables-pro'); ?>"
                        size="4"
                        pattern="<?php
                        echo esc_attr($pattern); ?>"
                        inputmode="<?php
                        echo esc_attr($inputmode); ?>"
                        aria-labelledby="<?php
                        // translators: %s is the product name
                        echo !empty(Arr::get($args, 'product_name')) ? sprintf(
                            esc_attr__('%s quantity', 'ninja-tables-pro'),
                            Arr::get($args, 'product_name')
                        ) : ''; ?>"
                        autocomplete="off"
                /><span class="nt-plus nt-qty-controller nt-noselect"></span>
            <?php
            else: ?>
                <select
                        class="nt-qty-select"
                        data-nt-qty-label="<?php
                        echo esc_attr($qty_label); ?>"
                        data-nt-max-qty="<?php
                        echo $max_qty; ?>"
                        min="<?php
                        echo $min_value; ?>"
                >
                    <option value="<?php
                    echo $min_value; ?>"><?php
                        echo WooDataSourceTrait . phpesc_html($qty_label); ?></option>
                    <?php
                    $val = $min_value;
                    if (!empty($max_value)) {
                        $max_qty = $max_value;
                    }
                    while ($val < $max_qty) {
                        $val += $step;
                        echo '<option value="' . esc_attr($val) . '">' . esc_html($val) . '</option>';
                    }
                    ?>
                </select>
            <?php
            endif; ?>
        </div>
        <?php
    }
}
