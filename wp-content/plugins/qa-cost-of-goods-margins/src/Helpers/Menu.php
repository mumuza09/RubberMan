<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\COG\Helpers;


class Menu {

	public function __construct() {

	}

	public function init() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	public function admin_menu() {

		if ( ! empty( $GLOBALS['admin_page_hooks']['qa-base-admin-page'] ) ) return;

		add_menu_page(
			__( 'Quick Assortments', 'qa-cost-of-goods-margins' ),
			__( 'Quick Assortments', 'qa-cost-of-goods-margins' ),
			'manage_options',
			'qa-base-admin-page',
			[ $this, 'admin_base_page_callback' ],
			QA_COG_BASE_URL . 'assets/img/icon-sq-bg.svg', // Dynamic
			50
		);

		do_action( 'qa_after_base_menu_page' );
	}

	public function admin_base_page_callback() {
		do_action( 'qa_admin_base_page_callback' );
	}
}