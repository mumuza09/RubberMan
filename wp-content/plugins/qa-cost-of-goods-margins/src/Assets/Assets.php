<?php # -*- coding: utf-8 -*-

namespace QuickAssortments\COG\Assets;

/**
 * Class AssetsEnqueue
 *
 * @package  QuickAssortments\COG\Assets
 * @author   Khan Mohammad R. <khan@quickassortments.com>
 * @version  1.0.0
 */
class Assets {

	/**
	 * The assets of WooCommerce.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $wc_assets;

	/**
	 * AssetsEnqueue constructor.
	 */
	public function __construct() {

	}

	/**
	 * Enqueueing scripts and styles.
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );

		return $this;
	}

	/**
	 * Enqueueing styles.
	 * @return void
	 */
	public function styles() {
		$this->wc_assets = new \WC_Admin_Assets();
		$this->wc_assets->admin_styles();
		wp_enqueue_style( 'qa-cog', QA_COG_BASE_URL . 'assets/css/style.css', null, '1.0.0', 'all' );
		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_style( 'woocommerce_admin_dashboard_styles' );
	}

	/**
	 * Enqueueing scripts.
	 * @return void
	 */
	public function scripts() {
		$this->wc_assets = new \WC_Admin_Assets();
		$this->wc_assets->admin_scripts();
		// Registering the script.
		wp_register_script( 'qa-cog', QA_COG_BASE_URL . 'assets/js/system.js', [ 'jquery' ], '1.0.0', true );
		// Local JS data
		$local_js_data = [ 'ajax_url' => admin_url( 'admin-ajax.php' ), 'currency' => get_woocommerce_currency(), ];
		// Pass data to myscript.js on page load
		wp_localize_script( 'qa-cog', 'QACOGAjaxObj', $local_js_data );
		// Enqueueing JS file.
		wp_enqueue_script( 'qa-cog' );

		$params = [
			'strings' => [
				'import_products' => __( 'Import', 'woocommerce' ),
				'export_products' => __( 'Export', 'woocommerce' ),
			],
			'urls'    => [
				'import_products' => current_user_can( 'import' )
					? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) )
					: null,
				'export_products' => current_user_can( 'export' )
					? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) )
					: null,
			],
		];
		wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );

		// Style ans Scripts Including
		wp_enqueue_script( 'woocommerce_admin' );
		wp_enqueue_script( 'jquery-blockui' );
		wp_enqueue_script( 'jquery-tiptip' );
		wp_enqueue_script( 'flot' );
		wp_enqueue_script( 'flot-resize' );
		wp_enqueue_script( 'flot-time' );
		wp_enqueue_script( 'flot-pie' );
		wp_enqueue_script( 'flot-stack' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'wc-enhanced-select' );
	}
}
