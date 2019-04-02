<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package Jobify
 * @since Jobify 1.0
 */

global $post;
?>

<div class="page-header">
	<h1 class="page-title"><?php the_title(); ?></h1>
	<h2 class="page-subtitle">
		<?php do_action( 'single_job_listing_meta_before' ); ?>

		<ul>
			<?php do_action( 'single_job_listing_meta_start' ); ?>

			<li class="job-type <?php echo get_the_job_type() ? sanitize_title( get_the_job_type()->slug ) : ''; ?>"><?php the_job_type(); ?></li>
			<li class="job-company">
				<?php
				if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != get_the_company_name() ) :
					$companies   = Astoundify_Job_Manager_Companies::instance();
					$company_url = esc_url( $companies->company_url( get_the_company_name() ) );
				?>
				<a href="<?php echo $company_url; ?>" target="_blank"><?php the_company_name(); ?></a>
				<?php else : ?>
					<?php the_company_name(); ?>
				<?php endif; ?>
			</li>
			<li class="job-location"><i class="icon-location"></i> <?php the_job_location(); ?></li>
			<li class="job-date-posted"><i class="icon-calendar"></i> <?php printf( __( 'Posted <date>%s</date> ago', 'jobify' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) ); ?></li>

			<?php do_action( 'single_job_listing_meta_end' ); ?>
		</ul>

		<?php do_action( 'single_job_listing_meta_after' ); ?>
	</h2>
</div>

<div id="content" class="container" role="main">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content">
			<?php if ( 'preview' == $post->post_status ) : ?>
				<?php get_job_manager_template_part( 'content-single', 'job_listing' ); ?>
			<?php else : ?>
				<?php the_content(); ?>
			<?php endif; ?>

			<?php get_template_part( 'content-single-job', 'related' ); ?>
		</div>
	</article><!-- #post -->
</div>
