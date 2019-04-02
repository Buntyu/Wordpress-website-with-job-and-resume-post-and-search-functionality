<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Orders
 */
class WC_Paid_Listings_Orders {

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
		add_action( 'woocommerce_thankyou', array( $this, 'woocommerce_thankyou' ), 5 );

		// Displaying user packages on the frontend
		add_action( 'woocommerce_before_my_account', array( $this, 'my_packages' ) );

		// Statuses
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_paid' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_paid' ) );
	}

	/**
	 * Thanks page
	 */
	public function woocommerce_thankyou( $order_id ) {
		global $wp_post_types;

		$order = new WC_Order( $order_id );

		foreach ( $order->get_items() as $item ) {
			if ( isset( $item['job_id'] ) && 'publish' === get_post_status( $item['job_id'] ) ) {
				echo wpautop( sprintf( __( '%s listed successfully. To view your listing <a href="%s">click here</a>.', 'wp-job-manager' ), get_the_title( $item['job_id'] ), get_permalink( $item['job_id'] ) ) );

			} elseif( isset( $item['resume_id'] ) ) {
				echo wpautop( sprintf( __( '%s listed successfully. To view your listing <a href="%s">click here</a>.', 'wp-job-manager' ), get_the_title( $item['resume_id'] ), get_permalink( $item['resume_id'] ) ) );
			}
		}
	}

	/**
	 * Show my packages
	 */
	public function my_packages() {
		if ( ( $packages = wc_paid_listings_get_user_packages( get_current_user_id(), 'job_listing' ) ) && is_array( $packages ) && sizeof( $packages ) > 0 ) {
			woocommerce_get_template( 'my-packages.php', array( 'packages' => $packages, 'type' => 'job_listing' ), 'wc-paid-listings/', JOB_MANAGER_WCPL_TEMPLATE_PATH );
		}
		if ( ( $packages = wc_paid_listings_get_user_packages( get_current_user_id(), 'resume' ) ) && is_array( $packages ) && sizeof( $packages ) > 0 ) {
			woocommerce_get_template( 'my-packages.php', array( 'packages' => $packages, 'type' => 'resume' ), 'wc-paid-listings/', JOB_MANAGER_WCPL_TEMPLATE_PATH );
		}
	}

	/**
	 * Triggered when an order is paid
	 * @param  int $order_id
	 */
	public function order_paid( $order_id ) {
		// Get the order
		$order = new WC_Order( $order_id );

		if ( get_post_meta( $order_id, 'wc_paid_listings_packages_processed', true ) ) {
			return;
		}
		foreach ( $order->get_items() as $item ) {
			$product = get_product( $item['product_id'] );

			if ( $product->is_type( array( 'job_package', 'resume_package', 'job_package_subscription', 'resume_package_subscription' ) ) && $order->customer_user ) {

				// Give packages to user
				for ( $i = 0; $i < $item['qty']; $i ++ ) {
					$user_package_id = wc_paid_listings_give_user_package( $order->customer_user, $product->id, $order_id );
				}

				// Approve job or resume with new package
				if ( isset( $item['job_id'] ) ) {
					$job = get_post( $item['job_id'] );

					if ( in_array( $job->post_status, array( 'pending_payment', 'expired' ) ) ) {
						wc_paid_listings_approve_job_listing_with_package( $job->ID, $order->customer_user, $user_package_id );
					}
				} elseif( isset( $item['resume_id'] ) ) {
					$resume = get_post( $item['resume_id'] );

					if ( in_array( $resume->post_status, array( 'pending_payment', 'expired' ) ) ) {
						wc_paid_listings_approve_resume_with_package( $resume->ID, $order->customer_user, $user_package_id );
					}
				}
			}
		}

		update_post_meta( $order_id, 'wc_paid_listings_packages_processed', true );
	}
}
WC_Paid_Listings_Orders::get_instance();