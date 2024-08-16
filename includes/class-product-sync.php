<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once plugin_dir_path(__FILE__) . 'class-api-client.php';
require_once plugin_dir_path(__FILE__) . 'class-product-importer.php';

class Product_Sync {

    private $api_client;
    private $product_importer;

    public function __construct() {
        $this->api_client = new API_Client();
        $this->product_importer = new Product_Importer();
    }

    public function init() {
        add_action('wc_product_sync_cron_hook', array($this, 'sync_products'));

        if (!wp_next_scheduled('wc_product_sync_cron_hook')) {
            wp_schedule_event(time(), 'hourly', 'wc_product_sync_cron_hook');
        }
    }

    public function sync_products() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sync_log';

        $products = $this->api_client->get_products();
        $status = '';

        if (!empty($products)) {
            $this->product_importer->import_products($products);
            $status = 'Sync completed successfully.';
        } else {
            $status = 'No products found or failed to fetch products.';
        }

        update_option('wc_product_sync_last_sync', current_time('mysql'));
        update_option('wc_product_sync_status', $status);

        $wpdb->insert($table_name, array(
            'time' => current_time('mysql'),
            'status' => $status,
        ));
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('wc_product_sync_cron_hook');
    }
}

register_deactivation_hook(__FILE__, array('Product_Sync', 'deactivate'));
