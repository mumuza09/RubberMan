<?php
/**
 * WC_Pay_To_Upload class.
 * 
 * @extends Airy_Framework
 */
class WC_Pay_To_Upload extends Airy_Framework {
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	 
	function __construct() {
		
		$this->shortname = 'wc_pay_to_upload';
		$this->plugin_name = __('Pay to Upload', 'wc_pay_to_upload' );
		$this->desc = __('Let WC customers pay to upload files when purchasing specific products.', 'wc_pay_to_upload' );
		$this->version = '2.0.4';
		$this->menu_location = 'woocommerce';
		$uploads = wp_upload_dir();
		$this->fileuploaddir = 	trailingslashit( $uploads['basedir'] ) . 'wc-pay-to-upload';
		$this->fileurl = trailingslashit( $uploads['baseurl'] ) . 'wc-pay-to-upload';
		$this->defaultLimit = (get_option('wc_ptu_default_limit') ? get_option('wc_ptu_default_limit') : 1);
		$this->defaultPath = (get_option('wc_ptu_uploads_path') ? get_option('wc_ptu_uploads_path') : $this->fileuploaddir);
		$this->defaultFileTypes = (get_option('wc_ptu_file_types') ? get_option('wc_ptu_file_types') : 'jpg,png,gif');
		$this->defaultListType = (get_option('wc_ptu_list_type') ? get_option('wc_ptu_list_type') : 'all');
		$this->defaultStatus = (get_option('wc_ptu_order_statuses') ? get_option('wc_ptu_order_statuses') : 'wc-completed');
		$this->fields = array(
			array(
				'name'		=> 'wc_ptu_default_limit',
				'title'		=> __( 'Default Upload Limit', 'wc_pay_to_upload' ),
				'type'		=> 'text',
				'desc'		=> __( 'Default upload limit when activating feature for products.', 'wc_pay_to_upload' ),
				'default'	=> $this->defaultLimit,
			),
			array(
				'name'		=> 'wc_ptu_uploads_path',
				'title'		=> __( 'Uploads Path', 'wc_pay_to_upload' ),
				'type'		=> 'text',
				'desc'		=> __( 'Uploads path for all new uploads, subfolders with the order ID name will be created within this.', 'wc_pay_to_upload' ),
				'default'	=> $this->defaultPath,
			),
			array(
				'name'		=> 'wc_ptu_list_type',
				'title'		=> __( 'Allow or Disallow File Types', 'wc_pay_to_upload' ),
				'type'		=> 'select',
				'desc'		=> __( 'The file types that are allowed/disallowed by your selection above.', 'wc_pay_to_upload' ),
				'values'	=> array(
					'all'	=> __( 'Allow All', 'wc_pay_to_upload' ),
					'white'	=> __( 'Whitelist', 'wc_pay_to_upload' ),
					'black'	=> __( 'Blacklist', 'wc_pay_to_upload' ),
				),
				'default'	=> $this->defaultListType
			),
			array(
				'name'		=> 'wc_ptu_file_types',
				'title'		=> __('File Types', 'wc_pay_to_upload' ),
				'type'		=> 'text',
				'desc'		=> __('The file types that are allowed/disallowed by your selection above, separate file types by commas.', 'wc_pay_to_upload' ),
				'default'	=> $this->defaultFileTypes
				),
		);
		
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
		add_action( 'woocommerce_init', array( &$this, 'woocommerce_init' ) );
		add_action( 'save_post', array( &$this, 'save_meta_box' ) );
		add_action( 'woocommerce_view_order', array( &$this, 'uploader' ) );
	}
	
	/**
	 * add_meta_boxes function.
	 * 
	 * @access public
	 * @return void
	 */
	function add_meta_boxes() {
		add_meta_box( 'wc_ptu_enable', __( 'Pay to Upload', 'wc_pay_to_upload' ), array( &$this, 'product_upload_options'), 'product', 'side' );
		add_meta_box( 'wc_ptu_files', __( 'Uploaded Files', 'wc_pay_to_upload' ), array( &$this, 'order_uploaded_files'), 'shop_order', 'side' );
	}
	
	/**
	 * woocommerce_init function.
	 * 
	 * @access public
	 * @return void
	 */
	function woocommerce_init() {
			
		$order_status = wc_get_order_statuses();

		$statuses = $order_status;

		$values = array();

		foreach( $statuses as $status => $key ) {

			$values[ $status ] = $key;

		}



		$this->fields[] = array(
			'name'		=> 'wc_ptu_order_statuses',
			'title'		=> __('Required Status(es)', 'wc_pay_to_upload' ),
			'type'		=> 'select',
			'desc'		=> __('The required order status to allow customers to upload files.', 'wc_pay_to_upload' ),
			'values'	=> $values,
			'default'	=> $this->defaultStatus,
		);
		parent::__construct();
	}
	
	/**
	 * order_uploaded_files function.
	 * 
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	function order_uploaded_files( $post ) {
		$order = new WC_Order( $post->ID );
		
		$items = $order->get_items();
		$limit = $this->check_for_uploadables( $post->ID );
		echo '<table style="border-collapse:collapse;" border="1" cellpadding="5" cellspacing="0">';
		foreach( $items as $item ) {
						echo '<tr><th colspan="3">Uploaded Files for '.$item['name'].'</th></tr>';
								echo '<tr><th>S.No.</th><th>File Name</th><th>Extra Info</th></tr>';

				$limit = (int) get_post_meta( $item['product_id'], '_wc_ptu_limit', true ); //Changed $item['item_id'] to $item['product_id'] for wordpress version 3.5.1 Ashok G
				if( get_post_meta( $item['product_id'], '_wc_ptu_enable', true ) == 1 && $limit > 0 ) {
					$limits = $limit;
				}
		for ($i = 1; $i <= $limit; $i++) {
			$item_id = $item['product_id'].$i;
			$url =  get_post_meta( $post->ID, '_wc_uploaded_file_path_' . $item_id, true );
			$name = get_post_meta( $post->ID, '_wc_uploaded_file_name_' . $item_id, true );
			$ef = get_post_meta( $post->ID, '_wc_uploaded_file_name_extra_info_' . $item_id, true );
			if( !empty( $url ) && !empty( $name ) ) {
				printf( __('<tr><td>%s</td><td> <a href="%s" target="_blank">%s</a></td><td>%s</td></tr>', 'wc_pay_to_upload'),$i,  $url, $name,$ef);
			} else {
				printf( __('<tr><td> %s</td><td> has not been uploaded.</td><td></td></tr>', 'wc_pay_to_upload'), $i );
			}
			
		}
	}
	echo '</table>';
	}
	
	/**
	 * product_upload_options function.
	 * 
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	function product_upload_options( $post ) {
		wp_nonce_field( 'wc_ptu_nonce', 'wc_ptu_nonce' );
		echo '<p>';
			echo '<label for="_wc_ptu_enable" style="float:left; width:50px;">' . __('Enable', 'wc_pay_to_upload' ) . '</label>';
			echo '<input type="hidden" name="_wc_ptu_enable" value="0" />';
			/* Individual product checks Auto enable primarily. Ashok G. 31/07/2016 */ 
			$upStats = get_post_meta( $post->ID, '_wc_ptu_enable', true );
			if($upStats !=0 || $upStats =='' || $upStats == Null) { $chked = 'checked="true"'; }			
			echo '<input type="checkbox" id="_wc_ptu_enable" '.$chked.' class="checkbox" name="_wc_ptu_enable" value="1" ' . checked( get_post_meta( $post->ID, '_wc_ptu_enable', true ), 1, false ) . ' />';
		echo '</p>';
		echo '<p>';
			$value = get_post_meta( $post->ID, '_wc_ptu_limit', true );
			$value = ( !empty( $value ) ) ? $value : $this->defaultLimit;
			echo '<label for="_wc_ptu_limit" style="float:left; width:50px;">' . __('Limit', 'wc_pay_to_upload' ) . '</label>';
			echo '<input type="text" id="_wc_ptu_limit" class="short" name="_wc_ptu_limit" value="' . $value . '" />';
		echo '</p>';
	}
	
	/**
	 * save_meta_box function.
	 * 
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	function save_meta_box( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( !isset( $_POST['wc_ptu_nonce'] ) || !wp_verify_nonce( $_POST['wc_ptu_nonce'], 'wc_ptu_nonce' ) ) return;
		update_post_meta( $post_id, '_wc_ptu_enable', (int) $_POST['_wc_ptu_enable'] );
		update_post_meta( $post_id, '_wc_ptu_limit', (int) $_POST['_wc_ptu_limit'] );		
	}
	
	/**
	 * check_for_uploadables function.
	 * 
	 * @access public
	 * @param mixed $order_id
	 * @return void
	 */
	function check_for_uploadables( $order_id ) {
	//Changed the Previous parameters to existing parameters for wordpress Version 3.5.1 Ashok G
	global $woocommerce;
	$order = new WC_Order( $order_id );
	$items = get_post_meta( $order_id ,'_order_items', true);
	$new_items = $order->get_items();
		


$limits = 0;		
		if( is_array( $new_items ) ) {
			foreach( $new_items as $item ) {
				$limit = (int) get_post_meta( $item['product_id'], '_wc_ptu_limit', true ); //Changed $item['item_id'] to $item['product_id'] for wordpress version 3.5.1 Ashok G
				if( get_post_meta( $item['product_id'], '_wc_ptu_enable', true ) == 1 && $limit > 0 ) {
					$limits += $limit;
				}
			}
		} else {
			echo wpautop( __( 'Sorry, no files have been uploaded yet.', 'wc_pay_to_upload' ) );
			
		}
		return $limits;
		
	}
	
	/**
	 * uploader function.
	 * 
	 * @access public
	 * @param mixed $order_id
	 * @return void
	 */
	function uploader( $order_id ) {
		$x = new WC_Order(  $order_id );
		if(isset($_GET['delete']) && isset($_GET['item']))
		{
			echo $this->delete_file($order_id,$_GET['item']);
			
			
		}
		if(!defined("PHP_EOL")){define("PHP_EOL", strtoupper(substr(PHP_OS,0,3) == "WIN") ? "\r\n" : "\n");}
		$order = new WC_Order( $order_id );
		 $items = $order->get_items();
		$limits = $this->check_for_uploadables( $order_id );
		
		
 	 	$admin_email = get_option('admin_email'); 
		echo '<h2>' . __( 'Upload Files', 'wc_pay_to_upload' ) . '</h2>';
		global $current_user;
     	 wp_get_current_user();
		 $from =  $current_user->user_email;
		 $to = $admin_email;
		if( isset( $_FILES ) ) {
			$path = trailingslashit( trailingslashit( $this->defaultPath ) . $order_id );
			foreach( $_FILES as $key => $file ) {
				if( empty( $file['name'] ) ) continue;
				wp_mkdir_p( $path );
				$filepath = $path . $file['name'];
				$ext = strtolower( pathinfo( $filepath, PATHINFO_EXTENSION ) );
				$types = explode( ',', $this->defaultFileTypes );
				foreach( $types as $k => $v ) { $types[$k] = strtolower( trim( $v ) ); }
				switch( $this->defaultListType ) {
					case 'all':
						$allow = true;
						break;
					case 'white':
						if( in_array( $ext, $types ) ) $allow = true;
						else $allow = false;
						break;
					case 'black':
						if( in_array( $ext, $types ) ) $allow = false;
						else $allow = true;
						break;
				}
				if( $allow == true ) {
					$headers = '';
					$dburl = $this->fileurl.'/'.$order_id.'/'.$file['name'];
					if( copy( $file['tmp_name'], $filepath ) ) {
						
						$subject = 'File Upload Notification OrderId - '.$order_id;
						$message = "Dear Admin, <br><br>User Has uploaded files for order - ".$order_id."<br><br> Please logon to your Admin Panel to download the files.";
						$headers .= 'From:'. $from.PHP_EOL;
						$headers .= 'Content-type: text/html; charset=iso-8859-1'.PHP_EOL;
						if (wp_mail( $to, $subject, $message, $headers ))
						{
						echo '<p class="success">' . __( 'Your file(s) were uploaded successfully.', 'wc_pay_to_upload') . '</p>';
							//echo 'Mail Success';
						}
						else
						{
							//echo 'Mail Error';
						}
						
						update_post_meta( $order_id, '_wc_uploaded_file_name_' . $key, $file['name'] );
						update_post_meta( $order_id, '_wc_uploaded_file_path_' . $key, $dburl );
						update_post_meta( $order_id, '_wc_uploaded_file_name_extra_info_' . $key, sanitize_text_field($_POST['wc_upload_extra_info_'.$key]) );
					} else {
						echo '<p class="error">' . __( 'There was an error in uploading your file(s).', 'wc_pay_to_upload') . '</p>';
					}
				} else {
					echo '<p class="error">' . sprintf( __( 'There %s filetype is not allowed.', 'wc_pay_to_upload'), $ext ) . '</p>';
				}
			}
		}
		
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$upload_mb = min($max_upload, $max_post, $memory_limit);
		
		echo '<form enctype="multipart/form-data" action="" method="POST">';
			$upload = false;
			if( is_array( $items ) ) {
			foreach( $items as $item ) {
				/* Individual product checks,whether item ie enabled for upload. Ashok G. 31/07/2016 */
				if(get_post_meta( $item['product_id'], '_wc_ptu_enable', true ) == 1)
				{
				echo '<h3> Upload Files for '.$item['name'].'</h3>';
				$limit = (int) get_post_meta( $item['product_id'], '_wc_ptu_limit', true ); //Changed $item['item_id'] to $item['product_id'] for wordpress version 3.5.1 Ashok G
				if( get_post_meta( $item['product_id'], '_wc_ptu_enable', true ) == 1 && $limit > 0 ) {
					$limits = $limit;
				}
			echo '<table border="1" style="border-collapse:collapse;" cellpadding="5"><tr><th>S.No</th><th>File</th><th>Extra Info</th><tr>';	
			for ($i = 1; $i <= $limits; $i++) {
				echo '<tr><td>';
				$item_id_upload = $item['product_id'].$i;
				$url_upload = get_post_meta( $order_id, '_wc_uploaded_file_path_' . $item_id_upload, true );
				$name = get_post_meta( $order_id, '_wc_uploaded_file_name_' . $item_id_upload, true );
				echo '<label for="' . $i . '">File ' . $i . ': </label></td>';
				$file_name_append = $item['product_id'].$i;
				$name = get_post_meta( $order_id, '_wc_uploaded_file_name_' . $file_name_append, true );
				if( empty( $name ) ) {
					echo '<td><input type="file" name="' . $file_name_append . '" /></td>';
					echo '<td><input type="text" name="wc_upload_extra_info_'.$file_name_append.'" placeholder="'.__('Additional Information','wc_pay_to_upload').'"/></td></tr>';
					$upload = true;
				} else {
					echo '<td>'.$this->file_type_icons($url_upload,$order_id,$name,$item_id_upload,$x->post_status).'</td>';
					echo '<td>'.$this->wc_ptu_extra_info($order_id,'_wc_uploaded_file_name_extra_info_'.$file_name_append).'</td>';
				}
				echo '<br/>';
			}
			}
			}
		}
			
			if( $upload && $x->post_status == $this->defaultStatus) {
				echo '<tr><td colspan="3"><input type="submit" class="button" value="' . __( 'Upload', 'wc_pay_to_upload' ) . '" /><br/>';
				echo '<p>' . sprintf( __( 'Max upload size: %s', 'wc_pay_to_upload' ), $upload_mb ) . 'MB</p></td></tr></table>';
			}
			else
			{
				echo $upload;
				echo '<p>' . sprintf( __( 'The order status has been changed so you cannot upload files contact Site Admin.', 'wc_pay_to_upload' )). ' </p>';
			}
		echo '</form>';
	
	}
	/*
	Extra Info display
	*/	
	function wc_ptu_extra_info($orderid,$exFldname)
	{
		$ef = get_post_meta($orderid,$exFldname,true);
		return $ef;
	}
	/*
	File type icons display
	*/
	function file_type_icons($file,$order,$fname,$srl,$stats)
	{
	
		$image =  array('gif','png' ,'jpg','tiff','bmp','jpeg','svg');
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if(in_array($ext,$image) ) {
		$iconURL = $file;
		$fileSize = 'width="250"';
	
		}
		else
		{
			switch($ext){
				 case "csv":
				 $icon = 'csv.png';
				 break;
				 
				 case "doc":
				 $icon = 'word.png';
				 break;
				 
				 case "docx":
				 $icon = 'word.png';
				 break;
				 
				 case "xls":
				 $icon = 'excel.png';
				 break;
				 
				 case "xlsx":
				 $icon = 'excel.png';
				 break;
				 
				 case "pdf":
				 $icon = 'pdf.png';
				 break;

				 case "txt":
				 $icon = 'text.png';
				 break;
				 
				 default: 
				  $icon = 'text.png';
				  break;

		 }
			$iconURL = plugin_dir_url( __FILE__ ).'../icons/'.$icon;
			$fileSize = '';
		 
		}
		
		if(( $stats == $this->defaultStatus )){
		$fileout = '<a href="'.$file.'"><img src="'.$iconURL.'" alt="'.$fname.'" '.$fileSize.' title="'.$fname.'"></a>&nbsp;&nbsp;&nbsp;<a  onclick="return confirm(\'Are you sure that you want delete '.$fname.' ?\');" href="?delete='.$order.'&item='.$srl.'">X</a>';
		}
		else{
				$fileout = '<a href="'.$file.'"><img src="'.$iconURL.'" alt="'.$fname.'" '.$fileSize.' title="'.$fname.'"></a>';
		}
		return $fileout;
	}
	
	/*
		Deleting the uploaded file
		*/
		
	
	function delete_file($oid,$itm)
	{
				
		$file = get_post_meta($oid,'_wc_uploaded_file_name_'.$itm,true);
		if($file !='')
		{
		
		$fileurl = explode($_SERVER['HTTP_HOST'],$this->fileurl);
		$dir = $_SERVER['DOCUMENT_ROOT'].$fileurl[1].'/'.$oid.'/'.$file;
		unlink($dir);
		delete_post_meta($oid,'_wc_uploaded_file_path_'.$itm);
		delete_post_meta($oid, '_wc_uploaded_file_name_' .$itm);
		delete_post_meta($oid, '_wc_uploaded_file_name_extra_info_' .$itm);
		
		$url = explode('?',$_SERVER['REQUEST_URI']);	
		echo "<script>location.href='".$url[0]."'</script>";
		
		}
		else
		{
		
		
		}
	}
}
