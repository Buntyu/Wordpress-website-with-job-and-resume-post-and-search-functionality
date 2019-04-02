<?php
/*
Plugin Name: WP Job Manager - Bookmarks
Plugin URI: https://wpjobmanager.com/add-ons/bookmarks/
Description: Allow logged in candidates and employers to bookmark jobs and resumes along with an added note.
Version: 1.1.7
Author: Mike Jolley
Author URI: http://mikejolley.com
Requires at least: 3.8
Tested up to: 3.9

	Copyright: 2014 Mike Jolley
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPJM_Updater' ) ) {
	include( 'includes/updater/class-wpjm-updater.php' );
}

/**
 * WP_Job_Manager_Bookmarks class.
 */
class WP_Job_Manager_Bookmarks extends WPJM_Updater {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Define constants
		define( 'JOB_MANAGER_BOOKMARKS_VERSION', '1.1.7' );
		define( 'JOB_MANAGER_BOOKMARKS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'JOB_MANAGER_BOOKMARKS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		// Add actions
		add_action( 'init', array( $this, 'init' ), 12 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'wp', array( $this, 'bookmark_handler' ) );
		add_action( 'single_job_listing_meta_after', array( $this, 'bookmark_form' ) );
		add_action( 'single_resume_start', array( $this, 'bookmark_form' ) );
		add_shortcode( 'my_bookmarks', array( $this, 'my_bookmarks' ) );

		// Activate
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'install' ) );

		// Init updates
		$this->init_updates( __FILE__ );
	}

	/**
	 * Localisation
	 */
	public function init() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-bookmarks' );
		load_textdomain( 'wp-job-manager-bookmarks', WP_LANG_DIR . "/wp-job-manager-bookmarks/wp-job-manager-bookmarks-$locale.mo" );
		load_plugin_textdomain( 'wp-job-manager-bookmarks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
		wp_register_script( 'wp-job-manager-bookmarks-bookmark-js', JOB_MANAGER_BOOKMARKS_PLUGIN_URL . '/assets/js/bookmark.min.js', array( 'jquery' ), JOB_MANAGER_BOOKMARKS_VERSION, true );
		wp_enqueue_style( 'wp-job-manager-bookmarks-frontend', JOB_MANAGER_BOOKMARKS_PLUGIN_URL . '/assets/css/frontend.css' );

		wp_localize_script( 'wp-job-manager-bookmarks-bookmark-js', 'job_manager_bookmarks', array(
			'i18n_confirm_delete' => __( 'Are you sure you want to delete this bookmark?', 'wp-job-manager-bookmarks' )
		) );
	}

	/**
	 * Install
	 */
	public function install() {
		global $wpdb;

		$wpdb->hide_errors();

		$collate = '';
	    if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
	    }

	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	    $sql = "
CREATE TABLE {$wpdb->prefix}job_manager_bookmarks (
  id bigint(20) NOT NULL auto_increment,
  user_id bigint(20) NOT NULL,
  post_id bigint(20) NOT NULL,
  bookmark_note longtext NULL,
  date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) $collate;
";
	    dbDelta( $sql );
	}

	/**
	 * Get a user's bookmarks
	 * @param  integer $user_id
	 * @return array
	 */
	public function get_user_bookmarks( $user_id = 0 ) {
		if ( ! $user_id && is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} elseif ( ! $user_id ) {
			return false;
		}

		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}job_manager_bookmarks WHERE user_id = %d ORDER BY date_created;", $user_id ) );
	}

	/**
	 * See if a post is bookmarked by ID
	 * @param  int post ID
	 * @return boolean
	 */
	public function is_bookmarked( $post_id ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}job_manager_bookmarks WHERE post_id = %d AND user_id = %d;", $post_id, get_current_user_id() ) ) ? true : false;
	}

	/**
	 * Get the total number of bookmarks for a post by ID
	 * @param  int $post_id
	 * @return int
	 */
	public function bookmark_count( $post_id ) {
		global $wpdb;

		if ( false === ( $bookmark_count = get_transient( 'bookmark_count_' . $post_id ) ) ) {
			$bookmark_count = absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( id ) FROM {$wpdb->prefix}job_manager_bookmarks WHERE post_id = %d;", $post_id ) ) );
			set_transient( 'bookmark_count_' . $post_id, $bookmark_count, YEAR_IN_SECONDS );
		}

		return absint( $bookmark_count );
	}

	/**
	 * Get a bookmark's note
	 * @param  int post ID
	 * @return string
	 */
	public function get_note( $post_id ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT bookmark_note FROM {$wpdb->prefix}job_manager_bookmarks WHERE post_id = %d AND user_id = %d;", $post_id, get_current_user_id() ) );
	}

	/**
	 * Handle the book mark form
	 */
	public function bookmark_handler() {
		global $wpdb;

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! empty( $_POST['submit_bookmark'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'update_bookmark' ) ) {
			$post_id = absint( $_POST['bookmark_post_id'] );
			$note    = wp_kses_post( stripslashes( $_POST['bookmark_notes'] ) );

			if ( $post_id && in_array( get_post_type( $post_id ), array( 'job_listing', 'resume' ) ) ) {
				if ( ! $this->is_bookmarked( $post_id ) ) {
					$wpdb->insert(
						"{$wpdb->prefix}job_manager_bookmarks",
						array(
							'user_id'       => get_current_user_id(),
							'post_id'       => $post_id,
							'bookmark_note' => $note,
							'date_created'  => current_time( 'mysql' )
						)
					);
				} else {
					$wpdb->update(
						"{$wpdb->prefix}job_manager_bookmarks",
						array(
							'bookmark_note' => $note
						),
						array(
							'post_id'       => $post_id,
							'user_id'       => get_current_user_id()
						)
					);
				}

				delete_transient( 'bookmark_count_' . $post_id );
			}
		}

		if ( ! empty( $_GET['remove_bookmark'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'remove_bookmark' ) ) {
			$post_id = absint( $_GET['remove_bookmark'] );

			$wpdb->delete(
				"{$wpdb->prefix}job_manager_bookmarks",
				array(
					'post_id'       => $post_id,
					'user_id'       => get_current_user_id()
				)
			);

			delete_transient( 'bookmark_count_' . $post_id );
		}
	}

	/**
	 * Show the bookmark form
	 */
	public function bookmark_form() {
		global $post, $resume_preview, $job_preview;

		if ( $resume_preview || $job_preview ) {
			return;
		}

		ob_start();

		$post_type = get_post_type_object( $post->post_type );

		if ( ! is_user_logged_in() ) {
			get_job_manager_template( 'logged-out-bookmark-form.php', array(
				'post_type'     => $post_type,
				'post'          => $post
			), 'wp-job-manager-bookmarks', JOB_MANAGER_BOOKMARKS_PLUGIN_DIR . '/templates/' );
		} else {
			$is_bookmarked = $this->is_bookmarked( $post->ID );

			if ( $is_bookmarked ) {
				$note = $this->get_note( $post->ID );
			} else {
				$note = '';
			}

			wp_enqueue_script( 'wp-job-manager-bookmarks-bookmark-js' );

			get_job_manager_template( 'bookmark-form.php', array(
				'post_type'     => $post_type,
				'post'          => $post,
				'is_bookmarked' => $is_bookmarked ,
				'note'          => $note
			), 'wp-job-manager-bookmarks', JOB_MANAGER_BOOKMARKS_PLUGIN_DIR . '/templates/' );
		}

		echo ob_get_clean();
	}

	/**
	 * User bookmarks shortcode
	 */
	public function my_bookmarks() {
		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your bookmarks.', 'wp-job-manager-bookmarks' );
		}

		ob_start();

		wp_enqueue_script( 'wp-job-manager-bookmarks-bookmark-js' );

		$bookmarks = $this->get_user_bookmarks();

		get_job_manager_template( 'my-bookmarks.php', array(
			'bookmarks'     => $bookmarks
		), 'wp-job-manager-bookmarks', JOB_MANAGER_BOOKMARKS_PLUGIN_DIR . '/templates/' );

		return ob_get_clean();
	}
}

$GLOBALS['job_manager_bookmarks'] = new WP_Job_Manager_Bookmarks();