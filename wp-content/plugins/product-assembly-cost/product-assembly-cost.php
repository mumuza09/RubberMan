<?php
/**
 * Plugin Name: Product Assembly Cost for WooCommerce
 * Plugin URI: https://www.webdados.pt/produtos-e-servicos/internet/desenvolvimento-wordpress/product-assembly-cost-for-woocommerce/
 * Description: Add an option to your WooCommerce products to enable assembly service and optionally charge a fee.
 * Version: 0.4.1
 * Author: Webdados
 * Author URI: https://www.webdados.pt
 * Text Domain: product-assembly-cost
 * Domain Path: /languages/
 * WC tested up to: 3.5.1
 * 
 * 	License: GNU General Public License v3.0
 * 	License URI: http://www.gnu.org/licenses/gpl-3.0.html
**/

/* WooCommerce CRUD ready */


/*
	TO-DO:
	- Ecrã de opções independente do Geral do WooCommerce
	- Possibilidade de criar vários "serviços" e definir TODOS os settings por serviço
	- Na edição do produto, nova tab de serviços adicionais e possibilidade de activar um a um e marcar o preço
	- Deve ser possível comprar com vários serviços ou apenas um (definição global ou por produto??)
		- O que acontece se colocarmos um mesmo produto com opções diferentes?
*/


/**
 * WC_Product_Extra_Service_Assembly class. - Fork of https://wordpress.org/plugins/woocommerce-product-gift-wrap/
 */
class WC_Product_Extra_Service_Assembly {

	public function __construct() {
		
		$default_message                  = '{checkbox} '. sprintf( __( 'Ask for assembly of this item for %s?', 'product-assembly-cost' ), '{price}' );
		$this->product_assembly_enabled   = get_option( 'product_assembly_enabled' ) == 'yes' ? true : false;
		$this->product_assembly_cost      = get_option( 'product_assembly_cost', 0 );
		$this->product_assembly_message   = get_option( 'product_assembly_message' );
		$this->product_assembly_cost_mode = get_option( 'product_assembly_cost_mode' ) == 'yes' ? 'subtotal' : 'product';

		$this->fee_name = '';

		if ( ! $this->product_assembly_message ) {
			$this->product_assembly_message = $default_message;
		}

		add_option( 'product_assembly_enabled', 'no' );
		add_option( 'product_assembly_cost', '0' );
		add_option( 'product_assembly_message', $default_message );
		add_option( 'product_assembly_cost_mode', 'no' );

		// Init settings
		$this->settings = array(
			array(
				'title' => __( 'Product assembly', 'woocommerce' ),
				'type' 	=> 'title',
				'desc' 	=> '',
				'id' 	=> 'product_assembly_cost_title',
			),
			array(
				'title' 	=> __( 'Assembly enabled by default?', 'product-assembly-cost' ),
				'desc' 		=> __( 'Enable this to allow assembly for products by default', 'product-assembly-cost' ),
				'id' 		=> 'product_assembly_enabled',
				'type' 		=> 'checkbox',
			),
			array(
				'title' 	=> __( 'Default assembly cost', 'product-assembly-cost' ),
				'desc' 		=> __( 'The cost of assembly, per product, unless overridden at the product level', 'product-assembly-cost' ),
				'id' 		=> 'product_assembly_cost',
				'type' 		=> 'number',
				'desc_tip'  => true,
				'custom_attributes' => array(
					'step' => 0.01,
				),
			),
			array(
				'title' 	=> __( 'Assembly message', 'product-assembly-cost' ),
				'id' 		=> 'product_assembly_message',
				'desc' 		=> '<br/>'.__( 'Note: <code>{checkbox}</code> will be replaced with a checkbox and <code>{price}</code> will be replaced with the assembly cost', 'product-assembly-cost' ),
				'type' 		=> 'text',
				'desc_tip'  => __( 'The checkbox and label shown to the user on the frontend.', 'product-assembly-cost' )
			),
			array(
				'title' 	=> __( 'Show cost as a global fee?', 'product-assembly-cost' ),
				'desc' 		=> __( 'Enable this to show the assembly as a global fee on the cart subtotals instead of adding the cost to the product itself', 'product-assembly-cost' ),
				'id' 		=> 'product_assembly_cost_mode',
				'type' 		=> 'checkbox',
			),
			array(
				'type' 	=> 'sectionend',
				'id' 	=> 'product_assembly_cost_title',
			),
		);

		//Localisation
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Display on the front end
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'assembly_option_html' ), 10 );

		// Filters for cart actions
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 ); //With price on the product itself
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta' ), 10, 2 );
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'assembly_fee' ) );

		// Admin
		add_filter( 'woocommerce_product_settings', array( $this, 'woocommerce_product_settings' ) );

		// Edit product options
		add_action( 'woocommerce_product_options_pricing', array( $this, 'woocommerce_product_options_pricing' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'woocommerce_process_product_meta' ) );

	}

	/* Localisation */
	public function load_textdomain() {
		load_plugin_textdomain( 'product-assembly-cost' );
	}

	/* Admin - settings */
	public function woocommerce_product_settings( $settings ) {
		foreach ( $this->settings as $setting ) {
			$settings[] = $setting;
		}
		return $settings;
	}

	/* Admin - Show fields on the product edit screen */
	public function woocommerce_product_options_pricing() {
		global $post;

		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) $product = wc_get_product( $post->ID );

		$has_assembly = trim( version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_meta( '_has_assembly' ) : get_post_meta( $post->ID, '_has_assembly', true ) );

		if ( $has_assembly == '' && $this->product_assembly_enabled ) {
			$has_assembly = 'yes';
		}

		echo '</div><div class="options_group show_if_simple show_if_variable">';

		woocommerce_wp_checkbox( array(
			'id'            => '_has_assembly',
			'wrapper_class' => '',
			'value'         => $has_assembly,
			'label'         => __( 'Assembly available', 'product-assembly-cost' ),
			'description'   => __( 'Enable this option if the customer can choose to buy the assembly service', 'product-assembly-cost' ),
		) );

		woocommerce_wp_text_input( array(
			'id'          => '_assembly_cost',
			'label'       => __( 'Assembly cost', 'product-assembly-cost' ),
			'placeholder' => $this->product_assembly_cost,
			'desc_tip'    => true,
			'description' => __( 'Override the default assembly cost by inputting a value here', 'product-assembly-cost' ),
		) );

		wc_enqueue_js( "
			jQuery( 'input#_has_assembly' ).change( function() {
				jQuery( '._assembly_cost_field' ).hide();
				if ( jQuery( '#_has_assembly' ).is( ':checked' ) ) {
					jQuery( '._assembly_cost_field' ).show();
				}
			} ).change();
		" );

	}
	/* Admin - Save fields */
	public function wc_clean( $value ) {
		return version_compare( WC_VERSION, '3.0', '>=' ) ? wc_clean( $value ) : woocommerce_clean( $value );
	}
	public function woocommerce_process_product_meta( $post_id ) {
		$meta = array();
		$meta['_has_assembly'] = ! empty( $_POST['_has_assembly'] ) ? 'yes' : 'no';
		$meta['_assembly_cost'] = ! empty( $_POST['_assembly_cost'] ) ? $this->wc_clean( $_POST['_assembly_cost'] ) : '';
		//Update meta
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			//CRUD
			$product = wc_get_product( $post_id );
			foreach ( $meta as $key => $value ) {
				$product->update_meta_data( $key, $value );
			}
			$product->save();
		} else {
			//Old WooCommerce
			foreach ( $meta as $key => $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}

	/* Frontend - option on the product */
	public function assembly_option_html() {
		global $post;

		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) $product = wc_get_product( $post->ID );

		$has_assembly = trim( version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_meta( '_has_assembly' ) : get_post_meta( $post->ID, '_has_assembly', true ) );

		if ( $has_assembly == '' && $this->product_assembly_enabled ) { //Default
			$has_assembly = 'yes';
		}

		if ( $has_assembly == 'yes' ) {

			$current_value = ! empty( $_REQUEST['assembly'] ) ? 1 : 0;

			$cost = floatval( version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_meta( '_assembly_cost' ) : get_post_meta( $post->ID, '_assembly_cost', true ) );

			if ( $cost == 0 ) { //Default
				$cost = $this->product_assembly_cost;
			}

			$price_text = $cost > 0 ? wc_price( $cost ) : __( 'free', 'product-assembly-cost' );
			$checkbox   = '<input type="checkbox" name="assembly" value="yes" ' . checked( $current_value, 1, false ) . ' />';

			?>
			<p class="product-assembly" style="clear:both; padding-top: .5em;">
				<label><?php echo str_replace( array( '{checkbox}', '{price}' ), array( $checkbox, $price_text ), wp_kses_post( $this->product_assembly_message ) ); ?></label>
			</p>
			<?php

		}
	}

	/* Frontend - When added to cart, save assembly data */
	public function add_cart_item_data( $cart_item_meta, $product_id ) {
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) $product = wc_get_product( $product_id );
		$has_assembly = trim( version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_meta( '_has_assembly' ) : get_post_meta( $post->ID, '_has_assembly', true ) );
		if ( $has_assembly == '' && $this->product_assembly_enabled ) { //Default
			$has_assembly = 'yes';
		}
		if ( ! empty( $_POST['assembly'] ) && $has_assembly == 'yes' ) {
			$cart_item_meta['assembly'] = true;
		}
		return $cart_item_meta;
	}

	/* Get assembly cost for cart item */
	public function get_assembly_for_cart_item( $cart_item ) {
		$product_type = version_compare( WC_VERSION, '3.0', '>=' ) ? $cart_item['data']->get_type() : $cart_item['data']->product_type;
		switch( $product_type ) {
			case 'variation':
				$product_id = version_compare( WC_VERSION, '3.0', '>=' ) ? $cart_item['data']->get_parent_id() : $cart_item['data']->parent_id;
				break;
			default:
				$product_id = version_compare( WC_VERSION, '3.0', '>=' ) ? $cart_item['data']->get_id() : $cart_item['data']->id;
				break;
		}
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) $product = wc_get_product( $product_id );
		$cost = floatval( version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_meta( '_assembly_cost' ) : get_post_meta( $post->ID, '_assembly_cost', true ) );
		if ( $cost == '' ) { //Default
			$cost = $this->product_assembly_cost;
		}
		return $cost;
	}

	/* Frontend - Get the assembly data from the session on page load */
	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( $this->product_assembly_cost_mode == 'product' ) {
			if ( ! empty( $values['assembly'] ) ) {
				$cart_item['assembly'] = true;
				$cost = $this->get_assembly_for_cart_item( $cart_item );
				//With price on the product itself
				if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
					$cart_item['data']->set_price( $cart_item['data']->get_price() + $cost );
				} else {
					$cart_item['data']->adjust_price( $cost ); 
				}
			}
		}
		return $cart_item;
	}

	/* Frontend - Display assembly data if present in the cart */
	public function get_item_data( $item_data, $cart_item ) {
		if ( ! empty( $cart_item['assembly'] ) ) {
			$cost = $this->get_assembly_for_cart_item( $cart_item );
			$item_data[] = array(
				'name'    => __( 'Assembly', 'product-assembly-cost' ),
				'value'   => __( 'Yes', 'product-assembly-cost' ).( $cost > 0 ? ' ('.sprintf( __( '%s / unit', 'product-assembly-cost' ), wc_price( $cost ) ).')' : '' ),
				'display' => __( 'Yes', 'product-assembly-cost' ).( $cost > 0 ? ' ('.sprintf( __( '%s / unit', 'product-assembly-cost' ), wc_price( $cost ) ).')' : '' ),
			);
		}
		return $item_data;
	}

	/* Frontend - Adjust price after adding to cart */
	public function add_cart_item( $cart_item ) {
		if ( $this->product_assembly_cost_mode == 'product' ) {
			if ( ! empty( $cart_item['assembly'] ) ) {
				$cost = $this->get_assembly_for_cart_item( $cart_item );
				//With price on the product itself
				if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
					$cart_item['data']->set_price( $cart_item['data']->get_price() + $cost );
				} else {
					$cart_item['data']->adjust_price( $cost ); 
				}
			}
		}
		return $cart_item;
	}

	/* Frontend - After ordering, add the data to the order line items */
	public function add_order_item_meta( $item_id, $cart_item ) {
		if ( ! empty( $cart_item['assembly'] ) ) {
			$cost = $this->get_assembly_for_cart_item( $cart_item );
			woocommerce_add_order_item_meta( $item_id, __( 'Assembly', 'product-assembly-cost' ), __( 'Yes', 'product-assembly-cost' ).( $cost > 0 ? ' ('.wc_price($cost).')' : '' ) );
		}
	}

	/* Frontend - Add assembly fee to the cart */
	public function assembly_fee() {
		if ( $this->product_assembly_cost_mode == 'subtotal' ) {
			if ( is_admin() && ! defined( 'DOING_AJAX' ) )
				return;
			$amount = 0;
			$items = 0;
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( ! empty( $cart_item['assembly'] ) ) {
					$cost = $this->get_assembly_for_cart_item( $cart_item );
					if ( $cart_item['quantity'] > 0 ) {
						$items += $cart_item['quantity'];
						$amount += $cost * $cart_item['quantity'];
					}
				}
			}
			$taxable = true; //This should be on the settings
			$tax_class = ''; //This should be on the settings
			if ( $amount > 0 ) {
				$this->fee_name = sprintf( __( 'Assembly (%d items)', 'product-assembly-cost' ), $items);
				WC()->cart->add_fee( $this->fee_name , $amount, $taxable, $tax_class );
				add_action( 'woocommerce_cart_totals_fee_html', array( $this, 'woocommerce_cart_totals_fee_html' ), 10, 2 );
			}
		}
	}
	public function woocommerce_cart_totals_fee_html( $cart_totals_fee_html, $fee ) {
		if ( $fee->id == sanitize_title( $this->fee_name ) ) {
			if ( wc_tax_enabled() && WC()->cart->display_prices_including_tax() && $fee->taxable ) {
				$tax = WC_Tax::get_rates( $fee->tax_class );
				foreach ( $tax as $tax1 ) {
					break; //Only one tax(?)
				}
				$cart_totals_fee_html .= '<small class="includes_tax">'.sprintf( __( '(includes %s)', 'product-assembly-cost' ), wc_price( $fee->tax ).' '.$tax1['label'] ).'</small>';
			}
		}
		return $cart_totals_fee_html;
	}

}
new WC_Product_Extra_Service_Assembly();

/* If you're reading this you must know what you're doing ;-) Greetings from sunny Portugal! */

