<?php
/**
 * Job: Type
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Deadline extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_application_deadline';
		$this->widget_description = __( 'Display the job application deadline', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_application_deadline';
		$this->widget_name        = __( 'Jobify - Job: Application Deadline', 'jobify' );
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

		$title    = apply_filters( 'widget_title', isset ( $instance[ 'title' ] ) ? $instance[ 'title' ] : '', $instance, $this->id_base );
		$deadline = get_post_meta( $post->ID, '_application_deadline', true );

		$expiring = ( floor( ( time() - strtotime( $deadline ) ) / ( 60 * 60 * 24 ) ) >= -2 );
		$expired  = ( floor( ( time() - strtotime( $deadline ) ) / ( 60 * 60 * 24 ) ) >= 0 );

		if ( ! $deadline ) {
			return;
		}

		echo $before_widget;
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<div class="application-deadline <?php echo $expiring ? 'expiring' : ''; ?><?php echo $expired ? 'expired' : ''; ?>">
			<?php echo date_i18n( __( 'M j, Y', 'jobify' ), strtotime( $deadline ) ); ?>
		</div>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_job_type', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}