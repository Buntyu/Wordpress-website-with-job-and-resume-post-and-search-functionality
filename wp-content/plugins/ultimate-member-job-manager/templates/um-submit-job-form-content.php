<?php

/**
 * Ultimate Member Job Manager Submit Job Form Screens
 *
 * @package Ultimate Member Job Manager
 * @subpackage Job Manager Screens Template
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
?>
<?php 
add_filter( 'submit_job_form_show_signin', __return_false ); ?>
<?php echo do_shortcode( '[submit_job_form]' ); ?>