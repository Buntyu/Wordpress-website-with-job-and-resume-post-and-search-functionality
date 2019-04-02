jQuery(document).ready(function($) {

	$('.wp-job-manager-applications-form-editor')
		.on( 'init', function() {
			$(this).sortable({
				items:'tr',
				cursor:'move',
				axis:'y',
				handle: 'td.sort-column',
				scrollSensitivity:40,
				helper:function(e,ui){
					ui.children().each(function(){
						$(this).width($(this).width());
					});
					return ui;
				},
				start:function(event,ui){
					ui.item.css( 'background-color','#FEFEE6' );
				},
				stop:function(event,ui){
					ui.item.removeAttr('style');
				}
			});
			$(this).find( '.field-type select' ).change();
			$(this).find( '.field-rules select:visible' ).chosen();
		})
		.on( 'change', '.field-type select', function() {
			$(this).closest('tr').find('.field-options .placeholder').hide();
			$(this).closest('tr').find('.field-options .options').hide();
			$(this).closest('tr').find('.field-options .na').hide();
			$(this).closest('tr').find('.field-options .file-options').hide();

			if ( 'select' === $(this).val() || 'multiselect' === $(this).val() ) {
				$(this).closest('tr').find('.field-options .options').show();
			} else if ( 'resumes' === $(this).val() || 'output-content' === $(this).val() ) {
				$(this).closest('tr').find('.field-options .na').show();
			} else if ( 'file' === $(this).val() ) {
				$(this).closest('tr').find('.field-options .file-options').show();
			} else {
				$(this).closest('tr').find('.field-options .placeholder').show();
			}

			$(this).closest('tr').find('.field-rules .rules').hide();
			$(this).closest('tr').find('.field-rules .na').hide();

			if ( 'output-content' === $(this).val() ) {
				$(this).closest('tr').find('.field-rules .na').show();
			} else {
				$(this).closest('tr').find( '.field-rules .rules' ).show();
				$(this).closest('tr').find( '.field-rules select:visible' ).chosen();
			}
		})
		.on( 'click', '.delete-field', function() {
			if ( window.confirm( wp_job_manager_applications_form_editor.cofirm_delete_i18n ) ) {
				$(this).closest('tr').remove();
			}
		})
		.on( 'click', '.reset', function() {
			if ( window.confirm( wp_job_manager_applications_form_editor.cofirm_reset_i18n ) ) {
				return true;
			}
			return false;
		})
		.on( 'click', '.add-field', function() {
			var $tbody = $(this).closest('table').find('tbody');
			var row    = $tbody.data( 'field' );
			row = row.replace( /\[-1\]/g, "[" + $tbody.find('tr').size() + "]");
			$tbody.append( row );
			$('.wp-job-manager-applications-form-editor').trigger( 'init' );
			return false;
		});

	$('.wp-job-manager-applications-form-editor').trigger( 'init' );

});
