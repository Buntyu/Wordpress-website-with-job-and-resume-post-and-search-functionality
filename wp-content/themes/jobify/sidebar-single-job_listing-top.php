<?php
/**
 *
 */

if ( 'side' == jobify_theme_mod( 'jobify_listings', 'jobify_listings_display_area' ) )
	return;

$columns = jobify_theme_mod( 'jobify_listings', 'jobify_listings_topbar_columns' );
$colspan = jobify_theme_mod( 'jobify_listings', 'jobify_listings_topbar_colspan' );
$colspan = array_map( 'trim', explode( ' ', $colspan ) );

$args    = array(
	'before_widget' => '<aside class="job_listing-widget-top default-widget">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h3 class="job_listing-widget-title-top">',
	'after_title'   => '</h3>'
);
?>

<div class="job-meta-top row">

	<?php do_action( 'single_job_listing_info_before' ); ?>

	<?php for ( $i = 1; $i <= $columns; $i++ ) :?>
		<div class="col-md-<?php echo $colspan[ $i - 1 ]; ?> col-sm-6 col-xs-12">

			<?php do_action( 'single_job_listing_info_start' ); ?>

			<?php if ( ! is_active_sidebar( 'single-job_listing-top-' . $i ) ) : ?>

				<?php if ( 1 == $i ) : ?>

					<?php the_widget( 'Jobify_Widget_Job_Company_Logo', array(), $args ); ?>

				<?php elseif ( 2 == $i ) : ?>

					<?php if ( ! class_exists( 'WP_Job_Manager_Job_Tags' ) ) : ?>
						<?php the_widget( 'Jobify_Widget_Job_Categories', array( 'title' => __( 'Job Category', 'jobify' ) ), $args ); ?>
					<?php else : ?>
						<?php the_widget( 'Jobify_Widget_Job_Tags', array( 'title' => __( 'Job Tags', 'jobify' ) ), $args ); ?>
					<?php endif; ?>

					<?php the_widget( 'Jobify_Widget_Job_Share', array( 'title' => __( 'Share Job Posting', 'jobify' ) ), $args ); ?>

				<?php elseif ( 3 == $i ) : ?>

					<?php the_widget( 'Jobify_Widget_Job_Company_Social', array( 'title' => __( 'Company Details', 'jobify' ) ), $args ); ?>

					<?php the_widget( 'Jobify_Widget_Job_Apply', array(), $args ); ?>

				<?php endif; ?>

			<?php else : ?>

				<?php dynamic_sidebar( 'single-job_listing-top-' . $i ); ?>

			<?php endif; ?>

			<?php do_action( 'single_job_listing_info_end' ); ?>

		</div>
	<?php endfor; ?>

	<?php do_action( 'single_job_listing_info_after' ); ?>

</div>