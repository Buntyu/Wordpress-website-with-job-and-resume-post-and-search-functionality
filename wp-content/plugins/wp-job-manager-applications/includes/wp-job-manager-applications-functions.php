<?php
if ( ! function_exists( 'create_job_application' ) ) {
	/**
	 * Create a new job application
	 * @param  int $job_id
	 * @param  string $candidate_name
	 * @param  string $application_message
	 * @param  string $candidate_email
	 * @param  array  $meta
	 * @param  bool $notification
	 * @return int|bool success
	 */
	function create_job_application( $job_id, $candidate_name, $candidate_email, $application_message, $meta = array(), $notification = true, $source = '' ) {
		$job = get_post( $job_id );

		if ( ! $job || $job->post_type !== 'job_listing' ) {
			return false;
		}

		$application_data = array(
			'post_title'     => wp_kses_post( $candidate_name ),
			'post_content'   => wp_kses_post( $application_message ),
			'post_status'    => current( array_keys( get_job_application_statuses() ) ),
			'post_type'      => 'job_application',
			'comment_status' => 'closed',
			'post_author'    => $job->post_author,
			'post_parent'    => $job_id
		);
		$application_id = wp_insert_post( $application_data );

		if ( $application_id ) {
			update_post_meta( $application_id, '_job_applied_for', $job->post_title );
			update_post_meta( $application_id, '_candidate_email', $candidate_email );
			update_post_meta( $application_id, '_candidate_user_id', get_current_user_id() );
			update_post_meta( $application_id, '_rating', 0 );
			update_post_meta( $application_id, '_application_source', $source );

			if ( $meta ) {
				foreach ( $meta as $key => $value ) {
					update_post_meta( $application_id, $key, $value );
				}
			}

			if ( $notification ) {
				$method = get_the_job_application_method( $job_id );

				if ( "email" === $method->type ) {
					$send_to = $method->raw_email;
				} elseif ( $job->post_author ) {
					$user    = get_user_by( 'id', $job->post_author );
					$send_to = $user->user_email;
				} else {
					$send_to = '';
				}

				if ( $send_to ) {
					$attachments = array();

					if ( ! empty( $meta['_attachment_file'] ) ) {
						if ( is_array( $meta['_attachment_file'] ) ) {
							foreach ( $meta['_attachment_file'] as $file ) {
								$attachments[] = $file;
							}
						} else {
							$attachments[] = $meta['_attachment_file'];
						}
					}

					$existing_shortcode_tags = $GLOBALS['shortcode_tags'];
					remove_all_shortcodes();
					job_application_email_add_shortcodes( array(
						'application_id'      => $application_id,
						'job_id'              => $job_id,
						'user_id'             => get_current_user_id(),
						'candidate_name'      => $candidate_name,
						'candidate_email'     => $candidate_email,
						'application_message' => $application_message,
						'meta'                => $meta
					) );
					$subject = do_shortcode( get_job_application_email_subject() );
					$message = do_shortcode( get_job_application_email_content() );
					$message = str_replace( "\n\n\n\n", "\n\n", implode( "\n", array_map( 'trim', explode( "\n", $message ) ) ) );
					$is_html = ( $message != strip_tags( $message ) );

					// Does this message contain formatting already?
					if ( $is_html && ! strstr( $message, '<p' ) && ! strstr( $message, '<br' ) ) {
						$message = nl2br( $message );
					}

					$GLOBALS['shortcode_tags'] = $existing_shortcode_tags;

					$headers   = array();
					$headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <noreply@' . str_replace( array( 'http://', 'https://', 'www.' ), '', site_url( '' ) ) . '>';
					$headers[] = 'Reply-To: ' . $candidate_email;
					$headers[] = $is_html ? 'Content-Type: text/html' : 'Content-Type: text/plain';
					$headers[] = 'charset=utf-8';

					wp_mail(
						apply_filters( 'create_job_application_notification_recipient', $send_to, $job_id, $application_id ),
						apply_filters( 'create_job_application_notification_subject', $subject, $job_id, $application_id ),
						apply_filters( 'create_job_application_notification_message', $message ),
						apply_filters( 'create_job_application_notification_headers', $headers, $job_id, $application_id ),
						apply_filters( 'create_job_application_notification_attachments', $attachments, $job_id, $application_id )
					);
				}
			}

			return $application_id;
		}

		return false;
	}
}

if ( ! function_exists( 'get_job_application_count' ) ) {
	/**
	 * Get number of applications for a job
	 * @param  int $job_id
	 * @return int
	 */
	function get_job_application_count( $job_id ) {
		return sizeof( get_posts( array(
			'post_type'      => 'job_application',
			'post_status'    => array_merge( array_keys( get_job_application_statuses() ), array( 'publish' ) ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_parent'    => $job_id
		) ) );
	}
}

if ( ! function_exists( 'user_has_applied_for_job' ) ) {
	/**
	 * See if a user has already appled for a job
	 * @param  int $user_id
	 * @param  int $job_id
	 * @return bool
	 */
	function user_has_applied_for_job( $user_id, $job_id ) {
		if ( ! $user_id ) {
			return false;
		}
		return sizeof( get_posts( array(
			'post_type'      => 'job_application',
			'post_status'    => array_merge( array_keys( get_job_application_statuses() ), array( 'publish' ) ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'post_parent'    => $job_id,
			'meta_query'     => array(
				array(
					'key' => '_candidate_user_id',
					'value' => absint( $user_id )
				)
			)
		) ) );
	}
}

/**
 * Job Application Statuses
 * @return array
 */
function get_job_application_statuses() {
	return apply_filters( 'job_application_statuses', array(
		'new'         => _x( 'New', 'job_application', 'wp-job-manager-applications' ),
		'interviewed' => _x( 'Interviewed', 'job_application', 'wp-job-manager-applications' ),
		'offer'       => _x( 'Offer extended', 'job_application', 'wp-job-manager-applications' ),
		'hired'       => _x( 'Hired', 'job_application', 'wp-job-manager-applications' ),
		'archived'    => _x( 'Archived', 'job_application', 'wp-job-manager-applications' )
	) );
}

/**
 * Get default form fields
 * @return array
 */
function get_job_application_default_form_fields() {
	$default_fields = array(
		'candidate_name' => array(
			'label'       => __( 'Full name', 'wp-job-manager-applications' ),
			'type'        => 'text',
			'required'    => true,
			'placeholder' => '',
			'priority'    => 1,
			'rules'       => array( 'from_name' )
		),
		'candidate_email' => array(
			'label'       => __( 'Email address', 'wp-job-manager-applications' ),
			'description' => '',
			'type'        => 'text',
			'required'    => true,
			'placeholder' => '',
			'priority'    => 2,
			'rules'       => array( 'from_email' )
		),
		'application_message' => array(
			'label'       => __( 'Message', 'wp-job-manager-applications' ),
			'type'        => 'textarea',
			'required'    => true,
			'placeholder' => __( 'Your cover letter/message sent to the employer', 'wp-job-manager-applications' ),
			'priority'    => 3,
			'rules'       => array( 'message' )
		),
		'resume_id' => array(
			'label'       => __( 'Online Resume', 'wp-job-manager-applications' ),
			'description' => '',
			'type'        => 'resumes',
			'required'    => false,
			'priority'    => 4,
			'rules'       => array(),
		),
		'application_attachment' => array(
			'label'       => __( 'Upload CV', 'wp-job-manager-applications' ),
			'type'        => 'file',
			'required'    => true,
			'priority'    => 5,
			'placeholder' => '',
			'multiple'    => true,
			'rules'       => array( 'attachment' ),
			'description' => sprintf( __( 'Upload your CV/resume or any other relevant file. Max. file size: %s.', 'wp-job-manager-applications' ), size_format( wp_max_upload_size() ) )
		)
	);

	if ( ! function_exists( 'get_resume_share_link' ) ) {
		unset( $default_fields['resume_id'] );
		$default_fields['application_attachment']['required'] = true;
	} else {
		$default_fields['application_attachment']['required'] = false;
	}

	return $default_fields;
}

/**
 * Get the form fields for the application form
 * @return array
 */
function get_job_application_form_fields( $suppress_filters = false ) {
	$option = get_option( 'job_application_form_fields', get_job_application_default_form_fields() );
	return $suppress_filters ? $option : apply_filters( 'job_application_form_fields', $option );
}

/**
 * Get the default email content
 * @return string
 */
function get_job_application_default_email_content() {
	$message = <<<EOF
Hello

A candidate ([from_name]) has submitted their application for the position "[job_title]".

=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

[message]

=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

[meta_data]

[job_dashboard_url prefix="You can view this and any other applications here: "]

You can contact them directly at: [from_email]
EOF;
	return $message;
}

/**
 * Get email content
 * @return string
 */
function get_job_application_email_content() {
	return apply_filters( 'job_application_email_content', get_option( 'job_application_email_content', get_job_application_default_email_content() ) );
}

/**
 * Get the default email subject
 * @return string
 */
function get_job_application_default_email_subject() {
	return __( "New job application for [job_title]", 'wp-job-manager-applications' );
}

/**
 * Get email content
 * @return string
 */
function get_job_application_email_subject() {
	return apply_filters( 'job_application_email_subject', get_option( 'job_application_email_subject', get_job_application_default_email_subject() ) );
}

/**
 * Get candidate email content
 * @return string
 */
function get_job_application_candidate_email_content() {
	return apply_filters( 'job_application_candidate_email_content', get_option( 'job_application_candidate_email_content' ) );
}

/**
 * Get the default email subject
 * @return string
 */
function get_job_application_default_candidate_email_subject() {
	return __( "Your job application for [job_title]", 'wp-job-manager-applications' );
}

/**
 * Get email content
 * @return string
 */
function get_job_application_candidate_email_subject() {
	return apply_filters( 'job_application_candidate_email_subject', get_option( 'job_application_candidate_email_subject', get_job_application_default_candidate_email_subject() ) );
}

/**
 * Get tags to dynamically replace in the notification email
 * @return array
 */
function get_job_application_email_tags() {
	$tags = array(
		'from_name'         => __( 'Candidate Name', 'wp-job-manager-applications' ),
		'from_email'        => __( 'Candidate Email', 'wp-job-manager-applications' ),
		'message'           => __( 'Message from candidate', 'wp-job-manager-applications' ),
		'meta_data'         => __( 'All custom form fields in list format', 'wp-job-manager-applications' ),
		'application_id'    => __( 'Application ID', 'wp-job-manager-applications' ),
		'user_id'           => __( 'User ID of applicant', 'wp-job-manager-applications' ),
		'job_id'            => __( 'Job ID', 'wp-job-manager-applications' ),
		'job_title'         => __( 'Job Title', 'wp-job-manager-applications' ),
		'job_url'           => __( 'URL of the job listing', 'wp-job-manager-applications' ),
		'job_dashboard_url' => __( 'URL to the frontend job dashboard page', 'wp-job-manager-applications' ),
		'company_name'      => __( 'Name of the company which submitted the job listing', 'wp-job-manager-applications' ),
		'job_post_meta'     => __( 'Some meta data from the job. e.g. <code>[job_post_meta key="_job_location"]</code>', 'wp-job-manager-applications' )
	);

	foreach ( get_job_application_form_fields() as $key => $field ) {
		if ( isset( $tags[ $key ] ) ) {
			continue;
		}
		if ( in_array( 'message', $field['rules'] ) || in_array( 'from_name', $field['rules'] ) || in_array( 'from_email', $field['rules'] ) || in_array( 'attachment', $field['rules'] ) ) {
			continue;
		}
		$tags[ $key ] = sprintf( __( 'Custom field named "%s"', 'wp-job-manager-applications' ), $field['label'] );
	}

	return $tags;
}

/**
 * Shortcode handler
 * @param  array $atts
 * @return string
 */
function job_application_email_shortcode_handler( $atts, $content, $value ) {
	$atts = shortcode_atts( array(
		'prefix' => '',
		'suffix' => ''
	), $atts );

	if ( ! empty( $value ) ) {
		return wp_kses_post( $atts['prefix'] ) . $value . wp_kses_post( $atts['suffix'] );
	}
}

/**
 * Add shortcodes for email content
 * @param  array $data
 */
function job_application_email_add_shortcodes( $data ) {
	extract( $data );

	$job_title         = strip_tags( get_the_title( $job_id ) );
	$dashboard_id      = get_option( 'job_manager_job_dashboard_page_id' );
	$job_dashboard_url = $dashboard_id ? htmlspecialchars_decode( add_query_arg( array( 'action' => 'show_applications', 'job_id' => $job_id ), get_permalink( $dashboard_id ) ) ) : '';
	$meta_data         = array();
	$company_name      = get_the_company_name( $job_id );
	$application_id    = $data['application_id'];
	$user_id           = $data['user_id'];

	add_shortcode( 'from_name', function( $atts, $content = '' ) use( $candidate_name ) {
		return job_application_email_shortcode_handler( $atts, $content, $candidate_name );
	} );
	add_shortcode( 'from_email', function( $atts, $content = '' ) use( $candidate_email ) {
		return job_application_email_shortcode_handler( $atts, $content, $candidate_email );
	} );
	add_shortcode( 'message', function( $atts, $content = '' ) use( $application_message ) {
		return job_application_email_shortcode_handler( $atts, $content, $application_message );
	} );
	add_shortcode( 'job_id', function( $atts, $content = '' ) use( $job_id ) {
		return job_application_email_shortcode_handler( $atts, $content, $job_id );
	} );
	add_shortcode( 'job_title', function( $atts, $content = '' ) use( $job_title ) {
		return job_application_email_shortcode_handler( $atts, $content, $job_title );
	} );
	add_shortcode( 'job_url', function( $atts, $content = '' ) use( $job_id ) {
		return job_application_email_shortcode_handler( $atts, $content, get_permalink( $job_id ) );
	} );
	add_shortcode( 'job_dashboard_url', function( $atts, $content = '' ) use( $job_dashboard_url ) {
		return job_application_email_shortcode_handler( $atts, $content, $job_dashboard_url );
	} );
	add_shortcode( 'company_name', function( $atts, $content = '' ) use( $company_name ) {
		return job_application_email_shortcode_handler( $atts, $content, $company_name );
	} );
	add_shortcode( 'application_id', function( $atts, $content = '' ) use( $application_id ) {
		return job_application_email_shortcode_handler( $atts, $content, $application_id );
	} );
	add_shortcode( 'user_id', function( $atts, $content = '' ) use( $user_id ) {
		return job_application_email_shortcode_handler( $atts, $content, $user_id );
	} );
	add_shortcode( 'job_post_meta', function( $atts, $content = '' ) use( $job_id ) {
		$atts  = shortcode_atts( array( 'key' => '' ), $atts );
		$value = get_post_meta( $job_id, sanitize_text_field( $atts['key'] ), true );
		return job_application_email_shortcode_handler( $atts, $content, $value );
	} );

	foreach ( get_job_application_form_fields() as $key => $field ) {
		if ( in_array( 'message', $field['rules'] ) || in_array( 'from_name', $field['rules'] ) || in_array( 'from_email', $field['rules'] ) || in_array( 'attachment', $field['rules'] ) ) {
			continue;
		}
		$value = isset( $meta[ $field['label'] ] ) ? $meta[ $field['label'] ] : '';

		if ( $field['type'] === 'resumes' && function_exists( 'get_resume_share_link' ) && $value ) {
			$value = get_resume_share_link( $value );
		}

		$meta_data[ $field['label'] ] = $value;

		add_shortcode( $key, function( $atts, $content = '' ) use( $value ) {
			return job_application_email_shortcode_handler( $atts, $content, $value );
		} );
	}

	$meta_data         = array_filter( $meta_data );
	$meta_data_strings = array();
	foreach ( $meta_data as $label => $value ) {
		$meta_data_strings[] = $label . ': ' . $value;
	}
	$meta_data_strings = implode( "\n", $meta_data_strings );

	add_shortcode( 'meta_data', function( $atts, $content = '' ) use( $meta_data_strings ) {
		return job_application_email_shortcode_handler( $atts, $content, $meta_data_strings );
	} );

	do_action( 'job_application_email_add_shortcodes', $data );
}
