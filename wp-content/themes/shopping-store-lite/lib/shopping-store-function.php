<?php
if (!function_exists('shopping_store_welcome_menu')) {
    function shopping_store_welcome_menu()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) :

            global $woocommerce;
            $menu_item = '';

            if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
                return '';
            ?><?php
            if (!is_user_logged_in()) { ?>
                <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>">
                    <?php esc_html_e('Login', 'shopping-store-lite'); ?>
                </a>
                <?php esc_html_e('or', 'shopping-store-lite'); ?>
            <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>">
                <?php esc_html_e('Register', 'shopping-store-lite'); ?>
                </a><?php } else { ?>
            <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>">
                <?php esc_html_e('My Account', 'shopping-store-lite'); ?></a><?php }
//                        }
        endif;
    }
}

if (!function_exists('shopping_store_blog_post_format')) {
    function shopping_store_blog_post_format($post_format, $post_id)
    {

        global $post;

        if ($post_format == 'video') {
            $content = trim(get_post_field('post_content', $post->ID));
            $ori_url = explode("\n", esc_html($content));
            $url = $ori_url[0];
            $url_type = explode(" ", $url);
            $url_type = explode("[", $url_type[0]);

            if (isset($url_type[1])) {
                $url_type_shortcode = $url_type[1];
            }
            $new_content = get_shortcode_regex();
            if (isset($url_type[1])) {
                if (preg_match_all('/' . $new_content . '/s', $post->post_content, $matches)
                    && array_key_exists(2, $matches)
                    && in_array($url_type_shortcode, $matches[2])
                ) {
                    echo do_shortcode($matches[0][0]);
                }
            } else {
                echo wp_oembed_get(shopping_store_the_featured_video($content));
            }
        } elseif ($post_format == 'audio') {
            $html = "";
            $content = trim(get_post_field('post_content', $post_id));
            $ori_url = explode("\n", esc_html($content));
            $new_content = preg_match_all('/\[[^\]]*\]/', $content, $matches);
            if ($new_content) {
                echo do_shortcode($matches[0][0]);
            } elseif (preg_match('#^<(script|iframe|embed|object)#i', $content)) {
                $regex = '/https?\:\/\/[^\" ]+/i';
                preg_match_all($regex, $ori_url[0], $matches);
                $urls = ($matches[0]);
                $html .= ('<iframe src="' . $urls[0] . '" width="100%" height="240" frameborder="no" scrolling="no"></iframe>');
            } elseif (0 === strpos($content, 'https://')) {
                $embed_url = wp_oembed_get(esc_url($ori_url[0]));
                $html .= ($embed_url);
            }
            echo esc_html($html);
        } elseif ($post_format == 'gallery') {
            $image_url = get_post_gallery_images($post_id);
            $post_thumbnail_id = get_post_thumbnail_id($post_id);
            $attachment = get_post($post_thumbnail_id);
            if ($image_url) {
                ?>

                <div class="post-gallery">

                    <div class="post-format-gallery">
                        <?php foreach ($image_url as $key => $images) { ?>
                            <div class="slider-item" style="background-image: url('<?php echo esc_url($images); ?>');"
                                 alt="<?php echo esc_attr($attachment->post_excerpt); ?>">
                            </div>
                        <?php } ?>
                    </div>

                </div>
            <?php } else {
                if (has_post_thumbnail() && is_single()) {
                    the_post_thumbnail();
                }

            }


        } else {
            if (has_post_thumbnail() && is_single() && !is_page_template('page-templates/template-home.php')) {
                echo '<div class="image">';
                echo '<a  href="' . esc_url(get_the_permalink()) . '">';
                the_post_thumbnail();
                echo '</a></div>';
            } else {
                the_post_thumbnail();
            }

        }

    }

}

if (!function_exists('shopping_store_the_featured_video')) {
    function shopping_store_the_featured_video($content)
    {
        $ori_url = explode("\n", $content);
        $url = $ori_url[0];
        $w = get_option('embed_size_w');
        if (!is_single()) {
            $url = str_replace('448', $w, $url);
            return $url;
        }

        if (0 === strpos($url, 'https://') || 0 == strpos($url, 'http://')) {
            echo esc_url(wp_oembed_get($url));
            $content = trim(str_replace($url, '', $content));
        } elseif (preg_match('#^<(script|iframe|embed|object)#i', $url)) {
            $h = get_option('embed_size_h');
            echo esc_url($url);
            if (!empty($h)) {

                if ($w === $h) $h = ceil($w * 0.75);
                $url = preg_replace(
                    array('#height="[0-9]+?"#i', '#height=[0-9]+?#i'),
                    array(sprintf('height="%d"', $h), sprintf('height=%d', $h)),
                    $url
                );
                echo esc_url($url);
            }

            $content = trim(str_replace($url, '', $content));

        }
    }
}

if (!function_exists('shopping_store_check_sidebar')) {
    function shopping_store_check_sidebar()
    {

        $check_sidebar = wp_get_sidebars_widgets();
        $sidebar_layout = get_theme_mod('layout_picker');

        if ($sidebar_layout == 1) {
            $sidebar_class = 'no-sidebar';
        } else if ($sidebar_layout == 2 && !empty($check_sidebar['sidebar-1'])) {
            $sidebar_class = 'pull-left';
        } else if ($sidebar_layout == 3 && !empty($check_sidebar['sidebar-1'])) {
            $sidebar_class = 'pull-right';
        } else {
            $sidebar_class = 'no-selection';
        }
        return $sidebar_class;
    }
}
if (!function_exists('shopping_store_strip_url_content')) {

    function shopping_store_strip_url_content($post_id, $count)
    {
        $content_post = get_post($post_id);
        $excerpt = $content_post->post_content;

        $excerpt = strip_shortcodes($excerpt);
        $excerpt = strip_tags($excerpt);


        $excerpt = preg_replace('/\s\s+/', ' ', $excerpt);
        $excerpt = preg_replace('#\[[^\]]+\]#', ' ', $excerpt);
        $strip = explode(' ', $excerpt);
        foreach ($strip as $key => $single) {
            if (!filter_var($single, FILTER_VALIDATE_URL) === false) {
                unset($strip[$key]);
            }
        }
        $excerpt = implode(' ', $strip);

        $excerpt = substr($excerpt, 0, $count);
        if (strlen($excerpt) >= $count) {
            $excerpt = substr($excerpt, 0, strripos($excerpt, ' '));
            $excerpt = $excerpt . '...';
        }
        return $excerpt;
    }
}

add_action('wp_ajax_mini_cart', 'shopping_store_woo_mini_cart');
add_action('wp_ajax_nopriv_mini_cart', 'shopping_store_woo_mini_cart');

if (!function_exists('shopping_store_woo_mini_cart')) {
    function shopping_store_woo_mini_cart()
    {
        global $woocommerce;
        $cart_contents_count = $woocommerce->cart->cart_contents_count;
        echo esc_html($cart_contents_count);
        die();
    }
}

add_action('wp_ajax_product_action', 'shopping_store_modal_footer');
add_action('wp_ajax_nopriv_product_action', 'shopping_store_modal_footer');

if (!function_exists('shopping_store_modal_footer')) {
    function shopping_store_modal_footer($data)
    {
        $get_id = $_POST['post_id'];
        if($get_id)
        echo do_shortcode('[product_page id="' . esc_attr($get_id) . '"]');
        die();
    }
}

if (!function_exists('shopping_store_banner_callback_choice')):
    function shopping_store_banner_callback_choice($control)
    {
        $banner_setting = $control->manager->get_setting('shopping_store_theme_options[banner_picker]')->value();

        $control_id = $control->id;

        if (($control_id == 'shopping_store_theme_options[slider_layout]' || $control_id == 'shopping_store_theme_options[slider_posts]') && $banner_setting == 'banner-slider') {
            return true;
        }

        if (($control_id == 'shopping_store_theme_options[single_link1]' || $control_id = 'shopping_store_theme_options[slider_layout]' || $control_id == 'shopping_store_theme_options[single_link2]' || $control_id == 'shopping_store_theme_options[single_btn1]' || $control_id == 'shopping_store_theme_options[single_btn2]' || $control_id == 'shopping_store_theme_options[slider_image_title]' || $control_id == 'shopping_store_theme_options[slider_image_description]' || $control_id == 'shopping_store_theme_options[upload_banner_image]') && $banner_setting == 'banner-image') {
            return true;
        }
        return false;
    }
endif;

if (!function_exists('shopping_store_slider_default_query')) {
    function shopping_store_slider_default_query()
    {
        global $post;
        $shopping_store_settings = shopping_store_get_theme_options();
        $shopping_store_total_page_no = 0;
        $shopping_store_list_page = array();
        for ($i = 1; $i <= 2; $i++) {
            if (isset ($shopping_store_settings['shopping_store_page_slider_' . $i]) && $shopping_store_settings['shopping_store_page_slider_' . $i] > 0) {
                $shopping_store_total_page_no++;
                $shopping_store_list_page = array_merge($shopping_store_list_page, array(esc_attr($shopping_store_settings['shopping_store_page_slider_' . $i])));
            }
        }
        if (!empty($shopping_store_list_page) && $shopping_store_total_page_no > 0) {
            $slider_query = new WP_Query(array('posts_per_page' => 2, 'post_type' => array('page'), 'post__in' => $shopping_store_list_page, 'orderby' => 'post__in',));
            $i = 0;
        if ($slider_query->have_posts()) {
                ?>
                <div class="banner-wrapper">
                    <div class="row">
                        <div class="Modern-Slider">
                            <?php
                            while ($slider_query->have_posts()) : $slider_query->the_post();
                                $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
                                $image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
                                $btnlink = get_post_meta(get_the_ID(), 'code_themes_essentials_slider_link', true);
                                $btntxt = get_post_meta(get_the_ID(), 'code_themes_essentials_slider_btntxt', true);
                                if (!empty($image)) {
                                    $image_style = "style='background-image:url(" . esc_url($image[0]) . ")'";
                                } else {
                                    $image_style = "";
                                }
                                ?>
                                <div class="item">
                                    <div class="img-fill">
                                        <img src="<?php echo esc_url($image[0]); ?>" alt="">
                                        <div class="info">
                                            <div>
                                                <h3><?php the_title() ?></h3>
                                                <h5>
                                                    <?php echo wp_kses_post(shopping_store_get_excerpt(get_the_ID(), 125)) ?>
                                                </h5>
                                                <a href="<?php echo esc_url(get_the_permalink()); ?>" class="btn btn-default"><?php echo esc_html__('Read More','shopping-store-lite') ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile;
                            wp_reset_postdata(); ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        
        }

    }
}


if (!function_exists('shopping_store_single_image_banner')) {

    function shopping_store_single_image_banner()
    {
        $customizer_options = shopping_store_get_theme_options();
        $image = $customizer_options['upload_banner_image'];
        $title = $customizer_options['slider_image_title'];
        $description = $customizer_options['slider_image_description'];
        $btntxt = $customizer_options['single_btn1'];
        $btnlink = $customizer_options['single_link1'];
        if ($title || $description || $btnlink || $btntxt) {
            ?>
            <div class="banner-wrapper">
                <div class="row">
                    <div class="Modern-Slider">

                        <div class="item">
                            <div class="img-fill">
                                <img src="<?php echo esc_url($image); ?>" alt="">
                                <div class="info">
                                    <div>
                                        <h3><?php echo esc_html($title); ?></h3>
                                        <h5>
                                            <?php echo esc_html($description); ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
    }
}

if (!function_exists('shopping_store_wp_body_classes')) {

    function shopping_store_wp_body_classes($classes)
    {
        $classes[] =' woocommerce';
        return $classes;
    }
}
add_filter( 'body_class','shopping_store_wp_body_classes' );

if ( ! function_exists( 'shopping_store_blog_post_format' ) ) {
    function shopping_store_blog_post_format( $post_format, $post_id) {
        global $post;

        if ($post_format == 'video') {

            $content = trim(get_post_field('post_content', $post->ID));
            $ori_url = explode("\n", esc_html($content));
            $url = $ori_url[0];
            $url_type = explode(" ", $url);
            $url_type = explode("[", $url_type[0]);

            if (isset($url_type[1])) {
                $url_type_shortcode = $url_type[1];
            }
            $new_content = get_shortcode_regex();
            if (isset($url_type[1])) {
                if (preg_match_all('/' . $new_content . '/s', $post->post_content, $matches)
                    && array_key_exists(2, $matches)
                    && in_array($url_type_shortcode, $matches[2])
                ) {
                    echo do_shortcode($matches[0][0]);
                }
            } else {
                echo wp_oembed_get(shopping_store_the_featured_video($content));
            }

        } elseif ($post_format == 'gallery') {
            add_filter( 'shortcode_atts_gallery', 'shopping_store_shortcode_atts_gallery' );
            $image_url = get_post_gallery_images($post_id);
            $post_thumbnail_id = get_post_thumbnail_id($post_id);
            $attachment = get_post($post_thumbnail_id);
            if ($image_url) {
                ?>

                <div class="post-gallery">

                    <div class="post-format-gallery">
                        <?php foreach ($image_url as $key => $images) { ?>
                            <div class="slider-item" style="background-image: url('<?php echo esc_url($images); ?>');">
                            </div>
                        <?php } ?>
                    </div>

                </div>
            <?php } else {
                if (has_post_thumbnail() && !is_single() && is_page_template('page-templates/template-home.php')) {
                    echo '<div class="featured-image archive-thumb">';
                    echo '<a  href="' . esc_url(get_the_permalink()) . '" class="post-thumbnail">';
                    the_post_thumbnail();
                    echo '<div class="share-mask"><div class="share-wrap"></div></div></a></div>';
                } else {
                    the_post_thumbnail();
                }

            }


        } else {
            if (has_post_thumbnail() && !is_single() && is_page_template('page-templates/template-home.php')) {
                echo '<div class="featured-image archive-thumb">';
                echo '<a  href="' . esc_url(get_the_permalink()) . '" class="post-thumbnail">';

                the_post_thumbnail();
                echo '<div class="share-mask"><div class="share-wrap"></div></div></a></div>';
            } else {
                the_post_thumbnail();
            }

        }


    }
}

if (!function_exists('shopping_store_get_excerpt')) :
    function shopping_store_get_excerpt($post_id, $count)
    {
        $content_post = get_post($post_id);
        $excerpt = $content_post->post_content;

        $excerpt = strip_shortcodes($excerpt);
        $excerpt = strip_tags($excerpt);


        $excerpt = preg_replace('/\s\s+/', ' ', $excerpt);
        $excerpt = preg_replace('#\[[^\]]+\]#', ' ', $excerpt);
        $strip = explode(' ', $excerpt);
        foreach ($strip as $key => $single) {
            if (!filter_var($single, FILTER_VALIDATE_URL) === false) {
                unset($strip[$key]);
            }
        }
        $excerpt = implode(' ', $strip);

        $excerpt = substr($excerpt, 0, $count);
        if (strlen($excerpt) >= $count) {
            $excerpt = substr($excerpt, 0, strripos($excerpt, ' '));
            $excerpt = $excerpt . '...';
        }
        return $excerpt;
    }
endif;

if (!function_exists('shopping_store_get_woo_args')) {

    function shopping_store_get_woo_args($data, $cat)
    {
        $args = '';
        if ($data == 'sale') {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 12,
                'meta_query' => array(
                    'relation' => 'OR',
                    array( // Simple products type
                        'key' => '_sale_price',
                        'value' => 0,
                        'compare' => '>',
                        'type' => 'numeric'
                    ),
                    array( // Variable products type
                        'key' => '_min_variation_sale_price',
                        'value' => 0,
                        'compare' => '>',
                        'type' => 'numeric'
                    )
                )
            );
        } elseif ($data == 'feature') {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 12,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_visibility',
                        'field' => 'name',
                        'terms' => 'featured',
                    ),
                ),
            );
        } elseif ($data == 'total-sales') {
            $args = array(
                'post_type' => 'product',
                'meta_key' => 'total_sales',
                'orderby' => 'meta_value_num',
                'posts_per_page' => 12,
            );

        } else {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 12
            );
        }
        if ($cat) {
            $args['product_cat'] = $cat;
        }
        return $args;
    }
}

if (!function_exists('shopping_store_archive_product')) {

    function shopping_store_archive_product($count = '')
    {
        global $product;
        $data = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
        $product_id = get_the_ID();
        $price = get_post_meta($product_id, '_price', true);
        $_product = wc_get_product($product_id);
        $cart_url = get_option('woocommerce_cart_page_id');

        ?>
        <div class="product-slider">
            <div class="product-wrap">
                <div class="product-top">
                    <?php echo wp_get_attachment_image(get_post_thumbnail_id(get_the_ID()),'shopping_store_woo_image', "", array( "class" => "primary-image" )); ?>
                    <div class="product-icons">
                        <a class="quick-view ion-ios-search modal-popup" data-toggle="tooltip" title="<?php echo esc_attr__('Quick View','shopping-store-lite'); ?>" href="#" data-id="<?php echo esc_attr($product_id); ?>"></a>
                        <?php
                        if($cart_url):?>
                            <a rel="nofollow" href="<?php echo esc_url(get_the_permalink($cart_url) . '?add-to-cart=' . $product_id); ?>" data-quantity="1" data-product_id="<?php echo esc_attr($product_id); ?>" data-product_sku="" class="product_type_simple ion-bag add-to-cart add_to_cart_button ajax_add_to_cart" title="<?php echo esc_attr__('Add To Cart','shopping-store-lite'); ?>"><?php echo esc_attr__('Add To Cart','shopping-store-lite'); ?></a>
                        <?php endif;?>
                    </div>
                </div>
                <div class="product-footer">
                    <div class="product-desc">
                        <h3><a href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title();?></a></h3>
                        <?php
                        $average = $_product->get_average_rating();
                        $average = round($average);
                        echo '<span class="rating">';
                        for($i=0;$i<$average;$i++){
                            echo '<i class="fa fa-star"></i>';
                        }
                        echo '</span>';
                        ?>
                        <div class="price">
                                            <span><?php
                                                if ($price) {
                                                    woocommerce_template_loop_price();
                                                } ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php

        if ($count % 3 == 0) {
            echo '</div>';
            echo '<div class="row">';
        }

    }
}


if (!function_exists('shopping_store_lite_headsearch')) {

    add_filter('wp_nav_menu_items', 'shopping_store_lite_headsearch', 10, 2);

    /** Add searchbar in header. */
    function shopping_store_lite_headsearch($items, $args)
    {
        if ($args->theme_location == 'primary') {
            return $items .= "<li class=\"slide-nav\"><a href=\"#\"><i class=\"ion-bag\" aria-hidden=\"true\"></i></a></li>";
        }
        return $items;
    }
}


add_action('wp_footer', 'shopping_store_modal_enquiry');

if (!function_exists('shopping_store_modal_enquiry')) {

    function shopping_store_modal_enquiry()
    {
        ?>
        <div class="product-quick-view">
            <div class="modal-frame">
                <div class="modal-wrap">
                    <div class="vertical-align-center">
                        <div class="modal-body">
                            <div class="modal-inner">
                                <div class="loading"></div>
                                <button id="close" class="close"><i class="fa fa-times"></i></button>
                                <div class="modal-content">
                                </div>
                            </div>
                        </div>
                        <div class="modal-overlay"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php }
}


add_action('woocommerce_after_add_to_cart_button', 'shopping_store_ajax_button');
if (!function_exists('shopping_store_ajax_button')) {

    function shopping_store_ajax_button($id)
    {
        $id = get_the_ID();
        echo '<a rel="nofollow" href="' . esc_url(site_url('/cart') . '?add-to-cart=' . esc_attr($id)) . '" data-quantity="1" data-product_id="' . esc_attr($id) . '" data-product_sku="" class="button product_type_simple add-to-cart add_to_cart_button ajax_add_to_cart" title="'.esc_attr__('Add to Cart','shopping-store-lite').'">' . esc_attr__('Add To Cart', 'shopping-store-lite') . '</a>';
    }
}


//product category
if (!function_exists('shopping_store_product_categories')) {
    function shopping_store_product_categories($args = array())
    {
        $class = '';
        $product_cat = shopping_store_get_theme_options();
        if($product_cat['show_top_callout_section'] == 1) {

            $html = "";
            $html .= '<div class="banner-section section">';
            $html .= '<div class="container">';
            $html .= '<div class="row">';
            if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                $topcategory = $cat1 = $cat2 =$cat3 = '';

                if ($product_cat['shopping_store_callout_1'])
                    $cat1 = get_term_by('slug', $product_cat['shopping_store_callout_1'], 'product_cat');
                if ($product_cat['shopping_store_callout_2'])
                    $cat2 = get_term_by('slug', $product_cat['shopping_store_callout_2'], 'product_cat');
                if ($product_cat['shopping_store_callout_3'])
                    $cat3 = get_term_by('slug', $product_cat['shopping_store_callout_3'], 'product_cat');

                $terms = array($cat1,$cat2,$cat3);
                $terms = array_filter($terms);
                if (count($terms)>0) {
                    foreach ($terms as $term) {
                        $featured_image_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                        $featured_image_url = wp_get_attachment_image_src($featured_image_id, 'full');
                        if (!empty($featured_image_url[0])) {
                            $background_style = "style='background-image:url(" . esc_url($featured_image_url[0]) . ")'";
                        } else {
                            $background_style = "";
                        }
                        $html .= '<div class="col-md-4 col-sm-12">';
                        $html .= '<div class="banner-wrap banner-layout2" ' . wp_kses_post($background_style) . '>';
                        $html .= '<div class="text-wrap">';
                        $html .= '<h3>' . esc_html($term->name) . '</h3>';
                        $html .= '<span>' . esc_html(wp_trim_words($term->description,6)) . '</span>';
                        $html .= '<a href="' . esc_url(get_term_link($term)) . '" class="btn btn-default">' . esc_attr__('Shop Now', 'shopping-store-lite') . '</a>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            echo($html);
        }
    }

}

if (!function_exists('shopping_store_single_navigation')) {
    function shopping_store_single_navigation($post_id)
    {
        global $post;
        ?>
        <div class="bgwhite post-pad">
            <nav class="navigation post-navigation" role="navigation">
                <h2 class="screen-reader-text"><?php echo esc_html__('Post navigation', 'shopping-store-lite'); ?></h2>

                <div class="nav-links">
                    <?php $next_post = get_adjacent_post(false, '', false);
                    if (!empty($next_post)) :
                        ?>
                        <div class="nav-previous">
                            <div class="post-nav-content">
                                <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" rel="prev"><span><i
                                                class="fa fa-long-arrow-left"
                                                aria-hidden="true"></i><?php echo esc_html__('&nbsp;Prev Post', 'shopping-store-lite'); ?></span>
                                    <br><h4><?php echo esc_html(get_the_title($next_post->ID)); ?></h4></a></div>
                        </div>
                    <?php endif; ?>

                    <?php $prev_post = get_adjacent_post(false, '', true);
                    if (!empty($prev_post)) :
                        $post_thumbnail_id = get_post_thumbnail_id($prev_post->ID);
                        $attachment = get_post($post_thumbnail_id);
                        $image_url = wp_get_attachment_image_src($post_thumbnail_id, 'small');
                        $image_url1 = $image_url[0];
                        ?>
                        <div class="nav-next">
                            <?php if ($image_url1) : ?>
                                <div class="nav-post-thumb"><img src="<?php echo esc_url($image_url1); ?>"></div>
                            <?php else: ?>
                                <div class="nav-post-thumb"></div>
                            <?php endif; ?>
                            <div class="post-nav-content">
                                <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>"
                                   rel="next"><span><?php echo esc_html__('Next Post&nbsp;', 'shopping-store-lite'); ?><i
                                                class="fa fa-long-arrow-right" aria-hidden="true"></i></span><br>
                                    <h4><?php echo esc_html(get_the_title($prev_post->ID)); ?></h4></a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <?php
    }
}

if (!function_exists('shopping_store_archive_link')) {
    function shopping_store_archive_link($post)
    {
        $year = date('Y', strtotime($post->post_date));
        $month = date('m', strtotime($post->post_date));
        $day = date('d', strtotime($post->post_date));
        $link = site_url('') . '/' . $year . '/' . $month . '?day=' . $day;
        return $link;
    }
}