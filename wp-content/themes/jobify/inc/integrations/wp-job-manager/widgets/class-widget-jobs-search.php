<?php
/**
 * Home: Jobs Search
 *
 * @since Jobify 1.7.0
 */
class Jobify_Widget_Jobs_Search extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_jobs_search';
		$this->widget_description = __( 'Output search options to search jobs.', 'jobify' );
		$this->widget_id          = 'jobify_widget_jobs_search';
		$this->widget_name        = __( 'Jobify - Home: Jobs Search', 'jobify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Search Jobs', 'jobify' ),
				'label' => __( 'Title:', 'jobify' )
			)
		);

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

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		?>

			<div class="container">

				<?php if ( $title ) echo $before_title . $title . $after_title; ?>

				<?php do_action( 'jobify_output_job_results' ); ?>

			</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( 'jobify_widget_jobs_search', $content );

		$this->cache_widget( $args, $content );
	}
}