<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Paid_Listings_Subscriptions
 */
class WC_Paid_Listings_Subscriptions {

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
		add_filter( 'woocommerce_is_subscription', array( $this, 'woocommerce_is_subscription' ), 10, 2 );
		add_action( 'wp_trash_post', array( $this, 'wp_trash_post' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );

		add_action( 'publish_to_expired', array( $this, 'check_expired_listing' ) );

		add_action( 'subscription_expired', array( $this, 'subscription_ended' ), 10, 2 );
		add_action( 'subscription_trashed', array( $this, 'subscription_ended' ), 10, 2 );
		add_action( 'subscription_end_of_prepaid_term', array( $this, 'subscription_ended' ), 10, 2 );
		add_action( 'scheduled_subscription_payment', array( $this, 'subscription_renewed' ), 10, 2 );
		add_action( 'switched_subscription', array( $this, 'switched_subscription' ), 10, 3 );
	}

	/**
	 * Is this a subscription product?
	 * @return bool
	 */
	public function woocommerce_is_subscription( $is_subscription, $product_id ) {
		$product = get_product( $product_id );
		if ( $product->is_type( array( 'job_package_subscription', 'resume_package_subscription' ) ) ) {
			$is_subscription = true;
		}
		return $is_subscription;
	}

	/**
	 * If a listing is expired, the pack may need it's listing count changing
	 */
	public function check_expired_listing( $post ) {
		global $wpdb;

		if ( 'job_listing' === $post->post_type || 'resume' === $post->post_type ) {
			$package_product_id = get_post_meta( $post->ID, '_package_id', true );
			$package_id         = get_post_meta( $post->ID, '_user_package_id', true );
			$package_product    = get_post( $package_product_id );

			if ( $package_product_id ) {
				$subscription_type = get_post_meta( $package_product_id, '_package_subscription_type', true );
				$subscription_type = empty( $subscription_type ) ? 'package' : $subscription_type;

				if ( 'listing' === $subscription_type ) {
					$new_count = $wpdb->get_var( $wpdb->prepare( "SELECT package_count FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
					$new_count --;

					$wpdb->update(
						"{$wpdb->prefix}wcpl_user_packages",
						array(
							'package_count'  => max( 0, $new_count )
						),
						array(
							'id' => $package_id
						)
					);

					// Remove package meta after adjustment
					delete_post_meta( $post->ID, '_package_id' );
					delete_post_meta( $post->ID, '_user_package_id' );
				}
			}
		}
	}

	/**
	 * If a listing gets trashed/deleted, the pack may need it's listing count changing
	 */
	public function wp_trash_post( $id ) {
		global $wpdb;

		if ( $id > 0 ) {
			$post_type = get_post_type( $id );

			if ( 'job_listing' === $post_type || 'resume' === $post_type ) {
				$package_product_id = get_post_meta( $id, '_package_id', true );
				$package_id         = get_post_meta( $id, '_user_package_id', true );
				$package_product    = get_post( $package_product_id );

				if ( $package_product_id ) {
					$subscription_type = get_post_meta( $package_product_id, '_package_subscription_type', true );
					$subscription_type = empty( $subscription_type ) ? 'package' : $subscription_type;

					if ( 'listing' === $subscription_type ) {
						$new_count = $wpdb->get_var( $wpdb->prepare( "SELECT package_count FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
						$new_count --;

						$wpdb->update(
							"{$wpdb->prefix}wcpl_user_packages",
							array(
								'package_count'  => max( 0, $new_count )
							),
							array(
								'id' => $package_id
							)
						);
					}
				}
			}
		}
	}

	/**
	 * If a listing gets restored, the pack may need it's listing count changing
	 */
	public function untrash_post( $id ) {
		global $wpdb;

		if ( $id > 0 ) {
			$post_type = get_post_type( $id );

			if ( 'job_listing' === $post_type || 'resume' === $post_type ) {
				$package_product_id = get_post_meta( $id, '_package_id', true );
				$package_id         = get_post_meta( $id, '_user_package_id', true );
				$package_product    = get_post( $package_product_id );

				if ( $package_product_id ) {
					$subscription_type = get_post_meta( $package_product_id, '_package_subscription_type', true );
					$subscription_type = empty( $subscription_type ) ? 'package' : $subscription_type;

					if ( 'listing' === $subscription_type ) {
						$package  = $wpdb->get_row( $wpdb->prepare( "SELECT package_count, package_limit FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
						$new_count = $package->package_count + 1;

						$wpdb->update(
							"{$wpdb->prefix}wcpl_user_packages",
							array(
								'package_count'  => min( $package->package_limit, $new_count )
							),
							array(
								'id' => $package_id
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Subscription has expired - cancel job packs
	 */
	public function subscription_ended( $user_id, $subscription_key ) {
		global $wpdb;

		// Get subscription details
		$subscription      = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		$subscription_type = get_post_meta( $subscription['product_id'], '_package_subscription_type', true );
		$subscription_type = empty( $subscription_type ) ? 'package' : $subscription_type;

		// Get the user package
		$user_package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE user_id = %d AND order_id = %d AND product_id = %d;", $user_id, $subscription['order_id'], $subscription['product_id'] ) );

		if ( $user_package ) {
			// Delete the package
			$wpdb->delete(
				"{$wpdb->prefix}wcpl_user_packages",
				array(
					'id' => $user_package->id
				)
			);

			// Expire listings posted with package
			if ( 'listing' === $subscription_type ) {
				$listing_ids = $wpdb->get_col( $wpdb->prepare( "
					SELECT post_id FROM {$wpdb->postmeta}
					LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
					WHERE meta_key = '_user_package_id'
					AND meta_value = %s
					AND post_author = %d;
				", $user_package->id, $user_id ) );

				foreach ( $listing_ids as $listing_id ) {
					$listing = array( 'ID' => $listing_id, 'post_status' => 'expired' );
					wp_update_post( $listing );
				}
			}
		}
	}

	/**
	 * Subscription term renewed - renew the job pack
	 */
	public function subscription_renewed( $user_id, $subscription_key ) {
		global $wpdb;

		// Get subscription details
		$subscription      = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		$subscription_type = get_post_meta( $subscription['product_id'], '_package_subscription_type', true );
		$subscription_type = empty( $subscription_type ) ? 'package' : $subscription_type;

		// Renew Packs only - listing subscriptions remain active
		if ( 'package' === $subscription_type ) {
			if ( ! $wpdb->update(
				"{$wpdb->prefix}wcpl_user_packages",
				array(
					'package_count'  => 0
				),
				array(
					'user_id'    => $user_id,
					'order_id'   => $subscription['order_id'],
					'product_id' => $subscription['product_id']
				)
			) ) {
				wc_paid_listings_give_user_package( $user_id, $subscription['product_id'], $subscription['order_id'] );
			}
		}
	}

	/**
	 * When switching a subscription we need to update old listings.
	 *
	 * No need to give the user a new package; thats still handled by the orders class.
	 */
	public function switched_subscription( $user_id, $original_subscription_key, $new_subscription_key ) {
		global $wpdb;

		// Get subscription details
		$old_subscription      = WC_Subscriptions_Manager::get_subscription( $original_subscription_key );
		$old_subscription_type = get_post_meta( $old_subscription['product_id'], '_package_subscription_type', true );
		$old_subscription_type = empty( $old_subscription_type ) ? 'package' : $old_subscription_type;

		$new_subscription      = WC_Subscriptions_Manager::get_subscription( $new_subscription_key );
		$new_subscription_type = get_post_meta( $new_subscription['product_id'], '_package_subscription_type', true );
		$new_subscription_type = empty( $new_subscription_type ) ? 'package' : $new_subscription_type;

		// Get the user package
		$user_package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE user_id = %d AND order_id = %d AND product_id = %d;", $user_id, $old_subscription['order_id'], $old_subscription['product_id'] ) );

		if ( $user_package ) {
			$switching_to_package = get_product( $new_subscription['product_id'] );

			// If invalid, abort
			if ( ! $switching_to_package->is_type( array( 'job_package', 'resume_package', 'job_package_subscription', 'resume_package_subscription' ) ) ) {
				return false;
			}

			$switching_to_package_id = wc_paid_listings_give_user_package( $user_id, $new_subscription['product_id'], $new_subscription['order_id'] );

			// Ensure package is not given twice
			update_post_meta( $new_subscription['order_id'], 'wc_paid_listings_packages_processed', true );

			// Upgrade?
			$upgrading = $switching_to_package->get_limit() >= $user_package->package_limit;

			// Delete the old package
			$wpdb->delete(
				"{$wpdb->prefix}wcpl_user_packages",
				array(
					'id' => $user_package->id
				)
			);

			// Update old listings
			if ( 'listing' === $new_subscription_type && $switching_to_package_id ) {
				$listing_ids = $wpdb->get_col( $wpdb->prepare( "
					SELECT post_id FROM {$wpdb->postmeta}
					LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
					WHERE meta_key = '_user_package_id'
					AND meta_value = %s;
				", $user_package->id ) );

				foreach ( $listing_ids as $listing_id ) {
					// If we are not upgrading, expire the old listing
					if ( ! $upgrading ) {
						$listing = array( 'ID' => $listing_id, 'post_status' => 'expired' );
						wp_update_post( $listing );
					} else {
						wc_paid_listings_increase_package_count( $user_id, $switching_to_package_id );
						// Change the user package ID and package ID
						update_post_meta( $listing_id, '_user_package_id', $switching_to_package_id );
						update_post_meta( $listing_id, '_package_id', $new_subscription['product_id'] );
					}

					// Featured or not
					update_post_meta( $listing_id, '_featured', $switching_to_package->is_featured() ? 1 : 0 );

					// Fire action
					do_action( 'wc_paid_listings_switched_subscription', $listing_id, $user_package );
				}
			}
		}
	}
}
WC_Paid_Listings_Subscriptions::get_instance();
