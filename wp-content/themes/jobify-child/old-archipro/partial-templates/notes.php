<?php 
$ajax_nonce = wp_create_nonce( "arc-notes" );
?>
<table id="arc-notes" class="arc-extra-links">
<tbody>
    <tr>
        <td colspan="2" class="color_dark">&nbsp;&nbsp;<b>Notes:</b></td>
</tr>
<tr>
    <td width="15%"><b>Date</b></td>
    <td><b>Description</b></td>
</tr>
<tr id="notes-frm-tr">
    <td width="15%">&nbsp;</td>
    <td>
        <div style="display: none" class="success_msg job-manager-success"></div>
        <div style="display: none" class="error_msg job-manager-error"></div>
        <textarea id="arc_note_textarea" name="note_content" placeholder="add new note"></textarea>
        <div style="height: 10px">&nbsp;</div>
        <input type="hidden" name="note_id" value="0">
        <button type="button" class="button button-small button-save-note">create note</button>
        <button type="button" class="button button-small button-discard-note">cancel</button>
    </td>
</tr>
<?php foreach ($user_notes as $note) {
    ?>
<tr id="<?= 'note-'.$note->ID ?>">
    <td width="15%">
        <?php echo date("m/d/Y",$note->AddedDate); ?>
        <div style="height: 10px">&nbsp;</div>
        <button type="button" class="button button-small button-edit-note">&#9998;</button>
        <button type="button" class="button button-small button-del-note">&#10007;</button>
    </td>
    <td class="note_description"><?= $note->Description; ?></td>
</tr>
<?php } ?>
<tr>
        <td colspan="2" class="color_dark"></td>
</tr>
</tbody>
</table>

<script type="text/javascript">
    ( function( $ ) {
        var ajax_url = '<?= admin_url( 'admin-ajax.php' ) ?>';
        var user_type = '<?= $user_type; ?>';
        var user_id = '<?= $user_id; ?>';
            $('#arc-notes').on('click','.button-edit-note',function(){
                $('.button-save-note').html('update note');
                var id = $(this).parents('tr').attr('id');
                var note_id = id.split("-");
                note_id = note_id[1];
                $('input[name="note_id"]').val(note_id);
                var note = $('#'+id+' .note_description').html();
                $('#arc_note_textarea').val(note);
            });
            
            $('#arc-notes').on('click','.button-del-note',function(){
                var id = $(this).parents('tr').attr('id');
                var note_id = id.split("-");
                note_id = note_id[1];
                $.ajax({
                    method: "POST",
                    url: ajax_url,
                    dataType:'json',
                    data: { user_id:user_id, user_type:user_type, note_id: note_id,action:'arc_notes_delete',security: '<?php echo $ajax_nonce; ?>' },
                    success:function(response){
                        if(response.response == 'success'){
                            $('#'+id).remove();
                            show_success(response.msg);
                        }else{
                            show_error(response.msg);
                        }
                    }
                });
            });
            
            $('#arc-notes').on('click','.button-discard-note',function(){
                $('#arc_note_textarea').val('');
                $('input[name="note_id"]').val(0);
                $('.button-save-note').html('create note');
            });
            
            $('#arc-notes').on('click','.button-save-note',function(){
                var note = $('#arc_note_textarea').val();
                var note_id = $('input[name="note_id"]').val();
                if(note == ''){
                    show_error('Note cannot be blank!');
                    return false;
                }
                $.ajax({
                    method: "POST",
                    url: ajax_url,
                    dataType:'json',
                    data: { user_id:user_id, user_type:user_type, note: note, note_id: note_id,action:'arc_notes_update',security: '<?php echo $ajax_nonce; ?>' },
                    success:function(response){
                        if(response.response == 'success'){
                            if(response.type == 'create'){
                                $('#notes-frm-tr').after(response.html);
                            }else{
                                $('#note-'+note_id+' td.note_description').html(note);
                            }
                            $('#arc_note_textarea').val('');
                            $('input[name="note_id"]').val(0);
                            $('.button-save-note').html('create note');
                            show_success(response.msg);
                        }else{
                            show_error(response.msg);
                        }
                    }
                });
                
            });
            
            function show_success(msg){
                $('div.success_msg').html(msg).show(200).delay(2000).hide(200);
            }
            function show_error(msg){
                $('div.error_msg').html(msg).show(200).delay(2000).hide(200);
            }
    } )( jQuery );
</script>