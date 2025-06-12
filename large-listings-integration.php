<?php
// File: your-plugin-folder/large-listings-integration.php

// Create the large listings table on plugin activation
function create_large_listings_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'large_listings';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        company_name varchar(255) NOT NULL,
        legal_name varchar(255),
        trade_name varchar(255),
        dba_name varchar(255),
        fictitious_name varchar(255),
        address1 varchar(255),
        address2 varchar(255),
        city varchar(100),
        state varchar(50),
        zip5 varchar(5),
        zip4 varchar(4),
        phone varchar(20),
        email varchar(100),
        industry varchar(100),
        naics_code varchar(10),
        sic_code varchar(10),
        website varchar(255),
        contact_name varchar(100),
        contact_title varchar(100),
        contact_phone varchar(20),
        contact_email varchar(100),
        employees varchar(50),
        sales_volume varchar(50),
        year_established varchar(4),
        latitude decimal(10,8),
        longitude decimal(11,8),
        PRIMARY KEY (id),
        KEY idx_company_name (company_name),
        KEY idx_city (city),
        KEY idx_state (state),
        KEY idx_industry (industry),
        KEY idx_location (latitude, longitude)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Custom search function for Ajax Pro Search
function large_listings_custom_search($keyword, $args) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'large_listings';

    $limit = isset($args['posts_per_page']) ? intval($args['posts_per_page']) : 10;
    $offset = isset($args['offset']) ? intval($args['offset']) : 0;

    $where_clauses = [];
    $where_values = [];

    if (!empty($keyword)) {
        $where_clauses[] = "(company_name LIKE %s OR legal_name LIKE %s OR trade_name LIKE %s)";
        $where_values = array_fill(0, 3, '%' . $wpdb->esc_like($keyword) . '%');
    }

    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

    $query = $wpdb->prepare(
        "SELECT SQL_CALC_FOUND_ROWS * FROM $table_name $where_sql LIMIT %d OFFSET %d",
        array_merge($where_values, [$limit, $offset])
    );

    $results = $wpdb->get_results($query);
    $total = $wpdb->get_var("SELECT FOUND_ROWS()");

    $formatted_results = array_map(function($item) {
        return [
            'id' => $item->id,
            'title' => $item->company_name,
            'content' => $item->legal_name . ' ' . $item->trade_name,
            'url' => home_url('/listing/' . $item->id),
        ];
    }, $results);

    return [
        'results' => $formatted_results,
        'total' => $total,
    ];
}

// Hook the custom search into Ajax Pro Search
add_filter('asp_custom_search', 'large_listings_asp_custom_search', 10, 3);

function large_listings_asp_custom_search($return, $keyword, $args) {
    // Check if this is the search instance we want to modify
    if ($args['search_id'] == 1) { // Adjust this ID as needed
        return large_listings_custom_search($keyword, $args);
    }
    return $return;
}