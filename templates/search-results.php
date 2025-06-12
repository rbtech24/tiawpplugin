<?php
/**
 * Template Name: Directory Search Results
 * 
 * @package YourDirectoryPlugin
 */

get_header();

// Get search parameters
$search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';

// Build query args
$args = array(
    'post_type' => 'directory_listing',
    'posts_per_page' => 12,
    'post_status' => 'publish',
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
);

// Add search query
if (!empty($search)) {
    $args['s'] = $search;
}

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

// Run the query
$listings = new WP_Query($args);
?>

<div class="container">
    <div class="search-results-page">
        <h1 class="page-title">
            <?php 
            if (!empty($search)) {
                echo 'Search Results for: ' . esc_html($search);
            } elseif ($category_id > 0) {
                $category = get_term($category_id, 'business_category');
                if ($category && !is_wp_error($category)) {
                    echo esc_html($category->name) . ' Listings';
                } else {
                    echo 'Directory Listings';
                }
            } else {
                echo 'Directory Listings';
            }
            ?>
        </h1>
        
        <div class="search-form-container">
            <form action="<?php echo esc_url(home_url('/search-results/')); ?>" method="get" class="search-form">
                <input type="text" name="search" value="<?php echo esc_attr($search); ?>" placeholder="Search..." class="search-input">
                
                <select name="category" class="filter-select">
                    <option value="">All Categories</option>
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'business_category',
                        'hide_empty' => true
                    ));
                    
                    foreach ($categories as $cat) {
                        echo '<option value="' . esc_attr($cat->term_id) . '" ' . selected($category_id, $cat->term_id, false) . '>' . esc_html($cat->name) . '</option>';
                    }
                    ?>
                </select>
                
                <select name="state" class="filter-select">
                    <option value="">All States</option>
                    <?php
                    $states = get_terms(array(
                        'taxonomy' => 'state',
                        'hide_empty' => true
                    ));
                    
                    foreach ($states as $st) {
                        echo '<option value="' . esc_attr($st->slug) . '" ' . selected($state, $st->slug, false) . '>' . esc_html($st->name) . '</option>';
                    }
                    ?>
                </select>
                
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
        
        <?php if ($listings->have_posts()) : ?>
            <div class="search-results-count">
                Found <?php echo $listings->found_posts; ?> listings
            </div>
            
            <div class="listings-grid">
                <?php while ($listings->have_posts()) : $listings->the_post(); ?>
                    <?php include(plugin_dir_path(__FILE__) . 'parts/listing-card.php'); ?>
                <?php endwhile; ?>
            </div>
            
            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '&laquo; Previous',
                    'next_text' => 'Next &raquo;',
                    'total' => $listings->max_num_pages,
                    'current' => max(1, get_query_var('paged'))
                ));
                ?>
            </div>
        <?php else : ?>
            <div class="no-results">
                <h2>No listings found</h2>
                <p>Try adjusting your search criteria or browse our categories.</p>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<?php
wp_reset_postdata();
get_footer();