// File: includes/directory-listings-review.php

<?php
if (!defined('ABSPATH')) {
    exit;
}

// Create reviews table
function create_directory_reviews_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        listing_id mediumint(9) NOT NULL,
        user_id mediumint(9) NOT NULL,
        rating tinyint(1) NOT NULL,
        review_text text NOT NULL,
        date_of_service date NOT NULL,
        service_received varchar(255) NOT NULL,
        review_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        status varchar(20) DEFAULT 'pending' NOT NULL,
        helpful_count int DEFAULT 0,
        PRIMARY KEY  (id),
        KEY listing_id (listing_id),
        KEY user_id (user_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Get rating breakdown
function get_directory_rating_breakdown($listing_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    $breakdown = array_fill(1, 5, 0);
    
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT rating, COUNT(*) as count 
        FROM $table_name 
        WHERE listing_id = %d 
        AND status = 'approved' 
        GROUP BY rating", 
        $listing_id
    ));
    
    if ($results) {
        foreach ($results as $result) {
            $breakdown[$result->rating] = $result->count;
        }
    }
    
    return $breakdown;
}

// Get overall rating
function get_directory_rating($listing_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    return $wpdb->get_row($wpdb->prepare("
        SELECT 
            AVG(rating) as avg_rating,
            COUNT(*) as total_reviews
        FROM $table_name 
        WHERE listing_id = %d 
        AND status = 'approved'", 
        $listing_id
    ));
}

// Submit review
function handle_directory_review_submission() {
    check_ajax_referer('directory_review_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please log in to submit a review.');
        return;
    }

    $listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $review_text = isset($_POST['review_text']) ? sanitize_textarea_field($_POST['review_text']) : '';
    $date_of_service = isset($_POST['date_of_service']) ? sanitize_text_field($_POST['date_of_service']) : '';
    $service_received = isset($_POST['service_received']) ? sanitize_text_field($_POST['service_received']) : '';

    if (!$listing_id || !$rating || !$review_text || !$date_of_service || !$service_received) {
        wp_send_json_error('All fields are required.');
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'listing_id' => $listing_id,
            'user_id' => get_current_user_id(),
            'rating' => $rating,
            'review_text' => $review_text,
            'date_of_service' => $date_of_service,
            'service_received' => $service_received
        ),
        array('%d', '%d', '%d', '%s', '%s', '%s')
    );

    if ($result === false) {
        wp_send_json_error('Failed to submit review.');
    } else {
        // Update listing meta
        update_listing_review_meta($listing_id);
        wp_send_json_success('Review submitted successfully and pending approval.');
    }
}
add_action('wp_ajax_submit_directory_review', 'handle_directory_review_submission');

// Update listing meta after review changes
function update_listing_review_meta($listing_id) {
    $ratings = get_directory_rating($listing_id);
    update_post_meta($listing_id, 'overall_rating', round($ratings->avg_rating, 1));
    update_post_meta($listing_id, 'review_count', $ratings->total_reviews);
}

// Handle helpful votes
function handle_directory_helpful_vote() {
    check_ajax_referer('helpful_vote_nonce', 'nonce');
    
    $review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;
    
    if (!$review_id) {
        wp_send_json_error('Invalid review ID');
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    $result = $wpdb->query($wpdb->prepare(
        "UPDATE $table_name SET helpful_count = helpful_count + 1 WHERE id = %d",
        $review_id
    ));

    if ($result !== false) {
        $new_count = $wpdb->get_var($wpdb->prepare(
            "SELECT helpful_count FROM $table_name WHERE id = %d",
            $review_id
        ));
        wp_send_json_success(array('new_count' => $new_count));
    } else {
        wp_send_json_error('Failed to update helpful count');
    }
}
add_action('wp_ajax_mark_directory_review_helpful', 'handle_directory_helpful_vote');
add_action('wp_ajax_nopriv_mark_directory_review_helpful', 'handle_directory_helpful_vote');

// Display reviews for a listing
function display_directory_reviews($listing_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_reviews';
    
    $ratings = get_directory_rating($listing_id);
    $breakdown = get_directory_rating_breakdown($listing_id);
    
    ob_start();
    ?>
    <div class="directory-reviews">
        <div class="rating-summary">
            <div class="overall-rating">
                <div class="rating-number"><?php echo number_format($ratings->avg_rating, 1); ?></div>
                <div class="stars">
                    <?php echo str_repeat('★', round($ratings->avg_rating)); ?>
                </div>
                <div class="review-count">
                    Based on <?php echo $ratings->total_reviews; ?> reviews
                </div>
            </div>
            
            <div class="rating-breakdown">
                <?php for ($i = 5; $i >= 1; $i--) : 
                    $count = $breakdown[$i];
                    $percentage = $ratings->total_reviews > 0 ? ($count / $ratings->total_reviews) * 100 : 0;
                ?>
                    <div class="rating-bar">
                        <span class="stars"><?php echo $i; ?> ★</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                        <span class="count"><?php echo $count; ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <?php
        $reviews = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE listing_id = %d AND status = 'approved' ORDER BY review_date DESC",
            $listing_id
        ));

        if ($reviews) : ?>
            <div class="review-list">
                <?php foreach ($reviews as $review) : 
                    $user_info = get_userdata($review->user_id);
                ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <?php echo get_avatar($review->user_id, 50); ?>
                                <div class="reviewer-meta">
                                    <div class="reviewer-name"><?php echo esc_html($user_info->display_name); ?></div>
                                    <div class="review-rating">
                                        <?php echo str_repeat('★', $review->rating); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="review-date">
                                <?php echo human_time_diff(strtotime($review->review_date), current_time('timestamp')); ?> ago
                            </div>
                        </div>
                        
                        <div class="review-content">
                            <div class="service-info">
                                <strong>Service Received:</strong> <?php echo esc_html($review->service_received); ?>
                                <br>
                                <strong>Date of Service:</strong> <?php echo esc_html($review->date_of_service); ?>
                            </div>
                            <div class="review-text">
                                <?php echo wpautop(esc_html($review->review_text)); ?>
                            </div>
                        </div>
                        
                        <div class="review-footer">
                            <div class="verified-badge">
                                <i class="fas fa-check-circle"></i> Verified Customer
                            </div>
                            <button class="helpful-button" data-review-id="<?php echo $review->id; ?>">
                                <i class="fas fa-thumbs-up"></i> Helpful (<?php echo $review->helpful_count; ?>)
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="no-reviews">No reviews yet. Be the first to review this business!</p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Initialize reviews system
function init_directory_reviews() {
    create_directory_reviews_table();
}
register_activation_hook(__FILE__, 'init_directory_reviews');