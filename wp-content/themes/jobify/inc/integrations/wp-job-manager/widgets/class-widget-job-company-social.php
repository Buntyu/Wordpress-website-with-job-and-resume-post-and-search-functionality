<?php
/**
 * Job: Company Social
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Company_Social extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_company_social';
		$this->widget_description = __( 'Display the job\'s company social profiles', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_company_social';
		$this->widget_name        = __( 'Jobify - Job: Company Social', 'jobify' );
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

		<ul class="company-social">
			<?php do_action( 'job_listing_company_social_before' ); ?>

			<?php if ( get_the_company_website() ) : ?>
			<li><a href="<?php echo get_the_company_website(); ?>" target="_blank" itemprop="url">
				<i class="icon-link"></i>
				<?php _e( 'Website', 'jobify' ); ?>
			</a></li>
			<?php endif; ?>

			<?php if ( get_the_company_twitter() ) : ?>
			<li><a href="http://twitter.com/<?php echo get_the_company_twitter(); ?>">
				<i class="icon-twitter"></i>
				<?php _e( 'Twitter', 'jobify' ); ?>
			</a></li>
			<?php endif; ?>

			<?php if ( jobify_get_the_company_facebook() ) : ?>
			<li><a href="http://facebook.com/<?php echo jobify_get_the_company_facebook(); ?>">
				<i class="icon-facebook"></i>
				<?php _e( 'Facebook', 'jobify' ); ?>
			</a></li>
			<?php endif; ?>

			<?php if ( jobify_get_the_company_gplus() ) : ?>
			<li><a href="http://plus.google.com/<?php echo jobify_get_the_company_gplus(); ?>">
				<i class="icon-gplus"></i>
				<?php _e( 'Google+', 'jobify' ); ?>
			</a></li>
			<?php endif; ?>

			<?php if ( jobify_get_the_company_linkedin() ) : ?>
			<li><a href="http://linkedin.com/company/<?php echo jobify_get_the_company_linkedin(); ?>">
				<i class="icon-linkedin"></i>
				<?php _e( 'LinkedIn', 'jobify' ); ?>
			</a></li>
			<?php endif; ?>

			<?php do_action( 'job_listing_company_social_after' ); ?>
		</ul>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_job_company_social', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}