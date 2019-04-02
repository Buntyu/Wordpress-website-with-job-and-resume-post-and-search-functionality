<?php
/**
 * Job Package Product Type
 */
class WC_Product_Job_Package extends WC_Product {

	/**
	 * Constructor
	 */
	public function __construct( $product ) {
		$this->product_type = 'job_package';
		parent::__construct( $product );
	}

	/**
	 * We want to sell jobs one at a time
	 * @return boolean
	 */
	public function is_sold_individually() {
		return apply_filters( 'wcpl_' . $this->product_type . '_is_sold_individually', true );
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
	 * Get the add to cart button text
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_text() {
		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add to cart', 'wp-job-manager-wc-paid-listings' ) : __( 'Read More', 'wp-job-manager-wc-paid-listings' );

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Job Packages can always be purchased regardless of price.
	 * @return boolean
	 */
	public function is_purchasable() {
		return true;
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
		if ( $this->job_listing_duration )
			return $this->job_listing_duration;
		else
			return get_option( 'job_manager_submission_duration' );
	}

	/**
	 * Return job listing limit
	 * @return int 0 if unlimited
	 */
	public function get_limit() {
		if ( $this->job_listing_limit )
			return $this->job_listing_limit;
		else
			return 0;
	}

	/**
	 * Return if featured
	 * @return int 0 if unlimited
	 */
	public function is_featured() {
		return $this->job_listing_featured === 'yes';
	}

	/**
	 * Get product id
	 * @return int
	 */
	public function get_product_id() {
		return $this->id;
	}
}