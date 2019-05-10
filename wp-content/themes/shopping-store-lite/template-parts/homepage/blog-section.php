<?php
global $post;
global $wp_query;
$shopping_store_theme_options = shopping_store_get_theme_options();
$loop = 0;
$show_blog = $shopping_store_theme_options['show_blog_section'];
$blog_page = $shopping_store_theme_options['blog_title'];
$meta = $shopping_store_theme_options['meta_blog'];
$getpage = '';
if($blog_page)
    $getpage = get_post($blog_page);
$args = array(
    'post_type' => 'post',
    'orderby' => 'DATE',
    'order' => 'DESC',
    'posts_per_page' => 3,
);
$loop=1;
$featured = new WP_Query($args);
if($show_blog) {
    ?>
    <section id="blog" class="hp-section blog-sec">
        <?php if ($blog_page) { ?>
            <div class="section-title">
                <?php echo (esc_html($blog_page)) ? '<h2>' . esc_html($getpage->post_title) . '</h2>' : ''; ?>
                <?php echo (esc_html($blog_page)) ? '<p>' . wp_kses_post(shopping_store_get_excerpt($getpage->ID,70)) . '</p>' : ''; ?>
            </div>
        <?php } ?>
        <div class="container">
            <div class="row">
                <?php
                while ($featured->have_posts()) : $featured->the_post();
                    $post_format = get_post_format($post->ID);
                    $blog_post_author = get_avatar(get_the_author_meta('ID'), 32);
                    $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
                    $image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
                    $author_name = get_the_author_meta('display_name');
                    $category = get_the_category();
                    $id = get_the_ID();
                    if ($loop <= 3):
                            ?>
                            <div class="col-md-4">
                                <div class="post-module">
                                    <!-- post-Thumbnail-->
                                    <div class="post-thumbnail">
                                        <div class="date">
                                            <div class="day"><?php echo get_the_time('d') ?></div>
                                            <div class="month"><?php echo get_the_time('M') ?></div>
                                        </div>
                                        <?php shopping_store_blog_post_format($post_format, $post->ID); ?>
                                    </div>
                                    <!-- Post Content-->
                                    <div class="post-content">
                                        <?php echo '<h1 class="title"><a href="' . esc_url(get_the_permalink()) . '">' . get_the_title() . '</a></h1>' ?>
                                        <p class="description"><?php echo wp_kses_post(shopping_store_get_excerpt($id, 125)); ?></p>

                                        <?php if ($meta == 'show-meta'): ?>
                                            <div class="post-meta">
                                                <span class="timestamp"><i
                                                            class="fa fa-clock-o"></i>&nbsp;<?php printf(_x('%s ago', '%s = human-readable time difference', 'shopping-store-lite'), human_time_diff(get_the_time('U'), current_time('timestamp'))); ?></span>
                                                <span class="comments"><a
                                                            href="<?php echo esc_url(get_comments_link($post->ID));; ?>"><i
                                                                class="fa fa-comments"></i><?php echo esc_html(get_comments_number()); ?></a></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        $loop++;
                        endif;
                        endwhile; ?>
            </div>
        </div>
    </section>
    <?php
}
?>
