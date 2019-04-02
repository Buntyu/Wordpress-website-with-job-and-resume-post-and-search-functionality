
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
		

		
				<div id="content" class="container" role="main">
					<div class="entry-content">
						
					<h1><?php the_title(); ?></h1>
					<h5><?php the_field('email'); ?></h5>
					<p><strong><a href="<?php the_field('upload_resume'); ?>">Download Resume</a></strong></p>
					<h5>Notes </h5>
					<p><?php the_field('notes'); ?></p>
					<h5>Result </h5>
					<p><?php the_field('results'); ?></p>
					
					</div>
					
				</div>


		

              

		<?php do_action( 'jobify_loop_after' ); ?>
	</div><!-- #primary -->

	<?php endwhile; ?>

<?php get_footer(); ?>
	