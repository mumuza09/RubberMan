<?php
if(!function_exists('shopping_store_get_option_defaults_values')):
	/******************** SHOPPING STORE LITE DEFAULT OPTION VALUES ******************************************/
	function shopping_store_get_option_defaults_values() {
		global $shopping_store_default_values;
        $shopping_store_default_values = array(
			'shopping_store_reset_all' 				=> 0,
			'theme_color'	                        => '#e23e57',
			'slider_layout' 	=>'layout-1',


            //product categories
            'product_cat' => 'none',
            'product_cat_1' => 'none',
            'product_cat_2' => 'none',

            //Clients Section
            'banner_picker' => 'banner-image',
            'slider_posts' => 4,
            'slider_image_title' => '',
            'slider_image_description' => '',
            'upload_banner_image' => '',
            'single_btn1' => '',
            'single_link1' => '',

            //Product Listing Section
            'product_grid_show' => 0,
            'product_listing1_title' => '',
            'product_listing1_image' => '',
            'product_listing1_woo' => 'new-product',

            'cta_section_show' => 0,
            'cta_description' => '',
            'cta_bg_img' => '',
            'cta_title' => '',
            'cta_btn_title' => '',
            'cta_btn_link' => '',


            /*Additional Options*/
            'show_top_callout_section' 	=> 0,

            //single product options
            'single_product_show'				=> 0,
            'single_product_layout'			=> 'layout-1',
            'single_product_woo'			=> 'new-product',

            /*Support Option*/
            'support_title'                     =>'',
            'support_phone'                     =>'',
            'support_email'                     =>'',
            'support_icon'                     =>'fa-life-buoy',

            //footer options
			'show_pre_footer_section'				=> 0,
			'footer_text'							=> esc_html__('Copyright','shopping-store-lite'),
			'developed_by_text'						=> esc_html__('Pride Themes','shopping-store-lite'),
			'developed_by_link'						=> esc_url('https://pridethemes.com'),

            //contact section
            'show_blog_section'=>0,
            'blog_title' => '',
            'meta_blog' => 'show-meta',

            //pages
            'shopping_store_callout_1' => '',
            'shopping_store_callout_2' => '',
            'shopping_store_callout_3' => '',
			);
		return apply_filters( 'shopping_store_get_option_defaults_values', $shopping_store_default_values );
	}
endif;
?>