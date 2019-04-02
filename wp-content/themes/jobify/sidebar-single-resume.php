<?php
/**
 *
 */

if ( 'top' == jobify_theme_mod( 'jobify_listings', 'jobify_listings_display_area' ) )
	return;

$args = array(
	'before_widget' => '<aside class="job_listing-widget default-widget">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h3 class="job_listing-widget-title">',
	'after_title'   => '</h3>'
);
?>

<?php do_action( 'single_resume_info_start' ); ?>

<div class="job-meta col-md-2 col-sm-4 col-xs-12">

	<?php do_action( 'single_resume_info_before' ); ?>

	<?php if ( ! is_active_sidebar( 'sidebar-single-resume' ) ) : ?>

		<?php the_widget( 'Jobify_Widget_Job_Company_Logo', array(), $args ); ?>

		<?php the_widget( 'Jobify_Widget_Job_Apply', array(), $args ); ?>

		<?php the_widget( 'Jobify_Widget_Resume_Links', array(), $args ); ?>

		<?php if ( get_option( 'resume_manager_enable_resume_upload' ) ) : ?>
			<?php the_widget( 'Jobify_Widget_Resume_File', array(), $args ); ?>
		<?php endif; ?>

		<?php if ( get_option( 'resume_manager_enable_skills' ) ) : ?>
			<?php the_widget( 'Jobify_Widget_Resume_Skills', array( 'title' => __( 'Candidate Skills', 'jobify' ) ), $args ); ?>
		<?php endif; ?>

	<?php else : ?>
		<?php dynamic_sidebar( 'sidebar-single-resume' ); ?>
	<?php endif; ?>

	<?php do_action( 'single_resume_info_after' ); ?>

</div>

<?php do_action( 'single_resume_info_end' ); ?>