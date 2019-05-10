<?php # -*- coding: utf-8 -*-
namespace QuickAssortments\COG\Admin\Settings;

use QuickAssortments\COG\Helpers;

/**
 * Class Page
 * @package QuickAssortments\COG\Admin
 * @author  Khan Mohammad R. <khan@quickassortments.com>
 * @version 1.0.0
 */
class Page {

	/**
	 * Including necessary classes
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array $classes Including necessary classes.
	 */
	private $classes = [];

	/**
	 * Product settings
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array $classes Including necessary classes.
	 */
	private $prod_sett =[];

	/**
	 * Page constructor.
	 */
	public function __construct() {
		$this->prod_sett = [
			'markup'		=> get_option( 'qa_cog_main_settings_show_markup_checkbox' ),
			'stock_value' 	=> get_option( 'qa_cog_main_settings_show_stock_value_checkbox' ),
			'margin'		=> get_option( 'qa_cog_main_settings_show_margin_checkbox' ),
		];
	}

	/**
	 *
	 */
	public function init() {
		( new Helpers\Menu() )->init();
		add_action( 'qa_after_base_menu_page', [ $this, 'admin_sub_menu' ] );

		// Pluggable hook method(function)
		if ( apply_filters( 'qa_cog_admin_base_content_bool', true ) ) {
			add_action( 'qa_admin_base_page_callback', [ $this, 'qa_admin_base_page_content' ], 0 );
		}
		add_action( 'qa_cog_admin_page_callback', [ $this, 'admin_page_content' ] );
		add_action( 'qa_cog_admin_page_body', [ $this, 'qa_cog_admin_page_body' ], 0, 1 );

		add_filter( 'qa_cog_additional_columns', [ $this, 'addition_columns_settings' ], 0, 1 );

		( new Settings() )->init();

		return $this;
	}

	/**
	 *
	 */
	public function admin_sub_menu() {
		add_submenu_page(
			'qa-base-admin-page',
			__( 'Cost of Goods', 'qa-cost-of-goods-margins' ),
			__( 'Cost of Goods', 'qa-cost-of-goods-margins' ),
			'manage_options',
			'qa-cog-admin-page',
			[ $this, 'admin_page_callback' ]
		);
	}

	/**
	 *
	 */
	public function qa_admin_base_page_content() {
		wp_safe_redirect( menu_page_url( 'qa-cog-admin-page' ) );
		exit();
		// $args = [];
		// Helpers\Template::include_template( __FUNCTION__, $args, 'admin/settings' );
	}

	/**
	 *
	 */
	public function admin_page_callback() {
		do_action( 'qa_cog_admin_page_callback' );
	}

	/**
	 *
	 */
	public function admin_page_content() {
		$args = [
			'icon' => QA_COG_BASE_URL . 'assets/img/icon-sq-bg.svg',
		];
		Helpers\Template::include_template( __FUNCTION__, $args, 'admin/settings' );
	}

	public function qa_cog_admin_page_body( $page ) {
		if ( 'qa-cog-admin-page' !== $page ) return;

		$current_tab = empty( $_GET['tab'] ) ? 'qa_cog_main_settings' : sanitize_key( $_GET['tab'] );
		// echo $current_tab;
		$args = [
			'module'      => 'main',
			'current_tab' => $current_tab,
			'page_slug'   => 'qa-cog-admin-page',
		];

		$args['tabs'] = apply_filters('qa_cog_' . $args['module'] . '_tabs_array', array());

		Helpers\Template::include_template( __FUNCTION__, $args, 'admin/settings' );
	}

	/**
	 * Implementing the settings for columns.
	 *
	 * @param array $columns
	 * @return mixed
	 */
	public function addition_columns_settings( $columns ) {
		foreach ( $this->prod_sett as $pk => $ps ) {
			if ( $ps === 'no' ) {
				unset( $columns[ $pk ] );
			}
		}
		return $columns;
	}
}