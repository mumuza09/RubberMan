<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Shopping_Store_lite
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class(''); ?>>
<!-- Header -->
<?php
$shopping_store_settings = shopping_store_get_theme_options();
$support_icon = $shopping_store_settings['support_icon'];
$support_email = $shopping_store_settings['support_email'];
$support_phone = $shopping_store_settings['support_phone'];
$support_title = $shopping_store_settings['support_title'];
?>


<div id="mySidenav" class="sidenav">
    <a class="closebtn"><i class="fa fa-times"
                           aria-hidden="true"><span><?php echo esc_html__('close', 'shopping-store-lite') ?></span></i></a>
    <?php
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) { ?>
        <div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
    <?php }
    ?>

</div>
<div id="main">
</div>

<header id="top" class="header hero">
    <div class="header-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-left">
                    <?php
                    if($support_phone || $support_title){
                        echo '<div class="content-left topbar-content">';
                        echo '<i class="ion-android-call"></i>'.esc_html($support_title).'<a href="tel:'.esc_attr($support_phone).'">'.esc_html($support_phone).'</a>';
                        echo '</div>';
                    }

                    if ($support_email) {
                        echo '<div class="content-left topbar-content">';
                        echo '<i class="ion-ios-email-outline"></i><a href="mailto:'.sanitize_email($support_email).'">'.esc_html($support_email).'</a>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="col-md-6 text-right">
                    <div class="content-left topbar-content">
                        <i class="ion-android-contact"></i> <?php shopping_store_welcome_menu() ?>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="header-mid">
        <!-- Start of Naviation -->
        <div class="nav-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <div class="logo">
                                <?php
                                if (function_exists('has_custom_logo') && has_custom_logo()) {
                                    the_custom_logo();
                                }
                                ?>
                                <div class="site-desc site-brand text-center">
                                    <h3 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h3>
                                    <?php
                                    $description = get_bloginfo('description', 'display');
                                    if ($description || is_customize_preview()) : ?>
                                        <p class="site-description"><?php echo esc_html($description); ?></p>
                                        <?php
                                    endif; ?>
                                </div>
                            </div>
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                                <span class="sr-only">Toggle navigation<?php echo esc_html__('Toggle navigation','shopping-store-lite'); ?></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>


                        <?php
                        if(has_nav_menu('primary') || is_user_logged_in()){
                        ?>
                        <nav id="primary-nav" class="navbar navbar-default">
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <div class="collapse navbar-collapse" id="navbar-collapse">
                                <?php
                                $menu = '';
                                $args = array(
                                    'theme_location' => 'primary',
                                    'container' => '',
                                    'menu_class' => 'nav navbar-nav navbar-right',
                                    'walker' => new shopping_store_nav_walker(),
                                    'fallback_cb' => 'shopping_store_nav_walker::fallback',
                                );
                                wp_nav_menu($args);
                                ?>
                            </div><!-- End navbar-collapse -->
                        </nav>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php
    if (is_page_template('page-templates/template-home.php')) {

        $banner_choice = sanitize_text_field($shopping_store_settings['banner_picker']);
                    echo wp_kses_post(shopping_store_slider_default_query());
    }
    ?>
</header>
<?php
if (!is_page_template('page-templates/template-home.php')): ?>
    <?php shopping_store_breadcrumb(); ?>
<?php endif; ?>
