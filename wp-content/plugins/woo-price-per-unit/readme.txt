=== WooCommerce Price Per Unit ===
Contributors: mechuram 
Donate link: https://www.paypal.me/mechuram/5usd
Tags: woocommerce, price, weight, price customization
Stable tag: 1.8
Tested up to: 5.1
Requires at least: 4.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WooCommerce Price Per Unit allows the user to show prices recalculated per units(weight) and do some other customization to the appearance of prices

== Description ==

This is an extension for WooCommerce which will help you to sell products where can be important for the customer to know how much a weight unit costs.
For example when selling food. Price is recalculated per weight and then shown according to your liking. The rules can be set store-wide or just for certain products.
**Main function of the plugin is, that it takes the price of your product and divides this price by its weight, this is then displayed to your liking**

== Important notice ==

**This plugin works only if you have weight set on the product. This is independent setting used for shipping purposes.**

**You can find it here:**
Products->All products->some of your products->Shipping->Weight

**For variable products with different weight of variations:**
Products->All products->some of your products->Variations->some variation->Weight

**Recalculated price can be shown**

* Instead of original price
* As a new row after original price

You can also add some text after recalculated price for example "/Kg"
Price is recalculated only when the weight is set on the product.

**There are also two different settings depending on viewed page**

* Settings for store page
* Settings for single product page
  
**This plugin can do also some other customization to the appearance of the price**

* Additional custom text for all prices
* You can hide original price when product is on sale
* Additional custom text for variations
* You can hide maximum price for variable products

Plugin is compatible with Woocommerce 2.6.x and 3.3.x

Help me develop this plugin. If you like it please donate some small sum.

**Future releases will bring:**

* Custom recalculation unit - you will be able to set custom number of units(let's say pieces) independent on weight.
* Translation for units text.
* Possibly setting rules to whole categories.
* Custom CSS selector for additional text.  

**There are two sets of settings**

**General settings** - which will affect every product in the store
  It is located under WooCommerce -> Settings -> Products -> Price Per Unit
**Single product settings** - affects just single product, can also override general settings
  It is located in product editor - tab Price Per Unit
  
**Changelog**   

**1.8**
- Bug fix
- Compatibility with WP 5.1, WooCommerce 3.5.5 update

**1.7**
- Code rewrite
- Bug fix

**1.6**
- Translation fix 
- Checked WooCommerce compatibility for version 3.3.x
- Documentation update

**1.5**
- Plugin is set to display recalculation for all products by default after installation. Doesn't affect current installations.
- Fixed compatibility with WooCommerce POS  

**1.4**
- Fixed bug of improperly displayed price with TAX

**1.3**
- Added CSS class for whole new row. The class name is mcmp_recalc_price_row.
- Added option for predefined styling of new row (off by default). New row will be in different size and italics.

**1.2**
- Improved handling of variable products. Now it works properly even with different weight on variations.
- Added CSS classes for modification of additional texts appearance. Classes can be found in general settings help texts.

**1.1**
Changed behavior on variable products to conform with WooCommerce 3.x
- Don't show sale price on variable products

**1.0**
Initial Release
  
== Installation ==

1. Install and activate the plugin in your WordPress dashboard by going to Plugins -> Add New.
2. Search for "WooCommerce Price Per Unit" to find the plugin.
3. When you see WooCommerce Price Per Unit, click "Install Now" to install the plugin.
4. Click "Activate" to activate the plugin.



== Frequently Asked Questions ==

= Is it possible to set recalculation just for one product? =

Yes. You have to go to product editor, tab Price per unit and set an override to the rule you want. It works then even despite the global settings is turned off.

= I have recalculation on, but the price seems untouched. What's wrong?  =

Recalculated price is shown only when the recalculation takes place. That means if you don't have the weight set on the product, nothing happens to the price. Make sure you have weight set.

= I want to use only other features, not recalculation. Is it necessary to have recalculation on?  =

No. Some features are independent on recalculation, you will find them as "General price options" and "Options for variable products" those settings are store wide - they will affect all products.



== Screenshots ==

1. General options - those settings will affect all products
2. Single product settings - these settings affect only current product
3. If you want to use recalculation you'll need to set product weight. Otherwise you can use only settings under "General price options"
4. Single product page with recalculated price as new row
5. Example of shop page with different overrides. Recalculated price replaced original one, recalculated price as new row, no recalculation at all.   
6. Store with recalculation set for all products

== Changelog ==

= 1.8 =
- Bug fix
- Compatibility with WP 5.1, WooCommerce 3.5.5 update

= 1.7 =
- Code rewrite
- Bug fix

= 1.6 =
- Translation fix 
- Checked WooCommerce compatibility for version 3.3.x
- Documentation update

= 1.5 =
- Plugin is set to display recalculation for all products by default after installation. Doesn't affect current installations.
- Fixed compatibility with WooCommerce POS  

= 1.4 =
- Fixed bug of improperly displayed price with TAX

= 1.3 =
- Added CSS class for whole new row. The class name is mcmp_recalc_price_row.
- Added option for predefined styling of new row (off by default). New row will be in different size and italics.

= 1.2 =
- Improved handling of variable products. Now it works properly even with different weight on variations.
- Added CSS classes for modification of additional texts appearance. Classes can be found in general settings help texts.

= 1.1 =
Changed behavior on variable products to conform with WooCommerce 3.x
- Don't show sale price on variable products

= 1.0 =
Initial Release