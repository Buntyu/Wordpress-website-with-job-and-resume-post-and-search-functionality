<?php
/**
 * WooCommerce by WooThemes
 */

class Jobify_WooCommerce {

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		add_filter( 'woocommerce_show_page_title', '__return_false' );
		add_filter( 'woocommerce_enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_styles' ), 11 );
	}

	public function enqueue_styles($enqueue_styles) {
		unset( $enqueue_styles[ 'woocommerce-general' ] );

		return $enqueue_styles;
	}

	/**
	 * Sets up theme support.
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	public function add_theme_support() {
		add_theme_support( 'woocommerce' );
	}

	/**
	 * Registers widgets, and widget areas for WooCommerce
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	public function widgets_init() {
		require_once( get_template_directory() . '/inc/integrations/woocommerce/widgets/class-widget-price-table-wc.php' );

		register_widget( 'Jobify_Widget_Price_Table_WC' );
	}

	public function wp_enqueue_styles() {
		wp_dequeue_style( 'woocommerce_chosen_styles' );
	}

}

$GLOBALS[ 'jobify_woocommerce' ] = new Jobify_WooCommerce();