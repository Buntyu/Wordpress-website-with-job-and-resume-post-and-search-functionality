<?php
/**
 * Home: Recent Resumes
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Resumes extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_resumes';
		$this->widget_description = __( 'Output a list of recent resumes.', 'jobify' );
		$this->widget_id          = 'jobify_widget_resumes';
		$this->widget_name        = __( 'Jobify - Home: Recent Resumes', 'jobify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => 'Recent Resumes',
				'label' => __( 'Title:', 'jobify' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of resumes to show:', 'jobify' )
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

		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$number = $instance[ 'number' ];

		echo $before_widget;
		?>

			<div class="container">

				<div class="recent-resumes">
					<?php
						if ( $title ) echo $before_title . $title . $after_title;
						echo do_shortcode( '[resumes show_filters=0 per_page=' . $number . ']' );
					?>
				</div>

			</div>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_resumes', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}