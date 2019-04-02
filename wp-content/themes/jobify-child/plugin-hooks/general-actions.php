<?php

/*
 * function to send email notification to admin for new job posted.
 */
function arch_new_job_email_notification_test($job_data, $post_title, $post_content, $status, $values){
    
    if($status != 'preview'){
    
	$subject = 'New Job Posted on Archipro';
	$message = "This email has been sent to notify you that a new Job ".$post_title." has been posted on your Archipro";
        $admin_email = get_option('admin_email');
        wp_mail($admin_email, $subject, $message );

    }
    return $job_data;
}
add_filter('submit_job_form_save_job_data','arch_new_job_email_notification_test',10,5);
?>
