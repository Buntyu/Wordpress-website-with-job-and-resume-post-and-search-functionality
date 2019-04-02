<?php
/**
 * Job: Categories
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Categories extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_categories';
		$this->widget_description = __( 'Display the job\'s categories', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_categories';
		$this->widget_name        = __( 'Jobify - Job: Categories', 'jobify' );
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

		if ( ! get_option( 'job_manager_enable_categories' ) )
			return;

		$title = apply_filters( 'widget_title', isset ( $instance[ 'title' ] ) ? $instance[ 'title' ] : '', $instance, $this->id_base );
		$categories = get_the_terms( $post->ID, 'job_listing_category' );

		if ( ! $categories )
			return;

		echo $before_widget;
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<div class="job_listing-categories">

			<?php foreach ( $categories as $category ) : ?>

				<?php if ( class_exists( 'WP_Job_Manager_Cat_Colors' ) ) : ?>

					<a href="<?php echo get_term_link( $category, 'job_listing_category' ); ?>" class="job-category <?php echo $category->slug; ?>"><?php echo $category->name; ?></a>

				<?php else : ?>

					<a href="<?php echo get_term_link( $category, 'job_listing_category' ); ?>"><i class="icon-book-open"></i> <?php echo $category->name; ?></a>

				<?php endif; ?>

			<?php endforeach; ?>

		</div>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_job_categories', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}