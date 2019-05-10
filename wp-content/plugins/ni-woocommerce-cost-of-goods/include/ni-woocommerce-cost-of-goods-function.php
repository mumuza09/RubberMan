<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'Ni_WooCommerce_Cost_Of_Goods_Function' ) ) { 
	class Ni_WooCommerce_Cost_Of_Goods_Function {
		 var $ni_cost_goods ='_ni_cost_goods';
		 public function __construct(){
		 	 //http://www.remicorson.com/mastering-woocommerce-products-custom-fields/
			 //http://www.remicorson.com/woocommerce-custom-fields-for-variations/
			
			/*Simple Proudtc*/ 
			 
			/*Cost of goods for Simple Product*/
			add_action( 'woocommerce_product_options_general_product_data', array(&$this,'ni_add_custom_cost_of_goods_general') );
			
			// Save Fields
			add_action( 'woocommerce_process_product_meta', array(&$this,'ni_save_custom_cost_of_goods_general' ));
			
			/*End Simple Product*/
			
			/*Variation Product*/
			// Add Variation Settings
			add_action( 'woocommerce_product_after_variable_attributes',  array(&$this,'ni_variation_settings_fields'), 10, 3 );
			// Save Variation Settings
			add_action( 'woocommerce_save_product_variation',  array(&$this,'ni_save_variation_settings_fields'), 10, 2 );

			/*End Product*/
			
			//add_action( 'woocommerce_add_order_item_meta',  array(&$this,'ni_woocommerce_add_order_item_meta'), 10, 3);
			add_action( 'woocommerce_new_order_item',  array(&$this,'ni_woocommerce_add_order_item_meta'), 10, 3);
			
			/*Add COG of goods Columns*/
			add_filter( 'manage_edit-product_columns', array(&$this,'ni_cost_of_goods_field'), 10, 2 );
			/*Add COG of goods Columns Value*/
			add_action( 'manage_product_posts_custom_column', array(&$this,'ni_cost_of_goods_field_value'), 10, 2 );
			add_action( 'admin_print_styles', array(&$this,'in_add_order_ni_cost_goods_column_style') );
			
		 }
		
		 function ni_add_custom_cost_of_goods_general(){
			echo '<div class="show_if_simple">'; 	
				woocommerce_wp_text_input( 
					array( 
						'id'          => $this->ni_cost_goods, 
						'label'       => __( 'Cost Of Goods', 'nicostofgoods' ), 
						'placeholder' => __( 'Cost Of Goods', 'nicostofgoods' ),
						'desc_tip'    => 'true',
						'description' => __( 'Enter Cost Of Goods here.', 'nicostofgoods' ) ,
						'class'		  =>"short wc_input_price"
					)
				);
			echo '</div>';	
		 }
		 function ni_save_custom_cost_of_goods_general( $post_id){
		 // Text Field
			$ni_cost_goods = $_POST[ $this->ni_cost_goods];
			if( !empty( $ni_cost_goods  ) ){
				update_post_meta( $post_id,  $this->ni_cost_goods, esc_attr( $ni_cost_goods ) );
			}
	
		 }
		 /*Variation*/
		 function ni_variation_settings_fields($loop, $variation_data, $variation ){
			 // Text Field
			echo '<div class="show_if_variable">'; 	 
				woocommerce_wp_text_input( 
					array( 
						'id'          => $this->ni_cost_goods.'[' . $variation->ID . ']', 
						'label'       => __( 'Cost Of Goods', 'nicostofgoods' ), 
						'placeholder' => __( 'Cost Of Goods', 'nicostofgoods' ), 
						'desc_tip'    => 'true',
						'description' => __( 'Enter Cost Of Goods here.', 'nicostofgoods' ) ,
						'value'       => get_post_meta( $variation->ID, $this->ni_cost_goods, true )
					)
				);
			echo '</div>';		
		 }
		 function ni_save_variation_settings_fields( $post_id ){
			 $ni_cost_goods= $_POST[$this->ni_cost_goods][ $post_id ];
			if( ! empty( $ni_cost_goods) ) {
				update_post_meta( $post_id, $this->ni_cost_goods, esc_attr( $ni_cost_goods ) );
			}
		 }
		 function ni_woocommerce_add_order_item_meta($item_id, $values, $cart_item_key){
		 	$ni_cost_goods = 0;
			
			//error_log(print_r($values->legacy_values,true));
			
			//$v = $values->legacy_values;
			//error_log(print_r( $values,true));
			//error_log($values["product_id"]);
			//error_log($values["variation_id"]);
			
			//error_log(print_r($values,true));
			
			//error_log(print_r($values["order-items"],true));
			
			$product_id = ($values["variation_id"]>0)?$values["variation_id"]:$values["product_id"];
			$ni_cost_goods = get_post_meta($product_id,$this->ni_cost_goods,true);
			if (empty($ni_cost_goods)){
				$ni_cost_goods = 0;
			}
		
			wc_update_order_item_meta($item_id, $this->ni_cost_goods, $ni_cost_goods );
		 }
		 function ni_cost_of_goods_field($columns){
			
			$new_columns = array();
			foreach ($columns as $column_name => $column_info) {
				$new_columns[$column_name] = $column_info;
				if ('price' === $column_name) {
					//$new_columns['ni_cost_goods'] = __('Profit', 'my-textdomain');
					$new_columns['ni_cost_goods'] = __( 'Ni COG'); 
				}
			}
			return $new_columns;
		 }
		 function in_add_order_ni_cost_goods_column_style(){
		 	$css = '.widefat .column-order_date, .widefat .column-ni_cost_goods { width: 9%; }';
    		wp_add_inline_style( 'woocommerce_admin_styles', $css );
		 }
		 function ni_cost_of_goods_field_value($column, $postid ){
			 $variation_price = array();
			 $variation_id = 0;
			 $ni_cost_goods  = 0;
			 
			 if ($column == "ni_cost_goods")
			 {
			 	   
					$product = wc_get_product($postid );
                	if( $product->is_type( 'variable' ) ){
						$variation_price  = array();
						
						$available_variations = $product->get_available_variations();
						foreach ($available_variations as $key => $value) { 
						 $variation_id = isset($value["variation_id"])?$value["variation_id"]:0;
							//print("<pre>".print_r($value,true)."</pre>");
							
							 $ni_cost_goods_variation = get_post_meta($variation_id , '_ni_cost_goods', true );
							//print("<pre>".print_r($ni_cost_goods_variation,true)."</pre>");
							 if ( strlen($ni_cost_goods_variation)>0){
								$variation_price[] =  $ni_cost_goods_variation;
							 }
						}
						if (count($variation_price)>0){
							$ni_cost_goods =  wc_price(min($variation_price)) . "-"	.  wc_price(max($variation_price)) ;
						}else{
							$ni_cost_goods = wc_price( 0);
						}
					}else if( $product->is_type( 'simple' ) ){
						 $ni_cost_goods =  get_post_meta( $postid, '_ni_cost_goods', true );	
						  $ni_cost_goods = wc_price( $ni_cost_goods );
					}else{
						$ni_cost_goods = wc_price( 0);
					}
					
					echo $ni_cost_goods ;
			 }
		 }
	}
}