<?php
/**
 * Cost of Goods for WooCommerce - General Section Settings
 *
 * @version 1.1.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_General' ) ) :

class Alg_WC_Cost_of_Goods_Settings_General extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    [dev] Tools: better descriptions
	 */
	function get_settings() {

		$orders_columns_settings = array(
			array(
				'title'    => __( 'Admin Orders List Columns', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'This section lets you add custom columns to WooCommerce admin orders list.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_columns_options',
			),
			array(
				'title'    => __( 'Order profit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_columns_profit',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Order cost', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_columns_cost',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_orders_columns_options',
			),
		);

		$products_columns_settings = array(
			array(
				'title'    => __( 'Admin Products List Columns', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'This section lets you add custom columns to WooCommerce admin products list.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_options',
			),
			array(
				'title'    => __( 'Product profit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_profit',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Product cost', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_cost',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_products_columns_options',
			),
		);

		$tool_settings = array(
			array(
				'title'    => __( 'Import Costs Tool', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'Import tool is in %s.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'tools.php?page=import-costs' ) . '">' . __( 'Tools > Import Costs', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_cog_tool_options',
			),
			array(
				'title'    => __( 'Key to import from', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'text',
				'id'       => 'alg_wc_cog_tool_key',
				'default'  => '_wc_cog_cost',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_tool_options',
			),
			array(
				'title'    => __( 'Orders Tools', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_orders_tools_options',
			),
			array(
				'title'    => __( 'Recalculate orders cost and profit for all orders', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Recalculate', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Set items costs in all orders (overriding previous costs).', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Enable the checkbox and save changes to run the tool.', 'cost-of-goods-for-woocommerce' ) .
					apply_filters( 'alg_wc_cog_option', '<br>' . sprintf( 'You will need %s plugin to use this tool.',
						'<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' .
							'Cost of Goods for WooCommerce Pro' . '</a>' ), 'settings' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_all',
				'default'  => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_cog_option', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'title'    => __( 'Recalculate orders cost and profit for orders with no costs', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Recalculate', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Set items costs in orders that do not have costs set.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Enable the checkbox and save changes to run the tool.', 'cost-of-goods-for-woocommerce' ) .
					apply_filters( 'alg_wc_cog_option', '<br>' . sprintf( 'You will need %s plugin to use this tool.',
						'<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' .
							'Cost of Goods for WooCommerce Pro' . '</a>' ), 'settings' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_no_costs',
				'default'  => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_cog_option', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_orders_tools_options',
			),
		);

		return array_merge( $orders_columns_settings, $products_columns_settings, $tool_settings );
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_General();
