<?php
/*
Plugin Name: NETOPIA Payments - V2
Plugin URI: https://www.netopia-payments.ro
Description: accept payments through NETOPIA Payments
Author: Netopia
Version: 1.0.0
License: GPLv2
*/

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'netopiapayments_v2_init', 0 );
function netopiapayments_v2_init() {
	// If the parent WC_Payment_Gateway class doesn't exist
	// it means WooCommerce is not installed on the site
	// so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
	DEFINE ('NTP_PLUGIN_DIR', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) . '/' );
	
	// If we made it this far, then include our Gateway Class
	include_once( 'wc-netopiapayments-gateway.php' );

	// Now that we have successfully included our class,
	// Lets add it too WooCommerce
	add_filter( 'woocommerce_payment_gateways', 'add_netopiapayments_gateway' );
	function add_netopiapayments_gateway( $methods ) {
		$methods[] = 'netopiapayments';
		return $methods;
	}

	// Add custom action links
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'netopia_action_links' );
	function netopia_action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=netopiapayments' ) . '">' . __( 'Settings', 'netopiapayments' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	add_action( 'admin_enqueue_scripts', 'netopiapaymentsjs_init' );
    function netopiapaymentsjs_init($hook) {
        if ( 'woocommerce_page_wc-settings' != $hook ) {
            return;
        }
        wp_enqueue_script( 'netopiapaymentsjs', plugin_dir_url( __FILE__ ) . 'js/netopiapayments.js',array('jquery'),'2.0' ,true);
        wp_enqueue_script( 'netopiatoastrjs', plugin_dir_url( __FILE__ ) . 'js/toastr.min.js',array(),'2.0' ,true);
        wp_enqueue_style( 'netopiatoastrcss', plugin_dir_url( __FILE__ ) . 'css/toastr.min.css',array(),'2.0' ,false);
    }

	// Add custom Fields in Checkout Page management

	/** Add ntpID & ntpTransactionID field in Admin Order title */ 
	add_filter( 'manage_edit-shop_order_columns', 'addNtpCustomFieldsView_order_admin_list_column' );
	function addNtpCustomFieldsView_order_admin_list_column( $columns ) {
		$columns['_ntpID'] = 'Netopia ID';
		$columns['_ntpTransactionID'] = 'Netopia Transaction ID';
		return $columns;
	}
	
	/** Add ntpID & ntpTransactionID field in Admin Order content */ 
	add_action( 'manage_shop_order_posts_custom_column', 'addNtpCustomFieldsView_order_admin_list_column_content' );
	function addNtpCustomFieldsView_order_admin_list_column_content( $column ) {
	
		global $post;
	
		if ( '_ntpID' === $column ) {
			$order = wc_get_order( $post->ID );
			$ntpID = get_metadata( 'post', $order->ID, '_ntpID', false );
			if(!empty($ntpID[0])) {
				echo ($ntpID[0]);
			}						
		}

		if ( '_ntpTransactionID' === $column ) {
			$order = wc_get_order( $post->ID );
			$ntpTransactionID = get_metadata( 'post', $order->ID, '_ntpTransactionID', false );
			if(!empty($ntpTransactionID[0])) {
				echo ($ntpTransactionID[0]);		
			}						
		}
	}
	

	/** Add ntpID as custom field in order (postmeta) */
	add_action( 'woocommerce_after_order_notes', 'ntpID_custom_checkout_field' );
	function ntpID_custom_checkout_field( $checkout ) {
		woocommerce_form_field( 'ntpID', array(
			'type'          => 'hidden',
			'class'         => array(''),
			'label'         => __(''),
			'placeholder'   => __(''),
			), $checkout->get_value( '_ntpID' ));
	}

	/** Add ntpTransactionID as custom field in order (postmeta) */
	add_action( 'woocommerce_after_order_notes', 'ntpTransactionID_custom_checkout_field' );
	function ntpTransactionID_custom_checkout_field( $checkout ) {
		woocommerce_form_field( 'ntpID', array(
			'type'          => 'hidden',
			'class'         => array(''),
			'label'         => __(''),
			'placeholder'   => __(''),
			), $checkout->get_value( '_ntpTransactionID' ));
	}


	/**
	 * Update the custom fields (ntpID & ntpTransactionID ) by defult value order meta with zero (string)
	 */
	add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );
	function my_custom_checkout_field_update_order_meta( $order_id ) {
			update_post_meta( $order_id, '_ntpID', sanitize_text_field( 0 ) );
			update_post_meta( $order_id, '_ntpTransactionID', sanitize_text_field( 0 ) );
	}
	
	/** 
	 * To customize wooCommerce "Order received" title
	 * Add the buyer first name at title
	 * ex. Navid, Order received
	 * */
	
	add_filter( 'the_title', 'woo_personalize_order_received_title', 10, 2 );
	function woo_personalize_order_received_title( $title, $id ) {
		if ( is_order_received_page() && get_the_ID() === $id ) {
			global $wp;

			// Get the order. Line 9 to 17 are present in order_received() in includes/shortcodes/class-wc-shortcode-checkout.php file
			$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $wp->query_vars['order-received'] ) );
			$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( $_GET['key'] ) );

			if ( $order_id > 0 ) {
				$order = wc_get_order( $order_id );
				if ( $order->get_order_key() != $order_key ) {
					$order = false;
				}
			}

			if ( isset ( $order ) ) {
			$title = sprintf( "%s, ".$title, esc_html( $order->get_billing_first_name() ) );
			}
		}
		return $title;
	}
}