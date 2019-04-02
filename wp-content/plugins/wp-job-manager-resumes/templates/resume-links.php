<?php if ( resume_has_links() || resume_has_file() ) : ?>
	<ul class="resume-links">
		<?php foreach( get_resume_links() as $link ) : ?>
			<?php get_job_manager_template( 'content-resume-link.php', array( 'post' => $post, 'link' => $link ), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>
		<?php endforeach; ?>
		<?php if ( resume_has_file() ) : ?>
			<?php get_job_manager_template( 'content-resume-file.php', array( 'post' => $post ), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>
		<?php endif; ?>
	</ul>
<?php endif; ?>