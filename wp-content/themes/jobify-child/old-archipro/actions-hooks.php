<?php

add_action( 'wp_ajax_arc_notes_update', 'arc_notes_update' );
add_action('wp_ajax_arc_notes_delete','arc_notes_delete');

function arc_notes_update(){
    check_ajax_referer( 'arc-notes', 'security' );
    
    if($_POST['note'] == ''){
        $response = json_encode(array(
            'response' => 'success',
            'msg' => 'Note cannot be blank'
        ));
        echo $response;
        wp_die();
    }
    
    $user_type = $_POST['user_type'];
    if(!class_exists('archipro_main')){
        include_once get_stylesheet_directory().'/old-archipro/class-archpiro-main.php';
    }
    $arc_obj = new archipro_main();
    $insert_id = $arc_obj->update_user_notes($_POST['note'],intval($_POST['note_id']),$_POST['user_id'],$user_type);
    
    $type = intval($_POST['note_id']) > 0?'update':'create';
    $html = '';
    $time = date("m/d/Y",time());
    if($type == 'create'){
        $html = <<<E
   <tr id="note-{$insert_id}">
    <td>
        {$time}
        <div style="height: 10px">&nbsp;</div>
        <button type="button" class="button button-small button-edit-note">&#9998;</button>
        <button type="button" class="button button-small button-del-note">&#10007;</button>
    </td>
    <td class="note_description">{$_POST['note']}</td>
</tr>
E;
    }
    
    $response = json_encode(array(
        'response' => 'success',
        'msg' => intval($_POST['note_id']) > 0?'Note Updated Successfully':'Note Created Successfully',
        'type' => $type,
        'html' => $html
    ));
    echo $response;
    wp_die();
}

function arc_notes_delete(){
    check_ajax_referer( 'arc-notes', 'security' );
    $user_type = $_POST['user_type'];
    if(!class_exists('archipro_main')){
        include_once get_stylesheet_directory().'/old-archipro/class-archpiro-main.php';
    }
    $arc_obj = new archipro_main();
    $chk = $arc_obj->delete_user_note(intval($_POST['note_id']),$_POST['user_id'],$user_type);
    $response = json_encode(array(
        'response' => 'success',
        'msg' => 'Note Deleted Successfully'
    ));
    echo $response;
    wp_die();
}
?>
