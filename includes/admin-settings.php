<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function ydp_add_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=directory_listing',
        'Related Listings Settings',
        'Related Listings',
        'manage_options',
        'ydp_related_listings_settings',
        'ydp_related_listings_settings_page'
    );
}
add_action('admin_menu', 'ydp_add_admin_menu');

function ydp_related_listings_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ydp_options_group');
            do_settings_sections('ydp_related_listings_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function ydp_settings_init() {
    register_setting('ydp_options_group', 'ydp_related_listings_enabled');
    register_setting('ydp_options_group', 'ydp_related_listings_count', array(
        'type' => 'integer',
        'sanitize_callback' => 'absint',
        'default' => 3,
    ));

    add_settings_section(
        'ydp_related_listings_section',
        'Related Listings Options',
        'ydp_related_listings_section_callback',
        'ydp_related_listings_settings'
    );

    add_settings_field(
        'ydp_related_listings_enabled',
        'Enable Related Listings',
        'ydp_related_listings_enabled_callback',
        'ydp_related_listings_settings',
        'ydp_related_listings_section'
    );

    add_settings_field(
        'ydp_related_listings_count',
        'Number of Related Listings',
        'ydp_related_listings_count_callback',
        'ydp_related_listings_settings',
        'ydp_related_listings_section'
    );
}
add_action('admin_init', 'ydp_settings_init');

function ydp_related_listings_section_callback() {
    echo '<p>Configure settings for related listings display.</p>';
}

function ydp_related_listings_enabled_callback() {
    $enabled = get_option('ydp_related_listings_enabled', 1);
    echo '<input type="checkbox" name="ydp_related_listings_enabled" value="1" ' . checked(1, $enabled, false) . '/>';
}

function ydp_related_listings_count_callback() {
    $count = get_option('ydp_related_listings_count', 3);
    echo '<input type="number" name="ydp_related_listings_count" value="' . esc_attr($count) . '" min="1" max="10" />';
}