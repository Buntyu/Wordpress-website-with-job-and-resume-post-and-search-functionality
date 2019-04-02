<?php
/**
 * Job/Resume: Logo
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Company_Logo extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_company_logo';
		$this->widget_description = __( 'Display the company logo or resume picture', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_company_logo';
		$this->widget_name        = __( 'Jobify - Job/Resume: Logo', 'jobify' );
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

		global $wp_embed;

		ob_start();

		extract( $args );

		global $post;

		$title = apply_filters( 'widget_title', isset ( $instance[ 'title' ] ) ? $instance[ 'title' ] : '', $instance, $this->id_base );

		echo $before_widget;
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<?php if ( 'job_listing' == get_post_type() ) : ?>

			<?php
				if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != get_the_company_name() ) :
					$companies   = Astoundify_Job_Manager_Companies::instance();
					$company_url = esc_url( $companies->company_url( get_the_company_name() ) );
			?>
			<a href="<?php echo $company_url; ?>" target="_blank"><?php the_company_logo(); ?></a>
			<?php else : ?>
				<?php the_company_logo(); ?>
			<?php endif; ?>

		<?php else : ?>

			<?php the_candidate_photo( 'large' ); ?>

		<?php endif; ?>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_job_company_logo', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}