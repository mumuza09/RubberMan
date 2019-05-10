<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Hasten
 */

global $post;
$post_format = get_post_format($post->ID);
$class = '';
if (is_sticky()) {
    // Sticky post content
    $class = 'sticky';
}
?>

    <article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>

        <?php
        if ($post_format != 'video')
            shopping_store_blog_post_format($post_format, $post->ID);
        ?>
        <div class="post-bg">
            <header class="entry-header">
                <?php
                if (is_singular()) :
                    the_title('<h1 class="entry-title">', '</h1>');
                else :
                    the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                endif;


                if ('post' === get_post_type()) :
                    ?>
                    <div class="metabar-wrap">
                        <span class="byline"><i class="fa fa-user" aria-hidden="true"></i> <span class="author vcard"><a
                                        href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo esc_html(get_the_author()); ?></a></span></span>
                        <span class="article-full-date"><i class="fa fa-calendar" aria-hidden="true"></i><a
                                    href="<?php echo esc_url(shopping_store_archive_link($post)); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
                        <span class="post-like-count"><i class="fa fa-comments" aria-hidden="true"></i><a
                                    href="<?php echo esc_url(get_comments_link()); ?>"><?php echo esc_html(get_comments_number()) . esc_html__(' Comments', 'shopping-store-lite'); ?></a></span>
                    </div>
                    <?php
                endif; ?>

            </header><!-- .entry-header -->

            <div class="entry-content">
                <?php

                if (is_archive() || is_home()):

                    echo wp_kses_post(shopping_store_strip_url_content($post->ID,400));

                else:
                    the_content(sprintf(
                        wp_kses(
                        /* translators: %s: Name of current post. Only visible to screen readers */
                            __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'shopping-store-lite'),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        get_the_title()
                    ));
                endif;

                ?>
            </div><!-- .entry-content -->
            <footer class="entry-footer">
                <?php
                shopping_store_entry_footer();
                ?>
            </footer><!-- .entry-footer -->
        </div>
    </article><!-- #post-<?php the_ID(); ?> -->
<?php
    shopping_store_single_navigation($post->ID);
?>