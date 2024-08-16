<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Product_Sync_Admin {

    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Product Sync',
            'Product Sync',
            'manage_options',
            'product-sync',
            array($this, 'admin_page_content'),
            'dashicons-update',
            20
        );
    }

    public function admin_page_content() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sync_log';
        $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time DESC LIMIT 10");

?>
        <div class="wrap">
            <h1>Product Sync Status</h1>
            <p>Last Sync: <?php echo get_option('wc_product_sync_last_sync') ? esc_html(get_option('wc_product_sync_last_sync')) : 'Never'; ?></p>
            <p>Sync Status: <?php echo get_option('wc_product_sync_status') ? esc_html(get_option('wc_product_sync_status')) : 'No status available'; ?></p>
            <p><a href="<?php echo esc_url(admin_url('admin-post.php?action=manual_product_sync')); ?>" class="button button-primary">Run Manual Sync</a></p>

            <h2>Sync Log</h2>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Time</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs) : ?>
                        <?php foreach ($logs as $log) : ?>
                            <tr>
                                <td><?php echo esc_html($log->time); ?></td>
                                <td><?php echo esc_html($log->status); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="2">No log entries found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
<?php
    }
}
