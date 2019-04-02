<?php
/**
 * Job/Resume: Share
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Share extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_share';
		$this->widget_description = __( 'Display job/resume sharing options', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_share';
		$this->widget_name        = __( 'Jobify - Job/Resume: Share', 'jobify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'jobify' )
			)
		);

		$this->settings = jobify_rcp_subscription_selector( $this->settings );

		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) )
			return;

		ob_start();

		extract( $args );

		$title = apply_filters( 'widget_title', isset ( $instance[ 'title' ] ) ? $instance[ 'title' ] : '', $instance, $this->id_base );

		echo $before_widget;

			if ( $title ) echo $before_title . $title . $after_title;

			do_action( 'jobify_share_object' );

		echo $after_widget;

		$content = apply_filters( 'jobify_widget_job_share', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}
