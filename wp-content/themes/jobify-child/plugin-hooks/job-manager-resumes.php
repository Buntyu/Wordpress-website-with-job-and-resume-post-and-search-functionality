<?php

function arc_resume_manager_actions($actions,$resume){
    unset($actions['delete']);
    $actions['edit'] = array( 'label' => __( 'Edit', 'wp-job-manager-resumes' ), 'nonce' => false );
    $actions['hide'] = array( 'label' => __( 'Hide', 'wp-job-manager-resumes' ), 'nonce' => true );
    $actions['delete'] = array( 'label' => __( 'Delete', 'wp-job-manager-resumes' ), 'nonce' => true );
    return $actions;
}

add_filter('resume_manager_my_resume_actions','arc_resume_manager_actions',10,2);

function arc_resume_fields($post_id, $post, $update){
    $post_type = get_post_type($post_id);
    if($post_type != 'resume'){
        return;
    }
    if ( isset( $_POST['position'] ) ) {
        update_post_meta( $post_id, '_arc_position', implode(',', $_POST['position']) );
    }
    
    if ( isset( $_POST['industry'] ) ) {
        update_post_meta( $post_id, '_arc_industry', implode(',', $_POST['industry']) );
    }
}

add_action('save_post','arc_resume_fields' ,10 ,3);
?>
