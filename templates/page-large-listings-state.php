<?php
/*
Template Name: Large Listings State Page
*/

get_header();

// Get the state from the URL parameter
$state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';

// Function to get large listings
function get_large_listings_for_state($state, $page = 1, $per_page = 20) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'large_listings';
    
    $offset = ($page - 1) * $per_page;
    
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name WHERE state = %s LIMIT %d OFFSET %d",
        $state,
        $per_page,
        $offset
    );
    
    error_log("Large listings query: " . $query);
    
    $results = $wpdb->get_results($query);
    
    error_log("Query results count: " . count($results));
    
    return $results;
}

// Get current page number
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Get listings
$listings = get_large_listings_for_state($state, $paged);

// Get total count for pagination
global $wpdb;
$table_name = $wpdb->prefix . 'large_listings';
$total_query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE state = %s", $state);
$total_listings = $wpdb->get_var($total_query);

$per_page = 20;
$total_pages = ceil($total_listings / $per_page);

?>

<div class="large-listings-state-page">
    <h1>Listings for <?php echo esc_html($state); ?></h1>
    
    <?php if (!empty($listings)) : ?>
        <p>Total listings: <?php echo $total_listings; ?></p>
        <div class="listings-grid">
            <?php foreach ($listings as $listing) : ?>
                <div class="listing-card">
                    <h2><?php echo esc_html($listing->company_name); ?></h2>
                    <p>Address: <?php echo esc_html($listing->address1); ?>, <?php echo esc_html($listing->city); ?>, <?php echo esc_html($listing->state); ?> <?php echo esc_html($listing->zip5); ?></p>
                    <p>Phone: <?php echo esc_html($listing->phone); ?></p>
                    <p>Email: <?php echo esc_html($listing->email); ?></p>
                    <!-- Add more fields as needed -->
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <div class="pagination">
            <?php
            echo paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo; Previous'),
                'next_text' => __('Next &raquo;'),
                'total' => $total_pages,
                'current' => $paged
            ));
            ?>
        </div>
    <?php else : ?>
        <p>No listings found for <?php echo esc_html($state); ?>.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>