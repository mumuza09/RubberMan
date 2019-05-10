<?php

//Single product listing section

$wp_customize->add_setting(
    'shopping_store_theme_options[single_product_show]',
    array(
        'default' => $shopping_store_setting['single_product_show'],
        'type' => 'option',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'shopping_store_checkbox_integer',
    )
);
$wp_customize->add_control(new Shopping_Store_checkbox_Customize_Controls(
        $wp_customize, 'shopping_store_theme_options[single_product_show]',
        array(
            'label' => esc_html__('Show Product Listing Section In Homepage ? ', 'shopping-store-lite'),
            'section' => 'shopping_store_single_listing',
            'settings' => 'shopping_store_theme_options[single_product_show]',
            'priority' => 1,
        )
    )
);

$wp_customize->add_setting(
    'shopping_store_theme_options[single_product_woo]',
    array(
        'type'    => 'option',
        'sanitize_callback' => 'shopping_store_sanitize_select',
        'default' => $shopping_store_setting['single_product_woo'],

    )
);
$wp_customize->add_control(
    'shopping_store_theme_options[single_product_woo]',
    array(
        'label'   => esc_html__( 'Choose Product To Show', 'shopping-store-lite' ),
        'section' =>  'shopping_store_single_listing',
        'type'    => 'select',
        'choices' =>  array(
            'new-product'   => esc_html__('New Products','shopping-store-lite'),
            'sale'   => esc_html__('Sale Products','shopping-store-lite'),
            'feature'   => esc_html__('Feature Products','shopping-store-lite'),
            'total-sales'   => esc_html__('Maximum Sale Products','shopping-store-lite'),
        ),
        'settings' => 'shopping_store_theme_options[product_listing1_woo]',
    )
);



$wp_customize->add_setting(
    'shopping_store_theme_options[cta_section_show]',
    array(
        'default' => $shopping_store_setting['cta_section_show'],
        'type' => 'option',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'shopping_store_checkbox_integer',
    )
);
$wp_customize->add_control(new Shopping_Store_checkbox_Customize_Controls(
        $wp_customize, 'shopping_store_theme_options[cta_section_show]',
        array(
            'label' => esc_html__('Show CTA Section In Homepage ? ', 'shopping-store-lite'),
            'section' => 'shopping_store_cta_section',
            'settings' => 'shopping_store_theme_options[cta_section_show]',
            'priority' => 1,
        )
    )
);

$wp_customize->add_setting('shopping_store_theme_options[cta_title]',
    array(
        'capability' => 'edit_theme_options',
        'default' => $shopping_store_setting['cta_title'],
        'sanitize_callback' => 'esc_html',
        'type' => 'option',
    ));
$wp_customize->add_control('shopping_store_theme_options[cta_title]',
    array(
        'label' => esc_html__('Section Title', 'shopping-store-lite'),
        'priority' => 1,
        'section' => 'shopping_store_cta_section',
        'type' => 'text',
    ));

$wp_customize->add_setting('shopping_store_theme_options[cta_description]',
    array(
        'capability' => 'edit_theme_options',
        'default' => $shopping_store_setting['cta_description'],
        'sanitize_callback' => 'esc_html',
        'type' => 'option',
    ));
$wp_customize->add_control('shopping_store_theme_options[cta_description]',
    array(
        'label' => esc_html__('Section Description', 'shopping-store-lite'),
        'priority' => 1,
        'section' => 'shopping_store_cta_section',
        'type' => 'text',
    ));

$wp_customize->add_setting('shopping_store_theme_options[cta_bg_img]',
    array(
        'type' => 'option',
        'sanitize_callback' => 'esc_url_raw',
    )
);
$wp_customize->add_control(
    new WP_Customize_Image_Control(
        $wp_customize,
        'shopping_store_theme_options[cta_bg_img]',
        array(
            'label' => esc_html__('Add Image', 'shopping-store-lite'),
            'description' => esc_html__('Recommended Image Size 1000*400', 'shopping-store-lite'),
            'section' => 'shopping_store_cta_section',
            'settings' => 'shopping_store_theme_options[cta_bg_img]',
        ))
);
$wp_customize->add_setting('shopping_store_theme_options[cta_btn_title]',
    array(
        'capability' => 'edit_theme_options',
        'default' => $shopping_store_setting['cta_btn_title'],
        'sanitize_callback' => 'esc_html',
        'type' => 'option',
    ));
$wp_customize->add_control('shopping_store_theme_options[cta_btn_title]',
    array(
        'label' => esc_html__('Cta Button Title', 'shopping-store-lite'),
        'priority' => 1,
        'section' => 'shopping_store_cta_section',
        'type' => 'text',
    ));

$wp_customize->add_setting('shopping_store_theme_options[cta_btn_link]',
    array(
        'capability' => 'edit_theme_options',
        'default' => $shopping_store_setting['cta_btn_link'],
        'sanitize_callback' => 'esc_html',
        'type' => 'option',
    ));
$wp_customize->add_control('shopping_store_theme_options[cta_btn_link]',
    array(
        'label' => esc_html__('Cta Button Link', 'shopping-store-lite'),
        'priority' => 1,
        'section' => 'shopping_store_cta_section',
        'type' => 'text',
    ));

