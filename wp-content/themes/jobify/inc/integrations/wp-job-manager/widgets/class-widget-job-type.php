<?php
/**
 * Job: Type
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Type extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_type';
		$this->widget_description = __( 'Display the job type', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_type';
		$this->widget_name        = __( 'Jobify - Job: Type', 'jobify' );
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
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<div class="job-type <?php echo get_the_job_type()->slug; ?> term-<?php echo get_the_job_type() ? sanitize_title( get_the_job_type()->term_id ) : ''; ?>"><?php the_job_type(); ?></div>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_job_type', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}