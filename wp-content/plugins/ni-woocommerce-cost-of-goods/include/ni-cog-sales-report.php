<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'Ni_COG_Sales_Report' ) ) { 
	class Ni_COG_Sales_Report {
		 public function __construct(){
		 }
		 function page_init(){
		 $input_type="text";
		 $input_type="hidden";
		 ?>
         <div class="ni-sales-report-wrap">
         	
            	
            	<form name="frm_cog_report" id="frm_cog_report">
            	<div class="search_form">
            	<div class="search_form_header">
                	<h3>Ni Cost Of Goods Sales Report</h3>
                </div>
                
                <div class="search_form_body">
                	<div class="search_field">
                        <div class="search_field_label"><label for="select_order">Select Order Period:</label></div>
                        <div class="search_field_input">
                        	<select name="select_order" id="select_order">
                              <option value="today">Today</option>
                              <option value="yesterday">Yesterday</option>
                              <option value="last_7_days">Last 7 days</option>
                              <option value="last_10_days">Last 10 days</option>
                              <option value="last_20_days">Last 20 days</option>
                              <option value="last_30_days">Last 30 days</option>
                              <option value="this_year">This year</option>
                            </select>
                        </div>   
                        <div class="cleafix"></div>                 
                    </div>
                    <div class="search_field">
                        <div class="search_field_label"><label for="order_id">Order ID:</label></div>
                        <div class="search_field_input">
                        	<input type="text" name="order_id" id="order_id" />
                        </div>   
                        <div class="cleafix"></div>                 
                    </div>
                </div>
                <div class="search_form_footer">
                      <div class="search_field_action"><input type="submit" value="Search" class="ni_cog_button" /></div> 
                </div>
                
                <div class="cleafix"></div>
            </div>
            		<input type="<?php echo  $input_type; ?>" name="sub_action" value="ni_cog_sales_report">
             		<input type="<?php echo  $input_type; ?>" name="action" value="ni_cog_action">
               </form>
            
           <div class="ajax_cog_content"></div>
         </div>
        <?php	
		 }
		 function page_ajax(){
			$row = $this->get_cog_sales_product();
			
			do_action("nicog_profit_report_row",$row );
			
			$columns = $this->get_columns();
			?>
            <div style="overflow-x:auto;">
            	<table class="ni-cog-table">
            	<tr>
                	<?php foreach($columns as $key=>$value): ?>
                    	<th><?php echo $value; ?></th>
                    <?php endforeach; ?>
                </tr>
            <?php
			if (count($row)==0 ):
				?>
                <tr>
                	<td colspan="<?php echo count($columns) ?>">No record found</td>
                </tr>
                <?php
				return;
			endif; 
			foreach($row as $key=>$value):
				?>
                <tr>
					<?php
                    foreach($columns as $k=>$v):
                    	
					?>
                    <?php switch($k) :
							case "line_total":
								?>
                            	<td><?php echo  wc_price($value->$k); ?></td>
                            	<?php
								break;
							case "line_subtotal":
								?>
                            	<td><?php echo  wc_price($value->$k); ?></td>
                            	<?php
								break;
							case "ni_cost_goods";
								?>
                            	<td><?php echo  wc_price($value->$k); ?></td>
                            	<?php
								break;
                   			case "sales_price":
								$qty = 0;
								$line_total = 0;
								$qty = isset($value->qty)?$value->qty:0;
								$line_total = isset($value->line_total)?$value->line_total:0;
								?>
                            	<td><?php echo wc_price( ($line_total/	$qty)) ; ?></td>
                            	<?php
					        	break;
							case "ni_profit":
								$ni_cost_goods = 0;
								$sales_price = 0;
								$qty = 0;
								$line_subtotal = 0;
								$ni_profit = 0;
								$ni_cost_goods = isset($value->ni_cost_goods)?$value->ni_cost_goods:0;
								$qty = isset($value->qty)?$value->qty:0;
								$line_total = isset($value->line_total)?$value->line_total:0;
								$sales_price = ($line_total/$qty);
								$ni_profit = (($sales_price-$ni_cost_goods ) *$qty);
								if ($ni_profit < 0){
									?>
									<td style="color:#f90202;font-weight:bold"><?php echo wc_price( $ni_profit) ; ?></td>
                            		<?php	
								}else{
									?>
									<td><?php echo wc_price( $ni_profit) ; ?></td>
                            		<?php
								}
								
					        	break;	
							case "order_status":
								?>
                                  <td> <?php echo ucfirst ( str_replace("wc-","", $value->$k));?> 
                                <?php
								
								break;	
							default:
        						?>
                                 <td><?php echo $value->$k; ?></td>
                                <?php
                   	    endswitch;?>
                   
					<?php
                    endforeach;
                    ?>
                </tr>
                <?php
			endforeach;
			?>
            </table>
            </div>
            
            <?php
			
			//$this->print_data($row);
			
			//echo json_encode($_REQUEST);
			die;	
		 }
		 function get_cog_sales_product(){
			 $row  = array();
			 $row = $this->get_query_data();
		 	 foreach($row as $k => $v){
				  /*Order Data*/
				$order_id =$v->order_id;
				$order_detail = $this->get_order_detail($order_id);
				foreach($order_detail as $dkey => $dvalue){
					$row[$k]->$dkey =$dvalue;
				}
				/*Order Item Detail*/
				$order_item_id = $v->order_item_id;
				$order_item_detail= $this->get_order_item_detail($order_item_id );
				foreach ($order_item_detail as $mKey => $mValue){
					$new_mKey = $str= ltrim ($mValue->meta_key, '_');
					$row[$k]->$new_mKey = $mValue->meta_value;		
				}
			 }
			 return $row;	
		 }
		 function get_query_data($type="DEFAULT")
		 {
			global $wpdb;	
			$today = date_i18n("Y-m-d");
			//$select_order = $this->get_request("select_order","today");
			$select_order = isset($_REQUEST["select_order"])?$_REQUEST["select_order"]:'today';
			$order_id = isset($_REQUEST["order_id"])?$_REQUEST["order_id"]:'0';
			//$select_order = "today";
			
			$query = "SELECT
			posts.ID as order_id
			,posts.post_status as order_status
			,woocommerce_order_items.order_item_id as order_item_id
			, date_format( posts.post_date, '%Y-%m-%d') as order_date 
			,woocommerce_order_items.order_item_name
			, ni_cost_goods.meta_value as ni_cost_goods
			FROM {$wpdb->prefix}posts as posts			
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as woocommerce_order_items ON woocommerce_order_items.order_id=posts.ID 
			
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as ni_cost_goods ON ni_cost_goods.order_item_id=woocommerce_order_items.order_item_id 
			
			WHERE 1=1
			AND posts.post_type ='shop_order' 
			AND woocommerce_order_items.order_item_type ='line_item'
			AND posts.post_status IN ('wc-pending','wc-processing','wc-on-hold', 'wc-completed')
			AND ni_cost_goods.meta_key='_ni_cost_goods' 
			
			";
			if ($order_id !="" and $order_id != 0){
				$query .= " AND	posts.ID IN ($order_id)";
			}
			//$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}'";		
			//AND DATE_ADD(CURDATE(), INTERVAL 1 day)	
			switch ($select_order) {
				case "today":
					$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today}' AND '{$today}'";
				break;
				case "yesterday":
					$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') = date_format( DATE_SUB(CURDATE(), INTERVAL 1 DAY), '%Y-%m-%d')";
				break;
				case "last_7_days":
					$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 7 DAY), '%Y-%m-%d') AND   '{$today}' ";
				break;
				case "last_10_days":
					$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 10 DAY), '%Y-%m-%d') AND   '{$today}' ";
				break;	
				case "last_30_days":
					$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '%Y-%m-%d') AND   '{$today}' ";
				break;	
				case "last_20_days":
					$query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 20 DAY), '%Y-%m-%d') AND   '{$today}' ";
				break;		
				case "this_year":
					$query .= " AND  YEAR(date_format( posts.post_date, '%Y-%m-%d')) = YEAR(date_format(CURDATE(), '%Y-%m-%d'))";			
				break;		
				default:
					$query .= " AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '{$today}' AND '{$today}'";
			}
			$query .= "order by posts.post_date DESC";	
			//AND   date_format( posts.post_date, '%Y-%m-%d') BETWEEN '2014-10-21' AND '2014-10-22'
			if ($type=="ARRAY_A") /*Export*/
			{
			$row = $wpdb->get_results( $query, ARRAY_A );
			}
			if($type=="DEFAULT") /*default*/
			{
			$row = $wpdb->get_results( $query);
			}
			if($type=="COUNT") /*Count only*/	
			{
			$row = $wpdb->get_var($query);		
			}
			//$this->print_data($row);
		 	return $row;	
		 }
		 function get_order_detail($order_id)
		 {
			$order_detail	= get_post_meta($order_id);
			$order_detail_array = array();
			foreach($order_detail as $k => $v)
			{
				$k =substr($k,1);
				$order_detail_array[$k] =$v[0];
			}
			return 	$order_detail_array;
		 }
		 function get_order_item_detail($order_item_id)
		 {
			global $wpdb;
			$sql = "SELECT
					* FROM {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta			
					WHERE order_item_id = {$order_item_id}
					";
					
			$results = $wpdb->get_results($sql);
			return $results;			
		 }
		 function print_data($data){
			print "<pre>";
			print_r($data);
			print "</pre>";
		 }
		 function get_columns(){
			$columns = array(					
				 "order_id"				=>"#ID"
				,"order_date"			=>"Order Date"
				,"billing_first_name"	=>"First Name"
				,"billing_last_name"	=>"Last Name"
				,"order_item_name"		=>"Product Name"
				,"order_status"			=>"Status"
				,"order_currency"		=>"Currency"
				,"payment_method_title" =>"Payment Method"
				,"qty"					=>"Quantity"
				,"sales_price"			=>"Sales Price"
				,"line_total"			=>"Line Total"
				,"ni_cost_goods"		=>"Cost Price"
				,"ni_profit"			=>"Profit"
				//,"line_subtotal"		=>"Subtotal"
				
				//,"line_discount"		=>"Discount"
			  );
			  
			return apply_filters('nicog_profit_report_column', $columns );
		}
	}
}
?>