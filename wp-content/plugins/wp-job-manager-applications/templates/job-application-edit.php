<form class="job-manager-application-edit-form job-manager-form" method="post">

	<fieldset class="fieldset-status">
		<label for="application-status-<?php esc_attr_e( $application->ID ); ?>"><?php _e( 'Application status', 'wp-job-manager-applications' ); ?>:</label>
		<div class="field">
			<select id="application-status-<?php esc_attr_e( $application->ID ); ?>" name="application_status">
				<?php foreach ( get_job_application_statuses() as $name => $label ) : ?>
					<option value="<?php echo esc_attr( $name ); ?>" <?php selected( $application->post_status, $name ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</fieldset>

	<fieldset class="fieldset-rating">
		<label for="application-rating-<?php esc_attr_e( $application->ID ); ?>"><?php _e( 'Rating (out of 5)', 'wp-job-manager-applications' ); ?>:</label>
		<div class="field">
			<input type="number" id="application-rating-<?php esc_attr_e( $application->ID ); ?>" name="application_rating" step="0.1" max="5" min="0" placeholder="0" value="<?php echo esc_attr( get_job_application_rating( $application->ID ) ); ?>" />
		</div>
	</fieldset>

	<p>
		<a class="delete_job_application" href="<?php echo wp_nonce_url( add_query_arg( 'delete_job_application', $application->ID ), 'delete_job_application' ); ?>"><?php _e( 'Delete', 'wp-job-manager-applications' ); ?></a>
		<input type="submit" name="wp_job_manager_edit_application" value="<?php esc_attr_e( 'Save changes', 'wp-job-manager-applications' ); ?>" />
		<input type="hidden" name="application_id" value="<?php echo absint( $application->ID ); ?>" />
		<?php wp_nonce_field( 'edit_job_application' ); ?>
	</p>
</form>