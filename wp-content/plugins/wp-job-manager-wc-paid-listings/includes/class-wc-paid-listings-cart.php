<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart
 */
class WC_Paid_Listings_Cart {

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_job_package_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

		// Force reg during checkout process
		add_filter( 'option_woocommerce_enable_signup_and_login_from_checkout', array( $this, 'enable_signup_and_login_from_checkout' ) );
		add_filter( 'option_woocommerce_enable_guest_checkout', array( $this, 'enable_guest_checkout' ) );
	}

	/**
	 * Checks an cart to see if it contains a job_package.
	 */
	public function cart_contains_job_package() {
		global $woocommerce;

		if ( ! empty( $woocommerce->cart->cart_contents ) ) {
			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
				$product = $cart_item['data'];
				if ( $product->is_type( 'job_package' ) && ! $product->is_type( 'job_package_subscription' ) ) {
					return true;
				}
			}
		}
	}

	/**
	 * Checks an cart to see if it contains a resume_package.
	 */
	public function cart_contains_resume_package() {
		global $woocommerce;

		if ( ! empty( $woocommerce->cart->cart_contents ) ) {
			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
				$product = $cart_item['data'];
				if ( $product->is_type( 'resume_package' ) && ! $product->is_type( 'resume_package_subscription' ) ) {
					return true;
				}
			}
		}
	}

	/**
	 * Ensure this is yes
	 */
	public function enable_signup_and_login_from_checkout( $value ) {
		remove_filter( 'option_woocommerce_enable_guest_checkout', array( $this, 'enable_guest_checkout' ) );
		$woocommerce_enable_guest_checkout = get_option( 'woocommerce_enable_guest_checkout' );
		add_filter( 'option_woocommerce_enable_guest_checkout', array( $this, 'enable_guest_checkout' ) );

		if ( 'yes' === $woocommerce_enable_guest_checkout && ( $this->cart_contains_job_package() || $this->cart_contains_resume_package() ) ) {
			return 'yes';
		} else {
			return $value;
		}
	}

	/**
	 * Ensure this is no
	 */
	public function enable_guest_checkout( $value ) {
		if ( $this->cart_contains_job_package() || $this->cart_contains_resume_package() ) {
			return 'no';
		} else {
			return $value;
		}
	}

	/**
	 * Get the data from the session on page load
	 *
	 * @param array $cart_item
	 * @param array $values
	 * @return array
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values['job_id'] ) ) {
			$cart_item['job_id'] = $values['job_id'];
		}
		if ( ! empty( $values['resume_id'] ) ) {
			$cart_item['resume_id'] = $values['resume_id'];
		}
		return $cart_item;
	}

	/**
	 * order_item_meta function for storing the meta in the order line items
	 */
	public function order_item_meta( $item_id, $values ) {
		// Add the fields
		if ( isset( $values['job_id'] ) ) {
			$job = get_post( absint( $values['job_id'] ) );

			woocommerce_add_order_item_meta( $item_id, __( 'Job Listing', 'wp-job-manager-wc-paid-listings' ), $job->post_title );
			woocommerce_add_order_item_meta( $item_id, '_job_id', $values['job_id'] );
		}
		if ( isset( $values['resume_id'] ) ) {
			$resume = get_post( absint( $values['resume_id'] ) );

			woocommerce_add_order_item_meta( $item_id, __( 'Resume', 'wp-job-manager-wc-paid-listings' ), $resume->post_title );
			woocommerce_add_order_item_meta( $item_id, '_resume_id', $values['resume_id'] );
		}
	}

	/**
	 * Output job name in cart
	 * @param  array $data
	 * @param  array $cart_item
	 * @return array
	 */
	public function get_item_data( $data, $cart_item ) {
		if ( isset( $cart_item['job_id'] ) ) {
			$job = get_post( absint( $cart_item['job_id'] ) );

			$data[] = array(
				'name'  => __( 'Job Listing', 'wp-job-manager-wc-paid-listings' ),
				'value' => $job->post_title
			);
		}
		if ( isset( $cart_item['resume_id'] ) ) {
			$resume = get_post( absint( $cart_item['resume_id'] ) );

			$data[] = array(
				'name'  => __( 'Resume', 'wp-job-manager-wc-paid-listings' ),
				'value' => $resume->post_title
			);
		}
		return $data;
	}
}
WC_Paid_Listings_Cart::get_instance();