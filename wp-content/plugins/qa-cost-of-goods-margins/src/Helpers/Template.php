<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\COG\Helpers;

/**
 * Class View
 *
 * @package QuickAssortments\COG\Helpers
 * @author  Khan Mohammad R. <khan@quickassortments.com>
 * @version 1.0.0
 */
class Template {
	/**
	 * @var string
	 */
	private static $base = '';

	public function __construct( $base ) {
		self::$base = $base;
	}

	/**
	 * Return the path of the template
	 *
	 * @param string $template
	 * @param string $base
	 * @return bool|string
	 */
	public static function load_template( $template = '', $base = '' ) {
		if ( '.php' !== substr( $template, - 4 ) ) {
			$template .= '.php';
		}

		if ( $base )
			$template = self::$base  . $base . '/' . $template;
		else
			$template = self::$base  . $template;

		// Allow using full paths as view name.
		if ( ! is_file( $template ) )
			return false;
		return $template;
	}

	/**
	 * Including templates
	 *
	 * @param string $template
	 * @param array $args
	 * @param string $base
	 */
	public static function include_template( $template = '', $args = [], $base = '' ) {
		$template = self::load_template( $template, $base );

		if ( ! is_array( $args ) ) return;

		extract( $args );

		if ( QA_COG_DEBUG ) {
			/* @noinspection PhpIncludeInspection */
			include( $template );
		} else {
			/* @noinspection PhpIncludeInspection */
			@include( $template ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}
	}

	/**
	 * Return template as string
	 *
	 * @param string $template
	 * @param array $args
	 * @param string $base
	 * @return false|string
	 */
	public static function template_to_string( $template = '', $args = [], $base = '' ) {
		ob_start();
		self::include_template( $template, $args, $base );

		return ob_get_clean();
	}

}