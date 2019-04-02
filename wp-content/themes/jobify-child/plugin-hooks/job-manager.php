<?php
/* This file includes actions and filters for job-manager plugins */

add_filter('job_manager_create_account_data','arc_jbm_candidate_signup_notificaiton');

function arc_jbm_candidate_signup_notificaiton($user_data){
    if($user_data['role'] == 'candidate'){
        $to = get_option('admin_email','leslie@archipro.com');
        $subject = 'Candidate Signup Notification';
        $message = <<<E
This email has been sent to notify you that a new user {$user_data['user_email']} has registered on Archipro as candidate.
E;
        wp_mail($to,$subject,$message);
    }
    return $user_data;
}
?>
