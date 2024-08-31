<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
$default_image_url = esc_url(WOOCSV_PLUGIN_URL . 'assets/images/default60.png'); // URL of the default image

?>
    <div class="wrap">
        <h1><?php esc_html_e('Export Products', 'wcom-product-import-export-csv');?></h1>
        <form id="export-products-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="export_selected_products_csv">
            <?php wp_nonce_field('export_products_nonce', 'export_products_nonce_field');?>

            <table id="export-table" class="display">
              <thead>
                    <tr>
                           <th scope="col" class="manage-column column-cb check-column">
                            <input type="checkbox" id="select_all_products">
                        </th>
                        <th scope="col"><?php esc_html_e('Images', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Name', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Type', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('SKU', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Regular Price', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Sale Price', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Short Description', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Categories', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Tags', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Stock Status', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Stock Quantity', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('Attributes', 'wcom-product-import-export-csv');?></th>
                        <th scope="col"><?php esc_html_e('View Product', 'wcom-product-import-export-csv');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
// Fetch products
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'post_status' => 'publish',
);

$products = get_posts($args);
foreach ($products as $product_post) {
    $product = wc_get_product($product_post->ID);
    ?>
                        <tr>
                              <th scope="row" class="check-column">
                                <input type="checkbox" name="product_ids[]" value="<?php echo esc_attr($product->get_id()); ?>" class="product-checkbox">
                            </th>
                            <td>
                                <?php
$cover_image_id = $product->get_image_id();
    if ($cover_image_id) {
        $cover_image_url = wp_get_attachment_image_src($cover_image_id, 'thumbnail');
        if ($cover_image_url) {
            echo '<div><img src="' . esc_url($cover_image_url[0]) . '" width="60" height="60" /></div>';
        } else {
            echo '<div><img src="' . esc_url($default_image_url) . '" width="60" height="60" /></div>';
        }
    } else {
        echo '<div><img src="' . esc_url($default_image_url) . '" width="60" height="60" /></div>';
    }
    ?>
                            </td>
                            <td><?php echo esc_html($product->get_name()); ?></td>
                            <td><?php echo esc_html($product->get_type()); ?></td>
                            <td><?php echo !empty($product->get_sku()) ? esc_html($product->get_sku()) : "-"; ?></td>
                            <td><?php echo !empty($product->get_regular_price()) ? esc_html($product->get_regular_price()) : "-"; ?></td>
                            <td><?php echo !empty($product->get_sale_price()) ? esc_html($product->get_sale_price()) : "-"; ?></td>
                            <td><?php echo !empty($product->get_short_description()) ? esc_html($product->get_short_description()) : "No Short Description"; ?></td>
                            <td><?php echo !empty($product->get_categories()) ? esc_html(implode('|', wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'names')))) : "-"; ?></td>
                            <td><?php echo !empty($product->get_tags()) ? esc_html(implode('|', wp_get_post_terms($product->get_id(), 'product_tag', array('fields' => 'names')))) : "-"; ?></td>
                            <td><?php echo !empty($product->get_stock_status()) ? esc_html($product->get_stock_status()) : "-"; ?></td>
                            <td><?php echo !empty($product->get_stock_quantity()) ? esc_html($product->get_stock_quantity()) : "-"; ?></td>
                            <td><?php echo !empty($product->get_attributes()) ? esc_html($this->woocsv_format_attributes_table($product->get_attributes())) : "-"; ?></td>
                            <td><a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" target="_blank"><?php esc_html_e('View', 'wcom-product-import-export-csv');?></a></td>
                        </tr>
                        <?php
}
?>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit" id="export-csv-button" value="<?php esc_attr_e('Export Selected Products as CSV', 'wcom-product-import-export-csv');?>" class="button button-primary">
            </p>
        </form>
    </div>
