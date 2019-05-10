<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Shopping_Store_lite
 */
$shopping_store_settings = shopping_store_get_theme_options();
$widgets = $shopping_store_settings['show_pre_footer_section'];
$count = 1;
 ?>

<?php
if($widgets==1) {
    if ($widgets==1 || is_active_sidebar('shopping_store_footer_1') || is_active_sidebar('shopping_store_footer_2') || is_active_sidebar('shopping_store_footer_3') || is_active_sidebar('shopping_store_footer_4')):
        $col2 = 3;
        ?>
        <footer>
            <div class="prefooter">
                <div class="container">
                    <div class="row">
                        <?php
                        if (is_active_sidebar('shopping_store_footer_1')) :
                            echo '<div class="col-md-' . esc_attr($col2) . '">';
                            dynamic_sidebar('shopping_store_footer_1');
                            echo '</div>';
                        endif; ?>

                        <?php
                        if (is_active_sidebar('shopping_store_footer_2')) :
                            echo '<div class="col-md-' . esc_attr($col2) . ' col-sm-6">';
                            dynamic_sidebar('shopping_store_footer_2');
                            echo '</div>';
                        endif; ?>

                        <?php
                        if (is_active_sidebar('shopping_store_footer_3')) :
                            echo '<div class="col-md-' . esc_attr($col2) . ' col-sm-6">';
                            dynamic_sidebar('shopping_store_footer_3');
                            echo '</div>';
                        endif; ?>
                        <?php
                        if (is_active_sidebar('shopping_store_footer_4')) :
                            echo '<div class="col-md-' . esc_attr($col2) . ' col-sm-6">';
                            dynamic_sidebar('shopping_store_footer_4');
                            echo '</div>';
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </footer>
        <?php
    endif;
}
$footer_text = $shopping_store_settings['footer_text'];
$developed_by_text = $shopping_store_settings['developed_by_text'];
$developed_by_link = $shopping_store_settings['developed_by_link'];

if ($footer_text || $developed_by_text || $developed_by_link || (has_nav_menu('footer'))) {
    ?>
    <div class="botfooter">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="copyright">
                        <p><?php echo esc_html__('Powered By WordPress', 'shopping-store-lite');
                            echo esc_html__(' | ', 'shopping-store-lite') ?>
                            <a target="_blank" rel="nofollow"
                               href="<?php echo esc_url('https://www.pridethemes.com/product/shopping-store-lite/'); ?>"><?php echo esc_attr__('Shopping Store Lite', 'shopping-store-lite'); ?></a>
                        </p>
                    </div>
                </div>
                <?php if (has_nav_menu('footer')) { ?>
                    <?php
                    $args = array(
                        'theme_location' => 'footer',
                        'container' => 'nav',
                        'depth' => 1,
                        'menu_class' => 'list-inline',
                        'fallback_cb' => false,
                    );
                    wp_nav_menu($args);
                    ?>
                <?php } ?>

            </div>
        </div>
    </div>
<?php }
wp_footer(); ?>

</body>
</html>
