<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\COG\Admin;

use QuickAssortments\COG\Helpers;

/**
 * Class Fields
 *
 * @package  QuickAssortments\COG\Admin
 * @author   Khan Mohammad R. <khan@quickassortments.com>
 * @version  1.0.0
 */
class Fields {

	/**
	 * @var string
	 */
	private $prefix = '';

	/**
	 * @var string
	 */
	private $name = '';

	/**
	 * @var string
	 */
	private $bi = '';

	/**
	 * @var string
	 */
	private $currency = '';

	/**
	 * Fields constructor.
	 * @param string $prefix
	 */
	public function __construct( $prefix = '' ) {
		$this->prefix = $prefix;
		$this->name = __( 'Cost of Goods & Margins', 'qa-cost-of-goods-margins' );
		$this->bi   = 'background-image: url("' . QA_COG_BASE_URL . 'assets/img/icon-sq-bg.svg")';
		$this->currency = apply_filters( 'qa_cog_product_data_currency_filter', get_woocommerce_currency_symbol() );
	}

	/**
	 * Initiating hooks.
	 */
	public function init() {
		/**
		 * Adding cost field to product data tab.
		 */
		add_action( 'woocommerce_product_options_pricing', [ $this, 'add_cost_field_to_product_data_tab' ] );
		// Adding fields to variation admin panel
		add_action( 'woocommerce_variation_options_pricing', [ $this, 'add_cost_field_to_variation_data_tab' ], 10, 3 );

		/**
		 * Adding cost field to bulk edit.
		 */
		add_action( 'woocommerce_product_quick_edit_start', [ $this, 'bulk_edit_cost_field' ] );

		/**
		 * Saving cost field data.
		 */
		add_action( 'woocommerce_admin_process_product_object', [ $this, 'save_cost_field' ], 10, 1 );
		add_action( 'woocommerce_product_quick_edit_save', [ $this, 'save_cost_field' ], 10, 1 );
		// Saving variation cost fields
		add_action( 'woocommerce_save_product_variation', [ $this, 'save_variation_cost_field' ], 10, 2 );

		return $this;
	}

	/**
	 *
	 */
	public function add_cost_field_to_product_data_tab() {
		global $post;

		if ( ! ( $product = wc_get_product( $post ) ) instanceof \WC_Product )
			return;

		$cp = get_post_meta( $product->get_id(), $this->prefix . 'cost', true );
		$cp = apply_filters( 'qa_cog_product_pricing_cost_price', $cp, $product, $this->currency );

		$price = apply_filters( 'qa_cog_product_price', $product->get_price(), $product );

		$fields['cost'] = [
			'id'            => $this->prefix . 'cost',
			'style'			=> $this->bi,
			'class'			=> 'qa-input-field',
			'value'         => $cp,
			'data_type'     => 'price',
			'placeholder'   => '0',
			'label'         => __( 'Cost Price', 'qa-cost-of-goods-margins' ) . ' (' . $this->currency . ')',
		];
		$fields['stock_value'] = [
			'id'            	=> $this->prefix . 'stock_value',
			'style'				=> $this->bi,
			'class'				=> 'qa-input-field',
			'value'         	=> $product->get_manage_stock() ? Helpers\Formulae::stock_value( $cp, $product->get_stock_quantity() ) : '–',
			'data_type'     	=> 'price',
			'placeholder'   	=> '0',
			'label'         	=> __( 'Stock Value', 'qa-cost-of-goods-margins' ) . ' (' . $this->currency . ')',
			'custom_attributes' => [ 'readonly' => 'true' ],
			'desc_tip'			=> true,
			'description'		=> __( 'Stock management for this product must need to be turned on', 'qa-cost-of-goods-margins' ),
		];
		$fields['mark_up'] = [
			'id'            	=> $this->prefix . 'mark_up',
			'style'				=> $this->bi,
			'class'				=> 'qa-input-field',
			'value'         	=> ( $mu = Helpers\Formulae::markup( $cp, $price ) ) ? $mu : '-',
			'data_type'     	=> 'price',
			'placeholder'   	=> '0',
			'label'         	=> __( 'Mark Up', 'qa-cost-of-goods-margins' ),
			'custom_attributes' => [ 'readonly' => 'true' ],
		];
		$fields['margin'] = [
			'id'                => $this->prefix . 'margin',
			'style'				=> $this->bi,
			'name'              => '',
			'class'				=> 'qa-input-field',
			'value'             => ( $m = Helpers\Formulae::margin( $cp, $price ) ) ? $m . '%' : '-',
			'data_type'         => 'price',
			'placeholder'       => '0',
			'label'             => __( 'Margin', 'qa-cost-of-goods-margins' ) . '(%)',
			'custom_attributes' => [ 'readonly' => 'true' ],
		];

		$fields = apply_filters( 'qa_cog_product_data_tab_fields', $fields, $product );

		/**
		 * qa_cog_product_data_tab_before action.
		 *
		 * @since 1.0.0
		 * @param \WC_Product $variation
		 * @param int     	  $loop
		 */
		do_action( 'qa_cog_product_data_tab_before', $product );

		foreach ( $fields as $field ) {
			woocommerce_wp_text_input( $field );
		}

		/**
		 * qa_cog_product_data_tab_after action.
		 *
		 * @since 1.0.0
		 * @param \WC_Product $product
		 * @param int     	  $loop
		 */
		do_action( 'qa_cog_product_data_tab_after', $product );
	}

	/**
	 * @param $loop
	 * @param $variation_data
	 * @param $variation
	 */
	public function add_cost_field_to_variation_data_tab( $loop, $variation_data, $variation ) {
		if ( 'product_variation' !== $variation->post_type ) {
			return;
		}

		$variation = wc_get_product( $variation->ID );

		$cp = get_post_meta( $variation->get_id(), $this->prefix . 'cost', true );
		$cp = apply_filters( 'qa_cog_variation_pricing_cost_price', $cp, $variation, $this->currency );

		$price = apply_filters( 'qa_cog_variation_price', $variation->get_price(), $variation, $loop );

		$fields['cost'] = [
			'id'            => $this->prefix . "cost_{$loop}",
			'name'          => $this->prefix . "cost[$loop]",
			'class'			=> 'qa-input-field',
			'style'			=> $this->bi,
			'value'         => $cp,
			'data_type'     => 'price',
			'placeholder'   => '0',
			'label'         => __( 'Cost Price', 'qa-cost-of-goods-margins' ) . ' (' . $this->currency . ')',
			'wrapper_class' => 'form-row form-row-first',
		];

		$fields['stock_value'] = [
			'id'            	=> $this->prefix . "stock_value_{$loop}",
			'name'				=> '',
			'class'				=> 'qa-input-field',
			'style'				=> $this->bi,
			'value'         	=> $variation->get_manage_stock() ? $this->currency . Helpers\Formulae::stock_value( $cp, $variation->get_stock_quantity() ) : '–',
			'label'         	=> __( 'Stock Value', 'qa-cost-of-goods-margins' ) . ' (' . $this->currency . ')',
			'custom_attributes' => [ 'readonly' => 'true' ],
			'wrapper_class' 	=> 'form-row form-row-last',
			'desc_tip'			=> true,
			'description'		=> __( 'Stock management for this product must need to be turned on', 'qa-cost-of-goods-margins' ),
		];

		$fields['mark_up'] = [
			'id'            	=> $this->prefix . "mark_up_{$loop}",
			'name'				=> '',
			'class'				=> 'qa-input-field',
			'style'				=> $this->bi,
			'value'         	=> ( $mu = Helpers\Formulae::markup( $cp, $price ) ) ? $mu : '-',
			'label'         	=> __( 'Mark Up', 'qa-cost-of-goods-margins' ),
			'custom_attributes' => [ 'readonly' => 'true' ],
			'wrapper_class' 	=> 'form-row form-row-first',
		];

		$fields['margin'] = [
			'id'                => $this->prefix . "margin_{$loop}",
			'name'              => '',
			'class'				=> 'qa-input-field',
			'style'				=> $this->bi,
			'value'             => ( $m = Helpers\Formulae::margin( $cp, $price ) ) ? $m . '%' : '-',
			'label'             => __( 'Margin', 'qa-cost-of-goods-margins' ) . '(%)',
			'custom_attributes' => [ 'readonly' => 'true' ],
			'wrapper_class' 	=> 'form-row form-row-last',
		];

		$fields = apply_filters( 'qa_cog_variation_data_tab_fields', $fields, $variation, $loop );

		/**
		 * qa_cog_variation_data_tab_before action.
		 *
		 * @since 1.0.0
		 * @param \WC_Product_Variation $variation
		 * @param int     				$loop
		 */
		do_action( 'qa_cog_variation_data_tab_before', $variation, $loop );

		foreach ( $fields as $field ) {
			woocommerce_wp_text_input( $field );
		}

		/**
		 * qa_cog_variation_data_tab_after action.
		 *
		 * @since 1.0.0
		 * @param \WC_Product_Variation $variation
		 * @param int     				$loop
		 */
		do_action( 'qa_cog_variation_data_tab_after', $variation, $loop );
	}

	/**
	 *
	 */
	public function bulk_edit_cost_field() {
		$args = [
			'name' 	=> esc_attr( $this->prefix . 'cost' ),
			'label'	=> esc_html__( 'Cost Price', 'qa-cost-of-goods-margins' ),
			'style'	=> esc_attr( $this->bi ),
		];

		Helpers\Template::include_template( __FUNCTION__, $args, 'admin/fields' );
	}

	/**
	 * Saving cost field data.
	 *
	 * @param object $product
	 *
	 * @return boolean
	 */
	public function save_cost_field( $product ) {
		$cp = apply_filters( 'qa_cog_general_product_before_save_cost_price', $_POST[ $this->prefix . 'cost' ], $product );
		$cp = abs( $cp );
		if ( ! isset( $cp ) || is_null( $cp ) || ! is_numeric( $cp ) )
			return false;
		return update_post_meta( $product->get_id(), $this->prefix . 'cost', $cp );
	}

	/**
	 * Saving variation cost field
	 *
	 * @param int $variation_id
	 * @param int $i
	 * @return bool|int
	 */
	public function save_variation_cost_field( $variation_id, $i ) {
		$cp = apply_filters( 'qa_cog_variation_product_before_save_cost_price', $_POST[ $this->prefix . 'cost' ][$i], $variation_id, $i );
		$cp = abs( $cp );
		if ( empty( $cp ) || ! is_numeric( $cp ) )
			return false;
		return update_post_meta( $variation_id, $this->prefix . 'cost', $cp );
	}
}
