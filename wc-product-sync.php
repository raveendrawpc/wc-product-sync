<?php
/*
Plugin Name: WooCommerce Product Sync
Description: Sync WooCommerce products from a third-party service.
Version: 1.0.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once plugin_dir_path(__FILE__) . 'includes/class-product-sync.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-product-sync-admin.php';

function wc_product_sync_init() {
    $product_sync = new Product_Sync();
    $product_sync->init();

    if (is_admin()) {
        $product_sync_admin = new Product_Sync_Admin();
        $product_sync_admin->init();
    }
}
add_action('plugins_loaded', 'wc_product_sync_init');

register_activation_hook(__FILE__, 'create_sync_log_table');
function create_sync_log_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'sync_log';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        status text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

add_action('admin_post_manual_product_sync', 'handle_manual_product_sync');
function handle_manual_product_sync() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $product_sync = new Product_Sync();
    $product_sync->sync_products();

    wp_safe_redirect(admin_url('admin.php?page=product-sync'));
    exit;
}
