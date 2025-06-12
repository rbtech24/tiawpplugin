<?php
/**
 * Template part for displaying listing cards
 *
 * @package YourDirectoryPlugin
 */

// Check memory usage at the start
if (memory_get_usage() > 20971520) { // 20MB
    error_log('High memory usage before listing-card.php: ' . memory_get_usage());
    return; // Exit if memory usage is too high
}

error_log('Memory usage at start of listing-card.php: ' . memory_get_usage());

// Get field values with null checks
$listing_type = get_field('listing_type');
$listing_type = $listing_type !== null ? $listing_type : '';

$is_featured = get_field('top_rated');
$is_featured = $is_featured !== null ? $is_featured : false;

$rating = get_field('overall_rating');
$rating = $rating !== null ? $rating : 0;

$review_count = get_field('review_count');
$review_count = $review_count !== null ? $review_count : 0;

$upvotes = get_post_meta(get_the_ID(), 'Upvotes', true) ?: 0;
$downvotes = get_post_meta(get_the_ID(), 'Downvotes', true) ?: 0;
$vote_score = get_post_meta(get_the_ID(), 'vote_score', true) ?: 0;
?>

<div class="listing-card <?php echo $is_featured ? 'featured-listing' : ''; ?>">
    <?php if ($is_featured) : ?>
        <div class="featured-icon">★ Top Rated</div>
    <?php endif; ?>
    
    <div class="listing-image">
        <?php 
        $logo = get_field('logo');
        if ($logo) {
            if (is_numeric($logo)) {
                echo wp_get_attachment_image($logo, 'thumbnail', false, array('class' => 'listing-logo', 'loading' => 'lazy'));
            } elseif (is_array($logo) && isset($logo['url'])) {
                echo '<img src="' . esc_url($logo['url']) . '" alt="' . esc_attr(get_the_title()) . '" class="listing-logo" loading="lazy">';
            } elseif (is_string($logo)) {
                echo '<img src="' . esc_url($logo) . '" alt="' . esc_attr(get_the_title()) . '" class="listing-logo" loading="lazy">';
            }
        } elseif (has_post_thumbnail()) {
            the_post_thumbnail('thumbnail', array('class' => 'listing-thumbnail', 'loading' => 'lazy'));
        } else {
            echo '<img src="' . esc_url(plugin_dir_url(dirname(__FILE__)) . 'assets/images/placeholder.png') . '" alt="Placeholder image" class="listing-placeholder" loading="lazy">';
        }
        ?>
    </div>
    
    <div class="listing-content">
        <h3 class="listing-title"><?php the_title(); ?></h3>
        
        <?php if (get_field('is_verified')) : ?>
            <div class="verified-badge">Verified Business</div>
        <?php endif; ?>
        
        <div class="listing-rating">
            <?php
            $rating_rounded = round($rating);
            $stars = str_repeat('★', $rating_rounded) . str_repeat('☆', 5 - $rating_rounded);
            echo $stars . ' ' . number_format($rating, 1) . ' (' . $review_count . ' reviews)';
            ?>
        </div>
        
        <div class="listing-votes">
            <span class="vote-score">Vote Score: <?php echo esc_html($vote_score); ?></span>
            <span class="vote-details">(<?php echo esc_html($upvotes); ?> 👍 / <?php echo esc_html($downvotes); ?> 👎)</span>
        </div>
        
        <?php
        $excerpt = get_the_excerpt();
        $excerpt = wp_trim_words($excerpt ? $excerpt : '', 15); // Reduced from 20 to 15 words
        echo '<p class="listing-services">' . $excerpt . '</p>';
        ?>
        
        <div class="listing-cta">
            <a href="<?php the_permalink(); ?>" class="btn btn-primary">View Profile</a>
            <?php
            $phone = function_exists('get_field') ? get_field('phone_number') : '';
            if ($phone) :
            ?>
                <a href="tel:<?php echo esc_attr($phone); ?>" class="btn btn-secondary">Call Now</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
error_log('Memory usage at end of listing-card.php: ' . memory_get_usage());
if (memory_get_usage() > 26214400) { // 25MB
    error_log('High memory usage after listing-card.php: ' . memory_get_usage());
}
?>