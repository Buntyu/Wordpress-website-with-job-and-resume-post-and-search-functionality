<?php
/**
 * Home: Job Spotlight
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Jobs_Spotlight extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_jobs_spotlight';
		$this->widget_description = __( 'Output a grid of spotlighted jobs.', 'jobify' );
		$this->widget_id          = 'jobify_widget_jobs_spotlight';
		$this->widget_name        = __( 'Jobify - Home: Jobs Spotlight', 'jobify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Job Spotlight', 'jobify' ),
				'label' => __( 'Title:', 'jobify' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of jobs to show:', 'jobify' )
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

		$title   = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$number  = $instance[ 'number' ];
		$count   = 1;

		$spotlight = new WP_Query( array(
			'post_type' => 'job_listing',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key'   => '_featured',
					'value' => 1
				)
			),
			'posts_per_page' => $number,
			'orderby' => 'rand',
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		) );

		if ( ! $spotlight->have_posts() ) {
			return;
		}

		echo $before_widget;
		?>

			<div class="container">

				<?php if ( $title ) echo $before_title . $title . $after_title;  ?>

				<div class="row">

					<?php while ( $spotlight->have_posts() ) : $spotlight->the_post(); ?>

						<div class="job-spotlight col-md-4 col-sm-6 col-xs-12">
							<?php
								add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
								get_template_part( 'content', 'single-job-featured' );
								remove_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
							?>
						</div>

						<?php if ( $count % 3 == 0 ) : ?></div><div class="row"><?php endif; ?>

					<?php $count++; endwhile; ?>

				</div>

			</div>

		<?php
		echo $after_widget;

		$content = apply_filters( $this->widget_id, ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}

	public function excerpt_length() {
		return 20;
	}
}