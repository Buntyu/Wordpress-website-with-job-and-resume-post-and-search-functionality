<?php
/**
 * Job Archives
 *
 * @package Jobify
 * @since Jobify 1.0
 */

get_header(); ?>

	<header class="page-header">
		<h1 class="page-title"><?php echo apply_filters( 'jobify_job_archives_title', __( 'All Jobs', 'jobify' ) ); ?></h1>
	</header>

	<div id="primary" class="content-area">
		<div id="content" class="container" role="main">
			<div class="entry-content">
				<?php do_action( 'jobify_output_job_results' ); ?>
			</div>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>