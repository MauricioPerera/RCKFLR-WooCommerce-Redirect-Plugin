<?php
/*
Plugin Name: RCKFLR WooCommerce Redirect
Plugin URI: https://rckflr.party/
Description: This plugin adds a custom redirect URL field to WooCommerce products.
Version: 1.0
Author: Mauricio Perera
Author URI: https://www.linkedin.com/in/mauricioperera/
Donate link: https://www.buymeacoffee.com/rckflr
*/

add_action( 'woocommerce_product_options_general_product_data', 'rckflr_woo_add_custom_general_fields' );
function rckflr_woo_add_custom_general_fields() {
    global $woocommerce, $post;
    echo '<div class="options_group">';
    woocommerce_wp_text_input(
        array(
            'id' => '_rckflr_woo_redirect_url',
            'label' => __( 'Redirect URL', 'woocommerce' ),
            'placeholder' => 'http://',
            'desc_tip' => 'true',
            'description' => __( 'Enter the URL the customer will be redirected to after purchasing this product.', 'woocommerce' )
        )
    );
    woocommerce_wp_checkbox(
        array(
            'id' => '_rckflr_woo_redirect_new_tab',
            'label' => __( 'Open in new tab', 'woocommerce' ),
            'description' => __( 'Check this box if you want the redirect URL to open in a new tab.', 'woocommerce' )
        )
    );
    woocommerce_wp_checkbox(
        array(
            'id' => '_rckflr_woo_redirect_include_order_id',
            'label' => __( 'Include Order ID', 'woocommerce' ),
            'description' => __( 'Check this box if you want to include the order ID in the redirect URL.', 'woocommerce' )
        )
    );
    woocommerce_wp_checkbox(
        array(
            'id' => '_rckflr_woo_redirect_include_nonce',
            'label' => __( 'Include Nonce', 'woocommerce' ),
            'description' => __( 'Check this box if you want to include a nonce in the redirect URL.', 'woocommerce' )
        )
    );
    echo '</div>';
}

add_action( 'woocommerce_process_product_meta', 'rckflr_woo_add_custom_general_fields_save' );
function rckflr_woo_add_custom_general_fields_save( $post_id ){
    $woocommerce_text_field = $_POST['_rckflr_woo_redirect_url'];
    if( !empty( $woocommerce_text_field ) )
        update_post_meta( $post_id, '_rckflr_woo_redirect_url', esc_attr( $woocommerce_text_field ) );
    $woocommerce_checkbox = isset( $_POST['_rckflr_woo_redirect_new_tab'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_rckflr_woo_redirect_new_tab', $woocommerce_checkbox );
    $woocommerce_checkbox = isset( $_POST['_rckflr_woo_redirect_include_order_id'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_rckflr_woo_redirect_include_order_id', $woocommerce_checkbox );
    $woocommerce_checkbox = isset( $_POST['_rckflr_woo_redirect_include_nonce'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_rckflr_woo_redirect_include_nonce', $woocommerce_checkbox );
}

add_action( 'woocommerce_thankyou', 'rckflr_woo_redirectcustom');
function rckflr_woo_redirectcustom( $order_id ){
    $order = new WC_Order( $order_id );
    $items = $order->get_items();
    $redirects = array();
    foreach ( $items as $item ) {
        $product_name = $item['name'];
        $product_id = $item['product_id'];
        $product_variation_id = $item['variation_id'];

        $redirect = get_post_meta( $product_id, '_rckflr_woo_redirect_url', true );
        $new_tab = get_post_meta( $product_id, '_rckflr_woo_redirect_new_tab', true );
        $include_order_id = get_post_meta( $product_id, '_rckflr_woo_redirect_include_order_id', true );
        $include_nonce = get_post_meta( $product_id, '_rckflr_woo_redirect_include_nonce', true );

        if ( ! empty( $redirect ) ) {
            // Get the user ID and order ID
            $user_id = $order->get_user_id();
            $order_id = $order->get_id();

            // Create a nonce
            $nonce = wp_create_nonce( 'rckflr_woo_redirect_nonce' );

            // Save the nonce in the order meta
            update_post_meta( $order_id, '_rckflr_woo_redirect_nonce', $nonce );

            // Add the user ID, order ID, and nonce to the redirect URL as GET parameters
            $args = array();
            if ( 'yes' === $include_order_id ) {
                $args['order_id'] = $order_id;
            }
            if ( 'yes' === $include_nonce ) {
                $args['nonce'] = $nonce;
            }
            $redirect = add_query_arg( $args, $redirect );

            if ( 'yes' === $new_tab ) {
                $redirects[] = $redirect;
            } else {
                wp_redirect( $redirect );
                exit;
            }
        }
    }

    // If there are URLs to open in new tabs, do it now
    if ( ! empty( $redirects ) ) {
        foreach ( $redirects as $redirect ) {
            echo "<script>window.open('".$redirect."', '_blank')</script>";
        }
    }
}
?>
