<?php

if (! function_exists('shopping_store_breadcrumb')) {
    function shopping_store_breadcrumb()
    {
        $header_image = get_header_image();
        if ((get_post_type() == 'portfolio') && !is_archive()) {
            $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
            $image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
            $header_image = $image[0];

        }
        ?>
        <div class="inner-banner-wrap"
             <?php if ($header_image) { ?>style="background-image:url(<?php echo esc_url($header_image); ?>)"<?php } ?>>
            <div class="container">
                <div class="row">
                    <div class="inner-banner-content">
                        <?php
                        if (is_archive()) {
                            the_archive_title('<h2>', '</h2>');
                        }
                        if (is_single()) {
                            the_title('<h2>', '</h2>');
                        }
                        if (is_search()) {
                            echo('<h2>' . esc_html__('Search Page', 'shopping-store-lite') . '</h2>');
                        }
                        ?>
                        <div class="header-breadcrumb">
                            <?php

                            if (is_page_template('page-templates/template-home.php')) {

                            } else {

                                $delimiter = '';
                                if (is_home())
                                    $home = esc_html__('Blog', 'shopping-store-lite'); // text for the 'Home' link
                                else
                                    $home = esc_html__('Home', 'shopping-store-lite'); // text for the 'Home' link

                                $before = '<li>'; // tag before the current crumb
                                $after = '</li>'; // tag after the current crumb
                                echo '<ul class="breadcrumb">';
                                global $post;
                                $homeLink = home_url();
                                echo '<li><a href="' . esc_url($homeLink) . '">' . esc_html($home) . '</a></li>' . wp_kses_post($delimiter) . ' ';
                                if (is_category()) {
                                    global $wp_query;
                                    $cat_obj = $wp_query->get_queried_object();
                                    $thisCat = $cat_obj->term_id;
                                    $thisCat = get_category($thisCat);
                                    $parentCat = get_category($thisCat->parent);
                                    if ($thisCat->parent != 0)
                                        echo(get_category_parents(esc_html($parentCat), TRUE, ' ' . wp_kses_post($delimiter) . ' '));
                                    echo wp_kses_post($before) . single_cat_title('', false) . wp_kses_post($after);
                                } elseif (is_day()) {
                                    echo '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_attr(get_the_time('Y')) . '</a></li> ' . wp_kses_post($delimiter) . ' ';
                                    echo '<li><a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . esc_attr(get_the_time('F')) . '</a></li> ' . wp_kses_post($delimiter) . ' ';
                                    echo wp_kses_post($before) . esc_attr(get_the_time('d')) . wp_kses_post($after);
                                } elseif (is_month()) {
                                    echo '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_attr(get_the_time('Y')) . '</a></li> ' . wp_kses_post($delimiter) . ' ';
                                    echo wp_kses_post($before) . esc_attr(get_the_time('F')) . wp_kses_post($after);
                                } elseif (is_year()) {
                                    echo wp_kses_post($before) . esc_attr(get_the_time('Y')) . wp_kses_post($after);
                                } elseif (is_single() && !is_attachment()) {
                                    if (get_post_type() != 'post') {
                                        $post_type = get_post_type_object(get_post_type());
                                        $slug = $post_type->rewrite;
                                        echo '<li><a href="' . esc_url($homeLink) . '/' . esc_attr($slug['slug']) . '/">' . esc_html($post_type->labels->singular_name) . '</a></li> ' . wp_kses_post($delimiter) . ' ';
                                        echo wp_kses_post($before) . esc_html(get_the_title()) . wp_kses_post($after);
                                    } else {
                                        $cat = get_the_category();
                                        $cat = $cat[0];
                                        //echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                                        echo wp_kses_post($before) . esc_html(get_the_title()) . wp_kses_post($after);
                                    }

                                } elseif (!is_single() && !is_page() && get_post_type() != 'post') {
                                    $post_type = get_post_type_object(get_post_type());
                                    if (!empty($post_type)) {
                                        echo wp_kses_post($before) . esc_html($post_type->labels->singular_name) . wp_kses_post($after);
                                    }
                                } elseif (is_attachment()) {
                                    $parent = get_post($post->post_parent);
                                    $cat = get_the_category($parent->ID);
                                    echo '<li><a href="' . esc_url(get_permalink($parent)) . '">' . esc_html($parent->post_title) . '</a></li> ' . wp_kses_post($delimiter) . ' ';
                                    echo wp_kses_post($before) . esc_attr(get_the_title()) . wp_kses_post($after);
                                } elseif (is_page() && !$post->post_parent) {
                                    echo wp_kses_post($before) . esc_attr(get_the_title()) . wp_kses_post($after);
                                } elseif (is_page() && $post->post_parent) {
                                    $parent_id = $post->post_parent;
                                    $breadcrumbs = array();
                                    while ($parent_id) {
                                        $page = get_page($parent_id);
                                        $breadcrumbs[] = '<li><a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html(get_the_title($page->ID)) . '</a></li>';
                                        $parent_id = $page->post_parent;
                                    }
                                    $breadcrumbs = array_reverse($breadcrumbs);
                                    foreach ($breadcrumbs as $crumb)
                                        echo wp_kses_post($crumb) . ' ' . wp_kses_post($delimiter) . ' ';
                                    echo wp_kses_post($before) . esc_html(get_the_title()) . wp_kses_post($after);
                                } elseif (is_search()) {
                                    echo wp_kses_post($before) . esc_html__("Search results for: ", "shopping-store-lite") . esc_html(get_search_query()) . '' . wp_kses_post($after);
                                } elseif (is_tag()) {
                                    echo wp_kses_post($before) . esc_html__('Tag', 'shopping-store-lite') . single_tag_title('', false) . wp_kses_post($after);
                                } elseif (is_author()) {
                                    global $author;
                                    $userdata = get_userdata($author);
                                    echo wp_kses_post($before) . esc_html__("Articles posted by", "shopping-store-lite") . ' ' . esc_html($userdata->display_name) . wp_kses_post($after);
                                } elseif (is_404()) {
                                    echo wp_kses_post($before) . esc_html__("Error 404", "shopping-store-lite") . wp_kses_post($after);
                                } elseif (is_page_template('page-templates/template-contact.php')) {
                                    echo wp_kses_post($before) . esc_attr(get_the_title()) . wp_kses_post($after);
                                }
                            }
                            echo '</ul>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}