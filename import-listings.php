<?php
// File: your-plugin-folder/import-listings.php

function import_listings_batch($file_path, $batch_size = 1000) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'large_listings';

    $file = fopen($file_path, 'r');
    $headers = fgetcsv($file);
    $wpdb->query('START TRANSACTION');

    $count = 0;
    while (($line = fgetcsv($file)) !== FALSE) {
        $data = array_combine($headers, $line);
        $wpdb->insert($table_name, $data);

        if (++$count % $batch_size == 0) {
            $wpdb->query('COMMIT');
            $wpdb->query('START TRANSACTION');
            echo "Imported $count records...\n";
        }
    }

    $wpdb->query('COMMIT');
    fclose($file);
    echo "Import completed. Total records: $count\n";
}

// Usage (you can run this from WP-CLI or create an admin page to trigger the import)
// import_listings_batch('/path/to/your/csv/file.csv');