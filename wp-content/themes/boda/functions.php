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
	add_image_size( 'blog-images', 250, 250, false);

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
/*
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );
*/

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

/*------- Register widget areas / sidebars ------*/
//main
function boda_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'General Sidebar', 'boda' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s general-widget">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title general-widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'boda_widgets_init' );


//blog
function boda_widgets_init2() {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'boda' ),
		'id'            => 'blog',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s blog-widget">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title blog-widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'boda_widgets_init2' );


//home
function boda_widgets_init3() {
	register_sidebar( array(
		'name'          => esc_html__( 'Home Buckets', 'boda' ),
		'id'            => 'home-buckets',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s home-widget">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title home-widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'boda_widgets_init3' );


//case studies
function boda_widgets_init4() {
	register_sidebar( array(
		'name'          => esc_html__( 'List on Cases Page (not sidebar)', 'boda' ),
		'id'            => 'cases',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s case-widget">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title case-widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'boda_widgets_init4' );


//footer
function boda_widgets_init5() {
	register_sidebar( array(
		'name'          => esc_html__( 'Footer', 'boda' ),
		'id'            => 'footer',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s footer-widget">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title footer-widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'boda_widgets_init5' );


//newsletter
function boda_widgets_init6() {
	register_sidebar( array(
		'name'          => esc_html__( 'Newsletter Sidebar', 'boda' ),
		'id'            => 'newsletter',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s newsletter-widget">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title newsletter-widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'boda_widgets_init6' );




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




//people pix on home page
function create_posttype2() {

	register_post_type( 'home_people',
	// CPT Options
		array(
			'labels' => array(
				'name' => __( 'Home People' ),
				'singular_name' => __( 'Home People' )
			),

      'menu_position' => 6,
			'public' => true,
			'supports'      => array( 'title', 'editor'),
      'taxonomies' => array('category'),
			'has_archive' => false,
			'rewrite' => array('slug' => 'home_people'),
		)
	);
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype2' );









////////short codes//////////////////////

//add lorem ipsum
function lorem_function() {
  return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec nulla vitae lacus mattis volutpat eu at sapien. Nunc interdum congue libero, quis laoreet elit sagittis ut. Pellentesque lacus erat, dictum condimentum pharetra vel, malesuada volutpat risus. Nunc sit amet risus dolor. Etiam posuere tellus nisl. Integer lorem ligula, tempor eu laoreet ac, eleifend quis diam. Proin cursus, nibh eu vehicula varius, lacus elit eleifend elit, eget commodo ante felis at neque. Integer sit amet justo sed elit porta convallis a at metus. Suspendisse molestie turpis pulvinar nisl tincidunt quis fringilla enim lobortis. Curabitur placerat quam ac sem venenatis blandit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam sed ligula nisl. Nam ullamcorper elit id magna hendrerit sit amet dignissim elit sodales. Aenean accumsan consectetur rutrum.';
}

add_shortcode('lorem', 'lorem_function');



//add PDF icon
function pdf_icon() {
  return '<img src="../../wp-content/uploads/2015/06/pdf_icon1.png">';
}
add_shortcode('PDF', 'pdf_icon');


//book
function book_icon() {
  return '<img src="../../wp-content/uploads/2015/06/book-icon.png">';
}
add_shortcode('BOOK', 'book_icon');


//article
function article_icon() {
  return '<img src="../../wp-content/uploads/2015/06/article-icon.png">';
  }
add_shortcode('ARTICLE', 'article_icon');


//video
function video_icon() {
  return '<img src="../../wp-content/uploads/2015/06/video-icon.png">';
}
add_shortcode('VIDEO', 'video_icon');



//podcast
function podcast_icon() {
  return '<img src="../../wp-content/uploads/2015/06/podcast-icon.png">';
}
add_shortcode('PODCAST', 'podcast_icon');


//blogpost
function blogpost_icon() {
  return '<img src="../../wp-content/uploads/2015/06/blogpost-icon.png">';
}
add_shortcode('BLOGPOST', 'blogpost_icon');


 
//register scripts (load jquery)
add_action( 'wp_enqueue_scripts', 'boda_load_jquery' );

function boda_load_jquery() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-widget' );
    wp_enqueue_script( 'jquery-ui-position' );
    wp_enqueue_script( 'jquery-ui-tooltip' );
    //note: also need to load jquery ui css for tooltip to work right

//uncomment to load hovers script for tooltips    
/*     wp_enqueue_script( 'boda-hovers', get_template_directory_uri() . '/js/hovers.js', true ); */


}
  
add_action('init', 'boda_load_jquery');


//set excerpt length for blog index page
function custom_excerpt_length( $length ) {
	return 26;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );



//Enqueue scripts and styles.

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
