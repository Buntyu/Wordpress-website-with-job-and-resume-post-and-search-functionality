<?php
/**
 * WooCommerce Job Packages
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jobify
 * @since Jobify 1.0
 */

global $wp_query;

$packages = new WP_Query( array(
	'post_type'  => 'product',
	'posts_per_page' => -1,
	'tax_query'  => array(
		array(
			'taxonomy' => 'product_type',
			'field'    => 'slug',
			'terms'    => array( 'job_package', 'job_package_subscription' )
		)
	),
	'meta_query' => array(
		array(
			'key'     => '_visibility',
			'value'   => array( 'catalog', 'visible' ),
			'compare' => 'IN'
		)
	)
) );

get_header(); ?>

	<header class="page-header">
<!--<h3 style="color: #ff1818;">BOGO Buy Any Package and Get Second One FREE  Of Equal Value</h3>-->
		<h1 class="page-title"><?php _e( 'Job Packages', 'jobify' ); ?></h1>
	</header>

	<div id="primary" class="content-area">
		<div id="content" class="container" role="main">

			<?php
				/**
				 * woocommerce_before_shop_loop hook
				 *
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
			?>

			<div class="job-packages row">

				<?php while ( $packages->have_posts() ) : $packages->the_post(); $package = get_product( get_post()->ID ); ?>
					<div class="col-lg-4 col-md-6 col-sm-12 pricing-table-widget-wrapper">
						<div class="pricing-table-widget woocommerce">
							<div class="pricing-table-widget-title">
								<?php the_title(); ?>
							</div>

							<div class="pricing-table-widget-description">
								<h2><?php echo $package->get_price_html(); ?></h2>

								<p><span class="rcp_level_duration">
									<?php
										$limit = $package->get_limit();
										$duration = $package->get_duration();

										echo _n(
											sprintf(
												'%1$s %2$s %3$s',
												$limit == 0 ? __( 'Unlimited', 'jobify' ) : $limit,
												$obj->labels->singular_name,
												$duration == 0 ? __( 'forever', 'jobify' ) : sprintf( 'for %d days',
												$duration )
											),
											sprintf(
												'%1$s %2$s %3$s',
												$limit == 0 ? __( 'Unlimited', 'jobify' ) : $limit,
												$obj->labels->name,
												$duration == 0 ? __( 'forever', 'jobify' ) : sprintf( 'for %d days',
												$duration )
											),
											$limit,
											'jobify'
										) . ' ';
									?>
								</span></p>

								<?php the_content(); ?>

								<p>
									<?php
										$link 	= $package->add_to_cart_url();
										$label 	= apply_filters( 'add_to_cart_text', __( 'Add to cart', 'jobify' ) );
									?>
									<a href="<?php echo esc_url( $link ); ?>" class="button-secondary"><?php echo $label; ?></a>
								</p>
							</div>
						</div>
					</div>

				<?php endwhile; ?>

				</div>

			</div>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>