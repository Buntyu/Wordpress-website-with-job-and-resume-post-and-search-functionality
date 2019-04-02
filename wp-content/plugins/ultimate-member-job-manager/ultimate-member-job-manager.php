<?php
/*
Plugin Name: Ultimate Member Job Manager
Plugin URI: http://opentuteplus.com/
Description: This plugin integrates WP Job Manager and its extensions into your Ultimate Member user profiles. This plugin needs Ultimate Member and WP Job Manager to be installed. Ultimate Member Job Manager is compatible with the following WP Job Manager extensions: Applications, Bookmarks and Job Alerts.
Author: SuitePlugins
Author URI: http://suiteplugins.com
Version: 1.0.1
Requires at least: 3.8
Tested up to: 5.0.3
Network: true
Text Domain: ultimate-member-job-manager
Domain Path: /languages/

License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! defined( 'ULTIMATE_MEMBER_WP_JOB_MANAGER_PLUGIN_DIR ' ) ) {
	define( 'ULTIMATE_MEMBER_WP_JOB_MANAGER_PLUGIN_DIR',  untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'ULTIMATE_MEMBER_WP_JOB_MANAGER ' ) ) {
	define( 'ULTIMATE_MEMBER_WP_JOB_MANAGER', plugin_dir_path( __FILE__ ) . 'ultimate-member-components/wp-job-manager/' );
}


// I18n
add_action( 'plugins_loaded', 'ultimate_member_job_manager_load_textdomain' );
function ultimate_member_job_manager_load_textdomain() {
	$domain = 'ultimate-member-job-manager';

	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	// wp-content/languages/um-events/plugin-name-de_DE.mo
	load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );

	// wp-content/plugins/um-events/languages/plugin-name-de_DE.mo
	load_plugin_textdomain( $domain, false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

function init_um_wp_job_manager_component(){
	include( ULTIMATE_MEMBER_WP_JOB_MANAGER . 'class-um-wp-job-manager.php' );
}

add_action( 'init', 'init_um_wp_job_manager_component', 40 );


