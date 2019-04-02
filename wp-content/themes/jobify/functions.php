<?php
/**
 * Jobify functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * see http://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 * @subpackage Jobify
 * @since Jobify 1.0
 */

/**
 * Sets up the content width value based on the theme's design.
 * @see jobify_content_width() for template-specific adjustments.
 */
if ( ! isset( $content_width ) )
	$content_width = 680;

/**
 * Sets up theme defaults and registers the various WordPress features that
 * Jobify supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for automatic feed links, post
 * formats, admin bar, and post thumbnails.
 * @uses register_nav_menu() To add support for a navigation menu.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_setup() {
	/*
	 * Makes Jobify available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Jobify, use a find and
	 * replace to change 'jobify' to the name of your theme in all
	 * template files.
	 */
	$locale = apply_filters( 'plugin_locale', get_locale(), 'jobify' );
	load_textdomain( 'jobify', WP_LANG_DIR . "/jobify-$locale.mo" );
	load_theme_textdomain( 'jobify', get_template_directory() . '/languages' );

	// Editor style
	add_editor_style();

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// Add support for custom background
	add_theme_support( 'custom-background', array(
		'default-color'    => '#ffffff'
	) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary'       => __( 'Navigation Menu', 'jobify' ),
		'footer-social' => __( 'Footer Social', 'jobify' )
	) );

	/** Shortcodes */
	add_filter( 'widget_text', 'do_shortcode' );

	/*
	 * This theme uses a custom image size for featured images, displayed on
	 * "standard" posts and pages.
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'content-grid', 400, 200, true );
	add_image_size( 'content-job-featured', 1350, 525, true );

	/**
	 * Misc
	 */
	add_filter( 'excerpt_more', '__return_false' );
}
add_action( 'after_setup_theme', 'jobify_setup' );

/**
 * Returns the Google font stylesheet URL, if available.
 *
 * The use of Source Sans Pro and Bitter by default is localized. For languages
 * that use characters not supported by the font, the font can be disabled.
 *
 * @since Jobify 1.0
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function jobify_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Montserrat, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$montserrat = _x( 'on', 'Montserrat font: on or off', 'jobify' );

	/* Translators: If there are characters in your language that are not
	 * supported by Varela Round, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$varela = _x( 'on', 'Varela Round font: on or off', 'jobify' );

	if ( 'off' !== $montserrat || 'off' !== $varela ) {
		$font_families = array();

		if ( 'off' !== $montserrat )
			$font_families[] = 'Montserrat:400,700';

		if ( 'off' !== $varela )
			$font_families[] = 'Varela+Round';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => implode( '|', $font_families ),
			'subset' => 'latin',
		);
		$fonts_url = esc_url_raw( add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ) );
	}

	return $fonts_url;
}

/**
 * Adds additional stylesheets to the TinyMCE editor if needed.
 *
 * @uses jobify_fonts_url() to get the Google Font stylesheet URL.
 *
 * @since Jobify 1.0
 *
 * @param string $mce_css CSS path to load in TinyMCE.
 * @return string
 */
function jobify_mce_css( $mce_css ) {
	$fonts_url = jobify_fonts_url();

	if ( empty( $fonts_url ) )
		return $mce_css;

	if ( ! empty( $mce_css ) )
		$mce_css .= ',';

	$mce_css .= esc_url_raw( str_replace( ',', '%2C', $fonts_url ) );

	return $mce_css;
}
add_filter( 'mce_css', 'jobify_mce_css' );

/**
 * Loads our special font CSS file.
 *
 * To disable in a child theme, use wp_dequeue_style()
 * function mytheme_dequeue_fonts() {
 *     wp_dequeue_style( 'jobify-fonts' );
 * }
 * add_action( 'wp_enqueue_scripts', 'mytheme_dequeue_fonts', 11 );
 *
 * Also used in the Appearance > Header admin panel:
 * @see twentythirteen_custom_header_setup()
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_fonts() {
	$fonts_url = jobify_fonts_url();

	if ( ! empty( $fonts_url ) )
		wp_enqueue_style( 'jobify-fonts', esc_url_raw( $fonts_url ), array(), null );
}
add_action( 'wp_enqueue_scripts', 'jobify_fonts' );

/**
 * Enqueues scripts and styles for front end.
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_scripts_styles() {
	global $wp_styles, $edd_options, $post;

	/*
	 * Adds JavaScript to pages with the comment form to support sites with
	 * threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_deregister_script( 'wp-job-manager-job-application' );

	$deps = array( 'jquery' );

	if ( class_exists( 'WooCommerce' ) )
		$deps[] = 'woocommerce';

	wp_enqueue_script( 'jobify', get_template_directory_uri() . '/js/jobify.min.js', $deps, 20140416, true );

	/**
	 * Localize/Send data to our script.
	 */
	$jobify_settings = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'archiveurl' => get_post_type_archive_link( 'job_listing' ),
		'i18n'    => array(

		),
		'pages'   => array(
			'is_widget_home'  => is_page_template( 'page-templates/jobify.php' ),
			'is_job'          => is_singular( 'job_listing' ),
			'is_resume'       => is_singular( 'resume' ),
			'is_testimonials' => is_page_template( 'page-templates/testimonials.php' ) || is_post_type_archive( 'testimonial' )
		),
		'widgets' => array()
	);

	foreach ( jobify_homepage_widgets() as $widget ) {
		$options = get_option( 'widget_' . $widget[ 'classname' ] );

		if ( ! isset( $widget[ 'callback' ][0] ) )
			continue;

		$options = $options[ $widget[ 'callback' ][0]->number ];

		$jobify_settings[ 'widgets' ][ $widget[ 'classname' ] ] = array(
			'animate' => isset ( $options[ 'animations' ] ) && 1 == $options[ 'animations' ] ? 1 : 0
		);
	}

	wp_localize_script( 'jobify', 'jobifySettings', $jobify_settings );

	/** Styles */
	wp_enqueue_style( 'jobify-parent', get_template_directory_uri() . '/style.css' );

	wp_dequeue_style( 'wp-job-manager-resume-frontend' );
}
add_action( 'wp_enqueue_scripts', 'jobify_scripts_styles' );

/**
 * Get all widgets used on the home page.
 *
 * @since Jobify 1.0
 *
 * @return array $_widgets An array of active widgets
 */
function jobify_homepage_widgets() {
	global $wp_registered_sidebars, $wp_registered_widgets;

	$index            = 'widget-area-front-page';
	$sidebars_widgets = wp_get_sidebars_widgets();
	$_widgets         = array();

	if ( empty( $sidebars_widgets ) || empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
		return $_widgets;

	foreach ( (array) $sidebars_widgets[$index] as $id ) {
		$_widgets[] = isset( $wp_registered_widgets[$id] ) ? $wp_registered_widgets[$id] : null;
	}

	return $_widgets;
}

/**
 * Creates a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @since Jobify 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function jobify_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'jobify' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'jobify_wp_title', 10, 2 );

/**
 * Registers widgets, and widget areas.
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_widgets_init() {
	register_widget( 'Jobify_Widget_Callout' );
	register_widget( 'Jobify_Widget_Video' );
	register_widget( 'Jobify_Widget_Blog_Posts' );
	register_widget( 'Jobify_Widget_Slider_Generic' );

	register_sidebar( array(
		'name'          => __( 'Homepage Widget Area', 'jobify' ),
		'id'            => 'widget-area-front-page',
		'description'   => __( 'Choose what should display on the custom static homepage.', 'jobify' ),
		'before_widget' => '<section id="%1$s" class="homepage-widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="homepage-widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Sidebar', 'jobify' ),
		'id'            => 'sidebar-blog',
		'description'   => __( 'Choose what should display on blog pages.', 'jobify' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="sidebar-widget-title">',
		'after_title'   => '</h3>',
	) );

	/*
	 * Figure out how many columns the footer has
	 */
	$the_sidebars = wp_get_sidebars_widgets();
	$footer       = isset ( $the_sidebars[ 'widget-area-footer' ] ) ? $the_sidebars[ 'widget-area-footer' ] : array();
	$count        = count( $footer );
	$count        = floor( 12 / ( $count == 0 ? 1 : $count ) );

	register_sidebar( array(
		'name'          => __( 'Footer Widget Area', 'jobify' ),
		'id'            => 'widget-area-footer',
		'description'   => __( 'Display columns of widgets in the footer.', 'jobify' ),
		'before_widget' => '<aside id="%1$s" class="footer-widget %2$s col-md-' . $count . ' col-sm-6 col-xs-12">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="footer-widget-title">',
		'after_title'   => '</h3>',
	) );

	if ( ! ( defined( 'RCP_PLUGIN_VERSION' ) || class_exists( 'WooCommerce' ) ) ) {
		register_widget( 'Jobify_Widget_Price_Table' );
		register_widget( 'Jobify_Widget_Price_Option' );

		register_sidebar( array(
			'name'          => __( 'Price Table', 'jobify' ),
			'id'            => 'widget-area-price-options',
			'description'   => __( 'Drag multiple "Price Option" widgets here. Then drag the "Pricing Table" widget to the "Homepage Widget Area".', 'jobify' ),
			'before_widget' => '<div class="col-lg-4 col-md-6 col-sm-12 pricing-table-widget-wrapper"><div id="%1$s" class="pricing-table-widget %2$s">',
			'after_widget'  => '</div></div>'
		) );
	}
}
add_action( 'widgets_init', 'jobify_widgets_init' );

/**
 * Extends the default WordPress body class to denote:
 * 1. Custom fonts enabled.
 *
 * @since Jobify 1.0
 *
 * @param array $classes Existing class values.
 * @return array Filtered class values.
 */
function jobify_body_class( $classes ) {
	if ( wp_style_is( 'jobify-fonts', 'queue' ) )
		$classes[] = 'custom-font';

	if ( class_exists( 'WP_Job_Manager_Job_Tags' ) )
		$classes[] = 'wp-job-manager-tags';

	return $classes;
}
add_filter( 'body_class', 'jobify_body_class' );

/**
 * Extends the default WordPress comment class to add 'no-avatars' class
 * if avatars are disabled in discussion settings.
 *
 * @since Jobify 1.0
 *
 * @param array $classes Existing class values.
 * @return array Filtered class values.
 */
function jobify_comment_class( $classes ) {
	if ( ! get_option( 'show_avatars' ) )
		$classes[] = 'no-avatars';

	return $classes;
}
add_filter( 'comment_class', 'jobify_comment_class' );

/**
 * Adds a class to menu items that have children elements
 * so that they can be styled
 *
 * @since Jobify 1.0
 */
function jobify_add_menu_parent_class( $items ) {
	$parents = array();

	foreach ( $items as $item ) {
		if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
			$parents[] = $item->menu_item_parent;
		}
	}

	foreach ( $items as $item ) {
		if ( in_array( $item->ID, $parents ) ) {
			$item->classes[] = 'has-children';
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'jobify_add_menu_parent_class' );

/**
 * Append modal boxes to the bottom of the the page that
 * will pop up when certain links are clicked.
 *
 * Login/Register pages must be set in EDD settings for this to work.
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_inline_modals() {
	if ( ! jobify_is_job_board() )
		return;

	$login = jobify_find_page_with_shortcode( array( 'jobify_login_form', 'login_form' ) );

	if ( 0 != $login )
		get_template_part( 'modal', 'login' );

	$register = jobify_find_page_with_shortcode( array( 'jobify_register_form', 'register_form' ) );

	if ( 0 != $register )
		get_template_part( 'modal', 'register' );
}
add_action( 'wp_footer', 'jobify_inline_modals' );

/**
 * If the menu item has a custom class, that means it is probably
 * going to be triggering a modal. The ID will be used to determine
 * the inline content to be displayed, so we need it to provide context.
 * This uses the specificed class name instead of `menu-item-x`
 *
 * @since Jobify 1.0
 *
 * @param string $id The ID of the current menu item
 * @param object $item The current menu item
 * @param array $args Arguments
 * @return string $id The modified menu item ID
 */
function jobify_nav_menu_item_id( $id, $item, $args ) {
	if ( ! empty( $item->classes[0] ) ) {
		return current($item->classes) . '-modal';
	}

	return $id;
}
add_filter( 'nav_menu_item_id', 'jobify_nav_menu_item_id', 10, 3 );

/**
 * Pagination
 *
 * After the loop, attach pagination to the current query.
 *
 * @since Jobify 1.0
 *
 * @return void
 */
function jobify_pagination() {
	global $wp_query;

	$big = 999999999; // need an unlikely integer

	$links = paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'total' => $wp_query->max_num_pages,
		'prev_text' => '<i class="icon-left-open-big"></i>',
		'next_text' => '<i class="icon-right-open-big"></i>'
	) );
?>
	<div class="paginate-links container">
		<?php echo $links; ?>
	</div>
<?php
}
add_action( 'jobify_loop_after', 'jobify_pagination' );

if ( ! function_exists( 'jobify_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments
 * template simply create your own twentythirteen_comment(), and that function
 * will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Thirteen 1.0
 *
 * @param object $comment Comment to display.
 * @param array $args Optional args.
 * @param int $depth Depth of comment.
 * @return void
 */
function jobify_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<p><?php _e( 'Pingback:', 'jobify' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'jobify' ), '<span class="ping-meta"><span class="edit-link">', '</span></span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
	?>
	<li id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
			<div class="comment-avatar">
				<?php echo get_avatar( $comment, 75 ); ?>
			</div><!-- .comment-author -->

			<header class="comment-meta">
				<span class="comment-author vcard"><cite class="fn"><?php comment_author_link(); ?></cite></span>
				<?php echo _x( 'on', 'comment author "on" date', 'jobify' ); ?>
				 <?php
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						sprintf( _x( '%1$s at %2$s', 'on 1: date, 2: time', 'jobify' ), get_comment_date(), get_comment_time() )
					);
					edit_comment_link( __( 'Edit', 'jobify' ), '<span class="edit-link"><i class="icon-pencil"></i> ', '<span>' );

					comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'jobify' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'jobify' ); ?></p>
			<?php endif; ?>

			<div class="comment-content">
				<?php comment_text(); ?>
			</div><!-- .comment-content -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // End comment_type check.
}
endif;

if ( ! function_exists( 'shortcode_exists' ) ) :
/**
 * Whether a registered shortcode exists named $tag
 *
 * @since 3.6.0
 *
 * @global array $shortcode_tags
 * @param string $tag
 * @return boolean
 */
function shortcode_exists( $tag ) {
	global $shortcode_tags;
	return array_key_exists( $tag, $shortcode_tags );
}
endif;

if ( ! function_exists( 'has_shortcode' ) ) :
/**
 * Whether the passed content contains the specified shortcode
 *
 * @since 3.6.0
 *
 * @global array $shortcode_tags
 * @param string $tag
 * @return boolean
 */
function has_shortcode( $content, $tag ) {
	if ( shortcode_exists( $tag ) ) {
		preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
		if ( empty( $matches ) )
			return false;

		foreach ( $matches as $shortcode ) {
			if ( $tag === $shortcode[2] )
				return true;
		}
	}
	return false;
}
endif;

/**
 * Is WP Job Manager active?
 *
 * @since Jobify 1.0.0
 *
 * @return boolean
 */
function jobify_is_job_board() {
	return class_exists( 'WP_Job_Manager' );
}

function jobify_rcp_subscription_selector( $settings ) {
	if ( ! defined( 'RCP_PLUGIN_VERSION' ) ) {
		return $settings;
	}

	$levels  = rcp_get_subscription_levels( 'all' );

	if ( empty( $levels ) ) {
		return $settings;
	}

	$keys    = wp_list_pluck( $levels, 'id' );
	$names   = wp_list_pluck( $levels, 'name' );

	if ( ! ( is_array( $keys ) && is_array( $names ) ) ) {
		return $settings;
	}

	$options = array_combine( $keys, $names );

	$settings[ 'subscription' ] = array(
		'label'   => __( 'Subscription Level Visibility:', 'jobify' ),
		'std'     => 0,
		'type'    => 'multicheck',
		'options' => $options
	);

	return $settings;
}

/**
 * Find pages that contain shortcodes.
 *
 * To avoid options, try to find pages for them.
 *
 * @since Jobify 1.0
 *
 * @return $_page
 */
function jobify_find_page_with_shortcode( $shortcodes ) {
	if ( ! is_array( $shortcodes ) )
		$shortcode = array( $shortcodes );

	$_page = 0;

	foreach ( $shortcodes as $shortcode ) {
		if ( ! get_option( 'job_manager_page_' . $shortcode ) ) {
			$pages = new WP_Query( array(
				'post_type'              => 'page',
				'post_status'            => 'publish',
				'ignore_sticky_posts'    => 1,
				'no_found_rows'          => true,
				'nopaging'               => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false
			) );

			while ( $pages->have_posts() ) {
				$pages->the_post();

				if ( has_shortcode( get_post()->post_content, $shortcode ) ) {
					$_page = get_post()->ID;

					break;
				}
			}

			add_option( 'job_manager_page_' . $shortcode, $_page );
		} else {
			$_page = get_option( 'job_manager_page_' . $shortcode );
		}

		if ( $_page > 0 )
			break;
	}

	return $_page;
}

/**
 * When a login fails (kinda) redirect them back
 * to the login page.
 *
 * @since Jobify 1.7.0
 */
function jobify_login_fail( $username ){
	$page = jobify_find_page_with_shortcode( array( 'jobify_login_form', 'login_form' ) );
	$page = get_permalink( $page );

	wp_redirect( esc_url( add_query_arg( 'login', 'failed', $page ) ) );

    exit();
}

function jobify_authenticate_username_password( $user, $username, $password ) {
	if ( is_a( $user, 'WP_User' ) ) {
		return $user;
	}

	if ( empty($username) || empty($password) ) {
		$page = jobify_find_page_with_shortcode( array( 'jobify_login_form', 'login_form' ) );
		$page = get_permalink( $page );

		wp_redirect( $page );

	    exit();
	}
}

function jobify_theme_login_page() {
	if ( jobify_theme_mod( 'jobify_general', 'theme_login' ) ) {
		add_action( 'wp_login_failed', 'jobify_login_fail' );
		add_filter( 'authenticate', 'jobify_authenticate_username_password', 30, 3);
	}
}
add_action( 'init', 'jobify_theme_login_page' );

/** TGM Plugin Activation */
if ( apply_filters( 'jobify_use_tgmpa', '__return_true' ) ) {
	require_once( get_template_directory() . '/inc/tgmpa/plugins.php' );
}

/** Custom Header */
require_once( get_template_directory() . '/inc/custom-header.php' );

/** Customizer */
require_once( get_template_directory() . '/inc/theme-customizer.php' );

/** Widgets */
require_once( get_template_directory() . '/inc/class-widget.php' );

$widgets = array(
	'class-widget-callout.php',
	'class-widget-video.php',
	'class-widget-blog-posts.php',
	'class-widget-slider-generic.php',
	'class-widget-stats.php'
);

foreach ( $widgets as $widget ) {
	require_once( get_template_directory() . '/inc/widgets/' . $widget );
}

if ( ! ( defined( 'RCP_PLUGIN_VERSION' ) || class_exists( 'WooCommerce' ) ) ) {
	require_once( get_template_directory() . '/inc/widgets/class-widget-price-option.php' );
	require_once( get_template_directory() . '/inc/widgets/class-widget-price-table.php' );
}

$integrations = array(
	'wp-job-manager' => class_exists( 'WP_Job_Manager' ),
	'wp-job-manager-bookmarks' => class_exists( 'WP_Job_Manager_Bookmarks' ),
	'wp-job-manager-tags' => class_exists( 'WP_Job_Manager_Job_Tags' ),
	'wp-job-manager-application-deadline' => class_exists( 'WP_Job_Manager_Application_Deadline' ),
	'wp-job-manager-applications' => class_exists( 'WP_Job_Manager_Applications' ),
	'wp-job-manager-apply-linkedin' => class_exists( 'WP_Job_Manager_Apply_With_Linkedin' ),
	'wp-job-manager-contact-listing' => class_exists( 'Astoundify_Job_Manager_Contact_Listing' ),
	'wp-job-manager-extended-location' => class_exists( 'WP_Job_Manager_Extended_Location' ),
	'wp-resume-manager' => class_exists( 'WP_Resume_Manager' ),
	'restrict-content-pro' => defined( 'RCP_PLUGIN_VERSION' ),
	'woocommerce' => class_exists( 'WooCommerce' ) && defined( 'JOB_MANAGER_WCPL_VERSION' ),
	'testimonials' => class_exists( 'Woothemes_Testimonials' ),
	'soliloquy' => function_exists( 'soliloquy' ),
	'gravityforms' => function_exists( 'gravity_form' ) && class_exists( 'Astoundify_Job_Manager_Contact_Listing' ),
	'jetpack' => class_exists( 'Jetpack' )
);

foreach ( $integrations as $key => $dep ) {
	if ( $dep ) {
		require_once( get_template_directory() . '/inc/integrations/' . $key . '/class-' . $key . '.php' );
	}
}







