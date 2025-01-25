<?php
/**
 * Plugin Name: woocommerce-custom-column-analytics
 *
 * @package WooCommerce\Admin
 */

/**
 * Register the JS and CSS.
 */
function add_extension_register_script() {
    if ( ! class_exists( 'Automattic\WooCommerce\Admin\PageController' ) || ! \Automattic\WooCommerce\Admin\PageController::is_admin_or_embed_page() ) {
        return;
    }

    $script_path = '/build/index.js'; // Points to the compiled JS file
    $script_asset_path = dirname( __FILE__ ) . '/build/index.asset.php';
    $script_asset = file_exists( $script_asset_path ) ? require( $script_asset_path ) : array( 'dependencies' => array(), 'version' => filemtime( $script_path ) );
    $script_url = plugins_url( $script_path, __FILE__ );

    wp_register_script(
        'woocommerce-custom-column-analytics',
        $script_url,
        $script_asset['dependencies'],
        $script_asset['version'],
        true
    );

    wp_register_style(
        'woocommerce-custom-column-analytics',
        plugins_url( '/build/style.css', __FILE__ ), // Points to the compiled CSS file
        array(),
        filemtime( dirname( __FILE__ ) . '/build/style.css' )
    );

    wp_enqueue_script( 'woocommerce-custom-column-analytics' );
    wp_enqueue_style( 'woocommerce-custom-column-analytics' );
}

add_action( 'admin_enqueue_scripts', 'add_extension_register_script' );

/**
 * Add payment method to the WooCommerce Analytics Orders data.
 */
add_filter('woocommerce_analytics_orders_select_query', function ($results, $args) {
    if ($results && isset($results->data) && !empty($results->data)) {
        foreach ($results->data as $key => $result) {
            $order = wc_get_order($result['order_id']);
            
            // Retrieve payment method title
            $payment_method_title = $order ? $order->get_payment_method_title() : '';

            $results->data[$key]['payment_method'] = $payment_method_title;
        }
    }

    return $results;
}, 10, 2);

/**
 * Add the payment method column to the exported CSV file.
 */
add_filter('woocommerce_report_orders_export_columns', function ($export_columns){
    $export_columns['payment_method'] = 'Payment Method';
    return $export_columns;
});

/**
 * Add the payment method data to the exported CSV file.
 */
add_filter('woocommerce_report_orders_prepare_export_item', function ($export_item, $item){
    $export_item['payment_method'] = $item['payment_method'];
    return $export_item;
}, 10, 2);
