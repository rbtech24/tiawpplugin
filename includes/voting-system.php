<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function handle_directory_vote() {
    error_log('Vote attempt received: ' . print_r($_POST, true));

    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'directory_vote_nonce')) {
        error_log('Nonce verification failed');
        wp_send_json_error('Security check failed');
        return;
    }

    $listing_id = intval($_POST['listing_id']);
    $vote_type = sanitize_text_field($_POST['vote_type']);
    
    // Get current vote counts
    $upvotes = abs((int)get_post_meta($listing_id, 'upvotes', true));
    $downvotes = abs((int)get_post_meta($listing_id, 'downvotes', true));

    // Update the appropriate count
    if ($vote_type === 'Upvote') {
        $upvotes++;
        update_post_meta($listing_id, 'upvotes', $upvotes);
    } else if ($vote_type === 'Downvote') {
        $downvotes++;
        update_post_meta($listing_id, 'downvotes', $downvotes);
    }

    $score = $upvotes - $downvotes;

    error_log("Vote processed. New counts - Upvotes: $upvotes, Downvotes: $downvotes, Score: $score");

    wp_send_json_success([
        'Upvote' => $upvotes,
        'Downvote' => $downvotes,
        'score' => $score
    ]);
}

// Enqueue necessary scripts and styles
function enqueue_voting_assets() {
    if (is_singular('directory_listing')) {
        wp_enqueue_script('directory-voting', plugins_url('assets/js/directory-voting.js', dirname(__FILE__)), array('jquery'), time(), true);
        wp_localize_script('directory-voting', 'directory_voting_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('directory_vote_nonce')
        ));
    }
}

// Hook up the AJAX actions
add_action('wp_ajax_directory_vote', 'handle_directory_vote');
add_action('wp_ajax_nopriv_directory_vote', 'handle_directory_vote');
add_action('wp_enqueue_scripts', 'enqueue_voting_assets');