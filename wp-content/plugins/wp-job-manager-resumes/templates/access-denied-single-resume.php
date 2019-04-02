<?php if ( $post->post_status === 'expired' ) : ?>
	<div class="job-manager-info"><?php _e( 'This listing has expired', 'wp-job-manager-resumes' ); ?></div>
<?php else : ?>
	<p class="job-manager-error"><?php _e( 'Sorry, you do not have permission to view this resume.', 'wp-job-manager-resumes' ); ?></p>
<?php endif; ?>