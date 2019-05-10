<?php
$shopping_store_setting = shopping_store_get_theme_options();

$wp_customize->add_setting(
    'shopping_store_theme_options[show_pre_footer_section]',
    array(
        'default' => $shopping_store_setting['show_pre_footer_section'],
        'type' => 'option',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'shopping_store_checkbox_integer',
    )
);

$wp_customize->add_control(new Shopping_Store_checkbox_Customize_Controls(
    $wp_customize, 'shopping_store_theme_options[show_pre_footer_section]',
        array(
            'label' => esc_html__('Show pre-footer section in homepage ? ', 'shopping-store-lite'),
            'section' => 'footer_options',
            'settings' => 'shopping_store_theme_options[show_pre_footer_section]',
            'priority' => 1,
        )
    )
);