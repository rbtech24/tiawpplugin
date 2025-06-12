<?php
/*
 * Template Name: Directory Category Page
 */

get_header();

// Get the current category object and pagination info
$category = get_queried_object();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$current_state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
$current_city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';

wp_enqueue_script('directory-filters');
wp_enqueue_script('directory-search');

wp_localize_script('directory-filters', 'directoryAjax', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('directory_filter_nonce'),
    'category_id' => $category->term_id
));

// Function to get cities by state AND category
function get_state_category_cities($state_slug, $category_id) {
    global $wpdb;
    
    $cities = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT pm.meta_value
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        INNER JOIN {$wpdb->term_relationships} tr_state ON tr_state.object_id = p.ID
        INNER JOIN {$wpdb->term_taxonomy} tt_state ON tt_state.term_taxonomy_id = tr_state.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t_state ON t_state.term_id = tt_state.term_id
        INNER JOIN {$wpdb->term_relationships} tr_cat ON tr_cat.object_id = p.ID
        INNER JOIN {$wpdb->term_taxonomy} tt_cat ON tt_cat.term_taxonomy_id = tr_cat.term_taxonomy_id
        WHERE pm.meta_key = 'city'
        AND pm.meta_value != ''
        AND p.post_type = 'directory_listing'
        AND p.post_status = 'publish'
        AND t_state.slug = %s
        AND tt_state.taxonomy = 'state'
        AND tt_cat.term_id = %d
        AND tt_cat.taxonomy = 'business_category'
        ORDER BY pm.meta_value ASC",
        $state_slug,
        $category_id
    ));
    
    return array_unique($cities);
}
?>

<div class="category-directory-template">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Playfair+Display:wght@700&display=swap');

:root {
    --navy: #26387E;
    --red: #CD1339;
    --white: #FFFFFF;
    --light-gray: #F5F6FA;
    --dark-gray: #4B5563;
    --gold: #FFD700;
    --shadow: 0 2px 4px rgba(0,0,0,0.1);
    --border-radius: 8px;
    --transition: all 0.3s ease;
    --verified-green: #4CAF50;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Roboto', system-ui, sans-serif;
}

body {
    background: var(--light-gray);
    line-height: 1.6;
    color: var(--navy);
    margin: 0;
}

.hero-section {
    position: relative;
    height: 600px;
    display: flex;
    align-items: center;
    text-align: center;
    overflow: hidden;
    margin-top: 40px;
    background-color: var(--navy);
    width: 100%;
}

.hero-bg {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
    background-position: center;
    background-size: cover;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(rgba(38,56,126,0.9), rgba(38,56,126,0.95));
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    width: 90%;
    max-width: 1400px;
    margin: 0 auto;
    color: var(--white);
    padding: 0 1rem;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-text {
    font-size: 1.25rem;
    max-width: 800px;
    margin: 0 auto 3rem;
    line-height: 1.6;
}

.search-box {
    background: var(--white);
    max-width: 1050px;
    margin: 0 auto;
    padding: 1rem;
    border-radius: var(--border-radius);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.search-grid {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 1rem;
}

.search-input {
    position: relative;
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border-radius: var(--border-radius);
    padding: 0.5rem 1rem;
}

.search-input i {
    color: var(--dark-gray);
    font-size: 1.25rem;
    margin-right: 0.75rem;
}

.search-input input {
    width: 100%;
    padding: 0.75rem;
    border: none;
    background: transparent;
    font-size: 1rem;
}

.search-input input:focus {
    outline: none;
}

.search-btn {
    background: var(--red);
    color: var(--white);
    border: none;
    padding: 0.75rem 1.5rem;
    min-width: 100px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: var(--transition);
}

.search-btn:hover {
    background: #b11131;
    transform: translateY(-2px);
}

.filter-section {
    max-width: 1050px;
    margin: -2rem auto 2rem;
    background: var(--white);
    padding: 1rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    position: relative;
    z-index: 4;
    display: flex;
    gap: 1rem;
}

.filter-select {
    flex: 1;
    padding: 0.5rem;
    background: var(--white);
    border: 1px solid #ced4da;
    border-radius: var(--border-radius);
    font-size: 1rem;
    cursor: pointer;
    transition: var(--transition);
}

.filter-select:hover {
    border-color: var(--navy);
}

.container {
    max-width: 1400px;
    width: 90%;
    margin: 2rem auto;
    padding: 0 1rem;
}

.directory-content {
    display: flex;
    flex-direction: column;
    min-height: 80vh;
}

.listings-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    flex: 1;
}

.listing-card {
    background: var(--white);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: var(--transition);
    position: relative;
}

.listing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.excerpt {
    color: var(--dark-gray);
    margin: 1rem 0;
    font-size: 0.9rem;
    line-height: 1.5;
    text-align: left;
}

.premium-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: var(--gold);
    color: var(--navy);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    z-index: 1;
    font-size: 0.9rem;
}

.premium-badge i {
    font-size: 0.9rem;
}

.logo-container {
    padding: 1.5rem;
    background: var(--light-gray);
    text-align: center;
    position: relative;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    height: 250px;
}

.company-logo {
    position: relative;
    height: 100%;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.company-logo img {
    max-width: 280px;
    max-height: 180px;
    width: auto;
    height: auto;
    object-fit: contain;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.listing-content {
    padding: 1.5rem;
    text-align: center;
}

.listing-title {
    font-size: 1.5rem;
    color: var(--navy);
    margin-bottom: 0.75rem;
}

.rating {
    color: var(--gold);
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.location {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: var(--dark-gray);
    margin-bottom: 0.75rem;
}

.services {
    color: var(--dark-gray);
    margin: 1rem 0;
    line-height: 1.6;
}

.vote-info {
    color: var(--dark-gray);
    margin-bottom: 1.5rem;
}

.card-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.btn {
    padding: 0.75rem;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    text-align: center;
}

.btn-primary {
    background: var(--red);
    color: var(--white);
}

.btn-outline {
    background: transparent;
    color: var(--navy);
    border: 2px solid var(--navy);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: auto;
    padding: 40px 0;
    width: 100%;
}

.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 16px 24px;
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.pagination .page-numbers-container {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    flex: 1;
    margin: 0 20px;
}

.page-numbers {
    min-width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--navy);
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
    background: var(--light-gray);
}

.page-numbers.current {
    background-color: var(--navy);
    color: var(--white);
    box-shadow: 0 2px 8px rgba(38,56,126,0.3);
}

.page-numbers:not(.current):hover {
    background-color: var(--navy);
    color: var(--white);
    transform: translateY(-2px);
}

.page-numbers.dots {
    background: none;
    min-width: 24px;
    pointer-events: none;
}

.page-numbers.next,
.page-numbers.prev {
    background: var(--red);
    color: var(--white);
    padding: 12px 24px;
    border-radius: var(--border-radius);
    min-width: 120px;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.page-numbers.next:hover,
.page-numbers.prev:hover {
    background: #b11131;
    transform: translateY(-2px);
}

.verified-badge {
    display: inline-block;
    background-color: var(--verified-green);
    color: var(--white);
    font-size: 0.8rem;
    font-weight: bold;
    padding: 3px 8px;
    border-radius: 12px;
    margin-top: 5px;
}

.verified-badge::before {
    content: '✓ ';
    font-weight: bold;
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 1000;
    display: none;
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .search-grid {
        grid-template-columns: 1fr;
    }

    .filter-section {
        flex-direction: column;
    }

    .listings-grid {
        grid-template-columns: 1fr;
    }

    .pagination {
        flex-wrap: wrap;
        padding: 15px;
    }

    .page-numbers {
        min-width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
}
    </style>

    <div class="hero-section">
        <img src="<?php echo esc_url(get_theme_file_uri('assets/images/hero-bg.jpg')); ?>" alt="" class="hero-bg">
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <h1 class="hero-title">Find Top <?php echo esc_html($category->name); ?> Services</h1>
            <p class="hero-text">Connect with experienced <?php echo esc_html($category->name); ?> professionals. Compare top-rated providers and get the help you need today.</p>
            
            <div class="search-box">
                <div class="search-grid">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" 
                               class="directory-search-input" 
                               placeholder="Search by business name or service..."
                               value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>"
                               data-category="<?php echo esc_attr($category->term_id); ?>">
                        <div class="search-results"></div>
                    </div>
                    <button class="search-btn" data-category="<?php echo esc_attr($category->term_id); ?>">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-section">
        <select name="state" id="state-filter" class="filter-select">
            <option value="">All States</option>
            <?php
            $states = get_terms(array(
                'taxonomy' => 'state',
                'hide_empty' => true,
                'parent' => 0
            ));
            
            foreach ($states as $state) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($state->slug),
                    selected($current_state, $state->slug, false),
                    esc_html($state->name)
                );
            }
            ?>
        </select>

        <select name="city" id="city-filter" class="filter-select" <?php echo empty($current_state) ? 'disabled' : ''; ?>>
            <option value="">All Cities</option>
            <?php
            if ($current_state && $category) {
                $cities = get_state_category_cities($current_state, $category->term_id);
                if (!empty($cities)) {
                    foreach ($cities as $city) {
                        printf(
                            '<option value="%s" %s>%s</option>',
                            esc_attr($city),
                            selected($current_city, $city, false),
                            esc_html($city)
                        );
                    }
                }
            }
            ?>
        </select>

        <select name="sort" class="filter-select">
            <option value="">Sort By</option>
            <option value="rating">Highest Rated</option>
            <option value="reviews">Most Reviews</option>
            <option value="newest">Newest</option>
        </select>
    </div>

    <div class="container">
        <div class="listings-grid">
            <?php
            // Build query arguments with priority sorting
            $args = array(
                'post_type' => 'directory_listing',
                'posts_per_page' => 6,
                'paged' => $paged,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'business_category',
                        'field' => 'term_id',
                        'terms' => $category->term_id,
                    )
                ),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        // For sorting verified and top rated first
                        array(
                            'key' => 'is_verified',
                            'value' => '1',
                            'type' => 'NUMERIC',
                            'compare' => '='
                        ),
                        array(
                            'key' => 'top_rated',
                            'value' => '1',
                            'type' => 'NUMERIC',
                            'compare' => '='
                        ),
                        // Include all listings (even those without these fields)
                        array(
                            'key' => 'listing_type',
                            'compare' => 'EXISTS'
                        )
                    )
                ),
                'orderby' => array(
                    'is_verified' => 'DESC',
                    'top_rated' => 'DESC',
                    'listing_type' => 'ASC', // 'free' will come last
                    'date' => 'DESC' // Then sort by date within each group
                )
            );

            // Add state filter
            if (!empty($current_state)) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'state',
                    'field' => 'slug',
                    'terms' => $current_state
                );
            }

            // Add city filter
            if (!empty($current_city)) {
                $args['meta_query'][] = array(
                    'key' => 'city',
                    'value' => $current_city,
                    'compare' => '=',
                    'type' => 'CHAR'
                );
            }

            $listings = new WP_Query($args);

            if ($listings->have_posts()) :
                while ($listings->have_posts()) : $listings->the_post();
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
            ?>
                                <div class="listing-card <?php echo $is_featured ? 'featured-listing' : ''; ?>">
                    <?php if ($is_featured) : ?>
                        <div class="premium-badge">
                            <i class="fas fa-award"></i> Top Rated
                        </div>
                    <?php endif; ?>
                    
                    <div class="logo-container">
                        <?php 
                        $logo = get_field('logo');
                        $placeholder_url = '/wp-content/plugins/directory-listings/assets/images/placeholder.png';
                        
                        echo '<div class="company-logo">';
                        if ($listing_type === 'free' || empty($logo) || !is_array($logo) || !isset($logo['url'])) {
                            echo '<img src="' . esc_url($placeholder_url) . '" alt="' . esc_attr(get_the_title()) . ' Placeholder Logo">';
                        } else {
                            echo '<img src="' . esc_url($logo['url']) . '" alt="' . esc_attr(get_the_title()) . '">';
                        }
                        echo '</div>';
                        ?>
                    </div>
                    <div class="listing-content">
                        <h2 class="listing-title"><?php the_title(); ?></h2>
                        <?php if ($is_verified) : ?>
                            <div class="verified-badge">Verified Business</div>
                        <?php endif; ?>
                        <div class="rating">
                            <?php
                            if ($rating > 0) {
                                $stars = str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating));
                                echo $stars . ' ' . number_format($rating, 1) . ' (' . $review_count . ' reviews)';
                            } else {
                                echo 'No ratings yet';
                            }
                            ?>
                        </div>
                        <div class="location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo esc_html($city ?: 'City not specified'); ?>, <?php echo esc_html($state ?: 'State not specified'); ?>
                        </div>
                        <div class="excerpt">
                            <?php 
                            $excerpt = get_field('business_description');
                            if (!empty($excerpt)) {
                                echo wp_trim_words($excerpt, 20, '...');
                            } else {
                                $excerpt = get_the_excerpt();
                                echo !empty($excerpt) ? wp_trim_words($excerpt, 20, '...') : 'No description available';
                            }
                            ?>
                        </div>
                        <div class="services">
                            <?php 
                            $services = get_field('services_offered');
                            if (!empty($services) && is_array($services)) {
                                $service_names = array_column($services, 'service_name');
                                echo esc_html(implode(' • ', array_slice($service_names, 0, 3)));
                            } else {
                                echo 'Services not specified';
                            }
                            ?>
                        </div>
                        <div class="vote-info">
                            <?php echo esc_html($vote_score); ?> Upvotes (<?php echo esc_html($upvotes); ?> 👍 / <?php echo esc_html($downvotes); ?> 👎)
                        </div>
                        <div class="card-actions">
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">View Profile</a>
                            <?php if ($phone) : ?>
                                <a href="tel:<?php echo esc_attr($phone); ?>" class="btn btn-outline">Call Now</a>
                            <?php else : ?>
                                <span class="btn btn-outline disabled">No Phone</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();

                // Only show pagination if there are actual listings and more than one page
if ($listings->max_num_pages > 1) :
    $total_posts = $listings->found_posts;
    $posts_per_page = $listings->query_vars['posts_per_page'];
    $actual_pages = ceil($total_posts / $posts_per_page);
    
    if ($actual_pages > 1) : 
        $current_page = max(1, $paged);
        
        // Build the base URL with any existing query parameters
        $base_url = add_query_arg(array(), '');
        if ($current_state) {
            $base_url = add_query_arg('state', $current_state, $base_url);
            if ($current_city) {
                $base_url = add_query_arg('city', $current_city, $base_url);
            }
        }
        ?>
        <div class="pagination-container">
            <div class="pagination">
                <?php if ($current_page > 1) : 
                    $prev_url = add_query_arg('paged', $current_page - 1, $base_url);
                    ?>
                    <a href="<?php echo esc_url($prev_url); ?>" class="pagination-button">Previous</a>
                <?php else : ?>
                    <span class="pagination-button disabled">Previous</span>
                <?php endif; ?>

                <?php if ($current_page < $actual_pages) : 
                    $next_url = add_query_arg('paged', $current_page + 1, $base_url);
                    ?>
                    <a href="<?php echo esc_url($next_url); ?>" class="pagination-button">Next</a>
                <?php else : ?>
                    <span class="pagination-button disabled">Next</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php else : ?>
    <p class="no-results">No listings found in this category.</p>
<?php endif; ?>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Initialize variables
        const searchInput = $('.directory-search-input');
        const listingsGrid = $('.listings-grid');
        const searchBtn = $('.search-btn');
        let searchTimeout;

        // Handle search input
        searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                updateListings();
            }, 500);
        });

        // Handle filter changes
        $('#state-filter, #city-filter, select[name="sort"]').on('change', function() {
            updateListings();
        });

        // Handle state changes to update cities
        $('#state-filter').on('change', function() {
            const stateId = $(this).val();
            const cityFilter = $('#city-filter');
            
            cityFilter.prop('disabled', !stateId);
            cityFilter.html('<option value="">All Cities</option>');
            
            if (stateId) {
                $.ajax({
                    url: directoryAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_cities',
                        state: stateId,
                        category_id: directoryAjax.category_id,
                        nonce: directoryAjax.nonce
                    },
                    success: function(response) {
                        if (response.success && response.data.cities) {
                            response.data.cities.forEach(function(city) {
                                cityFilter.append(new Option(city, city));
                            });
                        }
                    }
                });
            }
        });

        function updateListings() {
            const data = {
                action: 'filter_listings',
                nonce: directoryAjax.nonce,
                category_id: directoryAjax.category_id,
                state: $('#state-filter').val(),
                city: $('#city-filter').val(),
                sort: $('select[name="sort"]').val(),
                search: searchInput.val()
            };

            listingsGrid.addClass('loading');

            $.ajax({
                url: directoryAjax.ajaxurl,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        listingsGrid.html(response.data.html);
                        updateURL();
                    }
                },
                complete: function() {
                    listingsGrid.removeClass('loading');
                }
            });
        }

        function updateURL() {
            const params = new URLSearchParams(window.location.search);
            const filters = {
                'state': $('#state-filter').val(),
                'city': $('#city-filter').val(),
                'sort': $('select[name="sort"]').val(),
                'search': searchInput.val()
            };

            Object.entries(filters).forEach(([key, value]) => {
                if (value) {
                    params.set(key, value);
                } else {
                    params.delete(key);
                }
            });

            window.history.replaceState({}, '', `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`);
        }
    });
    </script>
</div>

<?php get_footer(); ?>