<?php

namespace DynamicContentForElementor;

trait Trait_Woo
{
    /**
     * Find matching product variation
     *
     * @param WC_Product $product
     * @param array $attributes
     * @return int Matching variation ID or 0.
     */
    public function find_matching_product_variation($product, $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (\strpos($key, 'attribute_') === 0) {
                continue;
            }
            unset($attributes[$key]);
            $attributes[\sprintf('attribute_%s', $key)] = $value;
        }
        if (\class_exists('WC_Data_Store')) {
            $data_store = \WC_Data_Store::load('product');
            return $data_store->find_matching_product_variation($product, $attributes);
        } else {
            return $product->get_matching_variation($attributes);
        }
    }
    public function get_fields()
    {
        $fields = array();
        $fields['product'] = ['_price' => __('Price', 'dynamic-content-for-elementor'), '_sale_price' => __('Sale Price', 'dynamic-content-for-elementor'), '_regular_price' => __('Regular Price', 'dynamic-content-for-elementor'), '_average_rating' => __('Average Rating', 'dynamic-content-for-elementor'), '_stock_status' => __('Stock Status', 'dynamic-content-for-elementor'), '_on_sale' => __('On Sale', 'dynamic-content-for-elementor'), '_featured' => __('Featured', 'dynamic-content-for-elementor'), '_product_type' => __('Product Type', 'dynamic-content-for-elementor')];
        return $fields;
    }
}
