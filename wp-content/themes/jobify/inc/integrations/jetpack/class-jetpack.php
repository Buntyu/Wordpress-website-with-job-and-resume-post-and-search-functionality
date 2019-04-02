<?php
/**
 * Jetpack
 */

class Jobify_Jetpack {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'loop_start' ) );
		add_action( 'jobify_share_object', array( $this, 'output' ) );
	}

	public function loop_start() {
		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_excerpt', 'sharing_display', 19 );

		if ( class_exists( 'Jetpack_Likes' ) ) {
			remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
		}
	}

	public function output() {
		global $post;

		if ( ! function_exists( 'sharing_display' ) ) {
			return;
		}

		$buttons = sharing_display( '' );

		if ( '' == $buttons ) {
			return;
		}

		echo $buttons;
	}

}

$GLOBALS[ 'jobify_jetpack' ] = new Jobify_Jetpack();
