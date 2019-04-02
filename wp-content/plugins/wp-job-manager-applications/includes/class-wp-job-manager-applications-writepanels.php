<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! class_exists( 'WP_Job_Manager_Writepanels' ) && defined( 'JOB_MANAGER_PLUGIN_DIR' ) ) {
	include( JOB_MANAGER_PLUGIN_DIR . '/includes/admin/class-wp-job-manager-writepanels.php' );
}

if ( class_exists( 'WP_Job_Manager_Writepanels' ) ) {
	class WP_Job_Manager_Applications_Writepanels extends WP_Job_Manager_Writepanels {

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_post' ), 1, 2 );
			add_action( 'job_manager_applications_save_job_application', array( $this, 'save_job_application_data' ), 1, 2 );
		}

		/**
		 * Job application fields
		 *
		 * @return array
		 */
		public function job_application_fields() {
			global $post;

			$fields = apply_filters( 'job_manager_applications_job_application_fields', array(
				'_candidate_email' => array(
					'label'       => __( 'Contact Email', 'wp-job-manager-applications' ),
					'placeholder' => __( 'you@yourdomain.com', 'wp-job-manager-applications' ),
					'description' => ''
				),
				'_attachment' => array(
					'label'       => __( 'Attachment', 'wp-job-manager-applications' ),
					'placeholder' => __( 'URL to the attachment if the candidate provided one', 'wp-job-manager-applications' ),
					'type'        => 'file',
					'multiple' => true
				),
				'_job_application_author' => array(
					'label'       => __( 'Posted by', 'wp-job-manager' ),
					'type'        => 'author',
					'placeholder' => ''
				),
				'_rating' => array(
					'label'       => __( 'Rating (out of 5)', 'wp-job-manager' ),
					'type'        => 'text',
					'placeholder' => '0'
				),
				'_resume_id' => array(
					'label'       => __( 'Online Resume ID', 'wp-job-manager' ),
					'type'        => 'text',
					'placeholder' => 'Post ID of the candidate\'s resume'
				),
				'post_parent' => array(
					'label'       => __( 'Job Listing ID', 'wp-job-manager' ),
					'type'        => 'text',
					'placeholder' => 'Post ID of the job ID applied for',
					'value'       => $post->post_parent
				)
			) );

			if ( ! function_exists( 'get_resume_share_link' ) ) {
				unset( $fields['_resume_id'] );
			}

			return $fields;
		}

		/**
		 * add_meta_boxes function.
		 */
		public function add_meta_boxes() {
			add_meta_box( 'job_application_save', __( 'Save Application', 'wp-job-manager-applications' ), array( $this, 'job_application_save' ), 'job_application', 'side', 'high' );
			add_meta_box( 'job_application_data', __( 'Job Application Data', 'wp-job-manager-applications' ), array( $this, 'job_application_data' ), 'job_application', 'normal', 'high' );
			add_meta_box( 'job_application_notes', __( 'Application Notes', 'wp-job-manager-applications' ), array( $this, 'application_notes' ), 'job_application', 'side', 'default' );
			remove_meta_box( 'submitdiv', 'job_application', 'side' );
		}

		/**
		 * Publish meta box
		 */
		public function job_application_save( $post ) {
			$statuses = get_job_application_statuses();
			?>
			<div class="submitbox" id="submitpost">
				<div id="minor-publishing">
					<div id="misc-publishing-actions">
						<div class="misc-pub-section misc-pub-post-status">
							<div id="post-status-select">
								<select name='post_status' id='post_status'>
									<?php
									foreach ( $statuses as $key => $label ) {
										$selected = selected( $post->post_status, $key, false );
										echo "<option{$selected} value='" . esc_attr( $key ) . "'>" . esc_html( $label ) . "</option>";
									}
									?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div id="major-publishing-actions">
					<div id="delete-action">
						<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php _e( 'Move to Trash' ); ?></a>
					</div>
					<div id="publishing-action">
						<span class="spinner"></span>
						<input name="save" class="button button-primary" type="submit" value="<?php _e( 'Save', 'wp-job-manager-applications' ); ?>">
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<?php
		}

		/**
		 * Job application data
		 * @param mixed $post
		 */
		public function job_application_data( $post ) {
			global $post, $thepostid;

			$thepostid = $post->ID;

			echo '<div class="wp_job_manager_meta_data">';

			wp_nonce_field( 'save_meta_data', 'job_manager_applications_nonce' );

			do_action( 'job_application_data_start', $thepostid );

			foreach ( $this->job_application_fields() as $key => $field ) {
				$type = ! empty( $field['type'] ) ? $field['type'] : 'text';

				if ( method_exists( $this, 'input_' . $type ) ) {
					call_user_func( array( $this, 'input_' . $type ), $key, $field );
				} else {
					do_action( 'job_manager_applications_input_' . $type, $key, $field );
				}
			}

			do_action( 'job_application_data_end', $thepostid );

			echo '</div>';
		}

		/**
		 * Triggered on Save Post
		 *
		 * @param mixed $post_id
		 * @param mixed $post
		 */
		public function save_post( $post_id, $post ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
			if ( is_int( wp_is_post_revision( $post ) ) ) return;
			if ( is_int( wp_is_post_autosave( $post ) ) ) return;
			if ( empty( $_POST[ 'job_manager_applications_nonce' ] ) || ! wp_verify_nonce( $_POST['job_manager_applications_nonce'], 'save_meta_data' ) ) return;
			if ( ! current_user_can( 'edit_post', $post_id ) ) return;
			if ( $post->post_type !== 'job_application' ) return;

			do_action( 'job_manager_applications_save_job_application', $post_id, $post );
		}

		/**
		 * Save application Meta
		 *
		 * @param mixed $post_id
		 * @param mixed $post
		 */
		public function save_job_application_data( $post_id, $post ) {
			global $wpdb;

			foreach ( $this->job_application_fields() as $key => $field ) {

				if( '_job_application_author' === $key ) {
					$wpdb->update( $wpdb->posts, array( 'post_author' => $_POST[ $key ] > 0 ? absint( $_POST[ $key ] ) : 0 ), array( 'ID' => $post_id ) );
				}

				elseif ( 'post_parent' === $key ) {
					// WP Handles this field
				}

				else {
					$type = ! empty( $field['type'] ) ? $field['type'] : '';

					switch ( $type ) {
						case 'textarea' :
							update_post_meta( $post_id, $key, wp_kses_post( stripslashes( $_POST[ $key ] ) ) );
						break;
						case 'checkbox' :
							if ( isset( $_POST[ $key ] ) ) {
								update_post_meta( $post_id, $key, 1 );
							} else {
								update_post_meta( $post_id, $key, 0 );
							}
						break;
						default :
							if ( is_array( $_POST[ $key ] ) ) {
								update_post_meta( $post_id, $key, array_filter( array_map( 'sanitize_text_field', $_POST[ $key ] ) ) );
							} else {
								update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
							}
						break;
					}
				}
			}
		}

		/**
		 * application_notes metabox
		 */
		public static function application_notes( $post ) {
			job_application_notes( $post );
			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery('#job_application_notes')
						.on( 'click', '.job-application-note-add input.button', function() {
							var button                     = jQuery(this);
							var application_id             = button.data('application_id');
							var job_application            = jQuery(this).closest('#job_application_notes');
							var job_application_note       = job_application.find('textarea');
							var disabled_attr              = jQuery(this).attr('disabled');
							var job_application_notes_list = job_application.find('ul.job-application-notes-list');

							if ( typeof disabled_attr !== 'undefined' && disabled_attr !== false ) {
								return false;
							}
							if ( ! job_application_note.val() ) {
								return false;
							}

							button.attr( 'disabled', 'disabled' );

							var data = {
								action: 		'add_job_application_note',
								note: 			job_application_note.val(),
								application_id: application_id,
								security: 		'<?php echo wp_create_nonce( "job-application-notes" ); ?>'
							};

							jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>', data, function( response ) {
								job_application_notes_list.append( response );
								button.removeAttr( 'disabled' );
								job_application_note.val( '' );
							});

							return false;
						})
						.on( 'click', 'a.delete_note', function() {
							var answer = confirm( '<?php echo __( 'Are you sure you want to delete this? There is no undo.', 'wp-job-manager-applications' ); ?>' );
							if ( answer ) {
								var button  = jQuery(this);
								var note    = jQuery(this).closest('li');
								var note_id = note.attr('rel');

								var data = {
									action: 		'delete_job_application_note',
									note_id:		note_id,
									security: 		'<?php echo wp_create_nonce( "job-application-notes" ); ?>'
								};

								jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>', data, function( response ) {
									note.fadeOut( 500, function() {
										note.remove();
									});
								});
							}
							return false;
						});
				});
			</script>
			<?php
		}
	}

	new WP_Job_Manager_Applications_Writepanels();
}