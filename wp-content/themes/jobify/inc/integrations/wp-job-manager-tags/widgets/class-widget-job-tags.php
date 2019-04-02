<?php
/**
 * Job: Tags
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Tags extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_tags';
		$this->widget_description = __( 'Display the job\'s tags', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_tags';
		$this->widget_name        = __( 'Jobify - Job: Tags', 'jobify' );
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
		if ( ! class_exists( 'WP_Job_Manager_Job_Tags' ) ) {
			return;
		}

		if ( $this->get_cached_widget( $args ) )
			return;

		ob_start();

		extract( $args );

		global $post;

		$title = apply_filters( 'widget_title', isset ( $instance[ 'title' ] ) ? $instance[ 'title' ] : '', $instance, $this->id_base );
		$tags  = get_the_terms( $post->ID, 'job_listing_tag' );

		if ( empty( $tags ) )
			return;

		echo $before_widget;
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<?php foreach ( $tags as $tag ) : ?>
			<a href="<?php echo get_term_link( $tag, 'job_listing_tag' ); ?>" class="job-tag"><?php echo $tag->name; ?></a>
		<?php endforeach; ?>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_job_tags', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}
