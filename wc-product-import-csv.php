<?php
/**
 * Plugin Name: Product Import and Export CSV for WooCommerce
 * Description: Import and export products for WooCommerce from and to a CSV file. Supports custom fields, categories, tags, images, and attributes.
 * Version: 1.0.0
 * Author: Soyeb Salar
 * Author URI: https://www.linkedin.com/in/soyebsalar/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: product-import-and-export-csv-for-woocommerce
 * Donate link: https: //soyebsalar.in/donate/
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define("WOOCSV_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("WOOCSV_PLUGIN_URL", plugin_dir_url(__FILE__));
define("WOOCSV_PLUGIN_BASENAME", plugin_basename(__FILE__));

// Include necessary files
require_once WOOCSV_PLUGIN_PATH . 'includes/class-wc-product-import-csv.php';

// Initialize the plugin
add_action('plugins_loaded', 'woocsv_init_product_import');
function woocsv_init_product_import()
{
    new WOOCSV_Product_Import_Export();
}
