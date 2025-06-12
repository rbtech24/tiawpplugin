// includes/search-filter-functions.php

<?php
if (!defined('ABSPATH')) {
    exit;
}

class Directory_Search_Filter {
    private static $instance = null;
    private $plugin_url;
    private $plugin_path;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->plugin_url = plugin_dir_url(dirname(__FILE__));
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));
        
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_get_cities_by_state', array($this, 'get_cities_by_state'));
        add_action('wp_ajax_nopriv_get_cities_by_state', array($this, 'get_cities_by_state'));
        add_action('wp_ajax_live_search_listings', array($this, 'live_search_listings'));
        add_action('wp_ajax_nopriv_live_search_listings', array($this, 'live_search_listings'));
        add_action('pre_get_posts', array($this, 'modify_search_query'));
        add_action('wp_footer', array($this, 'render_search_modals'));
    }

    public function enqueue_scripts() {
        // Only enqueue on relevant pages
        if (!is_post_type_archive('directory_listing') && !is_tax('business_category')) {
            return;
        }

        wp_enqueue_style(
            'directory-search-filters',
            $this->plugin_url . 'assets/css/search-filters.css',
            array(),
            DIRECTORY_LISTINGS_VERSION
        );

        wp_enqueue_script(
            'directory-search-filters',
            $this->plugin_url . 'assets/js/search-filters.js',
            array('jquery'),
            DIRECTORY_LISTINGS_VERSION,
            true
        );

        wp_localize_script('directory-search-filters', 'directorySearchParams', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('directory_search_nonce'),
            'mapApiKey' => get_option('directory_google_maps_api_key'),
            'noResults' => __('No results found', 'directory-listings'),
            'searchError' => __('Error performing search', 'directory-listings'),
            'loadingText' => __('Loading...', 'directory-listings')
        ));
    }

    public function get_cities_by_state() {
        check_ajax_referer('directory_search_nonce', 'nonce');
        
        $state = sanitize_text_field($_POST['state']);
        $cities = $this->get_state_cities($state);
        
        if (is_wp_error($cities)) {
            wp_send_json_error($cities->get_error_message());
        }
        
        wp_send_json_success(array(
            'cities' => $cities,
            'count' => count($cities)
        ));
    }

    public function live_search_listings() {
        check_ajax_referer('directory_search_nonce', 'nonce');

        $search_term = sanitize_text_field($_POST['term']);
        $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';

        $args = array(
            'post_type' => 'directory_listing',
            'posts_per_page' => 5,
            'post_status' => 'publish',
            's' => $search_term,
        );

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

        $query = new WP_Query($args);
        $results = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'rating' => get_field('overall_rating'),
                    'city' => get_field('city'),
                    'state' => get_field('state')
                );
            }
        }

        wp_reset_postdata();
        wp_send_json_success($results);
    }

    private function get_state_cities($state_slug) {
        global $wpdb;
        
        $cities = $wpdb->get_col($wpdb->prepare("
            SELECT DISTINCT pm.meta_value
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
            INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
            INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
            WHERE pm.meta_key = 'city'
            AND p.post_type = 'directory_listing'
            AND p.post_status = 'publish'
            AND t.slug = %s
            AND tt.taxonomy = 'state'
            ORDER BY pm.meta_value ASC
        ", $state_slug));

        if ($wpdb->last_error) {
            return new WP_Error('db_error', $wpdb->last_error);
        }

        return $cities;
    }

    public function modify_search_query($query) {
        if (!is_admin() && $query->is_main_query() && 
            (is_post_type_archive('directory_listing') || is_tax('business_category'))) {
            
            $this->add_location_filters($query);
            $this->add_rating_filters($query);
            $this->add_advanced_filters($query);
            $this->add_sorting_options($query);
        }
    }

    private function add_location_filters($query) {
        // State Filter
        if (!empty($_GET['state'])) {
            $tax_query = $query->get('tax_query', array());
            $tax_query[] = array(
                'taxonomy' => 'state',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['state'])
            );
            $query->set('tax_query', $tax_query);
        }

        // City Filter
        if (!empty($_GET['city'])) {
            $meta_query = $query->get('meta_query', array());
            $meta_query[] = array(
                'key' => 'city',
                'value' => sanitize_text_field($_GET['city'])
            );
            $query->set('meta_query', $meta_query);
        }
    }

    private function add_rating_filters($query) {
        if (!empty($_GET['rating'])) {
            $meta_query = $query->get('meta_query', array());
            $meta_query[] = array(
                'key' => 'overall_rating',
                'value' => floatval($_GET['rating']),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
            $query->set('meta_query', $meta_query);
        }
    }

    private function add_advanced_filters($query) {
        $meta_query = $query->get('meta_query', array());

        // Verified Filter
        if (!empty($_GET['verified'])) {
            $meta_query[] = array(
                'key' => 'is_verified',
                'value' => '1'
            );
        }

        // Open Now Filter
        if (!empty($_GET['open_now'])) {
            $current_day = strtolower(date('l'));
            $current_time = date('H:i');
            
            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key' => "business_hours_{$current_day}_open",
                    'value' => $current_time,
                    'compare' => '<=',
                    'type' => 'TIME'
                ),
                array(
                    'key' => "business_hours_{$current_day}_close",
                    'value' => $current_time,
                    'compare' => '>=',
                    'type' => 'TIME'
                )
            );
        }

        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }

    private function add_sorting_options($query) {
        if (!empty($_GET['sort'])) {
            switch ($_GET['sort']) {
                case 'rating_high':
                    $query->set('meta_key', 'overall_rating');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                
                case 'rating_low':
                    $query->set('meta_key', 'overall_rating');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'ASC');
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
    }

    public function render_search_modals() {
        if (!is_post_type_archive('directory_listing') && !is_tax('business_category')) {
            return;
        }
        
        include $this->plugin_path . 'templates/search/modals/filters-modal.php';
        include $this->plugin_path . 'templates/search/modals/map-modal.php';
    }

    public function get_active_filters() {
        $filters = array();
        
        if (!empty($_GET['state'])) {
            $filters['state'] = array(
                'label' => __('State', 'directory-listings'),
                'value' => sanitize_text_field($_GET['state'])
            );
        }
        
        if (!empty($_GET['city'])) {
            $filters['city'] = array(
                'label' => __('City', 'directory-listings'),
                'value' => sanitize_text_field($_GET['city'])
            );
        }
        
        if (!empty($_GET['rating'])) {
            $filters['rating'] = array(
                'label' => __('Rating', 'directory-listings'),
                'value' => floatval($_GET['rating']) . '+ Stars'
            );
        }
        
        if (!empty($_GET['verified'])) {
            $filters['verified'] = array(
                'label' => __('Verified Only', 'directory-listings'),
                'value' => __('Yes', 'directory-listings')
            );
        }
        
        return $filters;
    }

    public function get_template_part($template) {
        if (file_exists($this->plugin_path . 'templates/' . $template . '.php')) {
            include $this->plugin_path . 'templates/' . $template . '.php';
        }
    }
}

// Initialize the class
Directory_Search_Filter::get_instance();