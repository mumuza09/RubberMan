<?php
$shopping_store_setting = shopping_store_get_theme_options();

$wp_customize->add_setting(
    'shopping_store_theme_options[show_top_callout_section]',
    array(
        'default' => $shopping_store_setting['show_top_callout_section'],
        'type' => 'option',
        'capability' => 'edit_theme_options',
        'priority' => 1,
        'sanitize_callback' => 'shopping_store_checkbox_integer',
    )
);

$wp_customize->add_control(new Shopping_Store_checkbox_Customize_Controls($wp_customize, 'shopping_store_theme_options[show_top_callout_section]',
        array(
            'label' => esc_html__('Show Top Call Out in homepage ? ', 'shopping-store-lite'),
            'section' => 'top_call_options',
            'settings' => 'shopping_store_theme_options[show_top_callout_section]',
            'priority' => 2,
        )
    )
);


$wp_customize->add_setting('shopping_store_theme_options[shopping_store_callout_1]', array(
    'default' => $shopping_store_setting['shopping_store_callout_1'],
    'type' => 'option',
    'sanitize_callback' => 'sanitize_text_field',
    'capability' => 'edit_theme_options',

));

$wp_customize->add_control(new Shopping_Store_Top_Dropdown_Customize_Control(
    $wp_customize, 'shopping_store_theme_options[shopping_store_callout_1]',
    array(
        'label' => esc_html__('Choose Product Category First', 'shopping-store-lite'),
        'section' => 'top_call_options',
        'settings' => 'shopping_store_theme_options[shopping_store_callout_1]',
    )));

$wp_customize->add_setting('shopping_store_theme_options[shopping_store_callout_2]', array(
    'default' => $shopping_store_setting['shopping_store_callout_2'],
    'type' => 'option',
    'sanitize_callback' => 'sanitize_text_field',
    'capability' => 'edit_theme_options',

));

$wp_customize->add_control(new Shopping_Store_Top_Dropdown_Customize_Control(
    $wp_customize, 'shopping_store_theme_options[shopping_store_callout_2]',
    array(
        'label' => esc_html__('Choose Product Category Second', 'shopping-store-lite'),
        'section' => 'top_call_options',
        'type' => 'select',
        'settings' => 'shopping_store_theme_options[shopping_store_callout_2]',
    )));

$wp_customize->add_setting('shopping_store_theme_options[shopping_store_callout_3]',
    array(
        'default' => $shopping_store_setting['shopping_store_callout_3'],
        'type' => 'option',
        'sanitize_callback' => 'sanitize_text_field',
        'capability' => 'edit_theme_options',

    ));

$wp_customize->add_control(new Shopping_Store_Top_Dropdown_Customize_Control(
    $wp_customize, 'shopping_store_theme_options[shopping_store_callout_3]',
    array(
        'label' => esc_html__('Choose Product Category Third', 'shopping-store-lite'),
        'section' => 'top_call_options',
        'settings' => 'shopping_store_theme_options[shopping_store_callout_3]',
    )));
