<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class WOOCSV_Product_Import_Export
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'woocsv_add_menu_page'));
        add_action('admin_post_import_products', array($this, 'woocsv_handle_file_upload'));
        add_action('admin_post_export_selected_products_csv', array($this, 'woocsv_handle_export_selected_request'));
        add_action('admin_enqueue_scripts', array($this, 'woocsv_enqueue_styles'));
    }
    public function woocsv_valid_pages()
    {
        $valid_pages = array("woocsv-product-import", "woocsv-product-export");
        return $valid_pages;
    }
    public function woocsv_add_menu_page()
    {
        add_menu_page(
            'Product Import',
            'Product Import',
            'manage_options',
            'woocsv-product-import',
            array($this, 'woocsv_render_upload_page'),
            'dashicons-upload',
            57
        );

        add_submenu_page(
            'woocsv-product-import',
            'Product Export',
            'Product Export',
            'manage_options',
            'woocsv-product-export',
            array($this, 'woocsv_render_export_page')
        );
    }

    public function woocsv_enqueue_styles()
    {
        $valid_pages = $this->woocsv_valid_pages();
        // phpcs:ignore
        $page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : "";
        if (in_array($page, $valid_pages)) {
            wp_enqueue_style('wc-product-import-csv-styles', WOOCSV_PLUGIN_URL . "assets/css/style.css", array(), '1.0.0');
            wp_enqueue_script('wc-product-export-csv-js', WOOCSV_PLUGIN_URL . "assets/js/product-export.js", array('jquery'), '1.0.0', true);

            // Add DataTables.js and related styles
            wp_enqueue_style('datatables-csv-css', WOOCSV_PLUGIN_URL . 'assets/css/dataTables.css', array(), '1.0.0');
            wp_enqueue_script('datatables-csv-js', WOOCSV_PLUGIN_URL . 'assets/js/dataTables.js', array('jquery'), '1.0.0', true);
            wp_add_inline_script('wc-product-export-csv-js', $this->woocsv_get_inline_script());
        }
    }
    private function woocsv_get_inline_script()
    {
        return "
        jQuery(document).ready(function($) {
            // Initialize DataTables
            $('#export-table').DataTable();

            // Show loading indicator on form submit
            $('#import-form').on('submit', function() {
                $('#loading-indicator').show();
            });

            // Hide loading indicator after page load
            $(window).on('load', function() {
                $('#loading-indicator').hide();
            });

            // Validate CSV file format before upload
            $('#import-form').on('submit', function(e) {
                var fileInput = $('#csv_file')[0];
                if (fileInput.files.length == 0) {
                    alert('Please select a file.');
                    e.preventDefault();
                    return false;
                }

                var fileName = fileInput.files[0].name;
                var fileExtension = fileName.split('.').pop().toLowerCase();
                if (fileExtension !== 'csv') {
                    alert('Please upload a valid CSV file.');
                    e.preventDefault();
                    return false;
                }

                // Further validation can be added here
            });
        });
        ";
    }
    public function woocsv_render_upload_page()
    {

        include_once WOOCSV_PLUGIN_PATH . "pages/wc-product-import-form.php";

    }
    public function woocsv_render_export_page()
    {
        include_once WOOCSV_PLUGIN_PATH . "pages/wc-product-export-csv.php";
    }

    public function woocsv_handle_file_upload()
    {
        if (!isset($_POST['import_products_nonce_field']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['import_products_nonce_field'])), 'import_products_nonce')) {
            wp_die('Nonce verification failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        if (!isset($_FILES['product_file'])) {
            wp_die('No file uploaded');
        }

        $file = $_FILES['product_file'];
        $file_path = $file['tmp_name'];

        // Load the CSV file
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
        global $wp_filesystem;

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($ext != 'csv') {
            wp_die('Invalid file type');
        }

        $this->woocsv_import_products($file_path);
    }

    private function woocsv_import_products($file_path)
    {
        if (($handle = fopen($file_path, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');

            $success_count = 0;
            $error_count = 0;
            $errors = array();

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                try {
                    $product_data = array_combine($header, $row);
                    $this->woocsv_create_or_update_product($product_data);
                    $success_count++;
                } catch (Exception $e) {
                    $error_count++;
                    $errors[] = "Row $index: " . $e->getMessage();
                }
            }

            fclose($handle);

            // Provide feedback to the user
            $message = "Imported $success_count products successfully. ";
            if ($error_count > 0) {
                $message .= "$error_count errors occurred.";
            }

            if (!empty($errors)) {
                $message .= ' Errors: ' . implode('; ', $errors);
            }

            //  wp_redirect(add_query_arg('message', urlencode($message), admin_url('admin.php?page=woocsv-product-import')));
            //exit;
            $nonce = wp_create_nonce('woocsv_import_action_message');
            $redirect_url = add_query_arg(array(
                'message' => urlencode($message),
                'nonce_message' => $nonce,
            ), admin_url('admin.php?page=woocsv-product-import'));

            wp_redirect($redirect_url);
            exit;

        } else {
            wp_die('Unable to open file');
        }
    }

    /*
    private function woocsv_import_products($file_path)
    {
    // Initialize WP_Filesystem
    if (false === ($credentials = request_filesystem_credentials('', '', false, false, array()))) {
    return; // Prompt for credentials
    }

    if (!WP_Filesystem($credentials)) {
    wp_die(__('Unable to access the filesystem.', product-import-and-export-csv-for-woocommerce));
    }

    global $wp_filesystem;

    if ($wp_filesystem->exists($file_path)) {
    $file_content = $wp_filesystem->get_contents($file_path);

    if ($file_content !== false) {
    $lines = explode("\n", $file_content);
    $header = str_getcsv(array_shift($lines));

    $success_count = 0;
    $error_count = 0;
    $errors = array();

    foreach ($lines as $line) {
    if (!empty($line)) {
    try {
    $row = str_getcsv($line);
    $product_data = array_combine($header, $row);
    $this->woocsv_create_or_update_product($product_data);
    $success_count++;
    } catch (Exception $e) {
    $error_count++;
    $errors[] = $e->getMessage();
    }
    }
    }

    // Provide feedback to the user
    // Translators: %d is the number of successfully imported products.
    // %d is the number of errors that occurred.
    $message = sprintf(__('Imported %d products. ', product-import-and-export-csv-for-woocommerce), $success_count);
    if ($error_count > 0) {
    $message .= sprintf(__('%d errors occurred.', product-import-and-export-csv-for-woocommerce), $error_count);
    }

    if (!empty($errors)) {
    // Translators: %s is a list of error messages separated by semicolons.
    $message .= ' ' . __('Errors: %s', product-import-and-export-csv-for-woocommerce) . implode('; ', $errors);
    }

    // Redirect with nonce
    $nonce = wp_create_nonce('woocsv_import_action_message');
    $redirect_url = add_query_arg(array(
    'message' => urlencode($message),
    'nonce_message' => $nonce,
    ), admin_url('admin.php?page=woocsv-product-import'));

    wp_redirect($redirect_url);
    exit;

    } else {
    wp_die(__('Unable to read file', product-import-and-export-csv-for-woocommerce));
    }
    } else {
    wp_die(__('File does not exist', product-import-and-export-csv-for-woocommerce));
    }
    }
     */
    private function woocsv_create_or_update_product($product_data)
    {
        // Error handling for required fields
        if (empty($product_data['name']) || empty($product_data['sku'])) {
            throw new Exception('Name and SKU are required');
        }

        $product_id = wc_get_product_id_by_sku($product_data['sku']);
        if ($product_id) {
            $product = wc_get_product($product_id);
        } else {
            $product_type = isset($product_data['type']) ? $product_data['type'] : 'simple';
            $product_class = 'WC_Product_' . ucfirst($product_type);
            if (!class_exists($product_class)) {
                $product_class = 'WC_Product_Simple';
            }
            $product = new $product_class();
        }

        $product->set_name($product_data['name']);
        $product->set_sku($product_data['sku']);
        $product->set_regular_price($product_data['regular_price']);
        $product->set_sale_price($product_data['sale_price']);
        $product->set_description($product_data['description']);
        $product->set_short_description($product_data['short_description']);
        $product->set_weight($product_data['weight']);
        $product->set_length($product_data['length']);
        $product->set_width($product_data['width']);
        $product->set_height($product_data['height']);
        $product->set_downloadable($product_data['downloadable']);
        $product->set_virtual($product_data['virtual']);

        // Check if product type supports stock management
        $supports_stock_management = in_array($product_type, array('simple', 'variable', 'grouped'));

        if ($supports_stock_management) {
            $product->set_stock_status($product_data['stock_status']);
            $product->set_stock_quantity($product_data['stock_quantity']);
        } else {
            $product->set_stock_status('instock');
            $product->set_stock_quantity(null);
        }

        // Handle categories and tags
        if (!empty($product_data['categories'])) {
            $categories = array_map('trim', explode('|', $product_data['categories']));
            $product->set_category_ids($this->woocsv_get_term_ids($categories, 'product_cat'));
        }

        if (!empty($product_data['tags'])) {
            $tags = array_map('trim', explode('|', $product_data['tags']));
            $product->set_tag_ids($this->woocsv_get_term_ids($tags, 'product_tag'));
        }

        // Handle images
        if (!empty($product_data['images'])) {
            $images = array_map('trim', explode('|', $product_data['images']));
            $main_image_id = $this->woocsv_handle_image_upload($images[0]); // Main image
            if ($main_image_id) {
                $product->set_image_id($main_image_id);
            }

            // Handle gallery images
            if (count($images) > 1) {
                $gallery_images = array_slice($images, 1);
                $gallery_image_ids = array_filter(array_map(array($this, 'woocsv_handle_image_upload'), $gallery_images));
                if (!empty($gallery_image_ids)) {
                    $product->set_gallery_image_ids($gallery_image_ids);
                }
            }
        }

        if (!empty($product_data['attributes'])) {
            $attributes = maybe_unserialize($product_data['attributes']);
            if (!is_array($attributes)) {
                $attributes = array();
            }
            $product->set_attributes($this->woocsv_format_attributes($attributes));
        } else {
            $product->set_attributes(array());
        }

        if (!empty($product_data['downloads'])) {
            $downloads = maybe_unserialize($product_data['downloads']);
            if (!is_array($downloads)) {
                $downloads = array();
            }
            $product->set_downloads($downloads);
        } else {
            $product->set_downloads(array());
        }

        $product->save();
    }

    private function woocsv_handle_image_upload($image_url)
    {
        // Check if URL is valid and image exists
        if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check if image is already in media library
        $attachment_id = $this->woocsv_get_attachment_id($image_url);
        if ($attachment_id) {
            return $attachment_id;
        }

        // Upload image
        // $image_data = file_get_contents($image_url);
        $response = wp_remote_get($image_url);

        if (is_wp_error($response)) {
            // Translators: %s is the error message received from the remote request.
            // $error_message = esc_html($response->get_error_message());
            wp_die(sprintf(esc_html('Failed to fetch image: %s', wc - product - import - export - csv), esc_html($response->get_error_message())));
        } else {
            // Get the body of the response
            $image_data = wp_remote_retrieve_body($response);
        }

        if ($image_data === false) {
            return false;
        }

        $upload = wp_upload_bits(basename($image_url), null, $image_data);
        if ($upload['error']) {
            return false;
        }

        // Insert image into media library
        $wp_filetype = wp_check_filetype($upload['file']);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name(basename($upload['file'])),
            'post_content' => '',
            'post_status' => 'inherit',
        );

        $attachment_id = wp_insert_attachment($attachment, $upload['file']);
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        return $attachment_id;
    }

    private function woocsv_get_term_ids($terms, $taxonomy)
    {
        $term_ids = array();
        foreach ($terms as $term) {
            $term_obj = get_term_by('name', $term, $taxonomy);
            if ($term_obj) {
                $term_ids[] = $term_obj->term_id;
            } else {
                $new_term = wp_insert_term($term, $taxonomy);
                if (!is_wp_error($new_term)) {
                    $term_ids[] = $new_term['term_id'];
                }
            }
        }
        return $term_ids;
    }

    private function woocsv_get_attachment_id($url)
    {
        // Get the attachment ID from the URL
        $attachment_id = attachment_url_to_postid($url);

        // Return the attachment ID
        return $attachment_id;

    }

    private function woocsv_format_attributes($attributes)
    {
        $formatted_attributes = array();
        foreach ($attributes as $name => $values) {
            $attribute = new WC_Product_Attribute();
            $attribute->set_name($name);
            $attribute->set_options($values);
            $attribute->set_visible(true);
            $attribute->set_variation(false);
            $formatted_attributes[] = $attribute;
        }
        return $formatted_attributes;
    }

    //code for export product data as csv

    public function woocsv_handle_export_selected_request()
    {
        if (!isset($_POST['export_products_nonce_field']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['export_products_nonce_field'])), 'export_products_nonce')) {
            wp_die('Nonce verification failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        if (!isset($_POST['product_ids']) || !is_array($_POST['product_ids'])) {
            wp_die('No products selected');
        }

        // Get selected product IDs
        $product_ids = array_map('intval', $_POST['product_ids']);

        // Create CSV file
        $filename = 'products-' . gmdate('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $filename);

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, array(
            'name', 'type', 'sku', 'regular_price', 'sale_price', 'description',
            'short_description', 'categories', 'tags', 'images', 'stock_status',
            'stock_quantity', 'weight', 'length', 'width', 'height', 'attributes',
            'downloadable', 'downloads', 'virtual',
        ));

        // CSV Content
        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) {
                continue; // Skip if product not found
            }

            $product_data = array(
                $product->get_name(),
                $product->get_type(),
                $product->get_sku(),
                $product->get_regular_price(),
                $product->get_sale_price(),
                $product->get_description(),
                $product->get_short_description(),
                implode('|', wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'names'))),
                implode('|', wp_get_post_terms($product->get_id(), 'product_tag', array('fields' => 'names'))),
                implode(' | ', $this->woocsv_get_product_images($product)),
                $product->get_stock_status(),
                $product->get_stock_quantity(),
                $product->get_weight(),
                $product->get_length(),
                $product->get_width(),
                $product->get_height(),
                maybe_serialize($product->get_attributes()),
                $product->get_downloadable() ? 'yes' : 'no',
                maybe_serialize($product->get_downloads()),
                $product->get_virtual() ? 'yes' : 'no',
            );

            fputcsv($output, $product_data);
        }

        fclose($output);
        exit;
    }

    private function woocsv_get_product_images($product)
    {
        $images = array();
        if ($product->get_image_id()) {
            $images[] = wp_get_attachment_url($product->get_image_id());
        }
        $gallery_image_ids = $product->get_gallery_image_ids();
        foreach ($gallery_image_ids as $image_id) {
            $images[] = wp_get_attachment_url($image_id);
        }
        return $images;
    }
    // format attributes
    private function woocsv_format_attributes_table($attributes)
    {
        $formatted_attributes = array();

        foreach ($attributes as $attribute) {
            $name = wc_attribute_label($attribute->get_name());
            $values = $attribute->get_options();

            // Initialize formatted values array
            $formatted_values = array();

            foreach ($values as $value) {
                $term = get_term_by('id', $value, $attribute->get_taxonomy());
                if ($term) {
                    $formatted_values[] = $term->name;
                } else {
                    $formatted_values[] = $value; // Fallback if not a term
                }
            }

            // Ensure $formatted_values is an array before imploding
            if (is_array($formatted_values) && !empty($formatted_values)) {
                $formatted_attributes[] = $name . ': ' . implode(', ', $formatted_values);
            } else {
                $formatted_attributes[] = $name . ': No values';
            }
        }

        return implode('; ', $formatted_attributes);

    }

}
