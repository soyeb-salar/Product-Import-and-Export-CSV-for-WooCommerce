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
 * Donate link: https://www.soyebsalar.in/donate/
 * Requires Plugins: woocommerce
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
add_action('plugins_loaded', 'woocsv_run_my_plugin_after_woocommerce_or_if_installed', 20); //load the plugin after woocommerce using 20 priority

/**
 * Check if WooCommerce is activated or installed and run the plugin accordingly.
 *
 * If WooCommerce is activated, the plugin is initialized.
 * If WooCommerce is not activated but installed, the plugin is initialized and a notice is shown.
 * If WooCommerce is not installed, a different notice is shown.
 */
function woocsv_run_my_plugin_after_woocommerce_or_if_installed()
{
    // Check if WooCommerce is activated
    if (woocsv_check_is_plugin_installed_and_active('woocommerce/woocommerce.php')) {
        // WooCommerce is active, run your plugin
        woocsv_init_product_import();
    } else {
        // WooCommerce is not installed, show a different notice
        add_action('admin_notices', 'woocsv_woocommerce_not_installed_notice');

    }
}

/**
 * Display an admin notice if WooCommerce is not installed or not activated.
 *
 * @return void
 */
function woocsv_woocommerce_not_installed_notice()
{
    // Display an admin notice if WooCommerce is not installed
    echo '<div class="notice notice-error">
             <p>' . esc_html__("WooCommerce is not installed or not activated. Please install and activate WooCommerce to use Product Import and Export CSV for WooCommerce plugin.", "product-import-and-export-csv-for-woocommerce") . '</p>
          </div>';
}

/**
 * Initialize the product import/export plugin.
 *
 * Instantiates the WOOCSV_Product_Import_Export class, which handles the
 * product import and export functionality.
 */
function woocsv_init_product_import()
{
    new WOOCSV_Product_Import_Export();
}

/**
 * Checks if a plugin is installed and active.
 *
 * @param string $plugin_slug The plugin slug to check.
 *
 * @return bool True if the plugin is installed and active, false otherwise.
 */
function woocsv_check_is_plugin_installed_and_active($plugin_slug)
{
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    // Check if plugin is installed
    $installed_plugins = get_plugins();
    $plugin_installed = false;

    foreach ($installed_plugins as $plugin_path => $plugin_info) {
        if (strpos($plugin_path, $plugin_slug) !== false) {
            $plugin_installed = true;
            break;
        }
    }

    // Check if plugin is active
    $plugin_active = is_plugin_active($plugin_slug);

    return $plugin_installed && $plugin_active;
}
