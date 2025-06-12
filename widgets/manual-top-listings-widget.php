<?php
class Manual_Top_Listings_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'manual_top_listings_widget'; }
    public function get_title() { return __('Manual Top Listing List', 'directory-listings'); }
    public function get_icon() { return 'eicon-posts-grid'; }
    public function get_categories() { return ['directory-listings']; }
    public function get_style_depends() { return ['top-listings-styles']; }

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_style('top-listings-styles', plugin_dir_url(__FILE__) . '../assets/css/top-listings.css');
        
        add_filter('elementor/page_templates/canvas/template', function($template) {
            remove_action('wp_head', '_wp_render_title_tag', 1);
            remove_action('elementor/page_templates/canvas/before_content', 'the_title');
            return $template;
        });
    }

    protected function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Content Settings', 'directory-listings'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
            'is_full_width',
            [
                'label' => __('Full Width Layout', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        // Manual Listing Selection
        $this->add_control(
            'listing_ids',
            [
                'label' => __('Top Rated Listing IDs', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'description' => __('Enter top rated listing IDs separated by commas. The order matters - first ID will be #1, second #2, etc.', 'directory-listings'),
                'placeholder' => '123, 456, 789',
            ]
        );

        // Business Category Control
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

        $this->add_control('hero_spacing', [
            'label' => __('Top Spacing', 'directory-listings'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'selectors' => [
                '{{WRAPPER}} .top-listings-section' => 'padding-top: {{SIZE}}{{UNIT}};',
            ],
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
    private function get_manual_top_rated_listings($settings) {
        $listing_ids = array_map('trim', explode(',', $settings['listing_ids']));
        $listing_ids = array_filter($listing_ids); // Remove empty values
        
        if (empty($listing_ids)) {
            return array();
        }

        $args = array(
            'post_type' => 'directory_listing',
            'posts_per_page' => absint($settings['number_of_listings']),
            'post_status' => 'publish',
            'post__in' => $listing_ids,
            'orderby' => 'post__in', // Preserve the order of IDs as entered
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'listing_type',
                    'value' => 'paid',
                    'compare' => '='
                )
            ),
        );

        // Add category filter
        if ($settings['business_category'] !== 'all') {
            $args['tax_query'][] = array(
                'taxonomy' => 'business_category',
                'field' => 'term_id',
                'terms' => $settings['business_category'],
            );
        }

        // Add state filter
        if ($settings['state'] !== 'all') {
            $args['tax_query'][] = array(
                'taxonomy' => 'state',
                'field' => 'term_id',
                'terms' => $settings['state'],
            );
        }

        // Add city filter
        if (!empty($settings['city'])) {
            $args['meta_query'][] = array(
                'key' => 'city',
                'value' => $settings['city'],
                'compare' => 'LIKE'
            );
        }

        $listings = get_posts($args);
        error_log('Found ' . count($listings) . ' manual listings with query: ' . print_r($args, true));
        
        return $listings;
    }
   
    protected function render() {
        $settings = $this->get_settings_for_display();
        $container_class = $settings['is_full_width'] ? 'top-listings-section full-width' : 'top-listings-section';
        
        $listings = $this->get_manual_top_rated_listings($settings);
        
        // Get location information
        $category_name = ($settings['business_category'] !== 'all') 
            ? get_term($settings['business_category'], 'business_category')->name 
            : '';
        
        $state_name = ($settings['state'] !== 'all')
            ? get_term($settings['state'], 'state')->name
            : '';

        $city_name = $settings['city'];

        // Replace placeholders in title and description
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

        // Clean up empty placeholders
        $title = preg_replace('/\s+in\s+,\s*/', ' in ', $title);
        $title = preg_replace('/\s+,\s*,\s*/', ', ', $title);
        $title = preg_replace('/\s*,\s*$/', '', $title);
        $title = preg_replace('/\s+,\s*\./', '.', $title);

        $description = preg_replace('/\s+in\s+,\s*/', ' in ', $description);
        $description = preg_replace('/\s+,\s*,\s*/', ', ', $description);
        $description = preg_replace('/\s*,\s*$/', '', $description);
        $description = preg_replace('/\s+,\s*\./', '.', $description);
        
        if (!empty($listings) || true) : ?>
        <style>
                .full-width {
    width: 100vw !important;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw !important;
    margin-right: -50vw !important;
    margin-top: -1px !important;
}

.elementor-section.elementor-section-boxed > .elementor-container {
    max-width: 100% !important;
    padding: 0;
}

.top-listings-section {
    margin-top: -1px;
    background-color: #ffffff;
    text-align: center;
}

.top-listings-hero {
    padding: 40px 0;
    margin-bottom: 40px;
    text-align: center;
    background: transparent;
    width: 100%;
}

.top-listings-hero .container {
    text-align: center;
    width: 100%;
}

.top-listings-title {
    color: #0a3161 !important;
    margin: 0 0 20px 0;
    font-size: 2.5em;
    line-height: 1.2;
    text-align: center;
}

.top-listings-description {
    color: #c41230 !important;
    margin: 0 auto;
    font-size: 1.2em;
    line-height: 1.6;
    text-align: center;
    max-width: 800px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    text-align: center;
}

.top-listings-grid {
    display: grid;
    gap: 30px;
    padding: 20px;
}

.listing-card {
    display: flex;
    position: relative;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.listing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.logo-container {
    width: 200px;
    padding-right: 20px;
    padding-left: 85px;
    flex-shrink: 0;
    position: relative;
}

.logo-container img {
    width: 100%;
    height: auto;
    object-fit: contain;
    border-radius: 4px;
}

.listing-content {
    flex: 1;
    position: relative;
    min-width: 0;
    display: flex;
    flex-direction: column;
    text-align: center;
}

.listing-title {
    margin: 10px 0;
    font-size: 24px;
    color: #333;
    line-height: 1.3;
    padding-right: 100px;
    text-align: center;
}

.verified-badge {
    position: absolute;
    right: 20px;
    top: 10px;
    padding: 5px 10px;
    background: #4CAF50;
    color: white;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: 500;
    z-index: 5;
}

.top-rated-badge {
    position: absolute;
    left: 20px;
    top: 10px;
    background: #ffd700;
    color: #000;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
    z-index: 5;
}

.rank-badge {
    position: absolute;
    left: 20px;
    top: 45px;
    z-index: 10;
    background: #FFD700;
    border-radius: 20%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    border: 3px solid #c41230;
}

.medal {
    font-size: 30px;
}

.rank-number {
    color: #000;
    font-weight: bold;
    font-size: 18px;
}

.rating-stars {
    color: #ffd700;
    font-size: 1.1em;
    margin: 5px 0;
    display: flex;
    align-items: center;
    gap: 5px;
    justify-content: center;
}

.listing-description {
    margin: 15px 0;
    line-height: 1.6;
    color: #666;
    font-size: 1em;
    text-align: center;
}

.listing-actions {
    display: flex;
    gap: 15px;
    margin-top: auto;
    justify-content: center;
    padding-top: 20px;
}

.button {
    padding: 12px 24px;
    border-radius: 4px;
    text-align: center;
    text-decoration: none;
    font-weight: 500;
    min-width: 140px;
    display: inline-block;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.button-primary {
    background: #c41230;
    color: white !important;
    border: none;
}

.button-primary:hover {
    background: #a30f28;
}

.button-secondary {
    background: #0a3161;
    color: white !important;
    border: none;
}

.button-secondary:hover {
    background: #082548;
}

.button-phone {
    background: #4CAF50;
    color: white !important;
    border: none;
}

.button-phone:hover {
    background: #45a049;
}

.placeholder-card {
    border: 2px dashed #0a3161;
    background: rgba(255, 255, 255, 0.9);
}

.placeholder-card:hover {
    border-color: #c41230;
}

/* Mobile Styles */
@media (max-width: 767px) {
    .listing-card {
        flex-direction: column;
        align-items: center;
    }

    .logo-container {
        width: 100%;
        max-width: 200px;
        padding: 0;
        margin: 0 auto 20px;
    }

    .listing-content {
        padding: 0;
        text-align: center;
    }

    .listing-title {
        padding-right: 0;
        text-align: center;
    }

    .listing-actions {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .button {
        width: 100%;
        text-align: center;
    }

    .verified-badge {
        position: relative;
        right: auto;
        top: auto;
        margin: 0 auto 10px;
        display: inline-block;
    }
}
            </style>
            
            <div class="<?php echo esc_attr($container_class); ?>">
                <div class="top-listings-hero">
                    <div class="container">
                        <h2 class="top-listings-title"><?php echo esc_html($title); ?></h2>
                        <p class="top-listings-description"><?php echo esc_html($description); ?></p>
                    </div>
                </div>

                <div class="container">
                    <div class="top-listings-grid">
                        <?php 
                        $rank = 1;
                        $total_slots = absint($settings['number_of_listings']);
                        $actual_listings = count($listings);

                        // Display actual listings
                        foreach ($listings as $listing) :
                            $rating = get_field('overall_rating', $listing->ID);
                            $rating = (!empty($rating) && is_numeric($rating)) ? floatval($rating) : 0;
                            $rounded_rating = floor($rating);

                            $logo = get_field('logo', $listing->ID);
                            $website = get_field('website_link', $listing->ID);
                            $phone = get_field('phone_number', $listing->ID);
                            $is_verified = get_field('is_verified', $listing->ID);
                            ?>
                            <div class="listing-card">
                                <div class="rank-badge">
                                    <?php
                                    switch($rank) {
                                        case 1:
                                            echo '<span class="medal">🥇</span>';
                                            break;
                                        case 2:
                                            echo '<span class="medal">🥈</span>';
                                            break;
                                        case 3:
                                            echo '<span class="medal">🥉</span>';
                                            break;
                                        default:
                                            echo '<span class="rank-number">' . $rank . '</span>';
                                    }
                                    ?>
                                </div>

                                <div class="top-rated-badge">Top <?php echo $rank; ?></div>

                                <div class="logo-container">
                                    <?php if ($logo) : ?>
                                        <img src="<?php echo esc_url($logo['url']); ?>" 
                                             alt="<?php echo esc_attr($listing->post_title); ?>"
                                             loading="lazy">
                                    <?php endif; ?>
                                </div>

                                <div class="listing-content">
                                    <h3 class="listing-title"><?php echo esc_html($listing->post_title); ?></h3>

                                    <?php if ($is_verified) : ?>
                                        <div class="verified-badge">Verified Business</div>
                                    <?php endif; ?>

                                    <div class="rating-stars">
                                        <?php 
                                        echo str_repeat('★', $rounded_rating) . str_repeat('☆', 5 - $rounded_rating);
                                        echo " (" . number_format($rating, 1) . "/5)";
                                        ?>
                                    </div>

                                    <div class="listing-description">
                                        <?php echo wp_trim_words($listing->post_content, 30); ?>
                                    </div>

                                    <div class="listing-actions">
                                        <?php if ($phone) : ?>
                                            <a href="tel:<?php echo esc_attr($phone); ?>" 
                                               class="button button-phone">
                                                <i class="fas fa-phone"></i> Call Now
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($website) : ?>
                                            <a href="<?php echo esc_url($website); ?>" 
                                               class="button button-primary"
                                               target="_blank" 
                                               rel="noopener noreferrer">
                                                Visit Website
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="<?php echo get_permalink($listing->ID); ?>" 
                                           class="button button-secondary">
                                            View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            $rank++;
                        endforeach;

                        // Add placeholder cards if needed
                        for ($i = $actual_listings + 1; $i <= $total_slots; $i++) : ?>
                            <div class="listing-card placeholder-card">
                                <div class="rank-badge">
                                    <?php echo '<span class="rank-number">' . $i . '</span>'; ?>
                                </div>
                                <div class="logo-container">
                                    <div class="placeholder-logo">Your Logo Here</div>
                                </div>
                                <div class="listing-content">
                                    <h3 class="listing-title">Your Business Here</h3>
                                    <div class="listing-description">
                                        Join our directory and showcase your business to thousands of potential customers. Get featured in our top-rated listings and grow your business.
                                    </div>
                                    <div class="listing-actions placeholder-actions">
                                        <a href="/add-listing" class="button button-primary">Add Your Business</a>
                                        <a href="/pricing" class="button button-secondary">View Pricing</a>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        <?php endif;
    }
}