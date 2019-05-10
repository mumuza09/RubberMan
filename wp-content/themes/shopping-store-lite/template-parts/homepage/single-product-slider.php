<?php
$customizer_options = shopping_store_get_theme_options();
$show = $customizer_options['single_product_show'];
$woo_args = $customizer_options['single_product_woo'];
$layout = $customizer_options['single_product_layout'];
$args = shopping_store_get_woo_args($woo_args,'');
$product_query = new WP_Query($args);
if($show == 1) {
        ?>
        <div class="single-product section">
            <div class="container">
                <div class="row">
                    <div class="single-product-slider">

                        <?php
                        $loop = 1;
                        while ($product_query->have_posts()) : $product_query->the_post();
                            global $product;
                            $data = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()), 'shopping_store_woo_image');
                            $product_id = get_the_ID();
                            $price = get_post_meta($product_id, '_price', true);
                            $_product = wc_get_product($product_id);
                            $cart_url = get_option('woocommerce_cart_page_id');
                            if (in_array('yith-woocommerce-wishlist/init.php', apply_filters('active_plugins', get_option('active_plugins')))):
                                $wishid = yit_get_product_id($_product);
                                $default_wishlists = is_user_logged_in() ? YITH_WCWL()->get_wishlists(array('is_default' => true)) : false;
                                $exists = YITH_WCWL()->is_product_in_wishlist($wishid, false);
                            endif;
                            ?>

                            <div class="single-product-wrap">
                                <div class="col-md-4 col-md-offset-2">
                                    <?php echo wp_get_attachment_image(get_post_thumbnail_id(get_the_ID()),'shopping_store_woo_image'); ?>
                                </div>
                                <div class="col-md-5">
                                    <div class="single-product-desc">
                                        <h2>
                                            <a href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        <span><?php
                                            if ($price) {
                                                woocommerce_template_loop_price();
                                            } ?></span>
                                        <div class="product-description"><?php the_excerpt(); ?></div>
                                        <a href="<?php echo esc_url(get_the_permalink()); ?>"
                                           class="btn btn-default"><?php echo esc_html__('View Product', 'shopping-store-lite') ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata(); ?>

                    </div>
                </div>
            </div>
        </div>
        <?php
}
