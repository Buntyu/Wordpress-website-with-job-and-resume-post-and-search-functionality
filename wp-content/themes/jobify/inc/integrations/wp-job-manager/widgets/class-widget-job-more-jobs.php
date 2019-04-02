<?php
/**
 * Job: Company Listings
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_More_Jobs extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_more_jobs';
		$this->widget_description = __( 'Display a link to more jobs from the company', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_more_jobs';
		$this->widget_name        = __( 'Jobify - Job: Company Listings', 'jobify' );
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

		if ( ! class_exists( 'Astoundify_Job_Manager_Companies' ) )
			return;

		$title       = apply_filters( 'widget_title', isset ( $instance[ 'title' ] ) ? $instance[ 'title' ] : '', $instance, $this->id_base );
		$companies   = Astoundify_Job_Manager_Companies::instance();
		$company_url = esc_url( $companies->company_url( get_the_company_name() ) );

		echo $before_widget;
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<a href="<?php echo $company_url; ?>" title="<?php printf( __( 'More jobs by %s', 'jobify' ), get_the_company_name() ); ?>"><i class="icon-newspaper"></i> <?php _e( 'More Jobs', 'jobify' ); ?></a>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_job_more_jobs', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}