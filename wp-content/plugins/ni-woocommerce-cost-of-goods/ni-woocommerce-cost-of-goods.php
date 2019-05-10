<?php
/*
Plugin Name: Ni WooCommerce Cost Of Goods
Description: Ni WooCommerce Cost Of Goods provide the option to add the cost or purchase price of simple product and variation product.
Author: anzia
Version: 2.1.6
Author URI: http://naziinfotech.com/
Plugin URI: https://wordpress.org/plugins/ni-woocommerce-cost-of-goods/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.html
WC requires at least: 3.0.0
WC tested up to:3.5.8
Last Updated Date: 16-April-2019
*/
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'Ni_WooCommerce_Cost_Of_Goods' ) ) {
	class Ni_WooCommerce_Cost_Of_Goods{
		var $ni_constant = array();  
		 public function __construct(){
			 $this->ni_constant = array(
				 "prefix" 		  => "ni-",
				 "manage_options" => "manage_options",
				 "menu"   		  => "ni-cost-of-goods",
				);
			include("include/ni-woocommerce-cost-of-goods-init.php");
			$obj_init =  new Ni_WooCommerce_Cost_Of_Goods_Init($this->ni_constant);
		 }
	}
	$obj = new Ni_WooCommerce_Cost_Of_Goods();
}
?>