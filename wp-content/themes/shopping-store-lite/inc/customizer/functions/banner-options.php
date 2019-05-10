<?php
//Banner Section
$shopping_store_setting = shopping_store_get_theme_options();


$wp_customize->add_setting(
    'shopping_store_theme_options[banner_picker]',
    array(
        'type' => 'option',
        'sanitize_callback' => 'shopping_store_sanitize_page',
        'default' => $shopping_store_setting['banner_picker'],
    )
);

for ($i = 1; $i <= 2; $i++) {
    $wp_customize->add_setting('shopping_store_theme_options[shopping_store_page_slider_' . $i . ']',
        array(
            'default' => '',
            'sanitize_callback' => 'shopping_store_sanitize_page',
            'type' => 'option',
            'capability' => 'manage_options'
        ));
    $wp_customize->add_control('shopping_store_theme_options[shopping_store_page_slider_' . $i . ']',
        array(
            'priority' => 220 . $i,
            'label' => __(' Page Slider', 'shopping-store-lite') . ' ' . $i,
            'section' => 'banner_options',
            'type' => 'dropdown-pages',
        ));
}