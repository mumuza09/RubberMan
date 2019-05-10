<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\COG\Helpers;

/**
 * Class Formulae
 *
 * @package  QuickAssortments\COG\Helpers
 * @author   Khan Mohammad R. <khan@quickassortments.com>
 * @version  1.0.0
 */
class Formulae {

	/**
	 * Getting stock value.
	 *
	 * @param float|int $cost
	 * @param float|int $stock_value
	 *
	 * @return float|int
	 */
	public static function stock_value( $cost, $stock_value ) {
		return self::format( $cost * $stock_value );
	}

	/**
	 * Calculates profit based on cost and revenue.
	 *
	 * @param float|int $cost
	 * @param float|int $revenue
	 *
	 * @return float|int
	 */
	public static function profit( $cost, $revenue ) {
		return self::format( $revenue - $cost );
	}

	/**
	 * Calculates markup based on cost and revenue.
	 *
	 * @param float|int $cost
	 * @param float|int $revenue
	 *
	 * @return float|int
	 */
	public static function markup( $cost, $revenue ) {
        $cost = $cost ? self::format( ( $revenue - $cost ) / $cost ) : false;
        return abs( $cost );
	}

	/**
	 * Calculates margin based on cost and revenue.
	 *
	 * @param float|int $cost
	 * @param float|int $revenue
	 *
	 * @return float|int
	 */
	public static function margin( $cost, $revenue ) {
		return is_numeric( $revenue ) && is_numeric( $cost ) ? self::format( ( $revenue - $cost ) * 100 / $revenue ) : false;
	}

	/**
	 * Formatting numbers.
	 *
	 * @param $input
	 *
	 * @return float|int
	 */
	public static function format( $input ) {
		return number_format( $input, 2, wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );
	}
}