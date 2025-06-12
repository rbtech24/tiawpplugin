<?php
function get_directory_cities() {
    global $wpdb;
    return $wpdb->get_col($wpdb->prepare("
        SELECT DISTINCT pm.meta_value 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = %s 
        AND pm.meta_value != ''
        AND p.post_type = %s
        AND p.post_status = 'publish'
        ORDER BY pm.meta_value ASC
    ", 'city', 'directory_listing'));
}

function render_city_filter($current_city = '') {
    $cities = get_directory_cities();
    if (empty($cities)) return;

    $html = '<div class="directory-city-filter">';
    $html .= '<form method="get" class="city-filter-form">';
    
    // Preserve existing query parameters
    foreach ($_GET as $key => $value) {
        if ($key !== 'city' && $key !== 'submit') {
            $html .= sprintf(
                '<input type="hidden" name="%s" value="%s">',
                esc_attr($key),
                esc_attr($value)
            );
        }
    }
    
    $html .= '<select name="city" onchange="this.form.submit()">';
    $html .= '<option value="">' . __('Filter by City', 'directory-listings') . '</option>';
    
    foreach ($cities as $city) {
        $html .= sprintf(
            '<option value="%s" %s>%s</option>',
            esc_attr($city),
            selected($city, $current_city, false),
            esc_html($city)
        );
    }
    
    $html .= '</select>';
    $html .= '</form>';
    $html .= '</div>';
    
    return $html;
}

function filter_listings_by_city($query) {
    if (!is_admin() && 
        $query->is_main_query() && 
        (is_post_type_archive('directory_listing') || is_tax('business_category') || is_tax('state')) && 
        isset($_GET['city']) && 
        $_GET['city'] !== '') {
        
        $query->set('meta_key', 'city');
        $query->set('meta_value', sanitize_text_field($_GET['city']));
    }
}
add_action('pre_get_posts', 'filter_listings_by_city');