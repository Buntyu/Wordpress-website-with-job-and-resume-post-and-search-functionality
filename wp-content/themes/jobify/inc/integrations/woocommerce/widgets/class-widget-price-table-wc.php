<?php
/**
 * Price Table for WooCommerce Paid Listings
 *
 * Automatically populated with subscriptions.
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Price_Table_WC extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_price_table_wc';
		$this->widget_description = __( 'Outputs Job Packages from WooCommerce', 'jobify' );
		$this->widget_id          = 'jobify_widget_price_table_wc';
		$this->widget_name        = __( 'Jobify - Home: WooCommerce Packages', 'jobify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Plans and Pricing', 'jobify' ),
				'label' => __( 'Title:', 'jobify' )
			),
			'orderby' => array(
				'label' => __( 'Order By:', 'jobify' ),
				'type' => 'select',
				'std'  => 'id',
				'options' => array(
					'id'   => __( 'ID', 'jobify' ),
					'title' => __( 'Title', 'jobify' ),
					'menu_order' => __( 'Menu Order', 'jobify' )
				)
			),
			'order' => array(
				'label' => __( 'Order:', 'jobify' ),
				'type' => 'select',
				'std'  => 'desc',
				'options' => array(
					'desc'   => __( 'DESC', 'jobify' ),
					'asc' => __( 'ASC', 'jobify' )
				)
			),
			'packages' => array(
				'label' => __( 'Package Type:', 'jobify' ),
				'type' => 'select',
				'std' => 'job_package',
				'options' => array(
					'job_package' => __( 'Job Packages', 'jobify' ),
					'resume_package' => __( 'Resume Packages', 'jobify' )
				)
			),
			'description' => array(
				'type'  => 'textarea',
				'rows'  => 4,
				'std'   => '',
				'label' => __( 'Description:', 'jobify' ),
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

		$title        = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$description  = $instance[ 'description' ];

		$orderby = isset( $instance[ 'orderby' ] ) ? $instance[ 'orderby' ] : 'id';
		$order = isset( $instance[ 'order' ] ) ? $instance[ 'order' ] : 'desc';
		$packages = isset( $instance[ 'packages' ] ) ? esc_attr( $instance[ 'packages' ] ) : 'job_package';

		$type = 'job_package' == $packages ? 'job_listing' : 'resume';
		$obj  = get_post_type_object( $type );

		$packages     = get_posts( array(
			'post_type'  => 'product',
			'posts_per_page' => -1,
			'tax_query'  => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( $packages, $packages . '_subscription' )
				)
			),
			'meta_query' => array(
				array(
					'key'     => '_visibility',
					'value'   => array( 'catalog', 'visible' ),
					'compare' => 'IN'
				)
			),
			'orderby' => $orderby,
			'order'   => $order,
			'suppress_filters' => 0
		) );

		if ( ! $packages || ! defined( 'JOB_MANAGER_WCPL_VERSION' ) ) {
			return;
		}

		$content = ob_get_clean();

		echo $before_widget;
		?>

		<div class="container">

			<?php if ( $title ) echo $before_title . $title . $after_title; ?>

			<?php if ( $description ) : ?>
				<p class="homepage-widget-description"><?php echo $description; ?></p>
			<?php endif; ?>

			<div class="row pricing-table-wrapper" data-columns>

				<?php foreach ( $packages as $key => $package ) : $product = get_product( $package ); ?>
					<div class="pricing-table-widget-wrapper">
						<div class="pricing-table-widget">
							<div class="pricing-table-widget-title">
								<?php echo get_post_field( 'post_title', $package ); ?>
							</div>

							<div class="pricing-table-widget-description">
								<h2><?php echo $product->get_price_html(); ?></h2>

								<p><span class="rcp_level_duration">
									<?php
										printf( _n( '%s for %d job', '%s for %s jobs', $product->get_limit(), 'jobify' ) . ' ', $product->get_price_html(), $product->get_limit() ? $product->get_limit() : __( 'unlimited', 'wp-job-manager-wc-paid-listings' ) );

										if ( $product->get_duration() ) {
											printf( _n( 'listed for %s day', 'listed for %s days', $product->get_duration(), 'jobify' ), $product->get_duration() );
										}
									?>
								</span></p>

								<?php echo apply_filters( 'the_content', get_post_field( 'post_content', $product->id ) ); ?>

								<p>
									<?php
										$link 	= $product->add_to_cart_url();
										$label 	= apply_filters( 'add_to_cart_text', __( 'Add to Cart', 'jobify' ) );
									?>
									<a href="<?php echo esc_url( $link ); ?>" class="button-secondary"><?php echo $label; ?></a>
								</p>
							</div>
						</div>
					</div>

				<?php endforeach; ?>

			</div>

		</div>

		<?php
		echo $after_widget;

		echo $content;

		$this->cache_widget( $args, $content );
	}
}
