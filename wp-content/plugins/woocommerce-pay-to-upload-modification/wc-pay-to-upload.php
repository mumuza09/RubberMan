<?php
/*
Plugin Name: WooCommerce Pay to Upload
Plugin URI:  http://ashokg.in/woocommerce-pay-to-upload 
Description: Allow customers to upload a file after the payment made. Developed on the Airy plugin framework
Version: 2.1.3
Author: Ashok G
Author URI: http://ashokg.in

Copyright: © 2016 Ashok G.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Special thanks to Michael McCarthy PhD ( @WPC4U ) for having this plugin developed and allowing it to be shared.
*/

/**
 * Required files
 **/
require_once('classes/class-airy-framework.php' );
require_once('classes/class-wc-pay-to-upload.php' );

/**
 * Start the engines!
 **/
$WC_Pay_To_Upload = new WC_Pay_To_Upload;
