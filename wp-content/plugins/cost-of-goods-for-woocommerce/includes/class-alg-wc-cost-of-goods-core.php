<?php
/**
 * Cost of Goods for WooCommerce - Core Class
 *
 * @version 1.1.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Core' ) ) :

class Alg_WC_Cost_of_Goods_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    [feature] (urgent) add order profit/cost meta box
	 * @todo    [feature] (urgent) add options to exclude fees, shipping etc. in order profit
	 * @todo    [feature] (urgent) add reports (e.g. `calculate_all_products_profit()` etc.)
	 * @todo    [feature] add products quick and bulk edit
	 * @todo    [feature] add export (CSV, XML)
	 * @todo    [feature] enable multicurrency
	 * @todo    [feature] add custom cost fields
	 * @todo    [feature] add custom info fields
	 * @todo    [feature] custom columns: add options to set columns titles
	 * @todo    [feature] (maybe) add product profit/cost meta box
	 * @todo    [feature] (maybe) add option to enter costs with taxes
	 * @todo    [feature] (maybe) add option to change meta keys prefix (i.e. `_alg_wc_cog`)
	 */
	function __construct() {
		if ( is_admin() ) {
			// Cost input on admin product page (simple product)
			add_action( 'woocommerce_product_options_pricing',                array( $this, 'add_cost_input' ) );
			add_action( 'save_post_product',                                  array( $this, 'save_cost_input' ), PHP_INT_MAX, 2 );
			// Cost input on admin product page (variable product)
			add_action( 'woocommerce_variation_options_pricing',              array( $this, 'add_cost_input_variation' ), 10, 3 );
			add_action( 'woocommerce_save_product_variation',                 array( $this, 'save_cost_input_variation' ), PHP_INT_MAX, 2 );
			add_action( 'woocommerce_product_options_general_product_data',   array( $this, 'add_cost_input_variable' ), PHP_INT_MAX );
			// Order item costs on order edit page
			add_action( 'woocommerce_before_order_itemmeta',                  array( $this, 'add_cost_input_shop_order' ), PHP_INT_MAX, 3 );
			add_action( 'save_post_shop_order',                               array( $this, 'save_cost_input_shop_order_save_post' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_hidden_order_itemmeta',                  array( $this, 'hide_cost_input_meta_shop_order' ), PHP_INT_MAX );
			// Recalculate orders cost and profit
			add_filter( 'alg_wc_cog_save_settings',                           array( $this, 'recalculate_orders_cost_and_profit' ), PHP_INT_MAX );
			// Admin columns
			require_once( 'class-alg-wc-cost-of-goods-admin-columns.php' );
			// Import tool
			require_once( 'class-alg-wc-cost-of-goods-import-tool.php' );
		}
		// Save order items costs on new order
		add_action( 'woocommerce_new_order',                                  array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
		add_action( 'woocommerce_api_create_order',                           array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
		add_action( 'woocommerce_cli_create_order',                           array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
		add_action( 'kco_before_confirm_order',                               array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
		add_action( 'woocommerce_checkout_order_processed',                   array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
	}

	/**
	 * recalculate_orders_cost_and_profit.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 * @todo    [feature] (urgent) add to bulk actions
	 */
	function recalculate_orders_cost_and_profit( $current_section ) {
		if (
			'yes' === get_option( 'alg_wc_cog_recalculate_orders_cost_and_profit_all',      'no' ) ||
			'yes' === get_option( 'alg_wc_cog_recalculate_orders_cost_and_profit_no_costs', 'no' )
		) {
			if ( '' === $current_section && current_user_can( 'manage_woocommerce' ) ) {
				add_action( 'admin_notices', array( $this, ( apply_filters( 'alg_wc_cog_option', false, 'recalculate_orders_cost_and_profit' ) ?
					'admin_notices_recalculate_orders_success' : 'admin_notices_error' ) ), PHP_INT_MAX );
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notices_error' ), PHP_INT_MAX );
			}
			update_option( 'alg_wc_cog_recalculate_orders_cost_and_profit_all',      'no' );
			update_option( 'alg_wc_cog_recalculate_orders_cost_and_profit_no_costs', 'no' );
		}
	}

	/**
	 * admin_notices_recalculate_orders_success.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function admin_notices_recalculate_orders_success() {
		echo '<div class="notice notice-success is-dismissible"><p><strong>' .
			__( 'Orders cost and profit successfully recalculated.', 'cost-of-goods-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * admin_notices_error.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function admin_notices_error() {
		echo '<div class="notice notice-error"><p><strong>' . __( 'Something went wrong...', 'cost-of-goods-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * hide_cost_input_meta_shop_order.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function hide_cost_input_meta_shop_order( $meta_keys ) {
		$meta_keys[] = '_alg_wc_cog_item_cost';
		return $meta_keys;
	}

	/**
	 * add_cost_input_shop_order.
	 *
	 * @version 1.1.1
	 * @since   1.1.0
	 */
	function add_cost_input_shop_order( $item_id, $item, $product ) {
		if ( 'WC_Order_Item_Product' === get_class( $item ) ) {
			$order = $item->get_order();
			echo '<p>' .
				'<label for="alg_wc_cog_item_cost_' . $item_id . '">' . __( 'Cost of goods', 'cost-of-goods-for-woocommerce' ) .
					' (' . get_woocommerce_currency_symbol( $order->get_currency() ) . ') ' . '</label>' .
				'<input name="alg_wc_cog_item_cost[' . $item_id . ']" id="alg_wc_cog_item_cost_' . $item_id . '" type="text" class="short wc_input_price" value="' .
					wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_cost' ). '">' .
			'</p>';
		}
	}

	/**
	 * update_order_items_costs.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 * @todo    [dev] recheck: `$do_update  = ( 0 != $cost );`
	 */
	function update_order_items_costs( $order_id, $is_new_order, $is_no_costs_only = false ) {
		$order      = wc_get_order( $order_id );
		$profit     = 0;
		$total_cost = 0;
		foreach ( $order->get_items() as $item_id => $item ) {
			if ( $is_new_order ) {
				if ( ! $is_no_costs_only || '' === wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_cost' ) ) {
					$product_id = ( isset( $item['variation_id'] ) && 0 != $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );
					$cost       = $this->get_product_cost( $product_id );
					$do_update  = ( 0 != $cost );
				} else {
					$do_update  = false;
				}
			} else {
				$cost       = ( isset( $_POST['alg_wc_cog_item_cost'][ $item_id ] ) ? $_POST['alg_wc_cog_item_cost'][ $item_id ] : false );
				$do_update  = ( isset( $_POST['alg_wc_cog_item_cost'][ $item_id ] ) );
			}
			if ( $do_update ) {
				wc_update_order_item_meta( $item_id, '_alg_wc_cog_item_cost', $cost );
			} else {
				$cost = wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_cost' );
			}
			if ( '' != $cost ) {
				$cost        = str_replace( ',', '.', $cost );
				$line_cost   = $cost * $item['qty'];
				$profit     += $item['line_total'] - $line_cost;
				$total_cost += $line_cost;
			}
		}
		update_post_meta( $order_id, '_alg_wc_cog_order_profit', $profit );
		update_post_meta( $order_id, '_alg_wc_cog_order_cost',   $total_cost );
	}

	/**
	 * save_cost_input_shop_order_save_post.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function save_cost_input_shop_order_save_post( $post_id, $__post ) {
		$this->update_order_items_costs( $post_id, false );
	}

	/**
	 * save_cost_input_shop_order_new.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function save_cost_input_shop_order_new( $post_id ) {
		$this->update_order_items_costs( $post_id, true );
	}

	/**
	 * get_product_cost.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function get_product_cost( $product_id ) {
		if ( '' === ( $cost = get_post_meta( $product_id, '_alg_wc_cog_cost', true ) ) ) {
			$product   = wc_get_product( $product_id );
			$parent_id = $product->get_parent_id();
			$cost      = get_post_meta( $parent_id, '_alg_wc_cog_cost', true );
		}
		return str_replace( ',', '.', $cost );
	}

	/**
	 * get_product_cost_html.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_product_cost_html( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product->is_type( 'variable' ) ) {
			return $this->get_variable_product_html( $product_id, 'cost' );
		} else {
			return ( '' === ( $cost = $this->get_product_cost( $product_id ) ) ? '' : wc_price( $cost ) );
		}
	}

	/**
	 * get_product_profit.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    [dev] (urgent) maybe check if `wc_get_price_excluding_tax()` is numeric (e.g. maybe can return range)
	 */
	function get_product_profit( $product_id ) {
		$product = wc_get_product( $product_id );
		return ( '' === ( $cost = $this->get_product_cost( $product_id ) ) || '' === ( $price = wc_get_price_excluding_tax( $product ) ) ? '' : $price - $cost );
	}

	/**
	 * get_product_profit_html.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    [feature] add option to enable/disable profit percent
	 */
	function get_product_profit_html( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product->is_type( 'variable' ) ) {
			return $this->get_variable_product_html( $product_id, 'profit' );
		} else {
			return ( '' === ( $profit = $this->get_product_profit( $product_id ) ) ? '' :
				wc_price( $profit ) . sprintf( ' (%0.2f%%)', ( 0 != ( $cost = $this->get_product_cost( $product_id ) ) ? $profit / $cost * 100 : '' ) ) );
		}
	}

	/**
	 * get_variable_product_html.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    [dev] (urgent) profit percent
	 */
	function get_variable_product_html( $product_id, $profit_or_cost ) {
		$product = wc_get_product( $product_id );
		$data    = array();
		foreach ( $product->get_children() as $variation_id ) {
			$data[ $variation_id ] = ( 'profit' === $profit_or_cost ? $this->get_product_profit( $variation_id ) : $this->get_product_cost( $variation_id ) );
		}
		if ( empty( $data ) ) {
			return '';
		} else {
			asort( $data );
			$min = current( $data );
			$max = end( $data );
			if ( $min !== $max ) {
				$html = wc_format_price_range( $min, $max );
			} else {
				$html = wc_price( $min );
			}
		}
		return $html;
	}

	/**
	 * add_cost_input.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    [dev] rethink `$product_id` (and search all code for `get_the_ID()`)
	 * @todo    [feature] (maybe) min_profit
	 */
	function add_cost_input() {
		$product_id = get_the_ID();
		woocommerce_wp_text_input( array(
			'id'          => '_alg_wc_cog_cost',
			'value'       => wc_format_localized_price( get_post_meta( $product_id, '_alg_wc_cog_cost', true ) ),
			'data_type'   => 'price',
			'label'       => __( 'Cost', 'cost-of-goods-for-woocommerce' ) .
				' (' . __( 'excl. tax', 'cost-of-goods-for-woocommerce' ) . ')' . ' (' . get_woocommerce_currency_symbol() . ')',
			'description' => sprintf( __( 'Profit: %s', 'cost-of-goods-for-woocommerce' ), $this->get_product_profit_html( $product_id ) ),
		) );
	}

	/**
	 * add_cost_input_variable.
	 *
	 * @version 1.0.1
	 * @since   1.0.0
	 */
	function add_cost_input_variable() {
		if ( ( $product = wc_get_product() ) && $product->is_type( 'variable' ) ) {
			echo '<div class="options_group show_if_variable">';
			$this->add_cost_input();
			echo '</div>';
		}
	}

	/**
	 * save_cost_input.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    [dev] (urgent) maybe pre-calculate and save `_alg_wc_cog_profit` (same in `save_cost_input_variation()`)
	 */
	function save_cost_input( $post_id, $__post ) {
		if ( isset( $_POST['_alg_wc_cog_cost'] ) ) {
			update_post_meta( $post_id, '_alg_wc_cog_cost', $_POST['_alg_wc_cog_cost'] );
		}
	}

	/**
	 * add_cost_input_variation.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function add_cost_input_variation( $loop, $variation_data, $variation ) {
		woocommerce_wp_text_input( array(
			'id'            => "variable_alg_wc_cog_cost_{$loop}",
			'name'          => "variable_alg_wc_cog_cost[{$loop}]",
			'value'         => wc_format_localized_price( isset( $variation_data['_alg_wc_cog_cost'][0] ) ? $variation_data['_alg_wc_cog_cost'][0] : '' ),
			'label'         => __( 'Cost', 'cost-of-goods-for-woocommerce' ) .
				' (' . __( 'excl. tax', 'cost-of-goods-for-woocommerce' ) . ')' . ' (' . get_woocommerce_currency_symbol() . ')',
			'data_type'     => 'price',
			'wrapper_class' => 'form-row form-row-full',
			'description'   => sprintf( __( 'Profit: %s', 'cost-of-goods-for-woocommerce' ), $this->get_product_profit_html( $variation->ID ) ),
		) );
	}

	/**
	 * save_cost_input_variation.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function save_cost_input_variation( $variation_id, $i ) {
		if ( isset( $_POST['variable_alg_wc_cog_cost'][ $i ] ) ) {
			update_post_meta( $variation_id, '_alg_wc_cog_cost', wc_clean( $_POST['variable_alg_wc_cog_cost'][ $i ] ) );
		}
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Core();
