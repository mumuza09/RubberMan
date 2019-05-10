=== WooCommerce POS Inventory Count ===
Contributors: woopos
Tags: woocommerce pos, point of sale, woocommerce point of sale, stock manager, woocommerce stock manager, stock count, inventory management, physical count, stock management, frontend manager, woocommerce frontend shop manager, shop manager, woocommerce live manager, multi vendor, product vendors, woopos
Requires at least: 4.0
Tested up to: 5.0.3
Stable tag: 19.01.25
Requires PHP: 5.2.4
Donate link: https://woopos.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Physical inventory count by barcode scanner. Bulk update stock quantity. Manage stock automatically with WooCommerce POS (WooPOS Point Of Sale)

== Description ==
WooCommerce POS Inventory Count allows you do Physical Inventory Count for WooCommerce by scanning QR code and import and update counted quantities from inventory scanner.

This plugin is created by [WooPOS](http://woopos.com/), an all-in-one top-class Windows Desktop App for Point Of Sale (POS). From inventory management to data analytics, sales processing and employee management, WooPOS (WooCommerce POS) will help you manage your single or multiple location retail brick-and-mortar stores and online WooCommerce store. Build-in features: WooCommerce stock manager, WooCommerce shop manager, WooCommerce frontend manager, multi vendor, multi store, split payment, purchase order, average cost, employee commission, clerk permission, barcode designer and printing, product sales report, offline sales, cloud database, CRM loyalty points, rewards, store credit, gift card...and much much more.

First, select categories you want to do physical count. Click SAVE button to save selected categories.

The `Download Sku List File` button will create a SKU list file, a comma delimited CSV/TXT file which contains Sku, Description, Price. You can download this file to the [scanner app](https://play.google.com/store/apps/details?id=com.woopos.inventorycount) or click `Generate QR Code for App` and then scan the QR code from [scanner app](https://play.google.com/store/apps/details?id=com.woopos.inventorycount).

Once you have done scanning, you can upload counted list by clicking `Choose File`, or `Show QR Code` and then scan the QR code from scanner. Click `Update Stock Quantities` to make changes.


To start inventory count with this plugin, please visit iventory count plugin guide [here](https://support.woopos.com/knowledge-base/woocommerce-inventory-count-plugin/)
To start inventory count with WooPOS all-in-one Windows App, please check the general guide [here](https://support.woopos.com/knowledge-base/inventory-count-guide/)

This plugin uses 3rd Party or external service as listed below:

1. Google Chart API. This is to generate the QR code image for the scanner app to download and upload file.
The Google Chart API URL is http://chart.apis.google.com/chart?cht=qr&chs=200x200&chl

2. WooPOS file transfer service. 
If the Product List QR code scanned, your products list including Sku, Decription(Title), Price, and Stock Quantities will be uploaded to WooPOS file server.
If the Counted List QR code scanned, the counted list from the scanner including Sku and Quantities will be uploaded to WooPOS file server.



== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the `Add New` in the plugins dashboard
2. Search for `WooCommerce Inventory Count`
3. Click `Install Now`
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the `Add New` in the plugins dashboard
2. Navigate to the `Upload` area
3. Select `woocommerce-inventory-count.zip` from your computer
4. Click `Install Now`
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `woocommerce-inventory-count.zip`
2. Extract the `woocommerce-inventory-count` directory to your computer
3. Upload the `woocommerce-inventory-count` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


== Frequently Asked Questions ==

= Q: My products are not in SKU list. =
A: Be sure, that you have saved selected categories, and the products have "Manage Stock" checked on.

= Q: Update Stock Quantity change not working. =
A: Please save selected categories, unchecked `dry run`.

= Q: Do you offer support if I need help? =
A: Yes! Please go our support [forum](https://support.woopos.com/forums/) for help.

== Screenshots ==
1. WooCommerce Inventory Count Main Screen

== Changelog ==
= 19.01.25 =
* Updated QR Code scanning web service URL
= 18.04.27 =
* Fixed no products found in selected categories issue.
= 17.07.24 =
* Initial release.

== Upgrade Notice ==
No Upgrade notice.
