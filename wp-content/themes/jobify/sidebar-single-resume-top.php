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

	<?php do_action( 'single_resume_info_start' ); ?>

	<?php for ( $i = 1; $i <= $columns; $i++ ) :?>
		<div class="col-md-<?php echo $colspan[ $i - 1 ]; ?> col-sm-6 col-xs-12">

			<?php do_action( 'single_resume_info_before' ); ?>

			<?php if ( ! is_active_sidebar( 'single-resume-top-' . $i ) ) : ?>

				<?php if ( 1 == $i ) : ?>

					<?php the_widget( 'Jobify_Widget_Job_Company_Logo', array(), $args ); ?>

				<?php elseif ( 2 == $i ) : ?>

					<?php the_widget( 'Jobify_Widget_Job_Share', array( 'title' => __( 'Share Resume', 'jobify' ) ), $args ); ?>

					<?php if ( get_option( 'resume_manager_enable_skills' ) ) : ?>
						<?php the_widget( 'Jobify_Widget_Resume_Skills', array( 'title' => __( 'Candidate Skills', 'jobify' ) ), $args ); ?>

<?php endif; ?>

					<?php if ( get_option( 'resume_manager_enable_resume_upload' ) ) : ?>
						<?php the_widget( 'Jobify_Widget_Resume_File', array( 'title' => __( 'Candidate Resume', 'jobify' ) ), $args ); ?>
					<?php endif; ?>

				<?php elseif ( 3 ==  $i ) : ?>

					<?php the_widget( 'Jobify_Widget_Resume_Links', array( 'title' => __( 'Candidate Details', 'jobify' ) ), $args ); ?>

					<?php the_widget( 'Jobify_Widget_Job_Apply', array(), $args ); ?>

				<?php endif; ?>

			<?php else : ?>

				<?php dynamic_sidebar( 'single-resume-top-' . $i ); ?>

			<?php endif; ?>

			<?php do_action( 'single_resume_info_after' ); ?>

		</div>
	<?php endfor; ?>

	<?php do_action( 'single_resume_meta_end' ); ?>

</div>
