<?php
/**
 * Theme Customizer Functions
 *
 * @package Ram Nepal
 * @subpackage Shopping_Store_lite
 * @since Shopping_Store_lite 1.0
 */
$shopping_store_setting = shopping_store_get_theme_options();
/********************  Shopping Store THEME OPTIONS ******************************************/

/* Theme Color */
$wp_customize->add_setting(
    'shopping_store_theme_options[theme_color]',
    array(
        'type'              => 'option',
        'default'           => '#e23e57',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
$wp_customize->add_control(new WP_Customize_Color_Control ( $wp_customize, 'shopping_store_theme_options[theme_color]', array(
        'label'    => esc_html__( 'Theme Color', 'shopping-store-lite' ),
        'section'  => 'colors',
        'settings' => 'shopping_store_theme_options[theme_color]')
) );

/*
Product Cat
*/
$product_categories = get_terms('product_cat');
$count = count($product_categories);
if ($count > 0 && !is_wp_error($product_categories)) {
    $select_categories = array();
    foreach ($product_categories as $product_category) {
        $select_categories[$product_category->term_id] = $product_category->name;
    }
} else {
    $select_categories = array('' => esc_html__('Select', 'shopping-store-lite'));
}


$wp_customize->add_setting('shopping_store_theme_options[shopping_store_reset_all]',
    array(
        'default' => $shopping_store_setting['shopping_store_reset_all'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'shopping_store_reset_alls',
        'transport' => 'postMessage',
    ));
$wp_customize->add_control('shopping_store_theme_options[shopping_store_reset_all]',
    array(
        'priority' => 1,
        'label' => esc_html__('Reset all default settings. (Refresh it to view the effect)', 'shopping-store-lite'),
        'section' => 'shopping_store_custom_header',
        'type' => 'checkbox',
    ));


$wp_customize->add_setting('layout_picker', array(
        'default' => "3",
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
    )
);

$wp_customize->add_control(new shopping_store_Layout_Picker_Custom_Control($wp_customize, 'layout_picker', array(
            'label' => esc_html__('Layout picker', 'shopping-store-lite'),
            'section' => 'shopping_store_custom_header',
            'settings' => 'layout_picker',
            'priority' => 6,
        )
    )
);
$wp_customize->add_setting(
    'shopping_store_theme_options[product_grid_show]',
    array(
        'default' => $shopping_store_setting['product_grid_show'],
        'type' => 'option',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'shopping_store_checkbox_integer',
    )
);
$wp_customize->add_control(new Shopping_Store_checkbox_Customize_Controls(
        $wp_customize, 'shopping_store_theme_options[product_grid_show]',
        array(
            'label' => esc_html__('Show Product Listing Section In Homepage ? ', 'shopping-store-lite'),
            'section' => 'shopping_store_grid_listing',
            'settings' => 'shopping_store_theme_options[product_grid_show]',
            'priority' => 1,
        )
    )
);
$wp_customize->add_setting('shopping_store_theme_options[product_listing1_title]',
    array(
        'capability' => 'edit_theme_options',
        'default' => $shopping_store_setting['product_listing1_title'],
        'sanitize_callback' => 'sanitize_text_field',
        'type' => 'option',
    ));
$wp_customize->add_control('shopping_store_theme_options[product_listing1_title]',
    array(
        'label' => esc_html__('Section Title And Description', 'shopping-store-lite'),
        'priority' => 1,
        'section' => 'shopping_store_grid_listing',
        'type' => 'dropdown-pages',
    ));

$wp_customize->add_setting(
    'shopping_store_theme_options[product_listing1_woo]',
    array(
        'type'    => 'option',
        'sanitize_callback' => 'shopping_store_sanitize_select',
        'default' => $shopping_store_setting['product_listing1_woo'],

    )
);
$wp_customize->add_control(
    'shopping_store_theme_options[product_listing1_woo]',
    array(
        'label'   => esc_html__( 'Choose Product To Show', 'shopping-store-lite' ),
        'section' =>  'shopping_store_grid_listing',
        'type'    => 'select',
        'choices' =>  array(
            'feature'   => esc_html__('Feature Products','shopping-store-lite'),
            'sale'   => esc_html__('Sale Products','shopping-store-lite'),
            'total-sales'   => esc_html__('Maximum Sale Products','shopping-store-lite'),
            'new-product'   => esc_html__('New Products','shopping-store-lite'),
        ),
        'settings' => 'shopping_store_theme_options[product_listing1_woo]',
    )
);
$wp_customize->add_setting('shopping_store_theme_options[support_title]',
    array(
        'capability' => 'edit_theme_options',
        'default' => $shopping_store_setting['support_title'],
        'sanitize_callback' => 'sanitize_text_field',
        'type' => 'option',
    ));
$wp_customize->add_control('shopping_store_theme_options[support_title]',
    array(
        'label' => esc_html__('Support Title', 'shopping-store-lite'),
        'priority' => 1,
        'section' => 'support_options',
        'type' => 'text',
    ));
$wp_customize->add_setting('shopping_store_theme_options[support_phone]',
    array(
        'capability' => 'edit_theme_options',
        'default' => $shopping_store_setting['support_phone'],
        'sanitize_callback' => 'sanitize_text_field',
        'type' => 'option',
    ));
$wp_customize->add_control('shopping_store_theme_options[support_phone]',
    array(
        'label' => esc_html__('Support Phone', 'shopping-store-lite'),
        'priority' => 1,
        'section' => 'support_options',
        'type' => 'text',
    ));
$wp_customize->add_setting('shopping_store_theme_options[support_email]',
    array(
        'capability' => 'edit_theme_options',
        'default' => $shopping_store_setting['support_email'],
        'sanitize_callback' => 'sanitize_email',
        'type' => 'option',
    ));
$wp_customize->add_control('shopping_store_theme_options[support_email]',
    array(
        'label' => esc_html__('Support Email', 'shopping-store-lite'),
        'priority' => 1,
        'section' => 'support_options',
        'type' => 'text',
    ));
$wp_customize->add_setting('shopping_store_theme_options[support_icon]',
    array(
        'capability' => 'edit_theme_options',
        'default' => $shopping_store_setting['support_icon'],
        'sanitize_callback' => 'sanitize_text_field',
        'type' => 'option',
    ));


//Blog options
$wp_customize->add_setting(
    'shopping_store_theme_options[show_blog_section]',
    array(
        'default' => $shopping_store_setting['show_blog_section'],
        'type' => 'option',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'shopping_store_checkbox_integer',
    )
);

$wp_customize->add_control(new Shopping_Store_checkbox_Customize_Controls(
        $wp_customize, 'shopping_store_theme_options[show_blog_section]',
        array(
            'label' => esc_html__('Show Blog Section In Homepage? ', 'shopping-store-lite'),
            'section' => 'blog_options',
            'settings' => 'shopping_store_theme_options[show_blog_section]',
            'priority' => 1,
        )
    )
);
$wp_customize->add_setting('shopping_store_theme_options[blog_title]',
    array(
        'type' => 'option',
        'default' => $shopping_store_setting['blog_title'],
        'sanitize_callback' => 'shopping_store_sanitize_page',
    )
);
$wp_customize->add_control('shopping_store_theme_options[blog_title]',
    array(
        'label' => esc_html__('Title', 'shopping-store-lite'),
        'type' => 'dropdown-pages',
        'section' => 'blog_options',
        'settings' => 'shopping_store_theme_options[blog_title]',
    )
);

$wp_customize->add_setting('shopping_store_theme_options[meta_blog]',
    array(
        'default' => $shopping_store_setting['meta_blog'],
        'sanitize_callback' => 'shopping_store_sanitize_select',
        'type' => 'option',
    ));
$wp_customize->add_control('shopping_store_theme_options[meta_blog]',
    array(
        'priority' => 45,
        'label' => esc_html__('Meta for blog page', 'shopping-store-lite'),
        'section' => 'blog_options',
        'type' => 'select',
        'settings' => 'shopping_store_theme_options[meta_blog]',
        'choices' =>
            array(
                'show-meta' => esc_html__('Show Meta', 'shopping-store-lite'),
                'hide-meta' => esc_html__('Hide Meta', 'shopping-store-lite'),
            ),
    ));
?>