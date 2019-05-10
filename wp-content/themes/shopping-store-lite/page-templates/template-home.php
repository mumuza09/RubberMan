<?php
/**
 *
 * Template Name: Frontpage
 * Description: A page template that displays the Homepage or a Front page as in theme main page with slider and some other contents of the
 * post.
 *
 * @package Shopping_Store_lite
 */

get_header();
$shopping_store_settings = shopping_store_get_theme_options();
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
    shopping_store_product_categories();
    get_template_part('template-parts/homepage/product','section');
    get_template_part('template-parts/homepage/cta','section');
    get_template_part('template-parts/homepage/single-product','slider');

}
get_template_part('template-parts/homepage/blog','section');
get_footer();
