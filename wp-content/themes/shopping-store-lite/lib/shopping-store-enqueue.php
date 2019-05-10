<?php

/**
 * Enqueue scripts and styles.
 */
if ( ! function_exists( 'shopping_store_fonts_url' ) ) :
    function shopping_store_fonts_url() {
        $fonts_url = '';
        $fonts     = array();

        if ( 'off' !== _x( 'on', 'Playfair Display font: on or off', 'shopping-store-lite' ) ) {
            $fonts[] = 'Playfair Display:400,700';
        }

        if ( 'off' !== _x( 'on', 'Poppins font: on or off', 'shopping-store-lite' ) ) {
            $fonts[] = 'Poppins:400,500,600,700';
        }

        if ( $fonts ) {
            $fonts_url = add_query_arg( array(
                'family' => urlencode( implode( '|', $fonts ) ),
            ), '//fonts.googleapis.com/css' );
        }

        return $fonts_url;
    }
endif;

function shopping_store_scripts() {
    $customizer_data = shopping_store_get_theme_options();
    $color = $customizer_data['theme_color'];

    $custom_css = "


    ";

    wp_enqueue_style( 'shopping-store-lite-fonts', shopping_store_fonts_url() , array(), null);

    wp_enqueue_style( 'shopping-store-lite-style', get_stylesheet_uri() );

    wp_enqueue_style( 'shopping-store-lite-custom-style',  get_template_directory_uri() . '/assets/css/shopping-store.css', array(), '20151225', null);

    wp_enqueue_script( 'shopping-store-lite-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

    wp_enqueue_script( 'shopping-store-lite-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );
    wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), '20151215', true );
    wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/assets/js/slick.min.js', array('jquery'), '20151215', true );
    wp_enqueue_script( 'dynamic-min-js', get_template_directory_uri() . '/assets/js/dynamics.min.js', array('jquery'), '20151215', true );
    wp_enqueue_script('imagesloaded');
    wp_enqueue_script( 'sticky-header-js', get_template_directory_uri() . '/assets/js/sticky-header.js', array('jquery'), '20151215', true );
    wp_enqueue_script( 'maicha-app', get_template_directory_uri() . '/assets/js/maicha-app.js', array('jquery'), '20151220', true );
    wp_enqueue_script( 'shopping-store-app', get_template_directory_uri() . '/assets/js/app.js', array('jquery'), '20151220', true );
    wp_localize_script( 'shopping-store-app', 'woocommerce_product',array('ajaxurl' => admin_url( 'admin-ajax.php' ),));

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
    wp_add_inline_style( 'shopping-store-lite-custom-style', $custom_css );

}
add_action( 'wp_enqueue_scripts', 'shopping_store_scripts' );
?>
