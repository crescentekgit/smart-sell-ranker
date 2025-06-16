=== SmartSell Ranker – Automate WooCommerce Top-Seller Categories ===
Contributors: crescentek
Tags: woocommerce, best-seller, product automation, top-selling products, product categorization, dynamic categories, sales-based sorting, ecommerce optimization
Requires at least: 6.3
Tested up to: 6.8.1
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Automatically assign your WooCommerce top-selling products to a custom category with SmartSell Ranker. Keep your store fresh, dynamic, and conversion-focused.

== Description ==

**SmartSell Ranker** is a powerful WooCommerce plugin that automates the way you showcase your store’s best-performing products.

By analysing recent order data, SmartSell Ranker automatically detects your top-selling WooCommerce products and assigns them to a selected category like “Best Sellers” or “Trending Now.” The plugin runs daily using a scheduled Cron job to ensure the list stays fresh, saving you hours of manual work.

Whether you're managing a seasonal campaign, a large inventory, or flash sales, SmartSell Ranker ensures that your most popular products are always front and center.

### 🚀 Key Features:
- Automatically detect top-selling WooCommerce products
- Assign top sellers to a designated product category
- Run updates on a 24-hour schedule using Cron jobs
- Preserve existing product categories — no overwriting (as your choice)
- Define your own timeframe for sales data
- Fully compatible with WooCommerce themes and extensions
- Lightweight and performance-optimized

### 💼 Use Cases:
- Highlight top-selling products during promotions or sales
- Automate merchandising for large or busy stores
- Improve user experience with dynamic product sorting
- Increase conversions by showcasing what’s trending

Stop managing your best-seller lists manually — let SmartSell Ranker automate the process and boost your WooCommerce store’s performance.

== Installation ==

MODERN WAY:

1. Upload the plugin files to the `/wp-content/plugins/smartsell-ranker` directory, or install the plugin directly through the WordPress plugin repository.
2. Activate the plugin through the ‘Plugins’ menu in WordPress.
3. Go to **SmartSell Ranker Settings** in the WooCommerce admin menu.
4. Select your desired time period and target category for top-sellers.
5. Save your settings — SmartSell Ranker will begin auto-updating every 24 hours.

== Frequently Asked Questions ==

= What is SmartSell Ranker used for? =
SmartSell Ranker automatically assigns your WooCommerce top-selling products to a designated category, helping increase visibility and conversions without manual effort.

= How are top-selling products determined? =
The plugin reviews WooCommerce order data over a set timeframe (e.g., last 3 months) and ranks products based on sales quantity or revenue.

= Will this affect existing product categories? =
No, SmartSell Ranker does not remove products from their current categories. It adds an additional category for top-selling items.

= Can I change how often the update runs? =
The default is every 24 hours via Cron job. Advanced users may modify this via WordPress or server-level Cron configurations.

= Is it compatible with other WooCommerce plugins and themes? =
Yes. SmartSell Ranker is developed using WordPress best practices and is compatible with most WooCommerce themes and extensions.

= How do I show the product rank on the frontend? =
You can use the shortcode `[ss_ranker_products]` inside a product page to display the current rank of that product.

= Does it slow down my site? =
No. Smart Sell Ranker uses optimized queries and caching to prevent performance issues, even on stores with many products.

= Can I exclude certain products or categories from ranking? =
Not yet, but this is planned for a future release. You can use filters to exclude items programmatically.

== Screenshots ==

1. Plugin settings page.

== Changelog ==

= 1.0.1 =
* Added : WordPress compatibility - 6.8.1
* Added : WooCommerce compatibility - 9.9.3

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
* Initial release.
