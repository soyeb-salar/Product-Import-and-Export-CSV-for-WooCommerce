<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<div class="wrap main-import-form">
    <h1><?php esc_html_e('WooCommerce Product Import', 'product-import-and-export-csv-for-woocommerce');?></h1>
    <p><?php esc_html_e('Use the form below to upload a CSV file containing your product data. You can download an example file ', 'product-import-and-export-csv-for-woocommerce');?>
        <a href="<?php echo esc_url(WOOCSV_PLUGIN_URL . '/assets/example/woocommerce_products_example.csv'); ?>" download><?php esc_html_e('here', 'product-import-and-export-csv-for-woocommerce');?></a>.
    </p>
    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="wc-product-import-form">
        <input type="hidden" name="action" value="import_products">
        <?php wp_nonce_field('import_products_nonce', 'import_products_nonce_field');?>
        <input type="file" name="product_file" accept=".csv" required>
        <?php submit_button(esc_html__('Upload Products', 'product-import-and-export-csv-for-woocommerce'));?>
        <div id="loading-indicator" style="display:none;"><?php esc_html_e('Loading...', 'product-import-and-export-csv-for-woocommerce');?></div>
    </form>
    <?php

if (isset($_GET['message'])) {
    if (isset($_GET['nonce_message']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce_message'])), 'woocsv_import_action_message')) {
        $message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
        ?>
        <div class="notice notice-success is-dismissible">
            <div class="import-success-message">
        <?php
echo esc_html($message);
        ?>
        </div>
     </div>
        <?php
} else {
        wp_die('Nonce verification failed.');
    }
}

?>
    <h2><?php esc_html_e('CSV Columns Explanation', 'product-import-and-export-csv-for-woocommerce');?></h2>
    <table class="wp-list-table widefat fixed striped woocsvtable">
        <thead>
            <tr>
                <th class="woocsvtd-head"><?php esc_html_e('Column Name', 'product-import-and-export-csv-for-woocommerce');?></th>
                <th><?php esc_html_e('Description', 'product-import-and-export-csv-for-woocommerce');?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('name', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Product name.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('type', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Product type (simple, variable, downloadable, grouped, external).', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('sku', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Product SKU.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('regular_price', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Regular price.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('sale_price', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Sale price.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('description', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Full description.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('short_description', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Short description.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('categories', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Comma-separated category names. Separated by "|" pipe symbol (double quotes are not included) Example: "Category 1" | "Category 2"', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('tags', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Comma-separated tag names. Separated by "|" pipe symbol (double quotes are not included) Example: "Tag 1" | "Tag 2"', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('images', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Comma-separated image URLs (first URL is the main image, others are gallery images). Example: http://example.com/image2.jpg | http://example.com/gallery2-1.jpg | http://example.com/gallery2-2.jpg', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('stock_status', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Stock status (in_stock, out_of_stock, on_backorder).', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('stock_quantity', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Stock quantity.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('weight', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Product weight.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('length', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Product length.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('width', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Product width.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('height', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Product height.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('attributes', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Serialized array of attributes. you can use this online tools to generate the attributes from <a href="https://wtools.io/serialize-php-array">array to Serialize tools:</a>', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('downloadable', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Whether the product is downloadable (yes, no).', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('downloads', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Serialized array of downloads.', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('virtual', 'product-import-and-export-csv-for-woocommerce');?></td>
                <td><?php esc_html_e('Whether the product is virtual (yes, no).', 'product-import-and-export-csv-for-woocommerce');?></td>
            </tr>
        </tbody>
    </table>
</div>
