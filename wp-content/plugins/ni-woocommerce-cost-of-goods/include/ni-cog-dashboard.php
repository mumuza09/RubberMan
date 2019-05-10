<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'Ni_COG_Dashboard' ) ) { 
	class Ni_COG_Dashboard {
		 public function __construct(){
		 	//$this->get_profit_summary();
			//$this->test();
		 }
		 /*Not in user*/
		 function test2(){
		 	global $wpdb;
			$query = " SELECT ";
			$query .= " order_items.order_item_name as product_name ";
			$query .= " ,product_id.meta_value as product_id ";
			$query .= " ,variation_id.meta_value as variation_id ";
			$query .= " ,qty.meta_value as qty ";
			$query .= " , line_total.meta_value ";
			  
			//$query .= " ,SUM(qty.meta_value) as qty ";
			//$query .= " ,SUM(ROUND(line_total.meta_value,2)) as line_total ";
			$query .= " ,ni_cost_goods.meta_value as ni_cost_goods ";
			$query .= " ,posts.ID ";
			$query .= "	FROM {$wpdb->prefix}posts as posts ";			
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID ";
			
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as product_id ON product_id.order_item_id=order_items.order_item_id ";
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as variation_id ON variation_id.order_item_id=order_items.order_item_id ";
			
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as qty ON qty.order_item_id=order_items.order_item_id ";
			
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as line_total ON line_total.order_item_id=order_items.order_item_id ";
			
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as ni_cost_goods ON ni_cost_goods.order_item_id=order_items.order_item_id ";
			
			
			$query .= "	WHERE 1=1 ";
			$query .= " AND posts.post_type ='shop_order' ";
			$query .= " AND order_items.order_item_type ='line_item'";
			$query .= " AND product_id.meta_key ='_product_id'";
			$query .= " AND variation_id.meta_key ='_variation_id'";
			$query .= " AND qty.meta_key ='_qty'";
			$query .= " AND line_total.meta_key ='_line_total'";
			$query .= " AND ni_cost_goods.meta_key ='_ni_cost_goods'";
			$query .= " AND ni_cost_goods.meta_value>0";
			$query .= " AND posts.post_status IN ('wc-pending','wc-processing','wc-on-hold', 'wc-completed') ";
			//$query .= " GROUP BY product_id, variation_id ";
					
			$row = $wpdb->get_results($query);			
			$this->print_data($row);
		 }
		 function page_init(){
		?>
        <style>
        	.ni_cog_feature ul li { font-size:14px; font-weight:bold; color:#0277bd; height:20px; padding:5px;}
			.ni_cog_view_demo_btn {
			  border-style: solid;
			  border-width : 1px 1px 1px 1px;
			  text-decoration : none;
			  padding : 6px;
			  border-color : #0277bd;
			  background:#0277bd;
			  color:#FFF;
			  
			 
			  margin:5px;
			 
			}
        </style>
        <div class="parent_content">
			<div class="content">
            	<div style="height:50px;">
				<div style="border-bottom:2px solid #e7eaec; padding-top:25px"></div>
			</div>
            	<div class="ni-pro-info" style="border:4px solid #0277bd; width:98%">
            	<h3 style="text-align:center; font-size:20px; padding:0px; margin:10px; color:#78909C ">
            	Monitor your sales and grow your online business
                </h3>
				
				<h1 style="text-align:center; color:#0277bd">Buy Ni WooCommerce cost of goods Pro @ $34.00</h1>
				<div style="width:33%; float:left; padding:5px" class="ni_cog_feature">
					<ul>
						<li>Dashboard Summary</li>
                        <li>Daily profit Report</li>
                        <li>Monthly profit Report</li>
						<li>Profit Report</li>
						<li>Stock Report</li>
                        
                   </ul>
				</div>
				<div style="width:33%;padding:5px; float:left" class="ni_cog_feature">
					<ul>
                    	<li>Add Cost of goods for simple product</li>
                        <li>Add Cost of goods for variation product</li>
						<li>Export to CSV</li>
						<li>Stock valuation</li>
						<li>Stock quantity</li>
					</ul>
				</div>
				<div class="ni_cog_feature">
					<ul>
						<li><span style="color:#26A69A">Email at: <a href="mailto:support@naziinfotech.com">support@naziinfotech.com</a></span></li>
						<li><a class="ni_cog_view_demo_btn" href="http://demo.naziinfotech.com?demo_login=woo_cost_of_goods" target="_blank">View Demo</a>  </li>
						<li ><a class="ni_cog_view_demo_btn" href="http://naziinfotech.com/product/ni-woocommerce-cost-of-good-pro/" target="_blank">Buy Now</a>  </li>
						<li>Coupon Code: <span style="color:#26A69A; font-size:16px"><span style="font-size:24px; font-weight:bold;color:#F00">ni10</span> Get 10% OFF </span></li>
                        
					</ul>
				 </div>
				 
			   
				  <div style="clear:both"></div>
				  <div style="width:100%;padding:5px; float:left">
                  <p  style="width:100%;padding:5px; font-size:14px;color:#F00">
                  <strong>
                  We will create new custom report as per custom requirement, if you require more analytic report or require any customization in this report then please feel free to contact with us.
                  </strong>
                  </p>
               
				  <b> For any WordPress or woocommerce customization, queries and support please email at : <strong><a href="mailto:support@naziinfotech.com">support@naziinfotech.com</a></strong></b>
				  </div>
				  <div style="clear:both"></div>
				  
			</div>
            	<div style="height:50px;">
				<div style="border-bottom:2px solid #e7eaec; padding-top:25px"></div>
			</div>
				<div class="box-title"><i class="fa fa-tachometer" aria-hidden="true"></i> <?php _e('Dashboard - Sales Analysis', 'NiWooCommerceSalesReport'); ?> </div>
				<div style="border-bottom:1px solid #2cc185;"></div>
				 <div class="box-data">
					<div class="columns-box"  style="border-top:4px solid #BA68C8">
						<div class="columns-title">Total Sales</div>
						<div>
							<div class="columns-icon" style="color:#BA68C8"><i class="fa fa-cart-plus fa-4x"></i></div>
							<div class="columns-value" style="color:#BA68C8"><?php  echo wc_price( $this->get_total_sales("ALL")); ?></div>	
						</div>
					</div>
					<div class="columns-box" style="border-top:4px solid #BA68C8">
						<div class="columns-title">This Year Sales</div>
						<div>
							<div class="columns-icon"  style="color:#EF6C00"><i class="fa fa-cart-plus fa-4x"></i></div>
							<div class="columns-value"  style="color:#EF6C00"><?php  echo wc_price( $this->get_total_sales("YEAR")); ?></div>	
						</div>
					</div>
					<div class="columns-box"  style="border-top:4px solid #00897B">
						<div class="columns-title">This Month Sales</div>
						<div>
							<div class="columns-icon"  style="color:#00897B"><i class="fa fa-cart-plus fa-4x"></i></div>
							<div class="columns-value" style="color:#00897B"><?php  echo wc_price( $this->get_total_sales("MONTH")); ?></div>	
						</div>
					</div>
					<div class="columns-box" style="border-top:4px solid #039BE5">
						<div class="columns-title">This Week Sales</div>
						<div>
							<div class="columns-icon" style="color:#039BE5"><i class="fa fa-cart-plus fa-4x"></i></div>
							<div class="columns-value" style="color:#039BE5"><?php  echo wc_price( $this->get_total_sales("WEEK")); ?></div>	
						</div>
					</div>
					<div class="columns-box"  style="border-top:4px solid #795548">
						<div class="columns-title">Today Sales</div>
						<div>
							<div class="columns-icon"  style="color:#795548"><i class="fa fa-cart-plus fa-4x"></i></div>
							<div class="columns-value"  style="color:#795548"><?php  echo wc_price( $this->get_total_sales("DAY")); ?></div>	
						</div>
					</div>
					<div style="clear:both"></div>
				</div>
                 <div class="box-data">
					<div class="columns-box" style="border-top:4px solid #BA68C8">
						<div class="columns-title">Total Sales Count</div>
						<div>
							<div class="columns-icon" style="color:#BA68C8"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value" style="color:#BA68C8"><?php echo $this->get_total_sales_count("ALL"); ?></div>	
						</div>
					</div>
					<div class="columns-box"  style="border-top:4px solid #EF6C00">
						<div class="columns-title">This Year Sales Count</div>
						<div>
							<div class="columns-icon" style="color:#EF6C00"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value" style="color:#EF6C00"><?php echo $this->get_total_sales_count("YEAR"); ?></div>	
						</div>
					</div>
					<div class="columns-box"  style="border-top:4px solid #00897B">
						<div class="columns-title">This Month Sales Count</div>
						<div>
							<div class="columns-icon"  style="color:#00897B"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value"  style="color:#00897B"><?php echo $this->get_total_sales_count("MONTH"); ?></div>
						</div>
					</div>
					<div class="columns-box"  style="border-top:4px solid #039BE5">
						<div class="columns-title">This Week Sales Count</div>
						<div>
							<div class="columns-icon" style="color:#039BE5"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value" style="color:#039BE5"><?php echo $this->get_total_sales_count("WEEK"); ?></div>
						</div>
					</div>
					<div class="columns-box"  style="border-top:4px solid #795548">
						<div class="columns-title">Today Sales Count</div>
						<div>
							<div class="columns-icon" style="color:#795548"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value" style="color:#795548"><?php echo $this->get_total_sales_count("DAY"); ?></div>
						</div>
					</div>
					<div style="clear:both"></div>
				</div>
                 
                 <div class="box-title"><i class="fa fa-tachometer" aria-hidden="true"></i> <?php _e('Dashboard - Profit and Loss Analysis', 'NiWooCommerceSalesReport'); ?> </div>
				<div style="border-bottom:1px solid #2cc185;"></div>
			</div>
			<div class="box-data">
					<div class="columns-box"  style="border-top:4px solid #BA68C8">
						<div class="columns-title">Total Profit</div>
						<div>
							<div class="columns-icon" style="color:#BA68C8"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value" style="color:#BA68C8"><?php echo wc_price($this->get_profit_summary("ALL")); ?></div>	
						</div>
					</div>
					<div class="columns-box"  style="border-top:4px solid #EF6C00">
						<div class="columns-title">This Year Profit</div>
						<div>
							<div class="columns-icon" style="color:#EF6C00"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value" style="color:#EF6C00"><?php echo wc_price($this->get_profit_summary("YEAR")); ?></div>	
						</div>
					</div>
					<div class="columns-box" style="border-top:4px solid #00897B">
						<div class="columns-title">This Month Profit</div>
						<div>
							<div class="columns-icon"  style="color:#00897B"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value"  style="color:#00897B"><?php echo wc_price($this->get_profit_summary("MONTH")); ?></div>
						</div>
					</div>
					<div class="columns-box"  style="border-top:4px solid #039BE5">
						<div class="columns-title">This Week Profit</div>
						<div>
							<div class="columns-icon" style="color:#039BE5"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value" style="color:#039BE5"><?php echo wc_price($this->get_profit_summary("WEEK")); ?></div>
						</div>
					</div>
					<div class="columns-box"  style="border-top:4px solid #795548">
						<div class="columns-title">Today Profit</div>
						<div>
							<div class="columns-icon" style="color:#795548"><i class="fa fa-line-chart fa-3x"></i></div>
							<div class="columns-value" style="color:#795548"><?php echo wc_price( $this->get_profit_summary("DAY")); ?></div>
						</div>
					</div>
					<div style="clear:both"></div>
				</div>
            
        </div>
		<?php	
		 }
		 function get_total_sales($period="CUSTOM",$start_date=NULL,$end_date=NULL){
			global $wpdb;	
			$query = "SELECT
					SUM(order_total.meta_value)as 'total_sales'
					FROM {$wpdb->prefix}posts as posts			
					LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID 
					
					WHERE 1=1
					AND posts.post_type ='shop_order' 
					AND order_total.meta_key='_order_total' ";
					
			$query .= " AND posts.post_status IN ('wc-pending','wc-processing','wc-on-hold', 'wc-completed')
						
						";
			if ($period =="DAY"){		
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY) "; 
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') = SUBDATE(date_format(CURDATE(), '%Y-%m-%d'),1) "; 
				$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY) "; 
			}
			if ($period =="WEEK"){		
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 WEEK) "; 
				$query .= "  AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(CURRENT_DATE()) AND 
      WEEK(date_format( posts.post_date, '%Y-%m-%d')) = WEEK(CURRENT_DATE()) ";
			}
			if ($period =="MONTH"){		
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 MONTH) "; 
				$query .= "  AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(CURRENT_DATE()) AND 
      MONTH(date_format( posts.post_date, '%Y-%m-%d')) = MONTH(CURRENT_DATE()) ";
			}
			if ($period =="YEAR"){		
				//$query .= " AND YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(NOW(), '%Y-%m-%d')) "; 
				$query .= " AND YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(NOW(), '%Y-%m-%d')) "; 
			}
			
			
			//echo $query;		
					
			//$query .=" AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
					
			$results = $wpdb->get_var($query);
			$results = isset($results) ? $results : "0";
			return $results;
		}
		function get_total_sales_count($period="CUSTOM",$start_date=NULL,$end_date=NULL){
			global $wpdb;	
			$query = "SELECT
					count(order_total.meta_value)as 'sales_count'
					FROM {$wpdb->prefix}posts as posts			
					LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID 
					
					WHERE  1 = 1
					AND posts.post_type ='shop_order' 
					AND order_total.meta_key='_order_total' ";
					//if ($start_date!=NULL && $end_date!=NULL)
					//$query .=" AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";
			$query .= " AND posts.post_status IN ('wc-pending','wc-processing','wc-on-hold', 'wc-completed') ";
			
			if ($period =="DAY"){		
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY) "; 
			//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') = SUBDATE(date_format(CURDATE(), '%Y-%m-%d'),1) "; 
			$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY) "; 
			}
			if ($period =="WEEK"){		
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 WEEK) "; 
				$query .= "  AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(CURRENT_DATE()) AND 
      WEEK(date_format( posts.post_date, '%Y-%m-%d')) = WEEK(CURRENT_DATE()) ";
			}
			if ($period =="MONTH"){		
			//	$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 MONTH) "; 
			
			$query .= "  AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(CURRENT_DATE()) AND 
      MONTH(date_format( posts.post_date, '%Y-%m-%d')) = MONTH(CURRENT_DATE()) ";
			
			}
			if ($period =="YEAR"){		
				//$query .= " AND YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(NOW(), '%Y-%m-%d')) "; 
				$query .= " AND YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(NOW(), '%Y-%m-%d')) "; 
			}
			//echo $query;
			$results = $wpdb->get_var($query);	
			$results = isset($results) ? $results : "0";	
			return $results;
		}
		function get_profit_summary($period="CUSTOM",$start_date=NULL,$end_date=NULL){
			global $wpdb;
			$query = "";
			$query = "SELECT
			posts.ID as order_id
			,posts.post_status as order_status
			,woocommerce_order_items.order_item_id as order_item_id
			, date_format( posts.post_date, '%Y-%m-%d') as order_date 
			,woocommerce_order_items.order_item_name
			, ni_cost_goods.meta_value as ni_cost_goods
			, line_total.meta_value as line_total
			, qty.meta_value as qty
			FROM {$wpdb->prefix}posts as posts			
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as woocommerce_order_items ON woocommerce_order_items.order_id=posts.ID  ";
			
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as ni_cost_goods ON ni_cost_goods.order_item_id=woocommerce_order_items.order_item_id  ";
			
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as line_total ON 	line_total.order_item_id=woocommerce_order_items.order_item_id  ";
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as qty ON 	qty.order_item_id=woocommerce_order_items.order_item_id  ";
			
		$query .= " WHERE 1=1
			AND posts.post_type ='shop_order' 
			AND woocommerce_order_items.order_item_type ='line_item'
			AND posts.post_status IN ('wc-pending','wc-processing','wc-on-hold', 'wc-completed')
			AND ni_cost_goods.meta_key='_ni_cost_goods' 
			AND line_total.meta_key='_line_total' 
			AND qty.meta_key='_qty' 
			
			";
			if ($period =="DAY"){		
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY) "; 
			//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') = SUBDATE(date_format(CURDATE(), '%Y-%m-%d'),1) "; 
			$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 DAY) "; 
			}
			if ($period =="WEEK"){		
				//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 WEEK) "; 
				$query .= "  AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(CURRENT_DATE()) AND 
      WEEK(date_format( posts.post_date, '%Y-%m-%d')) = WEEK(CURRENT_DATE()) ";
			}
			if ($period =="MONTH"){		
			//	$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') > DATE_SUB(date_format(NOW(), '%Y-%m-%d'), INTERVAL 1 MONTH) "; 
			
			$query .= "  AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(CURRENT_DATE()) AND 
      MONTH(date_format( posts.post_date, '%Y-%m-%d')) = MONTH(CURRENT_DATE()) ";
			
			}
			if ($period =="YEAR"){		
				//$query .= " AND YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(NOW(), '%Y-%m-%d')) "; 
				$query .= " AND YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(NOW(), '%Y-%m-%d')) "; 
			}
			//echo $query;
			$row = $wpdb->get_results( $query);
			$total_profit = 0;
			$sales_price = 0;
			$qty= 0;
			$line_total= 0;
			$cost_goods= 0;
			$cost_total= 0;
			
			foreach($row as $key=>$value){
				$qty= 0;
				$line_total  = 0;
				$cost_good =0;
				
				$qty = isset($value->qty)?$value->qty:0;
				$line_total = isset($value->line_total)?$value->line_total:0;
			
				$cost_goods = isset($value->ni_cost_goods)?$value->ni_cost_goods:0;
				
				$cost_goods = is_numeric($cost_goods) ?$cost_goods:0;
				$qty = is_numeric($qty) ?$qty:0;
				
				$cost_total = ($cost_goods * $qty  ); /*Total Cost*/
				$total_profit += ($line_total-$cost_total);
			}
			//$this->print_data($row);
			return isset($total_profit)?$total_profit:0;
			
		}
		function print_data($data){
			print "<pre>";
			print_r($data);
			print "</pre>";
		 }
	}
}