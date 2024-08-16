<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Product_Importer {

    public function import_products($products) {
        foreach ($products as $product) {
            $this->import_product($product);
        }
    }

    private function import_product($product) {
        $existing_product_id = $this->get_existing_product_id($product['sku']);

        $product_data = array(
            'post_title'   => $product['name'],
            'post_content' => $product['description'],
            'post_status'  => 'publish',
            'post_type'    => 'product',
        );

        if ($existing_product_id) {
            $product_data['ID'] = $existing_product_id;
            wp_update_post($product_data);
        } else {
            $product_id = wp_insert_post($product_data);
            if ($product_id) {
                update_post_meta($product_id, '_sku', $product['sku']);
                update_post_meta($product_id, '_price', $product['price']);
                update_post_meta($product_id, '_stock', $product['stock']);
            }
        }
    }

    private function get_existing_product_id($sku) {
        global $wpdb;

        $product_id = $wpdb->get_var($wpdb->prepare("
            SELECT post_id FROM $wpdb->postmeta
            WHERE meta_key = '_sku' AND meta_value = %s
            LIMIT 1
        ", $sku));

        return $product_id;
    }
}
