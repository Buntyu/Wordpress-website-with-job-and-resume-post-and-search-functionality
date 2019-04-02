<?php
/**
 * Resume Package Product Type
 */
class WC_Product_Resume_Package_Subscription extends WC_Product_Subscription {

	/**
	 * Constructor
	 */
	public function __construct( $product ) {
		parent::__construct( $product );
	}

	/**
	 * Checks the product type.
	 *
	 * Backwards compat with downloadable/virtual.
	 *
	 * @access public
	 * @param mixed $type Array or string of types
	 * @return bool
	 */
	public function is_type( $type ) {
		return ( 'resume_package_subscription' == $type || ( is_array( $type ) && in_array( 'resume_package_subscription', $type ) ) ) ? true : parent::is_type( $type );
	}

	/**
	 * We want to sell jobs one at a time
	 * @return boolean
	 */
	public function is_sold_individually() {
		return true;
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_url() {
		$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Jobs are always virtual
	 * @return boolean
	 */
	public function is_virtual() {
		return true;
	}

	/**
	 * Return job listing duration granted
	 * @return int
	 */
	public function get_duration() {
		if ( 'listing' === $this->package_subscription_type ) {
			return false;
		} elseif ( $this->resume_duration ) {
			return $this->resume_duration;
		} else {
			return get_option( 'resume_manager_submission_duration' );
		}
	}

	/**
	 * Return job listing limit
	 * @return int 0 if unlimited
	 */
	public function get_limit() {
		if ( $this->resume_limit )
			return $this->resume_limit;
		else
			return 0;
	}

	/**
	 * Return if featured
	 * @return int 0 if unlimited
	 */
	public function is_featured() {
		return $this->resume_featured === 'yes';
	}

	/**
	 * Get product id
	 * @return int
	 */
	public function get_product_id() {
		return $this->id;
	}
}