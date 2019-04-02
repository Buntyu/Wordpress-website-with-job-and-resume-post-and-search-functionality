<?php

/**
 * Preview View
 *
 * The default "Preview" view in WP Job Manager adds a little extra and doesn't
 * use the exact template file that displays what we need. Override it here.
 *
 * @since Jobify 1.6.0
 *
 * @return void
 */
function jobify_preview_handler() {
	global $job_manager, $post;

	if ( version_compare( JOB_MANAGER_VERSION, '1.22.0', '<' ) ) {
		$job_id    = WP_Job_Manager_Form_Submit_Job::get_job_id();
		$step      = WP_Job_Manager_Form_Submit_Job::get_step();
		$form_name = WP_Job_Manager_Form_Submit_Job::$form_name;
	} else {
		$form      = WP_Job_Manager_Form_Submit_Job::instance();
		$job_id    = $form->get_job_id();
		$step      = $form->get_step();
		$form_name = $form->form_name;
	}

	if ( $job_id ) {
		$post = get_post( $job_id );
		setup_postdata( $post );
		?>
		<form method="post" id="job_preview">
			<div class="job_listing_preview_title">
				<input type="submit" name="continue" id="job_preview_submit_button" class="button" value="<?php echo apply_filters( 'submit_job_step_preview_submit_text', __( 'Submit Listing &rarr;', 'jobify' ) ); ?>" />
				<input type="submit" name="edit_job" class="button" value="<?php esc_attr_e( '&larr; Edit listing', 'jobify' ); ?>" />
				<input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>" />
				<input type="hidden" name="step" value="<?php echo esc_attr( $step ); ?>" />
				<input type="hidden" name="job_manager_form" value="<?php echo $form_name; ?>" />
			</div>
			<?php get_job_manager_template_part( 'content-single', 'job' ); ?>
		</form>
		<?php

		wp_reset_postdata();
	}
}

/**
 * The Company Description template tag.
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_the_company_description( $before = '', $after = '', $echo = true ) {
	$company_description = jobify_get_the_company_description();

	if ( strlen( $company_description ) == 0 )
		return;

	$company_description = wp_kses_post( $company_description );
	$company_description = $before . wpautop( $company_description ) . $after;

	if ( $echo )
		echo $company_description;
	else
		return $company_description;
}

/**
 * Get the company description.
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_get_the_company_description( $post = 0 ) {
	$post = get_post( $post );

	if ( $post->post_type !== 'job_listing' )
		return;

	return apply_filters( 'the_company_description', $post->_company_description, $post );
}

/**
 * Trim the job location output on all pages except the actual listing.
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_the_job_location( $location ) {
	if ( is_singular( 'job_listing' ) )
		return $location;

	$location = wp_trim_words( $location, 3, '' );

	return $location;
}
add_filter( 'the_job_location', 'jobify_the_job_location' );

/**
 * Get the Company Facebook
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_get_the_company_facebook( $post = 0 ) {
	$post = get_post( $post );

	if ( $post->post_type !== 'job_listing' )
		return;

	$company_facebook = $post->_company_facebook;

	if ( strlen( $company_facebook ) == 0 )
		return;

	return apply_filters( 'the_company_facebook', $company_facebook, $post );
}

/**
 * Get the Company Google Plus
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_get_the_company_gplus( $post = 0 ) {
	$post = get_post( $post );

	if ( $post->post_type !== 'job_listing' )
		return;

	$company_google = $post->_company_google;

	if ( strlen( $company_google ) == 0 )
		return;

	return apply_filters( 'the_company_google', $company_google, $post );
}

/**
 * Get the Company LinkedIn
 *
 * @since Jobify 1.6.0
 *
 * @return void
 */
function jobify_get_the_company_linkedin( $post = 0 ) {
	$post = get_post( $post );

	if ( $post->post_type !== 'job_listing' )
		return;

	$company_linkedin = $post->_company_linkedin;

	if ( strlen( $company_linkedin ) == 0 )
		return;

	return apply_filters( 'the_company_linkedin', $company_linkedin, $post );
}
