<?php
/**
 * Resume: Links
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Resume_Links extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_resume_links';
		$this->widget_description = __( 'Display the resume\'s links', 'jobify' );
		$this->widget_id          = 'jobify_widget_resume_links';
		$this->widget_name        = __( 'Jobify - Resume: Links', 'jobify' );
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

		if ( ! $items )
			return;

		echo $before_widget;
		?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>

		<ul class="resume-links">
			<?php foreach( $items as $item ) :
				$parsed_url = parse_url( $item['url'] );
				$host = '';

				if ( isset( $parsed_url[ 'host' ] ) ) {
					$host = current( explode( '.', $parsed_url['host'] ) );
				}
			?>
				<li class="resume-link resume-link-<?php echo esc_attr( sanitize_title( $host ) ); ?>"><a rel="nofollow" href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_resume_links', ob_get_clean(), $instance, $args );

		echo $content;

		$this->cache_widget( $args, $content );
	}
}