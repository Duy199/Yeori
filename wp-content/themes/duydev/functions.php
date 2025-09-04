<?php
/**
 * DuyDev functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package DuyDev
 */


 if ( ! defined( 'DUYDEV_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'DUYDEV_VERSION', '1.0.0' );
}

if ( ! defined ( 'DUYDEV_ENVIROMENT' ) ) {
	// development, staging, production
	if ( strpos( $_SERVER['HTTP_HOST'], 'localhost' ) !== false ) {
		define( 'DUYDEV_ENVIROMENT', 'development' );
	} else if ( strpos( $_SERVER['HTTP_HOST'], 'samuelw71.sg-host' ) !== false ) 	{
		define( 'DUYDEV_ENVIROMENT', 'staging' );
	} else {
		define( 'DUYDEV_ENVIROMENT', 'production' );
	}
}

if ( ! defined( 'DUYDEV_HOME' ) ) {
	define( 'DUYDEV_HOME', get_home_url() );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function duydev_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on DuyDev, use a find and replace
		* to change 'duydev' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'duydev', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary', 'duydev' ),
			
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'duydev_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'duydev_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function duydev_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'duydev_content_width', 640 );
}
add_action( 'after_setup_theme', 'duydev_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function duydev_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'duydev' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'duydev' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'duydev_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function duydev_scripts() {
    wp_enqueue_style( 'duydev-style', get_template_directory_uri() . '/assets/css/all.min.css', array(), DUYDEV_VERSION );
	// wp_enqueue_script( 'tailwind', "https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4", array(), DUYDEV_VERSION, false );
	// wp_enqueue_style( 'index', get_template_directory_uri() . '/assets/css/index.css', array(), DUYDEV_VERSION, false );
    wp_enqueue_script( 'duydev-script', get_template_directory_uri() . '/assets/js/all.min.js', array(), DUYDEV_VERSION, true );

	
	// Enqueue Google Fonts: Koho, Cabin
	wp_enqueue_style('koho-fonts', 'https://fonts.googleapis.com/css2?family=KoHo:wght@300;400;500;600;700&display=swap', false);
	wp_enqueue_style('cabin-fonts', 'https://fonts.googleapis.com/css2?family=Cabin:wght@400;500;600;700&display=swap', false);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

    // wp_localize_script('duydev-script', 'quote_info', array(
    //     'ajaxurl' => admin_url('admin-ajax.php'),
    //     'nonce'   => wp_create_nonce('quote_nonce'),
    // ));

}
add_action( 'wp_enqueue_scripts', 'duydev_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

// Add all Short code
require get_template_directory() . '/short-codes/all-short-code.php';

// Handle all Ajax
require get_template_directory() . '/inc/ajax/all-handle.php';

// add cpt to wordpress
require get_template_directory() . '/inc/cpt.php';


/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

// Add svg
function add_svg_to_allowed_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['avif'] = 'image/avif';
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'add_svg_to_allowed_mime_types');

// Create site settings




