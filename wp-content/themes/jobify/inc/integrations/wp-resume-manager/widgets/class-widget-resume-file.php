<?php
/**
 * Resume: File
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Resume_File extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_resume_file';
		$this->widget_description = __( 'Display the resume\'s file', 'jobify' );
		$this->widget_id          = 'jobify_widget_resume_file';
		$this->widget_name        = __( 'Jobify - Resume: File', 'jobify' );
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
		$resume_file = get_post_meta( $post->ID, '_resume_file', true );

		if ( ! $resume_file ) {
			return;
		}

		echo $before_widget;
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<a rel="nofollow" href="<?php echo function_exists( 'get_resume_file_download_url' ) ?
		esc_url( get_resume_file_download_url( $post->ID ) ) : esc_url( $resume_file ); ?>" class="resume-file resume-file-<?php echo substr( strrchr( $resume_file, '.' ), 1 ); ?>"><?php printf( __( 'Resume.%s', 'jobify' ), substr( strrchr( $resume_file, '.' ), 1 ) ); ?></a>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_resume_file', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}
