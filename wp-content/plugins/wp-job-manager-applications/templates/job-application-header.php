<?php echo get_job_application_avatar( $application->ID ) ?>
<h3>
	<?php if ( ( $resume_id = get_job_application_resume_id( $application->ID ) ) && 'publish' === get_post_status( $resume_id ) && function_exists( 'get_resume_share_link' ) && ( $share_link = get_resume_share_link( $resume_id ) ) ) : ?>
		<a href="<?php echo esc_attr( $share_link ); ?>"><?php echo $application->post_title; ?></a>
	<?php else : ?>
		<?php echo $application->post_title; ?>
	<?php endif; ?>
</h3>
<span class="job-application-rating"><span style="width: <?php echo ( get_job_application_rating( $application->ID ) / 5 ) * 100; ?>%;"></span></span>