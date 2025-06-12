// templates/search/results.php

<?php
if (!defined('ABSPATH')) {
    exit;
}

$directory_search = Directory_Search_Filter::get_instance();
$active_filters = $directory_search->get_active_filters();
?>

<div class="search-results-container">
    <!-- Active Filters -->
    <?php if (!empty($active_filters)): ?>
    <div class="active-filters">
        <?php foreach ($active_filters as $key => $filter): ?>
            <span class="filter-tag">
                <?php echo esc_html($filter['label']); ?>: <?php echo esc_html($filter['value']); ?>
                <button type="button" class="remove" data-filter="<?php echo esc_attr($key); ?>">×</button>
            </span>
        <?php endforeach; ?>
        <?php if (count($active_filters) > 1): ?>
            <a href="<?php echo esc_url(remove_query_arg(array_keys($active_filters))); ?>" class="clear-all">
                Clear All
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Results Header -->
    <div class="results-header">
        <div class="results-count">
            <?php
            $total_results = $wp_query->found_posts;
            printf(
                _n(
                    '%s listing found',
                    '%s listings found',
                    $total_results,
                    'directory-listings'
                ),
                number_format_i18n($total_results)
            );
            ?>
        </div>

        <div class="view-controls">
            <button type="button" class="view-map-btn">
                <i class="fas fa-map-marker-alt"></i> Map View
            </button>
            <select name="sort" id="sort-filter" class="filter-select">
                <option value="">Sort By</option>
                <option value="rating_high" <?php selected(isset($_GET['sort']) ? $_GET['sort'] : '', 'rating_high'); ?>>
                    Highest Rated
                </option>
                <option value="rating_low" <?php selected(isset($_GET['sort']) ? $_GET['sort'] : '', 'rating_low'); ?>>
                    Lowest Rated
                </option>
                <option value="reviews" <?php selected(isset($_GET['sort']) ? $_GET['sort'] : '', 'reviews'); ?>>
                    Most Reviews
                </option>
                <option value="newest" <?php selected(isset($_GET['sort']) ? $_GET['sort'] : '', 'newest'); ?>>
                    Newest First
                </option>
            </select>
        </div>
    </div>

    <!-- Listings Grid -->
    <div class="listings-grid">
        <?php
        if (have_posts()):
            while (have_posts()): the_post();
                $rating = get_field('overall_rating');
                $review_count = get_field('review_count');
                $is_verified = get_field('is_verified');
                $city = get_field('city');
                $state = get_field('state');
                ?>
                <div class="listing-card">
                    <div class="listing-header">
                        <?php if ($is_verified): ?>
                            <div class="verified-badge">
                                <i class="fas fa-check-circle"></i> Verified
                            </div>
                        <?php endif; ?>

                        <?php if ($rating): ?>
                            <div class="rating-badge">
                                <?php echo number_format($rating, 1); ?> ★
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="listing-content">
                        <h3 class="listing-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>

                        <div class="listing-meta">
                            <?php if ($rating): ?>
                            <div class="rating">
                                <div class="rating-stars">
                                    <?php
                                    $full_stars = floor($rating);
                                    $half_star = $rating - $full_stars >= 0.5;
                                    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

                                    echo str_repeat('★', $full_stars);
                                    if ($half_star) echo '⯨';
                                    echo str_repeat('☆', $empty_stars);
                                    ?>
                                </div>
                                <span class="review-count">
                                    (<?php echo number_format($review_count); ?> reviews)
                                </span>
                            </div>
                            <?php endif; ?>

                            <?php if ($city && $state): ?>
                            <div class="location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo esc_html($city . ', ' . $state); ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="listing-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                        </div>
                    </div>

                    <div class="listing-footer">
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                            View Profile
                        </a>
                        <button type="button" 
                                // templates/search/results.php (continued)

                                class="btn btn-secondary quick-view-btn" 
                                data-listing-id="<?php echo get_the_ID(); ?>">
                            Quick View
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="no-results">
                <div class="no-results-content">
                    <i class="fas fa-search"></i>
                    <h3><?php _e('No Listings Found', 'directory-listings'); ?></h3>
                    <p><?php _e('No listings match your current search criteria. Try adjusting your filters or search terms.', 'directory-listings'); ?></p>
                    
                    <div class="no-results-actions">
                        <a href="<?php echo esc_url(remove_query_arg(array_keys($_GET))); ?>" class="btn btn-primary">
                            <?php _e('Clear All Filters', 'directory-listings'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($wp_query->max_num_pages > 1): ?>
        <div class="pagination-wrapper">
            <?php
            echo paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo; Previous', 'directory-listings'),
                'next_text' => __('Next &raquo;', 'directory-listings'),
                'total' => $wp_query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
                'type' => 'list'
            ));
            ?>
        </div>
    <?php endif; ?>
</div>

<!-- Quick View Modal -->
<div id="quick-view-modal" class="search-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><?php _e('Quick View', 'directory-listings'); ?></h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Map Modal -->
<div id="map-modal" class="search-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><?php _e('Map View', 'directory-listings'); ?></h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div id="listings-map" class="map-view"></div>
            <div class="map-listings">
                <?php
                if (have_posts()):
                    rewind_posts(); // Reset the post query
                    while (have_posts()): the_post();
                        $lat = get_field('latitude');
                        $lng = get_field('longitude');
                        if ($lat && $lng):
                    ?>
                        <div class="map-listing-item" 
                             data-lat="<?php echo esc_attr($lat); ?>" 
                             data-lng="<?php echo esc_attr($lng); ?>">
                            <h4><?php the_title(); ?></h4>
                            <?php if ($rating = get_field('overall_rating')): ?>
                                <div class="rating">
                                    <?php echo number_format($rating, 1); ?> ★
                                </div>
                            <?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="btn btn-sm btn-primary">
                                View Details
                            </a>
                        </div>
                    <?php
                        endif;
                    endwhile;
                endif;
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Filters Modal -->
<div id="filters-modal" class="search-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><?php _e('Filter Options', 'directory-listings'); ?></h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="advanced-filters-form" method="GET">
                <!-- Price Range -->
                <div class="filter-group">
                    <label><?php _e('Price Range', 'directory-listings'); ?></label>
                    <select name="price_range" class="filter-select">
                        <option value=""><?php _e('Any Price', 'directory-listings'); ?></option>
                        <option value="low" <?php selected(isset($_GET['price_range']) ? $_GET['price_range'] : '', 'low'); ?>>
                            <?php _e('Budget Friendly ($)', 'directory-listings'); ?>
                        </option>
                        <option value="medium" <?php selected(isset($_GET['price_range']) ? $_GET['price_range'] : '', 'medium'); ?>>
                            <?php _e('Mid-Range ($$)', 'directory-listings'); ?>
                        </option>
                        <option value="high" <?php selected(isset($_GET['price_range']) ? $_GET['price_range'] : '', 'high'); ?>>
                            <?php _e('Premium ($$$)', 'directory-listings'); ?>
                        </option>
                    </select>
                </div>

                <!-- Verified Filter -->
                <div class="filter-group">
                    <label class="checkbox-label">
                        <input type="checkbox" 
                               name="verified" 
                               value="1" 
                               <?php checked(isset($_GET['verified']) ? $_GET['verified'] : '', '1'); ?>>
                        <?php _e('Verified Businesses Only', 'directory-listings'); ?>
                    </label>
                </div>

                <!-- Open Now Filter -->
                <div class="filter-group">
                    <label class="checkbox-label">
                        <input type="checkbox" 
                               name="open_now" 
                               value="1" 
                               <?php checked(isset($_GET['open_now']) ? $_GET['open_now'] : '', '1'); ?>>
                        <?php _e('Open Now', 'directory-listings'); ?>
                    </label>
                </div>

                <!-- Features Filter -->
                <?php if ($features = get_terms('listing_feature')): ?>
                    <div class="filter-group">
                        <label><?php _e('Features', 'directory-listings'); ?></label>
                        <div class="features-grid">
                            <?php foreach ($features as $feature): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" 
                                           name="features[]" 
                                           value="<?php echo esc_attr($feature->term_id); ?>"
                                           <?php checked(isset($_GET['features']) && in_array($feature->term_id, $_GET['features'])); ?>>
                                    <?php echo esc_html($feature->name); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Hidden fields to preserve existing query parameters -->
                <?php
                $preserve_params = array('state', 'city', 'sort');
                foreach ($preserve_params as $param) {
                    if (isset($_GET[$param])) {
                        printf(
                            '<input type="hidden" name="%s" value="%s">',
                            esc_attr($param),
                            esc_attr($_GET[$param])
                        );
                    }
                }
                ?>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close">
                <?php _e('Cancel', 'directory-listings'); ?>
            </button>
            <button type="submit" form="advanced-filters-form" class="btn btn-primary">
                <?php _e('Apply Filters', 'directory-listings'); ?>
            </button>
        </div>
    </div>
</div>