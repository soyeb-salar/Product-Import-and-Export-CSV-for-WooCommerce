jQuery(document).ready(function ($) {
    $('#select_all_products').on('click', function () {
        var checked = this.checked;
        $('input[name="product_ids[]"]').prop('checked', checked);
    });

    function toggleExportButton() {
        var checkedCount = $('.product-checkbox:checked').length;
        $('#export-csv-button').prop('disabled', checkedCount === 0);
    }

    $('#select_all_products').on('change', function () {
        $('.product-checkbox').prop('checked', this.checked);
        toggleExportButton();
    });

    $('.product-checkbox').on('change', toggleExportButton);

    $('#export-products-form').on('submit', function (event) {
        if ($('.product-checkbox:checked').length === 0) {
            event.preventDefault();
            alert('Please select at least one product to export.');
        }
    });
});
