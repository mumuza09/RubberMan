<?php
/**
 * Theme Customizer Functions
 *
 * @package Pride Themes
 * @subpackage Shopping Store
 * @since Shopping Store 1.0
 */
/******************** Shopping Store SLIDER SETTINGS ******************************************/
$shopping_store_setting = shopping_store_get_theme_options();

$wp_customize->add_section('shopping_store_page_post_options',
    array(
        'title' => esc_html__('Slider Option', 'shopping-store-lite'),
        'priority' => 1,
        'panel' => 'shopping_store_options_panel'
    ));
for ($i = 1; $i <= 2; $i++) {
    $wp_customize->add_setting('shopping_store_get_theme_options[shopping_store_featured_page_slider_' . $i . ']',
        array(
            'default' => '',
            'sanitize_callback' => 'shopping_store_sanitize_page',
            'type' => 'option',
            'capability' => 'manage_options'
        ));
    $wp_customize->add_control('shopping_store_theme_options[shopping_store_featured_page_slider_' . $i . ']',
        array(
            'priority' => 220 . $i,
            'label' => esc_html__(' Page Slider', 'shopping-store-lite') . ' ' . $i,
            'section' => 'shopping_store_page_post_options',
            'type' => 'dropdown-pages',
        ));
}