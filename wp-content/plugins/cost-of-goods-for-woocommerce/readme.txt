=== Cost of Goods for WooCommerce ===
Contributors: algoritmika, anbinder
Tags: woocommerce, cost, cost of goods, woo commerce
Requires at least: 4.4
Tested up to: 5.0
Stable tag: 1.1.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Save WooCommerce products purchase costs (i.e. cost of goods).

== Description ==

**Cost of Goods for WooCommerce** plugin lets you save WooCommerce products purchase costs.

For **variable products** costs can be saved for each variation separately or for all variations at once.

There are options to select which **admin columns** to add: product profit, product cost, order profit, order cost.

**Import costs tool** is available if you need to import costs from another product metas.

[Pro plugin version](https://wpfactory.com/item/cost-of-goods-for-woocommerce/) has options to recalculate orders cost and profit (for all orders or only for orders with no costs).

= Feedback =
* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* Please visit [Cost of Goods for WooCommerce plugin page](https://wpfactory.com/item/cost-of-goods-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Cost of Goods".

== Changelog ==

= 1.1.1 - 19/12/2018 =
* Fix - Core - `add_cost_input_shop_order()` - Getting order on AJAX correctly now.

= 1.1.0 - 06/12/2018 =
* Fix - Comma decimal separator bug fixed.
* Dev - Profit in percent added to profit HTML output.
* Dev - Cost meta changed from `_alg_cost` to `_alg_wc_cog_cost`.
* Dev - Forcing cost of goods to be always set excluding taxes.
* Dev - Saving costs as order item meta.
* Dev - Saving total cost and profit as order meta.
* Dev - Import Costs Tool - Code optimized.
* Dev - Major code refactoring.
* Dev - Plugin URI updated.
* Pro - Dev - "Recalculate orders cost and profit for all orders" option added.
* Pro - Dev - "Recalculate orders cost and profit for orders with no costs" option added.

= 1.0.1 - 17/05/2018 =
* Fix - Cost not saved for simple products - bug fixed.
* Fix - Admin settings link fixed.

= 1.0.0 - 10/05/2018 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
