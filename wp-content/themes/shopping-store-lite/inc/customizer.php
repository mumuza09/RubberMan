<?php
/**
 * Shopping Store Theme Customizer
 *
 * @package Shopping_Store
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function shopping_store_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'shopping_store_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'shopping_store_customize_partial_blogdescription',
		) );
	}

    $wp_customize->add_section('shopping_store_custom_header',
        array(
            'title' => esc_html__('General Options', 'shopping-store-lite'),
            'priority' => 2,
            'panel' => 'shopping_store_options_panel'
        ));

    $wp_customize->add_section('support_options', array(
        'title' => esc_html__('Header Phone And Email', 'shopping-store-lite'),
        'priority' => 2,
        'panel' => 'shopping_store_options_panel'
    ));
    $wp_customize->add_section('banner_options', array(
        'title' => esc_html__('Banner Option', 'shopping-store-lite'),
        'priority' => 3,
        'panel' => 'shopping_store_options_panel'
    ));
	// Top Call Out Section
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
        $wp_customize->add_section('top_call_options', array(
            'title' => esc_html__('Top Call Out Section', 'shopping-store-lite'),
            'priority' => 3,
            'panel' => 'shopping_store_options_panel'
        ));
        $wp_customize->add_section('shopping_store_grid_listing',array(
            'title' => esc_html__('Product Listing Section', 'shopping-store-lite'),
            'priority' => 5,
            'panel' => 'shopping_store_options_panel'
        ));
        $wp_customize->add_section('shopping_store_single_listing',array(
            'title' => esc_html__('Single Product Listing Section', 'shopping-store-lite'),
            'priority' => 9,
            'panel' => 'shopping_store_options_panel'
        ));
    }

    $wp_customize->add_section('shopping_store_cta_section',array(
        'title' => esc_html__('CTA Section', 'shopping-store-lite'),
        'priority' => 6,
        'panel' => 'shopping_store_options_panel'
    ));



    $wp_customize->add_section(
        'blog_options',
        array(
            'title'    => esc_html__( 'Blog Options','shopping-store-lite' ),
            'panel' => 'shopping_store_options_panel',
            'priority' => 24,
        )
    );
    $wp_customize->add_section('footer_options',
        array(
            'title' => esc_html__('Footer Options', 'shopping-store-lite'),
            'priority' => 10,
            'panel' => 'shopping_store_options_panel'
        ));

    require get_template_directory() . '/inc/customizer/functions/register-panel.php';
    require get_template_directory() . '/inc/customizer/functions/banner-options.php';
    require get_template_directory() . '/inc/customizer/functions/theme-options.php';
    require get_template_directory() . '/inc/customizer/functions/top-call-out.php';
    require get_template_directory() . '/inc/customizer/functions/footer-options.php';
    require get_template_directory() . '/inc/customizer/functions/additional-customizer.php';

}
add_action( 'customize_register', 'shopping_store_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function shopping_store_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function shopping_store_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function shopping_store_customize_preview_js() {
	wp_enqueue_script( 'shopping-store-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'shopping_store_customize_preview_js' );

function shopping_store_customize_style() {
    wp_enqueue_style( 'shopping-store-customizer-css', get_template_directory_uri() . '/inc/customizer/css/customizer-control.css', array(), '1.0.2' );
    wp_enqueue_style( 'shopping-store-sortable-css', get_template_directory_uri() . '/assets/sortable/customizer-control.css', array(), '1.0.2' );
}
add_action( 'customize_controls_enqueue_scripts', 'shopping_store_customize_style');