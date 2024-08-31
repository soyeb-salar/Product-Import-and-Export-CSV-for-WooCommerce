<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<div class="wrap main-import-form">
    <h1><?php esc_html_e('WooCommerce Product Import', 'woocsv-product-import-export-csv');?></h1>
    <p><?php esc_html_e('Use the form below to upload a CSV file containing your product data. You can download an example file ', 'woocsv-product-import-export-csv');?>
        <a href="<?php echo esc_url(WOOCSV_PLUGIN_URL . '/assets/example/woocommerce_products_example.csv'); ?>" download><?php esc_html_e('here', 'woocsv-product-import-export-csv');?></a>.
    </p>
    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="wc-product-import-form">
        <input type="hidden" name="action" value="import_products">
        <?php wp_nonce_field('import_products_nonce', 'import_products_nonce_field');?>
        <input type="file" name="product_file" accept=".csv" required>
        <?php submit_button(esc_html__('Upload Products', 'woocsv-product-import-export-csv'));?>
        <div id="loading-indicator" style="display:none;"><?php esc_html_e('Loading...', 'woocsv-product-import-export-csv');?></div>
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
    <h2><?php esc_html_e('CSV Columns Explanation', 'woocsv-product-import-export-csv');?></h2>
    <table class="wp-list-table widefat fixed striped woocsvtable">
        <thead>
            <tr>
                <th class="woocsvtd-head"><?php esc_html_e('Column Name', 'woocsv-product-import-export-csv');?></th>
                <th><?php esc_html_e('Description', 'woocsv-product-import-export-csv');?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('name', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Product name.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('type', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Product type (simple, variable, downloadable, grouped, external).', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('sku', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Product SKU.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('regular_price', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Regular price.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('sale_price', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Sale price.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('description', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Full description.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('short_description', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Short description.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('categories', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Comma-separated category names. Separated by "|" pipe symbol (double quotes are not included) Example: "Category 1" | "Category 2"', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('tags', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Comma-separated tag names. Separated by "|" pipe symbol (double quotes are not included) Example: "Tag 1" | "Tag 2"', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('images', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Comma-separated image URLs (first URL is the main image, others are gallery images). Example: http://example.com/image2.jpg | http://example.com/gallery2-1.jpg | http://example.com/gallery2-2.jpg', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('stock_status', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Stock status (in_stock, out_of_stock, on_backorder).', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('stock_quantity', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Stock quantity.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('weight', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Product weight.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('length', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Product length.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('width', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Product width.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('height', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Product height.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('attributes', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Serialized array of attributes. you can use this online tools to generate the attributes from <a href="https://wtools.io/serialize-php-array">array to Serialize tools:</a>', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('downloadable', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Whether the product is downloadable (yes, no).', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('downloads', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Serialized array of downloads.', 'woocsv-product-import-export-csv');?></td>
            </tr>
            <tr>
                <td class="woocsvtd-head"><?php esc_html_e('virtual', 'woocsv-product-import-export-csv');?></td>
                <td><?php esc_html_e('Whether the product is virtual (yes, no).', 'woocsv-product-import-export-csv');?></td>
            </tr>
        </tbody>
    </table>
</div>
