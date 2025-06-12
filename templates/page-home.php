<?php
/**
 * Template Name: Modern Directory Home Page
 * @package Directory_Listings
 */

defined('ABSPATH') || exit;

// Helper Functions
if (!function_exists('get_listing_image_html')) {
    function get_listing_image_html($post_id) {
        $listing_type = get_field('listing_type', $post_id);
        $logo = get_field('logo', $post_id);
        $placeholder_url = '/wp-content/plugins/directory-listings/assets/images/placeholder.png';
        
        $html = '<div class="company-logo">';
        if ($listing_type === 'free' || empty($logo) || !is_array($logo) || !isset($logo['url'])) {
            $html .= '<img src="' . esc_url($placeholder_url) . '" alt="Placeholder">';
        } else {
            $html .= '<img src="' . esc_url($logo['url']) . '" alt="' . esc_attr(get_the_title($post_id)) . '">';
        }
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('get_most_visited_categories')) {
    function get_most_visited_categories($limit = 8) {
        return get_terms(array(
            'taxonomy' => 'business_category',
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => $limit,
            'hide_empty' => true
        ));
    }
}

if (!function_exists('get_recently_added_listings')) {
    function get_recently_added_listings($limit = 6) {
        return new WP_Query(array(
            'post_type' => 'directory_listing',
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false
        ));
    }
}

// Get data
try {
    $popular_categories = get_most_visited_categories();
    $recent_listings = get_recently_added_listings();
} catch (Exception $e) {
    error_log('Directory Homepage Error: ' . $e->getMessage());
    $popular_categories = array();
    $recent_listings = new WP_Query();
}

get_header();
?>

<style>
/* Base Styles */
:root {
    --navy-blue: #002868;
    --true-red: #BF0A30;
    --pure-white: #FFFFFF;
    --off-white: #F8F9FA;
    --gold: #FFD700;
    --teal: #006d77;
    --text-dark: #333;
    --border-radius: 10px;
}

/* Global Container */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, var(--navy-blue), #001845);
    padding: 6rem 1rem;
    position: relative;
    overflow: hidden;
    margin-top: -60px;
}

.hero-content {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    color: var(--pure-white);
    position: relative;
    z-index: 2;
    padding: 2rem 0;
}

.hero-grid {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    opacity: 0.1;
    z-index: 1;
}

.hero-grid-item {
    border: 1px solid var(--pure-white);
    transform: rotate(45deg);
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.hero-subtitle {
    font-size: 1.25rem;
    margin: 1rem auto 2rem;
    opacity: 0.9;
    text-align: center;
    max-width: 800px;
}

/* Search Box */
.search-container {
    max-width: 800px;
    margin: 2rem auto 0;
    padding: 0 1rem;
}

.search-box {
    background: var(--pure-white);
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.search-form {
    display: flex;
    gap: 1rem;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.search-button {
    background: var(--true-red);
    color: var(--pure-white);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
}

.search-button:hover {
    background: #a30826;
}

/* Categories Section */
.categories-section {
    background: var(--pure-white);
    padding: 5rem 1rem;
}

.section-title {
    text-align: center;
    color: var(--navy-blue);
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 3rem;
    position: relative;
    padding-bottom: 1.5rem;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: var(--true-red);
    border-radius: 2px;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.category-card {
    background: var(--pure-white);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    text-decoration: none;
    color: var(--text-dark);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid #eee;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.category-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--off-white);
    border-radius: 50%;
    color: var(--navy-blue);
    font-size: 2rem;
}

.category-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--navy-blue);
}

.category-count {
    color: #64748b;
    font-size: 0.9rem;
}

/* States Section */
.states-section {
    background: var(--pure-white);
    padding: 5rem 0;
    position: relative;
    overflow: hidden;
}

.states-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, rgba(0,40,104,0.02) 25%, transparent 25%, transparent 75%, rgba(0,40,104,0.02) 75%),
                linear-gradient(45deg, rgba(0,40,104,0.02) 25%, transparent 25%, transparent 75%, rgba(0,40,104,0.02) 75%);
    background-size: 60px 60px;
    background-position: 0 0, 30px 30px;
    transform: rotate(15deg);
    z-index: 1;
}

.states-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 1rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    position: relative;
    z-index: 2;
}

.state-link {
    text-decoration: none;
    color: var(--text-dark);
    padding: 0.75rem;
    text-align: center;
    transition: all 0.3s ease;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    border: 1px solid #e5e7eb;
    font-weight: 500;
}

.state-link:hover {
    color: var(--true-red);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    background: #f8fafc;
}

/* Process Section */
.process-section {
    background: var(--off-white);
    padding: 5rem 0;
    position: relative;
    overflow: hidden;
}

.process-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2.5rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    position: relative;
}

.process-grid::before {
    content: '';
    position: absolute;
    top: 40%;
    left: 25%;
    right: 25%;
    height: 2px;
    background: linear-gradient(to right, transparent 0%, var(--navy-blue) 50%, transparent 100%);
    opacity: 0.1;
    z-index: 1;
}

.process-card {
    background: var(--pure-white);
    padding: 2.5rem 2rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
    border: 1px solid rgba(0,40,104,0.08);
}

.process-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.process-icon {
    width: 80px;
    height: 80px;
    background: var(--navy-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    position: relative;
}

.process-icon::after {
    content: '';
    position: absolute;
    inset: -6px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(0,40,104,0.1), transparent);
    z-index: -1;
}

.process-icon i {
    color: var(--pure-white);
    font-size: 2rem;
}

.process-card h3 {
    color: var(--navy-blue);
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 1rem;
}

.process-card p {
    color: #4a5568;
    font-size: 1.1rem;
    line-height: 1.6;
    margin: 0;
    padding: 0 0.5rem;
}

/* Listing Cards */
./* Listing Card */
.listings-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

.listing-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Navy Header Bar */
.listing-header {
    background: #002868;
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Title in Header */
.listing-header h3 {
    color: white;
    font-size: 1.1rem;
    margin: 0;
}

/* Badge Container */
.badge-container {
    display: flex;
    gap: 0.5rem;
}

/* Top Rated Badge */
.featured-tag {
    background: #BF0A30;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Verified Badge */
.verified-badge {
    background: #4CAF50;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Content Area */
.listing-body {
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

/* Logo/Image */
.listing-image {
    margin-bottom: 1.5rem;
}

.company-logo {
    width: 200px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.company-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* Buttons */
.listing-footer {
    padding: 1rem;
    display: flex;
    gap: 1rem;
    border-top: 1px solid #eee;
}

.btn-primary {
    flex: 1;
    padding: 0.75rem;
    background: #002868;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    text-align: center;
}

.btn-secondary {
    flex: 1;
    padding: 0.75rem;
    background: white;
    color: #002868;
    text-decoration: none;
    border: 1px solid #002868;
    border-radius: 4px;
    text-align: center;
}

@media (max-width: 768px) {
    .listings-grid {
        grid-template-columns: 1fr;
    }
    
    .listing-footer {
        flex-direction: column;
    }
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, var(--navy-blue), #001845);
    color: var(--pure-white);
    padding: 4rem 1rem;
    text-align: center;
    margin: 3rem 0;
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
}

.cta-content h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--pure-white);
}

.cta-features {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin: 2rem 0;
}

.feature {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.feature i {
    color: var(--gold);
}

.btn-cta {
    display: inline-block;
    background: var(--true-red);
    color: var(--pure-white);
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 2rem;
}

.btn-cta:hover {
    background: #a30826;
    transform: translateY(-2px);
}

/* FAQ Section */
.faq-section {
    padding: 4rem 0;
    background: var(--off-white);
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.faq-item {
    background: var(--pure-white);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Responsive Adjustments */
@media (max-width: 1024px) {
    .process-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .process-grid::before {
        display: none;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 4rem 1rem;
    }
    
    .hero-title {
        font-size: 2rem;
    }
    
    .search-form {
        flex-direction: column;
    }
    
    .process-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .states-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    }
    
    .listings-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .listing-header {
        padding: 1rem;
    }
    
    .listing-footer {
        flex-direction: column;
    }
    
    .cta-features {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
</head>
<body>
<div class="hero-section">
    <div class="hero-grid">
        <?php for ($i = 0; $i < 25; $i++) : ?>
            <div class="hero-grid-item"></div>
        <?php endfor; ?>
    </div>
    <div class="hero-content">
        <h1 class="hero-title">Locating & Verifying the<br>Best American Professionals</h1>
        <p class="hero-subtitle">Find trusted local businesses in your area</p>
        
        <div class="search-container">
            <div class="search-box">
                <form id="directory-search-form" class="search-form">
                    <input type="text" class="search-input" name="search" id="search-input" placeholder="Search for businesses...">
                    <select name="category" id="category-select" class="search-input">
                        <option value="">All Categories</option>
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'business_category',
                            'hide_empty' => true,
                            'parent' => 0
                        ));
                        
                        foreach ($categories as $category) {
                            echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                        }
                        ?>
                    </select>
                    <select name="state" id="state-select" class="search-input">
                        <option value="">All States</option>
                        <?php
                        $states = get_terms(array(
                            'taxonomy' => 'state',
                            'hide_empty' => true,
                        ));
                        
                        foreach ($states as $state) {
                            echo '<option value="' . esc_attr($state->slug) . '">' . esc_html($state->name) . '</option>';
                        }
                        ?>
                    </select>
                    <button type="submit" class="search-button" id="search-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="search-results" class="container"></div>

<section class="categories-section">
    <h2 class="section-title">Discover Top Professionals by Field</h2>
    <div class="categories-grid">
        <?php
        if (!empty($popular_categories) && !is_wp_error($popular_categories)) :
            foreach ($popular_categories as $category) :
                $icon = get_term_meta($category->term_id, 'category_icon', true);
        ?>
                <a href="<?php echo get_term_link($category); ?>" class="category-card">
                    <div class="category-icon">
                        <?php echo $icon ? esc_html($icon) : '★'; ?>
                    </div>
                    <h3 class="category-name"><?php echo esc_html($category->name); ?></h3>
                    <span class="category-count"><?php echo $category->count; ?> businesses</span>
                </a>
        <?php 
            endforeach;
        endif;
        ?>
    </div>
</section>

<section class="states-section">
    <h2 class="section-title">Search Services By State</h2>
    <div class="states-grid">
        <?php
        // Complete array of US states and territories alphabetically
        $states = array(
            'Alabama' => 'AL',
            'Alaska' => 'AK',
            'Arizona' => 'AZ',
            'Arkansas' => 'AR',
            'California' => 'CA',
            'Colorado' => 'CO',
            'Connecticut' => 'CT',
            'Delaware' => 'DE',
            'District of Columbia' => 'DC',
            'Florida' => 'FL',
            'Georgia' => 'GA',
            'Hawaii' => 'HI',
            'Idaho' => 'ID',
            'Illinois' => 'IL',
            'Indiana' => 'IN',
            'Iowa' => 'IA',
            'Kansas' => 'KS',
            'Kentucky' => 'KY',
            'Louisiana' => 'LA',
            'Maine' => 'ME',
            'Maryland' => 'MD',
            'Massachusetts' => 'MA',
            'Michigan' => 'MI',
            'Minnesota' => 'MN',
            'Mississippi' => 'MS',
            'Missouri' => 'MO',
            'Montana' => 'MT',
            'Nebraska' => 'NE',
            'Nevada' => 'NV',
            'New Hampshire' => 'NH',
            'New Jersey' => 'NJ',
            'New Mexico' => 'NM',
            'New York' => 'NY',
            'North Carolina' => 'NC',
            'North Dakota' => 'ND',
            'Ohio' => 'OH',
            'Oklahoma' => 'OK',
            'Oregon' => 'OR',
            'Pennsylvania' => 'PA',
            'Rhode Island' => 'RI',
            'South Carolina' => 'SC',
            'South Dakota' => 'SD',
            'Tennessee' => 'TN',
            'Texas' => 'TX',
            'Utah' => 'UT',
            'Vermont' => 'VT',
            'Virginia' => 'VA',
            'Washington' => 'WA',
            'West Virginia' => 'WV',
            'Wisconsin' => 'WI',
            'Wyoming' => 'WY'
        );
        
        foreach ($states as $state_name => $state_abbr) :
            $state_slug = sanitize_title($state_name);
            $state_url = home_url('state/' . $state_slug);
        ?>
            <a href="<?php echo esc_url($state_url); ?>" class="state-link" title="Find top businesses in <?php echo esc_attr($state_name); ?>">
                <?php echo esc_html($state_name); ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<style>
/* Base Styles */
.states-section {
    background: var(--pure-white);
    padding: 5rem 0;
    position: relative;
    overflow: hidden;
}

.states-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, rgba(0,40,104,0.02) 25%, transparent 25%, transparent 75%, rgba(0,40,104,0.02) 75%),
                linear-gradient(45deg, rgba(0,40,104,0.02) 25%, transparent 25%, transparent 75%, rgba(0,40,104,0.02) 75%);
    background-size: 60px 60px;
    background-position: 0 0, 30px 30px;
    transform: rotate(15deg);
    z-index: 1;
}

.section-title {
    text-align: center;
    color: var(--navy-blue);
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 3rem;
    position: relative;
    padding-bottom: 1.5rem;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: var(--true-red);
    border-radius: 2px;
}

.states-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 1rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    position: relative;
    z-index: 2;
}

.state-link {
    text-decoration: none;
    color: var(--text-dark);
    padding: 0.75rem;
    text-align: center;
    transition: all 0.3s ease;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    border: 1px solid #e5e7eb;
    font-weight: 500;
}

.state-link:hover {
    color: var(--true-red);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    background: #f8fafc;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .states-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    }
    
    .section-title {
        font-size: 2rem;
        margin-bottom: 2rem;
    }
    
    .states-section {
        padding: 3rem 0;
    }
}

@media (max-width: 480px) {
    .states-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
    
    .state-link {
        padding: 0.5rem;
        font-size: 0.9rem;
    }
}
</style>


<section class="featured-listings">
    <div class="container">
        <h2 class="section-title">Top Rated American Businesses</h2>
        <div class="listings-grid">
            <?php
            $featured_args = array(
                'post_type' => 'directory_listing',
                'posts_per_page' => 6,
                'meta_query' => array(
                    array(
                        'key' => 'top_rated',
                        'value' => '1',
                        'compare' => '='
                    )
                )
            );
            
            $featured_query = new WP_Query($featured_args);
            
            if ($featured_query->have_posts()) :
                while ($featured_query->have_posts()) : $featured_query->the_post();
                    $listing_type = get_field('listing_type');
                    $is_verified = get_field('is_verified');
                    $rating = get_field('overall_rating');
                    $review_count = get_field('review_count');
                    $phone = get_field('phone_number');
            ?>
              <div class="listing-card is-featured">
                <div class="badges-row">
                    <span class="badge top-rated-badge">★ Top Rated</span>
                    <?php if ($is_verified) : ?>
                        <span class="badge verified-badge">✓ Verified</span>
                    <?php endif; ?>
                </div>

                <div class="listing-image">
                    <?php 
                    $logo = get_field('logo');
                    $placeholder_url = '/wp-content/plugins/directory-listings/assets/images/placeholder.png';
                    
                    echo '<div class="company-logo">';
                    if ($listing_type === 'free' || empty($logo) || !is_array($logo) || !isset($logo['url'])) {
                        echo '<img src="' . esc_url($placeholder_url) . '" alt="Placeholder">'; 
                    } else {
                        echo '<img src="' . esc_url($logo['url']) . '" alt="' . esc_attr(get_the_title()) . '">';
                    }
                    echo '</div>';
                    ?>
                </div>
                
                <a href="<?php the_permalink(); ?>" class="business-title">
                    <?php the_title(); ?>
                </a>
                
                <div class="rating">
                    <?php if ($rating > 0) : ?>
                        <div class="stars">
                            <?php
                            $filled_stars = round($rating);
                            $empty_stars = 5 - $filled_stars;
                            echo str_repeat('★', max(0, min(5, $filled_stars)));
                            echo str_repeat('☆', max(0, min(5, $empty_stars)));
                            ?>
                        </div>
                        <div class="rating-count">(<?php echo $review_count; ?> reviews)</div>
                    <?php else : ?>
                        <div class="no-ratings">No ratings yet</div>
                    <?php endif; ?>
                </div>

                <div class="excerpt">
                    <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                </div>
                
                <div class="card-actions">
                    <a href="<?php the_permalink(); ?>" class="btn-primary">View Profile</a>
                    <?php if ($phone) : ?>
                        <a href="tel:<?php echo esc_attr($phone); ?>" class="btn-secondary">Call Now</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<style>
.listings-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.listing-card {
    background: white;
    border-radius: 8px;
    padding: 0.75rem 1rem 1.5rem;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.listing-card.is-featured {
    border: 2px solid #FFD700;
}

.listing-image {
    width: 100%;
    height: 200px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
    padding: 0;
}

.company-logo {
    width: 100%;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.company-logo img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
}

.badges-row {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin: 0;
    width: 100%;
    padding: 0 0 0.5rem 0;
}

.badge {
    padding: 0.35rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
}

.top-rated-badge {
    background: #FFD700;
    color: black;
}

.verified-badge {
    background: #4CAF50;
    color: white;
}

.business-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #003087;
    margin: 0.5rem 0;
    text-decoration: none;
    font-family: system-ui, -apple-system, sans-serif;
    display: block;
}

.business-title:hover {
    text-decoration: underline;
}

.rating {
    margin: 0.75rem 0;
}

.stars {
    color: #FFD700;
    font-size: 1.25rem;
    letter-spacing: 0.2em;
    margin-bottom: 0.25rem;
}

.rating-count {
    color: #666;
    font-size: 0.85rem;
}

.no-ratings {
    color: #FFD700;
    font-size: 1.1rem;
}

.excerpt {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
    margin: 0.75rem auto;
    padding: 0;
    max-width: 85%;
}

.card-actions {
    width: 100%;
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    padding: 0 0.5rem;
}

.btn-primary, 
.btn-secondary {
    flex: 1;
    padding: 0.75rem 1.5rem;
    text-align: center;
    text-decoration: none;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-primary {
    background: #CD1339;
    color: white;
    border: none;
}

.btn-secondary {
    background: white;
    color: #003087;
    border: 2px solid #003087;
}

.btn-primary:hover,
.btn-secondary:hover {
    transform: translateY(-2px);
    opacity: 0.95;
}

@media (max-width: 1024px) {
    .listings-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .listings-grid {
        grid-template-columns: 1fr;
    }
    
    .card-actions {
        flex-direction: column;
    }
}
</style>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>List Your Business Today</h2>
            <p>Join thousands of successful American businesses reaching new customers every day</p>
            <div class="cta-features">
                <div class="feature">
                    <i class="fas fa-check"></i>
                    <span>Enhanced Visibility</span>
                </div>
                <div class="feature">
                    <i class="fas fa-check"></i>
                    <span>Verified Business Status</span>
                </div>
                <div class="feature">
                    <i class="fas fa-check"></i>
                    <span>Customer Reviews</span>
                </div>
            </div>
            <a href="/add-your-business" class="btn-cta">Get Started Now</a>
        </div>
    </div>
</section>

<section class="faq-section">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="faq-grid">
            <div class="faq-item">
                <h3>How do you verify businesses?</h3>
                <p>Our thorough verification process includes license checks, customer reviews, and business documentation verification.</p>
            </div>
            <div class="faq-item">
                <h3>How can I list my business?</h3>
                <p>Simply click the "Get Started Now" button and follow our easy submission process to list your business.</p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>