<?php
/**
 * Job Content
 *
 * @package Jobify
 * @since Jobify 1.0
 */

global $job_manager;

$info         = jobify_theme_mod( 'jobify_listings', 'jobify_listings_display_area' );
$col_overview = 'top' == $info ? '12' : ( ! jobify_get_the_company_description() ? '10' : '6' );
$col_company  = 'top' == $info ? '12' : '4';
?>

<div class="single_job_listing" itemscope itemtype="http://schema.org/JobPosting">
	<meta itemprop="title" content="<?php echo esc_attr( $post->post_title ); ?>" />

	<?php if ( $post->post_status == 'expired' ) : ?>

		<div class="job-manager-info"><?php _e( 'This job listing has expired', 'jobify' ); ?></div>

	<?php else : ?>

		<?php if ( is_position_filled() ) : ?>
			<div class="job-manager-error"><?php _e( 'This position has been filled', 'jobify' ); ?></div>
		<?php endif; ?>

		<?php do_action( 'single_job_listing_start' ); ?>

		<?php locate_template( array( 'sidebar-single-job_listing-top.php' ), true, false ); ?>

		<div class="job-overview-content row">
			<div itemprop="description" class="job-overview col-md-<?php echo $col_overview; ?> col-sm-12">
				<h2 class="job-overview-title"><?php _e( 'Overview', 'jobify' ); ?></h2>

				<?php echo apply_filters( 'the_job_description', get_the_content() ); ?>
			</div>

			<?php if ( jobify_get_the_company_description() ) : ?>
			<div itemscope itemtype="http://data-vocabulary.org/Organization" class="job-company-about col-md-<?php echo $col_company; ?> <?php echo 'top' == $info ? 'col-md-12' : 'col-sm-6 col-xs-12'; ?>">
				<h2 class="job-overview-title" itemprop="name"><?php printf( __( 'About %s', 'jobify' ), get_the_company_name() ); ?></h2>

				<?php jobify_the_company_description(); ?>
			</div>
			<?php endif; ?>

			<?php locate_template( array( 'sidebar-single-job_listing.php' ), true, false ); ?>
		</div>

		<?php do_action( 'single_job_listing_end' ); ?>

	<?php endif; ?>
</div>