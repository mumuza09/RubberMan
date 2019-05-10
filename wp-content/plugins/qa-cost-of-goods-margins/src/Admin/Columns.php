<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\COG\Admin;

use QuickAssortments\COG\Helpers\Formulae;

/**
 * Class Columns
 *
 * @package  QuickAssortments\COG\Admin
 * @author   Khan Mohammad R. <khan@quickassortments.com>
 * @version  1.0.0
 */

/**
 * Class Columns
 * @package QuickAssortments\COG\Columns
 */
class Columns {

	/**
	 * @var string
	 */
	private $prefix = '';

	/**
	 * @var string
	 */
	private $currency = '';

	/**
	 * Columns constructor.
	 * @param string $prefix
	 */
	public function __construct( $prefix = '' ) {
		$this->prefix = $prefix;
		$this->currency = apply_filters( 'qa_cog_product_data_currency_filter', get_woocommerce_currency_symbol() );
	}

	/**
	 * Initiating hooks.
	 */
	public function init() {
		/**
		 * Adding columns to product backend.
		 */
		add_filter( 'manage_edit-product_columns', [ $this, 'additional_columns' ], 10, 1 );

		/**
		 * Adding value to the custom columns at products backend.
		 */
		add_action( 'manage_product_posts_custom_column', [ $this, 'column_cost_price_and_stock_value_data' ], 10, 2 );

		return $this;
	}

	/**
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function additional_columns( $columns ) {
		$return = [];
		foreach ( $columns as $k => $n ) {
			$return[$k] = $n;

			if ( 'price' !== $k ) continue;

			$return['cost_price'] 	= esc_html__( 'Cost Price','qa-cost-of-goods-margins' );
			$return['stock_value']	= esc_html__( 'Stock Value','qa-cost-of-goods-margins' );
			$return['markup'] 		= esc_html__( 'Mark Up','qa-cost-of-goods-margins' );
			$return['margin'] 		= esc_html__( 'Margin','qa-cost-of-goods-margins' );
		}
		return apply_filters( 'qa_cog_additional_columns', $return );
	}


	/**
	 *
	 *
	 * @param string $column
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function column_cost_price_and_stock_value_data( $column, $post_id ) {
		// Instantiating individual product.
		$product = wc_get_product( $post_id );
		$cp = get_post_meta( $post_id, $this->prefix . 'cost', true );
		$cp = is_numeric( $cp ) ? $cp : 0;

		if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) )
			$this->variable_products_column_data( $column, $product, $cp );
		else
			$this->general_products_column_data( $column, $product, $cp );

	}

	/**
	 * @param 	string 	$column
	 * @param 	object 	$product
	 * @param 	float 	$cp
	 * @return 	void
	 */
	public function general_products_column_data( $column, $product, $cp ) {

		$cp = apply_filters( 'qa_cog_column_cost_price_general', $cp, $product, $this->currency );

		$price = apply_filters( 'qa_cog_column_price_general', $product->get_price(), $product, $this->currency );

		switch ( $column ) {
			case 'cost_price':
				echo $cp ? esc_html( $this->currency . $cp ) : '–';
				break;

			case 'stock_value':
				if ( $product->get_manage_stock() )
					echo esc_html( $this->currency . Formulae::stock_value( $cp, $product->get_stock_quantity() ) );
				else
					echo '–';
				break;

			case 'markup':
				$mu = Formulae::markup( $cp, $price );
				echo $mu ? esc_html( $mu ) : '–';
				break;

			case 'margin':
				$m = Formulae::margin( $cp, $price );
				echo $m ? esc_html( ( $m ) . '%' ) : '–';
				break;
		}
	}

	/**
	 * @param 	string 	$column
	 * @param 	object 	$product
	 * @param 	float 	$cp
	 * @return 	void
	 */
	public function variable_products_column_data( $column, $product, $cp ) {
		$children = $product->get_children();

		$data = $this->get_children_data( $children, $product->get_type() );

		switch ( $column ) {
			case 'cost_price':
				$this->formatted_column_data( $children, $data, $column, $this->currency );
				break;
			case 'stock_value':
				$this->formatted_column_data( $children, $data, $column, $this->currency );
				break;
			case 'markup':
				$this->formatted_column_data( $children, $data, $column );
				break;
			case 'margin':
				$this->formatted_column_data( $children, $data, $column, '', '%' );
				break;
		}
	}

	/**
	 * Helper method to get children data for variable products
	 *
	 * @param object $children
	 * @param string $type
	 * @return array
	 */
	protected function get_children_data( $children, $type = 'variation' ) {
		$data = [];

		foreach ( $children as $child ) {
			$product = wc_get_product( $child );

			$data['price'][ $child ] = apply_filters( 'qa_cog_column_price_' . $type, $product->get_price(), $product, $this->currency );
			$data['price'][ $child ] = is_numeric( $data['price'][ $child ] ) ? $data['price'][ $child ] : null;

			$data['cost_price'][ $child ] = get_post_meta( $product->get_id(), $this->prefix . 'cost', true  );
			$data['cost_price'][ $child ] = is_numeric( $data['cost_price'][ $child ] ) ? $data['cost_price'][ $child ] : null;
			$data['cost_price'][ $child ] = apply_filters( 'qa_cog_column_cost_price_' . $type, $data['cost_price'][ $child ], $product, $this->currency );

			if ( '-' === $data['price'][$child] || '-' === $data['cost_price'][$child] ) {
				$data['stock_value'][ $child ] = $data['markup'][ $child ] = $data['margin'][ $child ] = null;
				continue;
			}

			if ( $product->get_manage_stock() ) {
				$data['stock_value'][ $child ] = Formulae::stock_value( $data['cost_price'][ $child ], $product->get_stock_quantity() );
				$data['stock_value'][ $child ] = apply_filters( 'qa_cog_column_stock_value_' . $type, $data['stock_value'][ $child ], $product, $this->currency );
			} else {
				$data['stock_value'][ $child ] = null;
			}

			$data['markup'][ $child ] = Formulae::markup( $data['cost_price'][ $child ], $data['price'][ $child ] );
			$data['markup'][ $child ] = apply_filters( 'qa_cog_column_markup_' . $type, $data['markup'][ $child ], $product, $this->currency );

			$data['margin'][ $child ] = Formulae::margin( $data['cost_price'][ $child ], $data['price'][ $child ] );
			$data['margin'][ $child ] = apply_filters( 'qa_cog_column_margin_' . $type, $data['margin'][ $child ], $product, $this->currency );
		}

		return $data;
	}

	/**
	 * Helper function to
	 *
	 * @param array $args
	 * @return mixed
	 */
	protected function get_min_max( $args = [] ) {
		if ( ! is_array( $args ) || count( $args ) < 2 )
			return end( $arg );
		$data['min'] = array_filter( $args,'strlen' ) ? min( array_filter( $args,'strlen' ) ) : 0 ;
		$data['max'] = max( $args ) ? max( $args ) : 0;
		if ( $data['min'] == $data['max']  )
			return $data['min'];
		return $data;
	}

	/**
	 * Helper method to print data in column fields
	 *
	 * @param array $children
	 * @param array $data
	 * @param string $column
	 * @param string $prefix
	 * @param string $suffix
	 * @return void
	 */
	protected function formatted_column_data( $children = [], $data = [], $column = '', $prefix = '', $suffix = '' ) {
		if ( empty( $data[ $column ] ) || empty( $children ) ) {
			echo '–';
			return;
		}

		$data = $this->get_min_max( $data[ $column ] );
		if ( is_array( $data ) )
			echo $prefix . $data['min'] . $suffix . ' – ' . $prefix . $data['max'] . $suffix;
		else if ( ! $data )
			echo '–';
		else
			echo $prefix . $data . $suffix;
	}

}
