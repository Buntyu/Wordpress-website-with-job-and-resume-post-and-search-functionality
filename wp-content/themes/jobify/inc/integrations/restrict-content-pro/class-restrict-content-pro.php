<?php
/**
 * Restrict Content Pro
 */

class Jobify_Restrict_Content_Pro {

	public function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_filter( 'jobify_job_widget', array( $this, 'widget_visibility' ), 10, 3 );
	}

	/**
	 * Registers widgets, and widget areas for RCP
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	function widgets_init() {
		require_once( get_template_directory() . '/inc/integrations/restrict-content-pro/widgets/class-widget-price-table-rcp.php' );

		register_widget( 'Jobify_Widget_Price_Table_RCP' );
	}

	/**
	 * Filter Jobify widget output depending on RCP subscription level.
	 *
	 * @since Jobify 1.6.0
	 *
	 * @return $widget
	 */
	function widget_visibility( $widget, $instance, $args ) {
		extract( $args );

		if ( ! isset( $instance[ 'subscription' ] ) ) {
			return $widget;
		}

		$sub_level = maybe_unserialize( $instance[ 'subscription' ] );

		if ( ! is_array( $sub_level ) )
			$sub_level = array();

		if ( ! in_array( rcp_get_subscription_id( get_current_user_id() ), $sub_level ) && ! empty( $sub_level ) ) {
			$widget = $before_widget . $this->subscription_teaser() . $after_widget;
		}

		return $widget;
	}

	/**
	 * @unknown
	 *
	 * @since unknown
	 *
	 * @return string
	 */
	function subscription_teaser() {
		global $rcp_options;

		return apply_filters( 'jobify_rcp_paid_message_widget', $rcp_options[ 'paid_message' ] );
	}

}

$GLOBALS[ 'jobify_restrict_content_pro' ] = new Jobify_Restrict_Content_Pro();