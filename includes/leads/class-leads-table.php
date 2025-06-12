<?php
if (!defined('ABSPATH')) exit;

class Directory_Listings_Leads_Table {
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'directory_leads';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            listing_id bigint(20) NOT NULL,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20),
            service_requested varchar(255),
            message text,
            status varchar(20) DEFAULT 'new',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            sold_at datetime DEFAULT NULL,
            price decimal(10,2) DEFAULT NULL,
            source varchar(50) DEFAULT 'form',
            lead_owner bigint(20) DEFAULT NULL,
            notes text,
            PRIMARY KEY  (id),
            KEY listing_id (listing_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}