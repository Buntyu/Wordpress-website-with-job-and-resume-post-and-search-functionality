<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WP_Job_Manager_Applications_Admin class.
 */
class WP_Job_Manager_Applications_Admin {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		include( 'class-wp-job-manager-applications-writepanels.php' );
		include( 'class-wp-job-manager-applications-form-editor.php' );
		include( 'class-wp-job-manager-applications-settings.php' );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );
		add_filter( 'job_manager_admin_screen_ids', array( $this, 'screen_ids' ) );
		add_filter( 'manage_edit-job_listing_columns', array( $this, 'job_columns' ), 12 );
		add_action( 'manage_job_listing_posts_custom_column', array( $this, 'job_custom_columns' ), 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_filter( 'manage_edit-job_application_columns', array( $this, 'columns' ) );
		add_action( 'manage_job_application_posts_custom_column', array( $this, 'custom_columns' ), 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
		add_action( 'parse_query', array( $this, 'search_meta' ) );
		add_filter( 'get_search_query', array( $this, 'search_meta_label' ) );
		add_filter( 'request', array( $this, 'request' ) );
		add_filter( 'manage_edit-job_application_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'admin_footer-edit.php', array( $this, 'add_custom_statuses' ) );

		$this->settings_page = new WP_Job_Manager_Applications_Settings();
	}

	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=job_application', __( 'Settings', 'wp-job-manager-applications' ), __( 'Settings', 'wp-job-manager-applications' ), 'manage_options', 'job-applications-settings', array( $this->settings_page, 'output' ) );
	}

	/**
	 * Add screen ids to JM
	 * @param  array $ids
	 * @return array
	 */
	public function screen_ids( $ids ) {
		$ids[] = 'edit-job_application';
		$ids[] = 'job_application';
		return $ids;
	}

	/**
	 * Add applications column
	 * @param  array $columns
	 * @return array
	 */
	public function job_columns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $column ) {
			$new_columns[ $key ] = $column;

			if ( 'filled' === $key ) {
				$new_columns[ 'job_applications' ] = __( 'Applications', 'wp-job-manager-applications' );
			}
		}

		return $new_columns;
	}

	/**
	 * custom_columns function.
	 *
	 * @access public
	 * @param mixed $column
	 * @return void
	 */
	public function job_custom_columns( $column ) {
		global $post;

		if ( 'job_applications' === $column ) {
			echo ( $count = get_job_application_count( $post->ID ) ) ? '<a href="' . admin_url( 'edit.php?s&post_status=all&post_type=job_application&_job_listing=' . $post->ID ) . '">' . $count . '</a>' : '&ndash;';
		}
	}

	/**
	 * Enqueue admin scripts
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'wp-job-manager-applications-menu', JOB_MANAGER_APPLICATIONS_PLUGIN_URL . '/assets/css/menu.css', '', JOB_MANAGER_APPLICATIONS_VERSION );
		wp_enqueue_style( 'wp-job-manager-applications-admin', JOB_MANAGER_APPLICATIONS_PLUGIN_URL . '/assets/css/admin.css', '', JOB_MANAGER_APPLICATIONS_VERSION );
	}

	/**
	 * enter_title_here function.
	 *
	 * @access public
	 * @return void
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == 'job_application' ) {
			return __( 'Candidate name', 'wp-job-manager-applications' );
		}
		return $text;
	}

	/**
	 * post_updated_messages function.
	 *
	 * @access public
	 * @param array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		$messages['job_application'] = array(
			0  => '',
			1  => __( 'Job application updated.', 'wp-job-manager-applications' ),
			2  => __( 'Custom field updated.', 'wp-job-manager-applications' ),
			3  => __( 'Custom field deleted.', 'wp-job-manager-applications' ),
			4  => __( 'Job application updated.', 'wp-job-manager-applications' ),
			5  => '',
			6  => __( 'Job application published.', 'wp-job-manager-applications' ),
			7  => __( 'Job application saved.', 'wp-job-manager-applications' ),
			8  => __( 'Job application submitted.', 'wp-job-manager-applications' ),
			9  => '',
			10 => __( 'Job application draft updated.', 'wp-job-manager-applications' )
		);

		return $messages;
	}

	/**
	 * columns function.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return void
	 */
	public function columns( $columns ) {
		if ( ! is_array( $columns ) ) {
			$columns = array();
		}

		unset( $columns['title'], $columns['date'] );

		$columns["application_status"]      = __( "Status", 'wp-job-manager-applications' );
		$columns["candidate"]               = __( "Candidate", 'wp-job-manager-applications' );
		$columns["job"]                     = __( "Job applied for", 'wp-job-manager-applications' );
		$columns["application_rating"]      = __( "Rating", 'wp-job-manager-applications' );
		$columns['application_notes']       = '<span class="application_notes_head tips" data-tip="' . esc_attr__( 'Notes', 'wp-job-manager-applications' ) . '">' . esc_attr__( 'Notes', 'wp-job-manager-applications' ) . '</span>';
		$columns["attachment"]              = __( "Attachment(s)", 'wp-job-manager-applications' );

		if ( function_exists( 'get_resume_share_link' ) ) {
			$columns["online_resume"]              = __( "Resume", 'wp-job-manager-applications' );
		}

		$columns["job_application_posted"]  = __( "Posted", 'wp-job-manager-applications' );
		$columns['job_application_actions'] = __( "Actions", 'wp-job-manager-applications' );

		return $columns;
	}

	/**
	 * custom_columns function.
	 *
	 * @access public
	 * @param mixed $column
	 * @return void
	 */
	public function custom_columns( $column ) {
		global $post;

		switch ( $column ) {
			case "application_status" :
				echo '<span class="status">' . $post->post_status . '</a>';
			break;
			case "candidate" :
				echo '<a href="' . admin_url('post.php?post=' . $post->ID . '&action=edit') . '" class="tips candidate_name" data-tip="' . sprintf( __( 'Application ID: %d', 'wp-job-manager-applications' ), $post->ID ) . '">' . $post->post_title . '</a>';

				if ( $email = get_post_meta( $post->ID, '_candidate_email', true ) ) {
					echo '<br/><a href="mailto:' . esc_attr( $email ) . '">' . esc_attr( $email ) . '</a>';
					echo get_avatar( $email , 42 );
				}
				echo '<div class="hidden" id="inline_' . $post->ID . '"><div class="post_title">' . $post->post_title . '</div></div>';
			break;
			case 'job' :
				$job = get_post( $post->post_parent );

				if ( $job && $job->post_type === 'job_listing' ) {
					echo '<a href="' . get_permalink( $job->ID ) . '">' . $job->post_title . '</a>';
				} elseif ( $job = get_post_meta( $post->ID, '_job_applied_for', true ) ) {
					echo esc_html( $job );
				} else {
					echo '<span class="na">&ndash;</span>';
				}
			break;
			case 'attachment' :
				if ( $attachments = get_job_application_attachments( $post->ID ) ) {
					foreach ( $attachments as $attachment ) {
						echo '<a href="' . esc_url( $attachment ) . '">' . get_job_application_attachment_name( $attachment, 20 ) . '</a></br>';
					}
				} else {
					echo '<span class="na">&ndash;</span>';
				}
			break;
			case 'online_resume' :
				if ( ( $resume_id = get_job_application_resume_id( $post->ID ) ) && function_exists( 'get_resume_share_link' ) && $share_link = get_resume_share_link( $resume_id )  ) {
					echo '<a href="' . esc_attr( $share_link ) . '" target="_blank" class="job-application-resume">' . get_the_title( $resume_id ) . '</a>';
				} else {
					echo '<span class="na">&ndash;</span>';
				}
			break;
			case 'application_rating' :
				echo '<span class="job-application-rating"><span style="width: ' . ( ( get_job_application_rating( $post->ID ) / 5 ) * 100 ) . '%;"></span></span>';
			break;
			case 'application_notes' :
				printf( _n( '%d note', '%d notes', $post->comment_count, 'wp-job-manager-applications' ), $post->comment_count );
			break;
			case "job_application_posted" :
				echo '<strong>' . date_i18n( __( 'M j, Y', 'wp-job-manager-applications' ), strtotime( $post->post_date ) ) . '</strong><span>';
				echo ( empty( $post->post_author ) ? __( 'by a guest', 'wp-job-manager-applications' ) : sprintf( __( 'by %s', 'wp-job-manager-applications' ), '<a href="' . get_edit_user_link( $post->post_author ) . '">' . get_the_author() . '</a>' ) ) . '</span>';
			break;
			case "job_application_actions" :
				echo '<div class="actions">';
				$admin_actions           = array();
				if ( $post->post_status !== 'trash' ) {
					$admin_actions['view']   = array(
						'action'  => 'view',
						'name'    => __( 'View', 'wp-job-manager-applications' ),
						'url'     => get_edit_post_link( $post->ID )
					);
					$admin_actions['delete'] = array(
						'action'  => 'delete',
						'name'    => __( 'Delete', 'wp-job-manager-applications' ),
						'url'     => get_delete_post_link( $post->ID )
					);
				}

				$admin_actions = apply_filters( 'job_manager_job_applications_admin_actions', $admin_actions, $post );

				foreach ( $admin_actions as $action ) {
					printf( '<a class="icon-%s button tips" href="%s" data-tip="%s">%s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
				}

				echo '</div>';

			break;
		}
	}

	/**
	 * Filter applications
	 */
	public function restrict_manage_posts() {
		global $typenow, $wp_query, $wpdb;

		if ( 'job_application' != $typenow ) {
			return;
		}

		// Customers
		?>
		<select id="dropdown_job_listings" name="_job_listing">
			<option value=""><?php _e( 'Applications for all jobs', 'woocommerce' ) ?></option>
			<?php
				$jobs_with_applications = $wpdb->get_col( "SELECT DISTINCT post_parent FROM {$wpdb->posts} WHERE post_type = 'job_application';" );
				$current                = isset( $_GET['_job_listing'] ) ? $_GET['_job_listing'] : 0;
				foreach ( $jobs_with_applications as $job_id ) {
					if ( ( $title = get_the_title( $job_id ) ) && $job_id ) {
						echo '<option value="' . $job_id . '" ' . selected( $current, $job_id, false ) . '">' . $title . '</option>';
					}
				}
			?>
		</select>
		<?php
	}

 	/**
 	 * modify what applications are shown
 	 */
	public function request( $vars ) {
		global $typenow, $wp_query;

		if ( $typenow == 'job_application' && isset( $_GET['_job_listing'] ) && $_GET['_job_listing'] > 0 ) {
			$vars['post_parent'] = (int) $_GET['_job_listing'];
		}

		// Sorting
		if ( isset( $vars['orderby'] ) ) {
			if ( 'rating' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_rating',
					'orderby'  => 'meta_value_num'
				) );
			}
		}

		return $vars;
	}

	/**
	 * Sorting
	 */
	public function sortable_columns( $columns ) {
		$custom = array(
			'application_rating'     => 'rating',
			'candidate'              => 'post_title',
			'job_application_posted' => 'date',
			'job'                    => 'post_parent'
		);
		unset( $columns['comments'] );

		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Search custom fields as well as content.
	 * @param WP_Query $wp
	 */
	public function search_meta( $wp ) {
		global $pagenow, $wpdb;

		if ( 'edit.php' != $pagenow || empty( $wp->query_vars['s'] ) || $wp->query_vars['post_type'] != 'job_application' ) {
			return;
		}

		$post_ids = array_unique( array_merge(
			$wpdb->get_col(
				$wpdb->prepare( "
					SELECT posts.ID
					FROM {$wpdb->posts} posts
					INNER JOIN {$wpdb->postmeta} p1 ON posts.ID = p1.post_id
					WHERE p1.meta_value LIKE '%%%s%%'
					OR posts.post_title LIKE '%%%s%%'
					OR posts.post_content LIKE '%%%s%%'
					AND posts.post_type = 'job_application'
					",
					esc_attr( $wp->query_vars['s'] ),
					esc_attr( $wp->query_vars['s'] ),
					esc_attr( $wp->query_vars['s'] )
				)
			),
			array( 0 )
		) );

		// Adjust the query vars
		unset( $wp->query_vars['s'] );
		$wp->query_vars['job_application_search'] = true;
		$wp->query_vars['post__in'] = $post_ids;
	}

	/**
	 * Change the label when searching meta.
	 * @param string $query
	 * @return string
	 */
	public function search_meta_label( $query ) {
		global $pagenow, $typenow;

		if ( 'edit.php' != $pagenow || $typenow != 'job_application' || ! get_query_var( 'job_application_search' ) ) {
			return $query;
		}

		return wp_unslash( sanitize_text_field( $_GET['s'] ) );
	}

	/**
	 * Add statuses to admin
	 */
	public function add_custom_statuses() {
		global $typenow;

		if ( 'job_application' === $typenow ) {
			echo "<script>jQuery(document).ready( function() {";
			echo "jQuery( 'select[name=\"_status\"]' ).find('option[value!=\"-1\"]').remove();";
			foreach( get_job_application_statuses() as $key => $value ) {
				echo "jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"" . esc_attr( $key ) . "\">" . esc_attr( $value ) . "</option>' );";
			}
			echo "});</script>";
		}
	}
}
new WP_Job_Manager_Applications_Admin();
