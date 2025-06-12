<?php
// Restaurant Review Form
function add_restaurant_review_form() {
    if (is_singular('restaurant')) {
        global $post;
        $current_user = wp_get_current_user();
        ?>
        <div class="restaurant-review-form-container">
            <h3>Leave a Review</h3>
            <form id="submit-restaurant-review-form">
                <?php wp_nonce_field('submit_restaurant_review', 'restaurant_review_nonce'); ?>
                <input type="hidden" name="restaurant_id" value="<?php echo $post->ID; ?>">
                <input type="hidden" name="user_id" value="<?php echo $current_user->ID; ?>">

                <div class="form-group">
                    <label for="overall_rating">Overall Rating</label>
                    <div class="star-rating-input" data-rating-input="overall_rating">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                    <input type="hidden" name="overall_rating" id="overall_rating" required>
                </div>

                <div class="form-group">
                    <label for="food_rating">Food Rating</label>
                    <div class="star-rating-input" data-rating-input="food_rating">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                    <input type="hidden" name="food_rating" id="food_rating" required>
                </div>

                <div class="form-group">
                    <label for="service_rating">Service Rating</label>
                    <div class="star-rating-input" data-rating-input="service_rating">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                    <input type="hidden" name="service_rating" id="service_rating" required>
                </div>

                <div class="form-group">
                    <label for="ambience_rating">Ambience Rating</label>
                    <div class="star-rating-input" data-rating-input="ambience_rating">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                    <input type="hidden" name="ambience_rating" id="ambience_rating" required>
                </div>

                <div class="form-group">
                    <label for="review_text">Review</label>
                    <textarea name="review_text" id="review_text" required></textarea>
                </div>

                <div class="form-group">
                    <label for="date_of_visit">Date of Visit</label>
                    <input type="date" name="date_of_visit" id="date_of_visit" required>
                </div>

                <button type="submit">Submit Review</button>
            </form>
        </div>
        <?php
    }
}
add_action('wp_footer', 'add_restaurant_review_form');

// Handle review submission
function handle_restaurant_review_submission() {
    check_ajax_referer('restaurant_review_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to submit a review.');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'restaurant_reviews';

    $restaurant_id = intval($_POST['restaurant_id']);
    $user_id = get_current_user_id();
    $overall_rating = intval($_POST['overall_rating']);
    $food_rating = intval($_POST['food_rating']);
    $service_rating = intval($_POST['service_rating']);
    $ambience_rating = intval($_POST['ambience_rating']);
    $review_text = sanitize_textarea_field($_POST['review_text']);
    $date_of_visit = sanitize_text_field($_POST['date_of_visit']);

    $result = $wpdb->insert(
        $table_name,
        array(
            'restaurant_id' => $restaurant_id,
            'user_id' => $user_id,
            'rating' => $overall_rating,
            'food_rating' => $food_rating,
            'service_rating' => $service_rating,
            'ambience_rating' => $ambience_rating,
            'review_text' => $review_text,
            'date_of_visit' => $date_of_visit,
        ),
        array('%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s')
    );

    if ($result) {
        wp_send_json_success('Review submitted successfully and pending approval.');
    } else {
        wp_send_json_error('Error submitting review. Please try again.');
    }
}
add_action('wp_ajax_submit_restaurant_review', 'handle_restaurant_review_submission');

// Display restaurant reviews
function display_restaurant_reviews() {
    if (is_singular('restaurant')) {
        global $post;
        global $wpdb;
        $table_name = $wpdb->prefix . 'restaurant_reviews';

        $reviews = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE restaurant_id = %d AND status = 'approved' ORDER BY review_date DESC",
            $post->ID
        ));

        if ($reviews) {
            echo '<div class="restaurant-reviews">';
            echo '<h3>Customer Reviews</h3>';
            foreach ($reviews as $review) {
                $user_info = get_userdata($review->user_id);
                echo '<div class="single-review">';
                echo '<div class="review-header">';
                echo get_avatar($user_info->user_email, 50, '', '', array('class' => 'review-avatar'));
                echo '<div class="review-meta">';
                echo '<div class="review-author">' . esc_html($user_info->display_name) . '</div>';
                echo '<div class="review-date">' . esc_html(date('F j, Y', strtotime($review->review_date))) . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="star-rating" title="' . esc_attr($review->rating) . ' out of 5 stars">';
                echo display_stars($review->rating);
                echo '</div>';
                echo '<div class="review-ratings">';
                echo '<span>Food: ' . display_stars($review->food_rating) . '</span>';
                echo '<span>Service: ' . display_stars($review->service_rating) . '</span>';
                echo '<span>Ambience: ' . display_stars($review->ambience_rating) . '</span>';
                echo '</div>';
                echo '<p>Date of Visit: ' . esc_html(date('F j, Y', strtotime($review->date_of_visit))) . '</p>';
                echo '<p class="review-text">' . esc_html($review->review_text) . '</p>';
                echo '</div>';
            }
            echo '</div>';
        }
    }
}
add_action('wp_footer', 'display_restaurant_reviews');

function display_stars($rating) {
    $output = '<span class="star-rating" title="' . esc_attr($rating) . ' out of 5 stars">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $output .= '<i class="fas fa-star"></i>';
        } else {
            $output .= '<i class="far fa-star"></i>';
        }
    }
    $output .= '</span>';
    return $output;
}

// Admin page for managing reviews
function add_restaurant_reviews_management_page() {
    add_submenu_page(
        'edit.php?post_type=restaurant',
        'Manage Restaurant Reviews',
        'Manage Reviews',
        'manage_options',
        'manage-restaurant-reviews',
        'display_restaurant_reviews_management_page'
    );
}
add_action('admin_menu', 'add_restaurant_reviews_management_page');

function display_restaurant_reviews_management_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'restaurant_reviews';

    // Handle review status updates
    if (isset($_POST['review_action']) && isset($_POST['review_id'])) {
        $review_id = intval($_POST['review_id']);
        $action = sanitize_text_field($_POST['review_action']);

        if ($action === 'approve' || $action === 'reject') {
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            $wpdb->update(
                $table_name,
                array('status' => $status),
                array('id' => $review_id),
                array('%s'),
                array('%d')
            );
            if ($action === 'approve') {
                do_action('restaurant_review_approved', $review_id);
            }
        }
    }

    // Fetch reviews
    $reviews = $wpdb->get_results("SELECT * FROM $table_name ORDER BY review_date DESC");

    // Display reviews
    echo '<div class="wrap">';
    echo '<h1>Manage Restaurant Reviews</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Restaurant</th><th>User</th><th>Rating</th><th>Review</th><th>Date of Visit</th><th>Status</th><th>Actions</th></tr></thead>';
    echo '<tbody>';

    foreach ($reviews as $review) {
        $restaurant = get_post($review->restaurant_id);
        $user_info = get_userdata($review->user_id);

        echo '<tr>';
        echo '<td>' . esc_html($restaurant->post_title) . '</td>';
        echo '<td>' . get_avatar($user_info->user_email, 32) . ' ' . esc_html($user_info->display_name) . '</td>';
        echo '<td>' . display_stars($review->rating) . '</td>';
        echo '<td>' . esc_html($review->review_text) . '</td>';
        echo '<td>' . esc_html($review->date_of_visit) . '</td>';
        echo '<td>' . esc_html($review->status) . '</td>';
        echo '<td>';
        if ($review->status === 'pending') {
            echo '<form method="post" style="display:inline;">';
            echo '<input type="hidden" name="review_id" value="' . esc_attr($review->id) . '">';
            echo '<input type="hidden" name="review_action" value="approve">';
            echo '<input type="submit" class="button button-small" value="Approve">';
            echo '</form> ';
            echo '<form method="post" style="display:inline;">';
            echo '<input type="hidden" name="review_id" value="' . esc_attr($review->id) . '">';
            echo '<input type="hidden" name="review_action" value="reject">';
            echo '<input type="submit" class="button button-small" value="Reject">';
            echo '</form>';
        }
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}

// Update restaurant rating when a review is approved
function update_restaurant_rating($review_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'restaurant_reviews';

    $review = $wpdb->get_row($wpdb->prepare(
        "SELECT restaurant_id FROM $table_name WHERE id = %d",
        $review_id
    ));

     if ($review) {
        $restaurant_id = $review->restaurant_id;

        // Calculate new average ratings
        $avg_ratings = $wpdb->get_row($wpdb->prepare(
            "SELECT AVG(rating) as overall, AVG(food_rating) as food, AVG(service_rating) as service, AVG(ambience_rating) as ambience 
            FROM $table_name WHERE restaurant_id = %d AND status = 'approved'",
            $restaurant_id
        ));

        // Update restaurant meta
        update_post_meta($restaurant_id, 'overall_rating', round($avg_ratings->overall, 1));
        update_post_meta($restaurant_id, 'food_rating', round($avg_ratings->food, 1));
        update_post_meta($restaurant_id, 'service_rating', round($avg_ratings->service, 1));
        update_post_meta($restaurant_id, 'ambience_rating', round($avg_ratings->ambience, 1));

        // Update review count
        $review_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE restaurant_id = %d AND status = 'approved'",
            $restaurant_id
        ));
        update_post_meta($restaurant_id, 'review_count', $review_count);
    }
}
add_action('restaurant_review_approved', 'update_restaurant_rating');