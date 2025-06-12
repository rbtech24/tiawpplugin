<?php
/**
 * Plugin Name: TIA Directory Listings
 * Plugin URI: https://example.com/directory-listings-plugin
 * Description: Sets up custom post types, taxonomies, and ACF fields for a directory website.
 * Version: 5.9
 * Author: Rod Bartruff
 * Author URI: https://topsinamerica.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: directory-listings
 * Domain Path: /languages
 */



// ==========================================
// 1. PLUGIN INITIALIZATION
// ==========================================

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('DIRECTORY_LISTINGS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DIRECTORY_LISTINGS_PLUGIN_URL', plugin_dir_url(__FILE__));





// Include the admin page file
$admin_page_file = DIRECTORY_LISTINGS_PLUGIN_PATH . 'includes/admin-page.php';
if (file_exists($admin_page_file)) {
    require_once $admin_page_file;
} else {
    error_log('Directory Listings: Admin page file not found: ' . $admin_page_file);
}
// Add menu items
if (!function_exists('add_directory_listings_menu')) {
    function add_directory_listings_menu() {
        if (!function_exists('directory_listings_admin_page')) {
            error_log('Directory Listings: directory_listings_admin_page function not found');
            return;
        }

        add_menu_page(
            'Directory Listings Dashboard',
            'Directory Listings',
            'manage_options',
            'directory-listings-dashboard',
            'directory_listings_admin_page',
            'dashicons-admin-home',
            20
        );
        
        add_submenu_page('directory-listings-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'directory-listings-dashboard');
        add_submenu_page('directory-listings-dashboard', 'Manage Listings', 'Manage Listings', 'manage_options', 'edit.php?post_type=directory_listing');
        add_submenu_page('directory-listings-dashboard', 'Add New Listing', 'Add New Listing', 'manage_options', 'post-new.php?post_type=directory_listing');
        add_submenu_page('directory-listings-dashboard', 'Manage Restaurants', 'Manage Restaurants', 'manage_options', 'edit.php?post_type=restaurant');
        add_submenu_page('directory-listings-dashboard', 'Add New Restaurant', 'Add New Restaurant', 'manage_options', 'post-new.php?post_type=restaurant');
        add_submenu_page('directory-listings-dashboard', 'Manage Cities', 'Manage Cities', 'manage_options', 'edit.php?post_type=city');
        add_submenu_page('directory-listings-dashboard', 'Add New City', 'Add New City', 'manage_options', 'post-new.php?post_type=city');
        add_submenu_page('directory-listings-dashboard', 'Q&A Section', 'Q&A Section', 'manage_options', 'edit.php?post_type=qa_post');

        if (function_exists('display_reviews_management_page')) {
            add_submenu_page('directory-listings-dashboard', 'Manage Reviews', 'Manage Reviews', 'manage_options', 'manage-reviews', 'display_reviews_management_page');
        }

        if (function_exists('display_restaurant_reviews_management_page')) {
            add_submenu_page('directory-listings-dashboard', 'Restaurant Reviews', 'Restaurant Reviews', 'manage_options', 'manage-restaurant-reviews', 'display_restaurant_reviews_management_page');
        }
    }
}
add_action('admin_menu', 'add_directory_listings_menu');

if (!function_exists('remove_old_directory_menu_items')) {
    function remove_old_directory_menu_items() {
        remove_menu_page('edit.php?post_type=directory_listing');
        remove_menu_page('edit.php?post_type=restaurant');
        remove_menu_page('edit.php?post_type=city');
        remove_menu_page('edit.php?post_type=qa_post');
    }
}
add_action('admin_menu', 'remove_old_directory_menu_items', 999);

if (!function_exists('modify_post_type_args')) {
    function modify_post_type_args($args, $post_type) {
        if (in_array($post_type, array('directory_listing', 'restaurant', 'city', 'qa_post'))) {
            $args['show_in_menu'] = false;
        }
        return $args;
    }
}






add_filter('register_post_type_args', 'modify_post_type_args', 10, 2);

if (!function_exists('directory_listings_admin_css')) {
    function directory_listings_admin_css() {
        echo '
        <style>
            #adminmenu .toplevel_page_directory-listings-dashboard .wp-submenu li a {
                background-color: #ffffff;
                color: #1e3a8a !important;
            }
            #adminmenu .toplevel_page_directory-listings-dashboard .wp-submenu li a:hover {
                background-color: #e63946;
                color: #ffffff !important;
            }
        </style>';
    }
}
add_action('admin_head', 'directory_listings_admin_css');

// Add activation and deactivation hooks
if (!function_exists('directory_listings_activate')) {
    function directory_listings_activate() {
        // Perform any necessary setup on activation
        if (function_exists('create_directory_listing_post_type')) create_directory_listing_post_type();
        if (function_exists('create_directory_taxonomies')) create_directory_taxonomies();
        if (function_exists('create_restaurant_post_type')) create_restaurant_post_type();
        if (function_exists('create_restaurant_taxonomies')) create_restaurant_taxonomies();
        if (function_exists('create_city_post_type')) create_city_post_type();
        if (function_exists('add_directory_listing_capabilities')) add_directory_listing_capabilities();
        if (function_exists('create_restaurant_reviews_table')) create_restaurant_reviews_table();
        flush_rewrite_rules();
    }
}
register_activation_hook(__FILE__, 'directory_listings_activate');

if (!function_exists('directory_listings_deactivate')) {
    function directory_listings_deactivate() {
        // Perform any necessary cleanup on deactivation
        if (function_exists('remove_directory_listing_capabilities')) remove_directory_listing_capabilities();
        flush_rewrite_rules();
    }
}
register_deactivation_hook(__FILE__, 'directory_listings_deactivate');

// Initialize the plugin
if (!function_exists('directory_listings_init')) {
    function directory_listings_init() {
        // Register post types and taxonomies
        if (function_exists('create_directory_listing_post_type')) {
            create_directory_listing_post_type();
        } else {
            error_log('Directory Listings: create_directory_listing_post_type function not found');
        }

        if (function_exists('create_directory_taxonomies')) {
            create_directory_taxonomies();
        } else {
            error_log('Directory Listings: create_directory_taxonomies function not found');
        }

        if (function_exists('create_restaurant_post_type')) {
            create_restaurant_post_type();
        } else {
            error_log('Directory Listings: create_restaurant_post_type function not found');
        }

        if (function_exists('create_restaurant_taxonomies')) {
            create_restaurant_taxonomies();
        } else {
            error_log('Directory Listings: create_restaurant_taxonomies function not found');
        }

        if (function_exists('create_city_post_type')) {
            create_city_post_type();
        } else {
            error_log('Directory Listings: create_city_post_type function not found');
        }

        // Add capabilities
        if (function_exists('add_directory_listing_capabilities')) {
            add_directory_listing_capabilities();
        } else {
            error_log('Directory Listings: add_directory_listing_capabilities function not found');
        }
    }
}
add_action('init', 'directory_listings_init');

// Register home page template
add_action('plugins_loaded', 'register_directory_home_template');

if (!function_exists('register_directory_home_template')) {
    function register_directory_home_template() {
        add_filter('theme_page_templates', 'add_directory_home_template');
        add_filter('template_include', 'load_directory_home_template');
    }
}

if (!function_exists('add_directory_home_template')) {
    function add_directory_home_template($templates) {
        $templates['page-home.php'] = 'Directory Home Page';
        return $templates;
    }
}

if (!function_exists('load_directory_home_template')) {
    function load_directory_home_template($template) {
        if (is_page_template('page-home.php')) {
            $file = plugin_dir_path(__FILE__) . 'templates/page-home.php';
            if (file_exists($file)) {
                return $file;
            } else {
                error_log('Directory Listings: Home Page template file not found: ' . $file);
            }
        }
        return $template;
    }
}




// Remove page title
function directory_listings_remove_page_title() {
    // Remove title from wp_head
    remove_action('wp_head', '_wp_render_title_tag', 1);
    add_filter('the_title', 'directory_listings_disable_page_title', 10, 2);
    add_filter('document_title_parts', 'directory_listings_modify_doc_title_parts');
    add_filter('show_admin_bar', '__return_false'); // Optional: removes admin bar
}
add_action('init', 'directory_listings_remove_page_title');

function directory_listings_disable_page_title($title, $id = null) {
    if (is_page() && in_the_loop() && is_main_query()) {
        return '';
    }
    return $title;
}

function directory_listings_modify_doc_title_parts($title_parts) {
    if (is_page()) {
        unset($title_parts['title']);
    }
    return $title_parts;
}

// Remove page title from templates
function directory_listings_remove_elementor_page_title() {
    if (did_action('elementor/loaded')) {
        add_filter('elementor/page_templates/canvas/template', function($template) {
            remove_action('wp_head', '_wp_render_title_tag', 1);
            remove_action('elementor/page_templates/canvas/before_content', 'the_title');
            return $template;
        });
    }
}
add_action('wp', 'directory_listings_remove_elementor_page_title');

// Add CSS to hide title
function directory_listings_hide_title_css() {
    echo '<style>
        .entry-title, .page-title { 
            display: none !important; 
        }
    </style>';
}
add_action('wp_head', 'directory_listings_hide_title_css');



function enqueue_location_filter_scripts() {
    if (is_tax('business_category') || is_tax('state')) {
        wp_enqueue_script(
            'directory-location-filters',
            plugins_url('includes/location-filters.js', __FILE__),
            array('jquery'),
            '1.0',
            true
        );

        wp_localize_script('directory-location-filters', 'directory_listings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('get_cities_nonce'),
            'texts' => array(
                'all_cities' => __('All Cities', 'directory-listings')
            )
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_location_filter_scripts');


// ==========================================
// 2. URL REWRITING AND QUERY VARIABLES
// ==========================================

function custom_query_vars($vars) {
    $vars[] = 'city';
    $vars[] = 'state';
    return $vars;
}
add_filter('query_vars', 'custom_query_vars');

function add_custom_rewrite_rule() {
    add_rewrite_rule('biz-category/([^/]+)/?state=([^&]+)&city=([^&]+)?', 'index.php?business_category=$matches[1]&state=$matches[2]&city=$matches[3]', 'top');
}
add_action('init', 'add_custom_rewrite_rule', 100); // Use a higher priority number

// ==========================================
// 3. CUSTOM POST TYPE AND TAXONOMY CREATION
// ==========================================

if (!function_exists('create_directory_listing_post_type')) {
    function create_directory_listing_post_type() {
        $labels = array(
            'name'               => _x('Directory Listings', 'post type general name', 'directory-listings'),
            'singular_name'      => _x('Directory Listing', 'post type singular name', 'directory-listings'),
            'menu_name'          => _x('Directory Listings', 'admin menu', 'directory-listings'),
            'name_admin_bar'     => _x('Directory Listing', 'add new on admin bar', 'directory-listings'),
            'add_new'            => _x('Add New', 'directory listing', 'directory-listings'),
            'add_new_item'       => __('Add New Directory Listing', 'directory-listings'),
            'new_item'           => __('New Directory Listing', 'directory-listings'),
            'edit_item'          => __('Edit Directory Listing', 'directory-listings'),
            'view_item'          => __('View Directory Listing', 'directory-listings'),
            'all_items'          => __('All Directory Listings', 'directory-listings'),
            'search_items'       => __('Search Directory Listings', 'directory-listings'),
            'parent_item_colon'  => __('Parent Directory Listings:', 'directory-listings'),
            'not_found'          => __('No directory listings found.', 'directory-listings'),
            'not_found_in_trash' => __('No directory listings found in Trash.', 'directory-listings')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'listing'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            'menu_icon'          => 'dashicons-building'
        );

        register_post_type('directory_listing', $args);
    }
}

if (!function_exists('create_restaurant_post_type')) {
    function create_restaurant_post_type() {
        $labels = array(
            'name'               => _x('Restaurants', 'post type general name', 'directory-listings'),
            'singular_name'      => _x('Restaurant', 'post type singular name', 'directory-listings'),
            'menu_name'          => _x('Restaurants', 'admin menu', 'directory-listings'),
            'name_admin_bar'     => _x('Restaurant', 'add new on admin bar', 'directory-listings'),
            'add_new'            => _x('Add New', 'restaurant', 'directory-listings'),
            'add_new_item'       => __('Add New Restaurant', 'directory-listings'),
            'new_item'           => __('New Restaurant', 'directory-listings'),
            'edit_item'          => __('Edit Restaurant', 'directory-listings'),
            'view_item'          => __('View Restaurant', 'directory-listings'),
            'all_items'          => __('All Restaurants', 'directory-listings'),
            'search_items'       => __('Search Restaurants', 'directory-listings'),
            'parent_item_colon'  => __('Parent Restaurants:', 'directory-listings'),
            'not_found'          => __('No restaurants found.', 'directory-listings'),
            'not_found_in_trash' => __('No restaurants found in Trash.', 'directory-listings')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'restaurant'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            'menu_icon'          => 'dashicons-food'
        );

        register_post_type('restaurant', $args);
    }
}

if (!function_exists('create_city_post_type')) {
    function create_city_post_type() {
        $labels = array(
            'name'               => _x('Cities', 'post type general name', 'directory-listings'),
            'singular_name'      => _x('City', 'post type singular name', 'directory-listings'),
            'menu_name'          => _x('Cities', 'admin menu', 'directory-listings'),
            'name_admin_bar'     => _x('City', 'add new on admin bar', 'directory-listings'),
            'add_new'            => _x('Add New', 'city', 'directory-listings'),
            'add_new_item'       => __('Add New City', 'directory-listings'),
            'new_item'           => __('New City', 'directory-listings'),
            'edit_item'          => __('Edit City', 'directory-listings'),
            'view_item'          => __('View City', 'directory-listings'),
            'all_items'          => __('All Cities', 'directory-listings'),
            'search_items'       => __('Search Cities', 'directory-listings'),
            'parent_item_colon'  => __('Parent Cities:', 'directory-listings'),
            'not_found'          => __('No cities found.', 'directory-listings'),
            'not_found_in_trash' => __('No cities found in Trash.', 'directory-listings')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'city'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
            'menu_icon'          => 'dashicons-location-alt'
        );

        register_post_type('city', $args);
    }
}
if (!function_exists('create_directory_taxonomies')) {
    function create_directory_taxonomies() {
        // Business Category taxonomy
        register_taxonomy('business_category', 
            array('directory_listing'), 
            array(
                'hierarchical'      => true,
                'labels'            => array(
                    'name'              => _x('Business Categories', 'taxonomy general name', 'directory-listings'),
                    'singular_name'     => _x('Business Category', 'taxonomy singular name', 'directory-listings'),
                    'search_items'      => __('Search Business Categories', 'directory-listings'),
                    'all_items'         => __('All Business Categories', 'directory-listings'),
                    'parent_item'       => __('Parent Business Category', 'directory-listings'),
                    'parent_item_colon' => __('Parent Business Category:', 'directory-listings'),
                    'edit_item'         => __('Edit Business Category', 'directory-listings'),
                    'update_item'       => __('Update Business Category', 'directory-listings'),
                    'add_new_item'      => __('Add New Business Category', 'directory-listings'),
                    'new_item_name'     => __('New Business Category Name', 'directory-listings'),
                    'menu_name'         => __('Business Categories', 'directory-listings'),
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'biz-category'),
            )
        );

        // State taxonomy
        register_taxonomy('state', 
            array('directory_listing', 'restaurant', 'city'), 
            array(
                'hierarchical'      => true,
                'labels'            => array(
                    'name'              => _x('States', 'taxonomy general name', 'directory-listings'),
                    'singular_name'     => _x('State', 'taxonomy singular name', 'directory-listings'),
                    'search_items'      => __('Search States', 'directory-listings'),
                    'all_items'         => __('All States', 'directory-listings'),
                    'parent_item'       => __('Parent State', 'directory-listings'),
                    'parent_item_colon' => __('Parent State:', 'directory-listings'),
                    'edit_item'         => __('Edit State', 'directory-listings'),
                    'update_item'       => __('Update State', 'directory-listings'),
                    'add_new_item'      => __('Add New State', 'directory-listings'),
                    'new_item_name'     => __('New State Name', 'directory-listings'),
                    'menu_name'         => __('States', 'directory-listings'),
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'state'),
            )
        );

        // Add City taxonomy
// In create_directory_taxonomies()
register_taxonomy('city',
    array('directory_listing', 'restaurant'),
    array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'              => _x('City Filter', 'taxonomy general name', 'directory-listings'),
            'singular_name'     => _x('City Filter', 'taxonomy singular name', 'directory-listings'),
            'search_items'      => __('Search Cities', 'directory-listings'),
            'all_items'         => __('All Cities', 'directory-listings'),
            'parent_item'       => __('Parent City', 'directory-listings'),
            'parent_item_colon' => __('Parent City:', 'directory-listings'),
            'edit_item'         => __('Edit City', 'directory-listings'),
            'update_item'       => __('Update City', 'directory-listings'),
            'add_new_item'      => __('Add New City', 'directory-listings'),
            'new_item_name'     => __('New City Name', 'directory-listings'),
            'menu_name'         => __('City Filters', 'directory-listings'),
        ),
        'show_ui'           => true,
        'show_admin_column' => false, // Hide from main listing view
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'city-filter'),
    )
);
    }
}

if (!function_exists('create_restaurant_taxonomies')) {
    function create_restaurant_taxonomies() {
        // Cuisine Type Taxonomy
        $cuisine_args = array(
            'hierarchical'      => true,
            'labels'            => array(
                'name'              => _x('Cuisine Types', 'taxonomy general name', 'directory-listings'),
                'singular_name'     => _x('Cuisine Type', 'taxonomy singular name', 'directory-listings'),
                'search_items'      => __('Search Cuisine Types', 'directory-listings'),
                'all_items'         => __('All Cuisine Types', 'directory-listings'),
                'parent_item'       => __('Parent Cuisine Type', 'directory-listings'),
                'parent_item_colon' => __('Parent Cuisine Type:', 'directory-listings'),
                'edit_item'         => __('Edit Cuisine Type', 'directory-listings'),
                'update_item'       => __('Update Cuisine Type', 'directory-listings'),
                'add_new_item'      => __('Add New Cuisine Type', 'directory-listings'),
                'new_item_name'     => __('New Cuisine Type Name', 'directory-listings'),
                'menu_name'         => __('Cuisine Types', 'directory-listings'),
            ),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'cuisine'),
        );
        register_taxonomy('cuisine', array('restaurant'), $cuisine_args);

        // Price Range Taxonomy
        $price_range_args = array(
            'hierarchical'      => false,
            'labels'            => array(
                'name'              => _x('Price Ranges', 'taxonomy general name', 'directory-listings'),
                'singular_name'     => _x('Price Range', 'taxonomy singular name', 'directory-listings'),
                'search_items'      => __('Search Price Ranges', 'directory-listings'),
                'all_items'         => __('All Price Ranges', 'directory-listings'),
                'edit_item'         => __('Edit Price Range', 'directory-listings'),
                'update_item'       => __('Update Price Range', 'directory-listings'),
                'add_new_item'      => __('Add New Price Range', 'directory-listings'),
                'new_item_name'     => __('New Price Range Name', 'directory-listings'),
                'menu_name'         => __('Price Ranges', 'directory-listings'),
            ),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'price-range'),
        );
        register_taxonomy('price_range', array('restaurant'), $price_range_args);
    }
}

// Hook these functions to the init action
add_action('init', 'create_directory_listing_post_type');
add_action('init', 'create_restaurant_post_type');
add_action('init', 'create_city_post_type');
add_action('init', 'create_directory_taxonomies');
add_action('init', 'create_restaurant_taxonomies');


function sync_city_meta_to_taxonomy($post_id) {
    // Only run on listings and restaurants
    if (!in_array(get_post_type($post_id), array('directory_listing', 'restaurant'))) {
        return;
    }
    
    // Get city from meta
    $city_name = get_field('city', $post_id);
    if (!$city_name) {
        return;
    }
    
    // Check if city term exists
    $city_term = get_term_by('name', $city_name, 'city');
    if (!$city_term) {
        // Create new city term
        $city_term = wp_insert_term($city_name, 'city');
        if (is_wp_error($city_term)) {
            return;
        }
        $city_term = get_term($city_term['term_id'], 'city');
    }
    
    // Assign city term to post
    wp_set_object_terms($post_id, array($city_term->term_id), 'city');
}
add_action('acf/save_post', 'sync_city_meta_to_taxonomy', 20);

// Register State and City Custom Post Types
function create_location_post_types() {
    // State Custom Post Type
    register_post_type('state', array(
        'labels' => array(
            'name' => 'States',
            'singular_name' => 'State',
            'menu_name' => 'States',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New State',
            'edit_item' => 'Edit State',
            'new_item' => 'New State',
            'view_item' => 'View State',
            'search_items' => 'Search States',
            'not_found' => 'No states found',
            'not_found_in_trash' => 'No states found in trash'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => 'directory-listings-dashboard',
        'menu_position' => 20,
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'state'),
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'hierarchical' => false
    ));

    // City Custom Post Type
    register_post_type('city', array(
        'labels' => array(
            'name' => 'Cities',
            'singular_name' => 'City',
            'menu_name' => 'Cities',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New City',
            'edit_item' => 'Edit City',
            'new_item' => 'New City',
            'view_item' => 'View City',
            'search_items' => 'Search Cities',
            'not_found' => 'No cities found',
            'not_found_in_trash' => 'No cities found in trash'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => 'directory-listings-dashboard',
        'menu_position' => 21,
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'city'),
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'hierarchical' => false
    ));
}
add_action('init', 'create_location_post_types');

// Create initial states on plugin activation
function create_initial_states() {
    $states = array(
        'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
        'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia',
        'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa',
        'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland',
        'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri',
        'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey',
        'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio',
        'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina',
        'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
        'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
    );

    foreach ($states as $state) {
        // Check if state already exists
        $existing_state = get_page_by_title($state, OBJECT, 'state');
        
        if (!$existing_state) {
            // Create the state
            wp_insert_post(array(
                'post_title' => $state,
                'post_type' => 'state',
                'post_status' => 'publish',
                'post_content' => '',
                'meta_input' => array(
                    'state_abbreviation' => get_state_abbreviation($state)
                )
            ));
        }
    }
}

// Function to get state abbreviation
function get_state_abbreviation($state_name) {
    $state_abbreviations = array(
        'Alabama' => 'AL', 'Alaska' => 'AK', 'Arizona' => 'AZ', 'Arkansas' => 'AR',
        'California' => 'CA', 'Colorado' => 'CO', 'Connecticut' => 'CT', 'Delaware' => 'DE',
        'Florida' => 'FL', 'Georgia' => 'GA', 'Hawaii' => 'HI', 'Idaho' => 'ID',
        'Illinois' => 'IL', 'Indiana' => 'IN', 'Iowa' => 'IA', 'Kansas' => 'KS',
        'Kentucky' => 'KY', 'Louisiana' => 'LA', 'Maine' => 'ME', 'Maryland' => 'MD',
        'Massachusetts' => 'MA', 'Michigan' => 'MI', 'Minnesota' => 'MN', 'Mississippi' => 'MS',
        'Missouri' => 'MO', 'Montana' => 'MT', 'Nebraska' => 'NE', 'Nevada' => 'NV',
        'New Hampshire' => 'NH', 'New Jersey' => 'NJ', 'New Mexico' => 'NM', 'New York' => 'NY',
        'North Carolina' => 'NC', 'North Dakota' => 'ND', 'Ohio' => 'OH', 'Oklahoma' => 'OK',
        'Oregon' => 'OR', 'Pennsylvania' => 'PA', 'Rhode Island' => 'RI', 'South Carolina' => 'SC',
        'South Dakota' => 'SD', 'Tennessee' => 'TN', 'Texas' => 'TX', 'Utah' => 'UT',
        'Vermont' => 'VT', 'Virginia' => 'VA', 'Washington' => 'WA', 'West Virginia' => 'WV',
        'Wisconsin' => 'WI', 'Wyoming' => 'WY'
    );
    
    return isset($state_abbreviations[$state_name]) ? $state_abbreviations[$state_name] : '';
}

// Auto-create city when a listing is created/updated
function auto_create_city($post_id) {
    // Only run for directory listings and restaurants
    if (!in_array(get_post_type($post_id), array('directory_listing', 'restaurant'))) {
        return;
    }

    // Get city and state from the listing
    $city_name = get_field('city', $post_id);
    $state_name = get_field('state', $post_id);

    if (!empty($city_name) && !empty($state_name)) {
        // Check if city already exists
        $existing_city = get_page_by_title($city_name, OBJECT, 'city');
        
        if (!$existing_city) {
            // Get state post
            $state = get_page_by_title($state_name, OBJECT, 'state');
            
            if ($state) {
                // Create the city
                $city_id = wp_insert_post(array(
                    'post_title' => $city_name,
                    'post_type' => 'city',
                    'post_status' => 'publish',
                    'post_content' => '',
                    'meta_input' => array(
                        'state_id' => $state->ID,
                        'state_name' => $state_name
                    )
                ));

                if ($city_id) {
                    // Create relationship between city and state
                    update_post_meta($city_id, 'state_id', $state->ID);
                }
            }
        }
    }
}
add_action('acf/save_post', 'auto_create_city', 20);

// Add custom meta boxes for cities to show state relationship
function add_city_meta_boxes() {
    add_meta_box(
        'city_state_info',
        'State Information',
        'render_city_state_meta_box',
        'city',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_city_meta_boxes');

function render_city_state_meta_box($post) {
    $state_id = get_post_meta($post->ID, 'state_id', true);
    $state = get_post($state_id);
    
    if ($state) {
        echo '<p><strong>State:</strong> ' . esc_html($state->post_title) . '</p>';
    }
}

// Register activation hook to create initial states
register_activation_hook(__FILE__, 'create_initial_states');

// Add custom columns to cities admin
function add_city_admin_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = $value;
            $new_columns['state'] = 'State';
        } else {
            $new_columns[$key] = $value;
        }
    }
    return $new_columns;
}
add_filter('manage_city_posts_columns', 'add_city_admin_columns');

function populate_city_admin_columns($column, $post_id) {
    if ($column === 'state') {
        $state_id = get_post_meta($post_id, 'state_id', true);
        if ($state_id) {
            $state = get_post($state_id);
            if ($state) {
                echo esc_html($state->post_title);
            }
        }
    }
}
add_action('manage_city_posts_custom_column', 'populate_city_admin_columns', 10, 2);


// ==========================================
// 4. AJAX HANDLERS
// ==========================================

// AJAX handler for loading subcategories
add_action('wp_ajax_load_subcategories', 'load_subcategories');
add_action('wp_ajax_nopriv_load_subcategories', 'load_subcategories');

function load_subcategories() {
    check_ajax_referer('directory_listing_nonce', 'nonce');

    $category_id = intval($_POST['category_id']);
    
    $subcategories = get_terms(array(
        'taxonomy' => 'business_category',
        'hide_empty' => false,
        'parent' => $category_id
    ));

    $subcategory_data = array_map(function($subcategory) {
        return array('id' => $subcategory->term_id, 'name' => $subcategory->name);
    }, $subcategories);

    wp_send_json_success($subcategory_data);
}

// AJAX handler for checking and adding cities
add_action('wp_ajax_check_and_add_city', 'check_and_add_city');
add_action('wp_ajax_nopriv_check_and_add_city', 'check_and_add_city');

function check_and_add_city() {
    check_ajax_referer('directory_listing_nonce', 'nonce');

    $city_name = sanitize_text_field($_POST['city_name']);
    $state_name = sanitize_text_field($_POST['state_name']);
    $place_id = sanitize_text_field($_POST['place_id']);

    // Check if state exists
    $state_term = get_term_by('name', $state_name, 'state');
    if (!$state_term) {
        // Create state if it doesn't exist
        $state_term = wp_insert_term($state_name, 'state');
        if (is_wp_error($state_term)) {
            wp_send_json_error(array('message' => 'Error creating state'));
            return;
        }
        $state_term = get_term_by('id', $state_term['term_id'], 'state');
    }

    // Check if city exists
    $city_term = get_term_by('name', $city_name, 'state');
    if (!$city_term) {
        // Create city if it doesn't exist
        $city_term = wp_insert_term($city_name, 'state', array('parent' => $state_term->term_id));
        if (is_wp_error($city_term)) {
            wp_send_json_error(array('message' => 'Error creating city'));
            return;
        }
        // Add Google Place ID as term meta
        add_term_meta($city_term['term_id'], 'google_place_id', $place_id, true);
    }

    wp_send_json_success(array('message' => 'City checked/added successfully'));
}

// AJAX handler for loading cities
add_action('wp_ajax_load_cities', 'load_cities');
add_action('wp_ajax_nopriv_load_cities', 'load_cities');

function load_cities() {
    check_ajax_referer('directory_search_nonce', 'nonce');
    $state_id = intval($_POST['state_id']);
    
    $cities = get_terms(array(
        'taxonomy' => 'state',
        'hide_empty' => false,
        'parent' => $state_id
    ));

    $output = '<option value="">' . __('Select a City', 'directory-listings') . '</option>';
    foreach ($cities as $city) {
        $output .= '<option value="' . esc_attr($city->term_id) . '">' . esc_html($city->name) . '</option>';
    }

    echo $output;
    wp_die();
}

// AJAX handler for loading subcategories (for search widget)
add_action('wp_ajax_load_search_subcategories', 'load_search_subcategories');
add_action('wp_ajax_nopriv_load_search_subcategories', 'load_search_subcategories');

function load_search_subcategories() {
    check_ajax_referer('directory_search_nonce', 'nonce');
    $category_id = intval($_POST['category_id']);
    
    $subcategories = get_terms(array(
        'taxonomy' => 'business_category',
        'hide_empty' => false,
        'parent' => $category_id
    ));

    $output = '<option value="">' . __('Select a Subcategory', 'directory-listings') . '</option>';
    foreach ($subcategories as $subcategory) {
        $output .= '<option value="' . esc_attr($subcategory->term_id) . '">' . esc_html($subcategory->name) . '</option>';
    }

    echo $output;
    wp_die();
}


// ==========================================
// 5. ENQUEUE SCRIPTS AND STYLES
// ==========================================

function directory_listings_enqueue_scripts() {
    wp_enqueue_style('directory-listings-form-review', plugins_url('assets/css/directory-listings-form.css', __FILE__));
    wp_enqueue_style('directory-listings-form-style', plugins_url('assets/css/directory-listings-review.css', __FILE__));
    wp_localize_script('directory-listings-script', 'directory_listings_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('directory_filter_nonce')
    ));

     wp_enqueue_script('directory-listings-form', plugins_url('assets/js/directory-listings-form.js', __FILE__), array('jquery'), '1.0', true);
        wp_localize_script('directory-listings-form', 'directory_form', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('directory_listings_form_nonce')
        ));
        
        
    // Check if we're on a page that needs the form styles and scripts
    if (is_page('submit-listing') || is_page('edit-listing') || 
        is_singular('directory_listing') || 
        has_shortcode(get_post()->post_content, 'directory_listing_form') ||
        has_shortcode(get_post()->post_content, 'edit_directory_listing_form')) {
        
       
  
        wp_enqueue_style('directory-listings-form-style', plugins_url('assets/css/directory-listings-form.css', __FILE__));
       
        wp_enqueue_script('directory-listings-form-script', plugins_url('assets/js/directory-listings-form.js', __FILE__), array('jquery'), '1.0.1', true);
        
        wp_localize_script('directory-listings-form-script', 'directory_listings_form', array(
           'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('directory_listing_nonce')
        ));

        // Add any edit-form-specific styles
        $edit_form_styles = "
            .current-logo img {
                max-width: 200px;
                height: auto;
            }
            
            .current-gallery img {
                max-width: 100px;
                height: auto;
                margin: 5px;
            }
        ";
        wp_add_inline_style('directory-listings-form-style', $edit_form_styles);
    }

    // Enqueue scripts and styles for restaurant pages
    if (is_singular('restaurant') || is_post_type_archive('restaurant') || is_tax('cuisine')) {
        wp_enqueue_style('restaurant-styles', plugins_url('assets/css/restaurant-styles.css', __FILE__));
        wp_enqueue_script('restaurant-scripts', plugins_url('assets/js/restaurant-scripts.js', __FILE__), array('jquery'), '1.0', true);
        wp_localize_script('restaurant-scripts', 'restaurant_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('restaurant_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'directory_listings_enqueue_scripts');

function enqueue_directory_search_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('directory-search-js', plugin_dir_url(__FILE__) . 'assets/js/directory-search.js', array('jquery'), '1.0', true);
    wp_localize_script('directory-search-js', 'directory_search_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('directory_search_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_directory_search_scripts');

function enqueue_restaurant_review_assets() {
    if (is_singular('restaurant')) {
        wp_enqueue_style('restaurant-review-styles', plugin_dir_url(__FILE__) . 'assets/css/restaurant-reviews.css');
        wp_enqueue_script('restaurant-review-script', plugin_dir_url(__FILE__) . 'assets/js/restaurant-reviews.js', array('jquery'), '1.0', true);
        wp_localize_script('restaurant-review-script', 'restaurantReviewsAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('restaurant_review_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_restaurant_review_assets');




// ==========================================
// 6. FORM SUBMISSION AND LISTING CREATION
// ==========================================

// Add AJAX handler for form submission
add_action('wp_ajax_submit_directory_listing', 'handle_directory_listing_submission');
add_action('wp_ajax_nopriv_submit_directory_listing', 'handle_directory_listing_submission');

function verify_directory_nonce() {
    if (!isset($_POST['directory_listing_nonce']) || !wp_verify_nonce($_POST['directory_listing_nonce'], 'directory_listing_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
        wp_die();
    }
}

function get_sanitized_listing_data() {
    return array(
        'listing_type' => isset($_POST['listing_type']) ? sanitize_text_field($_POST['listing_type']) : 'free',
        'first_name' => isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '',
        'last_name' => isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '',
        'business_name' => isset($_POST['business_name']) ? sanitize_text_field($_POST['business_name']) : '',
        'address' => isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '', // Changed from street_address
        // Google Places components
        'administrative_area_level_1' => isset($_POST['administrative_area_level_1']) ? sanitize_text_field($_POST['administrative_area_level_1']) : '',
        'locality' => isset($_POST['locality']) ? sanitize_text_field($_POST['locality']) : '',
        'postal_code' => isset($_POST['postal_code']) ? sanitize_text_field($_POST['postal_code']) : '',
        'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
        'phone' => isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '',
        'email' => isset($_POST['email']) ? sanitize_email($_POST['email']) : '',
        'category' => isset($_POST['category']) ? intval($_POST['category']) : 0,
        'subcategory' => isset($_POST['subcategory']) ? intval($_POST['subcategory']) : 0,
        'latitude' => isset($_POST['latitude']) ? sanitize_text_field($_POST['latitude']) : '',
        'longitude' => isset($_POST['longitude']) ? sanitize_text_field($_POST['longitude']) : '',
        'place_id' => isset($_POST['place_id']) ? sanitize_text_field($_POST['place_id']) : ''
    );
}

function validate_required_listing_fields($data) {
    $required_fields = array(
        'business_name' => 'Business Name',
        'address' => 'Address', // Changed from street_address
        'phone' => 'Phone Number',
        'email' => 'Email Address',
        'description' => 'Business Description',
        'category' => 'Business Category',
        'first_name' => 'First Name',
        'last_name' => 'Last Name'
    );

    foreach ($required_fields as $field => $label) {
        if (empty($data[$field])) {
            wp_send_json_error(array('message' => "Please fill in the {$label} field."));
            wp_die();
        }
    }

    if (!isset($_POST['terms_and_conditions']) || $_POST['terms_and_conditions'] !== 'on') {
        wp_send_json_error(array('message' => 'Please accept the Terms and Conditions.'));
        wp_die();
    }
}

function create_user_for_listing($email, $first_name, $last_name) {
    $username = sanitize_user(current(explode('@', $email)));
    $base_username = $username;
    $counter = 1;
    
    while (username_exists($username)) {
        $username = $base_username . $counter;
        $counter++;
    }
    
    $password = wp_generate_password();
    $user_id = wp_create_user($username, $password, $email);
    
    if (is_wp_error($user_id)) {
        wp_send_json_error(array('message' => 'Error creating user account: ' . $user_id->get_error_message()));
        wp_die();
    }
    
    wp_update_user(array(
        'ID' => $user_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'role' => 'subscriber'
    ));
    
    wp_new_user_notification($user_id, null, 'both');
    
    return $user_id;
}

function handle_directory_listing_submission() {
    try {
        verify_directory_nonce();
        
        $listing_data = get_sanitized_listing_data();
        validate_required_listing_fields($listing_data);

        // Create or get user
        $user = get_user_by('email', $listing_data['email']);
        $user_id = !$user ? create_user_for_listing($listing_data['email'], $listing_data['first_name'], $listing_data['last_name']) : $user->ID;

        // Create listing post
        $post_id = wp_insert_post([
            'post_title' => $listing_data['business_name'],
            'post_content' => $listing_data['description'],
            'post_status' => 'pending',
            'post_type' => 'directory_listing',
            'post_author' => $user_id
        ]);

        if (!$post_id || is_wp_error($post_id)) {
            wp_send_json_error(['message' => 'Error creating listing: ' . ($post_id->get_error_message() ?? 'Unknown error')]);
            wp_die();
        }
if(!empty($listing_data['listing_type']) && $listing_data['listing_type']=='paid'){
    
    $listing_type='paid';
}else{
     $listing_type='free'; 
}

        // Update standard meta fields
        $meta_fields = [
            'first_name' => $listing_data['first_name'],
            'last_name' => $listing_data['last_name'],
            'business_name' => $listing_data['business_name'],
            'phone_number' => $listing_data['phone'],
            'email' => $listing_data['email'],
            'listing_type' => $listing_type
        ];

        foreach ($meta_fields as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }

        // Update ACF fields if they exist
        if (function_exists('update_field')) {
            $acf_fields = [
                'street_address' => $listing_data['address'],
                'city' => $listing_data['locality'],
                'state' => $listing_data['administrative_area_level_1'],
                'zip_code' => $listing_data['postal_code'],
                'phone_number' => $listing_data['phone'],
                'email' => $listing_data['email'],
                'business_description' => $listing_data['description'],
                'first_name' => $listing_data['first_name'],
                'last_name' => $listing_data['last_name'],
                'latitude' => $listing_data['latitude'],
                'longitude' => $listing_data['longitude']
            ];

            foreach ($acf_fields as $key => $value) {
                update_field($key, $value, $post_id);
            }
        }

        // Handle business category
        if (!empty($listing_data['category'])) {
            wp_set_object_terms($post_id, intval($listing_data['category']), 'business_category');
        }

        // Handle state taxonomy
        if (!empty($listing_data['administrative_area_level_1'])) {
            $state = sanitize_text_field($listing_data['administrative_area_level_1']);
            wp_set_object_terms($post_id, $state, 'state');
        }

        // Handle city taxonomy
        if (!empty($listing_data['locality'])) {
            $city = sanitize_text_field($listing_data['locality']);
            wp_set_object_terms($post_id, $city, 'city');
        }

        // Try to send notifications
        try {
            send_listing_notification_emails($post_id, $listing_data['email']);
        } catch (Exception $e) {
            error_log('[Directory Form] Email error: ' . $e->getMessage());
        }

        wp_send_json_success([
            'message' => 'Listing submitted successfully! It will be reviewed shortly.',
            'post_id' => $post_id,
            'redirect_url' => home_url('/success/')
        ]);

    } catch (Exception $e) {
        error_log('[Directory Form] Fatal error: ' . $e->getMessage());
        wp_send_json_error(['message' => 'An error occurred: ' . $e->getMessage()]);
    }

    wp_die();
}

function handle_taxonomies($post_id, $listing_data) {
    // Handle business category
    if (!empty($listing_data['category'])) {
        $cat_result = wp_set_object_terms($post_id, intval($listing_data['category']), 'business_category');
        if (!is_wp_error($cat_result) && !empty($listing_data['subcategory'])) {
            wp_set_object_terms($post_id, intval($listing_data['subcategory']), 'business_category', true);
        }
    }

    // Handle state taxonomy
    if (!empty($listing_data['administrative_area_level_1'])) {
        $state = sanitize_text_field($listing_data['administrative_area_level_1']);
        $state_term = get_term_by('name', $state, 'state');
        
        if (!$state_term) {
            $new_state = wp_insert_term($state, 'state');
            if (!is_wp_error($new_state)) {
                $state_term = get_term_by('id', $new_state['term_id'], 'state');
            }
        }

        if ($state_term && !is_wp_error($state_term)) {
            wp_set_object_terms($post_id, $state_term->term_id, 'state');
        }
    }

    // Handle city taxonomy
    if (!empty($listing_data['locality'])) {
        $city = sanitize_text_field($listing_data['locality']);
        $city_term = get_term_by('name', $city, 'city');
        
        if (!$city_term) {
            $new_city = wp_insert_term($city, 'city');
            if (!is_wp_error($new_city)) {
                $city_term = get_term_by('id', $new_city['term_id'], 'city');
            }
        }

        if ($city_term && !is_wp_error($city_term)) {
            wp_set_object_terms($post_id, $city_term->term_id, 'city');
        }
    }

    // Log any taxonomy errors
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $term_assignments = get_object_terms($post_id, ['business_category', 'state', 'city']);
        if (empty($term_assignments) || is_wp_error($term_assignments)) {
            error_log('Error assigning terms to listing ' . $post_id . ': ' . print_r($term_assignments, true));
        }
    }
}

function update_core_fields($post_id, $listing_data) {
    $acf_fields = array(
        'listing_type' => $listing_data['listing_type'],
        'first_name' => $listing_data['first_name'],
        'last_name' => $listing_data['last_name'],
        'street_address' => $listing_data['street_address'],
        'city' => $listing_data['city'],
        'state' => $listing_data['state'],
        'zip_code' => $listing_data['zip'],
        'phone_number' => $listing_data['phone'],
        'email' => $listing_data['email'],
        'business_description' => $listing_data['description'],
        'latitude' => $listing_data['latitude'],
        'longitude' => $listing_data['longitude'],
        'place_id' => $listing_data['place_id']
    );

    foreach ($acf_fields as $key => $value) {
        update_field($key, $value, $post_id);
    }

    // Handle services if present
    if (isset($_POST['services'])) {
        $services = json_decode(stripslashes($_POST['services']), true);
        $formatted_services = array_map(function($service) {
            return array(
                'service_name' => sanitize_text_field($service['name']),
                'service_description' => sanitize_textarea_field($service['description'])
            );
        }, $services);
        update_field('services_offered', $formatted_services, $post_id);
    }
}

function handle_paid_listing_fields($post_id) {
    // Website
    if (isset($_POST['website'])) {
        update_field('website_link', esc_url_raw($_POST['website']), $post_id);
    }

    // Logo
    if (!empty($_FILES['logo'])) {
        $logo_id = media_handle_upload('logo', $post_id);
        if (!is_wp_error($logo_id)) {
            update_field('logo', $logo_id, $post_id);
        }
    }

    // Gallery
    if (!empty($_FILES['gallery'])) {
        $gallery_ids = handle_gallery_uploads($post_id);
        if (!empty($gallery_ids)) {
            update_field('photo_gallery', $gallery_ids, $post_id);
        }
    }

    // Business Hours
    if (isset($_POST['business_hours'])) {
        handle_business_hours($post_id);
    }

    // Social Media Links
    if (isset($_POST['social_links'])) {
        handle_social_links($post_id);
    }

    // Service Area
    if (isset($_POST['service_area_type'])) {
        update_field('service_area', array(
            'type' => sanitize_text_field($_POST['service_area_type']),
            'areas' => isset($_POST['service_area_list']) ? sanitize_textarea_field($_POST['service_area_list']) : ''
        ), $post_id);
    }
}

function handle_gallery_uploads($post_id) {
    $gallery_ids = array();
    $gallery_files = $_FILES['gallery'];
    
    for ($i = 0; $i < count($gallery_files['name']); $i++) {
        if ($gallery_files['error'][$i] === 0) {
            $gallery_item = array(
                'name' => $gallery_files['name'][$i],
                'type' => $gallery_files['type'][$i],
                'tmp_name' => $gallery_files['tmp_name'][$i],
                'error' => $gallery_files['error'][$i],
                'size' => $gallery_files['size'][$i]
            );
            $_FILES = array('gallery_item' => $gallery_item);
            $gallery_id = media_handle_upload('gallery_item', $post_id);
            if (!is_wp_error($gallery_id)) {
                $gallery_ids[] = $gallery_id;
            }
        }
    }
    
    return $gallery_ids;
}

function handle_business_hours($post_id) {
    $business_hours = json_decode(stripslashes($_POST['business_hours']), true);
    $days_of_week = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
    
    $formatted_hours = array_map(function($day) use ($business_hours) {
        $hours = isset($business_hours[$day]) ? $business_hours[$day] : array('open' => '0');
        return array(
            'day_of_week' => $day,
            'is_open' => isset($hours['open']) && $hours['open'] === '1',
            'opening_time' => isset($hours['open_time']) ? sanitize_text_field($hours['open_time']) : '',
            'closing_time' => isset($hours['close_time']) ? sanitize_text_field($hours['close_time']) : ''
        );
    }, $days_of_week);
    
    update_field('business_hours', $formatted_hours, $post_id);
}

function handle_social_links($post_id) {
    $social_links = json_decode(stripslashes($_POST['social_links']), true);
    $formatted_links = array(
        'facebook' => '',
        'google' => '',
        'yelp' => '',
        'thumbtack' => '',
        'other_urls' => array()
    );

    foreach ($social_links as $link) {
        $url = esc_url_raw($link['url']);
        $label = strtolower(sanitize_text_field($link['label']));
        
        if (strpos($label, 'facebook') !== false) {
            $formatted_links['facebook'] = $url;
        } elseif (strpos($label, 'google') !== false) {
            $formatted_links['google'] = $url;
        } elseif (strpos($label, 'yelp') !== false) {
            $formatted_links['yelp'] = $url;
        } elseif (strpos($label, 'thumbtack') !== false) {
            $formatted_links['thumbtack'] = $url;
        } else {
            $formatted_links['other_urls'][] = array(
                'url' => $url,
                'label' => $link['label']
            );
        }
    }

    update_field('social_media_links', $formatted_links, $post_id);
}

// Email Notification Functions
function send_listing_notification_emails($post_id, $user_email) {
    $business_name = get_the_title($post_id);
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');

    // Email to user
    $user_subject = "Your listing for $business_name has been submitted";
    $user_message = "Thank you for submitting your listing for $business_name to $site_name. Your listing is currently under review and will be published soon.";
    $result = wp_mail($user_email, $user_subject, $user_message);
    
    if (!$result) {
        error_log("Failed to send user notification email for listing $post_id");
    }

    // Email to admin
    $admin_subject = "New listing submitted: $business_name";
    $admin_message = "A new listing has been submitted for $business_name. Please review and publish it.\n\n";
    $admin_message .= "View listing: " . admin_url("post.php?post=$post_id&action=edit");
    $result = wp_mail($admin_email, $admin_subject, $admin_message);
    
    if (!$result) {
        error_log("Failed to send admin notification email for listing $post_id");
    }

    // Email to rodbartruff@gmail.com
    $rod_email = 'rodbartruff@gmail.com';
    $rod_subject = "Copy: New listing submitted: $business_name";
    $rod_message = "A new listing has been submitted for $business_name. This is a copy of the notification sent to the admin.";
    $result = wp_mail($rod_email, $rod_subject, $rod_message);
    
    if (!$result) {
        error_log("Failed to send Rod's notification email for listing $post_id");
    }

    return true;
}

// Get listing specific data for emails
function get_listing_details($post_id) {
    $listing_details = array(
        'business_name' => get_the_title($post_id),
        'listing_type' => get_field('listing_type', $post_id),
        'address' => get_field('street_address', $post_id),
        'city' => get_field('city', $post_id),
        'state' => get_field('state', $post_id),
        'zip' => get_field('zip_code', $post_id),
        'phone' => get_field('phone_number', $post_id),
        'email' => get_field('email', $post_id),
        'description' => wp_trim_words(get_field('business_description', $post_id), 50)
    );

    return $listing_details;
}

// Format email message
function format_listing_email($listing_details) {
    $message = "Business Details:\n\n";
    $message .= "Business Name: " . $listing_details['business_name'] . "\n";
    $message .= "Listing Type: " . ucfirst($listing_details['listing_type']) . "\n";
    $message .= "Address: " . $listing_details['address'] . "\n";
    $message .= "City: " . $listing_details['city'] . "\n";
    $message .= "State: " . $listing_details['state'] . "\n";
    $message .= "ZIP: " . $listing_details['zip'] . "\n";
    $message .= "Phone: " . $listing_details['phone'] . "\n";
    $message .= "Email: " . $listing_details['email'] . "\n\n";
    $message .= "Description:\n" . $listing_details['description'] . "\n";

    return $message;
}

// Error Logging Function
function directory_listings_log_error($message, $data = array()) {
    if (WP_DEBUG === true) {
        $log_entry = date('Y-m-d H:i:s') . " - " . $message;
        if (!empty($data)) {
            $log_entry .= "\nData: " . print_r($data, true);
        }
        error_log($log_entry . "\n", 3, WP_CONTENT_DIR . '/directory-listings-debug.log');
    }
}

// Cleanup old draft listings
function cleanup_old_draft_listings() {
    $args = array(
        'post_type' => 'directory_listing',
        'post_status' => 'draft',
        'date_query' => array(
            array(
                'before' => '30 days ago',
                'inclusive' => true,
            ),
        ),
        'posts_per_page' => -1,
        'fields' => 'ids',
    );

    $old_drafts = get_posts($args);

    foreach ($old_drafts as $post_id) {
        wp_delete_post($post_id, true);
    }
}
add_action('wp_scheduled_delete', 'cleanup_old_draft_listings');

// Handle form errors and validation messages
function directory_listings_get_error_message($error_code) {
    $error_messages = array(
        'nonce_failed' => 'Security check failed. Please refresh the page and try again.',
        'missing_required' => 'Please fill in all required fields.',
        'invalid_email' => 'Please enter a valid email address.',
        'invalid_phone' => 'Please enter a valid phone number.',
        'payment_failed' => 'Payment processing failed. Please try again.',
        'upload_failed' => 'File upload failed. Please try again.',
        'terms_not_accepted' => 'Please accept the terms and conditions.',
        'invalid_category' => 'Please select a valid business category.',
        'invalid_location' => 'Please enter a valid location.',
        'system_error' => 'A system error occurred. Please try again later.'
    );

    return isset($error_messages[$error_code]) ? $error_messages[$error_code] : 'An unknown error occurred.';
}

// Sanitize and validate phone numbers
function sanitize_phone_number($phone) {
    // Remove everything except digits
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if it's a valid US phone number (10 digits)
    if (strlen($phone) === 10) {
        // Format as (XXX) XXX-XXXX
        return sprintf('(%s) %s-%s',
            substr($phone, 0, 3),
            substr($phone, 3, 3),
            substr($phone, 6, 4)
        );
    }
    
    return false;
}

// Schedule cleanup tasks
function schedule_directory_cleanup() {
    if (!wp_next_scheduled('directory_listings_cleanup')) {
        wp_schedule_event(time(), 'daily', 'directory_listings_cleanup');
    }
}
add_action('wp', 'schedule_directory_cleanup');

// Cleanup task
function do_directory_cleanup() {
    // Clean up old draft listings
    cleanup_old_draft_listings();
    
    // Clean up orphaned attachments
    cleanup_orphaned_attachments();
    
    // Log cleanup completion
    directory_listings_log_error('Directory cleanup completed');
}
add_action('directory_listings_cleanup', 'do_directory_cleanup');

// Cleanup orphaned attachments
function cleanup_orphaned_attachments() {
    $args = array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'meta_query' => array(
            array(
                'key' => '_directory_listing_attachment',
                'compare' => 'EXISTS'
            )
        )
    );

    $attachments = get_posts($args);

    foreach ($attachments as $attachment) {
        $parent_id = get_post_field('post_parent', $attachment->ID);
        if (!$parent_id || get_post_type($parent_id) !== 'directory_listing') {
            wp_delete_attachment($attachment->ID, true);
        }
    }
}
// ==========================================
// 7. SHORTCODES
// ==========================================

function directory_listing_form_shortcode() {
    ob_start();
    $plugin_dir = plugin_dir_path(__FILE__);
    $template_path = $plugin_dir . 'templates/listing-form.php';
    
    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p>Error: Form template not found. Looking for: ' . esc_html($template_path) . '</p>';
        echo '<p>Plugin directory: ' . esc_html($plugin_dir) . '</p>';
    }
    
    $output = ob_get_clean();
    if (empty($output)) {
        return '<p>Error: No content generated from the form template.</p>';
    }
    $output .= '<script>
        var directory_listings_form = { 
            ajax_url: "' . admin_url('admin-ajax.php') . '", 
            nonce: "' . wp_create_nonce('directory_listing_nonce') . '",
            is_user_logged_in: ' . (is_user_logged_in() ? 'true' : 'false') . '
        };
    </script>';
    return $output;
}
add_shortcode('directory_listing_form', 'directory_listing_form_shortcode');

function custom_login_shortcode() {
    if (is_user_logged_in()) {
        return '<p>You are already logged in. <a href="' . wp_logout_url(home_url()) . '">Logout</a></p>';
    }

    $args = array(
        'redirect' => home_url('/dashboard/'), // Redirect to dashboard after login
        'form_id' => 'loginform-custom',
        'label_username' => __('Username'),
        'label_password' => __('Password'),
        'label_remember' => __('Remember Me'),
        'label_log_in' => __('Log In'),
        'remember' => true
    );
    
    // If there's a specific page to return to, use it
    if (isset($_GET['redirect_to'])) {
        $args['redirect'] = urldecode($_GET['redirect_to']);
    }

    return wp_login_form($args);
}
add_shortcode('custom_login', 'custom_login_shortcode');

// Also add this filter to modify the default login URL throughout your site
function custom_login_page_url($login_url) {
    return home_url('/login/');
}
add_filter('login_url', 'custom_login_page_url');

// Modify the logout redirect
function custom_logout_redirect() {
    return home_url('/login/');
}
add_filter('logout_redirect', 'custom_logout_redirect', 10, 3);


// ==========================================
// 8. TEMPLATE HANDLING
// ==========================================

function directory_listings_template($template) {
    error_log('Directory Listings: Template function called');
    error_log('Directory Listings: Current template: ' . $template);

    if (is_tax('business_category')) {
        error_log('Directory Listings: Is business category taxonomy');
        $new_template = plugin_dir_path(__FILE__) . 'templates/category-template.php';
        if (file_exists($new_template)) {
            error_log('Directory Listings: Category template found: ' . $new_template);
            return $new_template;
        } else {
            error_log('Directory Listings: Category template file not found: ' . $new_template);
        }
    } elseif (is_tax('state')) {
        error_log('Directory Listings: Is state taxonomy');
        $new_template = plugin_dir_path(__FILE__) . 'templates/state-template.php';
        if (file_exists($new_template)) {
            error_log('Directory Listings: State template found: ' . $new_template);
            return $new_template;
        } else {
            error_log('Directory Listings: State template file not found: ' . $new_template);
        }
    } elseif (is_singular('directory_listing')) {
        $single_template = plugin_dir_path(__FILE__) . 'templates/single-listing.php';
        if (file_exists($single_template)) {
            return $single_template;
        }
    } elseif (is_post_type_archive('restaurant')) {
        $archive_template = plugin_dir_path(__FILE__) . 'templates/archive-restaurant.php';
        if (file_exists($archive_template)) {
            return $archive_template;
        }
    } elseif (is_tax('cuisine')) {
        $taxonomy_template = plugin_dir_path(__FILE__) . 'templates/taxonomy-cuisine.php';
        if (file_exists($taxonomy_template)) {
            return $taxonomy_template;
        }
    } elseif (is_singular('restaurant')) {
        $single_template = plugin_dir_path(__FILE__) . 'templates/single-restaurant.php';
        if (file_exists($single_template)) {
            return $single_template;
        }
    } else {
        error_log('Directory Listings: Not a handled template type');
    }
    return $template;
}
add_filter('template_include', 'directory_listings_template', 99);

// Register page templates
function register_custom_page_templates($page_templates, $theme, $post) {
    // Existing templates
    $page_templates['template-restaurant-listing.php'] = 'Restaurant Listing';
    $page_templates['template-restaurant-form.php'] = 'Restaurant Listing Form';
    $page_templates['template-regular-business-form.php'] = 'Regular Business Form';
    
    // New templates
    $page_templates['free-regular-business-listing-form.php'] = 'Free Business Listing Form';
    $page_templates['paid-regular-business-listing-form.php'] = 'Paid Business Listing Form';
    $page_templates['free-template-restaurant-form.php'] = 'Free Restaurant Listing Form';
    $page_templates['paid-template-restaurant-form.php'] = 'Paid Restaurant Listing Form';
    
    return $page_templates;
}
add_filter('theme_page_templates', 'register_custom_page_templates', 10, 3);

// Load custom page templates
function load_custom_page_template($template) {
    $template_slug = get_page_template_slug();
    
    // Directory paths
    $base_dir = plugin_dir_path(__FILE__) . 'templates/';
    $free_dir = $base_dir . 'free-listings/';
    $paid_dir = $base_dir . 'paid-listings/';
    
    // Map of all templates and their paths
    $template_paths = array(
        // Existing templates
        'template-restaurant-listing.php' => $base_dir . 'template-restaurant-listing.php',
        'template-restaurant-form.php' => $base_dir . 'template-restaurant-form.php',
        'template-regular-business-form.php' => $base_dir . 'template-regular-business-form.php',
        
        // New templates
        'free-regular-business-listing-form.php' => $free_dir . 'free-regular-business-listing-form.php',
        'paid-regular-business-listing-form.php' => $paid_dir . 'paid-regular-business-listing-form.php',
        'free-template-restaurant-form.php' => $free_dir . 'free-template-restaurant-form.php',
        'paid-template-restaurant-form.php' => $paid_dir . 'paid-template-restaurant-form.php'
    );
    
    // If the template exists in our mapping and the file exists, use it
    if (isset($template_paths[$template_slug]) && file_exists($template_paths[$template_slug])) {
        return $template_paths[$template_slug];
    }
    
    return $template;
}
add_filter('page_template', 'load_custom_page_template');

// Create template directories on plugin activation
function create_template_directories() {
    $dirs = array(
        plugin_dir_path(__FILE__) . 'templates/free-listings',
        plugin_dir_path(__FILE__) . 'templates/paid-listings'
    );
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }
}
register_activation_hook(__FILE__, 'create_template_directories');

// ==========================================
// 9. EMAIL NOTIFICATIONS
// ==========================================



// ==========================================
// 10. USER CAPABILITIES
// ==========================================

function add_directory_listing_capabilities() {
    $roles = array('administrator', 'editor');
    
    foreach ($roles as $role) {
        $role_obj = get_role($role);
        if (!$role_obj) continue;

        $role_obj->add_cap('edit_directory_listing');
        $role_obj->add_cap('read_directory_listing');
        $role_obj->add_cap('delete_directory_listing');
        $role_obj->add_cap('edit_directory_listings');
        $role_obj->add_cap('edit_others_directory_listings');
        $role_obj->add_cap('publish_directory_listings');
        $role_obj->add_cap('read_private_directory_listings');
    }
}

function remove_directory_listing_capabilities() {
    global $wp_roles;

    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }

    $roles = array('administrator', 'editor');
    $capabilities = array(
        'edit_directory_listing',
        'read_directory_listing',
        'delete_directory_listing',
        'edit_directory_listings',
        'edit_others_directory_listings',
        'publish_directory_listings',
        'read_private_directory_listings'
    );

    foreach ($roles as $role) {
        $role_obj = $wp_roles->get_role($role);
        if ($role_obj) {
            foreach ($capabilities as $cap) {
                $role_obj->remove_cap($cap);
            }
        }
    }
}

// ==========================================
// 11. INTERNATIONALIZATION
// ==========================================

function directory_listings_load_textdomain() {
   load_plugin_textdomain('directory-listings', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'directory_listings_load_textdomain');

// ==========================================
// 12. FILE UPLOAD HANDLING
// ==========================================

function handle_file_upload($file, $post_id, $field_name) {
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
    $max_size = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowed_types)) {
        return new WP_Error('invalid_file_type', 'Invalid file type. Please upload a JPEG, PNG, or GIF image.');
    }

    if ($file['size'] > $max_size) {
        return new WP_Error('file_too_large', 'File is too large. Maximum size is 2MB.');
    }

    $upload_overrides = array('test_form' => false);
    $movefile = wp_handle_upload($file, $upload_overrides);

    if ($movefile && !isset($movefile['error'])) {
        $wp_filetype = wp_check_filetype($movefile['file'], null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name(basename($movefile['file'])),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $movefile['file'], $post_id);
        $attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);
        update_field($field_name, $attach_id, $post_id);
        return $attach_id;
    } else {
        return new WP_Error('upload_error', $movefile['error']);
    }
}

// ==========================================
// 13. ADMIN FILTERS AND COLUMNS
// ==========================================

function add_directory_custom_columns($columns) {
    // Start fresh by defining only the columns we want
    $columns = array(
        'cb' => '<input type="checkbox" />', // Maintain the checkbox
        'title' => __('Title', 'directory-listings'),
        'business_category' => __('Business Categories', 'directory-listings'),
        'state' => __('State', 'directory-listings'),
        'city' => __('City', 'directory-listings'),
        'listing_type' => __('Type', 'directory-listings'),
        'top_rated' => __('Top Rated', 'directory-listings'),
        'is_verified' => __('Verified', 'directory-listings'),
        'post_id' => __('Post #', 'directory-listings'),
        'date' => __('Published', 'directory-listings')
    );
    
    return $columns;
}
add_filter('manage_directory_listing_posts_columns', 'add_directory_custom_columns', 999);

function populate_directory_custom_columns($column, $post_id) {
    switch ($column) {
        case 'business_category':
            $terms = get_the_terms($post_id, 'business_category');
            if ($terms && !is_wp_error($terms)) {
                $term_names = array();
                foreach ($terms as $term) {
                    if ($term->parent == 0) { // Only get parent categories
                        $term_names[] = $term->name;
                    }
                }
                echo esc_html(implode(', ', $term_names));
            }
            break;
            
        case 'state':
            $state = get_post_meta($post_id, 'state', true);
            echo $state ? esc_html($state) : '—';
            break;
            
            
        case 'listing_type':
            $listing_type = get_field('listing_type', $post_id);
            echo ucfirst($listing_type);
            break;
            
        case 'top_rated':
            $top_rated = get_field('top_rated', $post_id);
            echo $top_rated ? '✓' : '✗';
            break;
            
        case 'is_verified':
            $is_verified = get_field('is_verified', $post_id);
            echo $is_verified ? '✓' : '✗';
            break;
            
  
    }
}
add_action('manage_directory_listing_posts_custom_column', 'populate_directory_custom_columns', 10, 2);


function make_directory_listing_columns_sortable($columns) {
    $columns['business_category'] = 'business_category';
    $columns['state'] = 'state';
    $columns['city'] = 'city';
    $columns['listing_type'] = 'listing_type';
    $columns['top_rated'] = 'top_rated';
    $columns['is_verified'] = 'is_verified';
    $columns['post_id'] = 'ID';
    return $columns;
}
add_filter('manage_edit-directory_listing_sortable_columns', 'make_directory_listing_columns_sortable');

function add_directory_listing_filters($post_type, $which) {
    if ('directory_listing' !== $post_type) {
        return;
    }

    $listing_type = isset($_GET['listing_type']) ? $_GET['listing_type'] : '';
    $top_rated = isset($_GET['top_rated']) ? $_GET['top_rated'] : '';
    $is_verified = isset($_GET['is_verified']) ? $_GET['is_verified'] : '';
    ?>
    <select name="listing_type" id="filter-by-listing-type">
        <option value="">All Listing Types</option>
        <option value="free" <?php selected($listing_type, 'free'); ?>>Free</option>
        <option value="paid" <?php selected($listing_type, 'paid'); ?>>Paid</option>
    </select>
    <select name="top_rated" id="filter-by-top-rated">
        <option value="">All Ratings</option>
        <option value="1" <?php selected($top_rated, '1'); ?>>Top Rated</option>
        <option value="0" <?php selected($top_rated, '0'); ?>>Not Top Rated</option>
    </select>
    <select name="is_verified" id="filter-by-verified">
        <option value="">All Verification</option>
        <option value="1" <?php selected($is_verified, '1'); ?>>Verified</option>
        <option value="0" <?php selected($is_verified, '0'); ?>>Not Verified</option>
    </select>
    <?php
}
add_action('restrict_manage_posts', 'add_directory_listing_filters', 10, 2);

function filter_directory_listings($query) {
    global $pagenow;
    $type = 'directory_listing';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ('directory_listing' == $type && is_admin() && $pagenow == 'edit.php') {
        $meta_query = array();

        if (isset($_GET['listing_type']) && $_GET['listing_type'] != '') {
            $meta_query[] = array(
                'key' => 'listing_type',
                'value' => $_GET['listing_type'],
                'compare' => '='
            );
        }

        if (isset($_GET['top_rated']) && $_GET['top_rated'] != '') {
            $meta_query[] = array(
                'key' => 'top_rated',
                'value' => $_GET['top_rated'],
                'compare' => '='
            );
        }

        if (isset($_GET['is_verified']) && $_GET['is_verified'] != '') {
            $meta_query[] = array(
                'key' => 'is_verified',
                'value' => $_GET['is_verified'],
                'compare' => '='
            );
        }

        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }
}
add_filter('parse_query', 'filter_directory_listings');
// ==========================================
// 14. DASHBOARD AND EDIT LISTING HANDLING
// ==========================================

function load_dashboard_template($template) {
    if (is_page('dashboard')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'page-dashboard.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('template_include', 'load_dashboard_template');	

function handle_listing_edit() {
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['listing_id'])) {
        $listing_id = intval($_GET['listing_id']);
        $listing = get_post($listing_id);

        if ($listing && $listing->post_author == get_current_user_id()) {
            // Load your edit form here
            include plugin_dir_path(__FILE__) . 'edit-listing-form.php';
            exit;
        }
    }
}
add_action('template_redirect', 'handle_listing_edit');

// ==========================================
// 15. ACF FIELDS CREATION
// ==========================================

function create_acf_fields() {
    if (function_exists('acf_add_local_field_group')):

        acf_add_local_field_group(array(
            'key' => 'group_directory_listing',
            'title' => 'Directory Listing Fields',
            'fields' => array(
                array(
                    'key' => 'field_listing_type',
                    'label' => 'Listing Type',
                    'name' => 'listing_type',
                    'type' => 'select',
                    'choices' => array(
                        'free' => 'Free',
                        'paid' => 'Paid'
                    ),
                    'default_value' => 'free',
                    'required' => 1
                ),
                array(
                    'key' => 'field_top_rated',
                    'label' => 'Top Rated',
                    'name' => 'top_rated',
                    'type' => 'true_false',
                    'instructions' => 'Mark this listing as Top Rated',
                    'default_value' => 0,
                    'ui' => 1
                ),
                array(
                    'key' => 'field_is_verified',
                    'label' => 'Verified Listing',
                    'name' => 'is_verified',
                    'type' => 'true_false',
                    'instructions' => 'Toggle this on if the listing is verified',
                    'ui' => 1
                ),
                array(
                    'key' => 'field_first_name',
                    'label' => 'First Name',
                    'name' => 'first_name',
                    'type' => 'text',
                    'required' => 1
                ),
                array(
                    'key' => 'field_last_name',
                    'label' => 'Last Name',
                    'name' => 'last_name',
                    'type' => 'text',
                    'required' => 1
                ),
                array(
                    'key' => 'field_street_address',
                    'label' => 'Street Address',
                    'name' => 'street_address',
                    'type' => 'text',
                    'required' => 1
                ),
                array(
                    'key' => 'field_city',
                    'label' => 'City',
                    'name' => 'city',
                    'type' => 'text',
                    'required' => 1
                ),
                array(
                    'key' => 'field_state',
                    'label' => 'State',
                    'name' => 'state',
                    'type' => 'text',
                    'required' => 1
                ),
                array(
                    'key' => 'field_zip_code',
                    'label' => 'ZIP Code',
                    'name' => 'zip_code',
                    'type' => 'text',
                    'required' => 1
                ),
                array(
                    'key' => 'field_phone_number',
                    'label' => 'Phone Number',
                    'name' => 'phone_number',
                    'type' => 'text',
                    'required' => 1
                ),
                array(
                    'key' => 'field_email',
                    'label' => 'Email Address',
                    'name' => 'email',
                    'type' => 'email',
                    'required' => 1
                ),
                array(
                    'key' => 'field_business_description',
                    'label' => 'Business Description',
                    'name' => 'business_description',
                    'type' => 'textarea',
                    'required' => 1
                ),
                array(
                    'key' => 'field_services',
                    'label' => 'Services Offered',
                    'name' => 'services_offered',
                    'type' => 'repeater',
                    'instructions' => 'Add the specific services your business offers',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => ''
                    ),
                    'collapsed' => '',
                    'min' => 0,
                    'max' => 0,
                    'layout' => 'table',
                    'button_label' => 'Add Service',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_service_name',
                            'label' => 'Service Name',
                            'name' => 'service_name',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => ''
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => ''
                        ),
                        array(
                            'key' => 'field_service_description',
                            'label' => 'Service Description',
                            'name' => 'service_description',
                            'type' => 'textarea',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => ''
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'maxlength' => '',
                            'rows' => 4,
                            'new_lines' => ''
                        )
                    )
                ),
                array(
                    'key' => 'field_overall_rating',
                    'label' => 'Overall Rating',
                    'name' => 'overall_rating',
                    'type' => 'number',
                    'instructions' => 'Enter the overall rating (0-5)',
                    'min' => 0,
                    'max' => 5,
                    'step' => 0.1
                ),
                array(
                    'key' => 'field_review_count',
                    'label' => 'Review Count',
                    'name' => 'review_count',
                    'type' => 'number',
                    'instructions' => 'Enter the total number of reviews',
                    'min' => 0
                ),
                array(
                    'key' => 'field_licenses_certifications',
                    'label' => 'Licenses & Certifications',
                    'name' => 'licenses_certifications',
                    'type' => 'repeater',
                    'instructions' => 'Add professional licenses and certifications',
                    'required' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => ''
                    ),
                    'collapsed' => '',
                    'min' => 0,
                    'max' => 0,
                    'layout' => 'block',
                    'button_label' => 'Add License/Certification',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_license_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                            'required' => 1,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => ''
                            )
                        ),
                        array(
                            'key' => 'field_license_number',
                            'label' => 'License Number',
                            'name' => 'number',
                            'type' => 'text',
                            'required' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => ''
                            )
                        ),
                        array(
                            'key' => 'field_license_expiration',
                            'label' => 'Expiration Date',
                            'name' => 'expiration',
                            'type' => 'date_picker',
                            'required' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => ''
                            ),
                            'display_format' => 'm/d/Y',
                            'return_format' => 'm/d/Y'
                        ),
                        array(
                            'key' => 'field_license_issuer',
                            'label' => 'Issuing Authority',
                            'name' => 'issuer',
                            'type' => 'text',
                            'required' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => ''
                            )
                        ),
                        array(
                            'key' => 'field_license_verified',
                            'label' => 'Verified',
                            'name' => 'verified',
                            'type' => 'true_false',
                            'ui' => 1,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => ''
                            )
                        )
                    )
                ),
                array(
                    'key' => 'field_logo',
                    'label' => 'Logo',
                    'name' => 'logo',
                    'type' => 'image',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_listing_type',
                                'operator' => '==',
                                'value' => 'paid'
                            )
                        )
                    )
                ),
                array(
                    'key' => 'field_photo_gallery',
                    'label' => 'Photo Gallery',
                    'name' => 'photo_gallery',
                    'type' => 'gallery',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_listing_type',
                                'operator' => '==',
                                'value' => 'paid'
                            )
                        )
                    )
                ),
                array(
                    'key' => 'field_website_link',
                    'label' => 'Website Link',
                    'name' => 'website_link',
                    'type' => 'url',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_listing_type',
                                'operator' => '==',
                                'value' => 'paid'
                            )
                        )
                    )
                ),
                array(
                    'key' => 'field_service_area',
                    'label' => 'Service Area',
                    'name' => 'service_area',
                    'type' => 'group',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_listing_type',
                                'operator' => '==',
                                'value' => 'paid'
                            )
                        )
                    ),
                    'sub_fields' => array(
                        array(
                            'key' => 'field_service_area_type',
                            'label' => 'Service Area Type',
                            'name' => 'type',
                            'type' => 'select',
                            'choices' => array(
                                'zip-codes' => 'ZIP Codes',
                                'cities' => 'Cities',
                                'counties' => 'Counties'
                            )
                        ),
                        array(
                            'key' => 'field_service_area_list',
                            'label' => 'Service Areas',
                            'name' => 'areas',
                            'type' => 'textarea'
                        )
                    )
                ),
                array(
                    'key' => 'field_business_hours',
                    'label' => 'Business Hours',
                    'name' => 'business_hours',
                    'type' => 'repeater',
                    'instructions' => 'Add business hours for each day',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_listing_type',
                                'operator' => '==',
                                'value' => 'paid'
                            )
                        )
                    ),
                    'sub_fields' => array(
                        array(
                            'key' => 'field_day_of_week',
                            'label' => 'Day',
                            'name' => 'day_of_week',
                            'type' => 'select',
                            'choices' => array(
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday'
                            )
                        ),
                        array(
                            'key' => 'field_opening_time',
                            'label' => 'Opening Time',
                            'name' => 'opening_time',
                            'type' => 'time_picker'
                        ),
                        array(
                            'key' => 'field_closing_time',
                            'label' => 'Closing Time',
                            'name' => 'closing_time',
                            'type' => 'time_picker'
                        )
                    )
                ),
                array(
                    'key' => 'field_latitude',
                    'label' => 'Latitude',
                    'name' => 'latitude',
                    'type' => 'number',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_listing_type',
                                'operator' => '==',
                                'value' => 'paid'
                            )
                        )
                    ),
                    'min' => -90,
                    'max' => 90,
                    'step' => 'any'
                ),
                array(
                    'key' => 'field_longitude',
                    'label' => 'Longitude',
                    'name' => 'longitude',
                    'type' => 'number',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_listing_type',
                                'operator' => '==',
                                'value' => 'paid'
                            )
                        )
                    ),
                    'min' => -180,
                    'max' => 180,
                    'step' => 'any'
                ),
                array(
                    'key' => 'field_social_media_links',
                    'label' => 'Social Media Links',
                    'name' => 'social_media_links',
                    'type' => 'group',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_listing_type',
                                'operator' => '==',
                                'value' => 'paid'
                           )
                        )
                    ),
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => ''
                    ),
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_facebook',
                            'label' => 'Facebook',
                            'name' => 'facebook',
                            'type' => 'url',
                            'instructions' => '',
                            'required' => 0
                        ),
                        array(
                            'key' => 'field_google',
                            'label' => 'Google',
                            'name' => 'google',
                            'type' => 'url',
                            'instructions' => '',
                            'required' => 0
                        ),
                        array(
                            'key' => 'field_yelp',
                            'label' => 'Yelp',
                            'name' => 'yelp',
                            'type' => 'url',
                            'instructions' => '',
                            'required' => 0
                        ),
                        array(
                            'key' => 'field_thumbtack',
                            'label' => 'Thumbtack',
                            'name' => 'thumbtack',
                            'type' => 'url',
                            'instructions' => '',
                            'required' => 0
                        ),
                        array(
                            'key' => 'field_other_urls',
                            'label' => 'Other URLs',
                            'name' => 'other_urls',
                            'type' => 'repeater',
                            'instructions' => 'Enter additional URLs',
                            'required' => 0,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_other_url',
                                    'label' => 'URL',
                                    'name' => 'url',
                                    'type' => 'url'
                                ),
                                array(
                                    'key' => 'field_other_url_label',
                                    'label' => 'Label',
                                    'name' => 'label',
                                    'type' => 'text'
                                )
                            )
                        )
                    )
                )
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'directory_listing'
                    )
                )
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => ''
        ));

    endif;
}
add_action('acf/init', 'create_acf_fields');
function create_restaurant_acf_fields() {
    if (function_exists('acf_add_local_field_group')):

        acf_add_local_field_group(array(
            'key' => 'group_restaurant',
            'title' => 'Restaurant Details',
            'fields' => array(
                array(
                    'key' => 'field_opening_hours',
                    'label' => 'Opening Hours',
                    'name' => 'opening_hours',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_day',
                            'label' => 'Day',
                            'name' => 'day',
                            'type' => 'select',
                            'choices' => array(
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday',
                            ),
                        ),
                        array(
                            'key' => 'field_open_time',
                            'label' => 'Open Time',
                            'name' => 'open_time',
                            'type' => 'time_picker',
                        ),
                        array(
                            'key' => 'field_close_time',
                            'label' => 'Close Time',
                            'name' => 'close_time',
                            'type' => 'time_picker',
                        ),
                    ),
                ),
                      array(
                    'key' => 'field_restaurant_logo',
                    'label' => 'Logo',
                    'name' => 'restaurant_logo',
                    'type' => 'image'
                    ),
                array(
                    'key' => 'field_menu_url',
                    'label' => 'Menu URL',
                    'name' => 'menu_url',
                    'type' => 'url',
                ),
                array(
                   'key' => 'field_restaurant_gallery',
                    'label' => 'Restaurant Gallery',
                    'name' => 'restaurant_gallery',
                    'type' => 'gallery',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'min' => '',
                    'max' => '',
                    'min_width' => '',
                    'min_height' => '',
                    'min_size' => '',
                    'max_width' => '',
                    'max_height' => '',
                    'max_size' => '',
                    'mime_types' => '',
                ),
                array(
                    'key' => 'field_delivery_options',
                    'label' => 'Delivery Options',
                    'name' => 'delivery_options',
                    'type' => 'checkbox',
                    'choices' => array(
                        'delivery' => 'Delivery',
                        'takeout' => 'Takeout',
                        'dine_in' => 'Dine-in',
                    ),
                ),
                array(
                    'key' => 'field_reservation_required',
                    'label' => 'Reservation Required',
                    'name' => 'reservation_required',
                    'type' => 'true_false',
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_average_wait_time',
                    'label' => 'Average Wait Time',
                    'name' => 'average_wait_time',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_signature_dishes',
                    'label' => 'Signature Dishes',
                    'name' => 'signature_dishes',
                    'type' => 'repeater',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_dish_name',
                            'label' => 'Dish Name',
                            'name' => 'dish_name',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_dish_price',
                            'label' => 'Price',
                            'name' => 'price',
                            'type' => 'number',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_alcohol_served',
                    'label' => 'Alcohol Served',
                    'name' => 'alcohol_served',
                    'type' => 'select',
                    'choices' => array(
                        'none' => 'None',
                        'beer_wine' => 'Beer/Wine',
                        'full_bar' => 'Full Bar',
                    ),
                ),
                array(
                    'key' => 'field_parking_options',
                    'label' => 'Parking Options',
                    'name' => 'parking_options',
                    'type' => 'checkbox',
                    'choices' => array(
                        'street' => 'Street',
                        'lot' => 'Parking Lot',
                        'valet' => 'Valet',
                    ),
                ),
                array(
                    'key' => 'field_wheelchair_accessible',
                    'label' => 'Wheelchair Accessible',
                    'name' => 'wheelchair_accessible',
                    'type' => 'true_false',
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_noise_level',
                    'label' => 'Noise Level',
                    'name' => 'noise_level',
                    'type' => 'select',
                    'choices' => array(
                        'quiet' => 'Quiet',
                        'moderate' => 'Moderate',
                        'loud' => 'Loud',
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'restaurant',
                    ),
                ),
            ),
        ));

    endif;
}
add_action('acf/init', 'create_restaurant_acf_fields');

// ==========================================
// 16. ACF DEPENDENCY CHECK
// ==========================================

function is_acf_active() {
    return class_exists('ACF');
}

function acf_admin_notice() {
    ?>
    <div class="error">
        <p><?php _e('Directory Listings plugin requires Advanced Custom Fields to be installed and activated.', 'directory-listings'); ?></p>
    </div>
    <?php
}

function check_acf_dependency() {
    if (!is_acf_active()) {
        add_action('admin_notices', 'acf_admin_notice');
    }
}
add_action('admin_init', 'check_acf_dependency');

// ==========================================
// 17. DIRECTORY LISTINGS SHORTCODE
// ==========================================

function directory_listings_shortcode($atts) {
    // Your existing shortcode function...
}
add_shortcode('directory_listings', 'directory_listings_shortcode');

// ==========================================
// 18. DEBUG AND CLEANUP FUNCTIONS
// ==========================================

function debug_term_relationships() {
    $taxonomies = array('business_category', 'state');
    
    foreach ($taxonomies as $taxonomy) {
        $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
        echo "<h2>$taxonomy</h2>";
        echo "<ul>";
        foreach ($terms as $term) {
            $parent_id = $term->parent;
            $parent_term = $parent_id ? get_term($parent_id, $taxonomy) : null;
            echo "<li>Term: {$term->name} (ID: {$term->term_id})";
            echo "<br>Parent: " . ($parent_term ? $parent_term->name . " (ID: {$parent_term->term_id})" : 'None');
            echo "</li>";
        }
        echo "</ul>";
    }
}

function display_cleanup_page() {
    ?>
    <div class="wrap">
        <h1>Cleanup Term Relationships</h1>
        <p>Click the button below to run the cleanup process for term relationships:</p>
        <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=trigger_cleanup'), 'trigger_cleanup_nonce'); ?>" class="button button-primary">Run Cleanup</a>
    </div>
    <?php
}

function cleanup_term_relationships() {
    global $wpdb;
    $taxonomies = array('business_category', 'state');
    
    foreach ($taxonomies as $taxonomy) {
        $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
        foreach ($terms as $term) {
            $parent_id = $term->parent;
            
            if ($parent_id) {
                $wpdb->update(
                    $wpdb->term_taxonomy,
                    array('parent' => $parent_id),
                    array('term_id' => $term->term_id, 'taxonomy' => $taxonomy)
                );
            }
        }
    }
    echo "Cleanup complete.";
}

function handle_cleanup_process() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    if (!wp_verify_nonce($_GET['_wpnonce'], 'trigger_cleanup_nonce')) {
        wp_die('Security check failed');
    }

    cleanup_term_relationships();
    wp_safe_redirect(admin_url('tools.php?page=cleanup-terms&cleanup=success'));
    exit;
}
add_action('admin_post_trigger_cleanup', 'handle_cleanup_process');

// Add debug and cleanup pages to admin menu
add_action('admin_menu', function() {
    add_management_page('Debug Term Relationships', 'Debug Terms', 'manage_options', 'debug-terms', 'debug_term_relationships');
    add_management_page('Cleanup Term Relationships', 'Cleanup Terms', 'manage_options', 'cleanup-terms', 'display_cleanup_page');
});

// ==========================================
// 19. PLUGIN ACTIVATION AND DEACTIVATION
// ==========================================

function directory_listings_activate() {
    // Perform any necessary setup on activation
    create_directory_listing_post_type();
    create_directory_taxonomies();
    create_restaurant_post_type();
    create_restaurant_taxonomies();
    add_directory_listing_capabilities();
    create_restaurant_reviews_table();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'directory_listings_activate');

function directory_listings_deactivate() {
    remove_directory_listing_capabilities();
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'directory_listings_deactivate');

function create_restaurant_reviews_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'restaurant_reviews';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        restaurant_id mediumint(9) NOT NULL,
        user_id mediumint(9) NOT NULL,
        rating tinyint(1) NOT NULL,
        food_rating tinyint(1) NOT NULL,
        service_rating tinyint(1) NOT NULL,
        ambience_rating tinyint(1) NOT NULL,
        review_text text NOT NULL,
        date_of_visit date NOT NULL,
        review_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        status varchar(20) DEFAULT 'pending' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Add these functions at the end of your file
if (!function_exists('create_restaurant_post_type')) {
    function create_restaurant_post_type() {
        $labels = array(
            'name' => 'Restaurants',
            'singular_name' => 'Restaurant',
            'menu_name' => 'Restaurants',
            'add_new' => 'Add New Restaurant',
            'add_new_item' => 'Add New Restaurant',
            'edit_item' => 'Edit Restaurant',
            'new_item' => 'New Restaurant',
            'view_item' => 'View Restaurant',
            'search_items' => 'Search Restaurants',
            'not_found' => 'No restaurants found',
            'not_found_in_trash' => 'No restaurants found in Trash',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-food',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'rewrite' => array('slug' => 'restaurants'),
            'show_in_menu' => false,
        );

        register_post_type('restaurant', $args);
    }
}

if (!function_exists('create_restaurant_taxonomies')) {
    function create_restaurant_taxonomies() {
        // Cuisine Type Taxonomy
        register_taxonomy(
            'cuisine',
            'restaurant',
            array(
                'label' => 'Cuisine Types',
                'hierarchical' => true,
                'rewrite' => array('slug' => 'cuisine'),
            )
        );

        // Price Range Taxonomy
        register_taxonomy(
            'price_range',
            'restaurant',
            array(
                'label' => 'Price Range',
                'hierarchical' => false,
                'rewrite' => array('slug' => 'price-range'),
            )
        );
    }
}
// ==========================================
// 20. SEARCH WIDGET AND RELATED FUNCTIONS
// ==========================================

function modify_directory_search_query($query) {
    if (!is_admin() && $query->is_main_query() && 
        (is_tax('business_category') || is_tax('state'))) {
        
        // Get filter values
        $state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
        $city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
        $sort = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : '';
        
        $tax_query = array();
        $meta_query = array();

        // Add state filter
        if (!empty($state)) {
            $tax_query[] = array(
                'taxonomy' => 'state',
                'field' => 'slug',
                'terms' => $state
            );
        }

        // Add city filter
        if (!empty($city)) {
            $meta_query[] = array(
                'key' => 'city',
                'value' => $city,
                'compare' => '='
            );
        }

        // Apply sorting
        if (!empty($sort)) {
            switch ($sort) {
                case 'rating':
                    $query->set('meta_key', 'overall_rating');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                case 'reviews':
                    $query->set('meta_key', 'review_count');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                case 'newest':
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
            }
        }

        if (!empty($tax_query)) {
            $query->set('tax_query', $tax_query);
        }

        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }
    return $query;
}
add_action('pre_get_posts', 'modify_directory_search_query');

function register_directory_search_widget( $widgets_manager ) {
    require_once( __DIR__ . '/widgets/directory-search-widget.php' );
    $widgets_manager->register( new \Directory_Search_Widget() );
}
add_action( 'elementor/widgets/register', 'register_directory_search_widget' );

// ==========================================
// 21. STRIPE PAYMENT INTEGRATION
// ==========================================

// Load Stripe PHP library via Composer
require 'vendor/autoload.php';

// Set Stripe secret key
\Stripe\Stripe::setApiKey('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');
//\Stripe\Stripe::setApiKey('sk_test_MsgPlUlQOWPJEi7qbMlb8qKm');

// Add AJAX action to handle form submission and payment processing
add_action('wp_ajax_process_stripe_payment', 'process_stripe_payment');
add_action('wp_ajax_nopriv_process_stripe_payment', 'process_stripe_payment');

function process_stripe_payment() {
    
    // Get form data (amount, customer email, etc.)
    $amount = sanitize_text_field($_POST['total']);
    $email = sanitize_email($_POST['email']);
    $name = sanitize_text_field($_POST['first_name'].' '.$_POST['last_name']);
  //$email='rodbartruff@gmail.com';
  //$name="Rod;

    
    try {
        // Create Stripe PaymentIntent
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount * 100, // Amount in cents
            'currency' => 'usd',
            'receipt_email' => $email, // Send receipt to the customer email
        ]);

        // Return clientSecret to the frontend for payment confirmation
        wp_send_json_success([
            'clientSecret' => $paymentIntent->client_secret,
        ]);

    } catch (Exception $e) {
        wp_send_json_error(['error' => $e->getMessage()]);
    }
}

// Hook to send email after successful payment
function send_email_after_payment($paymentIntent) {
    if ($paymentIntent->status === 'succeeded') {
        $email = $paymentIntent->receipt_email;
        $amount = $paymentIntent->amount / 100; // Convert from cents to dollars

        // Email content
        $subject = "Payment Confirmation";
        $message = "Dear customer,\n\nThank you for your payment of $" . $amount . ". Your payment was successful!\n\nBest regards,\nYour Company";
        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        // Send email to customer
        wp_mail($email, $subject, $message, $headers);

        // Send email to admin
        wp_mail(get_option('admin_email'), "New Payment Received", $message, $headers);
    }
}

// Handle the response from Stripe webhook (for handling successful payments)
add_action('rest_api_init', function () {
    register_rest_route('stripe/v1', '/webhook', array(
        'methods' => 'POST',
        'callback' => 'handle_stripe_webhook',
    ));
});

function handle_stripe_webhook(WP_REST_Request $request) {
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $endpoint_secret = 'your-webhook-secret'; // Set this from your Stripe webhook settings

    try {
        // Verify Stripe signature
        $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

        // Handle the event
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            send_email_after_payment($paymentIntent); // Send email after successful payment
        }

        http_response_code(200); // Return a response to Stripe
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// ==========================================
// 22. CUSTOMER REVIEWS
// ==========================================

// Helper functions for ratings
function get_ratings_breakdown($post_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    // Initialize array for all possible ratings
    $breakdown = array_fill(1, 5, 0);
    
    // Get approved reviews for this listing
    $ratings = $wpdb->get_results($wpdb->prepare(
        "SELECT rating, COUNT(*) as count 
         FROM $table_name 
         WHERE listing_id = %d 
         AND status = 'approved' 
         GROUP BY rating",
        $post_id
    ));
    
    if ($ratings) {
        foreach ($ratings as $rating) {
            $breakdown[$rating->rating] = $rating->count;
        }
    }
    
    return $breakdown;
}

function get_overall_rating($post_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    $average = $wpdb->get_var($wpdb->prepare(
        "SELECT AVG(rating) 
         FROM $table_name 
         WHERE listing_id = %d 
         AND status = 'approved'",
        $post_id
    ));
    
    return $average ? round($average, 1) : 0;
}

function get_review_count($post_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    return (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) 
         FROM $table_name 
         WHERE listing_id = %d 
         AND status = 'approved'",
        $post_id
    ));
}

// Create custom table for reviews
function create_reviews_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        listing_id mediumint(9) NOT NULL,
        user_id mediumint(9) NOT NULL,
        rating tinyint(1) NOT NULL,
        review_text text NOT NULL,
        date_of_service date NOT NULL,
        service_received varchar(255) NOT NULL,
        review_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        status varchar(20) DEFAULT 'pending' NOT NULL,
        helpful_count int DEFAULT 0,
        PRIMARY KEY  (id),
        KEY listing_id (listing_id),
        KEY user_id (user_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);
    
    error_log('Reviews table creation result: ' . print_r($result, true));
}
register_activation_hook(__FILE__, 'create_reviews_table');

// Function to manually recreate the reviews table
function recreate_reviews_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    // Drop the existing table if it exists
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    // Recreate the table
    create_reviews_table();
    
    error_log('Reviews table recreated');
}

// Function to check if the reviews table exists and has the correct structure
function check_reviews_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    
    if (!$table_exists) {
        error_log("Reviews table does not exist: $table_name");
        return false;
    }
    
    $columns = $wpdb->get_results("DESCRIBE $table_name");
    $column_names = array_column($columns, 'Field');
    $expected_columns = array('id', 'listing_id', 'user_id', 'rating', 'review_text', 'date_of_service', 'service_received', 'review_date', 'status', 'helpful_count');
    
    $missing_columns = array_diff($expected_columns, $column_names);
    
    if (!empty($missing_columns)) {
        error_log("Missing columns in reviews table: " . implode(', ', $missing_columns));
        return false;
    }
    
    error_log("Reviews table exists and has all expected columns");
    return true;
}

// Add review submission form to single listing template
function add_review_form() {
    static $form_displayed = false;
    if ($form_displayed) {
        return; // Do not display the form again
    }
    $form_displayed = true;

    if (is_singular('directory_listing')) {
        global $post;
        $current_user = wp_get_current_user();
        ?>
        <div class="add-review-form">
            <h3 class="section-title">Write a Review</h3>
            <form id="submit-review-form">
                <?php wp_nonce_field('directory_review_nonce', 'review_nonce'); ?>
                <input type="hidden" name="listing_id" value="<?php echo $post->ID; ?>">
                <input type="hidden" name="user_id" value="<?php echo $current_user->ID; ?>">

                <div class="form-group">
                    <label>Rating</label>
                    <div class="star-rating-input">
                        <?php for($i = 1; $i <= 5; $i++) : ?>
                            <button type="button" class="star-button" data-rating="<?php echo $i; ?>">★</button>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Review Title</label>
                    <input type="text" name="review_title" required>
                </div>

                <div class="form-group">
                    <label>Your Review</label>
                    <textarea name="review_text" required rows="4" placeholder="Share your experience..."></textarea>
                </div>

                <div class="form-group">
                    <label>Date of Service</label>
                    <input type="date" name="date_of_service" required max="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Service/Product Received</label>
                    <input type="text" name="service_received" required placeholder="What service or product did you receive?">
                </div>

                <div class="form-group">
                    <label>Photos (optional)</label>
                    <input type="file" name="review_photos[]" accept="image/*" multiple>
                    <div class="photo-preview"></div>
                </div>

                <button type="submit" class="submit-button">Submit Review</button>
            </form>
        </div>
        <?php
    }
}

// Handle review submission via AJAX
function handle_review_submission() {
    check_ajax_referer('directory_review_nonce', 'review_nonce');
    
    if (!check_reviews_table()) {
        recreate_reviews_table();
        if (!check_reviews_table()) {
            wp_send_json_error('Error: Reviews table setup failed.');
            return;
        }
    }

    // Validate and sanitize input
    $listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $review_text = isset($_POST['review_text']) ? sanitize_textarea_field($_POST['review_text']) : '';
    $date_of_service = isset($_POST['date_of_service']) ? sanitize_text_field($_POST['date_of_service']) : '';
    $service_received = isset($_POST['service_received']) ? sanitize_text_field($_POST['service_received']) : '';

    // Validate required fields
    if (!$listing_id || !$user_id || !$rating || !$review_text || !$date_of_service || !$service_received) {
        wp_send_json_error('Please fill in all required fields.');
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';

    $data = array(
        'listing_id' => $listing_id,
        'user_id' => $user_id,
        'rating' => $rating,
        'review_text' => $review_text,
        'date_of_service' => $date_of_service,
        'service_received' => $service_received
    );

    $format = array('%d', '%d', '%d', '%s', '%s', '%s');

    $result = $wpdb->insert($table_name, $data, $format);

    if ($result === false) {
        wp_send_json_error('Database error: ' . $wpdb->last_error);
    } else {
        $review_id = $wpdb->insert_id;
        
        // Handle photo uploads if any
        if (!empty($_FILES['review_photos'])) {
            handle_review_photo_uploads($review_id);
        }

        // Update listing ratings
        update_listing_rating($listing_id);
        
        wp_send_json_success('Review submitted successfully and pending approval.');
    }
}
add_action('wp_ajax_submit_review', 'handle_review_submission');

// Display reviews on single listing page
function display_listing_reviews() {
    if (is_singular('directory_listing')) {
        global $post;
        
        $overall_rating = get_overall_rating($post->ID);
        $review_count = get_review_count($post->ID);
        $ratings_breakdown = get_ratings_breakdown($post->ID);
        
        ?>
        <section class="reviews-section">
            <h2 class="section-title">
                <i class="fas fa-star"></i>
                Customer Reviews & Ratings
            </h2>
            
            <!-- Rating Statistics -->
            <div class="review-stats">
                <div class="overall-rating">
                    <div class="rating-number"><?php echo number_format($overall_rating, 1); ?></div>
                    <div class="rating-stars">
                        <?php echo str_repeat('★', round($overall_rating)); ?>
                    </div>
                    <div>Based on <?php echo $review_count; ?> reviews</div>
                </div>
                
                <div class="rating-distribution">
                    <?php for ($i = 5; $i >= 1; $i--) : 
                        $count = isset($ratings_breakdown[$i]) ? $ratings_breakdown[$i] : 0;
                        $percentage = $review_count > 0 ? ($count / $review_count) * 100 : 0;
                    ?>
                        <div class="rating-bar">
                            <span><?php echo $i; ?> ★</span>
                            <div class="rating-progress">
                                <div class="rating-progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <span><?php echo $count; ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <?php
            // Display individual reviews
            global $wpdb;
            $table_name = $wpdb->prefix . 'directory_reviews';

            $reviews = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE listing_id = %d AND status = 'approved' ORDER BY review_date DESC",
                $post->ID
            ));

            if ($reviews) : ?>
                <div class="review-cards">
                    <?php foreach ($reviews as $review) :
                        $user_info = get_userdata($review->user_id);
                    ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <?php echo get_avatar($review->user_id, 50); ?>
                                    <div>
                                        <strong><?php echo esc_html($user_info->display_name); ?></strong>
                                        <div class="rating-stars">
                                            <?php echo str_repeat('★', $review->rating); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-date">
                                    <?php echo human_time_diff(strtotime($review->review_date), current_time('timestamp')); ?> ago
                                </div>
                            </div>
                            <div class="review-content">
                                <p><strong>Service Received:</strong> <?php echo esc_html($review->service_received); ?></p>
                                <?php echo wp_kses_post($review->review_text); ?>
                            </div>
                            <div class="review-footer">
                                <div class="verified-badge">
                                    <i class="fas fa-check-circle"></i> Verified Customer
                                </div>
                                <button class="helpful-button" data-review-id="<?php echo $review->id; ?>">
                                    <i class="fas fa-thumbs-up"></i> Helpful (<?php echo (int) $review->helpful_count; ?>)
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (is_user_logged_in()) : ?>
                <?php add_review_form(); ?>
            <?php else : ?>
                <div class="review-login-prompt">
                    <p>Please <a href="<?php echo wp_login_url(get_permalink()); ?>">login</a> to leave a review.</p>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
}

// Add reviews management page in admin
function add_reviews_management_page() {
    add_submenu_page(
        'directory-listings-dashboard',
        'Manage Reviews',
        'Manage Reviews',
        'manage_options',
        'manage-reviews',
        'display_reviews_management_page'
    );
}
add_action('admin_menu', 'add_reviews_management_page');

// Update listing rating when reviews change
function update_listing_rating($listing_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';

    // Get new average rating
    $avg_rating = $wpdb->get_var($wpdb->prepare(
        "SELECT AVG(rating) FROM $table_name WHERE listing_id = %d AND status = 'approved'",
        $listing_id
    ));

    // Get new review count
    $review_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE listing_id = %d AND status = 'approved'",
        $listing_id ));

    // Update listing meta
    update_post_meta($listing_id, 'overall_rating', round($avg_rating, 1));
    update_post_meta($listing_id, 'review_count', $review_count);

    // Update rating breakdown
    $ratings_breakdown = get_ratings_breakdown($listing_id);
    update_post_meta($listing_id, 'rating_breakdown', $ratings_breakdown);
}

// Handle helpful votes
function handle_helpful_vote() {
    check_ajax_referer('review_helpful_nonce', 'nonce');
    
    $review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;
    
    if (!$review_id) {
        wp_send_json_error('Invalid review ID');
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';

    // Update helpful count
    $result = $wpdb->query($wpdb->prepare(
        "UPDATE $table_name SET helpful_count = helpful_count + 1 WHERE id = %d",
        $review_id
    ));

    if ($result !== false) {
        $new_count = $wpdb->get_var($wpdb->prepare(
            "SELECT helpful_count FROM $table_name WHERE id = %d",
            $review_id
        ));
        wp_send_json_success(array('new_count' => $new_count));
    } else {
        wp_send_json_error('Failed to update helpful count');
    }
}
add_action('wp_ajax_mark_review_helpful', 'handle_helpful_vote');
add_action('wp_ajax_nopriv_mark_review_helpful', 'handle_helpful_vote');

// Reviews management page
function display_reviews_management_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';

    // Handle review status updates
    if (isset($_POST['review_action']) && isset($_POST['review_id'])) {
        $review_id = intval($_POST['review_id']);
        $action = sanitize_text_field($_POST['review_action']);
        $review = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $review_id));

        if ($review && ($action === 'approve' || $action === 'reject')) {
            $old_status = $review->status;
            $new_status = $action === 'approve' ? 'approved' : 'rejected';
            
            $wpdb->update(
                $table_name,
                array('status' => $new_status),
                array('id' => $review_id),
                array('%s'),
                array('%d')
            );

            // Update listing rating if status changed
            if ($old_status !== $new_status) {
                update_listing_rating($review->listing_id);
            }
        }
    }

    // Get reviews with pagination
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;

    $total_reviews = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $reviews = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY review_date DESC LIMIT %d OFFSET %d",
        $per_page, $offset
    ));

    ?>
    <div class="wrap">
        <h1>Manage Reviews</h1>
        
        <div class="tablenav top">
            <div class="tablenav-pages">
                <?php
                $total_pages = ceil($total_reviews / $per_page);
                if ($total_pages > 1) {
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page
                    ));
                }
                ?>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Business</th>
                    <th>Reviewer</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review) :
                    $listing = get_post($review->listing_id);
                    $user_info = get_userdata($review->user_id);
                    if (!$listing || !$user_info) continue;
                ?>
                    <tr>
                        <td>
                            <a href="<?php echo get_edit_post_link($listing->ID); ?>">
                                <?php echo esc_html($listing->post_title); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html($user_info->display_name); ?></td>
                        <td><?php echo esc_html($review->rating); ?>/5</td>
                        <td><?php echo esc_html($review->review_text); ?></td>
                        <td><?php echo esc_html($review->service_received); ?></td>
                        <td><?php echo esc_html($review->review_date); ?></td>
                        <td><?php echo esc_html(ucfirst($review->status)); ?></td>
                        <td>
                            <?php if ($review->status === 'pending') : ?>
                                <form method="post" style="display:inline;">
                                    <?php wp_nonce_field('review_action_nonce'); ?>
                                    <input type="hidden" name="review_id" value="<?php echo $review->id; ?>">
                                    <input type="hidden" name="review_action" value="approve">
                                    <button type="submit" class="button button-small">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <?php wp_nonce_field('review_action_nonce'); ?>
                                    <input type="hidden" name="review_id" value="<?php echo $review->id; ?>">
                                    <input type="hidden" name="review_action" value="reject">
                                    <button type="submit" class="button button-small">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            <?php else : ?>
                                <?php echo $review->status === 'approved' ? 
                                    '<span class="status-approved">Approved</span>' : 
                                    '<span class="status-rejected">Rejected</span>'; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                if ($total_pages > 1) {
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page
                    ));
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}

// Enqueue review scripts and styles
function enqueue_review_assets() {
    if (is_singular('directory_listing')) {
        wp_enqueue_style(
            'directory-review-style',
            plugin_dir_url(__FILE__) . 'assets/css/reviews.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'directory-review-script',
            plugin_dir_url(__FILE__) . 'assets/js/reviews.js',
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('directory-review-script', 'reviewsAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'review_nonce' => wp_create_nonce('directory_review_nonce'),
            'helpful_nonce' => wp_create_nonce('review_helpful_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_review_assets');
// ==========================================
// 23. Custom Catagory Page
// ==========================================


function get_business_categories() {
    $categories = get_terms(array(
        'taxonomy' => 'business_category',
        'hide_empty' => false,
    ));

    if (is_wp_error($categories)) {
        wp_send_json_error($categories->get_error_message());
    } else {
        wp_send_json_success($categories);
    }
}
add_action('wp_ajax_get_business_categories', 'get_business_categories');
add_action('wp_ajax_nopriv_get_business_categories', 'get_business_categories');



// ==========================================
// 23. Q&A Section
// ==========================================
// Register the Q&A post type
function create_qa_post_type() {
    register_post_type('qa_post',
        array(
            'labels' => array(
                'name' => __('Q&A'),
                'singular_name' => __('Q&A Post')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'author', 'comments'),
            'menu_icon' => 'dashicons-format-chat',
            'rewrite' => array('slug' => 'qa'), // Added rewrite rule
            'publicly_queryable' => true, // Ensure it's publicly queryable
            'show_in_menu' => false,
        )
    );
}
add_action('init', 'create_qa_post_type');


// Register the question category taxonomy
function create_question_category_taxonomy() {
    $labels = array(
        'name' => 'Question Categories',
        'singular_name' => 'Question Category',
        'menu_name' => 'Question Categories',
    );
    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'question-category'),
    );
    register_taxonomy('question_category', array('qa_post'), $args);
}
add_action('init', 'create_question_category_taxonomy', 0);

// Add custom fields for Q&A posts
function add_qa_custom_fields() {
    add_meta_box('qa_meta_box', 'Q&A Details', 'qa_meta_box_callback', 'qa_post', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_qa_custom_fields');

function qa_meta_box_callback($post) {
    wp_nonce_field('qa_meta_box', 'qa_meta_box_nonce');
    $upvotes = get_post_meta($post->ID, 'upvotes', true);
    $downvotes = get_post_meta($post->ID, 'downvotes', true);
    $is_resolved = get_post_meta($post->ID, 'is_resolved', true);
    ?>
    <p>
        <label for="upvotes">Upvotes:</label>
        <input type="number" id="upvotes" name="upvotes" value="<?php echo esc_attr($upvotes); ?>">
    </p>
    <p>
        <label for="downvotes">Downvotes:</label>
        <input type="number" id="downvotes" name="downvotes" value="<?php echo esc_attr($downvotes); ?>">
    </p>
    <p>
        <label for="is_resolved">Resolved:</label>
        <input type="checkbox" id="is_resolved" name="is_resolved" <?php checked($is_resolved, 'on'); ?>>
    </p>
    <?php
}

function save_qa_custom_fields($post_id) {
    if (!isset($_POST['qa_meta_box_nonce']) || !wp_verify_nonce($_POST['qa_meta_box_nonce'], 'qa_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    update_post_meta($post_id, 'upvotes', sanitize_text_field($_POST['upvotes']));
    update_post_meta($post_id, 'downvotes', sanitize_text_field($_POST['downvotes']));
    update_post_meta($post_id, 'is_resolved', isset($_POST['is_resolved']) ? 'on' : 'off');
}
add_action('save_post', 'save_qa_custom_fields');

// Handle question submission
function handle_question_submission() {
    check_ajax_referer('submit_question_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to submit a question.');
    }

    $title = sanitize_text_field($_POST['title']);
    $content = wp_kses_post($_POST['content']);
    $category = intval($_POST['category']);

    $post_data = array(
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'qa_post',
        'post_author'  => get_current_user_id()
    );

    $post_id = wp_insert_post($post_data);

    if ($post_id) {
        wp_set_object_terms($post_id, $category, 'question_category');
        update_post_meta($post_id, 'upvotes', 0);
        update_post_meta($post_id, 'downvotes', 0);
        update_post_meta($post_id, 'is_resolved', 'off');
        wp_send_json_success('Question submitted successfully.');
    } else {
        wp_send_json_error('Failed to submit question. Please try again.');
    }
}
add_action('wp_ajax_submit_question', 'handle_question_submission');

// Handle voting

function handle_vote() {
  
    check_ajax_referer('vote_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to vote.');
    }

    $post_id = intval($_POST['post_id']);
    $vote_type = sanitize_text_field($_POST['vote_type']);
    
    // Get voter's IP address
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Retrieve the IP addresses that have already voted for this post
    $voted_ips = get_post_meta($post_id, 'voted_ips', true);
    $voted_ips = !empty($voted_ips) ? (array)$voted_ips : [];

    // Check if this IP has already voted
    if (in_array($user_ip, $voted_ips)) {
        wp_send_json_error('You have already voted on this post.');
    }

    // Retrieve the current votes meta value, default to 0 if not set
    $current_votes = get_post_meta($post_id, $vote_type, true);
    $current_votes = ($current_votes !== '') ? (int)$current_votes : 0;

    if ($current_votes === 0) {
        // Create a new meta key 'Upvote' with a value of 1 if current vote is 0
        add_post_meta($post_id, $vote_type, 1, true);
    } else {
        // Otherwise, update the meta key by incrementing the vote count
        update_post_meta($post_id, $vote_type, $current_votes + 1);
    }

    // Add the user's IP address to the list of voted IPs
    $voted_ips[] = $user_ip;
    update_post_meta($post_id, 'voted_ips', $voted_ips);

    wp_send_json_success('Vote recorded successfully.');
}
add_action('wp_ajax_handle_vote', 'handle_vote');


// Handle marking questions as resolved
function mark_question_resolved() {
    check_ajax_referer('resolve_question_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $user_id = get_current_user_id();

    if (current_user_can('edit_post', $post_id) || get_post_field('post_author', $post_id) == $user_id) {
        update_post_meta($post_id, 'is_resolved', 'on');
        wp_send_json_success('Question marked as resolved.');
    } else {
        wp_send_json_error('You do not have permission to mark this question as resolved.');
    }
}
add_action('wp_ajax_mark_question_resolved', 'mark_question_resolved');

// Register the Q&A page template
function register_qa_page_template($page_templates, $theme, $post) {
    $page_templates['template-qa.php'] = 'Q&A Page';
    return $page_templates;
}
add_filter('theme_page_templates', 'register_qa_page_template', 10, 3);

function load_qa_page_template($template) {
    if (get_page_template_slug() === 'template-qa.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/template-qa.php';
    }
    return $template;
}
add_filter('page_template', 'load_qa_page_template');

// Enqueue scripts and styles
function enqueue_qa_scripts() {
    wp_enqueue_style('sidebar-styles', plugin_dir_url(__FILE__) . 'assets/css/sidebar.css');
     wp_enqueue_style('restaurants-styles', plugin_dir_url(__FILE__) . 'assets/css/restaurants.css');
    wp_enqueue_style('category-styles', plugin_dir_url(__FILE__) . 'assets/css/category.css');
    wp_enqueue_style('state-styles', plugin_dir_url(__FILE__) . 'assets/css/state.css');
    wp_enqueue_style('qa-styles', plugin_dir_url(__FILE__) . 'assets/css/qa-styles.css');
    wp_enqueue_script('qa-scripts', plugin_dir_url(__FILE__) . 'assets/js/qa-scripts.js', array('jquery'), '1.0', true);
    wp_localize_script('qa-scripts', 'qa_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'submit_nonce' => wp_create_nonce('submit_question_nonce'),
        'vote_nonce' => wp_create_nonce('vote_nonce'),
        'resolve_nonce' => wp_create_nonce('resolve_question_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_qa_scripts');

// Debug query for taxonomy pages
function qa_debug_query($query) {
    if ( !is_admin() && $query->is_main_query() && is_tax('question_category') ) {
        error_log(print_r($query, true));
    }
}
add_action('pre_get_posts', 'qa_debug_query');

// Ensure custom post type is included in taxonomy queries
function qa_include_custom_post_type($query) {
    if ( !is_admin() && $query->is_main_query() && is_tax('question_category') ) {
        $query->set('post_type', 'qa_post');
    }
}
add_action('pre_get_posts', 'qa_include_custom_post_type');

// Flush rewrite rules on plugin activation
function qa_flush_rewrite_rules() {
    create_qa_post_type();
    create_question_category_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'qa_flush_rewrite_rules');

// Add this to your theme's functions.php or in your plugin file
function qa_template_redirect() {
    if (is_tax('question_category')) {
        $term = get_queried_object();
        $posts = get_posts(array(
            'post_type' => 'qa_post',
            'tax_query' => array(
                array(
                    'taxonomy' => 'question_category',
                    'field'    => 'slug',
                    'terms'    => $term->slug,
                ),
            ),
        ));
        
        if (empty($posts)) {
            error_log("No posts found for category: " . $term->slug);
        } else {
            error_log("Posts found for category: " . $term->slug . ". Count: " . count($posts));
        }
    }
}
add_action('template_redirect', 'qa_template_redirect');


// ==========================================
// 24. VOTING SYSTEM INTEGRATION
// ==========================================

// Include the voting system
require_once plugin_dir_path(__FILE__) . 'includes/voting-system.php';

// Activation hook for creating the votes table
register_activation_hook(__FILE__, 'check_and_create_votes_table');

// Also run this function on init to ensure the table exists

// Hook the asset enqueuing function
add_action('wp_enqueue_scripts', 'enqueue_voting_assets');

// ==========================================
// 25. RELATED LISTINGS
// ==========================================

// Include the related listings functionality
$related_listings_file = plugin_dir_path(__FILE__) . 'includes/related-listings.php';
if (file_exists($related_listings_file)) {
    require_once $related_listings_file;
} else {
    error_log('Related listings file not found: ' . $related_listings_file);
}

// If the functions from related-listings.php are not available, define fallback functions
if (!function_exists('ydp_get_related_listings')) {
    function ydp_get_related_listings($current_listing_id) {
        return array(); // Return an empty array as a fallback
    }
}

if (!function_exists('ydp_display_related_listings')) {
    function ydp_display_related_listings($content) {
        return $content; // Return the original content as a fallback
    }
}

// Enqueue related listings styles and scripts
function enqueue_related_listings_assets() {
    if (is_singular('directory_listing')) {
        $css_file = plugins_url('assets/css/related-listings.css', __FILE__);
        $js_file = plugins_url('assets/js/related-listings.js', __FILE__);
        
        if (file_exists(plugin_dir_path(__FILE__) . 'assets/css/related-listings.css')) {
            wp_enqueue_style('related-listings-style', $css_file);
        }
        
        if (file_exists(plugin_dir_path(__FILE__) . 'assets/js/related-listings.js')) {
            wp_enqueue_script('related-listings-script', $js_file, array('jquery'), '1.0', true);
        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_related_listings_assets');

// Add admin menu for related listings settings
function add_related_listings_menu() {
    add_submenu_page(
        'edit.php?post_type=directory_listing',
        'Related Listings Settings',
        'Related Listings',
        'manage_options',
        'related-listings-settings',
        'related_listings_settings_page'
    );
}
add_action('admin_menu', 'add_related_listings_menu');

// Create the settings page
function related_listings_settings_page() {
    ?>
    <div class="wrap">
        <h1>Related Listings Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ydp_related_listings_settings');
            do_settings_sections('ydp_related_listings_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function register_related_listings_settings() {
    register_setting('ydp_related_listings_settings', 'ydp_related_listings_enabled');
    register_setting('ydp_related_listings_settings', 'ydp_related_listings_count', array(
        'type' => 'integer',
        'sanitize_callback' => 'absint',
        'default' => 3,
    ));

    add_settings_section(
        'ydp_related_listings_main_section',
        'Related Listings Options',
        'related_listings_section_callback',
        'ydp_related_listings_settings'
    );

    add_settings_field(
        'ydp_related_listings_enabled',
        'Enable Related Listings',
        'related_listings_enabled_callback',
        'ydp_related_listings_settings',
        'ydp_related_listings_main_section'
    );

    add_settings_field(
        'ydp_related_listings_count',
        'Number of Related Listings',
        'related_listings_count_callback',
        'ydp_related_listings_settings',
        'ydp_related_listings_main_section'
    );
}
add_action('admin_init', 'register_related_listings_settings');

// Callbacks for settings fields
function related_listings_section_callback() {
    echo '<p>Configure settings for related listings display.</p>';
}

function related_listings_enabled_callback() {
    $enabled = get_option('ydp_related_listings_enabled', 1);
    echo '<input type="checkbox" name="ydp_related_listings_enabled" value="1" ' . checked(1, $enabled, false) . '/>';
}

function related_listings_count_callback() {
    $count = get_option('ydp_related_listings_count', 3);
    echo '<input type="number" name="ydp_related_listings_count" value="' . esc_attr($count) . '" min="1" max="10" />';
}

// ==========================================
// 26. RESTAURANT LISTINGS
// ==========================================

// Restaurant Custom Post Type
function create_restaurant_post_type() {
    $labels = array(
        'name' => 'Restaurants',
        'singular_name' => 'Restaurant',
        'menu_name' => 'Restaurants',
        'add_new' => 'Add New Restaurant',
        'add_new_item' => 'Add New Restaurant',
        'edit_item' => 'Edit Restaurant',
        'new_item' => 'New Restaurant',
        'view_item' => 'View Restaurant',
        'search_items' => 'Search Restaurants',
        'not_found' => 'No restaurants found',
        'not_found_in_trash' => 'No restaurants found in Trash',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-food',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'restaurants'),
        'show_in_menu' => true,
    );

    register_post_type('restaurant', $args);
}
add_action('init', 'create_restaurant_post_type');

// Restaurant Taxonomies
function create_restaurant_taxonomies() {
    // Cuisine Type Taxonomy
    register_taxonomy(
        'cuisine',
        'restaurant',
        array(
            'label' => 'Cuisine Types',
            'hierarchical' => true,
            'rewrite' => array('slug' => 'cuisine'),
        )
    );

    // Price Range Taxonomy
    register_taxonomy(
        'price_range',
        'restaurant',
        array(
            'label' => 'Price Range',
            'hierarchical' => false,
            'rewrite' => array('slug' => 'price-range'),
        )
    );
}
add_action('init', 'create_restaurant_taxonomies');

// Restaurant Custom Fields
if (!function_exists('create_restaurant_acf_fields')) {
    function create_restaurant_acf_fields() {
        if (function_exists('acf_add_local_field_group')):

            acf_add_local_field_group(array(
                'key' => 'group_restaurant',
                'title' => 'Restaurant Details',
                'fields' => array(
                    array(
                        'key' => 'field_restaurant_logo',
                        'label' => 'Logo',
                        'name' => 'restaurant_logo',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_street_address',
                        'label' => 'Street Address',
                        'name' => 'street_address',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_city',
                        'label' => 'City',
                        'name' => 'city',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_state',
                        'label' => 'State',
                        'name' => 'state',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_zip_code',
                        'label' => 'ZIP Code',
                        'name' => 'zip_code',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_phone_number',
                        'label' => 'Phone Number',
                        'name' => 'phone_number',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_website',
                        'label' => 'Website',
                        'name' => 'website',
                        'type' => 'url',
                    ),
                    array(
                        'key' => 'field_order_online_link',
                        'label' => 'Order Online Link',
                        'name' => 'order_online_link',
                        'type' => 'url',
                    ),
                    array(
                        'key' => 'field_opening_hours',
                        'label' => 'Opening Hours',
                        'name' => 'opening_hours',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_day',
                                'label' => 'Day',
                                'name' => 'day',
                                'type' => 'select',
                                'choices' => array(
                                    'monday' => 'Monday',
                                    'tuesday' => 'Tuesday',
                                    'wednesday' => 'Wednesday',
                                    'thursday' => 'Thursday',
                                    'friday' => 'Friday',
                                    'saturday' => 'Saturday',
                                    'sunday' => 'Sunday',
                                ),
                            ),
                            array(
                                'key' => 'field_open_time',
                                'label' => 'Open Time',
                                'name' => 'open_time',
                                'type' => 'time_picker',
                            ),
                            array(
                                'key' => 'field_close_time',
                                'label' => 'Close Time',
                                'name' => 'close_time',
                                'type' => 'time_picker',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_menu_url',
                        'label' => 'Menu URL',
                        'name' => 'menu_url',
                        'type' => 'url',
                    ),
                    array(
                       'key' => 'field_restaurant_gallery',
                        'label' => 'Restaurant Gallery',
                        'name' => 'restaurant_gallery',
                        'type' => 'gallery',
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_delivery_options',
                        'label' => 'Delivery Options',
                        'name' => 'delivery_options',
                        'type' => 'checkbox',
                        'choices' => array(
                            'delivery' => 'Delivery',
                            'takeout' => 'Takeout',
                            'dine_in' => 'Dine-in',
                        ),
                    ),
                    array(
                        'key' => 'field_reservation_required',
                        'label' => 'Reservation Required',
                        'name' => 'reservation_required',
                        'type' => 'true_false',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_average_wait_time',
                        'label' => 'Average Wait Time',
                        'name' => 'average_wait_time',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_signature_dishes',
                        'label' => 'Signature Dishes',
                        'name' => 'signature_dishes',
                        'type' => 'repeater',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_dish_name',
                                'label' => 'Dish Name',
                                'name' => 'dish_name',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_dish_price',
                                'label' => 'Price',
                                'name' => 'price',
                                'type' => 'number',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_alcohol_served',
                        'label' => 'Alcohol Served',
                        'name' => 'alcohol_served',
                        'type' => 'select',
                        'choices' => array(
                            'none' => 'None',
                            'beer_wine' => 'Beer/Wine',
                            'full_bar' => 'Full Bar',
                        ),
                    ),
                    array(
                        'key' => 'field_parking_options',
                        'label' => 'Parking Options',
                        'name' => 'parking_options',
                        'type' => 'checkbox',
                        'choices' => array(
                            'street' => 'Street',
                            'lot' => 'Parking Lot',
                            'valet' => 'Valet',
                        ),
                    ),
                    array(
                        'key' => 'field_wheelchair_accessible',
                        'label' => 'Wheelchair Accessible',
                        'name' => 'wheelchair_accessible',
                        'type' => 'true_false',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_noise_level',
                        'label' => 'Noise Level',
                        'name' => 'noise_level',
                        'type' => 'select',
                        'choices' => array(
                            'quiet' => 'Quiet',
                            'moderate' => 'Moderate',
                            'loud' => 'Loud',
                        ),
                    ),
                    array(
                        'key' => 'field_is_verified',
                        'label' => 'Verified Restaurant',
                        'name' => 'is_verified',
                        'type' => 'true_false',
                        'instructions' => 'Toggle this on if the restaurant is verified',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_top_rated',
                        'label' => 'Top Rated',
                        'name' => 'top_rated',
                        'type' => 'true_false',
                        'instructions' => 'Mark this restaurant as Top Rated',
                        'ui' => 1,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'restaurant',
                        ),
                    ),
                ),
            ));

        endif;
    }
    add_action('acf/init', 'create_restaurant_acf_fields');
}

// Debugging function to check if fields are being saved correctly
function debug_restaurant_fields($post_id) {
    if (get_post_type($post_id) === 'restaurant') {
        $fields = get_fields($post_id);
        error_log('Restaurant fields saved for post ' . $post_id . ': ' . print_r($fields, true));
    }
}
add_action('acf/save_post', 'debug_restaurant_fields', 20);

// Helper function to display restaurant fields in templates
function display_restaurant_fields() {
    $phone_number = get_field('phone_number');
    $street_address = get_field('street_address');
    $city = get_field('city');
    $state = get_field('state');
    $zip_code = get_field('zip_code');

    if ($phone_number) {
        echo '<p><strong>Phone:</strong> ' . esc_html($phone_number) . '</p>';
    }

    if ($street_address && $city && $state && $zip_code) {
        echo '<p><strong>Address:</strong><br>';
        echo esc_html($street_address) . '<br>';
        echo esc_html($city) . ', ' . esc_html($state) . ' ' . esc_html($zip_code);
        echo '</p>';
    }
    
    // Add more fields as needed
}

// Add Edit Restaurant Listing to WordPress Admin Menu
function add_edit_restaurant_menu_item() {
    add_submenu_page(
        'edit.php?post_type=restaurant',
        'Edit Restaurant Listing',
        'Edit Restaurant Listing',
        'edit_posts',
        'post.php?post_type=restaurant&action=edit',
        ''
    );
}
add_action('admin_menu', 'add_edit_restaurant_menu_item');

// Load custom edit template for restaurants
function load_restaurant_edit_template($template) {
    global $post;
    if ($post->post_type == 'restaurant' && is_admin()) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/edit-restaurant.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('single_template', 'load_restaurant_edit_template');

// Debug function to log template loading process for restaurants
function debug_restaurant_template_loading($template) {
    if (get_post_type() === 'restaurant') {
        error_log('Restaurant template being loaded: ' . $template);
    }
    return $template;
}
add_filter('template_include', 'debug_restaurant_template_loading', 1000);

// ==========================================
// 27. RESTAURANT REVIEWS WITH PHOTOS
// ==========================================

// Create a new table for storing review photos
function create_restaurant_review_photos_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'restaurant_review_photos';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        review_id mediumint(9) NOT NULL,
        photo_url varchar(255) NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (review_id) REFERENCES {$wpdb->prefix}restaurant_reviews(id) ON DELETE CASCADE
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Upgrade function to add foreign key if table already exists
function upgrade_restaurant_review_photos_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'restaurant_review_photos';

    // Check if the table exists
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        create_restaurant_review_photos_table();
        return;
    }

    // Check if the review_id column exists, if not add it
    $column = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'review_id'",
        DB_NAME, $table_name
    ));

    if(empty($column)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN review_id mediumint(9) NOT NULL");
    }

    // Add the foreign key constraint
    $wpdb->query("ALTER TABLE $table_name 
                  ADD CONSTRAINT fk_review_id 
                  FOREIGN KEY (review_id) 
                  REFERENCES {$wpdb->prefix}restaurant_reviews(id) 
                  ON DELETE CASCADE");
}

// Version checking and upgrade function
function check_and_upgrade_restaurant_reviews_plugin() {
    $current_version = get_option('restaurant_reviews_plugin_version', '0');
    if (version_compare($current_version, '1.1', '<')) {
        upgrade_restaurant_review_photos_table();
        update_option('restaurant_reviews_plugin_version', '1.1');
    }
}

// Activation hook
register_activation_hook(__FILE__, 'create_restaurant_review_photos_table');

// Run version check and upgrade on plugins loaded
add_action('plugins_loaded', 'check_and_upgrade_restaurant_reviews_plugin');

// Modify the review submission form to include photo uploads
function add_restaurant_review_form($restaurant_id = null) {
    if ($restaurant_id === null) {
        $restaurant_id = get_the_ID();
    }

    if (is_singular('restaurant')) {
        $current_user = wp_get_current_user();
        ?>
        <div class="restaurant-review-form-container">
            <h3>Leave a Review</h3>
            <form id="submit-restaurant-review-form" enctype="multipart/form-data">
                <?php wp_nonce_field('submit_restaurant_review', 'restaurant_review_nonce'); ?>
                <input type="hidden" name="restaurant_id" value="<?php echo $restaurant_id; ?>">
                <input type="hidden" name="user_id" value="<?php echo $current_user->ID; ?>">

                <!-- Existing form fields here -->

                <div class="form-group">
                    <label for="review_photos">Upload Photos (Max 5)</label>
                    <input type="file" name="review_photos[]" id="review_photos" multiple accept="image/*" max="5">
                    <div id="photo-preview"></div>
                </div>

                <button type="submit">Submit Review</button>
            </form>
        </div>
        <?php
    }
}

// Handle photo uploads
function handle_review_photo_uploads($review_id) {
    if (!empty($_FILES['review_photos']['name'][0])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        global $wpdb;
        $photo_table = $wpdb->prefix . 'restaurant_review_photos';

        foreach ($_FILES['review_photos']['name'] as $key => $value) {
            if ($_FILES['review_photos']['name'][$key]) {
                $file = array(
                    'name'     => $_FILES['review_photos']['name'][$key],
                    'type'     => $_FILES['review_photos']['type'][$key],
                    'tmp_name' => $_FILES['review_photos']['tmp_name'][$key],
                    'error'    => $_FILES['review_photos']['error'][$key],
                    'size'     => $_FILES['review_photos']['size'][$key]
                );
                
                $upload_overrides = array('test_form' => false);
                $upload = wp_handle_upload($file, $upload_overrides);

                if (!isset($upload['error'])) {
                    $wpdb->insert(
                        $photo_table,
                        array(
                            'review_id' => $review_id,
                            'photo_url' => $upload['url']
                        ),
                        array('%d', '%s')
                    );
                }
            }
        }
    }
}

// Modify the review submission handler
function handle_restaurant_review_submission() {
    check_ajax_referer('restaurant_review_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to submit a review.');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'restaurant_reviews';

    // Sanitize and validate input data
    $restaurant_id = intval($_POST['restaurant_id']);
    $user_id = intval($_POST['user_id']);
    $overall_rating = intval($_POST['overall_rating']);
    $food_rating = intval($_POST['food_rating']);
    $service_rating = intval($_POST['service_rating']);
    $ambience_rating = intval($_POST['ambience_rating']);
    $review_text = sanitize_textarea_field($_POST['review_text']);
    $date_of_visit = sanitize_text_field($_POST['date_of_visit']);

    $result = $wpdb->insert(
        $table_name,
        array(
            'restaurant_id' => $restaurant_id,
            'user_id' => $user_id,
            'rating' => $overall_rating,
            'food_rating' => $food_rating,
            'service_rating' => $service_rating,
            'ambience_rating' => $ambience_rating,
            'review_text' => $review_text,
            'date_of_visit' => $date_of_visit,
        ),
        array('%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s')
    );

    if ($result) {
        $review_id = $wpdb->insert_id;
        handle_review_photo_uploads($review_id);
        wp_send_json_success('Review submitted successfully and pending approval.');
    } else {
        wp_send_json_error('Error submitting review. Please try again.');
    }
}

// Display review photos
function display_review_photos($review_id) {
    global $wpdb;
    $photo_table = $wpdb->prefix . 'restaurant_review_photos';
    
    $photos = $wpdb->get_results($wpdb->prepare(
        "SELECT photo_url FROM $photo_table WHERE review_id = %d",
        $review_id
    ));

    if ($photos) {
        echo '<div class="review-photos">';
        foreach ($photos as $photo) {
            echo '<img src="' . esc_url($photo->photo_url) . '" alt="Review photo" class="review-photo">';
        }
        echo '</div>';
    }
}

// Modify the review display function
function display_restaurant_reviews($restaurant_id = null) {
    if ($restaurant_id === null) {
        $restaurant_id = get_the_ID();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'restaurant_reviews';

    $reviews = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE restaurant_id = %d AND status = 'approved' ORDER BY review_date DESC",
        $restaurant_id
    ));

    if ($reviews) {
        echo '<div class="restaurant-reviews">';
        echo '<h3>Customer Reviews</h3>';
        foreach ($reviews as $review) {
            $user_info = get_userdata($review->user_id);
            echo '<div class="single-review">';
            echo '<p class="review-meta">Rating: ' . esc_html($review->rating) . '/5 by ' . esc_html($user_info->display_name) . ' on ' . esc_html($review->review_date) . '</p>';
            echo '<p>Food: ' . esc_html($review->food_rating) . '/5 | Service: ' . esc_html($review->service_rating) . '/5 | Ambience: ' . esc_html($review->ambience_rating) . '/5</p>';
            echo '<p>Date of Visit: ' . esc_html($review->date_of_visit) . '</p>';
            echo '<p class="review-text">' . esc_html($review->review_text) . '</p>';
            display_review_photos($review->id);
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No reviews yet for this restaurant.</p>';
    }
}

// Add JavaScript for handling photo uploads and previews
function add_review_photo_scripts() {
    if (is_singular('restaurant')) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('#review_photos').on('change', function() {
                var preview = $('#photo-preview');
                preview.html('');
                var files = this.files;

                if (files) {
                    for (var i = 0; i < files.length; i++) {
                        if (i >= 5) break; // Limit to 5 photos
                        var file = files[i];
                        var reader = new FileReader();

                        reader.onload = function(e) {
                            preview.append('<img src="' + e.target.result + '" class="photo-preview-thumb">');
                        }

                        reader.readAsDataURL(file);
                    }
                }
            });
        });
        </script>
        <?php
    }
}

// Add necessary action hooks
add_action('wp_footer', 'add_review_photo_scripts');


// ==========================================
// 28. LARGE DATA HANDELING
// ==========================================


// Define plugin constants if not already defined
if (!defined('YOUR_PLUGIN_PATH')) {
    define('YOUR_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

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

// Register activation hook
register_activation_hook(__FILE__, 'create_large_listings_table');

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

// Import function
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

// Admin page for imports
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

function debug_large_listings($state) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'large_listings';
    
    $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE state = %s", $state);
    $count = $wpdb->get_var($query);
    
    error_log("Number of large listings for $state: $count");
    
    $sample_query = $wpdb->prepare("SELECT * FROM $table_name WHERE state = %s LIMIT 5", $state);
    $sample_results = $wpdb->get_results($sample_query);
    error_log("Sample results: " . print_r($sample_results, true));
}

// Register custom page template
function register_large_listings_state_template($page_templates, $theme, $post) {
    $page_templates['page-large-listings-state.php'] = __('Large Listings State Page', 'your-text-domain');
    return $page_templates;
}
add_filter('theme_page_templates', 'register_large_listings_state_template', 10, 3);

// Load the custom template
function load_large_listings_state_template($template) {
    if (get_page_template_slug() === 'page-large-listings-state.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/page-large-listings-state.php';
    }
    return $template;
}
add_filter('page_template', 'load_large_listings_state_template');

//Sidebar 
// Register the sidebar
// AJAX handler for loading more categories

// AJAX handler for searching categories
add_action('wp_ajax_search_categories', 'search_categories');
add_action('wp_ajax_nopriv_search_categories', 'search_categories');

function search_categories() {
    // Get search term from AJAX request
    $search_term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';

    // Fetch categories matching the search term
    $categories = get_terms(array(
        'taxonomy' => 'business_category',
        'hide_empty' => false,
        'name__like' => $search_term,
    ));

    // Check if categories were found and prepare response
    if (!empty($categories) && !is_wp_error($categories)) {
        $response = '';
        foreach ($categories as $category) {
            $category_link = get_term_link($category);
            $response .= '<li><a href="' . esc_url($category_link) . '">' . esc_html($category->name) . '</a></li>';
        }
        wp_send_json_success($response);
    } else {
        wp_send_json_error('No matching categories found.');
    }
}


// AJAX handler for loading more categories
add_action('wp_ajax_load_more_categories', 'load_more_categories');
add_action('wp_ajax_nopriv_load_more_categories', 'load_more_categories');

function load_more_categories() {
    $selected_letter = isset($_POST['letter']) ? sanitize_text_field($_POST['letter']) : 'A';
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $categories_per_page = 10; // Number of categories to load each time

    // Get all categories from custom taxonomy
    $categories = get_terms(array(
        'taxonomy' => 'business_category',
        'hide_empty' => false,
    ));

    // Organize categories by the starting letter
    $sorted_categories = [];
    foreach ($categories as $category) {
        $first_letter = strtoupper($category->name[0]);
        if (!isset($sorted_categories[$first_letter])) {
            $sorted_categories[$first_letter] = [];
        }
        $sorted_categories[$first_letter][] = $category;
    }

    // Prepare response for selected letter and offset
    $response = '';
    if (isset($sorted_categories[$selected_letter])) {
        $paged_categories = array_slice($sorted_categories[$selected_letter], $offset, $categories_per_page);

        // Build the list items HTML for categories
        foreach ($paged_categories as $category) {
            $category_link = get_term_link($category);
            $response .= '<li><a href="' . esc_url($category_link) . '">' . esc_html($category->name) . '</a></li>';
        }

        // Check if there are more categories to load
        $has_more = count($sorted_categories[$selected_letter]) > $offset + $categories_per_page;
        wp_send_json_success(['html' => $response, 'has_more' => $has_more]);
    } else {
        wp_send_json_error('No categories found for this letter.');
    }
}


function display_business_category_sidebar() {
    
       echo '<div class="widget">';
    echo '<h3 class="widget-title">Search Business Categories</h3>';

    // Search input field
    echo '<input type="text" id="category-search-input" placeholder="Search categories...">';

    // Category list
    echo '<ul class="business-category-list">';
    echo '<p>Type in the search box to filter categories.</p>';
    echo '</ul>';
    echo '</div>';
    echo '<div class="widget">';
    echo '<h3 class="widget-title">All Business Categories</h3>';

    // Display alphabetical filter
    echo '<div class="alphabet-filter">';
    foreach (range('A', 'Z') as $letter) {
        echo '<a href="#" class="alphabet-link">' . $letter . '</a> ';
    }
    echo '</div>';

    // Empty category list for AJAX content
    echo '<ul class="business-category-list">';
    echo '<p>Select a letter to view categories.</p>';
    echo '</ul>';

    // Load More button
    echo '<button class="load-more-button" style="display:none;">Load More</button>';
    echo '</div>';
}

// Enqueue the script for AJAX
function my_ajax_scripts() {
    wp_enqueue_script('my-ajax-script', get_template_directory_uri() . '/js/my-ajax-script.js', array('jquery'), null, true);
    wp_localize_script('my-ajax-script', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'my_ajax_scripts');




/*function display_business_category_sidebar() {
    // Get categories from the custom taxonomy 'business_category'
    $categories = get_terms(array(
        'taxonomy' => 'business_category',
        'hide_empty' => false,
    ));

    // Check if there are any categories returned
    if (!empty($categories) && !is_wp_error($categories)) {
        echo '<div class="widget">';
        echo '<h3 class="widget-title">Business Categories</h3>';
        echo '<ul class="business-category-list">';

        // Loop through the categories and display them
        foreach ($categories as $category) {
            // Get the category link
            $category_link = get_term_link($category);
            
            // Display each category as a list item with a link
            echo '<li><a href="' . esc_url($category_link) . '">' . esc_html($category->name) . '</a></li>';
        }

        echo '</ul>';
        echo '</div>';
    } else {
        echo '<p>No categories found.</p>';
    }
}*/

// Hook the function to display the sidebar in the proper location (e.g., widget area or directly in template)
add_action('wp_sidebar', 'display_business_category_sidebar');


// ==========================================
// 29. FINANCIAL MARKETS
// ==========================================



// AJAX handler for market data
function fetch_market_data() {
    try {
        check_ajax_referer('market_data_nonce', 'nonce');

        // Yahoo Finance API URL with all necessary parameters
        $url = 'https://query1.finance.yahoo.com/v8/finance/quote?symbols=^DJI,^GSPC,^IXIC,^GSPTSE,^FTSE,^GDAXI,^FCHI,^N225,^HSI,^AXJO&fields=shortName,regularMarketPrice,regularMarketChange,regularMarketChangePercent,marketState,regularMarketTime';
        
        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'headers' => array(
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'application/json',
                'Origin' => home_url(),
                'Referer' => home_url()
            )
        ));

        if (is_wp_error($response)) {
            error_log('API Request Error: ' . $response->get_error_message());
            throw new Exception('Failed to fetch live market data');
        }

        $body = wp_remote_retrieve_body($response);
        $response_code = wp_remote_retrieve_response_code($response);

        // Log response for debugging
        error_log('Yahoo Finance API Response Code: ' . $response_code);
        error_log('Yahoo Finance API Response: ' . substr($body, 0, 1000));

        if ($response_code !== 200) {
            throw new Exception('API returned status code: ' . $response_code);
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse API response');
        }

        if (!isset($data['quoteResponse']['result']) || empty($data['quoteResponse']['result'])) {
            throw new Exception('No market data in response');
        }

        // Format the response data
        $quotes = $data['quoteResponse']['result'];
        $formatted_data = array(
            'americas' => array(),
            'europe' => array(),
            'asia' => array()
        );

        foreach ($quotes as $quote) {
            $market_data = array(
                'symbol' => $quote['symbol'],
                'name' => $quote['shortName'] ?? $quote['symbol'],
                'price' => $quote['regularMarketPrice'] ?? 0,
                'change' => $quote['regularMarketChangePercent'] ?? 0,
                'state' => $quote['marketState'] ?? 'CLOSED',
                'timestamp' => $quote['regularMarketTime'] ?? time()
            );

            // Categorize markets by region
            if (in_array($quote['symbol'], array('^DJI', '^GSPC', '^IXIC', '^GSPTSE'))) {
                $formatted_data['americas'][] = $market_data;
            } elseif (in_array($quote['symbol'], array('^FTSE', '^GDAXI', '^FCHI'))) {
                $formatted_data['europe'][] = $market_data;
            } elseif (in_array($quote['symbol'], array('^N225', '^HSI', '^AXJO'))) {
                $formatted_data['asia'][] = $market_data;
            }
        }

        // Add timestamp to response
        $response_data = array(
            'quotes' => $formatted_data,
            'timestamp' => time(),
            'server_time' => current_time('timestamp')
        );

        wp_send_json_success($response_data);

    } catch (Exception $e) {
        error_log('Market Data Error: ' . $e->getMessage());
        
        // Return error to client
        wp_send_json_error(array(
            'message' => $e->getMessage(),
            'code' => 'MARKET_DATA_ERROR'
        ));
    }
}

add_action('wp_ajax_fetch_market_data', 'fetch_market_data');
add_action('wp_ajax_nopriv_fetch_market_data', 'fetch_market_data');

// Optional: Add a function to test the API connection
function test_market_api_connection() {
    $url = 'https://query1.finance.yahoo.com/v8/finance/quote?symbols=^DJI&fields=regularMarketPrice';
    
    $response = wp_remote_get($url, array(
        'timeout' => 15,
        'headers' => array(
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        )
    ));

    if (is_wp_error($response)) {
        error_log('API Test Error: ' . $response->get_error_message());
        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    error_log('API Test Response Code: ' . $response_code);
    error_log('API Test Response: ' . substr($body, 0, 500));

    return $response_code === 200;
}


// Check Elementor installation
function check_elementor_dependency() {
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', function() {
            printf(
                '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
                __('Directory Listings requires Elementor to be installed and activated.', 'directory-listings')
            );
        });
        return false;
    }
    return true;
}

// Register Widget Category
function add_directory_listings_category($elements_manager) {
    $elements_manager->add_category(
        'directory-listings',
        [
            'title' => __('Directory Listings', 'directory-listings'),
            'icon' => 'fa fa-plug',
        ]
    );
}

// Register Widgets
function register_directory_listings_widget() {
    if (!check_elementor_dependency()) return;
    
    // Include widget files
    require_once(__DIR__ . '/widgets/dynamic-top-listings-widget.php');
    require_once(__DIR__ . '/widgets/manual-top-listings-widget.php');
    require_once(__DIR__ . '/widgets/state-categories-widget.php');
    require_once(__DIR__ . '/widgets/class-dynamic-grid-listings-widget.php'); // Added new grid widget
    
    // Register category
    add_action('elementor/elements/categories_registered', 'add_directory_listings_category');
    
    // Register widgets
    add_action('elementor/widgets/register', function($widgets_manager) {
        $widgets_manager->register(new \Dynamic_Top_Listings_Widget());
        $widgets_manager->register(new \Manual_Top_Listings_Widget());
        $widgets_manager->register(new \State_Categories_Widget());
        $widgets_manager->register(new \Dynamic_Grid_Listings_Widget()); // Added new grid widget registration
    });
}
add_action('init', 'register_directory_listings_widget');





//cities
// Add custom columns to the admin listing page
function add_custom_admin_columns($columns) {
    $date_column = $columns['date'];
    unset($columns['date']); // Remove date temporarily
    
    $columns['city'] = __('City', 'directory-listings');
    $columns['post_id'] = __('Post #', 'directory-listings');
    $columns['date'] = $date_column; // Re-add date at the end
    
    return $columns;
}
add_filter('manage_directory_listing_posts_columns', 'add_custom_admin_columns');

// Populate custom columns
function populate_custom_admin_columns($column, $post_id) {
    switch ($column) {
        case 'city':
            $city = get_field('city', $post_id);
            echo $city ? esc_html($city) : '—';
            break;
        case 'post_id':
    echo '<a href="' . get_edit_post_link($post_id) . '">#' . $post_id . '</a>';
    break;
    }
}
add_action('manage_directory_listing_posts_custom_column', 'populate_custom_admin_columns', 10, 2);

// Make columns sortable
function make_custom_columns_sortable($columns) {
    $columns['city'] = 'city';
    $columns['post_id'] = 'ID';
    return $columns;
}
add_filter('manage_edit-directory_listing_sortable_columns', 'make_custom_columns_sortable');

// Add city filter dropdown to admin
function add_admin_city_filter() {
    global $typenow;
    if ($typenow !== 'directory_listing') return;

    $current_city = isset($_GET['filter_city']) ? sanitize_text_field($_GET['filter_city']) : '';
    
    // Get all unique cities
    global $wpdb;
    $cities = $wpdb->get_col($wpdb->prepare("
        SELECT DISTINCT pm.meta_value 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = %s 
        AND pm.meta_value != ''
        AND p.post_type = %s
        AND p.post_status = 'publish'
        ORDER BY pm.meta_value ASC
    ", 'city', 'directory_listing'));

    if (!empty($cities)) {
        echo '<select name="filter_city">';
        echo '<option value="">' . __('All Cities', 'directory-listings') . '</option>';
        foreach ($cities as $city) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($city),
                selected($city, $current_city, false),
                esc_html($city)
            );
        }
        echo '</select>';
    }
}
add_action('restrict_manage_posts', 'add_admin_city_filter');

// Filter admin listings by city
function filter_admin_listings_by_city($query) {
    global $pagenow, $typenow;
    
    if (is_admin() && 
        $pagenow === 'edit.php' && 
        $typenow === 'directory_listing' && 
        isset($_GET['filter_city']) && 
        $_GET['filter_city'] !== '') {
        
        $query->query_vars['meta_key'] = 'city';
        $query->query_vars['meta_value'] = sanitize_text_field($_GET['filter_city']);
    }
}

// Add these functions to directory-listings-functions.php

function get_available_states() {
    return get_terms(array(
        'taxonomy' => 'state',
        'hide_empty' => true,
        'parent' => 0
    ));
}

function get_cities_for_state($state_slug) {
    global $wpdb;
    
    return $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT pm.meta_value 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
        INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
        WHERE pm.meta_key = 'city'
        AND pm.meta_value != ''
        AND p.post_type = 'directory_listing'
        AND p.post_status = 'publish'
        AND t.slug = %s
        AND tt.taxonomy = 'state'
        ORDER BY pm.meta_value ASC",
        $state_slug
    ));
}

// AJAX handler for getting cities
function handle_get_cities_ajax() {
    check_ajax_referer('get_cities_nonce', 'nonce');
    
    $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';
    $cities = array();
    
    if ($state) {
        $cities = get_cities_for_state($state);
    }
    
    wp_send_json_success(array('cities' => $cities));
}
add_action('wp_ajax_get_cities', 'handle_get_cities_ajax');
add_action('wp_ajax_nopriv_get_cities', 'handle_get_cities_ajax');

// Function to modify query based on location filters
function modify_listings_query($query) {
    if (!is_admin() && $query->is_main_query() && 
        (is_tax('business_category') || is_tax('state'))) {
        
        // Add state filter
        if (isset($_GET['state']) && !empty($_GET['state'])) {
            $state_slug = sanitize_text_field($_GET['state']);
            
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'state',
                    'field' => 'slug',
                    'terms' => $state_slug
                )
            ));
        }
        
        // Add city filter
        if (isset($_GET['city']) && !empty($_GET['city'])) {
            $city = sanitize_text_field($_GET['city']);
            
            $query->set('meta_query', array(
                array(
                    'key' => 'city',
                    'value' => $city,
                    'compare' => '='
                )
            ));
        }
    }
}
add_action('pre_get_posts', 'modify_listings_query');

// Function to render location filters
function render_location_filters() {
    $current_state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
    $current_city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
    $states = get_available_states();
    
    ob_start();
    ?>
    <div class="location-filters">
        <select name="state" id="state-filter" class="filter-btn">
            <option value=""><?php _e('All States', 'directory-listings'); ?></option>
            <?php foreach ($states as $state) : ?>
                <option value="<?php echo esc_attr($state->slug); ?>" 
                        <?php selected($current_state, $state->slug); ?>>
                    <?php echo esc_html($state->name); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="city" id="city-filter" class="filter-btn" <?php echo empty($current_state) ? 'disabled' : ''; ?>>
            <option value=""><?php _e('All Cities', 'directory-listings'); ?></option>
            <?php 
            if ($current_state) {
                $cities = get_cities_for_state($current_state);
                foreach ($cities as $city) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($city),
                        selected($current_city, $city, false),
                        esc_html($city)
                    );
                }
            }
            ?>
        </select>
    </div>
    <?php
    return ob_get_clean();
}





add_action('pre_get_posts', 'filter_admin_listings_by_city');




// Update this function in your main plugin file
function enqueue_directory_filter_scripts() {
    if (is_tax('business_category') || is_tax('state')) {
        wp_enqueue_script(
            'directory-filters', 
            plugins_url('assets/js/location-filters.js', __FILE__),
            array('jquery'),
            '1.0',
            true
        );

        // Pass AJAX URL and nonce to JavaScript
        wp_localize_script('directory-filters', 'directory_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('directory_filter_nonce'),
            'current_category' => get_queried_object_id()
        ));
    }
}

// AJAX handler for getting cities
function get_cities_ajax_handler() {
    check_ajax_referer('directory_filter_nonce', 'nonce');

    $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    if (empty($state)) {
        wp_send_json_error('State is required');
    }

    $cities = get_state_category_cities($state, $category_id);
    wp_send_json_success(array('cities' => $cities));
}
add_action('wp_ajax_get_cities', 'get_cities_ajax_handler');
add_action('wp_ajax_nopriv_get_cities', 'get_cities_ajax_handler');

// AJAX handler for filtering listings
function filter_listings_ajax_handler() {
    check_ajax_referer('directory_filter_nonce', 'nonce');

    $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';
    $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    $category = get_queried_object();

    $args = array(
        'post_type' => 'directory_listing',
        'posts_per_page' => 6,
        'tax_query' => array(
            array(
                'taxonomy' => 'business_category',
                'field' => 'term_id',
                'terms' => $category->term_id,
            )
        )
    );

    if (!empty($state)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'state',
            'field' => 'slug',
            'terms' => $state
        );
    }

    if (!empty($city)) {
        $args['meta_query'] = array(
            array(
                'key' => 'city',
                'value' => $city,
                'compare' => '='
            )
        );
    }

    $listings = new WP_Query($args);
    ob_start();

    if ($listings->have_posts()) {
        while ($listings->have_posts()) {
            $listings->the_post();
            get_template_part('template-parts/listing-card');
        }
    } else {
        echo '<p class="no-results">No listings found matching your criteria.</p>';
    }

    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_filter_listings', 'filter_listings_ajax_handler');
add_action('wp_ajax_nopriv_filter_listings', 'filter_listings_ajax_handler');




// related lsitings
/**
 * Get related businesses based on categories and location
 * This function should be added to directory-listings-functions.php
 */
if (!function_exists('get_related_businesses')) {
    function get_related_businesses($post_id, $limit = 3) {
        // Get current business categories and location
        $categories = wp_get_post_terms($post_id, 'business_category', array('fields' => 'ids'));
        $states = wp_get_post_terms($post_id, 'state', array('fields' => 'ids'));
        $city = get_field('city', $post_id);

        // Build query arguments
        $args = array(
            'post_type' => 'directory_listing',
            'posts_per_page' => $limit,
            'post__not_in' => array($post_id),
            'post_status' => 'publish',
            'orderby' => 'rand',
        );

        // Add taxonomy queries
        $tax_queries = array();

        if (!empty($categories) && !is_wp_error($categories)) {
            $tax_queries[] = array(
                'taxonomy' => 'business_category',
                'field' => 'term_id',
                'terms' => $categories,
            );
        }

        if (!empty($states) && !is_wp_error($states)) {
            $tax_queries[] = array(
                'taxonomy' => 'state',
                'field' => 'term_id',
                'terms' => $states,
            );
        }

        if (!empty($tax_queries)) {
            $args['tax_query'] = array_merge(array('relation' => 'AND'), $tax_queries);
        }

        // Add city meta query if available
        if (!empty($city)) {
            $args['meta_query'] = array(
                array(
                    'key' => 'city',
                    'value' => $city,
                    'compare' => 'LIKE',
                )
            );
        }

        // Return WP_Query instance
        return new WP_Query($args);
    }
}


function register_directory_templates() {
    // Register main template
    add_filter('theme_page_templates', function($templates) {
        $templates['directory-listing-template.php'] = 'Single Directory Listing';
        return $templates;
    });

    // Register template parts
    $template_parts = ['meta-tags', 'listing/header', 'listing/key-info', 'listing/services', 
                      'listing/gallery', 'listing/reviews', 'listing/sidebar'];
                      
    foreach ($template_parts as $part) {
        add_filter("theme_{$part}_templates", function($templates) use ($part) {
            $path = plugin_dir_path(__FILE__) . "templates/template-parts/{$part}.php";
            if (file_exists($path)) {
                $templates[$path] = basename($part);
            }
            return $templates;
        });
    }
}
add_action('init', 'register_directory_templates');

// Override template hierarchy
function load_directory_template($template) {
    if (is_page_template('directory-listing-template.php')) {
        $template = plugin_dir_path(__FILE__) . 'templates/directory-listing-template.php';
    }
    return $template;
}
add_filter('template_include', 'load_directory_template');






/**
 * Display related businesses HTML
 * Helper function to render related businesses section
 */
if (!function_exists('display_related_businesses')) {
    function display_related_businesses($post_id, $limit = 3) {
        $related_query = get_related_businesses($post_id, $limit);

        if ($related_query->have_posts()) {
            echo '<div class="related-businesses">';
            
            while ($related_query->have_posts()) {
                $related_query->the_post();
                $logo = get_field('logo');
                $rating = get_field('overall_rating');
                $review_count = get_field('review_count');
                
                // Get placeholder image URL
                $placeholder_url = plugins_url('assets/images/placeholder.png', dirname(__FILE__));
                
                ?>
                <div class="related-business-item">
                    <div class="business-logo">
                        <?php if ($logo && isset($logo['url'])) : ?>
                            <img src="<?php echo esc_url($logo['url']); ?>" 
                                 alt="<?php echo esc_attr(get_the_title()); ?>">
                        <?php else : ?>
                            <img src="<?php echo esc_url($placeholder_url); ?>" 
                                 alt="<?php echo esc_attr(get_the_title()); ?>">
                        <?php endif; ?>
                    </div>
                    
                    <div class="business-info">
                        <h4>
                            <a href="<?php the_permalink(); ?>">
                                <?php echo esc_html(get_the_title()); ?>
                            </a>
                        </h4>
                        
                        <?php if ($rating > 0) : ?>
                            <div class="business-rating">
                                <div class="stars">
                                    <?php echo str_repeat('★', round($rating)); ?>
                                </div>
                                <span class="rating-text">
                                    <?php echo number_format($rating, 1); ?>
                                    <?php if ($review_count > 0) : ?>
                                        (<?php echo $review_count; ?> reviews)
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        // Get and display business categories
                        $categories = get_the_terms(get_the_ID(), 'business_category');
                        if ($categories && !is_wp_error($categories)) : ?>
                            <div class="business-categories">
                                <?php echo esc_html($categories[0]->name); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            wp_reset_postdata();
        }
    }
}

function enqueue_directory_styles() {
    wp_enqueue_style('directory-styles', 
        plugins_url('assets/css/directory-styles.css', __FILE__),
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_directory_styles');

function generate_business_schema($post_id) {
    $schema = array(
        "@context" => "http://schema.org",
        "@type" => "LocalBusiness",
        "name" => get_the_title($post_id),
        "image" => get_field('logo', $post_id) ? get_field('logo', $post_id)['url'] : '',
        "telephone" => get_field('phone_number', $post_id),
        "email" => get_field('email', $post_id),
        "url" => get_field('website_link', $post_id),
        "address" => array(
            "@type" => "PostalAddress",
            "addressRegion" => get_field('state', $post_id),
            "addressCountry" => "US"
        )
    );

    // Add ratings if they exist
    $rating = get_field('overall_rating', $post_id);
    $review_count = get_field('review_count', $post_id);
    if (is_numeric($rating) && is_numeric($review_count)) {
        $schema['aggregateRating'] = array(
            "@type" => "AggregateRating",
            "ratingValue" => floatval($rating),
            "reviewCount" => intval($review_count)
        );
    }

    // Add business hours
    $hours = get_field('business_hours', $post_id);
    if (!empty($hours)) {
        $schema['openingHours'] = array_map(function($hour) {
            if (isset($hour['day_of_week'], $hour['opening_time'], $hour['closing_time'])) {
                return ucfirst(substr($hour['day_of_week'], 0, 2)) . 
                       " " . $hour['opening_time'] . "-" . $hour['closing_time'];
            }
            return null;
        }, array_filter($hours));
    }

    // Add services
    $services = get_field('services_offered', $post_id);
    if (!empty($services)) {
        $schema['makesOffer'] = array_map(function($service) {
            return array(
                "@type" => "Offer",
                "itemOffered" => array(
                    "@type" => "Service",
                    "name" => $service['service_name']
                )
            );
        }, $services);
    }

    return $schema;
}


function is_24_hours($opening_time, $closing_time) {
    // Add 12AM-12AM pattern for 24-hour operations
    $midnight_variants = ['12:00 AM', '00:00', '12:00 am', '00:00:00', '12:00AM', '12am'];
    $midnight_close_variants = ['12:00 AM', '00:00', '12:00 am', '00:00:00', '12:00AM', '12am', '24:00'];
    
    // Check if both times are midnight variants or if it's a 12AM-12AM pattern
    return (in_array($opening_time, $midnight_variants) && in_array($closing_time, $midnight_close_variants)) || 
           ($opening_time === $closing_time && in_array($opening_time, $midnight_variants));
}

function get_business_timezone($state) {
    $timezones = array(
        'Alabama' => 'America/Chicago',
        'Alaska' => 'America/Anchorage',
        'Arizona' => 'America/Phoenix',
        'Arkansas' => 'America/Chicago',
        'California' => 'America/Los_Angeles',
        'Colorado' => 'America/Denver',
        'Connecticut' => 'America/New_York',
        'Delaware' => 'America/New_York',
        'Florida' => 'America/New_York',
        'Georgia' => 'America/New_York',
        'Hawaii' => 'Pacific/Honolulu',
        'Idaho' => 'America/Boise',
        'Illinois' => 'America/Chicago',
        'Indiana' => 'America/Indiana/Indianapolis',
        'Iowa' => 'America/Chicago',
        'Kansas' => 'America/Chicago',
        'Kentucky' => 'America/New_York',
        'Louisiana' => 'America/Chicago',
        'Maine' => 'America/New_York',
        'Maryland' => 'America/New_York',
        'Massachusetts' => 'America/New_York',
        'Michigan' => 'America/Detroit',
        'Minnesota' => 'America/Chicago',
        'Mississippi' => 'America/Chicago',
        'Missouri' => 'America/Chicago',
        'Montana' => 'America/Denver',
        'Nebraska' => 'America/Chicago',
        'Nevada' => 'America/Los_Angeles',
        'New Hampshire' => 'America/New_York',
        'New Jersey' => 'America/New_York',
        'New Mexico' => 'America/Denver',
        'New York' => 'America/New_York',
        'North Carolina' => 'America/New_York',
        'North Dakota' => 'America/Chicago',
        'Ohio' => 'America/New_York',
        'Oklahoma' => 'America/Chicago',
        'Oregon' => 'America/Los_Angeles',
        'Pennsylvania' => 'America/New_York',
        'Rhode Island' => 'America/New_York',
        'South Carolina' => 'America/New_York',
        'South Dakota' => 'America/Chicago',
        'Tennessee' => 'America/Chicago',
        'Texas' => 'America/Chicago',
        'Utah' => 'America/Denver',
        'Vermont' => 'America/New_York',
        'Virginia' => 'America/New_York',
        'Washington' => 'America/Los_Angeles',
        'West Virginia' => 'America/New_York',
        'Wisconsin' => 'America/Chicago',
        'Wyoming' => 'America/Denver'
    );
    
    return isset($timezones[$state]) ? $timezones[$state] : wp_timezone_string();
}

function get_current_business_hours($business_hours) {
    if (empty($business_hours)) return false;
    
    // Get business state and timezone
    $state = get_field('state');
    $timezone = get_business_timezone($state);
    
    // Create DateTime object with business timezone
    $datetime = new DateTime('now', new DateTimeZone($timezone));
    $current_day = strtolower($datetime->format('l'));
    $current_time = $datetime->format('H:i');
    
    foreach ($business_hours as $hours) {
        if (strtolower($hours['day_of_week']) === $current_day) {
            if (empty($hours['opening_time']) || empty($hours['closing_time'])) {
                return [
                    'is_open' => false,
                    'next_open' => get_next_open_time($business_hours, $timezone),
                    'timezone' => $timezone
                ];
            }
            
            // Check if it's a 24-hour operation
            if (is_24_hours($hours['opening_time'], $hours['closing_time'])) {
                return [
                    'is_open' => true,
                    'is_24_hours' => true,
                    'next_open' => null,
                    'timezone' => $timezone
                ];
            }
            
            // Convert times to DateTime objects for comparison
            try {
                $current_dt = DateTime::createFromFormat('H:i', $current_time, new DateTimeZone($timezone));
                $opening_dt = DateTime::createFromFormat('h:i A', $hours['opening_time'], new DateTimeZone($timezone));
                $closing_dt = DateTime::createFromFormat('h:i A', $hours['closing_time'], new DateTimeZone($timezone));
                
                if (!$opening_dt || !$closing_dt) {
                    // Fallback to strtotime if DateTime parsing fails
                    $is_open = strtotime($current_time) >= strtotime($hours['opening_time']) && 
                              strtotime($current_time) < strtotime($hours['closing_time']);
                } else {
                    $is_open = $current_dt >= $opening_dt && $current_dt < $closing_dt;
                }
                
                return [
                    'is_open' => $is_open,
                    'is_24_hours' => false,
                    'closes' => $hours['closing_time'],
                    'next_open' => $is_open ? null : get_next_open_time($business_hours, $timezone),
                    'timezone' => $timezone
                ];
            } catch (Exception $e) {
                error_log('Error processing business hours: ' . $e->getMessage());
                return false;
            }
        }
    }
    
    return false;
}

function get_next_open_time($business_hours, $timezone = null) {
    if (!$timezone) {
        $state = get_field('state');
        $timezone = get_business_timezone($state);
    }
    
    $datetime = new DateTime('now', new DateTimeZone($timezone));
    $current_day = strtolower($datetime->format('l'));
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    $current_index = array_search($current_day, $days);
    
    // First check remaining time today
    $today_hours = null;
    foreach ($business_hours as $hours) {
        if (strtolower($hours['day_of_week']) === $current_day) {
            $today_hours = $hours;
            break;
        }
    }

    if ($today_hours && !empty($today_hours['opening_time'])) {
        $current_time = $datetime->format('H:i');
        $opening_time = date('H:i', strtotime($today_hours['opening_time']));
        if ($opening_time > $current_time) {
            return 'Today at ' . format_business_hours($today_hours['opening_time'], $timezone);
        }
    }
    
    // Check next 7 days
    for ($i = 1; $i <= 7; $i++) {
        $next_index = ($current_index + $i) % 7;
        $next_day = $days[$next_index];
        
        foreach ($business_hours as $hours) {
            if (strtolower($hours['day_of_week']) === $next_day && 
                !empty($hours['opening_time'])) {
                $day_name = $i === 1 ? 'Tomorrow' : ucfirst($next_day);
                return $day_name . ' at ' . format_business_hours($hours['opening_time'], $timezone);
            }
        }
    }
    
    return 'soon';
}

function format_business_hours($time, $timezone) {
    if (empty($time)) return '';
    try {
        $dt = new DateTime($time, new DateTimeZone($timezone));
        return $dt->format('g:i A');
    } catch (Exception $e) {
        return date('g:i A', strtotime($time));
    }
}

/**
 * Debug function for related businesses
 * Use this function to troubleshoot if related businesses aren't showing up
 */
if (!function_exists('debug_related_businesses')) {
    function debug_related_businesses($post_id) {
        if (WP_DEBUG) {
            $related = get_related_businesses($post_id);
            error_log('Related Businesses Debug for post ' . $post_id);
            error_log('Query: ' . print_r($related->request, true));
            error_log('Found Posts: ' . $related->found_posts);
            
            if ($related->have_posts()) {
                while ($related->have_posts()) {
                    $related->the_post();
                    error_log('Related Business Found: ' . get_the_title() . ' (ID: ' . get_the_ID() . ')');
                }
                wp_reset_postdata();
            } else {
                error_log('No related businesses found');
            }
        }
    }
}

function localize_directory_scripts() {
    if (is_tax('business_category') || is_post_type_archive('directory_listing')) {
        wp_localize_script('directory-filters', 'directoryAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('directory_filter_nonce'),
            'category_id' => get_queried_object_id()
        ));
    }
}
add_action('wp_enqueue_scripts', 'localize_directory_scripts');


// Register the FAQ post type
function create_faq_post_type() {
    $labels = array(
        'name'               => __('FAQs', 'directory-listings'),
        'singular_name'      => __('FAQ', 'directory-listings'),
        'menu_name'          => __('FAQs', 'directory-listings'),
        'add_new'           => __('Add New', 'directory-listings'),
        'add_new_item'      => __('Add New FAQ', 'directory-listings'),
        'edit_item'         => __('Edit FAQ', 'directory-listings'),
        'new_item'          => __('New FAQ', 'directory-listings'),
        'view_item'         => __('View FAQ', 'directory-listings'),
        'search_items'      => __('Search FAQs', 'directory-listings'),
        'not_found'         => __('No FAQs found', 'directory-listings'),
        'not_found_in_trash'=> __('No FAQs found in trash', 'directory-listings')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'publicly_queryable' => true,
        'show_ui'           => true,
        'show_in_menu'      => false, // Don't show in main menu
        'capability_type'    => 'post',
        'hierarchical'      => false,
        'supports'          => array('title', 'editor', 'author', 'revisions'),
        'rewrite'           => array('slug' => 'faq'),
        'show_in_rest'      => true
    );

    register_post_type('faq', $args);

    // Register FAQ Topic taxonomy
    register_taxonomy('faq_topic', 'faq', array(
        'hierarchical'      => true,
        'labels'           => array(
            'name'              => __('FAQ Topics', 'directory-listings'),
            'singular_name'     => __('FAQ Topic', 'directory-listings'),
            'search_items'      => __('Search Topics', 'directory-listings'),
            'all_items'         => __('All Topics', 'directory-listings'),
            'parent_item'       => __('Parent Topic', 'directory-listings'),
            'parent_item_colon' => __('Parent Topic:', 'directory-listings'),
            'edit_item'         => __('Edit Topic', 'directory-listings'),
            'update_item'       => __('Update Topic', 'directory-listings'),
            'add_new_item'      => __('Add New Topic', 'directory-listings'),
            'new_item_name'     => __('New Topic Name', 'directory-listings'),
            'menu_name'         => __('Topics', 'directory-listings')
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'faq-topic'),
    ));
}
add_action('init', 'create_faq_post_type');

// Register FAQ section in Directory Listings menu
function add_directory_faq_menu_items() {
    add_submenu_page(
        'directory-listings-dashboard',
        __('FAQ Section', 'directory-listings'),
        __('FAQ Section', 'directory-listings'),
        'manage_options',
        'edit.php?post_type=faq',
        null
    );

    add_submenu_page(
        'directory-listings-dashboard',
        __('Add New FAQ', 'directory-listings'),
        __('Add New FAQ', 'directory-listings'),
        'manage_options',
        'post-new.php?post_type=faq',
        null
    );

    add_submenu_page(
        'directory-listings-dashboard',
        __('FAQ Topics', 'directory-listings'),
        __('FAQ Topics', 'directory-listings'),
        'manage_options',
        'edit-tags.php?taxonomy=faq_topic&post_type=faq',
        null
    );

    add_submenu_page(
        'directory-listings-dashboard',
        __('Import FAQs', 'directory-listings'),
        __('Import FAQs', 'directory-listings'),
        'manage_options',
        'admin.php?page=faq-import',
        'render_faq_import_page'
    );
}
add_action('admin_menu', 'add_directory_faq_menu_items', 20);

// Create sample CSV file
function create_faq_sample_file() {
    $sample_dir = DIRECTORY_LISTINGS_PLUGIN_PATH . 'templates/samples/';
    $sample_file = $sample_dir . 'faq-sample.csv';

    // Create samples directory if it doesn't exist
    if (!file_exists($sample_dir)) {
        wp_mkdir_p($sample_dir);
    }

    // Create sample CSV content
    $sample_content = "question,answer,topic,order\n";
    $sample_content .= "\"How do I list my business?\",\"To list your business, follow these steps: 1. Click Add Listing 2. Fill out the form 3. Submit for review\",\"General,Getting Started\",1\n";
    $sample_content .= "\"What are the listing types?\",\"We offer both free and paid listings. Paid listings include additional features like photo galleries and social media links.\",\"Pricing\",2\n";
    $sample_content .= "\"How long does approval take?\",\"Free listings are typically approved within 1-2 business days. Paid listings receive priority review.\",\"General\",3\n";

    // Write sample file
    file_put_contents($sample_file, $sample_content);
}

// Render FAQ import page
function render_faq_import_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // Ensure sample file exists
    if (!file_exists(DIRECTORY_LISTINGS_PLUGIN_PATH . 'templates/samples/faq-sample.csv')) {
        create_faq_sample_file();
    }

    $sample_file_url = DIRECTORY_LISTINGS_PLUGIN_URL . 'templates/samples/faq-sample.csv';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php
        // Show import results if any
        if (isset($_GET['imported']) || isset($_GET['updated']) || isset($_GET['skipped'])) {
            $message = 'Import completed. ';
            if (isset($_GET['imported'])) {
                $message .= sprintf(__('%d FAQs imported. ', 'directory-listings'), intval($_GET['imported']));
            }
            if (isset($_GET['updated'])) {
                $message .= sprintf(__('%d FAQs updated. ', 'directory-listings'), intval($_GET['updated']));
            }
            if (isset($_GET['skipped'])) {
                $message .= sprintf(__('%d FAQs skipped.', 'directory-listings'), intval($_GET['skipped']));
            }
            echo '<div class="notice notice-success"><p>' . esc_html($message) . '</p></div>';
        }
        ?>
        
        <div class="faq-import-instructions">
            <h3><?php _e('Import FAQs from CSV', 'directory-listings'); ?></h3>
            <p><?php _e('Upload a CSV file with the following columns:', 'directory-listings'); ?></p>
            <ul>
                <li><strong>question</strong> (<?php _e('required', 'directory-listings'); ?>)</li>
                <li><strong>answer</strong> (<?php _e('required', 'directory-listings'); ?>)</li>
                <li><strong>topic</strong> - <?php _e('Optional. Separate multiple topics with commas', 'directory-listings'); ?></li>
                <li><strong>order</strong> - <?php _e('Optional. Numeric value for display order', 'directory-listings'); ?></li>
            </ul>
            
            <p>
                <a href="<?php echo esc_url($sample_file_url); ?>" class="button button-secondary">
                    <?php _e('Download Sample CSV', 'directory-listings'); ?>
                </a>
            </p>
        </div>

        <form method="post" enctype="multipart/form-data" class="faq-import-form">
            <?php wp_nonce_field('faq_import_nonce', 'faq_import_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="csv_file"><?php _e('Choose CSV File', 'directory-listings'); ?></label>
                    </th>
                    <td>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                        <p class="description">
                            <?php _e('Maximum file size: ', 'directory-listings'); ?>
                            <?php echo esc_html(size_format(wp_max_upload_size())); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Import Options', 'directory-listings'); ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="update_existing" value="1">
                                <?php _e('Update existing FAQs if question matches', 'directory-listings'); ?>
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" name="skip_empty" value="1" checked>
                                <?php _e('Skip rows with empty required fields', 'directory-listings'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="submit_faq_import" class="button button-primary" 
                       value="<?php _e('Import FAQs', 'directory-listings'); ?>">
            </p>
        </form>
    </div>
    
    <style>
        .faq-import-instructions {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }
        .faq-import-instructions ul {
            list-style-type: disc;
            margin: 1em 0 1em 2em;
        }
        .faq-import-instructions li {
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .faq-import-instructions .button {
            margin-top: 15px;
        }
        .faq-import-form {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }
        .form-table td fieldset label {
            display: block;
            margin: 5px 0;
        }
    </style>
    <?php
}

// Hide FAQ from main WordPress menu
function hide_faq_menu() {
    remove_menu_page('edit.php?post_type=faq');
}
add_action('admin_menu', 'hide_faq_menu', 999);

// Create sample file on plugin activation
function activate_faq_importer() {
    create_faq_sample_file();
}
register_activation_hook(__FILE__, 'activate_faq_importer');

// Include FAQ importer functionality
require_once DIRECTORY_LISTINGS_PLUGIN_PATH . 'includes/faq-importer.php';




function create_test_city() {
    // Test data for Charleston
    $city_name = 'Miami';
    $state_name = 'Florida';

    // First, get the state
    $state = get_page_by_title($state_name, OBJECT, 'state');
    
    if (!$state) {
        // Create state if it doesn't exist
        $state_id = wp_insert_post(array(
            'post_title'    => $state_name,
            'post_type'     => 'state',
            'post_status'   => 'publish',
            'meta_input'    => array(
                'state_abbreviation' => 'SC'
            )
        ));
    } else {
        $state_id = $state->ID;
    }

    // Check if city already exists
    $existing_city = get_page_by_title($city_name, OBJECT, 'city');
    
    if (!$existing_city) {
        // Create the city
        $city_id = wp_insert_post(array(
            'post_title'    => $city_name,
            'post_type'     => 'city',
            'post_status'   => 'publish',
            'meta_input'    => array(
                'state_id'   => $state_id,
                'state_name' => $state_name
            )
        ));

        if ($city_id) {
            echo "Successfully created page with ID: " . $city_id;
        } else {
            echo "Failed to create city page";
        }
    } else {
        echo "Charleston city page already exists with ID: " . $existing_city->ID;
    }
}

// Add admin menu item for testing
function add_test_city_menu() {
    add_submenu_page(
        'directory-listings-dashboard',
        'Test City Creation',
        'Test City Creation',
        'manage_options',
        'test-city-creation',
        'render_test_city_page'
    );
}
add_action('admin_menu', 'add_test_city_menu');

// Render the test page
function render_test_city_page() {
    ?>
    <div class="wrap">
        <h1>Test City Creation</h1>
        
        <?php
        if (isset($_POST['create_test_city']) && check_admin_referer('create_test_city_nonce')) {
            create_test_city();
        }
        ?>

        <form method="post">
            <?php wp_nonce_field('create_test_city_nonce'); ?>
            <p>Click the button below to create a test city (miami, fl):</p>
            <input type="submit" name="create_test_city" class="button button-primary" 
                   value="Create Test City">
        </form>
    </div>
    <?php
}


// Add state template handling
function directory_listings_state_template($template) {
    if (is_tax('state')) {
        $template = plugin_dir_path(__FILE__) . 'templates/taxonomy-state.php';
        if (!file_exists($template)) {
            error_log('State template not found: ' . $template);
        }
    }
    return $template;
}
add_filter('template_include', 'directory_listings_state_template');

// Enqueue state-specific styles
function enqueue_state_specific_styles() {
    if (is_tax('state')) {
        wp_enqueue_style(
            'state-template-styles',
            plugins_url('assets/css/state.css', __FILE__),
            array(),
            '1.0.0'
        );
        
        // Add any state-specific inline styles
        $custom_css = "
            .state-header {
                background-color: #f8f9fa;
                padding: 40px 0;
                margin-bottom: 30px;
            }
            .state-content {
                padding: 20px;
            }
            .state-sidebar {
                background: #fff;
                padding: 20px;
                border: 1px solid #e5e5e5;
            }
            .state-listings {
                margin-top: 30px;
            }
        ";
        wp_add_inline_style('state-template-styles', $custom_css);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_state_specific_styles');

// Add state template specific body classes
function add_state_body_classes($classes) {
    if (is_tax('state')) {
        $classes[] = 'state-template';
        $classes[] = 'has-sidebar';
    }
    return $classes;
}
add_filter('body_class', 'add_state_body_classes');


/**
 * Get similar businesses based on category and location
 * 
 * @param int $current_post_id The current listing ID
 * @param int $limit Number of similar businesses to return
 * @return array Array of similar businesses
 */
function get_similar_businesses($current_post_id, $limit = 3) {
    // Get current business categories and location
    $current_categories = wp_get_post_terms($current_post_id, 'business_category', array('fields' => 'ids'));
    $current_state = wp_get_post_terms($current_post_id, 'state', array('fields' => 'ids'));
    $current_city = get_field('city', $current_post_id);

    // Setup query arguments
    $args = array(
        'post_type' => 'directory_listing',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'post__not_in' => array($current_post_id), // Exclude current listing
        'orderby' => array(
            'meta_value_num' => 'DESC', // Sort by rating
            'rand' => 'ASC' // Then randomize
        ),
        'meta_key' => 'overall_rating'
    );

    // Add category criteria
    if (!empty($current_categories) && !is_wp_error($current_categories)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'business_category',
            'field' => 'term_id',
            'terms' => $current_categories,
            'operator' => 'IN'
        );
    }

    // Add location criteria
    if (!empty($current_state) && !is_wp_error($current_state)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'state',
            'field' => 'term_id',
            'terms' => $current_state,
            'operator' => 'IN'
        );
    }

    // Add city criteria if available
    if (!empty($current_city)) {
        $args['meta_query'][] = array(
            'key' => 'city',
            'value' => $current_city,
            'compare' => '='
        );
    }

    // If we have both category and location criteria, set the relation
    if (!empty($args['tax_query']) && count($args['tax_query']) > 1) {
        $args['tax_query']['relation'] = 'AND';
    }

    // Get similar listings
    $similar_query = new WP_Query($args);
    $similar_businesses = array();

    if ($similar_query->have_posts()) {
        while ($similar_query->have_posts()) {
            $similar_query->the_post();
            $post_id = get_the_ID();

            // Get business data
            $business_data = array(
                'id' => $post_id,
                'name' => get_the_title(),
                'logo' => get_field('logo', $post_id),
                'rating' => get_field('overall_rating', $post_id) ?: 0,
                'review_count' => get_field('review_count', $post_id) ?: 0,
                'url' => get_permalink(),
                'is_verified' => get_field('is_verified', $post_id) ?: false,
                'city' => get_field('city', $post_id),
                'state' => get_field('state', $post_id)
            );

            // Get primary category
            $categories = wp_get_post_terms($post_id, 'business_category');
            if (!empty($categories) && !is_wp_error($categories)) {
                $business_data['category'] = $categories[0]->name;
            }

            $similar_businesses[] = $business_data;
        }
        wp_reset_postdata();
    }

    // If we don't have enough similar businesses, try without city restriction
    if (count($similar_businesses) < $limit && !empty($current_city)) {
        unset($args['meta_query']);
        $more_businesses = new WP_Query($args);
        
        if ($more_businesses->have_posts()) {
            while ($more_businesses->have_posts() && count($similar_businesses) < $limit) {
                $more_businesses->the_post();
                $post_id = get_the_ID();
                
                // Check if this business is already in our list
                $exists = false;
                foreach ($similar_businesses as $business) {
                    if ($business['id'] === $post_id) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    // Get business data
                    $business_data = array(
                        'id' => $post_id,
                        'name' => get_the_title(),
                        'logo' => get_field('logo', $post_id),
                        'rating' => get_field('overall_rating', $post_id) ?: 0,
                        'review_count' => get_field('review_count', $post_id) ?: 0,
                        'url' => get_permalink(),
                        'is_verified' => get_field('is_verified', $post_id) ?: false,
                        'city' => get_field('city', $post_id),
                        'state' => get_field('state', $post_id)
                    );

                    // Get primary category
                    $categories = wp_get_post_terms($post_id, 'business_category');
                    if (!empty($categories) && !is_wp_error($categories)) {
                        $business_data['category'] = $categories[0]->name;
                    }

                    $similar_businesses[] = $business_data;
                }
            }
            wp_reset_postdata();
        }
    }

    return $similar_businesses;
}

/**
 * Helper function to format rating display
 * 
 * @param float $rating Rating value
 * @return string Formatted rating stars
 */
function format_rating_stars($rating) {
    $stars = '';
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;

    // Add full stars
    for ($i = 0; $i < $full_stars; $i++) {
        $stars .= '★';
    }

    // Add half star if needed
    if ($half_star) {
        $stars .= '½';
    }

    // Add empty stars to make 5 total
    $empty_stars = 5 - ceil($rating);
    for ($i = 0; $i < $empty_stars; $i++) {
        $stars .= '☆';
    }

    return $stars;
}

/**
 * Get rating distribution for a business
 * 
 * @param int $post_id Business listing ID
 * @return array Rating distribution array
 */
function get_rating_distribution($post_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';

    $distributions = $wpdb->get_results($wpdb->prepare(
        "SELECT rating, COUNT(*) as count 
         FROM $table_name 
         WHERE listing_id = %d 
         AND status = 'approved'
         GROUP BY rating",
        $post_id
    ));

    $total_reviews = array_sum(array_column($distributions, 'count'));
    $formatted = array();

    for ($i = 5; $i >= 1; $i--) {
        $count = 0;
        foreach ($distributions as $dist) {
            if ($dist->rating == $i) {
                $count = $dist->count;
                break;
            }
        }
        $percentage = $total_reviews > 0 ? round(($count / $total_reviews) * 100) : 0;
        $formatted[$i] = $percentage;
    }

    return $formatted;
}


// Define leads management path constant if not already defined
if (!defined('DIRECTORY_LEADS_PATH')) {
    define('DIRECTORY_LEADS_PATH', plugin_dir_path(__FILE__) . 'includes/leads/');
}

// Function to initialize leads functionality
function directory_listings_init_leads() {
    // Check if files exist before requiring them
    $required_files = array(
        'class-leads-table.php',
        'class-leads-manager.php',
        'form-handler.php',
        'admin-page.php'
    );

    $all_files_exist = true;

    foreach ($required_files as $file) {
        if (!file_exists(DIRECTORY_LEADS_PATH . $file)) {
            error_log('Directory Listings: Missing required lead management file - ' . $file);
            $all_files_exist = false;
        }
    }

    if ($all_files_exist) {
        // Include required files
        require_once DIRECTORY_LEADS_PATH . 'class-leads-table.php';
        require_once DIRECTORY_LEADS_PATH . 'class-leads-manager.php';
        require_once DIRECTORY_LEADS_PATH . 'form-handler.php';
        require_once DIRECTORY_LEADS_PATH . 'admin-page.php';

        // Create the leads table if it doesn't exist
        if (class_exists('Directory_Listings_Leads_Table')) {
            Directory_Listings_Leads_Table::create_table();
        }

        // Initialize leads management
        if (class_exists('Directory_Listings_Leads_Manager')) {
            Directory_Listings_Leads_Manager::init();
        }
    }
}

// Initialize leads functionality after plugins are loaded
add_action('plugins_loaded', 'directory_listings_init_leads');

// Register activation hook for leads table
register_activation_hook(__FILE__, function() {
    if (class_exists('Directory_Listings_Leads_Table')) {
        Directory_Listings_Leads_Table::create_table();
    }
});

// Add leads menu item to admin menu
if (!function_exists('add_leads_management_menu')) {
    function add_leads_management_menu() {
        add_submenu_page(
            'directory-listings-dashboard', // Parent slug
            'Manage Leads',                // Page title
            'Leads',                       // Menu title
            'manage_options',              // Capability
            'manage-leads',                // Menu slug
            'render_leads_management_page' // Callback function
        );
    }
    add_action('admin_menu', 'add_leads_management_menu', 20);
}

// Add leads CSS to admin
function enqueue_leads_admin_styles() {
    $screen = get_current_screen();
    if ($screen && strpos($screen->id, 'manage-leads') !== false) {
        wp_enqueue_style(
            'directory-leads-admin',
            plugins_url('includes/leads/assets/css/admin-leads.css', __FILE__),
            array(),
            DIRECTORY_LISTINGS_VERSION
        );
    }
}
add_action('admin_enqueue_scripts', 'enqueue_leads_admin_styles');

// Add AJAX handlers for lead management
function register_leads_ajax_handlers() {
    add_action('wp_ajax_update_lead_status', array('Directory_Listings_Leads_Manager', 'update_lead_status'));
    add_action('wp_ajax_handle_quick_contact', array('Directory_Listings_Leads_Manager', 'handle_form_submission'));
    add_action('wp_ajax_nopriv_handle_quick_contact', array('Directory_Listings_Leads_Manager', 'handle_form_submission'));
}
add_action('init', 'register_leads_ajax_handlers');

//Claim section




// Register claim listing assets
function register_claim_listing_assets() {
    wp_register_style(
        'claim-listing-styles',
        plugins_url('assets/css/claim-listing.css', __FILE__),
        array(),
        '1.0.0'
    );

    wp_register_script(
        'claim-listing-scripts',
        plugins_url('assets/js/claim-listing.js', __FILE__),
        array('jquery'),
        '1.0.0',
        true
    );

    wp_localize_script('claim-listing-scripts', 'directory_listings', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('directory_listings_claim'),
        'redirect_url' => home_url('/dashboard/'),
        'messages' => array(
            'confirm_submit' => __('Are you sure you want to submit this claim request?', 'directory-listings'),
            'uploading' => __('Uploading files...', 'directory-listings'),
            'success' => __('Claim request submitted successfully!', 'directory-listings'),
            'error' => __('Error submitting claim request. Please try again.', 'directory-listings')
        )
    ));
}
add_action('wp_enqueue_scripts', 'register_claim_listing_assets');

// Enqueue assets on claim listing page
function enqueue_claim_listing_assets() {
    if (is_page_template('claim-listing-template.php') || 
        (isset($_GET['claim']) && isset($_GET['token']))) {
        wp_enqueue_style('claim-listing-styles');
        wp_enqueue_script('claim-listing-scripts');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_claim_listing_assets');

// Register claim listing endpoint
function register_claim_listing_endpoint() {
    add_rewrite_endpoint('claim-listing', EP_PAGES);
    flush_rewrite_rules();
}
add_action('init', 'register_claim_listing_endpoint');

// Add claim listing query vars
function add_claim_listing_query_vars($vars) {
    $vars[] = 'claim';
    $vars[] = 'token';
    return $vars;
}
add_filter('query_vars', 'add_claim_listing_query_vars');

// Handle claim listing template redirect
function handle_claim_listing_template() {
    if (get_query_var('claim') && get_query_var('token')) {
        include(plugin_dir_path(__FILE__) . 'templates/claim-listing-template.php');
        exit;
    }
}
add_action('template_redirect', 'handle_claim_listing_template');

// Generate claim URL
function generate_claim_url($listing_id) {
    $token = wp_generate_password(32, false);
    update_post_meta($listing_id, '_claim_token', $token);
    
    return add_query_arg(array(
        'claim' => $listing_id,
        'token' => $token
    ), home_url('/claim-listing/'));
}

// Add claim button to listing
function add_claim_listing_button($content) {
    if (is_singular('directory_listing')) {
        global $post;
        
        // Check if listing is already claimed
        $claimed = get_post_meta($post->ID, '_claimed', true);
        if ($claimed) {
            return $content;
        }
        
        // Generate claim URL
        $claim_url = generate_claim_url($post->ID);
        
        // Add claim button before content
        $button = '<div class="claim-listing-button-container">';
        $button .= '<a href="' . esc_url($claim_url) . '" class="claim-listing-button">';
        $button .= '<i class="fas fa-check-circle"></i> ' . __('Claim This Listing', 'directory-listings');
        $button .= '</a>';
        $button .= '<p class="claim-listing-note">' . __('Is this your business? Claim it now to manage your listing.', 'directory-listings') . '</p>';
        $button .= '</div>';
        
        return $button . $content;
    }
    
    return $content;
}
//add_filter('the_content', 'add_claim_listing_button');

// Add claimed badge to listings
function add_claimed_badge($title, $id = null) {
    if (!$id || !is_singular('directory_listing')) {
        return $title;
    }
    
    $claimed = get_post_meta($id, '_claimed', true);
    if ($claimed) {
        $title .= ' <span class="claimed-badge"><i class="fas fa-check-circle"></i> ' . __('Verified', 'directory-listings') . '</span>';
    }
    
    return $title;
}
add_filter('the_title', 'add_claimed_badge', 10, 2);

// Add claim status column to admin
function add_claim_status_column($columns) {
    $date_column = $columns['date'];
    unset($columns['date']);
    
    $columns['claim_status'] = __('Claim Status', 'directory-listings');
    $columns['date'] = $date_column;
    
    return $columns;
}
add_filter('manage_directory_listing_posts_columns', 'add_claim_status_column');

// Populate claim status column
function populate_claim_status_column($column, $post_id) {
    if ($column === 'claim_status') {
        $claimed = get_post_meta($post_id, '_claimed', true);
        $status = get_post_meta($post_id, '_claim_status', true);
        
        if ($claimed) {
            echo '<span class="status-badge status-claimed">' . __('Claimed', 'directory-listings') . '</span>';
        } elseif ($status === 'pending') {
            echo '<span class="status-badge status-pending">' . __('Claim Pending', 'directory-listings') . '</span>';
        } else {
            echo '<span class="status-badge status-unclaimed">' . __('Unclaimed', 'directory-listings') . '</span>';
        }
    }
}
add_action('manage_directory_listing_posts_custom_column', 'populate_claim_status_column', 10, 2);

// Add claim status to listing filters
function add_claim_status_filter() {
    global $typenow;
    
    if ($typenow === 'directory_listing') {
        $current_status = isset($_GET['claim_status']) ? $_GET['claim_status'] : '';
        ?>
        <select name="claim_status" id="claim-status-filter">
            <option value=""><?php _e('All Claim Statuses', 'directory-listings'); ?></option>
            <option value="claimed" <?php selected($current_status, 'claimed'); ?>>
                <?php _e('Claimed', 'directory-listings'); ?>
            </option>
            <option value="pending" <?php selected($current_status, 'pending'); ?>>
                <?php _e('Claim Pending', 'directory-listings'); ?>
            </option>
            <option value="unclaimed" <?php selected($current_status, 'unclaimed'); ?>>
                <?php _e('Unclaimed', 'directory-listings'); ?>
            </option>
        </select>
        <?php
    }
}
add_action('restrict_manage_posts', 'add_claim_status_filter');

// Filter listings by claim status
function filter_listings_by_claim_status($query) {
    global $pagenow, $typenow;
    
    if (is_admin() && $pagenow === 'edit.php' && 
        $typenow === 'directory_listing' && 
        isset($_GET['claim_status'])) {
        
        $status = $_GET['claim_status'];
        $meta_query = array();
        
        switch ($status) {
            case 'claimed':
                $meta_query[] = array(
                    'key' => '_claimed',
                    'value' => '1'
                );
                break;
                
            case 'pending':
                $meta_query[] = array(
                    'key' => '_claim_status',
                    'value' => 'pending'
                );
                break;
                
            case 'unclaimed':
                $meta_query[] = array(
                    'key' => '_claimed',
                    'compare' => 'NOT EXISTS'
                );
                break;
        }
        
        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'filter_listings_by_claim_status');


// AJAX handler for filtering listings
function handle_filter_listings() {
    // Verify nonce
    check_ajax_referer('directory_filter_nonce', 'nonce');
    
    // Get and sanitize search parameters
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';
    $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // Build query arguments
    $args = array(
        'post_type' => 'directory_listing',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'AND'
        ),
        'tax_query' => array()
    );
    
    // Add category filter
    if ($category_id > 0) {
        $args['tax_query'][] = array(
            'taxonomy' => 'business_category',
            'field' => 'term_id',
            'terms' => $category_id
        );
    }
    
    // Add state filter
    if (!empty($state)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'state',
            'field' => 'slug',
            'terms' => $state
        );
    }
    
    // Add city filter
    if (!empty($city)) {
        $args['meta_query'][] = array(
            'key' => 'city',
            'value' => $city,
            'compare' => '='
        );
    }
    
    // Add search query
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    // Add sorting
    if (!empty($sort)) {
        switch ($sort) {
            case 'rating':
                $args['meta_key'] = 'overall_rating';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'reviews':
                $args['meta_key'] = 'review_count';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'newest':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
        }
    } else {
        // Default sorting - prioritize featured and verified listings
        $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key' => 'is_verified',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'key' => 'top_rated',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'key' => 'listing_type',
                'compare' => 'EXISTS'
            )
        );
        $args['orderby'] = array(
            'meta_value' => 'DESC',
            'date' => 'DESC'
        );
    }
    
    // Run the query
    $listings = new WP_Query($args);
    
    // Output the results
    ob_start();
    
    if ($listings->have_posts()) {
        echo '<div class="listings-grid">';
        while ($listings->have_posts()) {
            $listings->the_post();
            
            // Get listing data
            $listing_type = get_field('listing_type');
            $is_featured = get_field('top_rated');
            $is_verified = get_field('is_verified');
            $rating = get_field('overall_rating');
            $review_count = get_field('review_count');
            $phone = get_field('phone_number');
            $city = get_field('city');
            $state = get_field('state');
            $upvotes = get_post_meta(get_the_ID(), 'upvotes', true) ?: 0;
            $downvotes = get_post_meta(get_the_ID(), 'downvotes', true) ?: 0;
            $vote_score = $upvotes - $downvotes;
            
            // Include the listing card template
            include(plugin_dir_path(__FILE__) . 'templates/parts/listing-card.php');
        }
        echo '</div>';
        
        wp_reset_postdata();
    } else {
        echo '<p class="no-results">No listings found matching your criteria.</p>';
    }
    
    $html = ob_get_clean();
    
    // Send response
    wp_send_json_success(array(
        'html' => $html,
        'found' => $listings->found_posts,
        'max_pages' => $listings->max_num_pages
    ));
}
add_action('wp_ajax_filter_listings', 'handle_filter_listings');
add_action('wp_ajax_nopriv_filter_listings', 'handle_filter_listings');

function register_search_results_template($page_templates, $theme, $post) {
    $page_templates['search-results.php'] = 'Directory Search Results';
    return $page_templates;
}
add_filter('theme_page_templates', 'register_search_results_template', 10, 3);

function load_search_results_template($template) {
    if (is_page_template('search-results.php')) {
        $template = plugin_dir_path(__FILE__) . 'templates/search-results.php';
    }
    return $template;
}
add_filter('template_include', 'load_search_results_template');


// Filter to restrict search to directory listings only
function restrict_search_to_directory_listings($query) {
    // Only apply to frontend search queries
    if (!is_admin() && $query->is_search() && $query->is_main_query()) {
        // Set post type to directory_listing only
        $query->set('post_type', 'directory_listing');
    }
    return $query;
}
add_filter('pre_get_posts', 'restrict_search_to_directory_listings');



