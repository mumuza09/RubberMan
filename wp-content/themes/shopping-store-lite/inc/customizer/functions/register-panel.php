<?php
/**
 * Theme Customizer Functions
 *
 * @package Pride Themes
 * @subpackage Shopping Store
 * @since Shopping Store 1.0
 */
/******************** Shopping Store CUSTOMIZE REGISTER *********************************************/
add_action('customize_register', 'shopping_store_customize_register_options', 20);
function shopping_store_customize_register_options($wp_customize)
{
    $wp_customize->add_panel('shopping_store_options_panel',
        array(
            'priority' => 2,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => esc_html__('Theme Options', 'shopping-store-lite'),
            'description' => '',
        ));
}

add_action('customize_register', 'shopping_store_customize_register_featuredcontent');
function shopping_store_customize_register_featuredcontent($wp_customize)
{
    $wp_customize->add_panel('shopping_store_featuredcontent_panel',
        array(
            'priority' => 31,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => esc_html__('Slider Options', 'shopping-store-lite'),
            'description' => '',
        ));
}

add_action('customize_register', 'shopping_store_customize_register_widgets');
function shopping_store_customize_register_widgets($wp_customize)
{
    $wp_customize->add_panel('widgets',
        array(
            'priority' => 33,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => esc_html__('Widgets', 'shopping-store-lite'),
            'description' => '',
        ));
}


add_action('customize_register', 'shopping_store_customize_contact');
function shopping_store_customize_contact($wp_customize)
{
    $wp_customize->add_panel('shopping_store_contact_option',
        array(
            'priority' => 32,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => esc_html__('Contact Us Page Options', 'shopping-store-lite'),
            'description' => '',
        ));
}

?>