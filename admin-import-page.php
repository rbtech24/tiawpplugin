<?php
// File: your-plugin-folder/admin-import-page.php

function large_listings_import_menu() {
    add_submenu_page(
        'tools.php',
        'Import Large Listings',
        'Import Large Listings',
        'manage_options',
        'large-listings-import',
        'large_listings_import_page'
    );
}
add_action('admin_menu', 'large_listings_import_menu');

function large_listings_import_page() {
    if (isset($_POST['import_listings'])) {
        // Handle file upload and import
        if (!empty($_FILES['csv_file']['tmp_name'])) {
            require_once plugin_dir_path(__FILE__) . 'import-listings.php';
            import_listings_batch($_FILES['csv_file']['tmp_name']);
            echo '<div class="updated"><p>Import completed.</p></div>';
        } else {
            echo '<div class="error"><p>Please select a file to import.</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h1>Import Large Listings</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="csv_file" accept=".csv">
            <input type="submit" name="import_listings" value="Import" class="button button-primary">
        </form>
    </div>
    <?php
}