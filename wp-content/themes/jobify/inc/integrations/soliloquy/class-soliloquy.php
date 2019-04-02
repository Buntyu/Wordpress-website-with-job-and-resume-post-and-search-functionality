<?php

class Jobify_Soliloquy {

	public function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	public function widgets_init() {
		require_once( get_template_directory() . '/inc/widgets/class-widget-slider-content.php' );
		require_once( get_template_directory() . '/inc/widgets/class-widget-slider-hero.php' );

		register_widget( 'Jobify_Widget_Slider' );
		register_widget( 'Jobify_Widget_Slider_Hero' );
	}

}

$GLOBALS[ 'jobify_soliloquy' ] = new Jobify_Soliloquy();