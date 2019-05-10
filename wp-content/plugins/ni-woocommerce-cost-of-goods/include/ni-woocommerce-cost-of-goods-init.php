<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'Ni_WooCommerce_Cost_Of_Goods_Init' ) ) { 
	class Ni_WooCommerce_Cost_Of_Goods_Init {
		var $ni_constant = array();  
		 public function __construct($ni_constant = array()){
			$this->ni_constant = $ni_constant; 
			$this->add_cost_of_goods();
			
			add_action('admin_menu', 		array($this,'admin_menu'));	
			add_action( 'admin_enqueue_scripts',  array(&$this,'admin_enqueue_scripts' ));
			add_action( 'wp_ajax_ni_cog_action',  array(&$this,'ajax_ni_cog_action' )); /*used in form field name="action" value="my_action"*/
			add_filter( 'plugin_row_meta',  array(&$this,'plugin_row_meta' ), 10, 2 );
			
			add_filter( 'admin_footer_text',  array(&$this,'admin_footer_text' ),101);
			
		 }
		 function admin_footer_text($text){
		
			$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : '';
			$admin_pages = $this->get_admin_pages();
			if (in_array($page,$admin_pages)){
					
					$text = sprintf( __( 'Thank you for using our plugins <a href="%s" target="_blank">naziinfotech</a>' ,'nisalesreport'), 
					__( 'http://naziinfotech.com/'  ,'nisalesreport') );
					$text = "<span id=\"footer-thankyou\">". $text ."</span>"	 ;
				
			 }
			return $text ; 
		 }
		 function get_admin_pages(){
			$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : '';
			$admin_pages = array();
			$admin_pages[] = 'ni-cost-of-goods';
			$admin_pages[] = 'ni-cog-report';
			$admin_pages[] = 'ni-cog-top-profit-product';
			$admin_pages[] = 'ni-cog-other-plugin';
			return $admin_pages;
		 
		 }
		 function admin_menu(){
			add_menu_page( 'Ni Cost Of Goods', 'Ni Cost Of Goods', $this->ni_constant['manage_options'], $this->ni_constant['menu'], array( $this, 'add_page'), 'dashicons-performance', "57.6361" );
			add_submenu_page($this->ni_constant["menu"],"Dashboard","Dashboard", $this->ni_constant['manage_options'],$this->ni_constant["menu"], array( $this, 'add_page'));
			add_submenu_page($this->ni_constant["menu"],"Ni COG Report","Ni COG Sales Report",  $this->ni_constant['manage_options'],'ni-cog-report', array( $this, 'add_page'));
			
			
				add_submenu_page($this->ni_constant["menu"]
		,__( 'Top Profit Product', 'nisalesreport' )
		,__( 'Top Profit Product', 'nisalesreport' )
		, $this->ni_constant['manage_options'], 'ni-cog-top-profit-product' 
		, array(&$this,'add_page'));
			
			
			add_submenu_page($this->ni_constant["menu"],"Other plugin","Other plugin",  $this->ni_constant['manage_options'],'ni-cog-other-plugin', array( $this, 'add_page'));
			
			
			
			do_action("nicog_add_menu_end",$this->ni_constant["menu"]);
			
			
	
		 }
		 function admin_enqueue_scripts(){
		 	if (isset($_REQUEST["page"])){
				$page = $_REQUEST["page"];
				
				$menu_name = $this->get_menu_name();
			   
				if ($page =="ni-cost-of-goods"){
					wp_register_style( 'ni-sales-report-summary-css', plugins_url( '../assets/css/ni-sales-report-summary.css', __FILE__ ));
					wp_enqueue_style( 'ni-sales-report-summary-css' );
					wp_register_style( 'ni-font-awesome-css', plugins_url( '../assets/css/font-awesome.css', __FILE__ ));
					wp_enqueue_style( 'ni-font-awesome-css' );
				}
				if ($page =="ni-cog-report"){
					wp_enqueue_script( 'ni-ajax-script-cog', plugins_url( '../assets/js/script.js', __FILE__ ), array('jquery') );
				
					wp_localize_script( 'ni-ajax-script-cog','ni_cog_ajax_object',array('ni_cog_ajax_object_ajaxurl'=>admin_url('admin-ajax.php') ) );
					wp_register_style( 'ni-ajax-css-cog', plugins_url('../assets/css/ni-cog.css', __FILE__));
					wp_enqueue_style( 'ni-ajax-css-cog' );
					wp_enqueue_script( 'ni-ajax-script-cog-report', plugins_url( '../assets/js/ni-sales-order-cost-of-goods.js', __FILE__ ) );
				}
				if ($page =="ni-cog-top-profit-product"){
					wp_register_style( 'ni-ajax-css-cog', plugins_url('../assets/css/ni-cog.css', __FILE__));
					wp_enqueue_style( 'ni-ajax-css-cog' );
				}
				if (in_array($page ,	$menu_name )){
					wp_enqueue_script( 'ni-ajax-script-cog', plugins_url( '../assets/js/script.js', __FILE__ ), array('jquery') );
					wp_localize_script( 'ni-ajax-script-cog','ni_cog_ajax_object',array('ni_cog_ajax_object_ajaxurl'=>admin_url('admin-ajax.php') ) );
				}
			}
		 }
		 function get_menu_name(){
		 	$menu_name = array();
			$menu_name[]="ni-cost-of-goods";
			$menu_name[] ="ni-cog-report";
			$menu_name[] ='ni-cog-top-profit-product';
			
		
			return apply_filters("nicog_menu_name", $menu_name);
		 }
		 function add_page(){
			 if(isset($_REQUEST["page"])){
			 	$page =  $_REQUEST["page"];
				if ($page=="ni-cog-report"){
					include_once("ni-cog-sales-report.php");
					$obj =  new Ni_COG_Sales_Report();
					$obj->page_init();
				
				}
				if ($page=="ni-cost-of-goods"){
					include_once("ni-cog-dashboard.php");
					$obj =  new Ni_COG_Dashboard();
					$obj->page_init();
				}
				if ($page=="ni-cog-other-plugin"){
					include_once("ni-sales-report-addons.php");
					$obj =new ni_sales_report_addons();
					$obj->page_init();
				}
				if ($page=="ni-cog-top-profit-product"){
					include_once('ni-cog-top-profit-product.php');
					$obj = new Ni_COG_Top_Profit_Product(); 
					$obj->page_init(); 
				}
				
			 }
		 }
		 function add_cost_of_goods(){
			 include("ni-woocommerce-cost-of-goods-function.php");
			 $obj_cogf =  new Ni_WooCommerce_Cost_Of_Goods_Function();
		 }
		 function plugin_row_meta($links, $file){
			if ( $file == "ni-woocommerce-cost-of-goods/ni-woocommerce-cost-of-goods.php" ) {
				$row_meta = array(
				
				'ni_cogpro_viewdemo'=> '<a target="_blank" href="http://demo.naziinfotech.com?demo_login=woo_cost_of_goods">View Pro Demo</a>',
				'ni_cogpro_buypro'=> '<a target="_blank" href="http://naziinfotech.com/product/ni-woocommerce-cost-of-good-pro/">Buy Pro Version</a>',	
				'ni_cogpro_review'=> '<a target="_blank" href="https://wordpress.org/plugins/ni-woocommerce-cost-of-goods/">Write a Review</a>'	);
					
	
				return array_merge( $links, $row_meta );
			}
			return (array) $links;
		}
		 function ajax_ni_cog_action(){
		 	//echo json_encode($_REQUEST);
			if (isset($_REQUEST["sub_action"])){
				
				do_action("nicog_ajax_action",$_REQUEST["sub_action"]);
				
				$sub_action = $_REQUEST["sub_action"];
				if ($sub_action=="ni_cog_sales_report"){
					include_once("ni-cog-sales-report.php");
					$obj =  new Ni_COG_Sales_Report();
					$obj->page_ajax();
				
				}
				
			}
			die;
		 }
	}
}
?>