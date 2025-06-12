<?php
class Dynamic_Grid_Listings_Widget extends \Elementor\Widget_Base {
    public function get_name() { 
        return 'dynamic_grid_listings_widget'; 
    }
    
    public function get_title() { 
        return __('Dynamic Grid Listing List', 'directory-listings'); 
    }
    
    public function get_icon() { 
        return 'eicon-gallery-grid'; 
    }
    
    public function get_categories() { 
        return ['directory-listings']; 
    }

    protected function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Content Settings', 'directory-listings'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        // Category Control
        $categories = get_terms([
            'taxonomy' => 'business_category',
            'hide_empty' => false,
            'fields' => 'id=>name',
        ]);

        $category_options = ['all' => 'All Categories'];
        if (!is_wp_error($categories)) {
            $category_options += $categories;
        }

        $this->add_control('business_category', [
            'label' => __('Business Category', 'directory-listings'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $category_options,
            'default' => 'all',
        ]);

        // State Control
        $states = get_terms([
            'taxonomy' => 'state',
            'hide_empty' => false,
            'parent' => 0,
            'fields' => 'id=>name',
        ]);

        $state_options = ['all' => 'All States'];
        if (!is_wp_error($states)) {
            $state_options += $states;
        }

        $this->add_control('state', [
            'label' => __('State', 'directory-listings'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $state_options,
            'default' => 'all',
        ]);

        // City Control
        $this->add_control('city', [
            'label' => __('City', 'directory-listings'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => __('Enter city name', 'directory-listings'),
            'description' => __('Leave empty for all cities', 'directory-listings'),
        ]);

        $this->add_control('number_of_listings', [
            'label' => __('Number of Listings', 'directory-listings'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 20,
            'step' => 1,
            'default' => 3,
        ]);

        $this->add_control('title', [
            'label' => __('Section Title', 'directory-listings'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => __('Top {listings} {category} Businesses in {city}, {state}', 'directory-listings'),
            'description' => __('Use {listings}, {category}, {city}, and {state} as placeholders', 'directory-listings'),
        ]);

        $this->add_control('description', [
            'label' => __('Section Description', 'directory-listings'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => __('Discover the highest-rated businesses in {city}, {state}', 'directory-listings'),
            'description' => __('Use {listings}, {category}, {city}, and {state} as placeholders', 'directory-listings'),
        ]);

        $this->end_controls_section();
    }

    private function get_top_rated_listings($settings) {
        $args = array(
            'post_type' => 'directory_listing',
            'posts_per_page' => absint($settings['number_of_listings']),
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'top_rated',
                    'compare' => 'EXISTS'
                ),
                array(
                    'key' => 'listing_type',
                    'value' => 'paid',
                    'compare' => '='
                )
            ),
            'tax_query' => array('relation' => 'AND'),
            'orderby' => array(
                'meta_value_num' => 'DESC',
                'date' => 'DESC'
            ),
            'meta_key' => 'overall_rating'
        );

        if ($settings['business_category'] !== 'all') {
            $args['tax_query'][] = array(
                'taxonomy' => 'business_category',
                'field' => 'term_id',
                'terms' => $settings['business_category'],
            );
        }

        if ($settings['state'] !== 'all') {
            $args['tax_query'][] = array(
                'taxonomy' => 'state',
                'field' => 'term_id',
                'terms' => $settings['state'],
            );
        }

        if (!empty($settings['city'])) {
            $args['meta_query'][] = array(
                'key' => 'city',
                'value' => $settings['city'],
                'compare' => 'LIKE'
            );
        }

        return get_posts($args);
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $listings = $this->get_top_rated_listings($settings);
        
        // Process placeholders
        $category_name = ($settings['business_category'] !== 'all') 
            ? get_term($settings['business_category'], 'business_category')->name 
            : '';
        
        $state_name = ($settings['state'] !== 'all')
            ? get_term($settings['state'], 'state')->name
            : '';

        $city_name = $settings['city'];

        $title = str_replace(
            ['{listings}', '{category}', '{city}', '{state}'],
            [$settings['number_of_listings'], $category_name, $city_name, $state_name],
            $settings['title']
        );

        $description = str_replace(
            ['{listings}', '{category}', '{city}', '{state}'],
            [$settings['number_of_listings'], $category_name, $city_name, $state_name],
            $settings['description']
        );

        // Clean up placeholder text
        $title = preg_replace('/\s+in\s+,\s*/', ' in ', $title);
        $title = preg_replace('/\s+,\s*,\s*/', ', ', $title);
        $title = preg_replace('/\s*,\s*$/', '', $title);
        $title = preg_replace('/\s+,\s*\./', '.', $title);

        $description = preg_replace('/\s+in\s+,\s*/', ' in ', $description);
        $description = preg_replace('/\s+,\s*,\s*/', ', ', $description);
        $description = preg_replace('/\s*,\s*$/', '', $description);
        $description = preg_replace('/\s+,\s*\./', '.', $description);

        if (!empty($listings)) : ?>
            <style>
                <style>
.elementor-widget-dynamic_grid_listings_widget .listings-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.elementor-widget-dynamic_grid_listings_widget .section-title {
    font-size: 32px;
    color: #26387E;
    margin-bottom: 10px;
}

.elementor-widget-dynamic_grid_listings_widget .section-description {
    color: #666;
    font-size: 16px;
    margin-bottom: 30px;
}

.elementor-widget-dynamic_grid_listings_widget .listing-card {
    border: 2px solid #FFD700;
    border-radius: 8px;
    display: flex;
    overflow: hidden;
    background: #fff;
}

.elementor-widget-dynamic_grid_listings_widget .left-side {
    width: 50%;
    background: #f8f9fa;
    position: relative;
    padding: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.elementor-widget-dynamic_grid_listings_widget .top-badge {
    position: absolute;
    left: 15px;
    top: 15px;
    color: #26387E;
    font-weight: bold;
    font-size: 16px;
}

.elementor-widget-dynamic_grid_listings_widget .company-logo {
    max-width: 80%;
    height: auto;
    display: flex;
    justify-content: center;
    align-items: center;
}

.elementor-widget-dynamic_grid_listings_widget .company-logo img {
    max-width: 100%;
    height: auto;
    object-fit: contain;
}

.elementor-widget-dynamic_grid_listings_widget .right-side {
    width: 50%;
    padding: 30px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.elementor-widget-dynamic_grid_listings_widget .listing-title {
    font-size: 24px;
    color: #26387E;
    font-weight: bold;
    margin-bottom: 10px;
}

.elementor-widget-dynamic_grid_listings_widget .verified-badge {
    display: inline-flex;
    background: #4CAF50;
    color: white;
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 13px;
    margin-bottom: 10px;
}

.elementor-widget-dynamic_grid_listings_widget .rating {
    color: #FFD700;
    font-size: 16px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.elementor-widget-dynamic_grid_listings_widget .location {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #4B5563;
    font-size: 14px;
    margin-bottom: 15px;
}

.elementor-widget-dynamic_grid_listings_widget .card-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
}

.elementor-widget-dynamic_grid_listings_widget .btn {
    padding: 8px 16px;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    flex: 1;
    font-size: 14px;
}

.elementor-widget-dynamic_grid_listings_widget .btn-primary {
    background: #CD1339;
    color: white;
}

.elementor-widget-dynamic_grid_listings_widget .btn-outline {
    border: 1px solid #26387E;
    color: #26387E;
    background: white;
}

@media (max-width: 767px) {
    .elementor-widget-dynamic_grid_listings_widget .listing-card {
        flex-direction: column;
    }

    .elementor-widget-dynamic_grid_listings_widget .left-side,
    .elementor-widget-dynamic_grid_listings_widget .right-side {
        width: 100%;
    }

    .elementor-widget-dynamic_grid_listings_widget .right-side {
        align-items: center;
        text-align: center;
    }

    .elementor-widget-dynamic_grid_listings_widget .card-actions {
        justify-content: center;
    }
}
</style>

            <div class="listings-section">
                <div class="section-header">
                    <h2 class="section-title"><?php echo esc_html($title); ?></h2>
                    <p class="section-description"><?php echo esc_html($description); ?></p>
                </div>

                <div class="listings-grid">
                    <?php 
                    $rank = 1;
                    foreach ($listings as $listing) :
                        $rating = get_field('overall_rating', $listing->ID);
                        $rating = (!empty($rating) && is_numeric($rating)) ? floatval($rating) : 0;
                        $rounded_rating = floor($rating);

                        $logo = get_field('logo', $listing->ID);
                        $phone = get_field('phone_number', $listing->ID);
                        $is_verified = get_field('is_verified', $listing->ID);
                        $city = get_field('city', $listing->ID);
                        $state = get_field('state', $listing->ID);
                    ?>
                        <div class="listing-card">
                            <?php if ($rank <= 3) : ?>
                                <div class="top-badge">
                                    Top <?php echo $rank; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="logo-container">
                                <div class="company-logo">
                                    <?php if ($logo && isset($logo['url'])) : ?>
                                        <img src="<?php echo esc_url($logo['url']); ?>" 
                                             alt="<?php echo esc_attr($listing->post_title); ?>"
                                             loading="lazy">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="listing-content">
                                <h3 class="listing-title"><?php echo esc_html($listing->post_title); ?></h3>
                                
                               <?php if ($is_verified) : ?>
                                    <div class="verified-badge">Verified Business</div>
                                <?php endif; ?>

                                <div class="rating">
                                    <?php 
                                    echo str_repeat('★', $rounded_rating) . str_repeat('☆', 5 - $rounded_rating);
                                    echo " (" . number_format($rating, 1) . "/5)";
                                    ?>
                                </div>

                                <div class="location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo esc_html($city); ?>, <?php echo esc_html($state); ?>
                                </div>

                                <div class="card-actions">
                                    <a href="<?php echo get_permalink($listing->ID); ?>" 
                                       class="btn btn-primary">View Profile</a>
                                    <?php if ($phone) : ?>
                                        <a href="tel:<?php echo esc_attr($phone); ?>" 
                                           class="btn btn-outline">Call Now</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                        $rank++;
                    endforeach; 
                    
                    // Add placeholder cards if needed
                    for ($i = $rank; $i <= $settings['number_of_listings']; $i++) : ?>
                        <div class="listing-card placeholder-card">
                            <div class="logo-container">
                                <div class="company-logo">
                                    <span>Your Logo Here</span>
                                </div>
                            </div>
                            <div class="listing-content">
                                <h3 class="listing-title">Your Business Here</h3>
                                <p>Join our directory and showcase your business to thousands of potential customers.</p>
                                <div class="card-actions">
                                    <a href="/add-listing" class="btn btn-primary">Add Your Business</a>
                                    <a href="/pricing" class="btn btn-outline">View Pricing</a>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif;
    }
}