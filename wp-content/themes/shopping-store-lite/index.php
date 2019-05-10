<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Shopping_Store_lite
 */

get_header();
$content_class = '';
$sidebar_class = shopping_store_check_sidebar();
if($sidebar_class == 'pull-left'):
    $content_class = 'pull-right';
elseif($sidebar_class == 'pull-right'):
    $content_class = 'pull-left';
else:
    $content_class = '';
endif;
?>
    <div class="section-content section">
        <div class="container">
            <div class="row">
                <?php if($sidebar_class != 'no-sidebar'):?>
                <div class="col-md-8 <?php echo esc_attr($content_class);?>">
                    <?php endif; ?>
                    <div id="primary" class="content-area">
                        <main id="main" class="site-main">

                            <?php
                            if (have_posts()) :

                                if (is_home() && !is_front_page()) : ?>
                                    <header>
                                        <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                                    </header>

                                    <?php
                                endif;

                                /* Start the Loop */
                                while (have_posts()) : the_post();

                                    /*
                                     * Include the Post-Format-specific template for the content.
                                     * If you want to override this in a child theme, then include a file
                                     * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                                     */
                                    get_template_part('template-parts/content', get_post_format());

                                endwhile;

                                the_posts_pagination( array(
                                    'mid_size' => 2,
                                    'prev_text' => __( '<i class="fa fa-angle-left"></i>', 'shopping-store-lite' ),
                                    'next_text' => __( '<i class="fa fa-angle-right"></i>', 'shopping-store-lite' ),
                                ) );

                            else :

                                get_template_part('template-parts/content', 'none');

                            endif; ?>

                        </main><!-- #main -->
                    </div><!-- #primary -->
                    <?php if($sidebar_class != 'no-sidebar'):?>
                </div>
            <?php endif; ?>
                <?php if($sidebar_class != 'no-sidebar'):?>
                    <div class="col-md-4 sidebar <?php echo esc_attr($sidebar_class);?>">

                        <div id="secondary">
                            <?php if (is_active_sidebar('sidebar-1')) {
                                dynamic_sidebar('sidebar-1');
                            } ?>
                        </div>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
get_footer();
