<?php
/**
 * Resume: Categories
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Resume_Categories extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_resume_categories';
		$this->widget_description = __( 'Display the resume\'s categories', 'jobify' );
		$this->widget_id          = 'jobify_widget_resume_categories';
		$this->widget_name        = __( 'Jobify - Resume: Categories', 'jobify' );
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

		$title = apply_filters( 'widget_title', isset ( $instance[ 'title' ] ) ? $instance[ 'title' ] : '', $instance, $this->id_base );
		$items = get_post_meta( $post->ID, '_links', true );

		if ( ! get_the_resume_category() )
			return;

		$categories = get_the_terms( $post->ID, 'resume_category' );

		echo $before_widget;
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<ul class="resume-categories">
			<?php foreach ( $categories as $category ) : ?>
				<li class="resume-category">
					<a href="<?php echo get_term_link( $category, 'resume_category' ); ?>"><i class="icon-book-open"></i> <?php echo $category->name; ?></a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_resume_categories', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}