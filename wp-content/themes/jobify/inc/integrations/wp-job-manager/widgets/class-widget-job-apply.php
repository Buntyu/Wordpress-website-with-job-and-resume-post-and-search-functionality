<?php
/**
 * Job/Resume: Actions
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Apply extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_apply';
		$this->widget_description = __( 'Display the job/resume action buttons', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_apply';
		$this->widget_name        = __( 'Jobify - Job/Resume: Actions', 'jobify' );
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

		global $post;

		if ( 'preview' == $post->post_status ) {
			return;
		}

		$title = apply_filters( 'widget_title', isset ( $instance[ 'title' ] ) ? $instance[ 'title' ] : '', $instance, $this->id_base );

		echo $before_widget;
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<?php if ( 'job_listing' == get_post_type() ) : ?>

			<?php if ( ! is_position_filled() ) : ?>
				<?php get_job_manager_template( 'job-application.php' ); ?>
			<?php endif; ?>

			<?php if ( '' != get_the_company_video() ) : ?>

				<a href="#company-video" class="button view-video popup-trigger"><?php _e( 'Watch Video', 'jobify' ); ?></a>

				<div id="company-video" class="modal">
					<?php the_company_video(); ?>
				</div>

			<?php endif; ?>

		<?php else : ?>

			<?php get_job_manager_template( 'contact-details.php', array( 'post' => $post ), 'resume_manager', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

			<?php if ( '' != get_the_candidate_video() ) : ?>

				<a href="#candidate-video" class="button view-video popup-trigger"><?php _e( 'Video Resume', 'jobify' ); ?></a>

				<div id="candidate-video" class="modal">
					<?php the_candidate_video(); ?>
				</div>

			<?php endif; ?>

		<?php endif; ?>

		<?php do_action( 'jobify_widget_job_apply_after' ); ?>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( 'jobify_widget_job_apply', $content, $instance, $args );

		$this->cache_widget( $args, $content );
	}
}