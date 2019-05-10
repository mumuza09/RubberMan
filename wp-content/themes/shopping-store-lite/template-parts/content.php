<?php
/**
 * The template for displaying Stardard post formats
 *
 * @package Digi Store Pro
 */
?>
<?php
global $post;
$post_format = get_post_format($post->ID);
$_format = get_the_terms( $post->ID, 'post_format' );
$featured_image = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
$class = '';
if (is_sticky()) {
    // Sticky post content
    $class = 'sticky';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>
    <div class="post-content entry-content">

        <?php
            shopping_store_blog_post_format($post_format, $post->ID);
        ?>
        <header class="entry-header">
            <?php
            if ( is_singular() ) :
                the_title( '<h1 class="entry-title">', '</h1>' );
            else :
                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
            endif;
            if ('post' === get_post_type()) : ?>
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

            if(is_archive() || is_home()):

                echo wp_kses_post(shopping_store_strip_url_content($post->ID,400));
            else:
                the_content( sprintf(
                    wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                        __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'shopping-store-lite' ),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                ) );
            endif;
            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shopping-store-lite' ),
                'after'  => '</div>',
            ) );

            ?>
        </div><!-- .entry-content -->
        <?php
        if(is_archive() || is_home()):
            echo '<a class="read-more btn btn-default" href="'.esc_url(get_the_permalink()).'">'.esc_html__( 'Read More', 'shopping-store-lite' ).'</a> ';
        endif;
        ?>
    </div>
    <?php if(is_single()): ?>
        <footer class="entry-footer">
            <?php shopping_store_entry_footer(); ?>
        </footer><!-- .entry-footer -->
    <?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->