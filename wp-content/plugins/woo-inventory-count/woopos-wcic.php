<?php 
/**
 * Plugin Name: WooCommerce POS Inventory Count
 * Author: WooPOS.com
 * Author URI: http://www.woopos.com/
 * Plugin URI: http://woopos.com/woocommerce-inventory-count-plugin/
 * Description: Physical Inventory Count for WooCommerce POS by scanning QR code, import and update counted quantities from inventory scanner.
 * Version: 19.01.26
 */

if (!defined('ABSPATH')) exit;

/* Initializations */
$_wcic_woopos_log = array('success' => null, 'error' => null);
$_wcic_woopos_prods_count = 0;

/** Functions **/

// Show main page
function wcic_woopos_show_page() {  global $_wcic_woopos_log, $_wcic_woopos_prods_count; wcic_woopos_enqueue(); ?>
    <div id="wcic_woopos_main" align="left">
      <h1 style="letter-spacing:0.1em"><a href="http://woopos.com/" target="_blank" rel="nofollow"><img align="middle" src="<?php echo plugins_url("assets/woopos-logo-150px.png", __FILE__); ?>" style="height:40px" /></a> WooCommerce Inventory Count</h1>
      <form method="post">
        <input type="hidden" name="method" value="categories">
        <div id="wcic_woopos_categories" class="wcic_woopos_section" style="float:left;margin-bottom:20px;margin-right:20px">
            <h3>Select categories to count</h3>
            <hr noshade>
                <?php wcic_woopos_show_categories(); ?>
            <hr noshade>
            <input type="checkbox" onClick="woopos_save_warn()" id="cat_select_all" class="woopos_btn" style="" /> Select - All / None
            <script>
                jQuery("#cat_select_all").click(function() {
                    var csa_chk = jQuery("#cat_select_all").attr('checked');
                    if (csa_chk == 'checked') {
                        jQuery('.woopos_chk_box').prop('checked', true);
                    } else {
                        jQuery('.woopos_chk_box').prop('checked', false);
                    }
                }
            );
            </script>
            <br /><Br />
            <?php
                $prod_count = count(wcic_woopos_get_products());
                if ($prod_count >= 1) {
                    echo "<div align='center' style='color:black;padding:5px;border-radius:5px;background-color:green;color:white;'>Products in selection → <b>$prod_count</b></div>";
                } else {
                    echo "<div align='center' style='color:red;padding:5px;border-radius:5px;background-color:red;color:white;font-weight:bold'>No products selected!</div><script>var no_product_found = true;</script>";
                }
            ?>
            <br />
            <div align="center">
                <input type="submit" class="woopos_btn" style="font-weight:bold;margin-right:5px" value=" SAVE ">
            </div>
            <div align="center" id="woopos_save_warn">
            </div>
        </div>
        </form>
        <div style="float:left">
         <?php if (count($_wcic_woopos_log['success']) > 0 && count($_wcic_woopos_log['error']) == 0) { ?>
            <div style="border-top: 2px solid #00ff00;" class="wcic_woopos_section">
                <h3>Success</h3>
                <?php foreach ($_wcic_woopos_log['success'] as $msg) {
                    echo $msg . "<br />"; 
                } ?>
            </div><br /> 
        <?php }  ?>
        <?php if (is_array($_wcic_woopos_log['error'])) { ?>
            <div style="border-top: 2px solid #ff0000;" class="wcic_woopos_section">
                <h3>Errors</h3>
                <?php foreach ($_wcic_woopos_log['error'] as $msg) {
                    echo $msg . "<br />"; 
                } ?>
            </div><br />
        <?php }  ?>
            <div id="wcic_woopos_export" class="wcic_woopos_section">
             <h1>Create SKU List</h1>
             <hr noshade>
             <h4>You can export product SKU list and load it to WooPOS Inventory Count Android App</h4>
             <a target="_blank" href="https://support.woopos.com/knowledge-base/woocommerce-inventory-count-plugin/">Inventory Count Guide</a>
             <br />
             <form method="post">
                <input type="hidden" name="method" value="export">
                <div align="center">
                    <?php wcic_woopos_step2_chk('<input id="dl_sku_file" type="submit" value=" Download SKU List File " class="woopos_btn">'); ?>
                    <?php wcic_woopos_step2_chk('<br /><br /><input type="button" id="gen_qr_code_btn" onClick="javascript:this.form.method.value=\'gen_qr_code\';this.form.submit();" value=" Generate QR Code For App " class="woopos_btn"><script>if (typeof no_product_found !== "undefined") { document.getElementById("dl_sku_file").disabled = true; document.getElementById("gen_qr_code_btn").disabled = true; } </script>', false); ?>
                </div>
             </form>
            </div>
                <br />
            <div id="wcic_woopos_import" class="wcic_woopos_section">
                <h1>Upload Counted List</h1>
                <hr noshade>
                <h4>Counted list should be a CSV or TXT file Including SKU and Quantity, without header.</h4>
                <form method="post" enctype="multipart/form-data" id="ucl_form">
                    <input type="hidden" name="method" value="import" id='ucl_form_method'>
                    <input type="hidden" name="ucl_remote_file" value="" id="ucl_remote_file">
                    <input type="hidden" name="ucl_remote_file_size" value="" id="ucl_remote_file_size">
                     <i>By</i> <b>File Upload:</b> <br />
                        <hr noshade>
                    <div style="border-top: solid 2px #d1e8ff;border-bottom: solid 2px #d1e8ff;"><span style="background-color:#d1e8ff;padding:8px;color:black;font-weight:bold">Upload CSV:&nbsp;&nbsp;&nbsp;</span><span>&nbsp;&nbsp;
                        <input type="file" name="counted_list" <?php echo (get_option("woopos_csv_upload_cache", false) == false) ? 'required' : null; ?> accept=".csv,.txt"> 
                        <?php if (get_option("woopos_csv_upload_cache", false) != false) { ?>
                            <span onClick="javascript:document.getElementById('ucl_form_method').value='clear';document.getElementById('ucl_form').submit();" title='Click to Clear Cache of this uploaded file.' style='cursor:pointer;color:black;font-weight:bold;border:1px solid #4a5700;background-color:#6ce63c;padding:2px;border-radius:5px'>&nbsp; <?php echo get_option('woopos_csv_filename'); ?> &nbsp;&#x2715;&nbsp;</span>
                        <?php } ?>
                    </span></div><br />
                     <i>By</i> <b>QR Code Scan:</b> <br />
                        <?php $upload_filename = time() . rand(99999,9999999); ?>
                        <hr noshade>
                        <input type="button" onClick="javascript:jQuery('#upload_by_qrcode').show()" value=" Show QR Code  " class="woopos_btn" id="up_by_qr"><br /><br />
                        <div id="upload_by_qrcode">
                            <img class="qr_code" align="top" src="http://chart.apis.google.com/chart?cht=qr&chs=200x200&chl=[[SCANLIST]]<?php echo $upload_filename; ?>.TXT">
                            <div style="float:left"><br /><br /><br /><br /><br />Please scan the code with the APP<br />to auto-upload<br /><br />
                            <div id="qr_upload_chk">Scan to begin.</div>
                            <div id="qr_upload_chk_hide" style="display:none">Scan to begin.</div>
                            <script>
                                function qr_check() {
                                    jQuery("#qr_upload_chk_hide").load("<?php echo $_SERVER['REQUEST_URI'] . "&method=ready_check&filename=SCANLIST_{$upload_filename}.TXT"; ?>");
                                       $remote_val = jQuery('#qr_upload_chk_hide').text();
                                        if ($remote_val > 0) {
                                            clearInterval(qr_check);
                                            jQuery('#qr_upload_chk').css('color', 'green');
                                            jQuery('#qr_upload_chk').text("AUTO UPLOADING...");
                                            // Auto upload now...
                                            jQuery('#ucl_remote_file').attr("value", 'SCANLIST_<?php echo $upload_filename; ?>.TXT');
                                            jQuery('#ucl_remote_file_size').attr("value", $remote_val);
                                            setTimeout("jQuery('#ucl_form').submit()", 1500);
                                        } else if ($remote_val == 'Waiting...') {
                                            jQuery('#qr_upload_chk').css('color', 'orange');
                                            jQuery('#qr_upload_chk').text("Waiting...");
                                        } else {
                                         
                                        }
                                }
                                jQuery("#up_by_qr").click( function() {
                                    var qr_check = setInterval("qr_check()", 3000);
                                }); 
                            </script>
                            </div>
                        </div>
                    <br />
                    <input type="checkbox" name="set_q_zero" id="set_q_zero" value="Y">
                    <label for="set_q_zero" title="Any SKUs not found in the uploaded list will have quantity set to 0, and stock status set according to backorder setting."><b>Set Quantity to Zero</b> (in selected categories if the products are not in Counted List)</label>
                    <br />
                    <input type="checkbox" checked name="dry_run" id="dry_run" value="Y">
                    <label for="dry_run" title="Dry Run enabled will simulate only, no updates to the database will be actually made."><b>Dry Run</b> (Uncheck to update stock database)</label>
                    <br /><br />          
                    <div align="center">
                        <input type="submit" value=" Update Stock Quantities " class="woopos_btn"><br /><br />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function woopos_save_warn() {
            document.getElementById("woopos_save_warn").innerHTML = "&#9650;<br /><div align='center' style='color:red;padding:5px;border-radius:5px;background-color:red;color:white;font-weight:bold'>Click SAVE to update product selection.</div>";
        }
    </script>
<?php }


// Show WooCommerce category tree
function wcic_woopos_cat_box($elements) {
    echo '<ul class="woopos_ul">';
    $selected_cats = get_option("woopos_selected_cats");
    foreach ($elements as $element) {
        $selected = @array_key_exists($element['id'], $selected_cats) ? 'checked' : null;
        echo '<li>' . '<input onClick="woopos_save_warn()" type="checkbox" class="woopos_chk_box" name="selected_cats[' . $element['id'] . ']" value="Y"' . $selected . '>' . $element['name'];
        if (!empty($element['children'])) {
            wcic_woopos_cat_box($element['children']);
        }
        echo '</li>';
    }
    echo '</ul>';
}

// Generate QR Code
function wcic_woopos_generate_qr_code($wooprods) {
    $finish = $tdata = $data = "";
    $block_bytes = 900; // 4096
    $filename =  time() . rand(99999,9999999);
    foreach ($wooprods as $p) {
        $data .= $p['sku'] . "," . $p['name'] . " QOH:" . $p['q'] . "," . $p['p'] . "\r\n";
    }
    $data = rtrim($data);
    $data_length = strlen($data);
    for ($bn = 0; $finish != true ; $bn++) {
        $block_data = substr($data, $bn * $block_bytes, $block_bytes);
        if (strlen($block_data) == 0) {
            $finish = true;
        } else {
            if (strlen($block_data) < $block_bytes) {
               $block_data = str_pad($block_data, $block_bytes);
            }
            $block_data = base64_encode($block_data);
            $request_url = "http://magentopos.com/POSLSService.asmx/SaveFileFromBase64?BytesToBase64={$block_data}&FileName=SKULIST_{$filename}.TXT&BlockNumber={$bn}&BlockSize={$block_bytes}";
            $resp = file_get_contents($request_url);
        }
    }
    wcic_woopos_log("Scan QR Code to download SKU list file: <br /><img class='qr_code' src='http://chart.apis.google.com/chart?cht=qr&chs=200x200&chl=[[SKULIST]]{$filename}.TXT'>", "success");
    return $tdata;
}

// Get the data from APP server.
function wcic_woopos_download_server_data($file_name, $bytes) {
    $blocks = round($bytes / 900);
    for ($i = 0; $i <= $blocks; $i++) {
        $down_link = "http://magentopos.com/POSLSService.asmx/GetFileBase64?FileName=" . $file_name . "&BlockNumber={$i}&BlockSize=900";
        $b64b = strip_tags(file_get_contents($down_link));
        if ($b64b != "") {
            $fd .= base64_decode($b64b);
        }
    }
    //$fd = explode("\r\n", $fd);
    update_option("woopos_csv_upload_cache", $fd);
    update_option("woopos_csv_filename", "SCANNED QR CODE");
}

// Build WooCommerce category list array
function wcic_woopos_build_tree($flat, $pidKey, $idKey = null) {
    $grouped = array();
    foreach ($flat as $sub) { $grouped[$sub[$pidKey]][] = $sub; }
    $fnBuilder = function($siblings) use (&$fnBuilder, $grouped, $idKey) {
        foreach ($siblings as $k => $sibling) {
            $id = $sibling[$idKey];
            if(isset($grouped[$id])) {
                $sibling['children'] = $fnBuilder($grouped[$id]);
            }
            $siblings[$k] = $sibling;
        }
        return $siblings;
    };
    $tree = $fnBuilder($grouped[0]);
    return $tree;
}

// Store success and error messages
function wcic_woopos_log($data, $type = "error") {
    global $_wcic_woopos_log;
    switch ($type) {
        case 'success':
            $_wcic_woopos_log['success'][] = $data;
            break;
        case 'error':
        default:
            $_wcic_woopos_log['error'][] = $data;
            break;
    }
}

// Parse upload count file, return data+meta
function wcic_woopos_parse_count_file($file = null) {
    //$return = array('valid_rows' => '', 'up_rows' => '', 'count_data' => '', 'valid_rows' => '', 'skipped' => '');
    if ($file == null) { $data = explode("\r\n", get_option("woopos_csv_upload_cache")); }
    else { $data  = file($file); }
    $return['valid_rows'] = 0;
    if ($data != false) {
        $return['up_rows'] = count($data);
        foreach ($data as $row) {
            $fields = explode(',', $row);
            $sku = strtoupper($fields[0]);
            $quantity = rtrim($fields[1]);
            if ((is_numeric($quantity)) && ($sku != null)) {
                $return['count_data'][$sku] += $quantity;
                $return['valid_rows']++;
            } else {
                $return['skipped']--;
            }
        }
        return $return;
    } else { return false; }
}

// Download SKU List...
function wcic_woopos_download_export(&$products) {
    $output = "";
    header('Content-Description: File Transfer');
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename=skulist.txt');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    header('Pragma: private');
    foreach ($products as $p) {
        $output .= $p['sku'] . "," . $p['name'] . " QOH:" . $p['q'] . "," . $p['p'] . "\r\n";
    }
    echo rtrim($output);
    exit;
}

// Get array of products in selected categories
function wcic_woopos_get_products() {
    global $_wcic_woopos_log, $_wcic_woopos_prods_count;
    $wooprods = array();
    $cats = @array_keys(get_option("woopos_selected_cats"));
    if (count($cats) > 0) {
    foreach ($cats as $c_id) {
        $args = array(
            'post_type'             => array('product'),
            'numberposts' => -1,
            'tax_query'             => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms'         => $c_id,
                    'operator'      => 'IN'
                )
            )
        );
        $products = get_posts( $args );
        foreach ($products as $product) {
            $_wcic_woopos_prods_count++;
            $pM = get_post_meta($product->ID);
            $sku = strtoupper($pM['_sku'][0]);
            if ($sku == "") { $sku = "product_id_" . $product->ID; }
            if ($pM['_manage_stock'][0] == 'yes') {
                $wooprods[$sku]['id'] = $product->ID;
                $wooprods[$sku]['q'] = round($pM['_stock'][0]);
                $wooprods[$sku]['sku'] = $sku;
                $wooprods[$sku]['p'] = @number_format($pM['_regular_price'][0], 2);
                $wooprods[$sku]['name'] = str_replace(',', '', $product->post_title);
                $wooprods[$sku]['parentid'] = $product->ID;
            }
            // has variants?
            $args2 = array('post_type' => 'product_variation', 'post_parent' => $product->ID, 'meta_query' => array('key' => '_manage_stock', 'value' => 'yes','compare' => '='));
            $vari = get_posts($args2);
            foreach ($vari as $var) {
                unset($wooprods[$sku]);
                $pM2 = get_post_meta($var->ID);
                if (isset($pM2['_manage_stock']) == true && $pM2['_manage_stock'][0] == 'yes') {
                    $vsku = strtoupper($pM2['_sku'][0]);
                    if ($vsku == "") { $vsku = "product_id_" . $var->ID; }
                    $vsku_price = @number_format(($pM2['_regular_price'][0] == null) ? 0 : $pM2['_regular_price'][0], 2);
                    $wooprods[$vsku]['id'] = $var->ID;
                    $wooprods[$vsku]['q'] = round($pM2['_stock'][0]);
                    $wooprods[$vsku]['sku'] = $vsku;
                    $wooprods[$vsku]['p'] = $vsku_price;
                    $wooprods[$vsku]['name'] = str_replace(',', '', $var->post_title);
                    $wooprods[$vsku]['vsku'] = true;
                    $wooprods[$vsku]['parentid'] = $product->ID;
                }
            }
        }
      }
    }
    $_wcic_woopos_prods_count = count($wooprods);
    return ($wooprods);
}

// Update the inventory count
function wcic_woopos_update_stock($data, &$prods, $zero = false) {
    $dry_run_warning = "&nbsp;&nbsp; <div id='woopos_warn' style='background-color:red;color:white;padding:5px;border-radius:5px;font-weight:bold;display:inline;'> Stock Quantities did NOT update/change in the database. Uncheck Dry Run when uploading Counted List to update stock </div>&nbsp; &nbsp;<br /><br />";
    global $_wcic_woopos_prods_count;
    $not_found = $found = $failed = $zero_updated = 0;
    $result_text = "";
    $up_sku_count = count($data);
    if ($_POST['dry_run'] == 'Y') {
        $dry_run_off = false;
    } else { $dry_run_off = true; }
    foreach ($data as $sku => $nq) {
      if (is_array($prods[$sku])) {
        // Backorder settings
        $backorder_on = get_post_meta($prods[$sku]['id'], '_backorders', true);
        if ($backorder_on == 'yes' | $nq > 0) {
            $ss = 'instock';
        } elseif ($backorder_on == 'no' && $nq <=0) {
            $ss = 'outofstock';
        }   
        // Quantity check and update
        if ($prods[$sku]['q'] != $nq) {
            $result_text .= $sku . "," . $prods[$sku]['name'] . "," . $prods[$sku]['q'] . "," . $nq . "\r\n";
            $found++;
            if ($dry_run_off) {
                update_post_meta($prods[$sku]['id'], '_stock', $nq);
            }
        } else {
            $failed++;
        }
        if ($dry_run_off) {
            update_post_meta($prods[$sku]['id'], '_stock_status', $ss);
        	WC_Product_Variable::sync( $prods[$sku]['parentid']);
			WC_Product_Variable::sync_stock_status( $prods[$sku]['parentid'] );		
	    }
        unset($prods[$sku]);
      } else {
        $not_found++;
        $_result_nf .= $sku . "\r\n";
      }
    }
    // Quantity to zero
    if ($_POST['set_q_zero'] == 'Y') {
       foreach ($prods as $p) {
            if ($p['q'] != 0) {
                $result_text .= $p['sku'] . "," . $p['name'] . "," . $p['q'] . ",0\r\n";
                $zero_updated++;
                if ($dry_run_off) {
                    update_post_meta($p['id'], '_stock', '0');
                }
            } else {
                $_az++;
            }
            if ($dry_run_off) {
                $backorder_on = get_post_meta($p['id'], '_backorders', true);
                if ($backorder_on == 'yes') {
                    $ss ="instock";
                } else {
                    $ss ='outofstock';
                }
                update_post_meta($p['id'], '_stock_status', $ss);
         	    WC_Product_Variable::sync( $p['parentid']);
			    WC_Product_Variable::sync_stock_status( $p['parentid'] );		
	      }
       }
    }
    // View results data
    if ($not_found + $zero_updated + $found > 0) {
        $result_text = "<br /><b>SKU</b><hr noshade>, <br /><b>Name</b><hr noshade>, <br /><b>Qty Changed From</b><hr noshade>, <br /><b>Qty Changed To</b><hr noshade>\r\n" . $result_text;
        if ($_result_nf != "") {
            $result_text .= "<br />Not found &#8628;\r\n" . $_result_nf;
        }
        $_SESSION['result_text'] = $result_text;
      } else {
        $_SESSION['result_text'] = null;
      }
    // Logs
        $_log_warn = ($_POST['dry_run'] == 'Y') ? $dry_run_warning : null;
        $_log_updated = (($found + $zero_updated) > 0) ? "<span style='color:green;font-size:16px'>Updated <b>" . ($found + $zero_updated) . "</b> products...</span><br /><br />" : "<b>No products were updated or no update required.</b><br /><br />";
        $_log_selprod = (count($prods) > 0) ? "SKUs selected &rarr; $_wcic_woopos_prods_count <br />" : null;
        $_log_upprod = ($up_sku_count > 0) ? "SKUs uploaded &rarr; $up_sku_count<br />" : null;
        $_log_zeroup = ($zero_updated > 0) ? "Quantity set to zero &rarr; $zero_updated<br />" : null;
        $_log_unchanged = (($failed+$_az) > 0) ? "Unchanged &rarr; " . ($failed + $_az) . "<br />" : null;
        $_log_notfound = ($not_found > 0) ? "Not found &rarr; $not_found<br /><br />" : null;
        $_log_result = ($found + $zero_updated + $not_found > 0) ? "<br /><button class='woopos_btn2' onClick='javascript:wcic_woopos_get_result(\"\", this)'>View Results</button>&nbsp;&nbsp;&nbsp;<a href='" . $_SERVER['REQUEST_URI'] . "&method=result&download=y'><button class='woopos_btn2'>Download Results</button></a><br/><div id='woopos_log_result_data'></div>" : null;
        $_log_msg = $_log_warn . $_log_updated . $_log_selprod . $_log_upprod . $_log_zeroup . $_log_unchanged . $_log_notfound . $_log_result;
        $_log_type = (($found + $zero_updated) > 0) ? 'success' : '';
        wcic_woopos_log($_log_msg, $_log_type);
    if ($dry_run_off) {
        delete_option("woopos_csv_upload_cache");
        delete_option("woopos_csv_filename");
    }
}

// View or download the result
function wcic_woopos_result($download = false) {
    if ($_SESSION['result_text'] != "") {
        $rows = explode("\n", $_SESSION['result_text']);
        $c = 0;
        $view_result = '<table cellpadding="4">';
        foreach ($rows as $r) {
            $i = explode(',', $r);
            $view_result .= '<tr>';
            foreach ($i as $d) {
                $view_result .= "<td>$d</td>";
            }
            $view_result .= '</tr>';
            $c++;
        }
        $view_result .= '</table>';
        if ($download == false) {
            return $view_result;
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename=import_results.txt');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            header('Pragma: private');
            echo strip_tags($_SESSION['result_text']);
            exit;
        }
    } else {
        return "<br /> No changes made, no results shown.";
    }
}

// Show categories selection HTML
function wcic_woopos_show_categories() {
        $all_categories = get_categories(array('taxonomy' => 'product_cat', 'hide_empty' => 0));
        $cat_arr = array();
        foreach ($all_categories as $cat) {
            $cat_arr[] = array('id' => $cat->cat_ID, 'parentID' => $cat->parent, 'name' => $cat->name);
            $cat_data[$cat->cat_ID] = $cat->name;
        }
        echo wcic_woopos_cat_box(wcic_woopos_build_tree($cat_arr, 'parentID', 'id'));
}

// Form operation selector and processing
function wcic_woopos_form_accept() {
    if(current_user_can('manage_woocommerce')) {
        session_start();
        global $_wcic_woopos_log, $_wcic_woopos_prods_count;
        if (isset($_REQUEST['method'])) {
            $wooprods = wcic_woopos_get_products();
            switch ($_REQUEST['method']) {
                case 'ready_check':
                    $fl = strip_tags(file_get_contents("http://magentopos.com/POSLSService.asmx/GetFileLen?FileName={$_GET['filename']}"));
                    if ($fl > 0) {
                        echo $fl;
                    } else {
                        echo "Waiting...";
                    }
                    exit;
                case 'result':
                    echo wcic_woopos_result(($_GET['download']=='y') ? true : false);
                    exit;
                // Set categories, save to session
                case 'categories':
                    if (isset($_POST['selected_cats']) && count($_POST['selected_cats']) > 0) {
                        update_option("woopos_selected_cats", $_POST['selected_cats']);
                    } else {
                        delete_option("woopos_selected_cats");
                    }
                break;
                case 'import':
                    if ($_POST['ucl_remote_file'] != "") {
                        @wcic_woopos_download_server_data($_POST['ucl_remote_file'], $_POST['ucl_remote_file_size']);
                    }
                    if (is_uploaded_file($up_file = $_FILES['counted_list']['tmp_name'])) {
                        // file uploaded - clear csv cache
                        delete_option("woopos_csv_filename");
                        delete_option("woopos_csv_upload_cache");
                        $up_result = @wcic_woopos_parse_count_file($up_file);
                        if ($up_result['valid_rows'] > 0) {
                            wcic_woopos_log('CSV Parser: Parsed <b>' . $up_result['valid_rows'] . "</b> CSV rows from " . $_FILES['counted_list']['name'] . "<br />CSV Parser: Merged to <b>" . count($up_result['count_data']) . '</b> unique SKUs...<br />', 'success');
                            // Update the stock
                            @wcic_woopos_update_stock($up_result['count_data'], $wooprods);
                            // cache file
                            update_option("woopos_csv_filename", $_FILES['counted_list']['name']);
                            update_option("woopos_csv_upload_cache", file_get_contents($up_file));
                        } else {
                            wcic_woopos_log('CSV Parser: No valid CSV rows found.');
                        }
                    } else if ($woopos_csv_cache = get_option("woopos_csv_upload_cache", false)) {
                        $up_result = @wcic_woopos_parse_count_file();
                        wcic_woopos_log('CSV Parser: Parsed <b>' . $up_result['valid_rows'] . "</b> CSV rows from <b>cache</b> of " . get_option("woopos_csv_filename") . "<br />CSV Parser: Merged to <b>" . count($up_result['count_data']) . '</b> unique SKUs...<br />', 'success');
                        @wcic_woopos_update_stock($up_result['count_data'], $wooprods);
                    } else {
                        wcic_woopos_log('CSV Parser: File upload failure or no file uploaded.');
                    }
                    break;
                case 'export':
                    if (count($wooprods) > 0) {
                        wcic_woopos_download_export($wooprods);
                    } else {
                        wcic_woopos_log("No valid products found in selection. Please make sure you have MANAGE-STOCK products in selected categories. Click SAVE button to save selected categories");
                    }
                    break;
                case 'gen_qr_code':
                    if (count($wooprods) > 0) {
                        $qr_image = wcic_woopos_generate_qr_code($wooprods);
                    } else {
                        wcic_woopos_log("No valid products found in selection. Please make sure you have MANAGE-STOCK products in selected categories. Click SAVE button to save selected categories");
                    }
                    break;
                case 'clear':
                    delete_option("woopos_csv_upload_cache");
                    delete_option("woopos_csv_filename");
                    wcic_woopos_log("Uploaded CSV data cleared", "success");
                    break;
                default:
                    break;
                }
            }
    } else {
        wcic_woopos_log("You do not have sufficient permission to use this plugin.");
    }
}

// Select categories before using feature
function wcic_woopos_step2_chk($html = "", $noerror = true) {
    if (count(get_option("woopos_selected_cats")) > 0) {
        echo $html;
    } else if ($noerror) {
        echo "<span style='color:red'>Select at least one category first, then click SAVE.</span>";
    }
}

// Setup the wp-admin menu
function wcic_woopos_admin_menu_setup() {
    add_menu_page('WooCommerce Inventory Count', 'WooCommerce Inventory Count', "manage_woocommerce", "woopos-wcic", "wcic_woopos_show_page", 'dashicons-cart', 4);
}

// Enqueue scripts
function wcic_woopos_enqueue() {
    wp_enqueue_style( 'woopos_css_1', plugins_url('css/main.css', __FILE__) );
    wp_enqueue_script( 'woopos_js_1', plugins_url('js/tree_select.js', __FILE__) );
}



/** Hooks **/
add_action('init', 'wcic_woopos_form_accept');
add_action('admin_enqueue_scripts', 'wcic_woopos_enqueue');
add_action('admin_menu', 'wcic_woopos_admin_menu_setup');
?>