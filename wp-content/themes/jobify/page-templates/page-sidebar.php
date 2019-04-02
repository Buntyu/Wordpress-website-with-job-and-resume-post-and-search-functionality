<?php
/**
 * Template Name: Page with Sidebar
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<header class="page-header">
		<h1 class="page-title"><?php the_title(); ?></h1>
	</header>

	<div id="primary" class="content-area">
		<div id="content" class="container" role="main">
			
			<div class="page-with-sidebar row">
				<div class="col-md-<?php echo is_active_sidebar( 'sidebar-blog' ) ? '9' : '12'; ?> col-xs-12">
					<?php get_template_part( 'content', 'page' ); ?>
					<?php comments_template(); ?>
				</div>

				<?php get_sidebar(); ?>
			</div>
			
		</div><!-- #content -->

		<?php do_action( 'jobify_loop_after' ); ?>
	</div><!-- #primary -->

	<?php endwhile; ?>

<?php get_footer(); ?>
