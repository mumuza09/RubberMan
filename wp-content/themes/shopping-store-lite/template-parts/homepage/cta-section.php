<?php
/**
 * Created by PhpStorm.
 * User: ram
 * Date: 06/10/2018
 * Time: 10:18
 */
$customizer_options = shopping_store_get_theme_options();
$cta_show = $customizer_options['cta_section_show'];
$cta_description = $customizer_options['cta_description'];
$cta_bg_img = $customizer_options['cta_bg_img'];
$cta_title = $customizer_options['cta_title'];
$cta_btn_title = $customizer_options['cta_btn_title'];
$cta_btn_link = $customizer_options['cta_btn_link'];
if($cta_show && ($cta_title || $cta_description|| $cta_bg_img)) {
    ?>
    <section data-aos="fade" class="section cta-sec  parallax bg-cover"
             style="background-image: url(<?php echo esc_url($cta_bg_img); ?>);">
        <div class="container">
            <div class="row">
                <div class="cta-content">
                    <?php
                    if($cta_title)
                        echo '<h2 class="cta-title">'.esc_html($cta_title).'</h2>';
                    if($cta_description)
                        echo '<p>'.esc_html($cta_description).'</p>';
                    if($cta_btn_link && $cta_btn_title)
                        echo '<a href="'.esc_url($cta_btn_link).'" class="btn btn-default">'.$cta_btn_title.'</a>';
                    ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}