<?php
/**
 * boda functions and definitions
 *
 * @package boda
 */

if ( ! function_exists( 'boda_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function boda_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on boda, use a find and replace
	 * to change 'boda' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'boda', get_template_directory() . '/languages' );

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
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'boda' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'boda_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // boda_setup
add_action( 'after_setup_theme', 'boda_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
/*
function boda_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'boda_content_width', 560 );
}
add_action( 'after_setup_theme', 'boda_content_width', 0 );
*/

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function boda_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'boda' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'boda_widgets_init' );


////////custom post types//////////////////////



// QUOTES
function create_posttype() {

	register_post_type( 'quotes',
	// CPT Options
		array(
			'labels' => array(
				'name' => __( 'Quotes' ),
				'singular_name' => __( 'Quote' )
			),

      'menu_position' => 5,
			'public' => true,
			'supports'      => array( 'title', 'custom-fields'),
      'taxonomies' => array('category'),
			'has_archive' => false,
			'rewrite' => array('slug' => 'quotes'),
		)
	);
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );



// BIOs
function create_posttype_2() {

	register_post_type( 'bios',
	// CPT Options
		array(
			'labels' => array(
				'name' => __( 'Bios' ),
				'singular_name' => __( 'Bio' )
			),

      'menu_position' => 6,
			'public' => true,
			'supports'      => array( 'title', 'editor', 'custom-fields' ),
      'taxonomies' => array('category'),
			'has_archive' => false,
			'rewrite' => array('slug' => 'bio'),
		)
	);
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype_2' );









////////short codes//////////////////////

//add lorem ipsum
function lorem_function() {
  return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec nulla vitae lacus mattis volutpat eu at sapien. Nunc interdum congue libero, quis laoreet elit sagittis ut. Pellentesque lacus erat, dictum condimentum pharetra vel, malesuada volutpat risus. Nunc sit amet risus dolor. Etiam posuere tellus nisl. Integer lorem ligula, tempor eu laoreet ac, eleifend quis diam. Proin cursus, nibh eu vehicula varius, lacus elit eleifend elit, eget commodo ante felis at neque. Integer sit amet justo sed elit porta convallis a at metus. Suspendisse molestie turpis pulvinar nisl tincidunt quis fringilla enim lobortis. Curabitur placerat quam ac sem venenatis blandit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam sed ligula nisl. Nam ullamcorper elit id magna hendrerit sit amet dignissim elit sodales. Aenean accumsan consectetur rutrum.';
}

add_shortcode('lorem', 'lorem_function');



//add PDF icon
function pdf_icon() {
  return '<img src="http://localhost/boda/wp-content/uploads/2015/06/pdf_icon1.png">';
}

add_shortcode('PDF', 'pdf_icon');








/**
 * Enqueue scripts and styles.
 */
function boda_scripts() {
	wp_enqueue_style( 'boda-style', get_stylesheet_uri() );
	


  wp_enqueue_style( 'boda-google-fonts', 'http://fonts.googleapis.com/css?family=Rokkitt:400,700|Montserrat:400,700' );

	

	wp_enqueue_style( 'boda-fontawesome', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );




	wp_enqueue_script( 'boda-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'boda-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'boda_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
