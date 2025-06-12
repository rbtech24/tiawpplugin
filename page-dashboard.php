<?php
/**
 * Template Name: User Dashboard
 * Description: Comprehensive dashboard for directory listings users
 * Version: 2.1
 */

// Security: Redirect if user is not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

get_header();

// Get current user info
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Pagination settings
$listings_per_page = 10;
$reviews_per_page = 10;
$current_page = max(1, get_query_var('paged'));

// Get all user listings
$total_listings = count_user_posts($user_id, 'directory_listing', true);

// Get counts for different listing statuses
$live_listings = count(get_posts(array(
    'post_type' => 'directory_listing',
    'author' => $user_id,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids'
)));

$pending_listings = count(get_posts(array(
    'post_type' => 'directory_listing',
    'author' => $user_id,
    'post_status' => 'pending',
    'posts_per_page' => -1,
    'fields' => 'ids'
)));

$draft_listings = count(get_posts(array(
    'post_type' => 'directory_listing',
    'author' => $user_id,
    'post_status' => 'draft',
    'posts_per_page' => -1,
    'fields' => 'ids'
)));

// Get reviews count
global $wpdb;
$total_reviews = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}directory_reviews WHERE user_id = %d",
    $user_id
));
?>

<div class="directory-dashboard">
    <div class="directory-dashboard">
<style>
/* Reset some basics */
.directory-dashboard {
    margin: 20px auto;
    padding: 0 20px;
    max-width: 1400px;
}

.directory-dashboard * {
    box-sizing: border-box;
}

/* Dashboard Layout */
.dashboard-container {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

/* Top Bar */
.dashboard-top-bar {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.quick-stats {
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.stat {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 14px;
    color: #666;
}

.badge {
    background: #e9ecef;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    color: #666;
}

/* Navigation */
.dashboard-navigation {
    flex: 0 0 280px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px 0;
}

.dashboard-navigation ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.dashboard-navigation .menu-group {
    padding: 20px 20px 10px;
    font-size: 12px;
    font-weight: 600;
    color: #666;
}

.dashboard-navigation a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    position: relative;
}

.dashboard-navigation a:hover {
    background: #f8f9fa;
}

.dashboard-navigation a.active {
    background: #f8f9fa;
    color: #0073aa;
    border-left: 3px solid #0073aa;
}

.dashboard-navigation .dashicons {
    margin-right: 10px;
}

.dashboard-navigation .count,
.dashboard-navigation .badge-soon {
    margin-left: auto;
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 12px;
}

.badge-soon {
    background: #cff4fc;
    color: #055160;
}

/* Main Content */
.dashboard-content {
    flex: 1;
}

.dashboard-section {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.stat-title {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #0073aa;
}

/* Quick Actions */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.action-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    text-decoration: none;
    color: #333;
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.action-card.disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.action-card h3 {
    margin: 10px 0;
    color: #0073aa;
}

/* Listings Grid */
.listings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.listing-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
}

.status.publish {
    background: #d4edda;
    color: #155724;
}

.status.pending {
    background: #fff3cd;
    color: #856404;
}

.status.draft {
    background: #e9ecef;
    color: #495057;
}

.listing-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.button {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    border: 1px solid #ddd;
    background: #fff;
    color: #333;
}

.button-primary {
    background: #0073aa;
    color: #fff;
    border-color: #0073aa;
}

/* Reviews */
.reviews-list {
    display: grid;
    gap: 20px;
}

/* Responsive */
@media (max-width: 992px) {
    .dashboard-container {
        flex-direction: column;
    }
    
    .dashboard-navigation {
        flex: none;
        width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .quick-stats {
        flex-direction: column;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>
    <!-- Top Stats Bar -->
    <div class="dashboard-top-bar">
        <div class="quick-stats">
            <div class="stat">
                <span class="stat-label">New Leads</span>
                <span class="stat-value"><span class="badge">Coming Soon</span></span>
            </div>
            <div class="stat">
                <span class="stat-label">Unread Messages</span>
                <span class="stat-value"><span class="badge">Coming Soon</span></span>
            </div>
            <div class="stat">
                <span class="stat-label">Today's Views</span>
                <span class="stat-value"><span class="badge">Coming Soon</span></span>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Left Navigation -->
        <div class="dashboard-navigation">
            <ul>
                <!-- Dashboard Overview -->
                <li><a href="#overview" class="active">
                    <span class="dashicons dashicons-dashboard"></span> Dashboard Overview
                </a></li>

                <!-- Listings Management -->
                <li class="menu-group">LISTINGS MANAGEMENT</li>
                <li><a href="#all-listings">
                    <span class="dashicons dashicons-admin-post"></span> My Listings
                    <span class="count"><?php echo $total_listings; ?></span>
                </a></li>
                <li><a href="#live-listings">
                    <span class="dashicons dashicons-visibility"></span> Live Listings
                    <span class="count"><?php echo $live_listings; ?></span>
                </a></li>
                <li><a href="#pending-listings">
                    <span class="dashicons dashicons-clock"></span> Pending Review
                    <span class="count"><?php echo $pending_listings; ?></span>
                </a></li>
                <li><a href="#draft-listings">
                    <span class="dashicons dashicons-edit"></span> Drafts
                    <span class="count"><?php echo $draft_listings; ?></span>
                </a></li>
                <li><a href="#featured-listings">
                    <span class="dashicons dashicons-star-filled"></span> Featured Listings
                </a></li>

                <!-- Leads Management -->
                <li class="menu-group">LEADS MANAGEMENT</li>
                <li><a href="#leads">
                    <span class="dashicons dashicons-groups"></span> All Leads
                    <span class="badge-soon">Coming Soon</span>
                </a></li>
                <li><a href="#messages">
                    <span class="dashicons dashicons-email"></span> Messages
                    <span class="badge-soon">Coming Soon</span>
                </a></li>

                <!-- Reviews & Feedback -->
                <li class="menu-group">REVIEWS & FEEDBACK</li>
                <li><a href="#reviews">
                    <span class="dashicons dashicons-star-half"></span> Reviews
                    <span class="count"><?php echo $total_reviews; ?></span>
                </a></li>
                <li><a href="#analytics">
                    <span class="dashicons dashicons-chart-bar"></span> Rating Analytics
                </a></li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="dashboard-content">
            <h1>Welcome, <?php echo esc_html($current_user->display_name); ?>!</h1>

            <!-- Overview Section -->
            <div class="dashboard-section" id="overview">
                <h2>Dashboard Overview</h2>
                
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-title">Total Listings</div>
                        <div class="stat-number"><?php echo $total_listings; ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-title">Live Listings</div>
                        <div class="stat-number"><?php echo $live_listings; ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-title">Total Reviews</div>
                        <div class="stat-number"><?php echo $total_reviews; ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-title">Pending Review</div>
                        <div class="stat-number"><?php echo $pending_listings; ?></div>
                    </div>
                </div>

                <div class="quick-actions-grid">
                    <a href="<?php echo home_url('/add-listing'); ?>" class="action-card">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <h3>Add New Listing</h3>
                        <p>Create a new business listing</p>
                    </a>
                    <div class="action-card disabled">
                        <span class="dashicons dashicons-groups"></span>
                        <h3>Manage Leads</h3>
                        <p>Coming Soon</p>
                    </div>
                    <div class="action-card">
                        <span class="dashicons dashicons-chart-area"></span>
                        <h3>View Analytics</h3>
                        <p>Track your performance</p>
                    </div>
                    <div class="action-card">
                        <span class="dashicons dashicons-megaphone"></span>
                        <h3>Promote Listings</h3>
                        <p>Boost your visibility</p>
                    </div>
                </div>
            </div>
            <!-- All Listings Section -->
            <div class="dashboard-section" id="all-listings" style="display: none;">
                <div class="section-header">
                    <h2>All My Listings</h2>
                    <div class="section-actions">
                        <a href="<?php echo home_url('/add-listing'); ?>" class="button button-primary">
                            <span class="dashicons dashicons-plus-alt"></span> Add New Listing
                        </a>
                    </div>
                </div>

                <?php
                $all_listings = get_posts(array(
                    'post_type' => 'directory_listing',
                    'author' => $user_id,
                    'posts_per_page' => $listings_per_page,
                    'paged' => $current_page,
                    'post_status' => array('publish', 'pending', 'draft')
                ));

                if (!empty($all_listings)) : ?>
                    <div class="listings-grid">
                        <?php foreach ($all_listings as $listing) : ?>
                            <div class="listing-card">
                                <div class="listing-header">
                                    <h3><?php echo esc_html($listing->post_title); ?></h3>
                                    <div class="listing-meta">
                                        <span class="status <?php echo esc_attr($listing->post_status); ?>">
                                            <?php 
                                            switch ($listing->post_status) {
                                                case 'publish':
                                                    echo 'Listing Live';
                                                    break;
                                                case 'pending':
                                                    echo 'Under Review';
                                                    break;
                                                case 'draft':
                                                    echo 'Draft';
                                                    break;
                                                default:
                                                    echo ucfirst($listing->post_status);
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="listing-details">
                                    <?php 
                                    $views = get_post_meta($listing->ID, 'listing_views', true);
                                    $rating = get_post_meta($listing->ID, 'listing_rating', true);
                                    ?>
                                    <div class="listing-stats">
                                        <span class="views">
                                            <span class="dashicons dashicons-visibility"></span>
                                            <?php echo $views ? number_format($views) : '0'; ?> views
                                        </span>
                                        <?php if ($rating) : ?>
                                            <span class="rating">
                                                <span class="dashicons dashicons-star-filled"></span>
                                                <?php echo number_format($rating, 1); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="listing-actions">
                                        <a href="<?php echo get_edit_post_link($listing->ID); ?>" class="button">
                                            <span class="dashicons dashicons-edit"></span> Edit
                                        </a>
                                        <a href="<?php echo get_permalink($listing->ID); ?>" class="button">
                                            <span class="dashicons dashicons-visibility"></span> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php 
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => ceil($total_listings / $listings_per_page),
                        'current' => $current_page,
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '?paged=%#%',
                    ));
                    echo '</div>';
                    ?>

                <?php else : ?>
                    <div class="no-listings">
                        <p>You haven't created any listings yet.</p>
                        <a href="<?php echo home_url('/add-listing'); ?>" class="button button-primary">
                            Create Your First Listing
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Live Listings Section -->
            <div class="dashboard-section" id="live-listings" style="display: none;">
                <div class="section-header">
                    <h2>Live Listings</h2>
                </div>

                <?php
                $live_listings_query = get_posts(array(
                    'post_type' => 'directory_listing',
                    'author' => $user_id,
                    'post_status' => 'publish',
                    'posts_per_page' => $listings_per_page,
                    'paged' => $current_page
                ));

                if (!empty($live_listings_query)) :
                ?>
                    <div class="listings-grid">
                        <?php foreach ($live_listings_query as $listing) : ?>
                            <div class="listing-card">
                                <div class="listing-header">
                                    <h3><?php echo esc_html($listing->post_title); ?></h3>
                                    <div class="listing-meta">
                                        <span class="status publish">Listing Live</span>
                                    </div>
                                </div>
                                
                                <div class="listing-details">
                                    <div class="listing-actions">
                                        <a href="<?php echo get_edit_post_link($listing->ID); ?>" class="button">
                                            <span class="dashicons dashicons-edit"></span> Edit
                                        </a>
                                        <a href="<?php echo get_permalink($listing->ID); ?>" class="button">
                                            <span class="dashicons dashicons-visibility"></span> View
                                        </a>
                                        <a href="#" class="button promote-button" data-listing="<?php echo $listing->ID; ?>">
                                            <span class="dashicons dashicons-star-filled"></span> Promote
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => ceil($live_listings / $listings_per_page),
                        'current' => $current_page
                    ));
                    echo '</div>';
                    ?>

                <?php else : ?>
                    <div class="no-listings">
                        <p>You don't have any live listings yet.</p>
                        <a href="<?php echo home_url('/add-listing'); ?>" class="button button-primary">
                            Create a Listing
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Reviews Section -->
            <div class="dashboard-section" id="reviews" style="display: none;">
                <div class="section-header">
                    <h2>My Reviews</h2>
                </div>

                <?php if (!empty($user_reviews)) : ?>
                    <div class="reviews-list">
                        <?php foreach ($user_reviews as $review) :
                            $listing = get_post($review->listing_id);
                            if ($listing) : ?>
                                <div class="review-card">
                                    <div class="review-header">
                                        <h4><?php echo esc_html($listing->post_title); ?></h4>
                                        <div class="review-meta">
                                            <div class="review-rating">
                                                <?php echo str_repeat('★', $review->rating); ?>
                                                <?php echo str_repeat('☆', 5 - $review->rating); ?>
                                            </div>
                                            <span class="review-date">
                                                <?php echo date('F j, Y', strtotime($review->review_date)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <p><?php echo esc_html($review->review_text); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="no-reviews">
                        <p>No reviews yet.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle tab navigation
    $('.dashboard-navigation a').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        
        // Update active state
        $('.dashboard-navigation a').removeClass('active');
        $(this).addClass('active');
        
        // Show target section
        $('.dashboard-section').hide();
        $(target).show();
        
        // Update URL without reload
        window.history.pushState(null, '', target);
    });
    
    // Show active section based on URL hash
    var hash = window.location.hash || '#overview';
    $('a[href="' + hash + '"]').click();
    
    // Handle browser back/forward
    $(window).on('popstate', function() {
        var hash = window.location.hash || '#overview';
        $('a[href="' + hash + '"]').click();
    });
});
</script>

<?php get_footer(); ?>