<?php
/**
 * Delete WooCommerce Unit Of Measure data if plugin is deleted.
 *
 * @since 1.0
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) :
	exit;
endif;
delete_post_meta_by_key( '_mcmp_ppu_general_override' );
delete_post_meta_by_key( '_mcmp_ppu_single_page_override' );
delete_post_meta_by_key( '_mcmp_ppu_recalc_text_override' );
delete_option('_mcmp_ppu_additional_text');
delete_option('_mcmp_ppu_hide_sale_price');
delete_option('_mcmp_ppu_var_prefix_text');
delete_option('_mcmp_ppu_var_hide_max_price');
delete_option('_mcmp_ppu_add_row_css');
delete_option('_mcmp_ppu_general');
delete_option('_mcmp_ppu_single_page');
delete_option('_mcmp_ppu_recalc_text');
