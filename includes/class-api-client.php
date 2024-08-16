<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class API_Client {

    private $api_endpoint = 'https://my.api.mockaroo.com/products.json?key=70069600';

    public function get_products() {
        $response = wp_remote_get($this->api_endpoint);

        if (is_wp_error($response)) {
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        $products = json_decode($body, true);

        return $products;
    }
}
