<?php global $post; ?>

<form class="apply_with_resume" method="post" action="<?php echo get_permalink( get_option( 'resume_manager_submit_resume_form_page_id' ) ); ?>">
    <p><?php _e( 'Before applying for this position you need to submit your <strong>online resume</strong>. Click the button below to continue.', 'wp-job-manager-resumes' ); ?></p>
    <p>
        <input type="submit" name="wp_job_manager_resumes_apply_with_resume_create" value="<?php esc_attr_e( 'Submit Resume', 'wp-job-manager-resumes' ); ?>" />
        <input type="hidden" name="job_id" value="<?php echo absint( $post->ID ); ?>" />
    </p>
</form>
