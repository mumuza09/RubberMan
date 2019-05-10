<?php
/**
 * Digi Store Pro functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Digi_Store_Pro
 */

if ( ! function_exists( 'shopping_store_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function shopping_store_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on City Digi Store, use a find and replace
		 * to change 'shopping-store-lite' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'shopping-store-lite', get_template_directory() . '/languages' );

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
		add_theme_support( 'woocommerce' );

        // This theme uses wp_nav_menu() in one location.
        add_action('init','register_shopping_store_menu');
        function register_shopping_store_menu(){
            register_nav_menus(array(
                'primary' => esc_html__('Primary Menu', 'shopping-store-lite'),
                'footer' => esc_html__('Footer Menu', 'shopping-store-lite'),
                ));
        }


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

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'shopping_store_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );




        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
        add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio' ) );

    }
endif;
add_action( 'after_setup_theme', 'shopping_store_setup' );
add_image_size('shopping_store_woo_image', 480, 480, true);

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
if (! function_exists('shopping_store_content_width') ) {

    function shopping_store_content_width()
    {
        $GLOBALS['content_width'] = apply_filters('shopping_store_content_width', 640);
    }
}
add_action( 'after_setup_theme', 'shopping_store_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
if (! function_exists('shopping_store_widgets_init') ) {

    function shopping_store_widgets_init()
    {
        register_sidebar(array(
            'name' => esc_html__('Sidebar', 'shopping-store-lite'),
            'id' => 'sidebar-1',
            'description' => esc_html__('Add widgets here.', 'shopping-store-lite'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Woocommerce Sidebar', 'shopping-store-lite'),
            'id' => 'woo-sidebar',
            'description' => esc_html__('Add widgets here.', 'shopping-store-lite'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Pre-footer 1', 'shopping-store-lite'),
            'id' => 'shopping_store_footer_1',
            'description' => '',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Pre-footer 2', 'shopping-store-lite'),
            'id' => 'shopping_store_footer_2',
            'description' => '',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Pre-footer 3', 'shopping-store-lite'),
            'id' => 'shopping_store_footer_3',
            'description' => '',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ));
        register_sidebar(array(
            'name' => esc_html__('Pre-footer 4', 'shopping-store-lite'),
            'id' => 'shopping_store_footer_4',
            'description' => '',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ));
    }
}
add_action( 'widgets_init', 'shopping_store_widgets_init' );

require get_template_directory() . '/lib/shopping-store-tgmp.php';

get_template_part('plugin', 'activation');


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/lib/shopping-store-enqueue.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-breadcrumb.php';
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/template-parts/header/shopping-store-menu.php';


/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/lib/shopping-store-function.php';
require get_template_directory() . '/inc/customizer/shopping-store-default-values.php';
require get_template_directory() . '/inc/customizer/functions/customizer-control.php';
require get_template_directory() . '/inc/customizer/functions/sanitize-functions.php';
require get_template_directory() . '/inc/customizer/shopping-store-color-picker/shopping-store-color-picker.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}
if(!function_exists('shopping_store_get_theme_options')):
    function shopping_store_get_theme_options() {
        return wp_parse_args(  get_option( 'shopping_store_theme_options', array() ), shopping_store_get_option_defaults_values() );
    }
endif;

if (! function_exists('shopping_store_add_editor_styles') ) {
    function shopping_store_add_editor_styles() {
        add_editor_style( 'assets/css/editor-style.css' );
    }
    add_action( 'admin_init', 'shopping_store_add_editor_styles' );
}


if ( ! function_exists ( 'shopping_store_demo_import_files' ) ) {

    function shopping_store_demo_import_files()
    {
        return array(
            array(
                'import_file_name' => esc_html__('Demo', 'shopping-store-lite'),
                'import_file_url' => ('http://pridethemes.com/demo-content/shopping-store-lite/import.xml'),
                'import_widget_file_url' => ('http://pridethemes.com/demo-content/shopping-store-lite/widgets.wie'),
                'import_customizer_file_url' => ('http://pridethemes.com/demo-content/shopping-store-lite/customizer-import.dat'),
                'import_notice' => esc_html__('Click On Import Demo Data And Please Wait It May Take A While.', 'shopping-store-lite'),
                'preview_url' => ('http://demos.pridethemes.com/shopping-store-lite/'),
            ),


        );
    }

    add_filter('pt-ocdi/import_files', 'shopping_store_demo_import_files');
    add_action( 'pt-ocdi/after_import', 'shopping_store_after_import_setup' );



    function shopping_store_after_import_setup() {
        // Assign menus to their locations.
        $main_menu = get_term_by( 'name', 'Primary Menu', 'nav_menu' );

        set_theme_mod( 'nav_menu_locations', array(
                'main-menu' => $main_menu->term_id,
            )
        );
        // Assign front page and posts page (blog page).
        $front_page_id = get_page_by_title( 'Home' );
        $blog_page_id  = get_page_by_title( 'Blog' );

        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $front_page_id->ID );
        update_option( 'page_for_posts', $blog_page_id->ID );

    }
}