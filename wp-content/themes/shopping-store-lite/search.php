<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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
elseif($sidebar_class == 'no-sidebar'):
    $content_class = 'no-sidebar';
endif;
?>
    <div class="section-content section">
        <div class="container">
            <div class="row">
                <?php if($sidebar_class != 'no-sidebar'):?>
                <div class="col-md-8 <?php echo esc_attr($content_class);?>">
                    <?php endif; ?>
                <section id="primary" class="content-area">
                    <main id="main" class="site-main">

                        <?php
                        if (have_posts()) : ?>

                            <header class="page-header">
                                <h1 class="page-title"><?php
                                    /* translators: %s: search query. */
                                    printf(esc_html__('Search Results for: %s', 'shopping-store-lite'), '<span>' . get_search_query() . '</span>');
                                    ?></h1>
                            </header><!-- .page-header -->

                            <?php
                            /* Start the Loop */
                            while (have_posts()) : the_post();

                                /**
                                 * Run the loop for the search to output the results.
                                 * If you want to overload this in a child theme then include a file
                                 * called content-search.php and that will be used instead.
                                 */
                                get_template_part('template-parts/content', 'search');

                            endwhile;

                            the_posts_navigation();

                        else :

                            get_template_part('template-parts/content', 'none');

                        endif; ?>

                    </main><!-- #main -->
                </section><!-- #primary -->
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
