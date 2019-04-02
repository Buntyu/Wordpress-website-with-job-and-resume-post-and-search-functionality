<?php
/**
 * Single Post
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jobify
 * @since Jobify 1.0
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<div id="primary" class="content-area">
		<?php if ( is_singular( 'job_listing' ) ) : ?>

			<?php get_template_part( 'content-single', 'job' ); ?>

		<?php elseif ( is_singular( 'resume' ) ) : ?>

			<?php if ( resume_manager_user_can_view_resume( $post->ID ) ) : ?>
				<?php get_template_part( 'content-single', 'the-resume' ); ?>
			<?php else : ?>
				<div id="content" class="container" role="main">
					<div class="entry-content">
						<?php get_job_manager_template_part( 'access-denied', 'single-resume', 'resume_manager', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>
					</div>
				</div>
			<?php endif; ?>

		<?php else : ?>

			<div id="content" class="container" role="main">

				<div class="blog-archive row">
					<div class="col-md-<?php echo is_active_sidebar( 'sidebar-blog' ) ? '9' : '12'; ?> col-xs-12">
						<?php get_template_part( 'content', get_post_format() ); ?>
						<?php comments_template(); ?>
					</div>

					<?php get_sidebar(); ?>
				</div>

			</div><!-- #content -->

		<?php endif; ?>

		<?php do_action( 'jobify_loop_after' ); ?>
	</div><!-- #primary -->

	<?php endwhile; ?>

<?php get_footer(); ?>