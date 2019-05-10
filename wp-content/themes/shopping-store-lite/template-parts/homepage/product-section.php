<?php
$customizer_options = shopping_store_get_theme_options();
$show = $customizer_options['product_grid_show'];
$woo_args = $customizer_options['product_listing1_woo'];
if ($show == 1) {
    $args = shopping_store_get_woo_args($woo_args,'');
    $product_query = new WP_Query($args);
    $page = $customizer_options['product_listing1_title'];
    $exists = '';
    $getpage = '';
    if($page)
        $getpage = get_post($page);
    ?>
    <section class="section product-sec">

        <?php
        if($page) {
            ?>
            <div class="container">
                <div class="row">
                    <div class="section-title">
                        <?php
                            echo '<h2>' . esc_html($getpage->post_title) . '</h2>';
                            echo '<p>' . wp_kses_post(shopping_store_get_excerpt($getpage->ID,70)) . '</p>';
                        ?>
                    </div>
                </div>
            </div>

            <?php
        }
            ?>
        <div class="container">
            <div class="row">

                <?php
                $loop = 1;
                while ($product_query->have_posts()) : $product_query->the_post();
                    global $product;
                    $product_id = get_the_ID();
                    $price = get_post_meta($product_id, '_price', true);
                    $_product = wc_get_product($product_id);
                    $cart_url = get_option('woocommerce_cart_page_id');
                    ?>

                    <div class="col-md-3 col-sm-6">
                        <div class="product-wrap">
                            <div class="product-top">
                                <?php echo wp_get_attachment_image(get_post_thumbnail_id(get_the_ID()),'shopping_store_woo_image', "", array( "class" => "primary-image" )); ?>
                                <div class="product-icons">
                                    <a class="quick-view ion-ios-search modal-popup" data-toggle="tooltip" title="<?php echo esc_html__('Quick View','shopping-store-lite'); ?>" href="#" data-id="<?php echo esc_attr($product_id); ?>"></a>
                                    <?php
                                    if($cart_url):?>
                                    <a rel="nofollow" href="<?php echo esc_url(get_the_permalink($cart_url) . '?add-to-cart=' . $product_id); ?>" data-quantity="1" data-product_id="<?php echo esc_attr($product_id); ?>" data-product_sku="" class="product_type_simple ion-bag add-to-cart add_to_cart_button ajax_add_to_cart" title="<?php echo esc_html__('Add To Cart','shopping-store-lite'); ?>"><?php echo esc_html__('Add To Cart','shopping-store-lite'); ?></a>
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
                    if ($loop % 4 == 0) {
                        echo '</div>';
                        echo '<div class="row">';
                    }
                    $loop++;
                endwhile;
                wp_reset_postdata(); ?>
        </div>
    </section>
    <?php

} ?>